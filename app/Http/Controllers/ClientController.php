<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ClientController
{
    public function index() {
        $user = Auth::user();
        return view('dashboard.home', [
            'user'=>$user,
            'domains'=>$user->domains()->latest()->get(),
            'hostings'=>$user->hostings()->with('plan','domain')->latest()->get(),
            'orders'=>$user->orders()->latest()->take(5)->get(),
        ]);
    }
}