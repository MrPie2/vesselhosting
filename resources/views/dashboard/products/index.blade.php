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
<form id="domain-search-form" method="POST" action="/domain-av">
     @csrf
<div class="input-group mb-3">
    <input type="text" class="form-control input-lg domain-text" placeholder="Search"/><a type="submit" href="/domain-av"class=" btn btn-lg btn-primary">Search <i class="bi bi-search"></i></a>
</div>
</form>
<div class="container-fluid py-4">

    {{-- Header --}}
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

                    {{-- Card Header --}}
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


                    {{-- Price --}}
                    <div class="p-4">

                        <div class="mb-4">

                            <span class="display-6 fw-bold">
                                ${{ $plan->amount }}
                            </span>

                            <span class="text-muted">
                                / {{ $plan->billing_cycle ?? 'month' }}
                            </span>

                        </div>


                        {{-- Features --}}
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


                        {{-- Action --}}
                        <a href="/dashboard/products/single-product/{{ $plan->id }}"
                           class="btn btn-dark w-100 py-2 rounded-3 fw-semibold">

                            Choose {{ $plan->name }}

                            <i class="bi bi-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>

            </div>

        @empty

            {{-- Empty State --}}
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

<div class="search-results mb-3"></div>
@endsection

