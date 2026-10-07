@php
    $showLandingLinks = request()->routeIs('home');
@endphp

<footer class="footer-vh">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4">
                <a href="{{ route('home') }}" class="footer-brand d-inline-flex align-items-center gap-2">
                    <span class="footer-brand-icon"><i class="bi bi-water"></i></span>
                    <span>Vessel <span>Host</span></span>
                </a>
                <p class="footer-copy mt-3 mb-4">
                    Fast, secure and reliable hosting infrastructure built for developers,
                    businesses and the ideas behind them.
                </p>
                @if($showLandingLinks)
                    <div class="footer-socials d-flex gap-2">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
                    </div>
                @endif
            </div>

            @if($showLandingLinks)
                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="footer-heading">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('home') }}#features">Features</a></li>
                        <li><a href="{{ route('home') }}#domains">Domains</a></li>
                        <li><a href="{{ route('home') }}#hosting">Hosting</a></li>
                        <li><a href="{{ route('home') }}#deploy">Deploy</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="footer-heading">Get Started</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('register') }}">Create Account</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('dashboard.products') }}">Hosting Plans</a></li>
                        <li><a href="{{ route('home') }}#domains">Find a Domain</a></li>
                    </ul>
                </div>
            @endif

            <div class="{{ $showLandingLinks ? 'col-lg-4' : 'col-lg-6 ms-lg-auto' }}">
                <div class="footer-newsletter">
                    <span class="footer-eyebrow">Stay in the loop</span>
                    <h5>Get Vessel Host updates in your inbox.</h5>
                    <p>New features, hosting updates and useful developer resources. No spam.</p>

                    @if(session('newsletter_success'))
                        <div class="alert footer-alert mb-3" role="status">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('newsletter_success') }}
                        </div>
                    @endif

                    @error('email')
                        <div class="alert footer-alert footer-alert-error mb-3" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('newsletter.subscribe') }}" class="footer-newsletter-form">
                        @csrf
                        <label class="visually-hidden" for="footer-newsletter-email">Email address</label>
                        <input id="footer-newsletter-email" type="email" name="email"
                               value="{{ old('email') }}" placeholder="Enter your email address"
                               autocomplete="email" required>
                        <button type="submit" class="btn btn-vh">
                            Subscribe <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="small footer-muted">
                © {{ date('Y') }} Vessel Host. All rights reserved.
            </div>
            <div class="small footer-muted">
                Built for developers · <a href="{{ route('home') }}">www.vesselhost.net</a>
            </div>
        </div>
    </div>
</footer>
