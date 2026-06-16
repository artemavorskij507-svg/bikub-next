@extends('worker.layout')

@section('title', 'Worker dashboard')

@section('content')
@php
    $isOnline = ($availability?->status === 'online' || $availability?->status === 'available');
    $activeOrder = $orders->first();
    $readyAmount = (float) ($earnings['ready_amount'] ?? 0);
    $paidAmount = (float) ($earnings['paid_amount'] ?? 0);
    $blockedCount = (int) ($earnings['blocked_count'] ?? 0);
@endphp

<section class="grid">
    <article class="worker-hero">
        <div>
            <p class="worker-hero-eyebrow">Courier operations</p>
            <h2>Line status: {{ $isOnline ? 'Online' : 'Offline' }}</h2>
            <p>Keep your worker status accurate. GPS is never faked: location is sent only from an assigned order screen after browser permission.</p>
            <div class="actions">
                <a class="worker-btn" href="{{ route('worker.orders.index') }}">Open assignments</a>
                <a class="worker-btn" href="{{ route('worker.payout-profile.show') }}">Payout readiness</a>
            </div>
        </div>
        <form method="post" action="{{ route($isOnline ? 'worker.presence.offline' : 'worker.presence.online') }}">
            @csrf
            <button class="btn {{ $isOnline ? 'danger' : 'primary' }}">
                {{ $isOnline ? 'Go offline' : 'Go online' }}
            </button>
        </form>
    </article>

    <section class="worker-kpis" aria-label="Worker daily snapshot">
        <article class="worker-card worker-kpi">
            <span>Assigned orders</span>
            <strong>{{ $orders->count() }}</strong>
            <p class="muted">Real active dispatch assignments for this account.</p>
        </article>
        <article class="worker-card worker-kpi">
            <span>Ready amount</span>
            <strong>{{ number_format($readyAmount, 2) }} NOK</strong>
            <p class="muted">Settlement entries ready for review. No wallet balance is implied.</p>
        </article>
        <article class="worker-card worker-kpi">
            <span>Paid amount</span>
            <strong>{{ number_format($paidAmount, 2) }} NOK</strong>
            <p class="muted">Recorded paid settlement amount only.</p>
        </article>
        <article class="worker-card worker-kpi">
            <span>Blocked</span>
            <strong>{{ $blockedCount }}</strong>
            <p class="muted">Entries blocked by payment, rule, profile or provider readiness.</p>
        </article>
    </section>

    <section class="grid cards">
        <article class="worker-card" style="grid-column:span 2">
            <div class="worker-page-head">
                <div>
                    <p class="worker-hero-eyebrow">Current assignment</p>
                    <h1>{{ $activeOrder?->order_number ?? 'No active assignment' }}</h1>
                    <p class="muted">Current delivery/task state and next worker cockpit action.</p>
                </div>
                @if($activeOrder)
                    <span class="worker-status-pill">{{ str($activeOrder->status->value)->replace('_', ' ')->title() }}</span>
                @endif
            </div>

            @if($activeOrder)
                <div class="worker-task-card">
                    <div>
                        <div class="kv"><span>Service</span><strong>{{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key }}</strong></div>
                        <div class="kv"><span>Payment</span><strong>{{ str($activeOrder->payment_status->value)->replace('_', ' ')->title() }}</strong></div>
                        <div class="kv"><span>Quote</span><strong>{{ $activeOrder->estimated_total ? 'NOK '.$activeOrder->estimated_total : 'Manual review' }}</strong></div>
                    </div>
                    <a class="btn primary" href="{{ route('worker.orders.show', $activeOrder) }}">Open assignment</a>
                </div>
            @else
                <div class="worker-empty">
                    <div>
                        <i>⌁</i>
                        <h3>No active assignment</h3>
                        <p class="muted">Stay online when you are ready. Dispatch must assign a real order before work can start.</p>
                    </div>
                </div>
            @endif
        </article>

        <aside class="grid">
            <article class="worker-card">
                <h3>Quick actions</h3>
                <div class="grid">
                    <a class="worker-btn" href="{{ route('worker.orders.index') }}">Assignment history</a>
                    <a class="worker-btn" href="{{ route('worker.payout-profile.show') }}">Payout profile</a>
                    <a class="worker-btn" href="{{ route('worker.payout-reviews.index') }}">Tax and identity reviews</a>
                    @if(Route::has('worker.support.index'))
                        <a class="worker-btn" href="{{ route('worker.support.index') }}">Support</a>
                    @endif
                </div>
            </article>

            <article class="worker-card">
                <h3>Work rules</h3>
                <ul class="muted">
                    <li>Go online only when you are ready for real assignments.</li>
                    <li>Send GPS only from the assigned order screen and only with permission.</li>
                    <li>Completion proof waits for customer confirmation; no fake completed state.</li>
                </ul>
            </article>
        </aside>
    </section>

    <section class="worker-card">
        <div class="worker-page-head">
            <div>
                <p class="worker-hero-eyebrow">Settlement readiness</p>
                <h1>Future payout blockers</h1>
                <p class="muted">This is readiness only. No payout is created from the worker cockpit.</p>
            </div>
            <a class="worker-btn" href="{{ route('worker.payout-profile.show') }}">Manage profile</a>
        </div>
        @forelse(($earnings['entries'] ?? collect())->take(5) as $entry)
            <div class="kv">
                <span>{{ $entry->entry_number }} · {{ str($entry->status)->replace('_', ' ')->title() }}</span>
                <strong>{{ $entry->worker_amount === null ? 'Amount blocked: settlement rule not configured' : number_format((float) $entry->worker_amount, 2).' '.$entry->currency }}</strong>
            </div>
            @if($entry->blocker_reason)<p class="muted">{{ $entry->blocker_reason }}</p>@endif
        @empty
            <p class="muted">No settlement ledger entries yet. No wallet balance or payout is implied.</p>
        @endforelse
        <div class="kv"><span>Payout profile</span><strong>{{ str($payoutProfile['profile']?->status ?? 'missing')->replace('_', ' ')->title() }}</strong></div>
        @if(($payoutProfile['blockers'] ?? []) !== [])
            <p class="muted">{{ collect($payoutProfile['blockers'])->join(' ') }}</p>
        @endif
    </section>
</section>
@endsection
