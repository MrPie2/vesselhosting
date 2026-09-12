<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AddDomain;
use Illuminate\Support\Facades\Auth;
use App\Models\Hosting;

class SearchController
{
   public function search_domain(Request $request){
    $v= $request->input('domain_text');
    $user_id = Auth::id();
        $post = AddDomain::create([
            'domain' => $v,
            'user_id' => $user_id,
            'expiry_date' => now()->addMonths(12),
            'status' => "Active",
        ]);

        $addhosting = Hosting::create([
            'domain' => $v,
            'user_id' => $user_id,
            'server_hostname' => "stableserver.net",
            'username' => substr($v, 0, 4),


        ]); 

    if($post && $addhosting){
      return response()->json(['message'=>"Domain created successfully"]);  
    }else{
      return  response()->json(['message'=>"error somewhere"]);
    }
   } 
}
