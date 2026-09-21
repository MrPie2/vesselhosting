<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Contracts\Registrar;

class ResellerClubRegistrar implements Registrar{
    /*
    protected string $baseurl='https://domaincheck.httpapi.com/api';
public function checkAvailability(array $domains, array $tlds){
    $response=Http::get($this->baseurl.'/domain/available.json', [
    'auth-userid'=>env('RESELLERCLUB_USER_ID'),
    'api-key'=>env('RESELLERCLUB_API_KEY'),
    'domain-name'=>$domains,
    'tlds'=>$tlds,
]);

return $response->json();
}
*/
}