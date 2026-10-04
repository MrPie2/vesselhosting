<?php

namespace App\Http\Controllers;

use App\Models\DnsRecord;
use App\Models\Domain;
use App\Services\DnsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DnsRecordController
{
    private const TYPES = [
        'A',
        'AAAA',
        'CAA',
        'MX',
        'CNAME',
        'TXT',
        'DMARC',
        'SRV',
        'SVCB',
        'HTTP',
    ];

    public function store(Request $request, Domain $domain, DnsService $dns): RedirectResponse
    {
        abort_unless((string) $domain->user_id === (string) Auth::id(), 403);

        $data = $request->validate([
            'type' => ['required', Rule::in(self::TYPES)],
            'name' => ['required', 'string', 'max:253'],
            'value' => ['required', 'string', 'max:4096'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        if (in_array($data['type'], ['MX', 'SRV'], true) && $data['priority'] === null) {
            return back()->withErrors([
                'priority' => $data['type'] . ' records require a priority.',
            ])->withInput();
        }

        if ($data['type'] === 'A' && filter_var($data['value'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return back()->withErrors(['value' => 'A records require a valid IPv4 address.'])->withInput();
        }

        if ($data['type'] === 'AAAA' && filter_var($data['value'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return back()->withErrors(['value' => 'AAAA records require a valid IPv6 address.'])->withInput();
        }

        if ($data['type'] === 'DMARC' && in_array(trim($data['name']), ['', '@'], true)) {
            $data['name'] = '_dmarc';
        }

        $record = $domain->dnsRecords()->create([
            'type' => $data['type'],
            'name' => trim($data['name']),
            'content' => trim($data['value']),
            'priority' => $data['priority'] ?? null,
            'provider_status' => 'pending',
        ]);

        try {
            $dns->syncRecord($domain, $record);
        } catch (\Throwable $e) {
            $record->forceFill([
                'provider_status' => 'failed',
                'provider_error' => $e->getMessage(),
            ])->save();

            return back()->withErrors([
                'dns' => 'ResellerClub rejected this DNS record: ' . $e->getMessage(),
            ])->withInput();
        }

        return back()->with('status', 'DNS record added and provisioned successfully.');
    }

    public function destroy(Domain $domain, DnsRecord $record, DnsService $dns): RedirectResponse
    {
        abort_unless(
            (string) $domain->user_id === (string) Auth::id()
            && (int) $record->domain_id === (int) $domain->id,
            403
        );

        try {
            $dns->deleteRecord($domain, $record);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'dns' => 'ResellerClub rejected the DNS record deletion: ' . $e->getMessage(),
            ]);
        }

        $record->delete();

        return back()->with('status', 'DNS record removed successfully.');
    }
}
