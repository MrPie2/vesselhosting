<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Contracts\Registrar;

class SuspendExpiredHostingService implements Registrar
{
  public function __construct()
    {  
    }   
  
public function suspendHosting($hosting): bool
{
    $hostname = config('services.whm.hostname');
    $username = config('services.whm.username');
    $token    = config('services.whm.token');

    // This must be the actual cPanel username
    $cpanelUsername = $hosting->username;

    if (empty($cpanelUsername)) {
        throw new \Exception(
            "No cPanel username found for hosting ID {$hosting->id}."
        );
    }

    $url = "https://{$hostname}:2087/json-api/suspendacct";

    $query = http_build_query([
        'api.version' => 1,
        'user' => $cpanelUsername,
        'reason' => 'Hosting subscription expired',
        'disallowun' => 1,
        'leave-ftp-accts-enabled' => 0,
    ]);

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url . '?' . $query,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: whm {$username}:{$token}",
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        throw new \Exception(
            "WHM suspendacct request failed: {$error}"
        );
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $result = json_decode($response, true);

    if (!is_array($result)) {
        throw new \Exception(
            "Invalid WHM API response: {$response}"
        );
    }

    if (($result['metadata']['result'] ?? 0) != 1) {
        $reason = $result['metadata']['reason']
            ?? 'Unknown WHM API error';

        throw new \Exception(
            "Unable to suspend cPanel account {$cpanelUsername}: {$reason}"
        );
    }

    return true;
}


}
