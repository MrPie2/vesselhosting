<!doctype html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/vesselhost.css') }}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'Dashboard' }} · Vessel Host</title>
</head>
<body>
<div class="dashboard-shell">
<aside class="sidebar p-3" id="dashboard-sidebar"><a href="{{ route('home') }}" class="d-flex align-items-center gap-2 mb-4 px-2 text-white"><span class="feature-icon" style="width:40px;height:40px"><i class="bi bi-water"></i></span><strong>Vessel Host</strong></a>

<nav class="d-grid gap-1">

@if(auth()->user()?->role ==="admin")
<a href="{{ route('admin.home') }}"><i class="bi bi-speedometer2"></i> Admin Center</a>
<a class="{{ request()->routeIs('admin.customers*')?'active':'' }}" href="{{ route('admin.customers') }}"><i class="bi bi-user"></i> Customers</a>
<a class="{{ request()->routeIs('admin.domains*')?'active':'' }}" href="{{ route('admin.domains') }}"><i class="bi bi-globe2"></i> Domains</a>
<a class="{{ request()->routeIs('admin.hosting*')?'active':'' }}" href="{{ route('admin.hosting') }}"><i class="bi bi-server"></i> Hosting</a>
<a class="{{ request()->routeIs('admin.orders*')?'active':'' }}" href="{{ route('admin.orders') }}"><i class="bi bi-receipt"></i> Billing</a>
<a class="{{ request()->routeIs('admin.plans*')?'active':'' }}" href="{{ route('admin.plans') }}"><i class="bi bi-box-seam"></i> Hosting Plans</a>
<a class="{{ request()->routeIs('admin.domain-prices*')?'active':'' }}" href="{{ route('admin.domain-prices') }}"><i class="bi bi-tags"></i> Domain Pricing</a>

@else
<div class="small text-uppercase opacity-50 px-2 mb-2">Workspace</div>
<a class="{{ request()->routeIs('dashboard.home')?'active':'' }}" href="{{ route('dashboard.home') }}"><i class="bi bi-grid"></i> Overview</a>
<a class="{{ request()->routeIs('dashboard.products*')?'active':'' }}" href="{{ route('dashboard.products') }}"><i class="bi bi-chart"></i> Place new order</a>
<a class="{{ request()->routeIs('dashboard.domains*')?'active':'' }}" href="{{ route('dashboard.domains') }}"><i class="bi bi-globe2"></i> Domains</a>
<a class="{{ request()->routeIs('dashboard.hosting*')?'active':'' }}" href="{{ route('dashboard.hosting') }}"><i class="bi bi-server"></i> Hosting</a>
<a class="{{ request()->routeIs('dashboard.orders*')?'active':'' }}" href="{{ route('dashboard.orders') }}"><i class="bi bi-receipt"></i> Billing</a>
@endif
</nav>
<div class="mt-auto position-absolute bottom-0 start-0 end-0 p-3">
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button class="btn btn-outline-light w-100 rounded-3"><i class="bi bi-box-arrow-right"></i> Sign out</button></form>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<main class="dashboard-main">
    <header class="topbar d-flex align-items-center justify-content-between px-3 px-lg-4">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light mobile-menu-btn" id="mobile-menu-btn" aria-label="Open navigation" aria-controls="dashboard-sidebar" aria-expanded="false">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fw-bold">{{ $title ?? 'Dashboard' }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-secondary d-none d-md-block">{{ auth()->user()?->email }}</span>
            <div class="rounded-circle bg-vh text-white d-grid place-items-center" style="width:38px;height:38px">{{ strtoupper(substr(auth()->user()?->name,0,1)) }}</div>
        </div></header>
<div class="p-3 p-lg-4">@if(session('status'))
    <div class="alert alert-success rounded-3">{{ session('status') }}</div>
    @endif 
    @yield('content')</div></main>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('dashboard-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const button = document.getElementById('mobile-menu-btn');

    function toggleSidebar(open) {
        if (!sidebar) return;
        sidebar.classList.toggle('mobile-open', open);
        if (overlay) overlay.classList.toggle('show', open);
        if (button) {
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.innerHTML = open
                ? '<i class="bi bi-x-lg"></i>'
                : '<i class="bi bi-list fs-4"></i>';
        }
        document.body.classList.toggle('sidebar-open', open);
    }

    if (button) button.addEventListener('click', function () {
        toggleSidebar(!sidebar.classList.contains('mobile-open'));
    });

    if (overlay) overlay.addEventListener('click', function () {
        toggleSidebar(false);
    });

    document.querySelectorAll('#dashboard-sidebar a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 991) toggleSidebar(false);
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) toggleSidebar(false);
    });
});
</script>
</body>
</html>