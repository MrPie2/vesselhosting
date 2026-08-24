<?php
namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\TldPrice;
use App\Contracts\Registrar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DomainController
{
    public function index() {
        $domains = Auth::user()->domains()->withCount('dnsRecords')->latest()->paginate(12);
        return view('dashboard.domains.index', compact('domains'));
    }

    public function show(Domain $domain) {
        abort_unless($domain->user_id === Auth::id(),403);
        $domain->load('dnsRecords','hosting');
        return view('dashboard.domains.show', compact('domain'));
    }

    public function renew(Request $request, Domain $domain) {
        abort_unless($domain->user_id === Auth::id(),403);
        $data = $request->validate(['years'=>'required|integer|min:1|max:10']);

        $parts = explode('.', $domain->name);
        $price = TldPrice::where('tld','.'.array_pop($parts))->where('active',true)->firstOrFail();
        $total = $price->renew_price * $data['years'];

        $order = $domain->user->orders()->create([
            'number'=>'VH-'.str()->upper(str()->random(10)),
            'status'=>'pending',
            'provisioning_status'=>'not_started',
            'currency'=>$price->currency,
            'subtotal'=>$total,'tax'=>0,'total'=>$total,
        ]);

        $order->items()->create([
            'type'=>'domain_renewal',
            'description'=>'Domain renewal · '.$domain->name,
            'reference'=>$domain->name,
            'quantity'=>$data['years'],
            'unit_price'=>$price->renew_price,
            'total'=>$total,
            'meta'=>['domain_id'=>$domain->id,'years'=>(int)$data['years']],
        ]);

        return redirect()->route('dashboard.orders.show',$order);
    }
}