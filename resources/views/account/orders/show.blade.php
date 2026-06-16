@extends('layouts.account-shell')

@section('title', $order->order_number)

@section('content')
@php
    $completionProof = $order->completionProofs->first();
    $statusValue = str($order->status->value)->replace('_', ' ')->title();
    $latestQuote = $order->latestPriceQuote();
@endphp

{{-- Page header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <div>
        <span class="shell-eyebrow">Order detail</span>
        <h1 style="margin:.35rem 0 .25rem;font-size:clamp(1.6rem,4vw,2.4rem);font-weight:950">{{ $order->order_number }}</h1>
        <p style="margin:0;color:var(--shell-muted)">{{ $order->scenario?->title ?? $order->service_scenario_key }}</p>
    </div>
    <div style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap">
        <span style="display:inline-flex;border:1px solid rgba(37,220,145,.28);border-radius:999px;background:rgba(37,220,145,.08);padding:.28rem .75rem;color:var(--bkb-accent,#25dc91);font-size:.72rem;font-weight:900;text-transform:uppercase">
            {{ $statusValue }}
        </span>
        <a href="{{ route('account.orders.index') }}"
           style="padding:.55rem .85rem;border-radius:6px;border:1px solid var(--shell-line);text-decoration:none;color:var(--shell-text);font-size:.85rem">
            ← Orders
        </a>
    </div>
</div>

<div class="shell-grid" style="grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">

    {{-- Order status --}}
    <section class="shell-card">
        <span class="shell-eyebrow">Status</span>
        <div style="margin-top:.65rem;display:grid;gap:.5rem">
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Order status</span>
                <strong style="font-size:.85rem">{{ $statusValue }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Service</span>
                <strong style="font-size:.85rem">{{ $order->scenario?->title ?? $order->service_scenario_key }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Estimate</span>
                <strong style="font-size:.85rem">
                    {{ $latestQuote?->total ? number_format((float) $latestQuote->total, 2).' '.$order->currency : 'Manual review' }}
                </strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Payment</span>
                <strong style="font-size:.85rem">Not connected yet</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0">
                <span style="color:var(--shell-muted);font-size:.85rem">Live tracking</span>
                <strong style="font-size:.85rem">Available after worker GPS</strong>
            </div>
        </div>
    </section>

    {{-- Support tickets --}}
    <section class="shell-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.65rem">
            <span class="shell-eyebrow">Support</span>
            <a href="{{ route('account.support.create', ['order_id' => $order->id]) }}"
               style="font-size:.8rem;color:var(--bkb-accent,#25dc91);text-decoration:none">New ticket →</a>
        </div>
        @forelse($order->supportTickets as $ticket)
            <a href="{{ route('account.support.show', $ticket) }}"
               style="display:block;padding:.55rem 0;border-bottom:1px solid var(--shell-line);text-decoration:none;color:inherit">
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:.85rem">{{ $ticket->ticket_number }}</span>
                    <span style="font-size:.75rem;color:var(--shell-muted)">{{ str($ticket->status)->replace('_', ' ')->title() }}</span>
                </div>
                <span style="font-size:.78rem;color:var(--shell-muted)">{{ str($ticket->subject)->limit(52) }}</span>
            </a>
        @empty
            <p style="color:var(--shell-muted);font-size:.85rem;margin:0">No support tickets for this order.</p>
        @endforelse
    </section>

</div>

{{-- Billing documents --}}
<section class="shell-card" style="margin-bottom:1rem">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.85rem">
        <span class="shell-eyebrow">Billing</span>
        <a href="{{ route('account.billing.index') }}" style="font-size:.8rem;color:var(--bkb-accent,#25dc91);text-decoration:none">Billing center →</a>
    </div>
    <p style="margin:0 0 .85rem;color:var(--shell-muted);font-size:.85rem">
        Online payment is not connected yet. Invoices are issued by operations manually.
    </p>
    @forelse($order->billingDocuments as $document)
        <a href="{{ route('account.billing.documents.show', $document) }}"
           style="display:flex;justify-content:space-between;align-items:center;padding:.65rem 0;border-bottom:1px solid var(--shell-line);text-decoration:none;color:inherit">
            <div>
                <strong style="font-size:.9rem">{{ $document->document_number }}</strong>
                <span style="display:block;font-size:.78rem;color:var(--shell-muted)">Issued {{ $document->issued_at?->format('d M Y') ?? 'Not issued' }}</span>
            </div>
            <div style="text-align:right">
                <strong style="font-size:.9rem">{{ number_format((float) $document->total_amount, 2) }} {{ $document->currency }}</strong>
                <span style="display:block;font-size:.78rem;color:var(--shell-muted)">{{ str($document->status)->title() }}</span>
            </div>
        </a>
    @empty
        <p style="color:var(--shell-muted);font-size:.85rem;margin:0">No billing documents for this order.</p>
    @endforelse
</section>

{{-- Completion proof --}}
<section class="shell-card" style="margin-bottom:2rem">
    <span class="shell-eyebrow">Completion confirmation</span>

    @if($completionProof)
        <div style="margin-top:.65rem;display:grid;gap:.5rem;margin-bottom:1rem">
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Proof status</span>
                <strong style="font-size:.85rem">{{ str($completionProof->status)->replace('_', ' ')->title() }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.85rem">Submitted</span>
                <strong style="font-size:.85rem">{{ $completionProof->submitted_at?->format('d M Y H:i') }}</strong>
            </div>
        </div>

        @if($completionProof->worker_note)
            <div style="padding:.85rem;border-radius:6px;background:rgba(148,163,184,.06);border:1px solid var(--shell-line);margin-bottom:1rem">
                <p style="margin:0 0 .3rem;font-size:.75rem;color:var(--shell-muted);text-transform:uppercase;font-weight:900;letter-spacing:.08em">Worker note</p>
                <p style="margin:0;font-size:.9rem">{{ $completionProof->worker_note }}</p>
            </div>
        @endif

        @if($completionProof->status === 'submitted')
            <p style="margin:0 0 1rem;color:var(--shell-muted);font-size:.85rem">
                Review the worker's note and confirm or dispute completion.
                Confirmation records your review. Payment remains unavailable until the payment provider is ready.
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem">
                <form method="post" action="{{ route('account.completion-proofs.accept', $completionProof) }}" class="shell-form" style="margin:0">
                    @csrf
                    <label style="font-size:.82rem">
                        Confirmation note (optional)
                        <textarea name="note" maxlength="2000" rows="3" placeholder="Describe what was delivered…"></textarea>
                    </label>
                    <button type="submit" class="shell-primary" style="width:100%;margin-top:.5rem">Confirm completed</button>
                </form>
                <form method="post" action="{{ route('account.completion-proofs.dispute', $completionProof) }}" class="shell-form" style="margin:0">
                    @csrf
                    <label style="font-size:.82rem">
                        Problem description <span style="color:#fca5a5">*</span>
                        <textarea name="reason" required maxlength="5000" rows="3" placeholder="Describe what was wrong…"></textarea>
                    </label>
                    <button type="submit"
                        style="width:100%;margin-top:.5rem;padding:.75rem 1rem;border:1px solid rgba(252,165,165,.4);border-radius:6px;background:rgba(72,22,34,.7);color:#ffd9df;font-weight:900;cursor:pointer">
                        Report a problem
                    </button>
                </form>
            </div>

        @elseif($completionProof->status === 'accepted')
            <div style="padding:.85rem;border-radius:6px;border:1px solid rgba(37,220,145,.24);background:rgba(10,50,35,.6)">
                <p style="margin:0;font-size:.85rem;color:#d0ffe8">
                    You confirmed this completion.
                    Payment remains unavailable until the payment provider is ready.
                </p>
            </div>

        @elseif($completionProof->status === 'disputed')
            <div style="padding:.85rem;border-radius:6px;border:1px solid rgba(252,165,165,.28);background:rgba(72,22,34,.6)">
                <p style="margin:0;font-size:.85rem;color:#ffd9df">
                    Your dispute was recorded.
                    <a href="{{ route('account.support.create', ['order_id' => $order->id]) }}" style="color:inherit">Continue in Support →</a>
                </p>
            </div>
        @endif

    @else
        <p style="margin:.65rem 0 0;color:var(--shell-muted);font-size:.85rem">
            No completion proof has been submitted by the worker yet.
        </p>
    @endif
</section>
@endsection
