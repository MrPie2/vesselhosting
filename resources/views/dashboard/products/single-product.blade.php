@extends('layouts.dashboard')
@section('content')
<div class="container py-4">
<div class="row justify-content-center mt-4">
<div class="col-lg-7">

<div class="card border-0 shadow-sm mb-4">
<div class="card-body p-4">
<span class="badge bg-light text-dark mb-3">Hosting Plan</span>
<h3 class="fw-bold mb-2 plan" cpanel_planid="{{ $plan->cpanel_planid }}" data-plan-id="{{ $plan->id }}">{{ $plan->name }}</h3>
<p class="text-muted mb-0">{{ $plan->description }}</p>
</div>
</div>

<div class="card border-0 shadow-sm">
<div class="card-body p-4">
<h5 class="fw-bold mb-3">Choose billing cycle</h5>
<div class="list-group">
@foreach([1 => 'Monthly', 6 => 'Semi-Annually', 12 => 'Annually', 24 => 'Biennially'] as $months => $label)
<label class="list-group-item d-flex justify-content-between align-items-center">
<div>
<input class="billing_cycle" type="radio" name="billing_cycle" value="{{ $months }}" amount="{{ $plan->amount * $months }}">
<span class="ms-2">{{ $label }}</span>
</div>
<strong>${{ number_format($plan->amount * $months, 2) }}</strong>
</label>
@endforeach
</div>
</div>
</div>

<div class="mt-4">
<h5 class="fw-bold mb-1">Choose your domain</h5>
<p class="text-muted small mb-3">Choose how you want to connect a domain to this hosting account.</p>
<div class="list-group">
<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 domain-option" style="cursor:pointer;">
<input class="form-check-input flex-shrink-0" type="radio" name="domain_option" value="register">
<div class="flex-grow-1"><div class="fw-semibold">Register a new domain</div><div class="small text-muted">Search for an available domain and register it with us.</div></div>
<i class="bi bi-chevron-right text-muted"></i>
</label>
<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 domain-option" style="cursor:pointer;">
<input class="form-check-input flex-shrink-0" type="radio" name="domain_option" value="transfer">
<div class="flex-grow-1"><div class="fw-semibold">Transfer a domain</div><div class="small text-muted">Transfer your domain from another registrar.</div></div>
<i class="bi bi-chevron-right text-muted"></i>
</label>
<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 domain-option" style="cursor:pointer;">
<input class="form-check-input flex-shrink-0" type="radio" name="domain_option" value="existing">
<div class="flex-grow-1"><div class="fw-semibold">Use an existing domain</div><div class="small text-muted">Use a domain you already own and point it to this hosting account.</div></div>
<i class="bi bi-chevron-right text-muted"></i>
</label>
</div>

<div id="domain-config" class="mt-3 d-none">
<label class="small text-muted" for="domain-input">Domain name</label>
<div class="input-group">
<input type="text" id="domain-input" placeholder="yourdomain.com" class="form-control domain-text" autocomplete="off">
<button type="button" id="domain-search-btn" class="btn btn-dark">Search</button>
<span class="input-group-text d-none" id="domain-spinner"><span class="spinner-border spinner-border-sm"></span></span>
</div>
<div id="domain-status" class="small mt-2"></div>
<div id="domain-suggestions" class="mt-3 d-none">
<div class="small fw-semibold mb-2">Related domain suggestions</div>
<div id="suggestion-list" class="list-group"></div>
</div>
</div>
</div>

<div class="mt-4 mb-3 cart_amount text-center"></div>
<button type="button" class="btn btn-dark w-100 add_to_cart" disabled>Add to Cart</button>
<div class="small text-muted text-center mt-2">Your hosting account is not created at this step.</div>
</div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const config = document.getElementById('domain-config');
    const input = document.getElementById('domain-input');

    document.querySelectorAll('input[name="domain_option"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (config) config.classList.remove('d-none');
            if (input) input.focus();
        });
    });
});
<script>
document.addEventListener('DOMContentLoaded', function () {
    const config = document.getElementById('domain-config');
    const input = document.getElementById('domain-input');
    const addButton = document.querySelector('.add_to_cart');
    const status = document.getElementById('domain-status');
    const searchButton = document.getElementById('domain-search-btn');
    let available = false;

    document.querySelectorAll('input[name="domain_option"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (config) config.classList.remove('d-none');
            available = false;
            if (status) status.textContent = '';
            if (addButton) addButton.disabled = !document.querySelector('.billing_cycle:checked');
            if (input) input.focus();
        });
    });

    document.querySelectorAll('.billing_cycle').forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateButton();
        });
    });

    function updateButton() {
        const option = document.querySelector('input[name="domain_option"]:checked')?.value;
        const domain = (input?.value || '').trim();
        const billing = document.querySelector('.billing_cycle:checked');

        let enabled = !!billing && !!option && !!domain;

        if (option === 'register') {
            enabled = enabled && available;
        }

        if (addButton) addButton.disabled = !enabled;
    }

    async function checkAvailability() {
        const domain = (input?.value || '').trim().toLowerCase();
        if (!domain) {
            if (status) status.innerHTML = '<span class="text-danger">Enter a domain name.</span>';
            available = false;
            updateButton();
            return;
        }

        available = false;
        updateButton();
        if (searchButton) searchButton.disabled = true;
        if (status) status.innerHTML = '<span class="text-muted">Checking availability...</span>';

        try {
            const response = await fetch('{{ route('domain.av') }}?domain=' + encodeURIComponent(domain), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.success && data.available) {
                available = true;
                if (status) status.innerHTML = '<span class="text-success">' + (data.domain || domain) + ' is available.</span>';
            } else {
                available = false;
                let message = data.message || 'Domain is not available.';
                if (data.provider_http_status) {
                    message += '<br><strong>HTTP Status:</strong> ' + data.provider_http_status;
                }
                if (data.provider_response) {
                    let provider = typeof data.provider_response === 'object'
                        ? JSON.stringify(data.provider_response, null, 2)
                        : String(data.provider_response);
                    message += '<div class="mt-2"><strong>ResellerClub response:</strong><pre class="small bg-light border rounded p-2 mt-1 mb-0" style="white-space:pre-wrap;word-break:break-word;">' +
                        provider.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                        '</pre></div>';
                }
                if (status) status.innerHTML = '<span class="text-danger">' + message + '</span>';
            }
        } catch (error) {
            available = false;
            if (status) status.innerHTML = '<span class="text-danger">Unable to check domain availability.</span>';
        } finally {
            if (searchButton) searchButton.disabled = false;
            updateButton();
        }
    }

    if (searchButton) searchButton.addEventListener('click', checkAvailability);

    if (input) {
        input.addEventListener('input', function () {
            available = false;
            updateButton();
        });
    }

    if (addButton) {
        addButton.addEventListener('click', async function () {
            const option = document.querySelector('input[name="domain_option"]:checked')?.value;
            const domain = (input?.value || '').trim();
            const billing = document.querySelector('.billing_cycle:checked')?.value;
            const planId = document.querySelector('.plan')?.dataset.planId;

            if (!option || !domain || !billing || !planId) {
                if (status) status.innerHTML = '<span class="text-danger">Please select a billing cycle and domain option.</span>';
                return;
            }

            addButton.disabled = true;
            addButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';

            try {
                const response = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        billing_cycle: billing,
                        domain: domain,
                        domain_option: option
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Unable to add this item to your cart.');
                }

                window.location.href = data.redirect || '{{ route('cart.index') }}';
            } catch (error) {
                if (status) status.innerHTML = '<span class="text-danger">' + error.message + '</span>';
                addButton.disabled = false;
                addButton.textContent = 'Add to Cart';
            }
        });
    }
});
</script>
@endsection
