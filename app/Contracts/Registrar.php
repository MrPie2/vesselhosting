<?php

namespace App\Contracts;

interface Registrar
{
    public function checkAvailability(string $domain): array;
    public function register(string $domain, int $years, array $contact): array;
    public function renew(string $domain, int $years): array;
    public function setNameservers(string $domain, array $nameservers): array;
}