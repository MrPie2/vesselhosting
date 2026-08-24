<?php
namespace App\Http\Controllers;

use App\Jobs\ProvisionDomainRegistration;
use App\Models\Domain;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController
{
    public function initialize(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id,403);
        abort_if($order->status === 'paid',422,'Order is already paid.');

        $reference = $order->number.'-'.str()->lower(str()->random(8));

        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize',[
                'email'=>$request->user()->email,
                'amount'=>(int)round($order->total * 100),
                'reference'=>$reference,
                'currency'=>$order->currency,
                'callback_url'=>route('dashboard.payments.callback'),
                'metadata'=>[
                    'order_id'=>$order->id,
                    'order_number'=>$order->number,
                ],
            ]);

        if (!$response->successful()) {
            return response()->json(['message'=>'Unable to initialize payment.'],502);
        }

        $order->update(['payment_reference'=>$reference]);

        return response()->json($response->json());
    }

    public function callback(Request $request)
    {
        $reference = $request->string('reference')->toString();
        if (!$reference) return redirect()->route('dashboard.orders');

        $response = Http::withToken(config('services.paystack.secret'))
            ->get('https://api.paystack.co/transaction/verify/'.urlencode($reference));

        if (!$response->successful() || $response->json('data.status') !== 'success') {
            return redirect()->route('dashboard.orders')->withErrors(['payment'=>'Payment was not completed.']);
        }

        $this->fulfilSuccessfulPayment($response->json('data'));

        return redirect()->route('dashboard.orders')->with('status','Payment confirmed. Your services are being provisioned.');
    }

    public function webhook(Request $request)
    {
        $secret = config('services.paystack.secret');
        $signature = $request->header('x-paystack-signature');

        abort_unless($secret && $signature &&
            hash_equals(hash_hmac('sha512',$request->getContent(),$secret),$signature),401);

        if ($request->input('event') === 'charge.success') {
            $this->fulfilSuccessfulPayment($request->input('data',[]));
        }

        return response()->json(['status'=>true]);
    }

    private function fulfilSuccessfulPayment(array $payment): void
    {
        $reference = $payment['reference'] ?? null;
        if (!$reference) return;

        $order = Order::where('payment_reference',$reference)->first();
        if (!$order || $order->status === 'paid') return;

        $order->update([
            'status'=>'paid',
            'paid_at'=>now(),
            'provisioning_status'=>'queued',
            'provisioning_error'=>null,
        ]);

        $renewalItem = $order->items()->where('type','domain_renewal')->first();
        if ($renewalItem) {
            $domain = Domain::where('user_id',$order->user_id)
                ->where('name',$renewalItem->reference)->firstOrFail();
            \App\Jobs\ProvisionDomainRenewal::dispatch(
                $order->id,
                $domain->id,
                (int)data_get($renewalItem->meta,'years',1)
            );
            return;
        }

        $domainItem = $order->items()->where('type','domain')->first();

        if ($domainItem) {
            $domain = Domain::firstOrCreate(
                ['user_id'=>$order->user_id,'name'=>strtolower($domainItem->reference)],
                ['status'=>'pending','auto_renew'=>true]
            );

            ProvisionDomainRegistration::dispatch($order->id,$domain->id);
        } else {
            // Hosting-only orders still need a domain record. The current
            // workflow expects the domain reference in the hosting item.
            $hostingItem = $order->items()->where('type','hosting')->first();
            if ($hostingItem) {
                $domain = Domain::firstOrCreate(
                    ['user_id'=>$order->user_id,'name'=>strtolower($hostingItem->reference)],
                    ['status'=>'pending','auto_renew'=>true]
                );
                ProvisionDomainRegistration::dispatch($order->id,$domain->id);
            } else {
                $order->update(['provisioning_status'=>'completed','status'=>'completed']);
            }
        }

        Log::info('Paid order queued for provisioning',['order'=>$order->number]);
    }
}