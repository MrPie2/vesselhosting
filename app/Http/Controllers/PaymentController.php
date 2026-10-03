<?php

namespace App\Http\Controllers;

use App\Jobs\ProvisionHosting;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController
{
    public function initialize(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_if($order->status === 'paid', 422, 'Order is already paid.');
        abort_if(!in_array($order->status, ['pending', 'payment_failed'], true), 422, 'This order cannot be paid.');

        $secret = config('services.paystack.secret');
        abort_if(!$secret, 500, 'Paystack is not configured.');

        $reference = 'VH-' . $order->number . '-' . strtoupper(str()->random(8));
        $currency = strtoupper((string) $order->currency);
        $amount = (int) round(((float) $order->total) * 100);

        $response = Http::acceptJson()
            ->withToken($secret)
            ->timeout(20)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $request->user()->email,
                'amount' => $amount,
                'reference' => $reference,
                'currency' => $currency,
                'callback_url' => route('dashboard.payments.callback'),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->number,
                ],
            ]);

        if (!$response->successful() || !$response->json('status')) {
            Log::error('Paystack initialization failed', [
                'order' => $order->number,
                'response' => $response->json(),
            ]);

            return response()->json([
                'message' => 'Unable to initialize payment. Please try again.',
            ], 502);
        }

        $order->update([
            'payment_reference' => $reference,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'authorization_url' => $response->json('data.authorization_url'),
            'access_code' => $response->json('data.access_code'),
            'reference' => $reference,
        ]);
    }

    public function callback(Request $request)
    {
        $reference = $request->string('reference')->toString();

        if (!$reference) {
            return redirect()->route('dashboard.orders')
                ->withErrors(['payment' => 'No payment reference was returned.']);
        }

        $payment = $this->verifyWithPaystack($reference);

        if (!$payment || !$this->paymentMatchesOrder($payment, $reference)) {
            return redirect()->route('dashboard.orders')
                ->withErrors(['payment' => 'Payment could not be verified.']);
        }

        $this->fulfilSuccessfulPayment($payment);

        return redirect()->route('dashboard.orders')
            ->with('status', 'Payment confirmed. Your services are being provisioned.');
    }

    public function webhook(Request $request)
    {
        $secret = config('services.paystack.secret');
        $signature = $request->header('x-paystack-signature');

        if (!$secret || !$signature ||
            !hash_equals(hash_hmac('sha512', $request->getContent(), $secret), $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        if ($request->input('event') === 'charge.success') {
            $payment = $request->input('data', []);
            if ($this->paymentMatchesOrder($payment, $payment['reference'] ?? null)) {
                $this->fulfilSuccessfulPayment($payment);
            }
        }

        return response()->json(['status' => true]);
    }

    private function verifyWithPaystack(string $reference): ?array
    {
        $secret = config('services.paystack.secret');

        $response = Http::acceptJson()
            ->withToken($secret)
            ->timeout(20)
            ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

        if (!$response->successful() || !$response->json('status')) {
            return null;
        }

        $payment = $response->json('data');

        return is_array($payment) ? $payment : null;
    }

    private function paymentMatchesOrder(array $payment, ?string $reference): bool
    {
        if (!$reference || ($payment['status'] ?? null) !== 'success') {
            return false;
        }

        $order = Order::where('payment_reference', $reference)->first();
        if (!$order) {
            return false;
        }

        $expectedAmount = (int) round(((float) $order->total) * 100);
        $paidAmount = (int) ($payment['amount'] ?? 0);
        $paidCurrency = strtoupper((string) ($payment['currency'] ?? ''));

        return $paidAmount === $expectedAmount
            && $paidCurrency === strtoupper((string) $order->currency);
    }

    private function fulfilSuccessfulPayment(array $payment): void
    {
        $reference = $payment['reference'] ?? null;
        if (!$reference) return;

        DB::transaction(function () use ($reference) {
            $order = Order::where('payment_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$order || $order->status === 'paid') {
                return;
            }

            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
                'provisioning_status' => 'queued',
                'provisioning_error' => null,
            ]);

            ProvisionHosting::dispatch($order->id);
        });

        Log::info('Paid order queued for hosting provisioning', [
            'reference' => $reference,
        ]);
    }
}
