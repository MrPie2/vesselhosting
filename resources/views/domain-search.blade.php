@extends('layouts.public')
@section('content')
<section class="section-pad"><div class="container" style="max-width:900px">
<div class="text-center mb-5"><div class="small text-vh fw-bold">DOMAIN SEARCH</div><h1 class="fw-bold">{{ $domain }}</h1></div>
<div class="table-card p-4">
@php $available = (bool)($result['available'] ?? false); @endphp
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
<div><span class="badge {{ $available?'badge-soft':'bg-light text-secondary' }}">{{ $available?'Available':'Unavailable' }}</span><h4 class="fw-bold mt-2 mb-0">{{ $domain }}</h4></div>
@if($available && auth()->check())
<a class="btn btn-vh" href="{{ route('checkout.domain',['domain'=>$domain]) }}">Register domain</a>
@elseif($available)
<a class="btn btn-vh" href="{{ route('login') }}">Login to register</a>
@endif
</div>
@if(!$available)<div class="alert alert-light mt-4 mb-0">The registrar returned: {{ $result['status'] ?? 'unavailable' }}</div>@endif
</div></div></section>
@endsection