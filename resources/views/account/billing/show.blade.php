@extends('layouts.account-shell')

@section('title', $document->document_number)

@push('scripts')
<style>
    @media print {
        .shell-header, .shell-footer, .invoice-actions, .no-print { display: none !important; }
        .invoice-document { border: none; background: white; color: black; }
    }
</style>
@endpush

@section('content')

{{-- Action bar --}}
<nav class="invoice-actions no-print"
     style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap;margin-bottom:1.5rem">
    <a href="{{ route('account.billing.index') }}"
       style="padding:.55rem .85rem;border-radius:6px;border:1px solid var(--shell-line);text-decoration:none;color:var(--shell-text);font-size:.85rem">
        ← Billing center
    </a>
    @if($document->order)
        <a href="{{ route('account.orders.show', $document->order) }}"
           style="padding:.55rem .85rem;border-radius:6px;border:1px solid var(--shell-line);text-decoration:none;color:var(--shell-text);font-size:.85rem">
            Order details
        </a>
        <a href="{{ route('account.support.create', ['order_id' => $document->order_id]) }}"
           style="padding:.55rem .85rem;border-radius:6px;border:1px solid var(--shell-line);text-decoration:none;color:var(--shell-text);font-size:.85rem">
            Contact support about this invoice
        </a>
    @endif
    <button onclick="window.print()"
            style="margin-left:auto;padding:.55rem .85rem;border-radius:6px;border:1px solid var(--shell-line);background:transparent;color:var(--shell-text);font-size:.85rem;cursor:pointer">
        Print / Save PDF
    </button>
</nav>

{{-- Invoice document --}}
<article class="shell-card invoice-document" style="max-width:52rem;margin:0 auto 2rem">

    <header style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
        <div>
            <span class="shell-eyebrow">BiKuBe invoice</span>
            <h1 style="margin:.35rem 0 .25rem;font-size:1.8rem;font-weight:950">{{ $document->document_number }}</h1>
        </div>
        <span style="display:inline-flex;border:1px solid rgba(37,220,145,.28);border-radius:999px;background:rgba(37,220,145,.08);padding:.28rem .85rem;color:var(--bkb-accent,#25dc91);font-size:.78rem;font-weight:900;text-transform:uppercase">
            {{ str($document->status)->title() }}
        </span>
    </header>

    <div class="shell-grid" style="grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">

        <section>
            <p style="margin:0 0 .6rem;font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--shell-muted)">Document</p>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Issued</span>
                <strong style="font-size:.85rem">{{ $document->issued_at?->format('d M Y') ?? 'Not issued' }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0">
                <span style="color:var(--shell-muted);font-size:.85rem">Due</span>
                <strong style="font-size:.85rem">{{ $document->due_at?->format('d M Y') ?? 'Not specified' }}</strong>
            </div>
        </section>

        <section>
            <p style="margin:0 0 .6rem;font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--shell-muted)">Customer</p>
            <p style="margin:0;font-size:.9rem">{{ $document->customer?->name ?? auth()->user()->name }}</p>
            <p style="margin:.25rem 0 0;color:var(--shell-muted);font-size:.85rem">{{ $document->customer?->email ?? auth()->user()->email }}</p>
        </section>

    </div>

    {{-- Order ref --}}
    <section style="margin-bottom:1.5rem;padding:1rem;border-radius:8px;border:1px solid var(--shell-line);background:rgba(148,163,184,.04)">
        <p style="margin:0 0 .5rem;font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--shell-muted)">Order reference</p>
        @if($document->order)
            <p style="margin:0;font-size:.9rem">
                <strong>{{ $document->order->order_number }}</strong>
                · {{ $document->order->scenario?->title ?? $document->order->service_scenario_key }}
            </p>
        @else
            <p style="margin:0;color:var(--shell-muted);font-size:.85rem">No order linked to this document.</p>
        @endif
    </section>

    {{-- Amounts --}}
    <section style="margin-bottom:1.5rem">
        <p style="margin:0 0 .6rem;font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--shell-muted)">Amount</p>
        <div style="display:flex;justify-content:space-between;padding:.55rem 0;border-bottom:1px solid var(--shell-line)">
            <span style="color:var(--shell-muted)">Subtotal</span>
            <strong>{{ number_format((float) $document->subtotal_amount, 2) }} {{ $document->currency }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:.55rem 0;border-bottom:1px solid var(--shell-line)">
            <span style="color:var(--shell-muted)">Tax</span>
            <strong>{{ number_format((float) $document->tax_amount, 2) }} {{ $document->currency }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:.75rem 0;font-size:1.15rem">
            <span style="font-weight:950">Total</span>
            <strong>{{ number_format((float) $document->total_amount, 2) }} {{ $document->currency }}</strong>
        </div>
    </section>

    {{-- Payment status --}}
    <section style="padding:.85rem;border-radius:8px;border:1px solid var(--shell-line);background:rgba(148,163,184,.04)">
        <p style="margin:0 0 .35rem;font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--shell-muted)">Payment</p>
        <p style="margin:0;font-size:.85rem;color:var(--shell-muted)">
            Online payment via Vipps MobilePay is not connected yet.
            You will be notified when payment becomes available.
        </p>
    </section>

</article>
@endsection
