@extends('layouts.dashboard')
@section('content')
<div class="container py-4">
<div class="row justify-content-center mt-4">
<div class="col-lg-7">

<div class="card border-0 shadow-sm mb-4">
<div class="card-body p-4">
<span class="badge bg-light text-dark mb-3">Hosting Plan</span>
<h3 class="fw-bold mb-2 plan" cpanel_planid="{{ $plan->cpanel_planid }}" data-plan-id="{{ $plan->id }}">{{ $plan->name }}</h3>
<p class="text-muted mb-0">{{ $plan->description }}</p>
</div>
</div>

<div class="card border-0 shadow-sm">
<div class="card-body p-4">
<h5 class="fw-bold mb-3">Choose billing cycle</h5>
<div class="list-group">
@foreach([1 => 'Monthly', 6 => 'Semi-Annually', 12 => 'Annually', 24 => 'Biennially'] as $months => $label)
<label class="list-group-item d-flex justify-content-between align-items-center">
<div>
<input class="billing_cycle" type="radio" name="billing_cycle" value="{{ $months }}" amount="{{ $plan->amount * $months }}">
<span class="ms-2">{{ $label }}</span>
</div>
<strong>${{ number_format($plan->amount * $months, 2) }}</strong>
</label>
@endforeach
</div>
</div>
</div>

<div class="mt-4">
<h5 class="fw-bold mb-1">Choose your domain</h5>
<p class="text-muted small mb-3">Choose how you want to connect a domain to this hosting account.</p>
<div class="list-group">
<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 domain-option" style="cursor:pointer;">
<input class="form-check-input flex-shrink-0" type="radio" name="domain_option" value="register">
<div class="flex-grow-1"><div class="fw-semibold">Register a new domain</div><div class="small text-muted">Search for an available domain and register it with us.</div></div>
<i class="bi bi-chevron-right text-muted"></i>
</label>
<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 domain-option" style="cursor:pointer;">
<input class="form-check-input flex-shrink-0" type="radio" name="domain_option" value="transfer">
<div class="flex-grow-1"><div class="fw-semibold">Transfer a domain</div><div class="small text-muted">Transfer your domain from another registrar.</div></div>
<i class="bi bi-chevron-right text-muted"></i>
</label>
<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 domain-option" style="cursor:pointer;">
<input class="form-check-input flex-shrink-0" type="radio" name="domain_option" value="existing">
<div class="flex-grow-1"><div class="fw-semibold">Use an existing domain</div><div class="small text-muted">Use a domain you already own and point it to this hosting account.</div></div>
<i class="bi bi-chevron-right text-muted"></i>
</label>
</div>

<div id="domain-config" class="mt-3 d-none">
<label class="small text-muted" for="domain-input">Domain name</label>
<div class="input-group">
<input type="text" id="domain-input" placeholder="yourdomain.com" class="form-control domain-text" autocomplete="off">
<span class="input-group-text d-none" id="domain-spinner"><span class="spinner-border spinner-border-sm"></span></span>
</div>
<div id="domain-status" class="small mt-2"></div>
<div id="domain-suggestions" class="mt-3 d-none">
<div class="small fw-semibold mb-2">Related domain suggestions</div>
<div id="suggestion-list" class="list-group"></div>
</div>
</div>
</div>

<div class="mt-4 mb-3 cart_amount text-center"></div>
<button type="button" class="btn btn-dark w-100 add_to_cart" disabled>Add to Cart</button>
<div class="small text-muted text-center mt-2">Your hosting account is not created at this step.</div>
</div>
</div>
</div>
@endsection
