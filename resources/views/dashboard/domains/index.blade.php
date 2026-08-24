<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

@extends('layouts.dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="fw-bold mb-1">Domains</h2><p class="text-secondary mb-0">Register, renew and manage DNS.</p></div><a class="btn btn-vh" href="{{ route('home') }}#domains"><i class="bi bi-plus-lg"></i> Register domain</a></div>
<div class="table-card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="px-4">Domain</th><th>Status</th><th>Expiry</th><th>DNS</th><th></th></tr></thead><tbody>
@forelse($domains as $domain)<tr><td class="px-4"><strong>{{ $domain->name }}</strong><div class="small text-secondary">{{ $domain->registrar ?: 'External / managed DNS' }}</div></td><td><span class="badge badge-soft">{{ ucfirst($domain->status) }}</span></td><td>{{ $domain->expiry_date?->format('M d, Y') ?: '—' }}</td><td>{{ $domain->dns_records_count }} records</td><td><a class="btn btn-sm btn-outline-dark rounded-3" href="{{ route('dashboard.domains.show',$domain) }}">Manage</a></td></tr>
@empty<tr><td colspan="5" class="p-5 text-center text-secondary">No domains found.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $domains->links() }}</div></div>
@endsection