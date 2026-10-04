@extends('layouts.dashboard')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Domain pricing</h2>
            <p class="text-muted mb-0">Set your retail price for each domain extension.</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Add extension</h5>
            <form method="POST" action="{{ route('admin.domain-prices.store') }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">TLD</label>
                    <input class="form-control" name="tld" placeholder=".com" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Registration / year</label>
                    <input class="form-control" type="number" step="0.01" min="0" name="registration_price" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Renewal / year</label>
                    <input class="form-control" type="number" step="0.01" min="0" name="renewal_price" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Transfer</label>
                    <input class="form-control" type="number" step="0.01" min="0" name="transfer_price" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-dark">Add extension</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($domainPrices as $price)
            <div class="col-lg-6">
                <form method="POST" action="{{ route('admin.domain-prices.update', $price) }}" class="card border-0 shadow-sm h-100">
                    @csrf
                    @method('PATCH')
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">{{ $price->tld }}</h5>
                            <div class="form-check form-switch">
                                <input type="hidden" name="active" value="0">
                                <input class="form-check-input" type="checkbox" name="active" value="1" {{ $price->active ? 'checked' : '' }}>
                                <label class="form-check-label small">Sell</label>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label small">Register</label>
                                <input class="form-control" type="number" step="0.01" min="0" name="registration_price" value="{{ $price->registration_price }}" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Renew</label>
                                <input class="form-control" type="number" step="0.01" min="0" name="renewal_price" value="{{ $price->renewal_price }}" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Transfer</label>
                                <input class="form-control" type="number" step="0.01" min="0" name="transfer_price" value="{{ $price->transfer_price }}" required>
                            </div>
                        </div>
                        <button class="btn btn-dark w-100 mt-3">Save {{ $price->tld }}</button>
                    </div>
                </form>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No domain extensions have been configured yet. Add .com, .net, .org and the other extensions you want to sell.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
