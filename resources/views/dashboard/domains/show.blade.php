@extends('layouts.dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <a class="small text-vh" href="{{ route('dashboard.domains') }}">← Domains</a>
        <h2 class="fw-bold mt-2">{{ $domain->name }}</h2>
        <div class="text-secondary small">
            {{ ucfirst(str_replace('_', ' ', $domain->status)) }}
            · Expires {{ $domain->expiry_date?->format('M d, Y') ?: 'pending' }}
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#renew">Renew</button>
        <span class="badge badge-soft align-self-center">
            {{ $domain->hosting ? 'Hosting attached' : 'Domain only' }}
        </span>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success rounded-3">{{ session('status') }}</div>
@endif

@if($errors->has('dns'))
    <div class="alert alert-danger rounded-3">{{ $errors->first('dns') }}</div>
@endif

<div class="row g-4">
    <div class="col-12">
        <div class="table-card p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">DNS records</h5>
                    <div class="small text-secondary">
                        Manage DNS records for {{ $domain->name }}.
                    </div>
                </div>
                <span class="badge bg-light text-dark">{{ $domain->dnsRecords->count() }} records</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($domain->dnsRecords as $record)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $record->type }}</span>
                            </td>
                            <td class="text-break">{{ $record->name }}</td>
                            <td class="text-break">{{ $record->content }}</td>
                            <td>{{ $record->priority ?? '—' }}</td>
                            <td>
                                @if($record->provider_status === 'synced')
                                    <span class="badge text-bg-success">Synced</span>
                                @elseif($record->provider_status === 'failed')
                                    <span class="badge text-bg-danger" title="{{ $record->provider_error }}">Failed</span>
                                @else
                                    <span class="badge text-bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($record->provider_status === 'synced')
                                    <form method="POST" action="{{ route('dashboard.dns.destroy', [$domain, $record]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.dns.destroy', [$domain, $record]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                No DNS records yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <h6 class="fw-bold mb-1">Add DNS record</h6>
                <div class="small text-secondary">
                    Enter the record name, value and priority. Priority is used by MX and SRV records.
                    TTL is managed by Vessel Host and defaults to ResellerClub's DNS TTL.
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.dns.store', $domain) }}" class="row g-3">
                @csrf

                <div class="col-12 col-md-3">
                    <label class="form-label">Record type</label>
                    <select name="type" id="dnsType" class="form-select @error('type') is-invalid @enderror" required>
                        @foreach(['A','AAAA','CAA','MX','CNAME','TXT','DMARC','SRV','SVCB','HTTP'] as $type)
                            <option value="{{ $type }}" @selected(old('type', 'A') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Name</label>
                    <input
                        name="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="@, www, _dmarc"
                        required
                    >
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Value</label>
                    <input
                        name="value"
                        value="{{ old('value') }}"
                        class="form-control @error('value') is-invalid @enderror"
                        id="dnsValue"
                        placeholder="Record value"
                        required
                    >
                    @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Priority</label>
                    <input
                        name="priority"
                        value="{{ old('priority') }}"
                        type="number"
                        min="0"
                        max="65535"
                        class="form-control @error('priority') is-invalid @enderror"
                        placeholder="10"
                    >
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div id="dnsHelp" class="small text-secondary"></div>
                </div>

                <div class="col-12">
                    <button class="btn btn-vh px-4">
                        <i class="bi bi-plus-lg me-1"></i> Add DNS record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="table-card p-4">
            <h5 class="fw-bold">Nameservers</h5>
            <p class="small text-secondary">
                DNS records only resolve when the domain is delegated to the DNS nameservers serving this zone.
            </p>
            <div class="p-3 rounded-3 bg-light small mb-2">{{ $domain->nameserver_1 ?: 'Pending' }}</div>
            <div class="p-3 rounded-3 bg-light small">{{ $domain->nameserver_2 ?: 'Pending' }}</div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="table-card p-4">
            <h5 class="fw-bold">Record notes</h5>
            <ul class="small text-secondary mb-0">
                <li class="mb-2">DMARC is provisioned to ResellerClub as a TXT record at <code>_dmarc</code>.</li>
                <li class="mb-2">For SRV, enter the value as <code>target:port:weight</code> and set the priority separately.</li>
                <li>CAA, SVCB and HTTP are shown in the interface, but ResellerClub's documented legacy DNS HTTP API does not expose write endpoints for them, so the application will not falsely mark them as provisioned.</li>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="renew" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('dashboard.domains.renew', $domain) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Renew {{ $domain->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Years</label>
                    <select name="years" class="form-select">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-vh">Create renewal order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const type = document.getElementById('dnsType');
    const value = document.getElementById('dnsValue');
    const help = document.getElementById('dnsHelp');

    function updateDnsHelp() {
        const selected = type.value;

        const messages = {
            A: 'Enter an IPv4 address, for example 203.0.113.10.',
            AAAA: 'Enter an IPv6 address.',
            CAA: 'CAA value normally contains the CA policy, for example 0 issue "letsencrypt.org".',
            MX: 'Enter the mail server hostname and set its priority. Lower priority numbers are preferred.',
            CNAME: 'Enter the destination hostname, for example app.example.com.',
            TXT: 'Enter the TXT value. SPF, DKIM and verification records can be stored here.',
            DMARC: 'Use _dmarc as the name. The value should begin with v=DMARC1;.',
            SRV: 'Enter target:port:weight in Value, for example sip.example.com:5060:10, then set Priority.',
            SVCB: 'SVCB records use service parameters such as priority and target.',
            HTTP: 'HTTP/HTTPS DNS records use service parameters such as priority and target.'
        };

        help.textContent = messages[selected] || '';
    }

    type.addEventListener('change', updateDnsHelp);
    updateDnsHelp();
})();
</script>
@endpush
@endsection
