@extends('layouts.dashboard')
@section('content')
<h2 class="fw-bold mb-4">Orders</h2>
<div class="table-card table-responsive"><table class="table align-middle mb-0">
<thead><tr><th class="px-4">Order</th><th>Customer</th><th>Status</th><th>Total</th><th>Date</th></tr></thead>
<tbody>@forelse($orders as $order)
<tr><td class="px-4 fw-semibold">{{ $order->number }}</td><td>{{ $order->user?->email }}</td><td>{{ ucfirst(str_replace('_',' ',$order->status)) }}</td><td>{{ $order->currency }} {{ number_format($order->total,2) }}</td><td>{{ $order->created_at->format('M d, Y') }}</td></tr>
@empty<tr><td colspan="5" class="p-5 text-center text-secondary">No orders yet.</td></tr>@endforelse</tbody></table></div>
@endsection