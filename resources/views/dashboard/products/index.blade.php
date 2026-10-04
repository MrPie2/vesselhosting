@extends('layouts.dashboard')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Buy Domain</h3>
            <p class="text-muted mb-0">
                Choose a preferred domain fits your website.
            </p>
        </div>
    </div>
<form id="buy-domain-search-form">
     @csrf
<div class="input-group mb-3">
    <input type="text" name="domain" class="form-control input-lg domain-text" placeholder="yourdomain.com" autocomplete="off" required/><button type="submit" class=" btn btn-lg btn-primary">Search <i class="bi bi-search"></i></button>
</div>
</form>
<div class="container-fluid py-4">

    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Hosting Plans</h3>
            <p class="text-muted mb-0">
                Choose the hosting plan that fits your website.
            </p>
        </div>
    </div>


    <div class="row g-4">
        @forelse($hosting_plan as $plan)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="p-4 border-bottom">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="bg-light rounded-3 p-2">
                                        <i class="bi bi-server fs-5"></i>
                                    </span>

                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                        Hosting
                                    </span>
                                </div>

                                <h4 class="fw-bold mb-1">
                                    {{ $plan->name }}
                                </h4>

                                <p class="text-muted small mb-0">
                                    {{ $plan->description ?? 'Reliable hosting for your website.' }}
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="p-4">

                        <div class="mb-4">

                            <span class="display-6 fw-bold">
                                ${{ $plan->amount }}
                            </span>

                            <span class="text-muted">
                                / {{ $plan->billing_cycle ?? 'month' }}
                            </span>

                        </div>


                        <div class="mb-4">

                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>
                                    {{ $plan->disk_space ?? 'Unlimited' }} Storage
                                </span>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>
                                    {{ $plan->bandwidth ?? 'Unlimited' }} Bandwidth
                                </span>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>
                                    {{ $plan->websites ?? '1' }} Website
                                </span>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>
                                    Free SSL Certificate
                                </span>
                            </div>

                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>
                                    cPanel Control Panel
                                </span>
                            </div>

                        </div>


                        
                        <a href="/dashboard/products/single-product/{{ $plan->id }}"
                           class="btn btn-dark w-100 py-2 rounded-3 fw-semibold">

                            Choose {{ $plan->name }}

                            <i class="bi bi-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>

            </div>

        @empty

            
            <div class="col-12">

                <div class="text-center py-5">

                    <div class="bg-light d-inline-flex rounded-circle p-4 mb-3">
                        <i class="bi bi-server fs-1 text-muted"></i>
                    </div>

                    <h5 class="fw-bold">
                        No hosting plans available
                    </h5>

                    <p class="text-muted mb-3">
                        Hosting plans will appear here once they are added.
                    </p>

                    <a href="#" class="btn btn-dark rounded-3 px-4">
                        <i class="bi bi-plus-lg me-2"></i>
                        Create Plan
                    </a>

                </div>

            </div>

        @endforelse

    </div>

</div>

<div id="domain-buy-result" class="mb-4"></div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('buy-domain-search-form');
    const input = form?.querySelector('.domain-text');
    const result = document.getElementById('domain-buy-result');

    if (!form || !input || !result) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const domain = input.value.trim().toLowerCase();
        if (!domain) return;

        result.innerHTML = '<div class="alert alert-light border"><span class="spinner-border spinner-border-sm me-2"></span>Checking availability...</div>';

        try {
            const response = await fetch('{{ route('domain.av') }}?domain=' + encodeURIComponent(domain), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                result.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Unable to search this domain.') + '</div>';
                return;
            }

            if (!data.available) {
                result.innerHTML = '<div class="alert alert-warning"><strong>' + (data.domain || domain) + '</strong> is not available.</div>';
                return;
            }

            const selected = data.domain || domain;
            const price = data.price_configured ? '{{ config('services.paystack.currency', 'USD') }} ' + Number(data.price || 0).toFixed(2) + ' / year' : 'Price not configured';

            result.innerHTML = '<div class="card border-0 shadow-sm"><div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">' +
                '<div><span class="badge bg-success-subtle text-success mb-2">Available</span><h5 class="fw-bold mb-1">' + selected + '</h5><div class="text-muted small">' + price + '</div></div>' +
                '<button type="button" class="btn btn-dark px-4" id="buy-domain-btn"><i class="bi bi-cart-plus me-2"></i>Add domain to cart</button></div></div>';

            document.getElementById('buy-domain-btn').addEventListener('click', async function () {
                const button = this;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

                const addResponse = await fetch('{{ route('cart.domain') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ domain: selected })
                });
                const addData = await addResponse.json();

                if (!addResponse.ok || !addData.success) {
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Add domain to cart';
                    result.insertAdjacentHTML('beforeend', '<div class="alert alert-danger mt-3">' + (addData.message || 'Unable to add the domain to your cart.') + '</div>');
                    return;
                }

                window.location.href = addData.redirect;
            });
        } catch (error) {
            result.innerHTML = '<div class="alert alert-danger">Unable to check domain availability right now.</div>';
        }
    });
});
</script>