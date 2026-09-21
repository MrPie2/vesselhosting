@extends('layouts.dashboard')
@section('content')
<a class="small text-vh" href="{{ route('dashboard.hosting') }}">← Hosting</a>
<<<<<<< HEAD
<div class="d-flex justify-content-between align-items-center mt-2 mb-4"><div>
    <h2 class="fw-bold mb-1">{{ $hosting->domain?->name }}</h2>
    <p class="text-secondary mb-0">{{ $hosting->plan?->name }} · {{ $hosting->server_hostname ?: 'Provisioning server' }}</p></div><span class="badge badge-soft">{{ ucfirst($hosting->provisioning_status) }}</span></div>
<div class="row g-4">
    <div class="col-md-4"><div class="stat-card">
        <div class="stat-icon"><i class="bi bi-person-badge">

        </i></div><div class="small text-secondary mt-3">cPanel username</div>
        <div class="h5 fw-bold">{{ $hosting->username ?: 'Pending' }}</div>
    </div></div><div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-hdd"></i></div>
            <div class="small text-secondary mt-3">Plan</div>
            <div class="h5 fw-bold">{{ $hosting->plan?->name }}</div>
        </div></div><div class="col-md-4"><div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-event">

            </i></div><div class="small text-secondary mt-3">Next due date</div>
            <div class="h5 fw-bold">{{ $hosting->next_due_date?->format('M d, Y') ?: 'Pending' }}</div>
        </div></div></div>
=======
<div class="d-flex justify-content-between align-items-center mt-2 mb-4"><div><h2 class="fw-bold mb-1">{{ $hosting->domain?->name }}</h2><p class="text-secondary mb-0">{{ $hosting->plan?->name }} · {{ $hosting->server_hostname ?: 'Provisioning server' }}</p></div><span class="badge badge-soft">{{ ucfirst($hosting->provisioning_status) }}</span></div>
<div class="row g-4"><div class="col-md-4"><div class="stat-card"><div class="stat-icon"><i class="bi bi-person-badge"></i></div><div class="small text-secondary mt-3">cPanel username</div><div class="h5 fw-bold">{{ $hosting->username ?: 'Pending' }}</div></div></div><div class="col-md-4"><div class="stat-card"><div class="stat-icon"><i class="bi bi-hdd"></i></div><div class="small text-secondary mt-3">Plan</div><div class="h5 fw-bold">{{ $hosting->plan?->name }}</div></div></div><div class="col-md-4"><div class="stat-card"><div class="stat-icon"><i class="bi bi-calendar-event"></i></div><div class="small text-secondary mt-3">Next due date</div><div class="h5 fw-bold">{{ $hosting->next_due_date?->format('M d, Y') ?: 'Pending' }}</div></div></div></div>
>>>>>>> origin/main
@endsection