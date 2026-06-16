@extends('layouts.account-shell')

@section('title', 'My orders')

@section('content')

<header style="margin-bottom:1.5rem">
    <span class="shell-eyebrow">Customer account</span>
    <h1 style="margin:.35rem 0 .4rem;font-size:clamp(1.6rem,4vw,2.4rem);font-weight:950">My orders</h1>
    <p style="margin:0;color:var(--shell-muted)">Orders linked to your verified account.</p>
</header>

@forelse($orders as $order)
    <a href="{{ route('account.orders.show', $order) }}"
       style="display:grid;grid-template-columns:1fr auto;gap:.85rem;align-items:center;text-decoration:none;color:inherit;margin-bottom:.65rem">
        <article class="shell-card" style="display:grid;grid-template-columns:1fr auto;gap:.85rem;align-items:center">
            <div>
                <strong>{{ $order->order_number }}</strong>
                <p style="margin:.2rem 0 0;color:var(--shell-muted);font-size:.85rem">
                    {{ $order->scenario?->title ?? $order->service_scenario_key }}
                    · {{ $order->created_at?->format('d M Y') }}
                </p>
            </div>
            <span style="display:inline-flex;border:1px solid rgba(37,220,145,.28);border-radius:999px;background:rgba(37,220,145,.08);padding:.22rem .6rem;color:var(--bkb-accent,#25dc91);font-size:.72rem;font-weight:900;text-transform:uppercase;white-space:nowrap">
                {{ str($order->status->value)->replace('_', ' ')->title() }}
            </span>
        </article>
    </a>
@empty
    <article class="shell-card" style="text-align:center;padding:3rem 1.5rem">
        <p style="margin:0;color:var(--shell-muted)">No orders are linked to this account.</p>
        <a href="/" style="display:inline-flex;margin-top:1rem;padding:.65rem 1.1rem;border-radius:6px;text-decoration:none;border:1px solid var(--shell-line);color:var(--shell-text)">Browse services</a>
    </article>
@endforelse

@endsection
