<?php

namespace App\Jobs;

use App\Models\Hosting;
use App\Models\Order;
use App\Services\CreateAccountService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProvisionHosting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int|string $orderId)
    {
    }

    public function handle(CreateAccountService $createAccountService): void
    {
        $order = Order::with('items')->find($this->orderId);
        if (!$order || $order->status !== 'paid') return;

        $hostingItem = $order->items->firstWhere('type', 'hosting');
        if (!$hostingItem) {
            $order->update(['provisioning_status' => 'completed']);
            return;
        }

        $existing = Hosting::where('user_id', $order->user_id)
            ->where('domain', strtolower($hostingItem->reference))
            ->whereIn('status', ['active', 'provisioning'])
            ->first();

        if ($existing) {
            $order->update(['provisioning_status' => 'completed']);
            return;
        }

        $meta = $hostingItem->meta ?? [];
        $months = max(1, (int) ($meta['billing_cycle'] ?? 1));

        $username = 'vh' . substr(hash('sha256', $order->number . $hostingItem->reference), 0, 10);
        $password = bin2hex(random_bytes(8));

        $order->update(['provisioning_status' => 'provisioning']);

        try {
            $result = $createAccountService->create([
                'username' => $username,
                'domain' => strtolower($hostingItem->reference),
                'domain_id' => $domain->id,
                'password' => $password,
                'plan' => $meta['cpanel_plan'] ?? $meta['plan_name'] ?? $hostingItem->description,
            ]);

            $this->assertWhmSuccess($result);

            $planId = $meta['plan_id'] ?? null;

            $domain = Domain::firstOrCreate(
                ['user_id' => $order->user_id, 'domain' => strtolower($hostingItem->reference)],
                ['status' => ($meta['domain_option'] ?? 'existing') === 'existing' ? 'external' : 'pending']
            );

            Hosting::create([
                'user_id' => $order->user_id,
                'plan_id' => $planId,
                'domain' => strtolower($hostingItem->reference),
                'username' => $username,
                'password' => $password,
                'server_hostname' => config('services.whm.hostname'),
                'expiry_date' => now()->addMonths($months),
                'duration' => $months,
                'status' => 'active',
                'provisioning_status' => 'completed',
                'provisioning_error' => null,
            ]);

            $domainOption = $meta['domain_option'] ?? 'existing';
            $order->update([
                'provisioning_status' => in_array($domainOption, ['register', 'transfer'], true) ? 'awaiting_domain' : 'completed',
                'provisioning_error' => null,
            ]);
        } catch (Throwable $e) {
            report($e);

            $order->update([
                'provisioning_status' => 'failed',
                'provisioning_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function assertWhmSuccess(array $result): void
    {
        $metadataResult = data_get($result, 'metadata.result');
        if ($metadataResult !== null && (int) $metadataResult !== 1) {
            throw new \RuntimeException(data_get($result, 'metadata.reason', 'WHM rejected the account creation request.'));
        }

        $apiResult = data_get($result, 'data.result.0');
        if (is_array($apiResult) && isset($apiResult['status']) && (int) $apiResult['status'] !== 1) {
            throw new \RuntimeException($apiResult['statusmsg'] ?? 'WHM could not create the hosting account.');
        }
    }
}
