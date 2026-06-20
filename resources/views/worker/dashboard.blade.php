@extends('worker.layout')
@section('title', 'Worker Dashboard')
@section('content')
@php
    $firstName = trim(str($user->name ?? 'Worker')->before(' ')->toString()) ?: 'Worker';
    $profileStatus = $user->workerProfile?->status ?? 'missing';
    $profileApproved = $profileStatus === 'approved';
    $availabilityStatus = $availability?->status ?? 'offline';
    $online = in_array($availabilityStatus, ['online', 'available'], true);
    $ordersCount = $orders->count();
    $activeStatus = $activeOrder?->status?->value ?? null;
    $activeAssignment = $activeOrder?->activeDispatchAssignment();
    $lastPingAge = $lastPing?->created_at ? now()->diffInSeconds($lastPing->created_at) : null;
    $staleSeconds = (int) ($mapConfig['stale_seconds'] ?? 120);
    $gpsFresh = ! is_null($lastPingAge) && $lastPingAge <= $staleSeconds;
    $gpsState = $gpsFresh ? 'Fresh' : ($lastPing ? 'Stale' : 'Not sharing');
    $gpsPill = $gpsFresh ? 'ok' : ($lastPing ? 'warn' : 'danger');
    $payoutReady = (bool) ($payoutProfile['ready'] ?? false);
    $payoutBlockers = collect($payoutProfile['blockers'] ?? []);
    $readyAmount = (float) ($earnings['ready_amount'] ?? 0);
    $paidAmount = (float) ($earnings['paid_amount'] ?? 0);
    $blockedCount = (int) ($earnings['blocked_count'] ?? 0);
    $todayEntries = collect($earnings['entries'] ?? [])->filter(fn ($entry) => $entry->created_at?->isToday());
    $todayAmount = (float) $todayEntries->sum('worker_amount');
    $readiness = [
        ['label' => 'Approved worker profile', 'ok' => $profileApproved, 'detail' => str($profileStatus)->replace('_',' ')->title()],
        ['label' => 'Work mode', 'ok' => $online, 'detail' => $online ? 'Online and visible to dispatch' : 'Offline — not receiving work'],
        ['label' => 'GPS readiness', 'ok' => $gpsFresh, 'detail' => $lastPing ? $gpsState.' · '.$lastPing->created_at->diffForHumans() : 'No recent location ping'],
        ['label' => 'Payout profile', 'ok' => $payoutReady, 'detail' => $payoutReady ? 'Ready for settlement review' : 'Review required before payout'],
    ];
@endphp

@push('styles')
<style>
    .wk-dashboard{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(320px,.75fr);gap:1rem;align-items:start}.wk-hero{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:24px;background:linear-gradient(145deg,rgba(12,31,50,.96),rgba(5,16,29,.96));padding:1.25rem;box-shadow:0 26px 80px rgba(0,0,0,.28)}.wk-hero:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 78% 22%,rgba(var(--brand-rgb),.19),transparent 28%),linear-gradient(90deg,rgba(85,217,255,.06),transparent);pointer-events:none}.wk-hero>*{position:relative}.wk-hero h2{margin:.25rem 0 0;font-size:clamp(2rem,5vw,3.5rem);line-height:1;font-weight:950;letter-spacing:-.04em}.wk-status-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem;margin-top:1rem}.wk-stat span{display:block;color:var(--muted);font-size:.68rem;font-weight:950;text-transform:uppercase;letter-spacing:.08em}.wk-stat strong{display:block;margin-top:.35rem;font-size:1.35rem}.wk-section{display:grid;gap:1rem}.wk-current{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;align-items:center}.wk-current h3{margin:0;font-size:1.45rem}.wk-current-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin:.8rem 0}.wk-readiness-list{display:grid;gap:.45rem}.wk-readiness-row{display:grid;grid-template-columns:auto minmax(0,1fr);gap:.65rem;align-items:start;border-bottom:1px solid var(--line2);padding:.65rem 0}.wk-readiness-row:last-child{border-bottom:0}.wk-dot{display:grid;width:1.45rem;height:1.45rem;place-items:center;border-radius:999px;font-size:.72rem;font-weight:950}.wk-dot.ok{background:rgba(var(--brand-rgb),.13);border:1px solid rgba(var(--brand-rgb),.32);color:var(--green)}.wk-dot.warn{background:rgba(245,189,84,.10);border:1px solid rgba(245,189,84,.32);color:var(--amber)}.wk-empty{display:grid;place-items:center;min-height:16rem;text-align:center}.wk-empty-icon{display:grid;width:4.5rem;height:4.5rem;place-items:center;border-radius:999px;background:rgba(148,163,184,.08);border:1px solid var(--line);font-size:1.9rem;margin:0 auto .8rem}.wk-actions{display:flex;flex-wrap:wrap;gap:.6rem}.wk-secondary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem}.wk-loading{display:none}.wk-mini-map{border:1px solid var(--line);border-radius:18px;min-height:190px;background:linear-gradient(135deg,rgba(85,217,255,.08),transparent),repeating-linear-gradient(35deg,rgba(148,163,184,.08) 0 1px,transparent 1px 42px),#06111e;position:relative;overflow:hidden}.wk-mini-map:after{content:"Narvik operations map readiness";position:absolute;left:1rem;bottom:1rem;color:var(--muted);font-size:.78rem}.wk-pin{position:absolute;left:52%;top:42%;width:14px;height:14px;border-radius:999px;background:var(--green);box-shadow:0 0 0 10px rgba(var(--brand-rgb),.12),0 0 30px rgba(var(--brand-rgb),.45)}
    @media(max-width:1100px){.wk-dashboard{grid-template-columns:1fr}.wk-secondary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.wk-status-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:720px){.wk-current{grid-template-columns:1fr}.wk-secondary-grid{grid-template-columns:1fr}.wk-status-grid{grid-template-columns:1fr 1fr}.wk-hero{border-radius:18px;padding:1rem}.wk-actions .worker-btn,.wk-actions form{width:100%}.wk-actions form .worker-btn{width:100%}}
</style>
@endpush

<div class="wk-dashboard">
    <div class="wk-section">
        <section class="wk-hero" aria-labelledby="worker-dashboard-title">
            <p class="worker-hero-eyebrow">Worker Cockpit · Wave 3A foundation</p>
            <h2 id="worker-dashboard-title">Hei, {{ $firstName }}</h2>
            <p class="muted" style="max-width:58rem;margin:.8rem 0 0">Your production cockpit shows only real operational data: readiness, current assignment, existing GPS status and payout readiness. No fake work, GPS, ETA or earnings are generated.</p>
            <div class="wk-status-grid" aria-label="Worker status summary">
                <article class="worker-card wk-stat"><span>Work mode</span><strong>{{ str($availabilityStatus)->replace('_',' ')->title() }}</strong></article>
                <article class="worker-card wk-stat"><span>Assignments</span><strong>{{ $ordersCount }}</strong></article>
                <article class="worker-card wk-stat"><span>GPS</span><strong>{{ $gpsState }}</strong></article>
                <article class="worker-card wk-stat"><span>Ready earnings</span><strong>{{ number_format($readyAmount, 2) }} NOK</strong></article>
            </div>
        </section>

        <section class="worker-card" aria-labelledby="current-assignment-title">
            @if($activeOrder)
                <div class="wk-current">
                    <div>
                        <p class="worker-hero-eyebrow">Current assignment</p>
                        <h3 id="current-assignment-title">{{ $activeOrder->order_number }}</h3>
                        <p class="muted" style="margin:.35rem 0 0">{{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key ?? 'Assigned service' }}</p>
                        <div class="wk-current-meta">
                            <span class="status-pill ok">{{ str($activeStatus)->replace('_',' ')->title() }}</span>
                            <span class="status-pill {{ $gpsPill }}">GPS {{ $gpsState }}</span>
                            @if($activeAssignment)<span class="status-pill warn">Assignment {{ str($activeAssignment->status)->replace('_',' ')->title() }}</span>@endif
                        </div>
                    </div>
                    <a class="worker-btn is-primary" href="{{ route('worker.orders.show', $activeOrder) }}">Open assignment</a>
                </div>
            @else
                <div class="wk-empty">
                    <div>
                        <span class="wk-empty-icon" aria-hidden="true">📦</span>
                        <h3 id="current-assignment-title" style="margin:.2rem 0">No active assignment</h3>
                        <p class="muted" style="max-width:34rem;margin:.4rem auto 1rem">Stay online to receive real dispatches. Assignments appear here only when BiKuBe operations assigns work to you.</p>
                        <div class="wk-actions" style="justify-content:center">
                            <a class="worker-btn" href="{{ route('worker.orders.index') }}">View orders</a>
                            <a class="worker-btn" href="{{ route('worker.support.index') }}">Contact support</a>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="wk-secondary-grid" aria-label="Operational summary cards">
            <article class="worker-card">
                <p class="worker-hero-eyebrow">Today</p>
                <h3 style="margin:.35rem 0 0">{{ number_format($todayAmount, 2) }} NOK</h3>
                <p class="muted" style="margin:.3rem 0 0">{{ $todayEntries->count() }} real settlement entries today.</p>
            </article>
            <article class="worker-card">
                <p class="worker-hero-eyebrow">Payout status</p>
                <h3 style="margin:.35rem 0 0">{{ $payoutReady ? 'Ready' : 'Needs review' }}</h3>
                <p class="muted" style="margin:.3rem 0 0">{{ $blockedCount }} blocked settlement entries.</p>
            </article>
            <article class="worker-card">
                <p class="worker-hero-eyebrow">Support</p>
                <h3 style="margin:.35rem 0 0">Help reachable</h3>
                <p class="muted" style="margin:.3rem 0 0">Use support for assignment, GPS, payout or safety issues.</p>
            </article>
        </section>
    </div>

    <aside class="wk-section" aria-label="Readiness and status panel">
        <section class="worker-card" aria-labelledby="readiness-title">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start">
                <div>
                    <p class="worker-hero-eyebrow">Ready to work?</p>
                    <h3 id="readiness-title" style="margin:.35rem 0 0">{{ $profileApproved && $online ? 'Operational' : 'Action needed' }}</h3>
                </div>
                <span class="status-pill {{ $profileApproved && $online ? 'ok' : 'warn' }}">{{ $online ? 'Online' : 'Offline' }}</span>
            </div>
            <div class="wk-readiness-list" style="margin-top:.75rem">
                @foreach($readiness as $item)
                    <div class="wk-readiness-row">
                        <span class="wk-dot {{ $item['ok'] ? 'ok' : 'warn' }}">{{ $item['ok'] ? '✓' : '!' }}</span>
                        <div><strong>{{ $item['label'] }}</strong><br><span class="muted">{{ $item['detail'] }}</span></div>
                    </div>
                @endforeach
            </div>
            <div class="wk-actions" style="margin-top:1rem">
                @if($online)
                    <form method="POST" action="{{ route('worker.presence.offline') }}">@csrf<button class="worker-btn is-danger" type="submit">Go offline</button></form>
                @else
                    <form method="POST" action="{{ route('worker.presence.online') }}">@csrf<button class="worker-btn is-primary" type="submit" @disabled(! $profileApproved)>Go online</button></form>
                @endif
                <a class="worker-btn" href="{{ route('worker.profile.index') }}">Profile</a>
            </div>
            @unless($profileApproved)
                <p class="muted" style="margin:.8rem 0 0;font-size:.85rem">Online mode is disabled until the worker profile is approved.</p>
            @endunless
        </section>

        <section class="worker-card" aria-labelledby="gps-title">
            <p class="worker-hero-eyebrow">GPS readiness</p>
            <h3 id="gps-title" style="margin:.35rem 0 0">{{ $gpsState }}</h3>
            <div class="wk-mini-map" aria-label="GPS readiness map placeholder"><span class="wk-pin" aria-hidden="true"></span></div>
            <div class="kv"><span class="muted">Last ping</span><strong>{{ $lastPing ? $lastPing->created_at->diffForHumans() : 'No ping recorded' }}</strong></div>
            <div class="kv"><span class="muted">Accuracy</span><strong>{{ $lastPing?->accuracy_meters ? number_format((float) $lastPing->accuracy_meters, 0).' m' : 'Unavailable' }}</strong></div>
            <div class="kv"><span class="muted">Stale after</span><strong>{{ $staleSeconds }} seconds</strong></div>
            <p class="muted" style="margin:.85rem 0 0;font-size:.85rem">Wave 3A displays existing GPS data only. It does not start new tracking logic.</p>
        </section>

        <section class="worker-card" aria-labelledby="payout-title">
            <p class="worker-hero-eyebrow">Payout readiness</p>
            <h3 id="payout-title" style="margin:.35rem 0 0">{{ $payoutReady ? 'Ready for review' : 'Manual review required' }}</h3>
            <div class="kv"><span class="muted">Ready amount</span><strong>{{ number_format($readyAmount, 2) }} NOK</strong></div>
            <div class="kv"><span class="muted">Paid amount</span><strong>{{ number_format($paidAmount, 2) }} NOK</strong></div>
            <div class="kv"><span class="muted">Blocked entries</span><strong>{{ $blockedCount }}</strong></div>
            @if($payoutBlockers->isNotEmpty())
                <div style="margin-top:.8rem;padding:.75rem;border:1px solid rgba(245,189,84,.28);border-radius:12px;background:rgba(72,50,10,.45)">
                    <strong style="color:var(--amber)">Blockers</strong>
                    <ul class="muted" style="margin:.45rem 0 0;padding-left:1.2rem;font-size:.85rem">
                        @foreach($payoutBlockers as $blocker)<li>{{ $blocker }}</li>@endforeach
                    </ul>
                </div>
            @endif
            <div class="wk-actions" style="margin-top:1rem"><a class="worker-btn" href="{{ route('worker.wallet.index') }}">Open wallet</a></div>
        </section>
    </aside>
</div>

<noscript>
    <div class="worker-alert worker-error" role="alert" style="margin-top:1rem">JavaScript is disabled. The cockpit still shows server-rendered readiness, but future interactive GPS/PWA features require browser JavaScript.</div>
</noscript>
@endsection
