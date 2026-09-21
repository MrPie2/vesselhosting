<?php

namespace App\Http\Controllers;

use App\Models\Hosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
class HostingController
{
    public function index() {
        $hostings = Auth::user()->hostings()->with('plan','domain')->latest()->paginate(12);
        $loginUrl=$this->show_cpanel();
        return view('dashboard.hosting.index', compact('hostings', 'loginUrl'));
    }

    public function show(Hosting $hosting) {
        abort_unless($hosting->user_id === Auth::id(), 403);
        $hosting->load('plan','domain');
        return view('dashboard.hosting.show', compact('hosting'));
    }

    public function show_cpanel(){
            $data = Auth::user()->hostings()->get();
            foreach ($data as $res) {
            $hostname = $res->server_hostname;
            $cpanelUsername = $res->username;
            }

            
            $whmUsername=config('services.whm.username');
            $whmApiToken=config('services.whm.token');
            
            $response = Http::withHeaders([
            'Authorization' => 'whm ' . $whmUsername . ':' . $whmApiToken,
            'Accept' => 'application/json',])->get(
            "https://{$hostname}:2087/json-api/create_user_session",
                [
                    'api.version' => 1,
                    'user' => $cpanelUsername,
                    'service' => 'cpaneld',
                ]);

$result = $response->json();
return $loginUrl = $result['data']['url'] ?? null;
    }
    
}