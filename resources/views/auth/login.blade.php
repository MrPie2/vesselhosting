<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@extends('layouts.public')
@section('content')
<div class="container py-5" style="max-width:520px"><div class="bg-white rounded-4 shadow-soft p-4 p-lg-5">
<div class="text-center mb-4"><div class="feature-icon mx-auto"><i class="bi bi-lock"></i></div><h2 class="fw-bold mt-3">Welcome back</h2><p class="text-secondary">Sign in to your Vessel Host account.</p></div>
@if($errors->any())<div class="alert alert-danger rounded-3">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login.store') }}">@csrf
<label class="form-label fw-semibold">Email</label><input class="form-control mb-3" name="email" type="email" required value="{{ old('email') }}">
<label class="form-label fw-semibold">Password</label><div class="input-group mb-3"><input id="password" class="form-control" name="password" type="password" required><button class="btn btn-outline-secondary" type="button" data-password-toggle="password"><i class="bi bi-eye"></i></button></div>
<div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label" for="remember">Remember me</label></div>
<button class="btn btn-vh w-100">Sign in</button></form>
<div class="text-center small mt-4">New here? <a class="text-vh fw-semibold" href="{{ route('register') }}">Create an account</a></div>
</div></div>
@endsection