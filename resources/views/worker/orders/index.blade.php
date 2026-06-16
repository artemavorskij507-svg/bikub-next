@extends('worker.layout')

@section('title', 'Assignments')

@section('content')
<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Worker queue</p>
        <h1>Assigned orders</h1>
        <p class="muted">Real orders assigned by Dispatch Center. No demo jobs are shown.</p>
    </div>
    <a class="worker-btn" href="{{ route('worker.dashboard') }}">Dashboard</a>
</div>

<section class="worker-order-list">
    @forelse($orders as $order)
        <a class="worker-order-row" href="{{ route('worker.orders.show', $order) }}">
            <span class="worker-order-icon">{{ str($order->order_number)->substr(-2) }}</span>
            <span>
                <strong>{{ $order->order_number }}</strong>
                <span class="muted" style="display:block">{{ $order->scenario?->title ?? $order->service_scenario_key }}</span>
                <small class="muted">{{ $order->updated_at?->diffForHumans() }}</small>
            </span>
            <span class="worker-status-pill">{{ str($order->status->value)->replace('_', ' ')->title() }}</span>
        </a>
    @empty
        <article class="worker-card worker-empty">
            <div>
                <i>⌁</i>
                <h3>No assigned orders</h3>
                <p class="muted">Dispatch has not assigned an active order to this account.</p>
            </div>
        </article>
    @endforelse
</section>
@endsection
