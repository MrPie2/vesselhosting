@extends('layouts.dashboard')
@section('content')
<a class="small text-vh" href="{{ route('dashboard.orders') }}">← Billing</a>
<div class="row g-4 mt-1">
<div class="col-lg-8"><div class="table-card p-4">
<div class="d-flex justify-content-between align-items-start"><div><div class="small text-secondary">Order</div><h3 class="fw-bold">{{ $order->number }}</h3></div><span class="badge badge-soft">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span></div>
<hr>
@foreach($order->items as $item)<div class="d-flex justify-content-between py-3 border-bottom"><div><div class="fw-semibold">{{ $item->description }}</div><div class="small text-secondary">{{ $item->quantity }} × {{ $order->currency }} {{ number_format($item->unit_price,2) }}</div></div><strong>{{ $order->currency }} {{ number_format($item->total,2) }}</strong></div>@endforeach
<div class="d-flex justify-content-between pt-4"><span class="text-secondary">Total</span><strong class="fs-4">{{ $order->currency }} {{ number_format($order->total,2) }}</strong></div>
</div></div>
<div class="col-lg-4"><div class="table-card p-4">
<h5 class="fw-bold">Payment & provisioning</h5>
<div class="small text-secondary mb-2">Payment: <span class="text-dark">{{ $order->status }}</span></div>
<div class="small text-secondary mb-4">Provisioning: <span class="text-dark">{{ str_replace('_',' ',$order->provisioning_status) }}</span></div>
@if($order->provisioning_error)<div class="alert alert-danger small">{{ $order->provisioning_error }}</div>@endif
@if($order->status === 'pending')
<button id="pay" class="btn btn-vh w-100">Pay {{ $order->currency }} {{ number_format($order->total,2) }}</button>
<div id="paymsg" class="small text-secondary mt-2"></div>
@endif
</div></div></div>
@if($order->status === 'paid' || $order->status === 'completed')
<div class="alert alert-success mt-4">Payment confirmed. Provisioning runs automatically in the background. You can safely leave this page.</div>
@endif
@if($order->status === 'pending')
<script>
document.getElementById('pay')?.addEventListener('click', async () => {
  const btn=document.getElementById('pay'), msg=document.getElementById('paymsg');
  btn.disabled=true; msg.textContent='Connecting to Paystack…';
  const res=await fetch(@json(route('dashboard.payments.initialize',$order)), {
    method:'POST',
    headers:{'X-CSRF-TOKEN':@json(csrf_token()),'Accept':'application/json'}
  });
  const data=await res.json();
  if(data.status && data.data?.authorization_url){ window.location.href=data.data.authorization_url; return; }
  btn.disabled=false; msg.textContent=data.message || 'Unable to initialize payment.';
});
</script>
@endif
@endsection