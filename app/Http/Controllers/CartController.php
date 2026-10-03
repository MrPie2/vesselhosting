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
        $total = 0;

        foreach ($cart as $key => &$item) {
            $plan = Plan::find($item['plan_id']);

            if (!$plan) {
                unset($cart[$key]);
                continue;
            }

            $months = (int) ($item['billing_cycle'] ?? 1);
            $item['plan_name'] = $plan->name;
            $item['unit_price'] = (float) $plan->amount;
            $item['total'] = round((float) $plan->amount * $months, 2);
            $total += $item['total'];
        }
        unset($item);

        $request->session()->put('cart', $cart);

        return view('cart.index', [
            'cart' => $cart,
            'total' => round($total, 2),
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

        $domain = strtolower(trim($data['domain']));

        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain)) {
            return response()->json(['message' => 'Enter a valid domain name.'], 422);
        }

        $plan = Plan::findOrFail($data['plan_id']);
        $months = (int) $data['billing_cycle'];
        $unitPrice = (float) $plan->amount;
        $key = (string) Str::uuid();

        $cart = $request->session()->get('cart', []);
        $cart[$key] = [
            'key' => $key,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'domain' => $domain,
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
