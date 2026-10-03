<?php

namespace App\Http\Controllers;

use App\Services\ResellerClubService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainSearchController
{
    public function search(Request $request, ResellerClubService $resellerClub)
    {
        $data = $request->validate([
            'domain' => 'required|string|max:253',
        ]);

        $domain = strtolower(trim($data['domain']));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = trim($domain, '/.');

        if (!Str::contains($domain, '.')) {
            return back()->withErrors([
                'domain' => 'Enter a full domain such as example.com.',
            ])->withInput();
        }

        $result = $resellerClub->check($domain);

        if (!($result['success'] ?? false)) {
            return back()->withErrors([
                'domain' => $result['message'] ?? 'Domain search is temporarily unavailable.',
            ])->withInput();
        }

        return view('domain-search', [
            'domain' => $domain,
            'result' => $result,
            'available' => (bool) ($result['available'] ?? false),
        ]);
    }
}
