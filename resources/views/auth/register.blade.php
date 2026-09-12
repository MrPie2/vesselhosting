<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@extends('layouts.public')
@section('content')
<div class="container py-5" style="max-width:560px"><div class="bg-white rounded-4 shadow-soft p-4 p-lg-5">
<div class="text-center mb-4"><div class="feature-icon mx-auto"><i class="bi bi-person-plus"></i></div><h2 class="fw-bold mt-3">Create your account</h2><p class="text-secondary">Manage domains, DNS and hosting from one dashboard.</p></div>
@if($errors->any())<div class="alert alert-danger rounded-3"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('register.store') }}">@csrf
<label class="form-label fw-semibold">Full name</label><input class="form-control mb-3" name="name" required value="{{ old('name') }}">
<label class="form-label fw-semibold">Email</label><input class="form-control mb-3" name="email" type="email" required value="{{ old('email') }}">
<label class="form-label fw-semibold">Password</label><input class="form-control mb-3" name="password" type="password" required>
<label class="form-label fw-semibold">Confirm password</label><input class="form-control mb-4" name="password_confirmation" type="password" required>
<button class="btn btn-vh w-100">Create account</button></form>
</div></div>
@endsection