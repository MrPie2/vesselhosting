<?php

namespace App\Http\Controllers;
use App\Services\ResellerClubService;
use Illuminate\Http\Request;

class ResellerClubController
{
    public function check(Request $request, ResellerClubService $resellerClub){
$request-> validate([
    'domain' => 'required|string|max:255'
]);

return response()->json($resellerClub->checkDomain($request->domain));
    }
}
