<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Services\ResellerClubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController
{
    public function show(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $items = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $plan = Plan::find($item['plan_id']);

            if (!$plan) {
                continue;
            }

            $months = (int) ($item['billing_cycle'] ?? 1);
            $total = round((float) $plan->amount * $months, 2);

            $items[] = [
                'plan' => $plan,
                'domain' => strtolower(trim($item['domain'])),
                'domain_option' => $item['domain_option'],
                'billing_cycle' => $months,
                'total' => $total,
            ];

            $subtotal += $total;
        }

        if (empty($items)) {
            $request->session()->forget('cart');
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'The selected hosting plans are no longer available.',
            ]);
        }

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'currency' => config('services.paystack.currency', 'USD'),
        ]);
    }

    public function placeOrder(Request $request, ResellerClubService $resellerClub)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $order = DB::transaction(function () use ($cart, $request, $resellerClub) {
            $currency = config('services.paystack.currency', 'USD');
            $subtotal = 0;
            $prepared = [];

            foreach ($cart as $item) {
                $plan = Plan::lockForUpdate()->find($item['plan_id']);

                if (!$plan) {
                    abort(422, 'One of the selected hosting plans is no longer available.');
                }

                $months = (int) ($item['billing_cycle'] ?? 1);
                if (!in_array($months, [1, 6, 12, 24], true)) {
                    abort(422, 'Invalid billing cycle.');
                }

                $domain = strtolower(trim($item['domain']));
                if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain)) {
                    abort(422, 'Please enter a valid domain name.');
                }

                $domainOption = $item['domain_option'] ?? null;
                if (!in_array($domainOption, ['register', 'transfer', 'existing'], true)) {
                    abort(422, 'Invalid domain option.');
                }

                if ($domainOption === 'register') {
                    $availability = $resellerClub->check($domain);
                    if (!($availability['success'] ?? false) || !($availability['available'] ?? false)) {
                        abort(422, 'The selected domain is no longer available.');
                    }
                }

                $hostingTotal = round((float) $plan->amount * $months, 2);
                $subtotal += $hostingTotal;

                $prepared[] = [
                    'plan' => $plan,
                    'domain' => $domain,
                    'domain_option' => $domainOption,
                    'months' => $months,
                    'hosting_total' => $hostingTotal,
                ];
            }

            $order = Order::create([
                'id' => (string) Str::uuid(),
                'user_id' => $request->user()->id,
                'number' => 'VH-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'provisioning_status' => 'not_started',
                'currency' => $currency,
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
            ]);

            foreach ($prepared as $item) {
                $order->items()->create([
                    'type' => 'hosting',
                    'description' => $item['plan']->name . ' hosting · ' . $item['domain'],
                    'reference' => $item['domain'],
                    'quantity' => 1,
                    'unit_price' => $item['hosting_total'],
                    'total' => $item['hosting_total'],
                    'meta' => [
                        'plan_id' => $item['plan']->id,
                        'plan_name' => $item['plan']->name,
                        'cpanel_plan' => $item['plan']->name,
                        'billing_cycle' => $item['months'],
                        'domain_option' => $item['domain_option'],
                    ],
                ]);
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('dashboard.orders.show', $order);
    }
}
