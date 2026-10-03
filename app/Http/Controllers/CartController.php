<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        return view('cart.index', [
            'cart' => $cart,
            'total' => collect($cart)->sum('total'),
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'domain' => 'required|string|max:253',
            'billing_cycle' => 'required|integer|in:1,6,12,24',
            'domain_option' => 'required|in:register,transfer,existing',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $months = (int) $data['billing_cycle'];
        $unitPrice = (float) $plan->amount;
        $key = (string) Str::uuid();

        $cart = $request->session()->get('cart', []);
        $cart[$key] = [
            'key' => $key,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'domain' => strtolower(trim($data['domain'])),
            'domain_option' => $data['domain_option'],
            'billing_cycle' => $months,
            'unit_price' => $unitPrice,
            'total' => round($unitPrice * $months, 2),
        ];

        $request->session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Hosting plan added to cart.',
            'redirect' => route('cart.index'),
        ]);
    }

    public function remove(Request $request, string $key)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$key]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }
}
