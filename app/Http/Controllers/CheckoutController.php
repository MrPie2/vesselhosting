<?php
namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\HostingPlan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TldPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController
{
    public function domain(Request $request)
    {
        $data = $request->validate([
            'domain'=>'required|string|max:253|regex:/^[a-z0-9.-]+$/i',
        ]);

        $domain = strtolower(trim($data['domain']));
        $price = $this->priceFor($domain);

        return view('checkout.domain', compact('domain','price'));
    }

    public function purchaseDomain(Request $request)
    {
        $data = $request->validate([
            'domain'=>'required|string|max:253|regex:/^[a-z0-9.-]+$/i',
            'years'=>'required|integer|min:1|max:10',
        ]);

        $price = $this->priceFor($data['domain']);
        $total = $price->register_price * $data['years'];

        $order = Order::create([
            'user_id'=>Auth::id(),
            'number'=>'VH-'.strtoupper(Str::random(10)),
            'status'=>'pending',
            'provisioning_status'=>'not_started',
            'currency'=>$price->currency,
            'subtotal'=>$total,
            'tax'=>0,
            'total'=>$total,
        ]);

        OrderItem::create([
            'order_id'=>$order->id,
            'type'=>'domain',
            'description'=>'Domain registration · '.$data['domain'],
            'reference'=>strtolower($data['domain']),
            'quantity'=>$data['years'],
            'unit_price'=>$price->register_price,
            'total'=>$total,
            'meta'=>['years'=>(int)$data['years'],'tld'=>$price->tld],
        ]);

        return redirect()->route('dashboard.orders.show',$order);
    }

    public function hosting(Request $request, HostingPlan $plan)
    {
        abort_unless($plan->active,404);

        $data = $request->validate(['domain'=>'required|string|max:253']);

        $order = Order::create([
            'user_id'=>Auth::id(),
            'number'=>'VH-'.strtoupper(Str::random(10)),
            'status'=>'pending',
            'provisioning_status'=>'not_started',
            'currency'=>config('services.app.currency','USD'),
            'subtotal'=>$plan->price_monthly,
            'tax'=>0,
            'total'=>$plan->price_monthly,
        ]);

        OrderItem::create([
            'order_id'=>$order->id,
            'type'=>'hosting',
            'description'=>$plan->name.' hosting · '.$data['domain'],
            'reference'=>strtolower($data['domain']),
            'unit_price'=>$plan->price_monthly,
            'total'=>$plan->price_monthly,
            'meta'=>['hosting_plan_id'=>$plan->id],
        ]);

        return redirect()->route('dashboard.orders.show',$order);
    }

    public function bundle(Request $request, HostingPlan $plan)
    {
        abort_unless($plan->active,404);

        $data = $request->validate([
            'domain'=>'required|string|max:253',
            'years'=>'required|integer|min:1|max:10',
        ]);

        $price = $this->priceFor($data['domain']);
        $domainTotal = $price->register_price * $data['years'];
        $total = $domainTotal + $plan->price_monthly;

        $order = Order::create([
            'user_id'=>Auth::id(),
            'number'=>'VH-'.strtoupper(Str::random(10)),
            'status'=>'pending',
            'provisioning_status'=>'not_started',
            'currency'=>$price->currency,
            'subtotal'=>$total,
            'tax'=>0,
            'total'=>$total,
        ]);

        OrderItem::create([
            'order_id'=>$order->id,'type'=>'domain',
            'description'=>'Domain registration · '.$data['domain'],
            'reference'=>strtolower($data['domain']),
            'quantity'=>$data['years'],'unit_price'=>$price->register_price,
            'total'=>$domainTotal,'meta'=>['years'=>(int)$data['years'],'tld'=>$price->tld],
        ]);

        OrderItem::create([
            'order_id'=>$order->id,'type'=>'hosting',
            'description'=>$plan->name.' hosting · '.$data['domain'],
            'reference'=>strtolower($data['domain']),
            'quantity'=>1,'unit_price'=>$plan->price_monthly,
            'total'=>$plan->price_monthly,'meta'=>['hosting_plan_id'=>$plan->id],
        ]);

        return redirect()->route('dashboard.orders.show',$order);
    }

    private function priceFor(string $domain): TldPrice
    {
        $parts = explode('.', strtolower(trim($domain)));
        $tld = '.'.array_pop($parts);

        $price = TldPrice::where('tld',$tld)->where('active',true)->first();
        abort_unless($price,422,'This TLD is not configured for sale yet.');

        return $price;
    }
}