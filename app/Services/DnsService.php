<?php

namespace App\Services;

use App\Models\DnsRecord;
use App\Models\Domain;

class DnsService
{
    public function __construct(
        private readonly ResellerClubDnsService $provider
    ) {
    }

    public function syncRecord(Domain $domain, DnsRecord $record): void
    {
        $this->provider->add($domain, $record);

        $record->forceFill([
            'provider_status' => 'synced',
            'provider_error' => null,
        ])->save();
    }

    public function deleteRecord(Domain $domain, DnsRecord $record): void
    {
        $this->provider->delete($domain, $record);
    }
}
