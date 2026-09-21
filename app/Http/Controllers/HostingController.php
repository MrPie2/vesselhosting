<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Hosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
=======
use App\Models\HostingAccount;
use Illuminate\Support\Facades\Auth;

>>>>>>> origin/main
class HostingController
{
    public function index() {
        $hostings = Auth::user()->hostings()->with('plan','domain')->latest()->paginate(12);
<<<<<<< HEAD
        $loginUrl=$this->show_cpanel();
        return view('dashboard.hosting.index', compact('hostings', 'loginUrl'));
    }

    public function show(Hosting $hosting) {
=======
        return view('dashboard.hosting.index', compact('hostings'));
    }

    public function show(HostingAccount $hosting) {
>>>>>>> origin/main
        abort_unless($hosting->user_id === Auth::id(), 403);
        $hosting->load('plan','domain');
        return view('dashboard.hosting.show', compact('hosting'));
    }
<<<<<<< HEAD

    public function show_cpanel(){
            $data = Auth::user()->hostings()->get();
            foreach ($data as $res) {
            $hostname = $res->server_hostname;
            $cpanelUsername = $res->username;
            }

            
            $whmUsername=config('services.whm.whm_username');
            $whmApiToken=config('services.whm.whm_token');
            
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
    
=======
>>>>>>> origin/main
}