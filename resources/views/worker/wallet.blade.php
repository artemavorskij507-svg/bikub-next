@extends('worker.layout')
@section('title', 'Finances')
@section('content')
<style>
.ledger-row{display:grid;grid-template-columns:auto 1fr auto;gap:.85rem;align-items:center;border:1px solid var(--line);border-radius:12px;background:var(--panel2);padding:.9rem 1rem;transition:border-color .2s}
.ledger-row.is-ready{border-color:rgba(52,230,154,.28)}
.ledger-row.is-paid{border-color:rgba(85,217,255,.18)}
.ledger-row.is-blocked{border-color:rgba(245,189,84,.22)}
.ledger-icon{display:grid;width:2.5rem;height:2.5rem;place-items:center;border-radius:11px;font-size:1.1rem;flex-shrink:0}
.ledger-icon.ready{background:rgba(52,230,154,.14)}
.ledger-icon.paid{background:rgba(85,217,255,.12)}
.ledger-icon.blocked{background:rgba(245,189,84,.12)}
.ledger-num{font-size:.72rem;color:var(--muted);margin:0 0 .15rem}
.ledger-status{display:inline-flex;align-items:center;border:1px solid transparent;border-radius:999px;padding:.15rem .5rem;font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em}
.s-ready{border-color:rgba(52,230,154,.32);background:rgba(52,230,154,.1);color:var(--green)}
.s-paid{border-color:rgba(85,217,255,.28);background:rgba(85,217,255,.08);color:var(--blue)}
.s-blocked,.s-pending_capture{border-color:rgba(245,189,84,.28);background:rgba(245,189,84,.08);color:var(--amber)}
.s-pending,.s-created,.s-recalculated{border-color:rgba(148,163,184,.22);background:rgba(148,163,184,.06);color:var(--muted)}
.ledger-amount{font-size:1rem;font-weight:950;text-align:right}
.ledger-list{display:grid;gap:.55rem}
.blocker-list{display:grid;gap:.4rem;margin:.5rem 0 0}
.blocker-item{display:grid;grid-template-columns:1.2rem 1fr;gap:.4rem;font-size:.82rem;color:#ffd9df;align-items:start}
</style>

<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Settlement</p>
        <h1>Finances</h1>
        <p class="muted">Settlement ledger, earnings summary and payout readiness. No balance is held — each entry maps to a completed order.</p>
    </div>
    <div class="actions">
        <a class="btn" href="{{ route('worker.payout-profile.show') }}">Payout profile</a>
        <a class="btn" href="{{ route('worker.payout-reviews.index') }}">Tax &amp; identity</a>
    </div>
</div>

{{-- KPI row --}}
<section class="worker-kpis" style="margin-bottom:1rem">
    <article class="worker-card worker-kpi">
        <span>Ready to pay</span>
        <strong style="color:var(--green)">{{ number_format($readyAmount, 2) }}</strong>
        <p class="muted" style="font-size:.73rem;margin:.25rem 0 0">NOK · approved entries</p>
    </article>
    <article class="worker-card worker-kpi">
        <span>Paid out</span>
        <strong style="color:var(--blue)">{{ number_format($paidAmount, 2) }}</strong>
        <p class="muted" style="font-size:.73rem;margin:.25rem 0 0">NOK · total paid</p>
    </article>
    <article class="worker-card worker-kpi">
        <span>Blocked</span>
        <strong style="color:{{ $blockedCount > 0 ? 'var(--amber)' : 'var(--muted)' }}">{{ $blockedCount }}</strong>
        <p class="muted" style="font-size:.73rem;margin:.25rem 0 0">Pending resolution</p>
    </article>
    <article class="worker-card worker-kpi">
        <span>Profile</span>
        @php $profileStatus = $payoutProfile['profile']?->status ?? 'missing'; @endphp
        <strong style="color:{{ $profileStatus === 'approved' ? 'var(--green)' : ($profileStatus === 'missing' ? 'var(--danger)' : 'var(--amber)') }}">
            {{ ucfirst(str_replace('_', ' ', $profileStatus)) }}
        </strong>
        <p class="muted" style="font-size:.73rem;margin:.25rem 0 0">Payout readiness</p>
    </article>
</section>

<div class="grid" style="grid-template-columns:minmax(0,1fr) minmax(0,320px);gap:1rem;align-items:start">
    {{-- Ledger --}}
    <article class="worker-card">
        <h3 style="margin:0 0 .9rem;font-size:.92rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-weight:950">Settlement ledger</h3>
        <div class="ledger-list">
            @forelse($entries as $entry)
            @php
                $st = $entry->status;
                $iconMap = ['ready'=>'✓','paid'=>'↑','blocked'=>'!','pending_capture'=>'⏳','created'=>'·','recalculated'=>'~'];
                $icon = $iconMap[$st] ?? '·';
                $amount = $entry->worker_amount;
                $currency = $entry->currency ?? 'NOK';
            @endphp
            <div class="ledger-row is-{{ $st }}">
                <div class="ledger-icon {{ in_array($st,['ready']) ? 'ready' : (in_array($st,['paid']) ? 'paid' : 'blocked') }}">{{ $icon }}</div>
                <div>
                    <p class="ledger-num">{{ $entry->entry_number }}</p>
                    <strong style="font-size:.88rem">Order #{{ $entry->order_id }}</strong>
                    <div style="margin-top:.3rem">
                        <span class="ledger-status s-{{ $st }}">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
                    </div>
                    @if($entry->blocker_reason)
                    <p style="color:var(--muted);font-size:.74rem;margin:.25rem 0 0">{{ Str::limit($entry->blocker_reason, 120) }}</p>
                    @endif
                </div>
                <div class="ledger-amount" style="color:{{ $st==='ready' ? 'var(--green)' : ($st==='paid' ? 'var(--blue)' : 'var(--muted)') }}">
                    @if($amount !== null)
                        {{ number_format((float)$amount, 2) }}<span style="font-size:.72rem;color:var(--muted);font-weight:500"> {{ $currency }}</span>
                    @else
                        <span style="color:var(--muted);font-size:.78rem">Blocked</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="worker-empty">
                <div>
                    <i style="display:grid;width:4.2rem;height:4.2rem;place-items:center;border-radius:999px;background:rgba(148,163,184,.1);font-style:normal;font-size:1.8rem;margin:0 auto 1rem">₿</i>
                    <h3>No entries yet</h3>
                    <p class="muted">Complete your first assignment and it will appear here once settlement is calculated.</p>
                </div>
            </div>
            @endforelse
        </div>
        @if(count($entries) >= 20)
        <p class="muted" style="font-size:.78rem;margin:.85rem 0 0;text-align:center">Showing 20 most recent entries.</p>
        @endif
    </article>

    {{-- Right column --}}
    <div class="grid">
        {{-- Payout readiness --}}
        <article class="worker-card" @if(!$payoutProfile['ready']) style="border-color:rgba(251,113,133,.28)" @endif>
            <h3 style="margin:0 0 .6rem;font-size:.9rem">Payout readiness</h3>
            @if($payoutProfile['ready'])
                <p style="color:var(--green);font-size:.84rem;margin:0 0 .75rem">✓ Payout profile is approved and ready.</p>
            @else
                @if(($payoutProfile['blockers'] ?? []))
                <div class="blocker-list">
                    @foreach($payoutProfile['blockers'] as $b)
                    <div class="blocker-item"><span>⚠</span><span>{{ $b }}</span></div>
                    @endforeach
                </div>
                @endif
            @endif
            <div class="actions" style="margin-top:.85rem">
                <a class="btn {{ $payoutProfile['ready'] ? '' : 'primary' }}" href="{{ route('worker.payout-profile.show') }}" style="font-size:.82rem">
                    {{ $payoutProfile['ready'] ? 'View payout profile' : 'Fix payout profile' }}
                </a>
            </div>
        </article>

        {{-- How earnings work --}}
        <article class="worker-card" style="border-color:rgba(85,217,255,.18)">
            <h3 style="margin:0 0 .6rem;font-size:.9rem;color:var(--blue)">How settlement works</h3>
            <div class="kv"><span>Created</span><small>Calculated from completed order evidence</small></div>
            <div class="kv"><span>Blocked</span><small>Awaiting payment capture or compliance</small></div>
            <div class="kv"><span>Ready</span><small>Approved, queued for next payout cycle</small></div>
            <div class="kv"><span>Paid</span><small>Transferred to your payout profile</small></div>
            <p class="muted" style="font-size:.75rem;margin:.75rem 0 0">Settlement amounts are only visible once a worker settlement rule has been approved for your account and attached order type.</p>
        </article>
    </div>
</div>
@endsection
