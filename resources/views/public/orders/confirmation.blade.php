@extends('public.layouts.app')
@section('content')
<article><header class="content-hero"><p class="eyebrow">Request received</p><h1>{{ $order->order_number }}</h1><p class="subtitle">Order request received. Payment and dispatch are not connected yet.</p></header><div class="content-body">Status: {{ $order->status->value }}<br>Service: {{ $order->service_scenario_key }}</div></article>
@endsection
