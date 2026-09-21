@extends('layouts.dashboard')
@section('content')
<div class="container py-4">

<div class="row justify-content-center mt-4">

    <div class="col-lg-7">

        {{-- Selected Plan --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">

                <span class="badge bg-light text-dark mb-3">
                    Hosting Plan
                </span>

                <h3 class="fw-bold mb-2 plan" cpanel_planid="{{$plan->cpanel_planid}}">
                    {{ $plan->name }}
                </h3>

                <p class="text-muted mb-0">
                    {{ $plan->description }}
                </p>

            </div>
        </div>

        {{-- Billing Cycle --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">
                    Choose billing cycle
                </h5>

                <div class="list-group">

                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input class="billing_cycle" type="radio"
                                   name="billing_cycle"
                                   value="1" amount="{{ $plan->amount * 1  }}">
                            <span class="ms-2">Monthly</span>
                        </div>

                        <strong>
                            ${{ number_format($plan->amount, 2) * 1 }}
                        </strong>
                    </label>

                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input class="billing_cycle" type="radio"
                                   name="billing_cycle"
                                   value="6" amount="{{ $plan->amount * 6 }}">

                            <span class="ms-2">Semi-Annually</span>
                        </div>

                        <strong>
                            ${{ number_format($plan->amount, 2) * 6 }}
                        </strong>
                    </label>

                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input class="billing_cycle" type="radio"
                                   name="billing_cycle"
                                   value="12" amount="{{ $plan->amount * 12  }}">

                            <span class="ms-2">Annually</span>
                        </div>

                        <strong>
                            ${{ number_format($plan->amount, 2) * 12 }}
                        </strong>
                    </label>

                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input class="billing_cycle" type="radio"
                                   name="billing_cycle"
                                   value="24" amount="{{ $plan->amount * 24 }}">

                            <span class="ms-2">Biennially</span>
                        </div>

                        <strong>
                            ${{ number_format($plan->amount, 2) * 24 }}
                        </strong>
                    </label>

                </div>

                            </div>
        </div>

        <div class="mt-4">

<h5 class="fw-bold mb-1">Choose your domain</h5>

<p class="text-muted small mb-3">
    Select how you want to connect a domain to this hosting account.
</p>

<div class="list-group">

    {{-- Register New Domain --}}
    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3"
           style="cursor:pointer;">

        <input class="form-check-input flex-shrink-0"
               type="radio"
               name="domain_option"
               value="register">

        <div class="flex-grow-1">
            <div class="fw-semibold">
                Register a new domain
            </div>

            <div class="small text-muted">
                Search and register a new domain with us.
            </div>
        </div>

        <i class="bi bi-chevron-right text-muted"></i>

    </label>


    {{-- Transfer Domain --}}
    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3"
           style="cursor:pointer;">

        <input class="form-check-input flex-shrink-0"
               type="radio"
               name="domain_option"
               value="transfer">

        <div class="flex-grow-1">
            <div class="fw-semibold">
                Transfer a domain
            </div>

            <div class="small text-muted">
                Transfer your domain from another registrar.
            </div>
        </div>

        <i class="bi bi-chevron-right text-muted"></i>

    </label>


    {{-- Existing Domain --}}
    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3"
           style="cursor:pointer;">

        <input class="form-check-input flex-shrink-0"
               type="radio"
               name="domain_option"
               value="existing">

        <div class="flex-grow-1">
            <div class="fw-semibold">
                Use an existing domain
            </div>

            <div class="small text-muted">
                Use a domain you already own and point it to this hosting account.
            </div>
        </div>

        <i class="bi bi-chevron-right text-muted"></i>

    </label>

</div>

</div>
<br>
<div class="mb-3">
    <label class="small text-muted">Account Domain name</label>
    <input type="text" placeholder="Domain name" class="form-control domain-text">
</div>
<div class="mb-3 cart_amount" style="text-align: center"></div>
<div class="mb-3">
    <button class="btn btn-dark w-100 mt-4 add_to_cart">Continue to Cart Checkout</button>

</div>


    </div>



    
</div>





</div>


@endsection
