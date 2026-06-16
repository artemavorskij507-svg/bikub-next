@extends('worker.layout')

@section('title', 'Tax & identity reviews')

@section('content')

<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Payout compliance</p>
        <h1>Tax &amp; identity reviews</h1>
        <p class="muted">Text attestations are reviewed manually. No approval is implied by submission alone.</p>
    </div>
    <a class="worker-btn" href="{{ route('worker.payout-profile.show') }}">← Payout profile</a>
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1rem">

    @foreach(['identity' => $identity, 'tax' => $tax] as $type => $review)
    <section class="worker-card">

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.85rem">
            <div>
                <p class="worker-hero-eyebrow" style="margin-bottom:.25rem">{{ str($type)->title() }} review</p>
                <h2 style="margin:0;font-size:1.05rem">{{ str($review->status)->replace('_', ' ')->title() }}</h2>
            </div>
            <span class="worker-status-pill"
                style="@if(in_array($review->status, ['approved','accepted'])) border-color:rgba(52,230,154,.36);background:rgba(52,230,154,.1);color:#d0fff0; @elseif($review->status === 'rejected') border-color:rgba(251,113,133,.36);background:rgba(98,29,43,.4);color:#ffd9df; @endif">
                {{ str($review->status)->replace('_', ' ')->upper() }}
            </span>
        </div>

        {{-- Requested changes --}}
        @if($review->requested_changes)
            <div style="margin-bottom:.85rem;padding:.75rem;border-radius:9px;border:1px solid rgba(245,189,84,.28);background:rgba(72,50,10,.6)">
                <p class="worker-hero-eyebrow" style="color:var(--amber);margin-bottom:.3rem">Requested changes</p>
                <p class="muted" style="margin:0;font-size:.85rem">{{ $review->requested_changes }}</p>
            </div>
        @endif

        {{-- Submission form --}}
        <form method="post" action="{{ route('worker.payout-reviews.submit', $type) }}">
            @csrf

            <label>
                Evidence summary
                <textarea
                    name="evidence_summary"
                    required
                    rows="4"
                    placeholder="Describe your {{ $type }} evidence clearly."
                    style="margin-top:.35rem"
                >{{ old('evidence_summary', $review->evidence_summary) }}</textarea>
            </label>

            <label>
                Your note (optional)
                <textarea
                    name="worker_note"
                    rows="2"
                    placeholder="Additional context for the reviewer."
                    style="margin-top:.35rem"
                >{{ old('worker_note', $review->worker_note) }}</textarea>
            </label>

            <button class="btn primary" style="width:100%;margin-top:.5rem">
                Submit {{ str($type)->title() }} attestation
            </button>
        </form>

        {{-- Document upload --}}
        <div style="margin-top:1rem;padding:.75rem;border-radius:9px;border:1px solid var(--line);background:rgba(148,163,184,.05)">
            <p class="worker-hero-eyebrow" style="margin-bottom:.4rem">Document upload</p>
            <button class="btn" disabled style="width:100%;opacity:.55;cursor:not-allowed"
                title="Private document upload requires approved media security policy.">
                Document upload unavailable
            </button>
            <p class="muted" style="margin:.5rem 0 0;font-size:.75rem">
                Private document upload requires approved media security policy.
                Images are disabled until EXIF stripping is configured.
                Virus scanning is not yet configured.
            </p>
        </div>

    </section>
    @endforeach

</div>

<section class="worker-card" style="margin-top:1rem">
    <p class="worker-hero-eyebrow" style="margin-bottom:.5rem">Review process</p>
    <ul class="muted" style="margin:0;padding-left:1.2rem;font-size:.82rem;line-height:1.8">
        <li>Submit text attestations for both identity and tax reviews</li>
        <li>Operations team reviews each submission manually</li>
        <li>Both reviews must be approved before settlements can be paid</li>
        <li>No payment or payout is created from this page</li>
        <li>Evidence is private — not visible to customers</li>
    </ul>
</section>

@endsection
