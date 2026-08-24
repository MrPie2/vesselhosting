<?php
namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\HostingAccount;
use App\Models\Order;
use App\Models\User;
use App\Models\HostingPlan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.home', [
            'customers'=>User::where('role','customer')->count(),
            'domains'=>Domain::count(),
            'hosting'=>HostingAccount::count(),
            'orders'=>Order::latest()->take(8)->get(),
        ]);
    }

    public function customers()
    {
        $customers = User::where('role','customer')->latest()->paginate(25);
        return view('admin.customers', compact('customers'));
    }

    public function domains()
    {
        $domains = Domain::with('user')->latest()->paginate(25);
        return view('admin.domains', compact('domains'));
    }

    public function hosting()
    {
        $hosting = HostingAccount::with('user','domain','plan')->latest()->paginate(25);
        return view('admin.hosting', compact('hosting'));
    }

    public function plans()
    {
        $plans = HostingPlan::latest()->get();
        return view('admin.plans', compact('plans'));
    }

    public function updatePlan(Request $request, HostingPlan $plan)
    {
        $data = $request->validate([
            'price_monthly'=>'required|numeric|min:0',
            'active'=>'nullable|boolean',
        ]);
        $plan->update([
            'price_monthly'=>$data['price_monthly'],
            'active'=>$request->boolean('active'),
        ]);
        return back()->with('status','Hosting plan updated.');
    }
}