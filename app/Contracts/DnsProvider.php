<?php

namespace App\Contracts;

use App\Models\Domain;
use App\Models\DnsRecord;

interface DnsProvider
{
    public function syncRecord(Domain $domain, DnsRecord $record): void;
    public function deleteRecord(Domain $domain, DnsRecord $record): void;
}