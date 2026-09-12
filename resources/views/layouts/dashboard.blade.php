<!doctype html>
<html lang="en"><head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $title ?? 'Dashboard' }} · Vessel Host</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body><div class="dashboard-shell">
<aside class="sidebar p-3"><a href="{{ route('home') }}" class="d-flex align-items-center gap-2 mb-4 px-2 text-white"><span class="feature-icon" style="width:40px;height:40px"><i class="bi bi-water"></i></span><strong>Vessel Host</strong></a>
<div class="small text-uppercase opacity-50 px-2 mb-2">Workspace</div>
<nav class="d-grid gap-1">
<a class="{{ request()->routeIs('dashboard.home')?'active':'' }}" href="{{ route('dashboard.home') }}"><i class="bi bi-grid"></i> Overview</a>
<a class="{{ request()->routeIs('dashboard.products*')?'active':'' }}" href="{{ route('dashboard.products') }}"><i class="bi bi-chart"></i> Place new order</a>

<a class="{{ request()->routeIs('dashboard.domains*')?'active':'' }}" href="{{ route('dashboard.domains') }}"><i class="bi bi-globe2"></i> Domains</a>

<a class="{{ request()->routeIs('dashboard.hosting*')?'active':'' }}" href="{{ route('dashboard.hosting') }}"><i class="bi bi-server"></i> Hosting</a>
<a class="{{ request()->routeIs('dashboard.orders*')?'active':'' }}" href="{{ route('dashboard.orders') }}"><i class="bi bi-receipt"></i> Billing</a>
@if(auth()->user()->role === 'admin')
<div class="small text-uppercase opacity-50 px-2 mt-3 mb-1">Administration</div>
<a href="{{ route('admin.home') }}"><i class="bi bi-speedometer2"></i> Admin Center</a>
@endif
</nav>
<div class="mt-auto position-absolute bottom-0 start-0 end-0 p-3"><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-light w-100 rounded-3"><i class="bi bi-box-arrow-right"></i> Sign out</button></form></div>
</aside>
<main class="dashboard-main"><header class="topbar d-flex align-items-center justify-content-between px-3 px-lg-4"><div><span class="fw-bold">{{ $title ?? 'Dashboard' }}</span></div><div class="d-flex align-items-center gap-3"><span class="small text-secondary d-none d-md-block">{{ auth()->user()->email }}</span><div class="rounded-circle bg-vh text-white d-grid place-items-center" style="width:38px;height:38px">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div></div></header>
<div class="p-3 p-lg-4">@if(session('status'))<div class="alert alert-success rounded-3">{{ session('status') }}</div>@endif @yield('content')</div></main>
</div></body></html>