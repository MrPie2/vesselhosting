@extends('layouts.dashboard')
@section('content')
<div class="mb-4"><h2 class="fw-bold mb-1">Hosting</h2><p class="text-secondary mb-0">Your cPanel hosting accounts and provisioning status.</p></div>
<div class="row g-3">@forelse($hostings as $hosting)<div class="col-lg-6">
    <div class="table-card p-4"><div class="d-flex justify-content-between">
        <div><div class="fw-bold">{{ $hosting->domain ?: 'Hosting account' }}</div>
        <div class="small text-secondary">{{ $hosting->plan?->name }}</div>
    </div><span class="badge badge-soft">{{ ucfirst($hosting->status) }}</span></div>
    <hr><div class="row small"><div class="col-6 text-secondary">Username</div>
    <div class="col-6 text-end">{{ $hosting->username ?: 'Provisioning…' }}</div>
    <div class="col-6 text-secondary mt-2">Server</div><div class="col-6 text-end mt-2">{{ $hosting->server_hostname ?: 'Pending' }}</div></div><a class="btn btn-vh w-100 mt-4" href="{{ route('dashboard.hosting.show',$hosting) }}">Manage hosting</a></div></div>@empty<div class="col-12"><div class="table-card p-5 text-center text-secondary">No hosting accounts yet.</div></div>@endforelse</div>
@endsection