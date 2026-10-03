@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <span class="badge bg-light text-dark mb-2">Shopping Cart</span>
        <h3 class="fw-bold mb-1">Your hosting cart</h3>
        <p class="text-muted mb-0">Review your selections before checkout.</p>
    </div>

    @if(empty($cart))
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart3 fs-1 text-muted"></i>
                <h5 class="fw-bold mt-3">Your cart is empty</h5>
                <a href="{{ route('dashboard.products') }}" class="btn btn-dark mt-2">Browse Hosting Plans</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach($cart as $item)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $item['plan_name'] }}</h5>
                                    <div class="text-muted small">
                                        {{ ucfirst($item['domain_option']) }} domain · {{ $item['domain'] }}
                                    </div>
                                    <div class="text-muted small mt-1">
                                        {{ $item['billing_cycle'] }} month billing period
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>{{ config('services.paystack.currency', 'USD') }} {{ number_format($item['total'], 2) }}</strong>
                                    <form method="POST" action="{{ route('cart.remove', $item['key']) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Order summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <strong>{{ config('services.paystack.currency', 'USD') }} {{ number_format($total, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-3">
                            <strong>Total</strong>
                            <strong>{{ config('services.paystack.currency', 'USD') }} {{ number_format($total, 2) }}</strong>
                        </div>
                        <a href="{{ route('checkout.show') }}" class="btn btn-dark w-100 mt-4">
                            Proceed to Checkout
                        </a>
                        <div class="small text-muted mt-3">
                            Payment checkout will create the order. Hosting provisioning will happen only after successful payment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
