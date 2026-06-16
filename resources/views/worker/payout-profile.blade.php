@extends('worker.layout')

@section('title', 'Payout profile')

@section('content')

<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Payout readiness</p>
        <h1>Payout profile</h1>
        <p class="muted">Required before future settlements can be paid. No payout is executed from this page.</p>
    </div>
    <a class="worker-btn" href="{{ route('worker.dashboard') }}">← Dashboard</a>
</div>

{{-- Status card --}}
<section class="worker-card" style="margin-bottom:1rem">
    <div class="kv">
        <span>Profile status</span>
        <strong>{{ str($profile->status)->replace('_', ' ')->title() }}</strong>
    </div>
    <div class="kv">
        <span>Tax review</span>
        <strong>{{ str($profile->tax_profile_status)->replace('_', ' ')->title() }}</strong>
    </div>
    <div class="kv" style="border:none">
        <span>Identity review</span>
        <strong>{{ str($profile->identity_profile_status)->replace('_', ' ')->title() }}</strong>
    </div>

    @if(count($readiness['blockers'] ?? []))
        <div style="margin-top:.85rem;padding:.75rem;border-radius:9px;border:1px solid rgba(245,189,84,.28);background:rgba(72,50,10,.6)">
            <p class="worker-hero-eyebrow" style="color:var(--amber);margin-bottom:.4rem">Blockers</p>
            @foreach($readiness['blockers'] as $reason)
                <p class="muted" style="margin:.25rem 0;font-size:.85rem">{{ $reason }}</p>
            @endforeach
        </div>
    @endif
</section>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1rem">

    {{-- Edit profile form --}}
    <section class="worker-card">
        <h2 style="margin:0 0 1rem;font-size:1rem">Update profile</h2>
        <form method="post" action="{{ route('worker.payout-profile.update') }}">
            @csrf

            <label>
                Payout method
                <select name="payout_method" style="margin-top:.35rem">
                    <option value="manual_bank_review"        @selected($profile->payout_method === 'manual_bank_review')>Manual bank review</option>
                    <option value="vipps_deferred"            @selected($profile->payout_method === 'vipps_deferred')>Vipps deferred</option>
                    <option value="external_provider_deferred" @selected($profile->payout_method === 'external_provider_deferred')>External provider deferred</option>
                </select>
            </label>

            <label>
                Account holder name
                <input
                    name="account_holder_name"
                    value="{{ $profile->account_holder_name }}"
                    autocomplete="name"
                    placeholder="Full legal name"
                >
            </label>

            <label>
                Bank account number
                <input
                    name="bank_account"
                    autocomplete="off"
                    placeholder="{{ $profile->bank_account_last_four ? 'Stored · ••••'.$profile->bank_account_last_four : 'Not stored' }}"
                >
            </label>

            <label>
                IBAN
                <input
                    name="iban"
                    autocomplete="off"
                    placeholder="{{ $profile->iban_last_four ? 'Stored · ••••'.$profile->iban_last_four : 'Not stored' }}"
                >
            </label>

            <label>
                SWIFT / BIC
                <input
                    name="swift_bic"
                    autocomplete="off"
                    placeholder="{{ $profile->swift_bic_last_four ? 'Stored · ••••'.$profile->swift_bic_last_four : 'Not stored' }}"
                >
            </label>

            <label>
                Vipps phone number
                <input
                    name="vipps_phone"
                    autocomplete="off"
                    placeholder="{{ $profile->vipps_phone_last_four ? 'Stored · ••••'.$profile->vipps_phone_last_four : 'Not stored' }}"
                >
            </label>

            <input type="hidden" name="country"  value="NO">
            <input type="hidden" name="currency" value="NOK">

            <button class="btn primary" style="width:100%;margin-top:.75rem">Save encrypted profile</button>
        </form>
    </section>

    {{-- Submit for review --}}
    <section class="worker-card">
        <h2 style="margin:0 0 .75rem;font-size:1rem">Submit for review</h2>
        <p class="muted" style="font-size:.85rem;margin-bottom:1rem">
            Once all fields are saved, submit the profile for manual review by the operations team.
            Approval is required before any settlement can be paid out.
        </p>

        <form method="post" action="{{ route('worker.payout-profile.submit') }}">
            @csrf
            <button class="btn" style="width:100%">Submit profile for review</button>
        </form>

        <div style="margin-top:1.2rem;padding:.85rem;border-radius:9px;background:rgba(148,163,184,.06);border:1px solid var(--line)">
            <p class="worker-hero-eyebrow" style="margin-bottom:.5rem">What happens next</p>
            <ul class="muted" style="margin:0;padding-left:1.2rem;font-size:.82rem;line-height:1.7">
                <li>Operations team receives a review notification</li>
                <li>Identity and tax reviews must also pass</li>
                <li>No payout is created from this page</li>
                <li>Bank data is encrypted at rest</li>
            </ul>
        </div>

        <div style="margin-top:1rem">
            <a class="worker-btn" href="{{ route('worker.payout-reviews.index') }}" style="width:100%;justify-content:center">
                Go to tax &amp; identity reviews →
            </a>
        </div>
    </section>

</div>
@endsection
