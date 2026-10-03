<?php

namespace App\Http\Controllers;

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

        return response()->json([
            'success' => true,
            'suggestions' => $resellerClub->suggestions($data['domain']),
        ]);
    }
}
