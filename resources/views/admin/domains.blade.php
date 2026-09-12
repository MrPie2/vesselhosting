@extends('layouts.dashboard')
@section('content')
<h2 class="fw-bold mb-4">All domains</h2><div class="table-card table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="px-4">Domain</th><th>Customer</th><th>Status</th><th>Expiry</th></tr></thead><tbody>@foreach($domains as $domain)<tr><td class="px-4 fw-semibold">{{ $domain->name }}</td><td>{{ $domain->user->email }}</td><td>{{ $domain->status }}</td><td>{{ $domain->expiry_date?->format('M d, Y') ?: '—' }}</td></tr>@endforeach</tbody></table></div>
@endsection