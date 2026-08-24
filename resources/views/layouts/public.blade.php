<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vessel Host' }} · Vessel Host</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-vh sticky-top">
  <div class="container py-2">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
      <span class="feature-icon" style="width:40px;height:40px;border-radius:12px"><i class="bi bi-water"></i></span>
      <span>Vessel <span class="text-vh">Host</span></span>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav mx-auto gap-lg-2">
        <li><a class="nav-link" href="#features">Features</a></li>
        <li><a class="nav-link" href="#domains">Domains</a></li>
        <li><a class="nav-link" href="#hosting">Hosting</a></li>
        <li><a class="nav-link" href="#deploy">Deploy</a></li>
      </ul>
      <div class="d-flex gap-2">
        @auth <a class="btn btn-outline-dark rounded-3" href="{{ route('dashboard.home') }}">Dashboard</a>
        @else <a class="btn btn-outline-dark rounded-3" href="{{ route('login') }}">Login</a>
        <a class="btn btn-vh" href="{{ route('register') }}">Get Started</a> @endauth
      </div>
    </div>
  </div>
</nav>
@yield('content')
<footer class="footer-vh py-5">
  <div class="container d-flex justify-content-between flex-wrap gap-3">
    <div><strong>Vessel Host</strong><div class="small opacity-75 mt-1">Fast, secure and reliable hosting.</div></div>
    <div class="small opacity-75">© {{ date('Y') }} Vessel Host. All rights reserved.</div>
  </div>
</footer>
</body>
</html>