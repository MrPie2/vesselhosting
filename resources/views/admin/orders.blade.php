@extends('layouts.dashboard')
@section('content')
<h2 class="fw-bold mb-4">Hosting plans</h2>
<div class="row g-3">@foreach($plans as $plan)<div class="col-lg-4"><form method="POST" action="{{ route('admin.plans.update',$plan) }}" class="table-card p-4">@csrf @method('PATCH')
<h5 class="fw-bold">{{ $plan->name }}</h5><div class="small text-secondary mb-3">{{ $plan->slug }}</div>
<label class="form-label">Monthly price</label><input class="form-control mb-3" type="number" step="0.01" name="price_monthly" value="{{ $plan->price_monthly }}">
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="active" value="1" id="plan{{ $plan->id }}" {{ $plan->active?'checked':'' }}><label class="form-check-label" for="plan{{ $plan->id }}">Active</label></div>
<button class="btn btn-vh w-100">Save plan</button></form></div>@endforeach</div>
@endsection