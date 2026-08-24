<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@extends('layouts.public')
@section('content')
<section class="hero-vh">
  <div class="container hero-grid">
    <div class="row align-items-center g-5 w-100">
      <div class="col-lg-6">
        <div class="small fw-semibold mb-3 opacity-75"><i class="bi bi-stars"></i> Trusted infrastructure · 99.9% uptime target</div>
        <h1 class="display-4 fw-bold lh-1 mb-4">Fast, secure & reliable web hosting.</h1>
        <p class="lead opacity-75 mb-4">Launch websites, register domains and manage DNS from one modern control center.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-3 fw-bold">Get Started</a>
          <a href="#hosting" class="btn btn-outline-light btn-lg rounded-3">View Plans</a>
        </div>
        <div class="d-flex gap-4 mt-4 small opacity-75">
          <span><i class="bi bi-check-circle"></i> Free SSL</span>
          <span><i class="bi bi-check-circle"></i> SSD servers</span>
          <span><i class="bi bi-check-circle"></i> 24/7 support</span>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="glass rounded-4 shadow-soft p-4 text-dark">
          <div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold">Vessel Control Center</span><span class="badge badge-soft">Live</span></div>
          <div class="p-4 rounded-4 bg-light mb-3"><div class="small text-secondary">Your infrastructure</div><div class="h3 fw-bold mt-1">12 services online</div><div class="progress" style="height:8px"><div class="progress-bar" style="width:92%;background:var(--vh-purple)"></div></div></div>
          <div class="row g-3">
            <div class="col-6"><div class="p-3 border rounded-4"><i class="bi bi-globe2 text-vh fs-3"></i><div class="fw-bold mt-2">Domains</div><div class="small text-secondary">DNS included</div></div></div>
            <div class="col-6"><div class="p-3 border rounded-4"><i class="bi bi-hdd-stack text-vh fs-3"></i><div class="fw-bold mt-2">Hosting</div><div class="small text-secondary">cPanel powered</div></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="features" class="container">
  <div class="row g-3 feature-card">
    <div class="col-md-4"><div class="bg-white rounded-4 shadow-soft p-4 h-100"><div class="feature-icon"><i class="bi bi-lightning-charge-fill"></i></div><h5 class="mt-3">Ultra Fast Speed</h5><p class="small text-secondary mb-0">SSD-backed infrastructure with optimized hosting stacks.</p></div></div>
    <div class="col-md-4"><div class="bg-white rounded-4 shadow-soft p-4 h-100"><div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div><h5 class="mt-3">SSL & Security</h5><p class="small text-secondary mb-0">Secure your websites with SSL and sensible account isolation.</p></div></div>
    <div class="col-md-4"><div class="bg-white rounded-4 shadow-soft p-4 h-100"><div class="feature-icon"><i class="bi bi-headset"></i></div><h5 class="mt-3">24/7 Support</h5><p class="small text-secondary mb-0">A customer-first support experience built into the portal.</p></div></div>
  </div>
</section>

<section id="domains" class="section-pad">
  <div class="container">
    <div class="bg-vh text-white rounded-4 p-4 p-lg-5 shadow-soft">
      <div class="row align-items-center g-4">
        <div class="col-lg-5"><div class="small opacity-75">DOMAIN REGISTRATION</div><h2 class="fw-bold mt-2">Your domain. Your DNS.</h2><p class="opacity-75 mb-0">Domain-only customers still get a complete DNS manager. Add A, CNAME, MX, TXT, NS and SRV records without buying hosting.</p></div>
        <div class="col-lg-7"><form method="POST" action="{{ route('domain.search') }}" class="bg-white p-2 rounded-3 d-flex">@csrf<input name="domain" class="form-control border-0" placeholder="Search your next domain" required><button class="btn btn-vh">Search</button></form></div>
      </div>
    </div>
  </div>
</section>

<section id="hosting" class="section-pad pt-0">
  <div class="container">
    <div class="text-center mb-5"><div class="small text-vh fw-bold">HOSTING</div><h2 class="fw-bold">Simple plans that scale</h2><p class="text-secondary">Choose a plan and provision it automatically after payment.</p></div>
    <div class="row g-4">
      @foreach([
        ['Basic','$1.99/mo','1 Website','5GB SSD Storage'],
        ['Standard','$3.99/mo','3 Websites','20GB SSD Storage'],
        ['Premium','$5.99/mo','5 Websites','50GB SSD Storage']
      ] as $i=>$plan)
      <div class="col-lg-4"><div class="price-card bg-white rounded-4 p-4 h-100 {{ $i===1 ? 'shadow-soft' : '' }}">
        @if($i===1)<span class="badge badge-soft mb-3">Most Popular</span>@endif
        <h5 class="fw-bold">{{ $plan[0] }}</h5><div class="display-6 fw-bold my-3">{{ $plan[1] }}</div>
        <div class="small text-secondary mb-4"><div>{{ $plan[2] }}</div><div>{{ $plan[3] }}</div><div>Free SSL</div><div>Priority support</div></div>
        @auth
@else
<a href="{{ route('register') }}" class="btn btn-vh w-100">Choose Plan</a>
@endauth
      </div></div>
      @endforeach
    </div>
  </div>
</section>

<section id="deploy" class="section-pad bg-white">
 <div class="container text-center"><div class="small text-vh fw-bold">GO LIVE IN MINUTES</div><h2 class="fw-bold mt-2">One account for your entire web stack.</h2><p class="text-secondary">Domains, DNS, hosting, billing and provisioning in one portal.</p>
 <div class="row g-3 mt-4 justify-content-center"><div class="col-md-3"><div class="p-4 border rounded-4"><i class="bi bi-globe2 fs-2 text-vh"></i><div class="fw-bold mt-2">Domains</div></div></div><div class="col-md-3"><div class="p-4 border rounded-4"><i class="bi bi-diagram-3 fs-2 text-vh"></i><div class="fw-bold mt-2">DNS</div></div></div><div class="col-md-3"><div class="p-4 border rounded-4"><i class="bi bi-server fs-2 text-vh"></i><div class="fw-bold mt-2">cPanel</div></div></div></div></div>
</section>
@endsection