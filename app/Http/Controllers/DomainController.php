<?php
namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\TldPrice;
use App\Services\ResellerClubDomainService;
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
        abort_unless((string) $domain->user_id === (string) Auth::id(), 403);
        $domain->load('dnsRecords','hosting');
        return view('dashboard.domains.show', compact('domain'));
    }

    public function updateNameservers(Request $request, Domain $domain, ResellerClubDomainService $domainService)
    {
        abort_unless((string) $domain->user_id === (string) Auth::id(), 403);

        $data = $request->validate([
            'nameserver_type' => 'required|in:default,custom',
            'nameservers' => 'nullable|array|max:4',
            'nameservers.*' => 'nullable|string|max:253',
        ]);

        $nameservers = $data['nameserver_type'] === 'default'
            ? config('services.resellerclub.nameservers', [])
            : array_values(array_filter(array_map('trim', $data['nameservers'] ?? [])));

        if (!is_array($nameservers) || count($nameservers) < 2) {
            return back()->withErrors(['nameservers' => 'At least Nameserver 1 and Nameserver 2 are required.'])->withInput();
        }

        try {
            $domainService->setNameservers($domain, $nameservers);
        } catch (\Throwable $e) {
            return back()->withErrors(['nameservers' => $e->getMessage()])->withInput();
        }

        return back()->with('status', 'Nameservers updated successfully.');
    }

    public function renew(Request $request, Domain $domain) {
        abort_unless((string) $domain->user_id === (string) Auth::id(), 403);
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
