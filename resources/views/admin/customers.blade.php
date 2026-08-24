@extends('layouts.dashboard')
@section('content')
<h2 class="fw-bold mb-4">Customers</h2><div class="table-card table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="px-4">Customer</th><th>Email</th><th>Joined</th></tr></thead><tbody>@foreach($customers as $customer)<tr><td class="px-4 fw-semibold">{{ $customer->name }}</td><td>{{ $customer->email }}</td><td>{{ $customer->created_at->format('M d, Y') }}</td></tr>@endforeach</tbody></table></div>
@endsection