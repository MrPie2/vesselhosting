<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

@extends('layouts.dashboard')
@section('content')
<div class="mb-4"><h2 class="fw-bold mb-1">Billing</h2><p class="text-secondary mb-0">Orders, invoices and payment status.</p></div>
<div class="table-card table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="px-4">Order</th><th>Status</th><th>Total</th><th>Date</th><th></th></tr></thead><tbody>@forelse($orders as $order)<tr><td class="px-4 fw-semibold">{{ $order->number }}</td><td><span class="badge badge-soft">{{ ucfirst($order->status) }}</span></td><td>{{ $order->currency }} {{ number_format($order->total,2) }}</td><td>{{ $order->created_at->format('M d, Y') }}</td><td><a class="btn btn-sm btn-outline-dark rounded-3" href="{{ route('dashboard.orders.show',$order) }}">View</a></td></tr>@empty<tr><td colspan="5" class="p-5 text-center text-secondary">No orders yet.</td></tr>@endforelse</tbody></table></div>
@endsection