<?php

namespace App\Http\Controllers;

use App\Models\HostingAccount;
use Illuminate\Support\Facades\Auth;

class HostingController
{
    public function index() {
        $hostings = Auth::user()->hostings()->with('plan','domain')->latest()->paginate(12);
        return view('dashboard.hosting.index', compact('hostings'));
    }

    public function show(HostingAccount $hosting) {
        abort_unless($hosting->user_id === Auth::id(), 403);
        $hosting->load('plan','domain');
        return view('dashboard.hosting.show', compact('hosting'));
    }
}