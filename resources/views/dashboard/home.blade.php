@extends('layouts.dashboard')
@section('content')
<div class="mb-4"><h2 class="fw-bold mb-1">Good morning, {{ Str::before($user->name,' ') }} 👋</h2><p class="text-secondary mb-0">Here’s what’s happening with your web services.</p></div>
<div class="row g-3 mb-4">
@foreach([['Domains',$domains->count(),'bi-globe2'],['Hosting',$hostings->count(),'bi-server'],['Orders',$orders->count(),'bi-receipt'],['DNS Records',$domains->sum(fn($d)=>$d->dnsRecords()->count()),'bi-diagram-3']] as $stat)
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon"><i class="bi {{ $stat[2] }}"></i></div><div class="small text-secondary mt-3">{{ $stat[0] }}</div><div class="h3 fw-bold mb-0">{{ $stat[1] }}</div></div></div>
@endforeach
</div>
<div class="row g-4">
<div class="col-xl-7"><div class="table-card"><div class="p-4 border-bottom d-flex justify-content-between"><h5 class="fw-bold mb-0">Your domains</h5><a class="text-vh fw-semibold" href="{{ route('dashboard.domains') }}">View all</a></div>
@forelse($domains->take(5) as $domain)
<div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom"><div>
    <div class="fw-semibold">{{ $domain->domain }}</div>
    <div class="small text-secondary">{{ $domain->expiry_date ? 'Expires '.$domain->expiry_date->format('M d, Y') : 'No expiry date' }}</div>
</div><a class="btn btn-sm btn-outline-dark rounded-3" href="{{ route('dashboard.domains.show',$domain) }}">Manage</a></div>
@empty<div class="p-4 text-secondary">No domains yet.</div>
@endforelse
</div></div>
<div class="col-xl-5"><div class="bg-vh text-white rounded-4 p-4 h-100"><div class="small opacity-75">NEED A WEBSITE?</div><h4 class="fw-bold mt-2">Launch hosting in minutes.</h4><p class="opacity-75">Connect a domain, provision cPanel and manage DNS without leaving your account.</p><a class="btn btn-light rounded-3 fw-semibold" href="{{ route('dashboard.hosting') }}">View hosting</a></div></div>
</div>
@endsection