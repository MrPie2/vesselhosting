<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Contracts\Registrar;

class ResellerClubRegistrar implements Registrar{
<<<<<<< HEAD
    protected string $baseurl='https://domaincheck.httpapi.com/api'
public function checkAvailability(array, $domains, array $tlds){
=======
    protected string $baseurl='https://domaincheck.httpapi.com/api';

public function checkAvailability(array $domains, array $tlds){
>>>>>>> origin/main
    $response=Http::get($this->baseurl.'/domain/available.json', [
    'auth-userid'=>env('RESELLERCLUB_USER_ID'),
    'api-key'=>env('RESELLERCLUB_API_KEY'),
    'domain-name'=>$domains,
    'tlds'=>$tlds,
]);

<<<<<<< HEAD
retun $response->json();
=======
return $response->json();
>>>>>>> origin/main
}
}