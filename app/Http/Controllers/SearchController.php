<?php

namespace App\Http\Controllers;

use App\Services\ResellerClubService;
use Illuminate\Http\Request;

class SearchController
{
    public function search_domain(Request $request, ResellerClubService $resellerClub)
    {
        $data = $request->validate([
            'domain_text' => 'required|string|max:253',
        ]);

        return response()->json($resellerClub->check($data['domain_text']));
    }
}
