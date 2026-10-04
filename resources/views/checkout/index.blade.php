@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <span class="badge bg-light text-dark mb-2">Checkout</span>
        <h3 class="fw-bold mb-1">Review your order</h3>
        <p class="text-secondary mb-0">Confirm your domain and hosting selections before payment.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach($items as $item)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="small text-uppercase text-secondary fw-semibold">{{ $item['type'] === 'domain' ? 'Domain' : 'Hosting' }}</div>
                                    <h5 class="fw-bold mb-1">{{ $item['type'] === 'domain' ? $item['domain'] : $item['plan']->name }}</h5>
                                    @if($item['type'] !== 'domain')
                                    <div class="small text-secondary">{{ $item['billing_cycle'] }} month billing period</div>
                                    @endif
                                </div>
                                <strong>{{ $currency }} {{ number_format($item['total'], 2) }}</strong>
                            </div>

                            <hr>

                            <div class="row g-3 small">
                                <div class="col-md-6">
                                    <span class="text-secondary d-block">Domain</span>
                                    <strong>{{ $item['domain'] }}</strong>
                                    @if(($item['domain_price'] ?? 0) > 0)
                                        <div class="text-secondary mt-1">{{ $currency }} {{ number_format($item['domain_price'], 2) }} domain fee / year</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <span class="text-secondary d-block">Product</span>
                                    <strong>{{ $item['type'] === 'domain' ? 'Domain registration' : ucfirst($item['domain_option']) . ' + hosting' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="alert alert-light border rounded-3 small">
                    Your payment is processed by Paystack. The hosting account is not created until the payment is successfully verified on the server.
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-lg-top" style="top: 1rem;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Order summary</h5>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Subtotal</span>
                            <span>{{ $currency }} {{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <strong>Total</strong>
                            <strong class="fs-5">{{ $currency }} {{ number_format($subtotal, 2) }}</strong>
                        </div>

                        <button class="btn btn-dark w-100 mt-4 py-3">
                            <i class="bi bi-lock me-2"></i>Continue to Payment
                        </button>

                        <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-2 text-decoration-none">
                            Back to cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
