<?php
namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\DnsRecord;
use App\Services\DnsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DnsRecordController
{
    public function store(Request $request, Domain $domain, DnsService $dns) {
        abort_unless($domain->user_id === Auth::id(),403);

        $data = $request->validate([
            'type'=>'required|in:A,AAAA,CNAME,MX,TXT,NS,SRV',
            'name'=>'required|string|max:253',
            'content'=>'required|string|max:2048',
            'ttl'=>'required|integer|min:60|max:86400',
            'priority'=>'nullable|integer|min:0|max:65535',
        ]);

        $record = $domain->dnsRecords()->create($data);

        try {
            $dns->syncRecord($domain,$record);
        } catch (\Throwable $e) {
            $record->delete();
            return back()->withErrors(['dns'=>'DNS provider rejected the record: '.$e->getMessage()]);
        }

        return back()->with('status','DNS record added successfully.');
    }

    public function destroy(Domain $domain, DnsRecord $record, DnsService $dns) {
        abort_unless($domain->user_id === Auth::id() && $record->domain_id === $domain->id,403);
        $dns->deleteRecord($domain,$record);
        $record->delete();
        return back()->with('status','DNS record removed.');
    }
}