@extends('layouts.dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
<div><a class="small text-vh" href="{{ route('dashboard.domains') }}">← Domains</a><h2 class="fw-bold mt-2">{{ $domain->name }}</h2><div class="text-secondary small">{{ ucfirst(str_replace('_',' ',$domain->status)) }} · Expires {{ $domain->expiry_date?->format('M d, Y') ?: 'pending' }}</div></div>
<div class="d-flex gap-2"><button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#renew">Renew</button><span class="badge badge-soft align-self-center">{{ $domain->hosting ? 'Hosting attached' : 'Domain only' }}</span></div>
</div>
<div class="row g-4">
<div class="col-lg-8"><div class="table-card p-4"><div class="d-flex justify-content-between mb-3"><div><h5 class="fw-bold mb-1">DNS records</h5><div class="small text-secondary">DNS is available even when this domain has no hosting account.</div></div></div>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Type</th><th>Name</th><th>Value</th><th>TTL</th><th></th></tr></thead><tbody>
@forelse($domain->dnsRecords as $record)<tr><td><span class="badge bg-light text-dark">{{ $record->type }}</span></td><td>{{ $record->name }}</td><td class="text-break">{{ $record->content }}</td><td>{{ $record->ttl }}</td><td><form method="POST" action="{{ route('dashboard.dns.destroy',[$domain,$record]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="5" class="text-center text-secondary py-4">No DNS records yet.</td></tr>@endforelse</tbody></table></div>
<hr><h6 class="fw-bold">Add DNS record</h6>
<form method="POST" action="{{ route('dashboard.dns.store',$domain) }}" class="row g-2">@csrf
<div class="col-md-2"><select name="type" class="form-select"><option>A</option><option>AAAA</option><option>CNAME</option><option>MX</option><option>TXT</option><option>NS</option><option>SRV</option></select></div>
<div class="col-md-3"><input name="name" class="form-control" placeholder="@ or www" required></div>
<div class="col-md-4"><input name="content" class="form-control" placeholder="Record value" required></div>
<div class="col-md-2"><input name="ttl" type="number" class="form-control" value="3600" min="60" max="86400" required></div>
<div class="col-md-1"><button class="btn btn-vh w-100">+</button></div>
<div class="col-md-3"><input name="priority" type="number" class="form-control" placeholder="MX priority"></div>
</form></div></div>
<div class="col-lg-4"><div class="table-card p-4"><h5 class="fw-bold">Nameservers</h5><p class="small text-secondary">For DNS records to resolve, the domain must be delegated to the DNS provider shown here.</p>
<div class="p-3 rounded-3 bg-light small mb-2">{{ $domain->nameserver_1 ?: 'Pending' }}</div>
<div class="p-3 rounded-3 bg-light small">{{ $domain->nameserver_2 ?: 'Pending' }}</div>
</div></div></div>
<div class="modal fade" id="renew" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST" action="{{ route('dashboard.domains.renew',$domain) }}">@csrf<div class="modal-header"><h5 class="modal-title">Renew {{ $domain->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">Years</label><select name="years" class="form-select">@for($i=1;$i<=10;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div><div class="modal-footer"><button class="btn btn-vh">Create renewal order</button></div></form></div></div></div>
@endsection