<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CreateAccountService;
use Illuminate\Support\Facades\Auth;
use App\Models\Hosting;

class CreateAccountController
{
    public function createhosting(Request $request, CreateAccountService $createAccountService)
    {
        $user_id = Auth::id();
        $domain=$request->input('domain');
        $cycle=$request->input('cycle');
        $plan=$request->input('plan');
        $password=bin2hex(random_bytes(8));
        $username='vh' . substr(hash('sha256', (string) $user_id), 0, 10);
        $expiry_date = now()->addMonths((int) $cycle);

        $data = [
            'username' => $username,
            'domain' => $domain,
            'password' => $password,
            'plan' => $plan,
            'expiry_date' => $expiry_date,
            'server_hostname' => "s15256.fra1.stableserver.net",

        ];

        $addhosting = Hosting::create([
            'username' => $username,
            'domain' => $domain,
            'password' => $password,
            'plan_id' => $plan,
            'expiry_date' => $expiry_date,
            'server_hostname' => "s15256.fra1.stableserver.net",
            'user_id' => $user_id,
            'duration' => $cycle,
        ]); 

        // Call the create method of CreateAccountService
        if(!$addhosting) {
            return response()->json(['message' => "Error creating hosting account"], 500);
        }else{
            $result = $createAccountService->create($data);
            return response()->json(['message'=>"Account Created Successfully", 'data'=>$result]);
        }
        
    }
}



