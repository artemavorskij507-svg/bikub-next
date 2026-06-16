@extends('layouts.account-shell')

@section('title', 'My BiKuBe')

@section('content')
@php
    $activeOrder    = $orders->firstWhere(fn ($o) => !in_array($o->status->value, ['completed', 'cancelled']));
    $proof          = $activeOrder?->completionProofs->first();
    $activeCount    = $orders->filter(fn ($o) => !in_array($o->status->value, ['completed', 'cancelled']))->count();
    $completedCount = $orders->where('status.value', 'completed')->count();
    $openTickets    = $tickets->whereNotIn('status', ['resolved', 'closed'])->count();
    $issuedDocs     = $documents->where('status', 'issued');
@endphp

{{-- Completion proof banner --}}
@if($proof && $proof->status === 'submitted')
    <section class="shell-card" style="margin-bottom:1.5rem;border-color:rgba(37,220,145,.32);background:rgba(10,50,35,.7)">
        <span class="shell-eyebrow">Action required</span>
        <h2 style="margin:.35rem 0 .5rem;font-size:1.1rem">Worker submitted completion proof</h2>
        <p style="margin:0 0 1rem;color:#b0cfc0">Review the worker's completion note and confirm or dispute.</p>
        <a class="shell-primary" href="{{ route('account.orders.show', $activeOrder) }}"
           style="display:inline-flex;text-decoration:none;padding:.65rem 1.1rem;border-radius:6px">
            Review completion →
        </a>
    </section>
@endif

{{-- Hero --}}
<header style="margin-bottom:1.5rem">
    <span class="shell-eyebrow">Customer account</span>
    <h1 style="margin:.35rem 0 .4rem;font-size:clamp(1.8rem,4vw,2.6rem);font-weight:950">My BiKuBe</h1>
    <p style="margin:0;color:var(--shell-muted)">Orders, invoices and support in one place.</p>
</header>

{{-- KPI strip --}}
<section class="shell-grid cards" style="margin-bottom:1.5rem">
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Active orders</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ $activeCount }}</strong>
    </article>
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Completed</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ $orders->filter(fn ($o) => $o->status->value === 'completed')->count() }}</strong>
    </article>
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Open support</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ $openTickets }}</strong>
    </article>
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Invoices issued</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ $issuedDocs->count() }}</strong>
        <p style="margin:.2rem 0 0;color:var(--shell-muted);font-size:.8rem">{{ number_format((float) $issuedDocs->sum('total_amount'), 2) }} NOK</p>
    </article>
</section>

<div class="shell-grid" style="grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">

    {{-- Active order --}}
    <section class="shell-card">
        <span class="shell-eyebrow">Current order</span>
        @if($activeOrder)
            <h2 style="margin:.35rem 0 .25rem">{{ $activeOrder->order_number }}</h2>
            <p style="margin:0 0 .85rem;color:var(--shell-muted)">
                {{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key }}
            </p>

            @php($invoice = $activeOrder->billingDocuments->first())
            <div style="display:grid;gap:.5rem;margin-bottom:1rem">
                <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                    <span style="color:var(--shell-muted);font-size:.85rem">Status</span>
                    <strong style="font-size:.85rem">{{ str($activeOrder->status->value)->replace('_', ' ')->title() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                    <span style="color:var(--shell-muted);font-size:.85rem">Invoice</span>
                    <strong style="font-size:.85rem">
                        @if($invoice)
                            {{ $invoice->document_number }} · {{ number_format((float) $invoice->total_amount, 2) }} {{ $invoice->currency }}
                        @else
                            Not issued yet
                        @endif
                    </strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.5rem 0">
                    <span style="color:var(--shell-muted);font-size:.85rem">Live tracking</span>
                    <strong style="font-size:.85rem">
                        @if($activeOrder->workerLocationPings->isNotEmpty())
                            Real GPS update available
                        @else
                            Available after courier confirms GPS
                        @endif
                    </strong>
                </div>
            </div>

            <a class="shell-primary" href="{{ route('account.orders.show', $activeOrder) }}"
               style="display:inline-flex;text-decoration:none;padding:.65rem 1.1rem;border-radius:6px">
                Open order →
            </a>
        @else
            <h2 style="margin:.35rem 0 .5rem;color:var(--shell-muted)">No active orders</h2>
            <p style="margin:0;color:var(--shell-muted);font-size:.85rem">Your orders will appear here once confirmed by operations.</p>
        @endif
    </section>

    {{-- Payment status --}}
    <section class="shell-card">
        <span class="shell-eyebrow">Payment</span>
        <h2 style="margin:.35rem 0 .5rem">Payment readiness</h2>
        <p style="margin:0 0 1rem;color:var(--shell-muted);font-size:.85rem">
            Online payment via Vipps MobilePay is being prepared and is not available yet.
            You will be notified when payment becomes available.
        </p>
        <div style="padding:.75rem;border-radius:6px;border:1px solid var(--shell-line);background:rgba(148,163,184,.05)">
            <p style="margin:0;font-size:.8rem;color:var(--shell-muted)">
                Invoices are issued and tracked. Payment will be collected once the provider integration is complete.
            </p>
        </div>
    </section>

</div>

<div class="shell-grid" style="grid-template-columns:1fr 1fr 1fr;gap:1rem">

    {{-- Recent orders --}}
    <section class="shell-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.85rem">
            <span class="shell-eyebrow">Recent orders</span>
            <a href="{{ route('account.orders.index') }}" style="font-size:.8rem;color:var(--bkb-accent,#25dc91);text-decoration:none">View all →</a>
        </div>
        @forelse($orders->take(5) as $o)
            <a href="{{ route('account.orders.show', $o) }}"
               style="display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid var(--shell-line);text-decoration:none;color:inherit">
                <span style="font-size:.85rem">{{ $o->order_number }}</span>
                <span style="font-size:.75rem;color:var(--shell-muted)">{{ str($o->status->value)->replace('_', ' ')->title() }}</span>
            </a>
        @empty
            <p style="color:var(--shell-muted);font-size:.85rem;margin:0">No orders yet.</p>
        @endforelse
    </section>

    {{-- Recent billing --}}
    <section class="shell-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.85rem">
            <span class="shell-eyebrow">Billing</span>
            <a href="{{ route('account.billing.index') }}" style="font-size:.8rem;color:var(--bkb-accent,#25dc91);text-decoration:none">View all →</a>
        </div>
        @forelse($documents->take(5) as $d)
            <a href="{{ route('account.billing.documents.show', $d) }}"
               style="display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid var(--shell-line);text-decoration:none;color:inherit">
                <span style="font-size:.85rem">{{ $d->document_number }}</span>
                <span style="font-size:.75rem;color:var(--shell-muted)">{{ number_format((float) $d->total_amount, 2) }} {{ $d->currency }}</span>
            </a>
        @empty
            <p style="color:var(--shell-muted);font-size:.85rem;margin:0">No invoices yet.</p>
        @endforelse
    </section>

    {{-- Recent support --}}
    <section class="shell-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.85rem">
            <span class="shell-eyebrow">Support</span>
            <a href="{{ route('account.support.create') }}" style="font-size:.8rem;color:var(--bkb-accent,#25dc91);text-decoration:none">New ticket →</a>
        </div>
        @forelse($tickets->take(5) as $t)
            <a href="{{ route('account.support.show', $t) }}"
               style="display:block;padding:.55rem 0;border-bottom:1px solid var(--shell-line);text-decoration:none;color:inherit">
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:.85rem">{{ $t->ticket_number }}</span>
                    <span style="font-size:.75rem;color:var(--shell-muted)">{{ str($t->status)->replace('_', ' ')->title() }}</span>
                </div>
                <span style="font-size:.78rem;color:var(--shell-muted)">{{ str($t->subject)->limit(48) }}</span>
            </a>
        @empty
            <p style="color:var(--shell-muted);font-size:.85rem;margin:0">No support tickets.</p>
        @endforelse
    </section>

</div>
@endsection
