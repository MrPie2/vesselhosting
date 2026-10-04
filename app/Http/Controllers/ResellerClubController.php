<?php

namespace App\Http\Controllers;

use App\Models\DomainPrice;
use App\Services\ResellerClubService;
use Illuminate\Http\Request;

class ResellerClubController
{
    public function check(Request $request, ResellerClubService $resellerClub)
    {
        $data = $request->validate(['domain' => 'required|string|max:253']);
        return response()->json($resellerClub->check($data['domain']));
    }

    public function suggestions(Request $request, ResellerClubService $resellerClub)
    {
        $data = $request->validate(['domain' => 'required|string|max:253']);

        $suggestions = $resellerClub->suggestions($data['domain'], 10);

        $suggestions = collect($suggestions)
            ->map(function (string $name) {
                $tld = '.' . ltrim(strtolower(substr($name, strrpos($name, '.') + 1)), '.');
                $price = DomainPrice::where('tld', $tld)->where('active', true)->first();

                if (!$price) {
                    return null;
                }

                return [
                    'domain' => $name,
                    'tld' => $tld,
                    'price' => (float) $price->registration_price,
                ];
            })
            ->filter()
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
        ]);
    }
}
