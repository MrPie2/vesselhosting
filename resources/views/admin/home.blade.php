@extends('layouts.dashboard')
@section('content')
<div class="mb-4"><h2 class="fw-bold">Admin overview</h2><p class="text-secondary">Vessel Host operations center.</p></div>
<div class="row g-3 mb-4">
@foreach([['Customers',$customers,'bi-people'],['Domains',$domains,'bi-globe2'],['Hosting',$hosting,'bi-server'],['Recent orders',$orders->count(),'bi-receipt']] as $stat)
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon"><i class="bi {{ $stat[2] }}"></i></div><div class="small text-secondary mt-3">{{ $stat[0] }}</div><div class="h3 fw-bold">{{ $stat[1] }}</div></div></div>
@endforeach
</div>
<div class="table-card p-4"><div class="d-flex justify-content-between mb-3"><h5 class="fw-bold">Recent orders</h5><a class="text-vh" href="{{ route('admin.customers') }}">Customers</a></div>
<div class="table-responsive"><table class="table"><thead><tr><th>Order</th><th>Status</th><th>Total</th><th>Date</th></tr></thead><tbody>@foreach($orders as $order)<tr><td>{{ $order->number }}</td><td>{{ $order->status }}</td><td>{{ $order->currency }} {{ number_format($order->total,2) }}</td><td>{{ $order->created_at->format('M d') }}</td></tr>@endforeach</tbody></table></div></div>
@endsection