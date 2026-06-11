@extends('worker.layout') @section('title','Orders') @section('content')
<h1>Assigned orders</h1><section class="grid">@forelse($orders as $order)<a class="card" href="{{ route('worker.orders.show',$order) }}" style="text-decoration:none;color:inherit"><strong>{{ $order->order_number }}</strong><div class="muted">{{ $order->scenario?->title ?? $order->service_scenario_key }} · {{ str($order->status->value)->replace('_',' ')->title() }}</div></a>@empty<div class="card">No assigned orders.</div>@endforelse</section>
@endsection
