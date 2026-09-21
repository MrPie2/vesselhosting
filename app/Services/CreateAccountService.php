<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Contracts\Registrar;
use Illuminate\Support\Facades\Auth;

class CreateAccountService
{
    public function __construct()
    {  
    }


public function create(array $data): array
{
    $username = config('services.whm.username');
    $apiToken = config('services.whm.token');
    $hostname = config('services.whm.hostname');

    $query = [
        'api.version' => 1,
        'username' => $data['username'],
        'domain'   => $data['domain'],
        'password' => $data['password'],
        'plan' => $data['plan'], // Your hosting package 

    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://{$hostname}:2087/json-api/createacct?" . http_build_query($query),

        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: whm {$username}:{$apiToken}",
        ],

        CURLOPT_CUSTOMREQUEST => 'GET',

        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
        $error = curl_error($curl);
        curl_close($curl);

        throw new \Exception("WHM API error: {$error}");
    }

    curl_close($curl);

    $result = json_decode($response, true);

    if (!is_array($result)) {
        throw new \Exception('Invalid response from WHM.');
    }

    return $result;
}
}
