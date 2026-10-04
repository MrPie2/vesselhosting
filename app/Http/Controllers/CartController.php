<?php

namespace App\Http\Controllers;

use App\Models\DomainPrice;
use App\Models\Plan;
use App\Services\ResellerClubService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $total = 0;

        foreach ($cart as $key => &$item) {
            if (($item['type'] ?? 'hosting') === 'domain') {
                $item['domain_price'] = (float) ($item['domain_price'] ?? 0);
                $item['total'] = $item['domain_price'];
                $total += $item['total'];
                continue;
            }

            $plan = Plan::find($item['plan_id']);
            if (!$plan) {
                unset($cart[$key]);
                continue;
            }

            $months = (int) ($item['billing_cycle'] ?? 1);
            $item['plan_name'] = $plan->name;
            $item['unit_price'] = (float) $plan->amount;
            $hostingTotal = round((float) $plan->amount * $months, 2);
            $item['hosting_total'] = $hostingTotal;
            $item['domain_price'] = (float) ($item['domain_price'] ?? 0);
            $item['total'] = round($hostingTotal + $item['domain_price'], 2);
            $total += $item['total'];
        }
        unset($item);

        $request->session()->put('cart', $cart);

        return view('cart.index', [
            'cart' => $cart,
            'total' => round($total, 2),
        ]);
    }

    public function add(Request $request, ResellerClubService $resellerClub)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'domain' => 'required|string|max:253',
            'billing_cycle' => 'required|integer|in:1,6,12,24',
            'domain_option' => 'required|in:register,transfer,existing',
            'nameserver_type' => 'required|in:default,custom',
            'nameservers' => 'nullable|array|max:4',
            'nameservers.*' => 'nullable|string|max:253',
        ]);

        $domain = strtolower(trim($data['domain']));
        $nameservers = array_values(array_filter(array_map('trim', $data['nameservers'] ?? [])));
        if ($data['nameserver_type'] === 'custom' && count($nameservers) < 2) {
            return response()->json(['message' => 'Enter at least Nameserver 1 and Nameserver 2.'], 422);
        }

        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain)) {
            return response()->json(['message' => 'Enter a valid domain name.'], 422);
        }

        // A domain selected for registration must still be available
        // when it reaches the server, not only when the browser checked it.
        if ($data['domain_option'] === 'register') {
            $availability = $resellerClub->check($domain);

            if (!($availability['success'] ?? false)) {
                return response()->json([
                    'message' => $availability['message'] ?? 'Unable to verify domain availability.',
                ], 422);
            }

            if (!($availability['available'] ?? false)) {
                return response()->json([
                    'message' => $domain . ' is no longer available. Please search for another domain.',
                ], 422);
            }

            $domain = $availability['domain'] ?? $domain;
        }

        $plan = Plan::findOrFail($data['plan_id']);
        $months = (int) $data['billing_cycle'];
        $unitPrice = (float) $plan->amount;
        $domainPrice = 0;

        if ($data['domain_option'] === 'register') {
            $firstDot = strpos($domain, '.');
            $tld = $firstDot !== false
                ? '.' . strtolower(ltrim(substr($domain, $firstDot + 1), '.'))
                : '';

            $pricing = DomainPrice::forTld($tld)->where('active', true)->first();

            if (!$pricing) {
                return response()->json([
                    'message' => 'We have not configured a registration price for ' . $tld . ' yet. Please choose another extension.',
                ], 422);
            }

            $domainPrice = (float) $pricing->registration_price;
        } elseif ($data['domain_option'] === 'transfer') {
            $firstDot = strpos($domain, '.');
            $tld = $firstDot !== false
                ? '.' . strtolower(ltrim(substr($domain, $firstDot + 1), '.'))
                : '';
            $pricing = DomainPrice::forTld($tld)->where('active', true)->first();
            $domainPrice = $pricing ? (float) $pricing->transfer_price : 0;
        }
        $key = (string) Str::uuid();

        $cart = $request->session()->get('cart', []);
        $cart[$key] = [
            'key' => $key,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'domain' => $domain,
            'domain_option' => $data['domain_option'],
            'nameserver_type' => $data['nameserver_type'],
            'nameservers' => $data['nameserver_type'] === 'custom' ? $nameservers : [],
            'billing_cycle' => $months,
            'unit_price' => $unitPrice,
            'hosting_total' => round($unitPrice * $months, 2),
            'domain_price' => $domainPrice,
            'total' => round(($unitPrice * $months) + $domainPrice, 2),
        ];

        $request->session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Hosting plan added to cart.',
            'redirect' => route('cart.index'),
        ]);
    }

    public function addDomain(Request $request, ResellerClubService $resellerClub)
    {
        $data = $request->validate([
            'domain' => 'required|string|max:253',
        ]);

        $domain = strtolower(trim($data['domain']));
        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\\.)+[a-z]{2,63}$/i', $domain)) {
            return response()->json(['message' => 'Enter a valid domain name.'], 422);
        }

        $availability = $resellerClub->check($domain);
        if (!($availability['success'] ?? false) || !($availability['available'] ?? false)) {
            return response()->json([
                'message' => $availability['message'] ?? $domain . ' is not available.',
            ], 422);
        }

        $domain = $availability['domain'] ?? $domain;
        $tld = '.' . strtolower(ltrim(substr($domain, strpos($domain, '.') + 1), '.'));
        $pricing = DomainPrice::forTld($tld)->where('active', true)->first();

        if (!$pricing) {
            return response()->json([
                'message' => 'We have not configured a registration price for ' . $tld . ' yet. Please choose another extension.',
            ], 422);
        }

        $key = (string) Str::uuid();
        $cart = $request->session()->get('cart', []);
        $cart[$key] = [
            'key' => $key,
            'type' => 'domain',
            'domain' => $domain,
            'domain_option' => 'register',
            'domain_price' => (float) $pricing->registration_price,
            'total' => (float) $pricing->registration_price,
        ];

        $request->session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => $domain . ' added to cart.',
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
