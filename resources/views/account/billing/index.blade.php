@extends('layouts.account-shell')

@section('title', 'Billing')

@section('content')

<header style="margin-bottom:1.5rem">
    <span class="shell-eyebrow">Customer billing</span>
    <h1 style="margin:.35rem 0 .4rem;font-size:clamp(1.6rem,4vw,2.4rem);font-weight:950">Billing center</h1>
    <p style="margin:0;color:var(--shell-muted)">Invoices linked to your verified account.</p>
</header>

{{-- KPI strip --}}
<section class="shell-grid cards" style="margin-bottom:1.5rem">
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Issued invoices</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ $documents->where('status', 'issued')->count() }}</strong>
    </article>
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Issued total</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ number_format((float) $issuedAmount, 2) }}</strong>
        <p style="margin:.2rem 0 0;color:var(--shell-muted);font-size:.8rem">NOK</p>
    </article>
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Paid amount</p>
        <strong style="display:block;font-size:2rem;margin:.3rem 0">{{ number_format((float) $paidAmount, 2) }}</strong>
        <p style="margin:.2rem 0 0;color:var(--shell-muted);font-size:.8rem">NOK</p>
    </article>
    <article class="shell-card" style="text-align:center">
        <p style="margin:0;color:var(--shell-muted);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em">Payment</p>
        <strong style="display:block;font-size:1.1rem;margin:.5rem 0;color:var(--shell-muted)">Not connected</strong>
    </article>
</section>

{{-- Document list --}}
@forelse($documents as $document)
    <a href="{{ route('account.billing.documents.show', $document) }}"
       style="display:block;text-decoration:none;color:inherit;margin-bottom:.65rem">
        <article class="shell-card" style="display:grid;grid-template-columns:1fr auto;gap:.85rem;align-items:center">
            <div>
                <strong>{{ $document->document_number }}</strong>
                <p style="margin:.25rem 0 0;color:var(--shell-muted);font-size:.82rem">
                    Order {{ $document->order?->order_number ?? 'Not linked' }}
                    · Issued {{ $document->issued_at?->format('d M Y') ?? 'Not issued' }}
                </p>
            </div>
            <div style="text-align:right">
                <strong>{{ number_format((float) $document->total_amount, 2) }} {{ $document->currency }}</strong>
                <p style="margin:.2rem 0 0;font-size:.78rem;color:var(--shell-muted)">{{ str($document->status)->title() }}</p>
            </div>
        </article>
    </a>
@empty
    <article class="shell-card" style="text-align:center;padding:3rem 1.5rem">
        <p style="margin:0;color:var(--shell-muted)">No billing documents yet.</p>
    </article>
@endforelse

@endsection
