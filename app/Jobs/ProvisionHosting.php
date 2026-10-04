<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Order;
use App\Services\CreateAccountService;
use App\Services\ResellerClubDomainService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProvisionHosting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int|string $orderId)
    {
    }

    public function handle(
        CreateAccountService $createAccountService,
        ResellerClubDomainService $domainService
    ): void {
        $order = Order::with('items')->find($this->orderId);
        if (!$order || $order->status !== 'paid') return;

        $order->update([
            'provisioning_status' => 'provisioning',
            'provisioning_error' => null,
        ]);

        try {
            $requiresTransfer = false;

            // Register purchased domains before creating hosting accounts.
            // This makes the hosting account point at a domain that actually
            // exists at the registrar when the order includes registration.
            foreach ($order->items->where('type', 'domain') as $domainItem) {
                $meta = $domainItem->meta ?? [];
                $domainName = strtolower(trim((string) ($meta['domain'] ?? $domainItem->reference)));

                if ($domainName === '') {
                    throw new \RuntimeException('The paid domain order item has no domain name.');
                }

                $domain = Domain::firstOrCreate(
                    ['user_id' => $order->user_id, 'domain' => $domainName],
                    ['status' => 'pending']
                );

                $option = $meta['domain_option'] ?? 'register';
                if ($option === 'register') {
                    if ($domain->status !== 'active') {
                        $domainService->register(
                            $domain,
                            max(1, (int) ($meta['years'] ?? 1))
                        );
                    }
                } elseif ($option === 'transfer') {
                    $requiresTransfer = true;
                    $domain->update(['status' => 'pending_transfer']);
                }
            }

            $hostingItem = $order->items->firstWhere('type', 'hosting');

            if (!$hostingItem) {
                $order->update([
                    'provisioning_status' => $requiresTransfer ? 'awaiting_domain' : 'completed',
                    'provisioning_error' => null,
                ]);
                return;
            }

            $hostingDomain = strtolower(trim((string) $hostingItem->reference));
            $meta = $hostingItem->meta ?? [];
            $months = max(1, (int) ($meta['billing_cycle'] ?? 1));

            $domain = Domain::firstOrCreate(
                ['user_id' => $order->user_id, 'domain' => $hostingDomain],
                ['status' => ($meta['domain_option'] ?? 'existing') === 'existing' ? 'external' : 'pending']
            );

            $existing = Hosting::where('user_id', $order->user_id)
                ->where('domain', $hostingDomain)
                ->whereIn('status', ['active', 'provisioning'])
                ->first();

            if ($existing) {
                $order->update([
                    'provisioning_status' => $requiresTransfer || in_array(($meta['domain_option'] ?? 'existing'), ['transfer'], true)
                        ? 'awaiting_domain'
                        : 'completed',
                    'provisioning_error' => null,
                ]);
                return;
            }

            $username = 'vh' . substr(hash('sha256', $order->number . $hostingDomain), 0, 10);
            $password = bin2hex(random_bytes(8));

            $result = $createAccountService->create([
                'username' => $username,
                'domain' => $hostingDomain,
                'domain_id' => $domain->id,
                'password' => $password,
                'plan' => $meta['cpanel_plan'] ?? $meta['plan_name'] ?? $hostingItem->description,
            ]);

            $this->assertWhmSuccess($result);

            Hosting::create([
                'user_id' => $order->user_id,
                'plan_id' => $meta['plan_id'] ?? null,
                'domain' => $hostingDomain,
                'domain_id' => $domain->id,
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
                'provisioning_status' => $requiresTransfer || $domainOption === 'transfer'
                    ? 'awaiting_domain'
                    : 'completed',
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
