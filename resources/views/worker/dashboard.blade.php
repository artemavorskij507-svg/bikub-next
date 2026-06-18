@extends('worker.layout')
@section('title', 'Cockpit')
@section('content')

@php
$firstName   = explode(' ', $user->name)[0] ?? 'Worker';
$workerType  = str($user->workerProfile?->worker_type ?? 'courier')->title();
$entries     = $earnings['entries'] ?? collect();
$readyAmount = (float) ($earnings['ready_amount'] ?? 0);
$paidAmount  = (float) ($earnings['paid_amount']  ?? 0);
$todayOrders = $entries->filter(fn($e) => $e->created_at?->isToday())->count();

$centerLat  = $mapConfig['center_lat'];
$centerLng  = $mapConfig['center_lng'];
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

$lastPingAge  = $lastPing ? now()->diffInSeconds($lastPing->created_at) : null;
$lastPingFmt  = $lastPing ? $lastPing->created_at->diffForHumans() : null;
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

<style>
/* Override layout padding for full-bleed map */
.worker-content { padding: 0 !important; overflow: hidden !important; position: relative; height: 100%; }

/* Map fills entire content area */
#worker-map {
    position: absolute; inset: 0; z-index: 0;
    background: #071828;
}

/* GPS badge — top-left overlay */
.gps-badge {
    position: absolute; top: 14px; left: 14px; z-index: 5;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 13px; border-radius: 999px;
    background: rgba(7,17,32,.9); border: 1px solid rgba(148,163,184,.2);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    font-size: 11px; font-weight: 900; letter-spacing: .07em; text-transform: uppercase; color: #f3f7fb;
    transition: all .3s;
}
.gps-dot {
    width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; transition: background .3s, box-shadow .3s;
}

/* Cockpit panel — right side */
.cockpit-panel {
    position: absolute; z-index: 10;
    top: 14px; bottom: 14px; right: 14px;
    width: 396px;
    display: flex; flex-direction: column;
    border-radius: 20px;
    background: rgba(7,16,32,.94);
    border: 1px solid rgba(148,163,184,.14);
    box-shadow: 0 24px 70px rgba(0,0,0,.55);
    backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
    overflow: hidden;
}
.panel-scroll {
    flex: 1; overflow-y: auto; padding: 18px 18px 24px;
    scrollbar-width: thin; scrollbar-color: rgba(148,163,184,.15) transparent;
}
.panel-scroll::-webkit-scrollbar { width: 4px; }
.panel-scroll::-webkit-scrollbar-track { background: transparent; }
.panel-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,.2); border-radius: 2px; }

/* Cards inside panel */
.cp-card {
    background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px; padding: 14px;
}
.cp-card + .cp-card, .cp-card + div, div + .cp-card { margin-top: 10px; }

/* Status badge */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 999px; font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .04em; flex-shrink: 0;
    transition: all .3s;
}
.status-online  { background: rgba(52,230,154,.12); color: var(--green); border: 1px solid rgba(52,230,154,.28); }
.status-offline { background: rgba(251,113,133,.1);  color: var(--danger); border: 1px solid rgba(251,113,133,.25); }

/* Swipe control */
.swipe-track {
    position: relative; height: 62px; border-radius: 999px;
    background: rgba(5,12,24,.96); border: 1px solid rgba(148,163,184,.18);
    overflow: hidden; user-select: none; touch-action: none; cursor: grab;
}
.swipe-fill {
    position: absolute; inset: 0 auto 0 0; border-radius: 999px; min-width: 0;
    background: linear-gradient(90deg, rgba(37,99,235,.6) 0%, rgba(52,230,154,.8) 100%);
}
.swipe-lbl {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 2;
    font-size: 10px; font-weight: 900; letter-spacing: .07em; text-transform: uppercase; pointer-events: none;
}
.swipe-lbl-l { left: 72px; color: rgba(226,232,240,.4); }
.swipe-lbl-r { right: 16px; color: rgba(167,243,208,.7); }
.swipe-center {
    position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: rgba(255,255,255,.75); font-size: 12px; pointer-events: none;
    z-index: 2; padding: 0 72px; text-align: center;
}
.swipe-knob {
    position: absolute; top: 7px; left: 7px; width: 48px; height: 48px;
    background: #fff; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; color: #0f172a; font-size: 18px; z-index: 3;
    box-shadow: 0 4px 14px rgba(0,0,0,.28); transition: transform .08s;
}

/* Action buttons */
.cp-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 12px 18px; border-radius: 12px; cursor: pointer;
    font-weight: 700; font-size: 13px; border: 1px solid transparent; text-decoration: none;
    transition: opacity .15s;
}
.cp-btn:disabled { opacity: .45; cursor: not-allowed; }
.cp-btn-offline { background: rgba(251,113,133,.08); color: var(--danger); border-color: rgba(251,113,133,.25); }
.cp-btn-soft {
    background: rgba(255,255,255,.07); color: var(--text);
    border-color: rgba(148,163,184,.14); font-size: 12px;
    padding: 7px 13px; border-radius: 9px; width: auto;
}
.cp-btn-action {
    background: linear-gradient(135deg,#25c889,#0c7c5b);
    color: #fff; border-color: rgba(52,230,154,.3); font-size: .95rem;
    padding: 14px 20px; border-radius: 13px; min-height: 52px;
    box-shadow: 0 6px 20px rgba(52,230,154,.2);
}

/* Chart */
.chart-col { display: flex; flex-direction: column; align-items: center; gap: 4px; flex: 1; }
.chart-bar { width: 100%; border-radius: 3px 3px 0 0; min-height: 4px; background: rgba(148,163,184,.15); transition: height .4s; }
.chart-bar.active { background: var(--green); }
.chart-lbl { font-size: 9px; font-weight: 700; color: rgba(148,163,184,.45); }

/* Assignment active card */
.active-order-card {
    border: 1px solid rgba(52,230,154,.22); border-radius: 14px;
    background: linear-gradient(145deg, rgba(12,40,30,.92), rgba(5,18,12,.92));
    padding: 14px;
}
.milestone-row {
    display: flex; align-items: center; gap: 8px; padding: 6px 0;
    border-bottom: 1px solid rgba(148,163,184,.08); font-size: 12px;
}
.milestone-row:last-child { border-bottom: none; }
.milestone-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    border: 1.5px solid rgba(148,163,184,.3); background: transparent;
}
.milestone-dot.done { background: var(--green); border-color: var(--green); box-shadow: 0 0 6px rgba(52,230,154,.5); }
.milestone-dot.current { background: var(--amber); border-color: var(--amber); animation: mPulse 1.5s infinite; }
@keyframes mPulse { 0%,100%{box-shadow:0 0 6px rgba(245,189,84,.4)} 50%{box-shadow:0 0 14px rgba(245,189,84,.8)} }
@keyframes dotPulse { 0%,100%{opacity:1} 50%{opacity:.35} }
@media (prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; } }

@media (max-width: 860px) {
    .cockpit-panel { top: auto; left: 0; right: 0; bottom: 0; width: 100%;
        border-radius: 20px 20px 0 0; max-height: 78dvh; }
    .gps-badge { top: 10px; left: 10px; }
}
</style>

{{-- GPS badge --}}
<div class="gps-badge" id="gps-badge">
    <span class="gps-dot" id="gps-dot" style="{{ $isOnline ? 'background:var(--green);box-shadow:0 0 8px rgba(52,230,154,.65)' : 'background:var(--danger)' }}"></span>
    <span id="gps-text">{{ $isOnline ? 'GPS READY' : 'OFFLINE' }}</span>
</div>

{{-- Leaflet map --}}
<div id="worker-map"></div>

{{-- Cockpit panel --}}
<div class="cockpit-panel" x-data="cockpitApp">

    <div class="panel-scroll">

        {{-- ── Header ── --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px">
            <div>
                <h2 style="font-size:1.45rem;font-weight:950;color:#fff;margin:0;line-height:1.1">Hei, {{ $firstName }}</h2>
                <p style="color:var(--muted);font-size:.75rem;margin:.3rem 0 0">BiKuBe {{ $workerType }} Cockpit</p>
            </div>
            <span class="status-badge" :class="isOn ? 'status-online' : 'status-offline'">
                <span class="gps-dot" :style="isOn
                    ? 'background:var(--green);box-shadow:0 0 8px rgba(52,230,154,.7)'
                    : 'background:var(--danger)'"></span>
                <span x-text="isOn ? 'Online' : 'Offline'">{{ $isOnline ? 'Online' : 'Offline' }}</span>
            </span>
        </div>

        {{-- Error / blockers --}}
        <div x-show="locationError" x-cloak
             style="background:rgba(251,113,133,.07);border:1px solid rgba(251,113,133,.22);border-radius:11px;padding:9px 13px;color:var(--danger);font-size:.78rem;margin-bottom:10px"
             x-text="locationError"></div>

        {{-- ── OFFLINE STATE ── --}}
        <div x-show="!isOn" style="display:grid;gap:10px">

            {{-- Offline hero --}}
            <div class="cp-card" style="text-align:center;padding:22px 14px;border-style:dashed;border-color:rgba(148,163,184,.18)">
                <div style="width:60px;height:60px;border-radius:50%;background:rgba(148,163,184,.08);display:grid;place-items:center;font-size:1.7rem;margin:0 auto 10px">🌙</div>
                <h3 style="font-size:1rem;font-weight:900;color:#fff;margin:0 0 5px">Du er offline</h3>
                <p style="color:var(--muted);font-size:.8rem;margin:0 auto;max-width:220px;line-height:1.5">Gå online for å motta oppdrag og starte GPS-deling.</p>
            </div>

            {{-- Swipe to go online --}}
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
                    <span x-show="loading==='online'" style="font-size:14px">⏳</span>
                    <span x-show="loading!=='online'">⟶</span>
                </div>
            </div>

            @if($lastPingFmt)
            <p style="color:var(--muted);font-size:.74rem;text-align:center;margin:0">
                Last location recorded {{ $lastPingFmt }}
            </p>
            @endif
        </div>

        {{-- ── ONLINE STATE ── --}}
        <div x-show="isOn" x-cloak style="display:grid;gap:10px">

            @if($activeOrder)
            {{-- Active assignment card --}}
            @php
                $intake   = $activeOrder->metadata['intake'] ?? [];
                $pickup   = $intake['pickup_address'] ?? $intake['vehicle_location'] ?? $intake['task_location'] ?? null;
                $dropoff  = $intake['dropoff_address'] ?? $intake['destination_address'] ?? null;
                $aStatus  = $activeOrder->status->value;
                $events   = $activeOrder->events->pluck('event_type')->toArray();
                $milestones = [
                    'worker.accepted'      => 'Accepted',
                    'worker.started'       => 'Started',
                    'worker.arrived_pickup'  => 'Arrived pickup',
                    'worker.picked_up'     => 'Picked up',
                    'worker.arrived_dropoff' => 'Arrived dropoff',
                    'worker.completed'     => 'Completed',
                ];
                $currentMilestone = null;
                foreach (array_reverse(array_keys($milestones)) as $ev) {
                    if (in_array($ev, $events)) { $currentMilestone = $ev; break; }
                }
            @endphp
            <div class="active-order-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                    <span style="font-size:.68rem;font-weight:900;color:var(--green);text-transform:uppercase;letter-spacing:.08em">Active assignment</span>
                    <span style="font-size:.72rem;color:var(--muted)">#{{ $activeOrder->order_number }}</span>
                </div>

                {{-- Milestone timeline --}}
                <div style="margin-bottom:11px">
                    @foreach($milestones as $ev => $label)
                    @php
                        $done = in_array($ev, $events);
                        $curr = !$done && ($ev === array_keys($milestones)[array_search($currentMilestone, array_keys($milestones)) + 1] ?? null);
                    @endphp
                    <div class="milestone-row">
                        <span class="milestone-dot {{ $done ? 'done' : ($curr ? 'current' : '') }}"></span>
                        <span style="color:{{ $done ? '#d0fce8' : 'var(--muted)' }};font-weight:{{ $done ? '700' : '500' }}">{{ $label }}</span>
                        @if($done)<span style="margin-left:auto;color:rgba(52,230,154,.6);font-size:11px">✓</span>@endif
                    </div>
                    @endforeach
                </div>

                @if($pickup)
                <div style="background:rgba(148,163,184,.06);border-radius:9px;padding:8px 10px;margin-bottom:8px">
                    <p style="color:var(--muted);font-size:.65rem;font-weight:900;text-transform:uppercase;margin:0 0 2px">Pickup</p>
                    <p style="margin:0;font-size:.8rem;color:#d0e8f8">{{ $pickup }}</p>
                </div>
                @endif
                @if($dropoff)
                <div style="background:rgba(148,163,184,.06);border-radius:9px;padding:8px 10px;margin-bottom:8px">
                    <p style="color:var(--muted);font-size:.65rem;font-weight:900;text-transform:uppercase;margin:0 0 2px">Drop-off</p>
                    <p style="margin:0;font-size:.8rem;color:#d0e8f8">{{ $dropoff }}</p>
                </div>
                @endif

                <a href="{{ route('worker.orders.show', $activeOrder) }}" class="cp-btn cp-btn-action" style="margin-top:4px">
                    Open assignment details →
                </a>
            </div>

            @else
            {{-- No active assignment --}}
            <div class="cp-card" style="text-align:center;padding:20px 14px;border-style:dashed;border-color:rgba(52,230,154,.16)">
                <div style="width:32px;height:32px;border-radius:50%;background:rgba(52,230,154,.1);display:grid;place-items:center;margin:0 auto 9px">
                    <div style="width:10px;height:10px;border-radius:50%;background:var(--green);animation:dotPulse 2.2s infinite"></div>
                </div>
                <p style="color:var(--text);font-size:.84rem;margin:0 0 4px;font-weight:700">Waiting for assignment</p>
                <p style="color:var(--muted);font-size:.76rem;margin:0">Dispatch has not assigned a task yet.</p>
                <a href="{{ route('worker.orders.index') }}" style="display:inline-block;margin-top:9px;color:var(--green);font-size:.76rem;font-weight:700">View all orders →</a>
            </div>
            @endif

            {{-- GPS status card --}}
            <div class="cp-card" style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <p style="color:var(--muted);font-size:.7rem;font-weight:900;text-transform:uppercase;margin:0 0 3px">Live GPS</p>
                    <p x-text="gpsLabel()" style="font-size:.82rem;font-weight:700;color:#d0e8f8;margin:0">Loading…</p>
                    <p x-show="lastPingText" x-text="'Last ping: '+lastPingText" x-cloak
                       style="font-size:.7rem;color:var(--muted);margin:2px 0 0"></p>
                </div>
                <button x-show="!gpsWatching" @click="startGps()" class="cp-btn cp-btn-soft" style="width:auto">Start GPS</button>
                <button x-show="gpsWatching" @click="stopGps()"  class="cp-btn cp-btn-soft" x-cloak style="width:auto;color:var(--danger)">Stop GPS</button>
            </div>

            {{-- Go offline --}}
            <button type="button" class="cp-btn cp-btn-offline" @click="goOffline()" :disabled="loading==='offline'">
                <span x-show="loading!=='offline'">Gå offline</span>
                <span x-show="loading==='offline'" x-cloak>Disconnecting…</span>
            </button>
        </div>

        {{-- ── Earnings (always visible) ── --}}
        <div class="cp-card" style="margin-top:12px">
            <h3 style="font-size:.74rem;font-weight:900;color:var(--muted);margin:0 0 3px;text-transform:uppercase;letter-spacing:.06em">Today's earnings</h3>
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px">
                <span style="font-size:1.85rem;font-weight:950;color:#fff;letter-spacing:-.02em">{{ number_format($todayEarnings, 0, '.', ' ') }} kr</span>
                @if($todayOrders > 0)
                <span style="font-size:.78rem;font-weight:700;color:var(--green)">+{{ $todayOrders }} {{ Str::plural('order', $todayOrders) }}</span>
                @else
                <span style="font-size:.78rem;color:var(--muted)">No completed orders today</span>
                @endif
            </div>
            {{-- 7-day chart --}}
            <div style="display:flex;align-items:flex-end;gap:3px;height:46px">
                @foreach($chart as $date => $val)
                @php $pct = max(8, (int)round(($val / $chartMax) * 100)); @endphp
                <div class="chart-col">
                    <div class="chart-bar {{ $date === $todayKey ? 'active' : '' }}" style="height:{{ $pct }}%"></div>
                    <span class="chart-lbl">{{ \Carbon\Carbon::parse($date)->format('D')[0] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Ready for payout --}}
        @if($readyAmount > 0)
        <div style="margin-top:10px;border-radius:12px;border:1px solid rgba(52,230,154,.2);background:rgba(52,230,154,.05);padding:11px 13px;display:flex;justify-content:space-between;align-items:center">
            <div>
                <p style="color:var(--muted);font-size:.72rem;margin:0">Ready for payout</p>
                <p style="color:var(--green);font-size:1.05rem;font-weight:850;margin:.1rem 0 0">{{ number_format($readyAmount, 2) }} NOK</p>
            </div>
            <a href="{{ route('worker.wallet.index') }}" class="cp-btn cp-btn-soft">View →</a>
        </div>
        @endif

        {{-- Quick links --}}
        <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap">
            <a href="{{ route('worker.orders.index') }}" class="cp-btn cp-btn-soft" style="flex:1">📋 Assignments</a>
            <a href="{{ route('worker.support.index') }}" class="cp-btn cp-btn-soft" style="flex:1">🛟 Support</a>
        </div>

    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('cockpitApp', function () {
        return {
            isOn: {{ $isOnline ? 'true' : 'false' }},
            swipePct: 0, thumbPx: 0,
            isDragging: false, startX: 0, maxPx: 0,
            loading: null,
            locationError: '',
            gpsWatching: false, gpsWatcherId: null,
            lastPingText: '',
            lastSentMs: 0,
            map: null, marker: null, circle: null,
            pingIntervalMs: {{ $pingSeconds * 1000 }},
            maxAccuracy: {{ $maxAccuracy }},
            staleMs: {{ $staleSeconds * 1000 }},

            init() {
                this.$nextTick(() => this.initMap());
            },

            initMap() {
                var el = document.getElementById('worker-map');
                if (!el || typeof L === 'undefined') return;
                this.map = L.map(el, { zoomControl: false, attributionControl: false })
                    .setView([{{ $centerLat }}, {{ $centerLng }}], {{ $defaultZoom }});
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20, subdomains: 'abcd', crossOrigin: true
                }).addTo(this.map);
                L.control.zoom({ position: 'bottomleft' }).addTo(this.map);
                var icon = L.divIcon({
                    className: '',
                    html: '<div style="width:14px;height:14px;background:var(--green,#34e69a);border:3px solid white;border-radius:50%;box-shadow:0 0 12px rgba(52,230,154,.8)"></div>',
                    iconSize: [14, 14], iconAnchor: [7, 7]
                });
                this.marker = L.marker([{{ $centerLat }}, {{ $centerLng }}], { icon: icon });
                this.circle = L.circle([{{ $centerLat }}, {{ $centerLng }}], {
                    radius: 500, color: '#34e69a', fillColor: '#34e69a', fillOpacity: 0.07, weight: 1
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
                this.circle.setLatLng(ll).setRadius(acc);
                this.map.flyTo(ll, 15, { animate: true, duration: 1.5 });
                var dot = document.getElementById('gps-dot');
                var txt = document.getElementById('gps-text');
                if (dot) { dot.style.background = 'var(--green)'; dot.style.boxShadow = '0 0 8px rgba(52,230,154,.7)'; }
                if (txt) txt.textContent = 'GPS LIVE';
            },

            gpsLabel() {
                if (!this.isOn) return 'Go online to enable';
                if (this.gpsWatching) return 'Live tracking active';
                return 'Ready — tap to start';
            },

            swipeLabel() {
                if (this.loading === 'online') return 'Connecting…';
                if (this.isDragging) return 'Release to go online';
                return 'Swipe to go online →';
            },

            startSwipe(e) {
                if (this.isOn || this.loading) return;
                this.isDragging = true;
                this.startX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
                this.maxPx = Math.max((e.currentTarget.offsetWidth || 380) - 62, 1);
                e.currentTarget.setPointerCapture && e.currentTarget.setPointerCapture(e.pointerId);
            },
            moveSwipe(e) {
                if (!this.isDragging) return;
                var x = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : this.startX);
                var off = Math.max(0, Math.min(x - this.startX, this.maxPx));
                this.thumbPx = off;
                this.swipePct = (off / this.maxPx) * 100;
            },
            endSwipe() {
                if (!this.isDragging) return;
                this.isDragging = false;
                if (this.swipePct >= 85) {
                    this.thumbPx = this.maxPx;
                    this.swipePct = 100;
                    this.goOnline();
                } else {
                    this.resetSwipe();
                }
            },
            resetSwipe() {
                this.thumbPx = 0;
                this.swipePct = 0;
            },

            goOnline() {
                var self = this;
                self.loading = 'online';
                self.locationError = '';
                if (!navigator.geolocation) {
                    self.locationError = 'Geolocation is not supported by this browser.';
                    self.loading = null;
                    self.resetSwipe();
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        var lat = pos.coords.latitude;
                        var lng = pos.coords.longitude;
                        var acc = pos.coords.accuracy;
                        self.updateMap(lat, lng, acc);
                        fetch('{{ route('worker.presence.online') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        }).finally(function () {
                            self.isOn = true;
                            self.loading = null;
                            var dot = document.getElementById('gps-dot');
                            var txt = document.getElementById('gps-text');
                            if (dot) { dot.style.background = 'var(--green)'; dot.style.boxShadow = '0 0 8px rgba(52,230,154,.7)'; }
                            if (txt) txt.textContent = 'GPS READY';
                        });
                    },
                    function (err) {
                        self.locationError = 'GPS denied: ' + err.message + '. Going online without GPS.';
                        fetch('{{ route('worker.presence.online') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        }).finally(function () {
                            self.isOn = true;
                            self.loading = null;
                        });
                    },
                    { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                );
            },

            goOffline() {
                var self = this;
                self.loading = 'offline';
                self.stopGps();
                fetch('{{ route('worker.presence.offline') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).finally(function () {
                    self.isOn = false;
                    self.loading = null;
                    var dot = document.getElementById('gps-dot');
                    var txt = document.getElementById('gps-text');
                    if (dot) { dot.style.background = 'var(--danger)'; dot.style.boxShadow = 'none'; }
                    if (txt) txt.textContent = 'OFFLINE';
                    if (self.map && self.marker && self.map.hasLayer(self.marker)) {
                        self.map.removeLayer(self.marker);
                        self.map.removeLayer(self.circle);
                    }
                });
            },

            startGps() {
                var self = this;
                if (self.gpsWatching || !navigator.geolocation) return;
                self.gpsWatching = true;
                self.gpsWatcherId = navigator.geolocation.watchPosition(
                    function (pos) { self.handlePosition(pos); },
                    function (err) {
                        self.locationError = 'GPS error: ' + err.message;
                        self.gpsWatching = false;
                        self.gpsWatcherId = null;
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 }
                );
                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) self.stopGps();
                });
            },

            stopGps() {
                if (this.gpsWatcherId !== null) {
                    navigator.geolocation.clearWatch(this.gpsWatcherId);
                    this.gpsWatcherId = null;
                }
                this.gpsWatching = false;
            },

            handlePosition(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                var acc = pos.coords.accuracy;
                var now = Date.now();

                if (acc > this.maxAccuracy) {
                    this.locationError = 'GPS accuracy too low (' + Math.round(acc) + ' m). Enable precise location.';
                    return;
                }
                this.locationError = '';
                this.updateMap(lat, lng, acc);

                if ((now - this.lastSentMs) < this.pingIntervalMs) return;
                this.lastSentMs = now;
                this.lastPingText = 'just now';

                fetch('{{ route('worker.location-pings.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                        accuracy_meters: acc,
                        heading: pos.coords.heading,
                        speed_mps: pos.coords.speed,
                        captured_at: new Date(pos.timestamp).toISOString(),
                        consent: true
                    })
                }).then(function (r) { return r.json(); })
                  .then(function (d) {
                      if (d.recorded) {
                          var self = Alpine.$data(document.querySelector('[x-data="cockpitApp"]'));
                          if (self) self.lastPingText = 'just now — ' + Math.round(acc) + ' m accuracy';
                      }
                  })
                  .catch(function () {});
            }
        };
    });
});
</script>
@endsection
