@extends('layouts.public')
@section('content')
<section class="section-pad"><div class="container" style="max-width:760px">
<div class="text-center mb-4"><div class="small text-vh fw-bold">DOMAIN CHECKOUT</div><h1 class="fw-bold">{{ $domain }}</h1><p class="text-secondary">Choose your registration term.</p></div>
<form method="POST" action="{{ route('checkout.domain.purchase') }}" class="table-card p-4">
@csrf<input type="hidden" name="domain" value="{{ $domain }}">
<label class="form-label fw-semibold">Registration period</label>
<select name="years" class="form-select mb-4">@for($i=1;$i<=10;$i++)<option value="{{ $i }}">{{ $i }} year{{ $i>1?'s':'' }} · {{ $price->currency }} {{ number_format($price->register_price*$i,2) }}</option>@endfor</select>
<div class="d-flex justify-content-between align-items-center"><span class="text-secondary">Starting price</span><strong>{{ $price->currency }} {{ number_format($price->register_price,2) }}/year</strong></div>
<button class="btn btn-vh w-100 mt-4">Continue to billing</button>
</form></div></section>
@endsection