@extends('layouts.account-shell')
@section('title','My orders')
@section('content')
<header><span class="shell-eyebrow">Customer account</span><h1>My orders</h1><p>Orders linked to your verified account ownership.</p></header>
<section class="shell-grid cards">
@forelse($orders as $order)
<article class="shell-card"><strong>{{ $order->order_number }}</strong><p>{{ $order->scenario?->title ?? $order->service_scenario_key }}</p><p>{{ str($order->status->value)->replace('_',' ')->title() }}</p><a href="{{ route('account.orders.show',$order) }}">Open order</a></article>
@empty <div class="shell-card">No orders are linked to this account.</div> @endforelse
</section>
@endsection
