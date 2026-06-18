@extends('worker.layout')
@section('title', 'Cockpit')
@section('body-class', 'wk-dash')
@section('content')

@php
$firstName     = explode(' ', $user->name)[0] ?? 'Worker';
$workerType    = str($user->workerProfile?->worker_type ?? 'courier')->title();
$profileOk     = $user->workerProfile?->status === 'approved';
$payoutReady   = $payoutProfile['ready'] ?? false;
$payoutBlockers = $payoutProfile['blockers'] ?? [];
$entries       = $earnings['entries'] ?? collect();
$readyAmount   = (float) ($earnings['ready_amount'] ?? 0);
$todayOrders   = $entries->filter(fn($e) => $e->created_at?->isToday())->count();

$centerLat   = $mapConfig['center_lat'];
$centerLng   = $mapConfig['center_lng'];
$defaultZoom = $mapConfig['default_zoom'];
$maxAccuracy = $mapConfig['max_accuracy'];
$pingSeconds = $mapConfig['ping_seconds'];
$staleSeconds = $mapConfig['stale_seconds'];

$chart = [];
for ($i = 6; $i >= 0; $i--) {
    $d = now()->subDays($i)->format('Y-m-d');
    $chart[$d] = (float) $entries->filter(fn($e) =>
        $e->created_at?->format('Y-m-d') === $d &&
        in_array($e->status, ['ready', 'paid'])
    )->sum('worker_amount');
}
$todayKey      = now()->format('Y-m-d');
$todayEarnings = $chart[$todayKey] ?? 0;
$chartMax      = max(max(array_values($chart) ?: [0]), 1);
$lastPingFmt   = $lastPing ? $lastPing->created_at->diffForHumans() : null;
$initial       = mb_strtoupper(mb_substr($firstName, 0, 1));

// Navigation destination — resolves based on current milestone
$navDest  = null;
$navLabel = 'Destination';
if ($activeOrder) {
    $navIntake   = $activeOrder->metadata['intake'] ?? [];
    $navEvents   = $activeOrder->events->pluck('event_type')->toArray();
    $navPickedUp = in_array('worker.picked_up', $navEvents);
    $navDest     = $navPickedUp
        ? ($navIntake['dropoff_address']   ?? $navIntake['destination_address'] ?? null)
        : ($navIntake['pickup_address']    ?? $navIntake['vehicle_location']    ?? $navIntake['task_location'] ?? null);
    $navLabel    = $navPickedUp ? 'Drop-off' : 'Pickup';
}
@endphp

{{-- ─────────── Leaflet CSS ─────────── --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
{{-- ─────────── Alpine.js ──────────── --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>

<style>
/* Alpine cloak — hide before Alpine initialises */
[x-cloak] { display: none !important; }

/* ── Full-bleed dashboard: hide sidebar & topbar ─────────────────── */
.wk-dash .worker-sidebar,
.wk-dash .worker-topbar { display: none !important; }
.wk-dash .worker-shell,
.wk-dash .worker-main   { height: 100dvh !important; overflow: hidden; }
.wk-dash .worker-content {
    padding: 0 !important; overflow: hidden !important;
    position: relative; height: 100dvh !important;
}

/* ── Overlay top bar ─────────────────────────────────────────────── */
.dash-topbar {
    position: absolute; top: 0; left: 0; right: 0; height: 52px;
    z-index: 20; display: flex; align-items: center; gap: 0;
    background: rgba(4,10,22,.88);
    border-bottom: 1px solid rgba(148,163,184,.12);
    backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
}
.dash-brand {
    display: flex; align-items: center; gap: 8px;
    padding: 0 16px; text-decoration: none; flex-shrink: 0;
    border-right: 1px solid rgba(148,163,184,.1);
    height: 100%;
}
.dash-mark {
    width: 28px; height: 28px; border-radius: 8px;
    background: linear-gradient(135deg,#45efaa,#079263);
    color: #02130d; font-weight: 950; font-size: 10px;
    display: grid; place-items: center; flex-shrink: 0;
    box-shadow: 0 0 14px rgba(52,230,154,.35);
}
.dash-brand-name { font-size: .78rem; font-weight: 850; color: #e2f0ff; letter-spacing: .01em; }
.dash-brand-sub  { font-size: .6rem; color: var(--green); font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
.dash-nav {
    display: flex; align-items: center; gap: 2px;
    padding: 0 12px; flex: 1; overflow: hidden;
}
.dash-nav a {
    padding: 5px 11px; border-radius: 8px; font-size: .76rem; font-weight: 750;
    text-decoration: none; color: var(--muted); white-space: nowrap;
    border: 1px solid transparent; transition: all .15s;
}
.dash-nav a:hover, .dash-nav a.active {
    background: rgba(52,230,154,.08); border-color: rgba(52,230,154,.2);
    color: #d4faed;
}
.dash-user {
    display: flex; align-items: center; gap: 8px;
    padding: 0 14px; margin-left: auto; flex-shrink: 0;
    border-left: 1px solid rgba(148,163,184,.1); height: 100%;
}
.dash-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg,rgba(52,230,154,.3),rgba(85,217,255,.2));
    border: 1px solid rgba(52,230,154,.3);
    display: grid; place-items: center; font-size: .7rem; font-weight: 900; color: var(--green);
}
.dash-user-name { font-size: .78rem; font-weight: 750; color: #d0e8f8; }

/* ── Map ─────────────────────────────────────────────────────────── */
#worker-map {
    position: absolute; inset: 0; z-index: 0;
    background: #0d1f35;
}
/* Slight dark vignette overlay (pointer-events: none so map stays interactive) */
#worker-map-overlay {
    position: absolute; inset: 0; z-index: 1; pointer-events: none;
    background:
        radial-gradient(ellipse at 50% 50%, transparent 55%, rgba(4,10,22,.45) 100%),
        linear-gradient(to bottom, rgba(4,10,22,.25) 0%, transparent 15%, transparent 85%, rgba(4,10,22,.3) 100%);
}

/* ── Left info panel — hidden ────────────────────────────────────── */
.left-panel { display: none !important; }

/* ── Right cockpit panel ─────────────────────────────────────────── */
.cockpit-panel {
    position: absolute; z-index: 10; top: 64px; bottom: 14px; right: 14px;
    width: 420px; display: flex; flex-direction: column;
    border-radius: 20px;
    background: rgba(5,13,28,.95);
    border: 1px solid rgba(148,163,184,.13);
    box-shadow: 0 28px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(52,230,154,.04);
    backdrop-filter: blur(26px); -webkit-backdrop-filter: blur(26px);
    overflow: hidden;
}
.panel-scroll {
    flex: 1; overflow-y: auto; padding: 20px 20px 28px;
    scrollbar-width: thin; scrollbar-color: rgba(148,163,184,.12) transparent;
}
.panel-scroll::-webkit-scrollbar { width: 3px; }
.panel-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,.18); border-radius: 2px; }

/* Panel header accent line */
.cockpit-panel::before {
    content: ''; display: block; height: 3px; flex-shrink: 0;
    background: linear-gradient(90deg, #34e69a 0%, #55d9ff 60%, transparent 100%);
}

/* ── Glass cards ─────────────────────────────────────────────────── */
.gc {
    background: rgba(10,22,42,.82);
    border: 1px solid rgba(148,163,184,.1);
    border-radius: 14px; padding: 13px 14px;
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
}
.gc-green {
    background: rgba(10,30,20,.88);
    border-color: rgba(52,230,154,.2);
    box-shadow: 0 0 20px rgba(52,230,154,.06);
}

/* ── Status badge ────────────────────────────────────────────────── */
.s-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 999px; font-size: 10px; font-weight: 900;
    text-transform: uppercase; letter-spacing: .06em; flex-shrink: 0; transition: all .3s;
}
.s-online  { background: rgba(52,230,154,.14); color: var(--green); border: 1px solid rgba(52,230,154,.3); }
.s-offline { background: rgba(251,113,133,.1);  color: var(--danger); border: 1px solid rgba(251,113,133,.25); }
.s-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; transition: all .3s; }

/* ── Readiness checklist ─────────────────────────────────────────── */
.check-row {
    display: flex; align-items: center; gap: 8px;
    padding: 6px 0; border-bottom: 1px solid rgba(148,163,184,.07); font-size: .76rem;
}
.check-row:last-child { border-bottom: none; padding-bottom: 0; }
.check-icon {
    width: 18px; height: 18px; border-radius: 50%; flex-shrink: 0;
    display: grid; place-items: center; font-size: 9px; font-weight: 900;
}
.ci-ok   { background: rgba(52,230,154,.15); color: var(--green); border: 1px solid rgba(52,230,154,.3); }
.ci-warn { background: rgba(245,189,84,.1);  color: var(--amber); border: 1px solid rgba(245,189,84,.3); }
.ci-pend { background: rgba(148,163,184,.08); color: var(--muted); border: 1px solid rgba(148,163,184,.15); }

/* ── Swipe control ───────────────────────────────────────────────── */
.swipe-track {
    position: relative; height: 64px; border-radius: 999px;
    background: rgba(4,10,22,.9); border: 1px solid rgba(148,163,184,.18);
    overflow: hidden; user-select: none; touch-action: none; cursor: grab;
}
.swipe-fill {
    position: absolute; inset: 0 auto 0 0; border-radius: 999px; min-width: 0;
    background: linear-gradient(90deg, rgba(22,78,180,.7) 0%, rgba(52,230,154,.85) 100%);
    transition: width .06s;
}
.swipe-lbl {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 2;
    font-size: 10px; font-weight: 900; letter-spacing: .07em; text-transform: uppercase; pointer-events: none;
}
.swipe-lbl-l { left: 74px; color: rgba(226,232,240,.35); }
.swipe-lbl-r { right: 18px; color: rgba(167,243,208,.75); }
.swipe-center {
    position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: rgba(255,255,255,.7); font-size: 12px; pointer-events: none;
    z-index: 2; padding: 0 76px; text-align: center; letter-spacing: .01em;
}
.swipe-knob {
    position: absolute; top: 8px; left: 8px; width: 48px; height: 48px;
    background: #fff; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; color: #0f172a; font-size: 20px; z-index: 3;
    box-shadow: 0 4px 16px rgba(0,0,0,.3), 0 0 0 2px rgba(52,230,154,.2);
    transition: box-shadow .2s;
}
.swipe-knob:active { box-shadow: 0 2px 8px rgba(0,0,0,.4), 0 0 0 3px rgba(52,230,154,.4); }

/* ── Action buttons ──────────────────────────────────────────────── */
.cp-btn {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    width: 100%; padding: 12px 18px; border-radius: 12px; cursor: pointer;
    font-weight: 700; font-size: 13px; border: 1px solid transparent; text-decoration: none;
    transition: opacity .15s, transform .1s; font-family: inherit;
}
.cp-btn:active { transform: scale(.98); }
.cp-btn:disabled { opacity: .42; cursor: not-allowed; transform: none; }
.cp-btn-offline { background: rgba(251,113,133,.08); color: var(--danger); border-color: rgba(251,113,133,.22); }
.cp-btn-soft {
    background: rgba(255,255,255,.06); color: var(--text);
    border-color: rgba(148,163,184,.13); font-size: 12px;
    padding: 7px 12px; border-radius: 9px; width: auto;
}
.cp-btn-soft:hover { background: rgba(255,255,255,.1); }
.cp-btn-primary {
    background: linear-gradient(135deg,#25c889,#0c7c5b); color: #fff;
    border-color: rgba(52,230,154,.35); font-size: .92rem;
    padding: 14px 20px; border-radius: 13px; min-height: 52px;
    box-shadow: 0 6px 24px rgba(52,230,154,.22), 0 2px 6px rgba(0,0,0,.3);
}
.cp-btn-primary:hover { box-shadow: 0 8px 30px rgba(52,230,154,.3), 0 2px 8px rgba(0,0,0,.3); }

/* ── 7-day chart ─────────────────────────────────────────────────── */
.chart-col { display: flex; flex-direction: column; align-items: center; gap: 3px; flex: 1; }
.chart-bar { width: 100%; border-radius: 3px 3px 0 0; min-height: 3px; background: rgba(148,163,184,.12); }
.chart-bar.today { background: linear-gradient(to top, #34e69a, #55d9ff); box-shadow: 0 0 8px rgba(52,230,154,.3); }
.chart-lbl { font-size: 8px; font-weight: 700; color: rgba(148,163,184,.4); }

/* ── Milestone timeline ──────────────────────────────────────────── */
.ms-row {
    display: flex; align-items: center; gap: 9px; padding: 5px 0;
    border-bottom: 1px solid rgba(148,163,184,.07); font-size: 11.5px;
}
.ms-row:last-child { border-bottom: none; }
.ms-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    border: 1.5px solid rgba(148,163,184,.25); background: transparent; transition: all .3s;
}
.ms-dot.done { background: var(--green); border-color: var(--green); box-shadow: 0 0 5px rgba(52,230,154,.5); }
.ms-dot.next { background: var(--amber); border-color: var(--amber); animation: msPulse 1.6s ease-in-out infinite; }

/* ── Central empty-state overlay ────────────────────────────────── */
#map-empty-state {
    position: absolute;
    top: 52px;
    left: 14px;
    right: calc(14px + 420px + 14px); /* clear right panel */
    bottom: 0;
    z-index: 6;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.map-empty-card {
    pointer-events: auto;
    background: rgba(4,10,22,.84);
    border: 1px solid rgba(148,163,184,.18);
    border-radius: 20px;
    padding: 28px 30px 24px;
    text-align: center;
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    box-shadow: 0 20px 60px rgba(0,0,0,.5), 0 0 0 1px rgba(52,230,154,.06);
    max-width: 340px;
    width: 100%;
    animation: fadeIn .5s ease both;
}
/* Animated radar */
.mec-radar {
    position: relative; width: 72px; height: 72px; margin: 0 auto 16px;
}
.mec-ring {
    position: absolute; border-radius: 50%;
    border: 1.5px solid rgba(52,230,154,.22);
    animation: radarPulse 3s ease-out infinite;
}
.mec-ring.r1 { inset: 0;    animation-delay: 0s; }
.mec-ring.r2 { inset: 12px; animation-delay: 1s; border-color: rgba(52,230,154,.35); }
.mec-ring.r3 { inset: 22px; animation-delay: 2s; border-color: rgba(52,230,154,.5); }
.mec-dot {
    position: absolute; width: 14px; height: 14px; border-radius: 50%;
    background: rgba(52,230,154,.2); border: 2px solid rgba(52,230,154,.7);
    top: 50%; left: 50%; transform: translate(-50%,-50%);
    box-shadow: 0 0 14px rgba(52,230,154,.6);
    animation: mecDotPulse 2s ease-in-out infinite;
}
@keyframes radarPulse {
    0%   { transform: scale(.8); opacity: .8; }
    60%  { transform: scale(1.15); opacity: .2; }
    100% { transform: scale(1.25); opacity: 0; }
}
@keyframes mecDotPulse {
    0%,100% { box-shadow: 0 0 8px rgba(52,230,154,.5); }
    50%     { box-shadow: 0 0 20px rgba(52,230,154,.9); }
}
.mec-title {
    font-size: 1rem; font-weight: 900; color: #e8f4ff; margin: 0 0 5px; letter-spacing: -.01em;
}
.mec-sub {
    font-size: .78rem; color: var(--muted); margin: 0 0 14px; line-height: 1.5;
}
.mec-pills {
    display: flex; flex-direction: column; gap: 5px;
}
.mec-pill {
    display: inline-block; padding: 5px 11px; border-radius: 999px;
    font-size: .68rem; font-weight: 800; letter-spacing: .04em;
}
.mec-pill-green {
    background: rgba(52,230,154,.1); color: #b0f0d5;
    border: 1px solid rgba(52,230,154,.28);
}
.mec-pill-muted {
    background: rgba(148,163,184,.07); color: var(--muted);
    border: 1px solid rgba(148,163,184,.14);
}

@media (max-width: 860px) {
    #map-empty-state {
        left: 12px; right: 12px; top: 52px; bottom: 56px;
    }
    .map-empty-card { max-width: 280px; padding: 20px 18px 18px; }
}

/* ── Mobile collapse tab (only on mobile) ────────────────────────── */
.cp-mobile-tab { display: none; }
@media (max-width: 860px) {
    .cp-mobile-tab {
        display: block; padding: 10px 20px 4px; cursor: pointer; user-select: none; flex-shrink: 0;
    }
    .cockpit-panel {
        transition: max-height .32s cubic-bezier(.4,0,.2,1);
        overflow: hidden;
    }
    .cockpit-panel.cp-collapsed { max-height: 54px !important; }
    .cockpit-panel.cp-collapsed .panel-scroll { overflow: hidden; }
}

/* ── Animations ──────────────────────────────────────────────────── */
@keyframes msPulse { 0%,100%{box-shadow:0 0 5px rgba(245,189,84,.4)} 50%{box-shadow:0 0 12px rgba(245,189,84,.8)} }
@keyframes onlinePulse { 0%,100%{transform:scale(1);opacity:.7} 50%{transform:scale(1.4);opacity:0} }
@keyframes slideUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.anim-up { animation: slideUp .35s ease both; }
.anim-up-2 { animation: slideUp .35s .07s ease both; }
.anim-up-3 { animation: slideUp .35s .14s ease both; }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; transition: none !important; }
}

/* ── Mobile ──────────────────────────────────────────────────────── */
@media (max-width: 860px) {
    .left-panel { display: none; }
    .cockpit-panel {
        top: auto; left: 0; right: 0; bottom: 56px; width: 100%;
        border-radius: 22px 22px 0 0; max-height: 72dvh;
        box-shadow: 0 -8px 40px rgba(0,0,0,.5);
    }
    .dash-nav { display: none; }
    .dash-user-name { display: none; }
    .map-badge-zone { display: none; }
}
@media (max-width: 480px) {
    .cockpit-panel { max-height: 80dvh; bottom: 52px; }
}
</style>

{{-- ─────────── Slim overlay top bar ─────────────────────────────── --}}
<div class="dash-topbar">
    <a class="dash-brand" href="{{ route('worker.dashboard') }}">
        <span class="dash-mark">BKB</span>
        <div>
            <div class="dash-brand-name">BiKuBe</div>
            <div class="dash-brand-sub">Cockpit</div>
        </div>
    </a>
    <nav class="dash-nav">
        <a href="{{ route('worker.dashboard') }}" class="active">Dashboard</a>
        <a href="{{ route('worker.orders.index') }}">Assignments</a>
        <a href="{{ route('worker.wallet.index') }}">Finances</a>
        <a href="{{ route('worker.notifications.index') }}">Notifications</a>
        <a href="{{ route('worker.support.index') }}">Support</a>
    </nav>
    <div class="dash-user">
        <span class="dash-avatar">{{ $initial }}</span>
        <span class="dash-user-name">{{ $firstName }}</span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:.72rem;padding:4px 8px;border-radius:6px;font-family:inherit" title="Log out">↩</button>
        </form>
    </div>
</div>

{{-- ─────────── Map background ────────────────────────────────────── --}}
<div id="worker-map"></div>
<div id="worker-map-overlay"></div>

{{-- ─────────── Central empty-state map overlay ───────────────────── --}}
@if(!$activeOrder)
<div id="map-empty-state">
    <div class="map-empty-card">
        <div class="mec-radar">
            <div class="mec-ring r1"></div>
            <div class="mec-ring r2"></div>
            <div class="mec-ring r3"></div>
            <div class="mec-dot"></div>
        </div>
        <p class="mec-title">Waiting for dispatch</p>
        <p class="mec-sub">No real order assigned yet</p>
        <div class="mec-pills">
            <span class="mec-pill mec-pill-green">Pilot coverage: Narvik + Ballangen</span>
            <span class="mec-pill mec-pill-muted">GPS marker appears only after browser consent</span>
            <span class="mec-pill mec-pill-muted">No live route until real assignment</span>
        </div>
    </div>
</div>
@endif


{{-- ─────────── Left info panel ──────────────────────────────────── --}}
<div class="left-panel" style="top:118px">

    {{-- Worker card --}}
    <div class="gc anim-up">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,rgba(52,230,154,.25),rgba(85,217,255,.15));border:1.5px solid rgba(52,230,154,.3);display:grid;place-items:center;font-size:.9rem;font-weight:950;color:var(--green);flex-shrink:0">{{ $initial }}</div>
            <div style="min-width:0">
                <div style="font-weight:850;font-size:.84rem;color:#e8f4ff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $firstName }}</div>
                <div style="font-size:.65rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">{{ $workerType }}</div>
            </div>
        </div>
        <div x-data="{ isOn: {{ $isOnline ? 'true' : 'false' }} }"
             x-init="window.addEventListener('bkb:status', function(e){ isOn = e.detail.isOn; })">
            <span class="s-badge" :class="isOn ? 's-online' : 's-offline'">
                <span class="s-dot" :style="isOn ? 'background:var(--green);box-shadow:0 0 6px rgba(52,230,154,.7)' : 'background:var(--danger)'"></span>
                <span x-text="isOn ? 'Online' : 'Offline'">{{ $isOnline ? 'Online' : 'Offline' }}</span>
            </span>
        </div>
    </div>

    {{-- Readiness checklist --}}
    <div class="gc anim-up-2">
        <p style="font-size:.62rem;font-weight:900;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin:0 0 8px">Worker readiness</p>

        {{-- Profile --}}
        <div class="check-row">
            <span class="check-icon {{ $profileOk ? 'ci-ok' : 'ci-warn' }}">{{ $profileOk ? '✓' : '!' }}</span>
            <div>
                <div style="color:{{ $profileOk ? '#d0fce8' : 'var(--amber)' }};font-weight:700">Profile</div>
                <div style="color:var(--muted);font-size:.67rem">{{ $profileOk ? 'Approved' : 'Not approved' }}</div>
            </div>
        </div>

        {{-- Payout --}}
        <div class="check-row">
            <span class="check-icon {{ $payoutReady ? 'ci-ok' : 'ci-warn' }}">{{ $payoutReady ? '✓' : '!' }}</span>
            <div>
                <div style="color:{{ $payoutReady ? '#d0fce8' : 'var(--amber)' }};font-weight:700">Payout profile</div>
                <div style="color:var(--muted);font-size:.67rem">
                    @if($payoutReady) Ready
                    @elseif(!empty($payoutBlockers)) {{ count($payoutBlockers) }} blocker(s)
                    @else Draft — not submitted
                    @endif
                </div>
            </div>
        </div>

        {{-- GPS --}}
        <div class="check-row" id="gps-check-row">
            <span class="check-icon ci-pend" id="gps-check-icon">○</span>
            <div>
                <div style="color:var(--muted);font-weight:700" id="gps-check-label">GPS permission</div>
                <div style="color:var(--muted);font-size:.67rem" id="gps-check-sub">Waiting for browser</div>
            </div>
        </div>

        {{-- Assignment --}}
        <div class="check-row">
            <span class="check-icon ci-pend">—</span>
            <div>
                <div style="color:var(--muted);font-weight:700">Assignment</div>
                <div style="color:var(--muted);font-size:.67rem">
                    @if($activeOrder) Active: #{{ $activeOrder->order_number }}
                    @else No assignment yet
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Today's stats --}}
    <div class="gc anim-up-3">
        <p style="font-size:.62rem;font-weight:900;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin:0 0 8px">Today</p>
        <div style="display:flex;gap:10px">
            <div style="flex:1;background:rgba(255,255,255,.03);border-radius:9px;padding:9px 10px;border:1px solid rgba(148,163,184,.07)">
                <div style="font-size:1.3rem;font-weight:950;color:#fff">{{ number_format($todayEarnings, 0) }}</div>
                <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">kr earned</div>
            </div>
            <div style="flex:1;background:rgba(255,255,255,.03);border-radius:9px;padding:9px 10px;border:1px solid rgba(148,163,184,.07)">
                <div style="font-size:1.3rem;font-weight:950;color:#fff">{{ $todayOrders }}</div>
                <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">orders</div>
            </div>
        </div>
        @if($todayEarnings == 0 && $todayOrders == 0)
        <p style="color:var(--muted);font-size:.68rem;margin:.6rem 0 0;line-height:1.4">No completed orders yet — earnings show when Dispatch assigns real tasks.</p>
        @endif
    </div>

    {{-- Quick links --}}
    <div style="display:flex;flex-direction:column;gap:5px">
        <a href="{{ route('worker.orders.index') }}" class="gc" style="display:flex;align-items:center;gap:8px;text-decoration:none;padding:9px 12px;font-size:.78rem;font-weight:750;color:#d0e8f8;transition:background .15s">
            <span style="opacity:.65">📋</span> Assignments
        </a>
        <a href="{{ route('worker.payout-profile.show') }}" class="gc" style="display:flex;align-items:center;gap:8px;text-decoration:none;padding:9px 12px;font-size:.78rem;font-weight:750;color:#d0e8f8;transition:background .15s">
            <span style="opacity:.65">💳</span> Payout profile
        </a>
        <a href="{{ route('worker.support.index') }}" class="gc" style="display:flex;align-items:center;gap:8px;text-decoration:none;padding:9px 12px;font-size:.78rem;font-weight:750;color:#d0e8f8;transition:background .15s">
            <span style="opacity:.65">🛟</span> Support
        </a>
    </div>

</div>

{{-- ─────────── Right cockpit panel ───────────────────────────────── --}}
<div class="cockpit-panel" x-data="cockpitApp" :class="panelOpen ? '' : 'cp-collapsed'">

    {{-- Mobile collapse/expand tab ─────────────────────── --}}
    <div class="cp-mobile-tab" @click="panelOpen = !panelOpen">
        <div style="width:36px;height:4px;border-radius:2px;background:rgba(148,163,184,.25);margin:0 auto 7px"></div>
        <div style="display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:.78rem;font-weight:850;color:#c8daf0;letter-spacing:.01em">Partner Cockpit</span>
            <span :style="isOn ? 'color:var(--green)' : 'color:var(--muted)'"
                  style="font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em"
                  x-text="panelOpen ? '▼ collapse' : '▲ expand'">▼ collapse</span>
        </div>
    </div>

    <div class="panel-scroll">

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px">
            <div>
                <p style="color:var(--green);font-size:.62rem;font-weight:900;letter-spacing:.1em;text-transform:uppercase;margin:0 0 3px">Partner cockpit</p>
                <h2 style="font-size:1.5rem;font-weight:950;color:#fff;margin:0;line-height:1.1;letter-spacing:-.01em">Hei, {{ $firstName }}</h2>
                <p style="color:var(--muted);font-size:.74rem;margin:.25rem 0 0">BiKuBe {{ $workerType }} · Narvik</p>
            </div>
            <span class="s-badge" :class="isOn ? 's-online' : 's-offline'" style="margin-top:3px">
                <span class="s-dot" :style="isOn ? 'background:var(--green);box-shadow:0 0 7px rgba(52,230,154,.8)' : 'background:var(--danger)'"></span>
                <span x-text="isOn ? 'Online' : 'Offline'">{{ $isOnline ? 'Online' : 'Offline' }}</span>
            </span>
        </div>

        {{-- Error banner --}}
        <div x-show="locationError" x-cloak
             style="background:rgba(251,113,133,.07);border:1px solid rgba(251,113,133,.2);border-radius:11px;padding:9px 13px;color:var(--danger);font-size:.78rem;margin-bottom:12px;line-height:1.5"
             x-text="locationError"></div>

        {{-- ════ OFFLINE STATE ════ --}}
        <div x-show="!isOn" style="display:grid;gap:12px">

            {{-- Hero offline card --}}
            <div style="border-radius:16px;border:1px solid rgba(148,163,184,.12);background:linear-gradient(145deg,rgba(10,22,42,.9),rgba(5,13,28,.95));padding:22px 18px;text-align:center;position:relative;overflow:hidden">
                {{-- decorative bg glow --}}
                <div style="position:absolute;top:-30px;left:50%;transform:translateX(-50%);width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(52,230,154,.08),transparent 70%);pointer-events:none"></div>

                {{-- Moon icon --}}
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.12);display:grid;place-items:center;font-size:1.5rem;margin:0 auto 12px;position:relative">
                    🌙
                </div>
                <h3 style="font-size:1.05rem;font-weight:950;color:#fff;margin:0 0 6px">Du er offline</h3>
                <p style="color:var(--muted);font-size:.8rem;margin:0;max-width:250px;margin:0 auto;line-height:1.55">
                    Gå online for å motta oppdrag fra Narvik dispatch.
                </p>

                {{-- Honest note --}}
                <div style="margin-top:14px;padding:9px 12px;border-radius:9px;background:rgba(148,163,184,.05);border:1px solid rgba(148,163,184,.1)">
                    <p style="color:var(--muted);font-size:.71rem;margin:0;line-height:1.5;text-align:left">
                        <strong style="color:#9ab5cc">No assignment is shown</strong> because Dispatch has not assigned a real order to this account yet. GPS sharing starts only after you explicitly enable it.
                    </p>
                </div>
            </div>

            {{-- Swipe to go online --}}
            <div>
                <div class="swipe-track"
                     @pointerdown.prevent="startSwipe($event)"
                     @pointermove.window.passive="moveSwipe($event)"
                     @pointerup.window="endSwipe()"
                     @pointercancel.window="isDragging=false;resetSwipe()">
                    <div class="swipe-fill" :style="{width: swipePct+'%'}"></div>
                    <span class="swipe-lbl swipe-lbl-l">Offline</span>
                    <span class="swipe-lbl swipe-lbl-r">Online</span>
                    <div class="swipe-center" x-text="swipeLabel()"></div>
                    <div class="swipe-knob" :style="{transform:'translateX('+thumbPx+'px)'}">
                        <span x-show="loading === 'online'" style="font-size:16px;animation:none">⏳</span>
                        <span x-show="loading !== 'online'">⟶</span>
                    </div>
                </div>
                <p style="color:var(--muted);font-size:.7rem;text-align:center;margin:.6rem 0 0;line-height:1.4">
                    GPS position is requested <em>only</em> when you swipe — never before consent.
                </p>
            </div>

            {{-- Readiness summary inside panel (abridged) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div class="gc" style="padding:11px 12px">
                    <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px">Profile</div>
                    <div style="font-size:.82rem;font-weight:800;color:{{ $profileOk ? 'var(--green)' : 'var(--amber)' }}">{{ $profileOk ? '✓ Approved' : '⚠ Pending' }}</div>
                </div>
                <div class="gc" style="padding:11px 12px">
                    <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px">Payout</div>
                    <div style="font-size:.82rem;font-weight:800;color:{{ $payoutReady ? 'var(--green)' : 'var(--amber)' }}">{{ $payoutReady ? '✓ Ready' : '⚠ Draft' }}</div>
                </div>
            </div>

            @if($lastPingFmt)
            <p style="color:var(--muted);font-size:.72rem;text-align:center;margin:0">Last location shared {{ $lastPingFmt }}</p>
            @endif
        </div>

        {{-- ════ ONLINE STATE ════ --}}
        <div x-show="isOn" x-cloak style="display:grid;gap:12px">

            @if($activeOrder)
            {{-- ── Active order card ── --}}
            @php
                $intake  = $activeOrder->metadata['intake'] ?? [];
                $pickup  = $intake['pickup_address'] ?? $intake['vehicle_location'] ?? $intake['task_location'] ?? null;
                $dropoff = $intake['dropoff_address'] ?? $intake['destination_address'] ?? null;
                $events  = $activeOrder->events->pluck('event_type')->toArray();
                $milestones = [
                    'worker.accepted'        => 'Accepted',
                    'worker.started'         => 'Started',
                    'worker.arrived_pickup'  => 'Arrived at pickup',
                    'worker.picked_up'       => 'Confirmed pickup',
                    'worker.arrived_dropoff' => 'Arrived at drop-off',
                    'worker.completed'       => 'Completed',
                ];
                $lastDoneIdx = -1;
                foreach (array_values(array_keys($milestones)) as $i => $ev) {
                    if (in_array($ev, $events)) $lastDoneIdx = $i;
                }
            @endphp
            <div class="gc gc-green">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                    <span style="font-size:.62rem;font-weight:900;color:var(--green);text-transform:uppercase;letter-spacing:.09em">Active assignment</span>
                    <span style="font-size:.7rem;color:var(--muted)">#{{ $activeOrder->order_number }}</span>
                </div>
                <div style="margin-bottom:12px">
                    @foreach($milestones as $i => $label)
                    @php
                        $idx  = array_search($i, array_keys($milestones));
                        $done = in_array($i, $events);
                        $next = ($idx === $lastDoneIdx + 1);
                    @endphp
                    <div class="ms-row">
                        <span class="ms-dot {{ $done ? 'done' : ($next ? 'next' : '') }}"></span>
                        <span style="color:{{ $done ? '#c8f7e4' : ($next ? 'var(--amber)' : 'var(--muted)') }};font-weight:{{ ($done || $next) ? '700' : '500' }}">{{ $label }}</span>
                        @if($done)<span style="margin-left:auto;color:rgba(52,230,154,.6);font-size:10px">✓</span>@endif
                        @if($next && !$done)<span style="margin-left:auto;font-size:9px;color:var(--amber)">NEXT</span>@endif
                    </div>
                    @endforeach
                </div>
                @if($pickup)
                <div style="background:rgba(255,255,255,.04);border-radius:9px;padding:8px 10px;margin-bottom:7px;border:1px solid rgba(148,163,184,.08)">
                    <p style="color:var(--muted);font-size:.62rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px">Pickup</p>
                    <p style="margin:0;font-size:.8rem;color:#d4ecff;line-height:1.4">{{ $pickup }}</p>
                </div>
                @endif
                @if($dropoff)
                <div style="background:rgba(255,255,255,.04);border-radius:9px;padding:8px 10px;margin-bottom:10px;border:1px solid rgba(148,163,184,.08)">
                    <p style="color:var(--muted);font-size:.62rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px">Drop-off</p>
                    <p style="margin:0;font-size:.8rem;color:#d4ecff;line-height:1.4">{{ $dropoff }}</p>
                </div>
                @endif
                {{-- Navigation button --}}
                <button type="button" @click="openNav()"
                    class="cp-btn"
                    style="margin-bottom:8px;background:{{ $navDest ? 'rgba(85,217,255,.1)' : 'rgba(148,163,184,.05)' }};color:{{ $navDest ? '#a0e0ff' : 'var(--muted)' }};border-color:{{ $navDest ? 'rgba(85,217,255,.3)' : 'rgba(148,163,184,.15)' }};font-size:.84rem;gap:8px"
                    @if(!$navDest) disabled @endif
                    title="{{ $navDest ? 'Open navigation to '.$navLabel : 'No pickup/dropoff address in this assignment yet' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    @if($navDest)
                        Open navigation → {{ $navLabel }}
                    @else
                        Navigation available after address assigned
                    @endif
                </button>
                <a href="{{ route('worker.orders.show', $activeOrder) }}" class="cp-btn cp-btn-primary">
                    Open assignment details →
                </a>
            </div>

            @else
            {{-- No assignment — dispatch standby --}}
            <div class="gc" style="text-align:center;padding:22px 16px;border-style:dashed;border-color:rgba(52,230,154,.18);position:relative;overflow:hidden">
                {{-- animated radar rings --}}
                <div style="position:relative;width:64px;height:64px;margin:0 auto 12px">
                    <div style="position:absolute;inset:0;border-radius:50%;border:1.5px solid rgba(52,230,154,.12);animation:onlinePulse 2.4s ease-out infinite"></div>
                    <div style="position:absolute;inset:12px;border-radius:50%;border:1.5px solid rgba(52,230,154,.2);animation:onlinePulse 2.4s .8s ease-out infinite"></div>
                    <div style="position:absolute;inset:22px;border-radius:50%;background:rgba(52,230,154,.15);border:2px solid rgba(52,230,154,.4);display:grid;place-items:center">
                        <div style="width:10px;height:10px;border-radius:50%;background:var(--green);box-shadow:0 0 10px rgba(52,230,154,.8)"></div>
                    </div>
                </div>
                <p style="color:#e2f7f0;font-size:.9rem;font-weight:850;margin:0 0 5px">Dispatch standby</p>
                <p style="color:var(--muted);font-size:.76rem;margin:0;line-height:1.5">You are online. Dispatch has not assigned a task yet. Assignments will appear here when ready.</p>
                <a href="{{ route('worker.orders.index') }}" style="display:inline-block;margin-top:10px;color:var(--green);font-size:.76rem;font-weight:750;text-decoration:none">View assignment queue →</a>
                {{-- Navigation disabled state --}}
                <div style="margin-top:12px;padding:8px 11px;border-radius:9px;background:rgba(148,163,184,.05);border:1px solid rgba(148,163,184,.1);display:flex;align-items:center;gap:7px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="rgba(148,163,184,.5)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    <p style="color:rgba(143,165,189,.65);font-size:.68rem;margin:0;line-height:1.4;text-align:left">Navigation available after Dispatch assigns an order with pickup/dropoff address.</p>
                </div>
            </div>
            @endif

            {{-- GPS card --}}
            <div class="gc" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
                <div style="min-width:0">
                    <p style="color:var(--muted);font-size:.62rem;font-weight:900;text-transform:uppercase;letter-spacing:.07em;margin:0 0 2px">Live GPS</p>
                    <p x-text="gpsLabel()" style="font-size:.82rem;font-weight:750;color:#d4ecff;margin:0">Ready</p>
                    <p x-show="lastPingText" x-text="lastPingText" x-cloak style="font-size:.68rem;color:var(--muted);margin:2px 0 0"></p>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0">
                    <button x-show="!gpsWatching" @click="startGps()" class="cp-btn cp-btn-soft" style="white-space:nowrap">▶ Start GPS</button>
                    <button x-show="gpsWatching" @click="stopGps()" class="cp-btn cp-btn-soft" x-cloak style="white-space:nowrap;color:var(--danger)">■ Stop</button>
                </div>
            </div>

            {{-- Go offline --}}
            <button type="button" class="cp-btn cp-btn-offline" @click="goOffline()" :disabled="loading === 'offline'">
                <span x-show="loading !== 'offline'">Gå offline</span>
                <span x-show="loading === 'offline'" x-cloak>Disconnecting…</span>
            </button>
        </div>

        {{-- ════ EARNINGS (always) ════ --}}
        <div class="gc" style="margin-top:14px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                <h3 style="font-size:.72rem;font-weight:900;color:var(--muted);margin:0;text-transform:uppercase;letter-spacing:.07em">7-day earnings</h3>
                <a href="{{ route('worker.wallet.index') }}" style="font-size:.7rem;color:var(--green);font-weight:750;text-decoration:none">View ledger →</a>
            </div>
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:14px">
                <span style="font-size:2rem;font-weight:950;color:#fff;letter-spacing:-.02em;line-height:1">{{ number_format($todayEarnings, 0, '.', ' ') }}</span>
                <span style="font-size:.82rem;color:var(--muted)">kr today</span>
                @if($todayOrders > 0)
                <span style="font-size:.76rem;font-weight:750;color:var(--green);margin-left:auto">+{{ $todayOrders }} {{ Str::plural('order', $todayOrders) }}</span>
                @else
                <span style="font-size:.72rem;color:var(--muted);margin-left:auto">no orders yet</span>
                @endif
            </div>
            <div style="display:flex;align-items:flex-end;gap:4px;height:44px">
                @foreach($chart as $date => $val)
                @php $pct = max(7, (int) round(($val / $chartMax) * 100)); @endphp
                <div class="chart-col">
                    <div class="chart-bar {{ $date === $todayKey ? 'today' : '' }}" style="height:{{ $pct }}%"></div>
                    <span class="chart-lbl">{{ \Carbon\Carbon::parse($date)->format('D')[0] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Payout ready banner --}}
        @if($readyAmount > 0)
        <div style="margin-top:10px;border-radius:12px;border:1px solid rgba(52,230,154,.22);background:rgba(52,230,154,.05);padding:11px 14px;display:flex;justify-content:space-between;align-items:center">
            <div>
                <p style="color:var(--muted);font-size:.68rem;margin:0;text-transform:uppercase;letter-spacing:.05em">Ready for payout</p>
                <p style="color:var(--green);font-size:1.05rem;font-weight:850;margin:.1rem 0 0">{{ number_format($readyAmount, 2) }} NOK</p>
            </div>
            <a href="{{ route('worker.wallet.index') }}" class="cp-btn cp-btn-soft">View →</a>
        </div>
        @endif

    </div>
</div>

{{-- ─────────── Navigation picker bottom sheet ────────────────────── --}}
<div x-data="navSheet">
    {{-- Backdrop --}}
    <div x-show="open"
         x-transition.opacity.duration.200ms
         class="nav-backdrop" @click.self="close()" x-cloak></div>

    {{-- Sheet / modal --}}
    <div x-show="open"
         x-transition:enter.duration.250ms
         x-transition:leave.duration.180ms
         class="nav-sheet" x-cloak>

        <div class="nav-handle"></div>

        {{-- Destination heading --}}
        <p class="nav-sheet-title">Navigation</p>
        <h3 style="font-size:1rem;font-weight:950;color:#fff;margin:0 0 4px" x-text="'Navigate to ' + label"></h3>
        <p class="nav-sheet-dest" x-text="dest"></p>

        {{-- Recommended --}}
        <p class="nav-section-label">Recommended</p>
        <template x-for="app in apps.filter(a => a.rec)" :key="app.id">
            <button type="button"
                    @click="choice = app.id"
                    :class="choice === app.id ? 'nav-app-row selected' : 'nav-app-row'">
                <div class="nav-app-icon" :style="'background:' + app.bg">
                    <span x-text="app.icon"></span>
                </div>
                <div>
                    <span class="nav-app-name" x-text="app.name"></span>
                    <span class="nav-app-sub"  x-text="app.sub"></span>
                </div>
                <div x-show="choice === app.id" class="nav-app-check">✓</div>
                <div x-show="choice !== app.id" class="nav-badge-rec">DEFAULT</div>
            </button>
        </template>

        {{-- Other options --}}
        <p class="nav-section-label" style="margin-top:14px">Other options</p>
        <template x-for="app in apps.filter(a => !a.rec)" :key="app.id">
            <button type="button"
                    @click="choice = app.id"
                    :class="choice === app.id ? 'nav-app-row selected' : 'nav-app-row'">
                <div class="nav-app-icon" :style="'background:' + app.bg">
                    <span x-text="app.icon"></span>
                </div>
                <div>
                    <span class="nav-app-name" x-text="app.name"></span>
                    <span class="nav-app-sub"  x-text="app.sub"></span>
                </div>
                <div x-show="choice === app.id" class="nav-app-check">✓</div>
            </button>
        </template>

        {{-- Remember choice --}}
        <label class="nav-remember">
            <input type="checkbox" x-model="remember">
            Remember my choice
        </label>

        {{-- Launch CTA --}}
        <button type="button" class="nav-launch-btn" @click="launch()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
            <span>Open in </span><span x-text="apps.find(a=>a.id===choice)?.name || 'navigation app'"></span>
        </button>

        <p class="nav-disclaimer">
            Opens the external app on your device. BiKuBe does not track your in-app route.
            No GPS data is sent to external apps by BiKuBe.
        </p>
    </div>
</div>

{{-- ─────────── Scripts ────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
/* ── GPS permission check for left panel ─────────────────────────── */
if (navigator.permissions) {
    navigator.permissions.query({ name: 'geolocation' }).then(function (p) {
        var icon = document.getElementById('gps-check-icon');
        var lbl  = document.getElementById('gps-check-label');
        var sub  = document.getElementById('gps-check-sub');
        function upd(s) {
            if (!icon) return;
            if (s === 'granted') {
                icon.className = 'check-icon ci-ok'; icon.textContent = '✓';
                if (lbl) lbl.style.color = '#d0fce8';
                if (sub) sub.textContent = 'Allowed';
            } else if (s === 'denied') {
                icon.className = 'check-icon ci-warn'; icon.textContent = '!';
                if (sub) sub.textContent = 'Denied — check browser settings';
            } else {
                icon.className = 'check-icon ci-pend'; icon.textContent = '○';
                if (sub) sub.textContent = 'Not yet requested';
            }
        }
        upd(p.state);
        p.onchange = function () { upd(p.state); };
    }).catch(function () {});
}

/* ── Alpine cockpit component ─────────────────────────────────────── */
document.addEventListener('alpine:init', function () {
    Alpine.data('cockpitApp', function () {
        return {
            isOn: {{ $isOnline ? 'true' : 'false' }},
            swipePct: 0, thumbPx: 0,
            isDragging: false, startX: 0, maxPx: 0,
            loading: null, locationError: '',
            gpsWatching: false, gpsWatcherId: null,
            lastPingText: '', lastSentMs: 0,
            map: null, marker: null, circle: null, zoneCircle: null,
            pingIntervalMs: {{ (int)($pingSeconds * 1000) }},
            maxAccuracy: {{ (int)$maxAccuracy }},
            navDest: @json($navDest ?? null),
            navLabel: @json($navLabel ?? 'Destination'),
            panelOpen: true,

            init() {
                this.$nextTick(() => this.initMap());
            },

            initMap() {
                var el = document.getElementById('worker-map');
                if (!el || typeof L === 'undefined') return;

                /* Zoom 14 — street-level detail, Narvik city clearly visible */
                this.map = L.map(el, {
                    zoomControl: false, attributionControl: false
                }).setView([{{ $centerLat }}, {{ $centerLng }}], 14);

                /* CartoDB Voyager — good contrast, all labels visible, no API key */
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20, subdomains: 'abcd', crossOrigin: true
                }).addTo(this.map);

                /* Zoom control bottom-left, clear of right panel */
                L.control.zoom({ position: 'bottomleft' }).addTo(this.map);

                /* ── Narvik pilot zone ─ three concentric rings for visual depth ── */
                /* Outer reference ring — 8 km — faint */
                L.circle([{{ $centerLat }}, {{ $centerLng }}], {
                    radius: 8000,
                    color: '#34e69a', weight: 0.8, opacity: 0.35,
                    fillColor: '#34e69a', fillOpacity: 0.015,
                    dashArray: '4 10', interactive: false
                }).addTo(this.map);

                /* Main coverage ring — 5 km */
                this.zoneCircle = L.circle([{{ $centerLat }}, {{ $centerLng }}], {
                    radius: 5000,
                    color: '#34e69a', weight: 2, opacity: 0.7,
                    fillColor: '#34e69a', fillOpacity: 0.06,
                    dashArray: '7 5', interactive: false
                }).addTo(this.map);

                /* Inner dispatch radius — 2.5 km — brighter */
                L.circle([{{ $centerLat }}, {{ $centerLng }}], {
                    radius: 2500,
                    color: '#34e69a', weight: 1.5, opacity: 0.55,
                    fillColor: '#34e69a', fillOpacity: 0.04,
                    dashArray: '4 7', interactive: false
                }).addTo(this.map);

                /* ── Ballangen coverage zone — secondary ── */
                L.circle([68.3357, 17.0293], {
                    radius: 4000,
                    color: '#55d9ff', weight: 1.8, opacity: 0.65,
                    fillColor: '#55d9ff', fillOpacity: 0.05,
                    dashArray: '6 5', interactive: false
                }).addTo(this.map);

                /* ── Zone label markers (divIcon pills, permanent) ── */
                var _lbl = function(text, color, bg, border) {
                    return L.divIcon({
                        className: '',
                        html: '<div style="background:' + bg + ';border:1px solid ' + border + ';border-radius:999px;padding:4px 12px;color:' + color + ';font:700 10px/1.2 Inter,system-ui,sans-serif;letter-spacing:.06em;text-transform:uppercase;white-space:nowrap;box-shadow:0 2px 14px rgba(0,0,0,.35);pointer-events:none">' + text + '</div>',
                        iconAnchor: [0, 0]
                    });
                };

                /* Narvik label — below zone centre */
                L.marker([68.4200, 17.4272], { icon: _lbl('Narvik Pilot Zone', '#c8f7e4', 'rgba(4,10,22,.88)', 'rgba(52,230,154,.45)'), interactive: false }).addTo(this.map);

                /* Dispatch standby label — above zone centre */
                L.marker([68.4550, 17.4272], { icon: _lbl('Dispatch Standby', '#d4ecff', 'rgba(4,10,22,.82)', 'rgba(148,163,184,.3)'), interactive: false }).addTo(this.map);

                /* Pilot coverage disclaimer — positioned visibly */
                L.marker([68.4385, 17.5600], { icon: _lbl('Pilot coverage only — no live route', '#8fa5bd', 'rgba(4,10,22,.78)', 'rgba(148,163,184,.2)'), interactive: false }).addTo(this.map);

                /* Ballangen label */
                L.marker([68.3357, 17.0293], { icon: _lbl('Ballangen coverage', '#b0d4f5', 'rgba(4,10,22,.88)', 'rgba(85,217,255,.4)'), interactive: false }).addTo(this.map);

                /* ── Narvik dispatch-centre dot (zone anchor, NOT a courier) ── */
                L.circleMarker([{{ $centerLat }}, {{ $centerLng }}], {
                    radius: 6,
                    color: '#fff', weight: 2,
                    fillColor: '#34e69a', fillOpacity: 1,
                    interactive: false
                }).addTo(this.map);

                /* Ballangen centre dot */
                L.circleMarker([68.3357, 17.0293], {
                    radius: 5,
                    color: '#fff', weight: 1.5,
                    fillColor: '#55d9ff', fillOpacity: 1,
                    interactive: false
                }).addTo(this.map);

                /* ── Courier GPS marker — only shown after real browser GPS consent ── */
                var wIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:16px;height:16px;background:#34e69a;border:3px solid #fff;border-radius:50%;box-shadow:0 0 14px rgba(52,230,154,.95),0 0 0 6px rgba(52,230,154,.15)"></div>',
                    iconSize: [16,16], iconAnchor: [8,8]
                });
                this.marker = L.marker([{{ $centerLat }}, {{ $centerLng }}], { icon: wIcon });
                this.circle = L.circle([{{ $centerLat }}, {{ $centerLng }}], {
                    radius: 80, color: '#34e69a', fillColor: '#34e69a', fillOpacity: 0.15, weight: 1, interactive: false
                });

                this.map.invalidateSize();
            },

            updateMap(lat, lng, acc) {
                if (!this.map) return;
                var ll = [lat, lng];
                if (!this.map.hasLayer(this.marker)) {
                    this.marker.addTo(this.map);
                    this.circle.addTo(this.map);
                }
                this.marker.setLatLng(ll);
                this.circle.setLatLng(ll).setRadius(Math.min(acc, 500));
                this.map.flyTo(ll, 15, { animate: true, duration: 1.2 });
                var dot = document.getElementById('gps-dot');
                var txt = document.getElementById('gps-text');
                if (dot) { dot.style.background='var(--green)'; dot.style.boxShadow='0 0 8px rgba(52,230,154,.7)'; }
                if (txt) txt.textContent = 'GPS LIVE';
            },

            gpsLabel() {
                if (!this.isOn) return 'Go online to enable';
                if (this.gpsWatching) return 'Live tracking active';
                return 'Consent given — tap to share';
            },

            swipeLabel() {
                if (this.loading === 'online') return 'Connecting…';
                if (this.isDragging) return 'Release to go online';
                return 'Swipe → to go online';
            },

            startSwipe(e) {
                if (this.isOn || this.loading) return;
                this.isDragging = true;
                this.startX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                this.maxPx  = Math.max((e.currentTarget.offsetWidth || 400) - 64, 1);
                e.currentTarget.setPointerCapture && e.currentTarget.setPointerCapture(e.pointerId);
            },
            moveSwipe(e) {
                if (!this.isDragging) return;
                var x = e.clientX || (e.touches ? e.touches[0].clientX : this.startX);
                var off = Math.max(0, Math.min(x - this.startX, this.maxPx));
                this.thumbPx = off; this.swipePct = (off / this.maxPx) * 100;
            },
            endSwipe() {
                if (!this.isDragging) return;
                this.isDragging = false;
                if (this.swipePct >= 85) { this.thumbPx = this.maxPx; this.swipePct = 100; this.goOnline(); }
                else this.resetSwipe();
            },
            resetSwipe() { this.thumbPx = 0; this.swipePct = 0; },

            goOnline() {
                var self = this;
                self.loading = 'online';
                self.locationError = '';
                if (!navigator.geolocation) {
                    self.locationError = 'Geolocation not supported.';
                    self.loading = null; self.resetSwipe(); return;
                }
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        self.updateMap(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
                        self._postOnline(true);
                    },
                    function (err) {
                        self.locationError = 'GPS denied: ' + err.message + '. Going online without location.';
                        self._postOnline(false);
                    },
                    { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                );
            },
            _postOnline(hadGps) {
                var self = this;
                fetch('{{ route('worker.presence.online') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
                }).finally(function () {
                    self.isOn = true; self.loading = null;
                    window.dispatchEvent(new CustomEvent('bkb:status', { detail: { isOn: true } }));
                });
            },

            goOffline() {
                var self = this; self.loading = 'offline'; self.stopGps();
                fetch('{{ route('worker.presence.offline') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
                }).finally(function () {
                    self.isOn = false; self.loading = null;
                    window.dispatchEvent(new CustomEvent('bkb:status', { detail: { isOn: false } }));
                    if (self.map && self.marker && self.map.hasLayer(self.marker)) {
                        self.map.removeLayer(self.marker); self.map.removeLayer(self.circle);
                    }
                });
            },

            startGps() {
                var self = this;
                if (self.gpsWatching || !navigator.geolocation) return;
                self.gpsWatching = true;
                self.gpsWatcherId = navigator.geolocation.watchPosition(
                    function (pos) { self.handlePosition(pos); },
                    function (err) { self.locationError = 'GPS error: ' + err.message; self.gpsWatching = false; self.gpsWatcherId = null; },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 }
                );
                document.addEventListener('visibilitychange', function () { if (document.hidden) self.stopGps(); });
            },

            stopGps() {
                if (this.gpsWatcherId !== null) { navigator.geolocation.clearWatch(this.gpsWatcherId); this.gpsWatcherId = null; }
                this.gpsWatching = false;
            },

            handlePosition(pos) {
                var lat = pos.coords.latitude, lng = pos.coords.longitude, acc = pos.coords.accuracy;
                var now = Date.now();
                if (acc > this.maxAccuracy) {
                    this.locationError = 'GPS accuracy too low (' + Math.round(acc) + ' m). Enable precise location.'; return;
                }
                this.locationError = '';
                this.updateMap(lat, lng, acc);
                if ((now - this.lastSentMs) < this.pingIntervalMs) return;
                this.lastSentMs = now;
                this.lastPingText = 'Last ping: just now — ' + Math.round(acc) + ' m';
                fetch('{{ route('worker.location-pings.store') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        latitude: lat, longitude: lng, accuracy_meters: acc,
                        heading: pos.coords.heading, speed_mps: pos.coords.speed,
                        captured_at: new Date(pos.timestamp).toISOString(), consent: true
                    })
                }).catch(function () {});
            },

            openNav() {
                if (!this.navDest) return;
                window.dispatchEvent(new CustomEvent('bkb:nav-open', {
                    detail: { dest: this.navDest, label: this.navLabel }
                }));
            }
        };
    });

    /* ── Navigation picker sheet ──────────────────────────────────── */
    Alpine.data('navSheet', function () {
        return {
            open: false,
            dest: '',
            label: 'Destination',
            choice: localStorage.getItem('bkb_nav_default') || 'google',
            remember: true,

            apps: [
                {
                    id: 'google', name: 'Google Maps',
                    sub: 'Recommended for Norway — works on any device',
                    icon: '🗺', bg: 'rgba(66,133,244,.15)', rec: true
                },
                {
                    id: 'apple', name: 'Apple Maps',
                    sub: 'Best on iPhone, iPad, and macOS',
                    icon: '🍎', bg: 'rgba(255,255,255,.07)', rec: false
                },
                {
                    id: 'waze', name: 'Waze',
                    sub: 'Driver-optimised, real-time traffic',
                    icon: '🚗', bg: 'rgba(54,195,127,.15)', rec: false
                },
                {
                    id: 'here', name: 'HERE WeGo',
                    sub: 'Offline maps — works without mobile data',
                    icon: '📡', bg: 'rgba(0,177,227,.12)', rec: false
                }
            ],

            init() {
                var self = this;
                window.addEventListener('bkb:nav-open', function (e) {
                    self.dest  = e.detail.dest  || '';
                    self.label = e.detail.label || 'Destination';
                    self.open  = true;
                });
            },

            buildUrl(app) {
                if (!this.dest) return '#';
                var enc = encodeURIComponent(this.dest);
                if (app === 'google') return 'https://www.google.com/maps/dir/?api=1&destination=' + enc + '&travelmode=driving';
                if (app === 'waze')   return 'https://waze.com/ul?q=' + enc + '&navigate=yes&utm_source=bikube';
                if (app === 'apple')  return 'http://maps.apple.com/?daddr=' + enc + '&dirflg=d';
                if (app === 'here')   return 'https://wego.here.com/directions/mix//' + enc;
                return '#';
            },

            launch() {
                if (!this.dest) return;
                if (this.remember) localStorage.setItem('bkb_nav_default', this.choice);
                window.open(this.buildUrl(this.choice), '_blank', 'noopener,noreferrer');
                this.open = false;
            },

            close() { this.open = false; }
        };
    });
});
</script>

{{-- Tooltip style --}}
<style>
.bkb-tip { background: rgba(4,10,22,.92) !important; color: #d0e8f8 !important; border: 1px solid rgba(52,230,154,.3) !important; font-size: 11px !important; font-weight: 700 !important; letter-spacing: .05em !important; padding: 4px 10px !important; border-radius: 6px !important; box-shadow: none !important; }
.bkb-tip::before { display: none !important; }
.leaflet-control-zoom a { background: rgba(4,10,22,.88) !important; color: #d0e8f8 !important; border-color: rgba(148,163,184,.18) !important; }

/* ── Navigation picker — bottom sheet (mobile) / centered modal (desktop) ── */
.nav-backdrop {
    position: fixed; inset: 0; z-index: 200;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
}
.nav-sheet {
    position: fixed; z-index: 201;
    bottom: 0; left: 0; right: 0;
    background: rgba(6,16,32,.98);
    border-radius: 22px 22px 0 0;
    border: 1px solid rgba(148,163,184,.16);
    border-bottom: none;
    box-shadow: 0 -24px 80px rgba(0,0,0,.6);
    padding: 12px 20px 36px;
    max-height: 92dvh;
    overflow-y: auto;
}
.nav-handle {
    width: 40px; height: 4px; border-radius: 2px;
    background: rgba(148,163,184,.25); margin: 0 auto 20px;
}
.nav-sheet-title {
    font-size: .6rem; font-weight: 900; color: var(--green);
    text-transform: uppercase; letter-spacing: .1em; margin: 0 0 3px;
}
.nav-sheet-dest {
    font-size: .82rem; color: var(--muted); margin: 0 0 18px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.nav-section-label {
    font-size: .58rem; font-weight: 900; color: rgba(148,163,184,.5);
    text-transform: uppercase; letter-spacing: .1em;
    margin: 0 0 7px; padding: 0 2px;
}
.nav-app-row {
    display: flex; align-items: center; gap: 12px;
    width: 100%; padding: 11px 13px; border-radius: 13px;
    background: rgba(255,255,255,.04); border: 1.5px solid rgba(148,163,184,.1);
    text-align: left; cursor: pointer; font-family: inherit;
    transition: background .12s, border-color .12s;
    margin-bottom: 7px;
}
.nav-app-row:hover { background: rgba(255,255,255,.07); }
.nav-app-row.selected {
    background: rgba(52,230,154,.07);
    border-color: rgba(52,230,154,.3);
}
.nav-app-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: grid; place-items: center; flex-shrink: 0;
    font-size: 17px;
}
.nav-app-name { font-size: .88rem; font-weight: 750; color: #e2ecf8; display: block; }
.nav-app-sub  { font-size: .68rem; color: var(--muted); display: block; margin-top: 1px; }
.nav-app-check {
    margin-left: auto; flex-shrink: 0;
    width: 20px; height: 20px; border-radius: 50%;
    background: rgba(52,230,154,.15); border: 1.5px solid rgba(52,230,154,.45);
    display: grid; place-items: center;
    color: var(--green); font-size: 10px; font-weight: 900;
}
.nav-badge-rec {
    margin-left: auto; flex-shrink: 0;
    padding: 2px 7px; border-radius: 999px;
    background: rgba(52,230,154,.1); border: 1px solid rgba(52,230,154,.25);
    color: var(--green); font-size: 9px; font-weight: 900;
    text-transform: uppercase; letter-spacing: .06em;
}
.nav-remember {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 2px; margin-bottom: 14px;
    font-size: .76rem; color: var(--muted); cursor: pointer;
}
.nav-remember input[type=checkbox] {
    width: 16px; height: 16px; accent-color: var(--green); cursor: pointer;
}
.nav-launch-btn {
    width: 100%; padding: 15px; border-radius: 14px; cursor: pointer;
    background: linear-gradient(135deg,#25c889,#0c7c5b);
    color: #fff; border: 1px solid rgba(52,230,154,.35);
    font-weight: 850; font-size: .95rem; font-family: inherit;
    box-shadow: 0 6px 24px rgba(52,230,154,.25);
    transition: opacity .15s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.nav-launch-btn:active { transform: scale(.98); opacity: .9; }
.nav-disclaimer {
    font-size: .68rem; color: rgba(148,163,184,.5);
    text-align: center; margin-top: 10px; line-height: 1.5;
}

/* Desktop: centered modal instead of bottom sheet */
@media (min-width: 861px) {
    .nav-backdrop { display: flex; align-items: center; justify-content: center; }
    .nav-sheet {
        position: static;
        width: 380px; border-radius: 20px;
        border: 1px solid rgba(148,163,184,.18); border-bottom: revert;
        padding: 18px 24px 28px;
        max-height: 80dvh;
    }
    .nav-handle { display: none; }
}
</style>
@endsection
