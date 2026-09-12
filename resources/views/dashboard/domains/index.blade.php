
@extends('layouts.dashboard')
@section('content')
<div class="container py-4">

    <div class=" mb-4">
        <h2 class="fw-bold mb-1">Domain</h2>
        <p class="text-muted mb-0">
            Register, new domain, transfer an existing domain, or use a domain you already own.
        </p>
    </div>

    <div class="row g-3 justify-content-center">

        {{-- Register New Domain --}}
        <div class="col-md-4">
            <label class="domain-option w-100">
                <input type="radio" name="domain_option" value="register" class="d-none">

                <div class="card border-0 shadow-sm h-100 domain-card">
                    <div class="card-body p-4">

                        <div class="domain-icon mb-3">
                            <i class="bi bi-globe2"></i>
                        </div>

                        <h5 class="fw-bold">Register a new domain</h5>

                        <p class="text-muted small mb-0">
                            Search for an available domain and register it with us.
                        </p>

                    </div>
                </div>
            </label>
        </div>


        {{-- Transfer Domain --}}
        <div class="col-md-4">
            <label class="domain-option w-100">
                <input type="radio" name="domain_option" value="transfer" class="d-none">

                <div class="card border-0 shadow-sm h-100 domain-card">
                    <div class="card-body p-4">

                        <div class="domain-icon mb-3">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>

                        <h5 class="fw-bold">Transfer a domain</h5>

                        <p class="text-muted small mb-0">
                            Move your domain from another registrar to us.
                        </p>

                    </div>
                </div>
            </label>
        </div>


        {{-- Existing Domain --}}
        <div class="col-md-4">
            <label class="domain-option w-100">
                <input type="radio" name="domain_option" value="existing" class="d-none">

                <div class="card border-0 shadow-sm h-100 domain-card">
                    <div class="card-body p-4">

                        <div class="domain-icon mb-3">
                            <i class="bi bi-link-45deg"></i>
                        </div>

                        <h5 class="fw-bold">Use an existing domain</h5>

                        <p class="text-muted small mb-0">
                            Use a domain you already own and update its nameservers.
                        </p>

                    </div>
                </div>
            </label>
        </div>

    </div>


    {{-- Dynamic Form Area --}}
    <div id="domain-action" class="mt-4"></div>

</div>


<style>
    .domain-option {
        cursor: pointer;
    }

    .domain-card {
        border: 2px solid transparent !important;
        transition: .2s ease;
    }

    .domain-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,.08) !important;
    }

    .domain-option input:checked + .domain-card {
        border-color: #212529 !important;
        box-shadow: 0 10px 30px rgba(0,0,0,.08) !important;
    }

    .domain-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
</style>


<script>
    $('input[name="domain_option"]').on('change', function () {

        let option = $(this).val();

        let html = '';

        if (option === 'register') {

            html = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-1">Register a new domain</h5>
                        <p class="text-muted small">
                            Search for the domain you want to register.
                        </p>

                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   name="domain"
                                   placeholder="example.com">

                            <button type="button"
                                    class="btn btn-dark">
                                Search
                            </button>
                        </div>

                    </div>
                </div>
            `;

        } else if (option === 'transfer') {

            html = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-1">Transfer your domain</h5>
                        <p class="text-muted small">
                            Enter the domain you want to transfer.
                        </p>

                        <div class="mb-3">
                            <input type="text"
                                   class="form-control"
                                   name="transfer_domain"
                                   placeholder="example.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Transfer / EPP Code
                            </label>

                            <input type="text"
                                   class="form-control"
                                   name="epp_code"
                                   placeholder="Enter authorization code">
                        </div>

                        <button type="button"
                                class="btn btn-dark">
                            Continue Transfer
                        </button>

                    </div>
                </div>
            `;

        } else if (option === 'existing') {

            html = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-1">Use your existing domain</h5>
                        <p class="text-muted small">
                            Enter your domain and point it to our nameservers.
                        </p>

                        <div class="mb-3">
                            <label class="form-label">
                                Domain name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   name="existing_domain"
                                   placeholder="example.com">
                        </div>

                        <div class="p-3 rounded bg-light mb-3">

                            <div class="small fw-semibold mb-2">
                                Update your nameservers to:
                            </div>

                            <div class="small">
                                <code>ns1.vesselhost.net</code>
                            </div>

                            <div class="small">
                                <code>ns2.vesselhost.net</code>
                            </div>

                        </div>

                        <button type="button"
                                class="btn btn-dark">
                            Use This Domain
                        </button>

                    </div>
                </div>
            `;
        }

        $('#domain-action').html(html);
    });
</script>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
    <h2 class="fw-bold mb-1">My Domains</h2>
    <p class="text-secondary mb-0">Manage your domains.</p></div>
</div>

<div class="table-card"><div class="table-responsive">
    <table class="table align-middle mb-0"><thead>
        <tr><th class="px-4">Domain</th>
        <th>Status</th>
        <th>Expiry</th>
        <th>DNS</th>
        <th></th></tr>
    </thead><tbody>
@forelse($domains as $domain)<tr>
    <td class="px-4"><strong>{{ $domain->domain }}</strong>
    <div class="small text-secondary">{{ $domain->registrar ?: 'External / managed DNS' }}</div>
</td
><td><span class="badge badge-soft">{{ ucfirst($domain->status) }}</span></td>
<td>{{ $domain->expiry_date?->format('M d, Y') ?: '—' }}</td>
<td>{{ $domain->dns_records_count }} records</td>
<td><a class="btn btn-sm btn-outline-dark rounded-3" href="{{ route('dashboard.domains.show',$domain) }}">Manage</a></td></tr>
@empty<tr><td colspan="5" class="p-5 text-center text-secondary">No domains found.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $domains->links() }}</div></div>
@endsection