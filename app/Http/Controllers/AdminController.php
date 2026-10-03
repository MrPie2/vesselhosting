<?php

namespace App\Http\Controllers;

use App\Models\Domain;
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
        $plans = Plan::latest()->get();
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

    public function orders()
    {
        $orders = Order::with('user')->latest()->paginate(25);
        return view('admin.orders', compact('orders'));
    }
}
