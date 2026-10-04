<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\DomainPrice;
use App\Models\Hosting;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController
{
    public function index()
    {
        return view('admin.home', [
            'customers' => User::where('role', 'customer')->count(),
            'domains' => Domain::count(),
            'hosting' => Hosting::count(),
            'orders' => Order::latest()->take(8)->get(),
        ]);
    }

    public function customers()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(25);
        return view('admin.customers', compact('customers'));
    }

    public function domains()
    {
        $domains = Domain::with('user')->latest()->paginate(25);
        return view('admin.domains', compact('domains'));
    }

    public function hosting()
    {
        $hosting = Hosting::with('user', 'domainRelation', 'plan')->latest()->paginate(25);
        return view('admin.hosting', compact('hosting'));
    }

    public function plans()
    {
        $plans = Plan::orderByDesc('id')->get();
        return view('admin.plans', compact('plans'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $plan->update([
            'amount' => $data['amount'],
        ]);

        return back()->with('status', 'Hosting plan updated.');
    }

    public function domainPrices()
    {
        $domainPrices = DomainPrice::orderBy('tld')->get();

        return view('admin.domain-prices', compact('domainPrices'));
    }

    public function storeDomainPrice(Request $request)
    {
        $data = $request->validate([
            'tld' => ['required', 'string', 'max:100'],
            'registration_price' => ['required', 'numeric', 'min:0'],
            'renewal_price' => ['required', 'numeric', 'min:0'],
            'transfer_price' => ['required', 'numeric', 'min:0'],
        ]);

        $data['tld'] = '.' . ltrim(strtolower(trim($data['tld'])), '.');

        DomainPrice::create($data);

        return back()->with('status', 'Domain extension pricing added.');
    }

    public function updateDomainPrice(Request $request, DomainPrice $domainPrice)
    {
        $data = $request->validate([
            'registration_price' => ['required', 'numeric', 'min:0'],
            'renewal_price' => ['required', 'numeric', 'min:0'],
            'transfer_price' => ['required', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        $domainPrice->update([
            'registration_price' => $data['registration_price'],
            'renewal_price' => $data['renewal_price'],
            'transfer_price' => $data['transfer_price'],
            'active' => $request->boolean('active'),
        ]);

        return back()->with('status', $domainPrice->tld . ' pricing updated.');
    }

    public function orders()
    {
        $orders = Order::with('user')->latest()->paginate(25);
        return view('admin.orders', compact('orders'));
    }
}
