@extends('worker.layout')
@section('title', 'Schedule')
@section('content')
<style>
.cockpit-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,330px);gap:1rem;align-items:start}
.main-stack,.side-stack{display:grid;gap:.75rem}
.panel{border:1px solid var(--line);border-radius:14px;background:var(--panel)}
.panel-inner{padding:1rem}
.panel-head{display:flex;justify-content:space-between;align-items:center;gap:.75rem;margin-bottom:.85rem}
.panel-title{font-size:.96rem;font-weight:950;margin:0}
.badge{display:inline-flex;align-items:center;border:1px solid var(--line);border-radius:999px;padding:.15rem .52rem;font-size:.68rem;font-weight:900;letter-spacing:.04em;color:var(--muted);background:rgba(148,163,184,.08)}
.badge-green{border-color:rgba(var(--brand-rgb),.32);background:rgba(var(--brand-rgb),.1);color:var(--green)}
.badge-blue{border-color:rgba(85,217,255,.28);background:rgba(85,217,255,.08);color:var(--blue)}
.badge-amber{border-color:rgba(245,189,84,.28);background:rgba(245,189,84,.08);color:var(--amber)}
.status-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .75rem;border-radius:999px;border:1px solid rgba(var(--brand-rgb),.28);background:rgba(var(--brand-rgb),.08);color:var(--green);font-size:.75rem;font-weight:900;white-space:nowrap}
.status-dot{width:.45rem;height:.45rem;border-radius:999px;background:var(--green);box-shadow:0 0 8px rgba(var(--brand-rgb),.6);flex-shrink:0}
.side-card{border:1px solid var(--line);border-radius:11px;background:var(--panel2);padding:.75rem}
.list-row{display:flex;justify-content:space-between;align-items:center;gap:.75rem;padding:.55rem 0;border-bottom:1px solid var(--line)}
.list-row:last-child{border-bottom:none}
.card-list{display:grid;gap:.55rem}
.item-card{display:grid;grid-template-columns:1fr auto;gap:.75rem;align-items:center;border:1px solid var(--line);border-radius:12px;background:var(--panel2);padding:.85rem}
.a-btn{display:inline-flex;align-items:center;justify-content:center;min-height:2.3rem;border:1px solid var(--line);border-radius:9px;background:rgba(14,30,49,.86);color:var(--text);padding:.45rem .8rem;font-weight:850;text-decoration:none;cursor:pointer;font-size:.82rem;white-space:nowrap}
.a-btn-primary{border-color:rgba(var(--brand-rgb),.48);background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff}
.a-btn-danger{border-color:rgba(251,113,133,.4);background:rgba(98,29,43,.6);color:var(--danger)}
.kpi-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem}
.kpi-box{border:1px solid var(--line);border-radius:12px;background:var(--panel);padding:.85rem}
.kpi-label{color:var(--muted);font-size:.68rem;font-weight:950;text-transform:uppercase;letter-spacing:.06em}
.kpi-value{font-size:1.65rem;font-weight:950;line-height:1;margin:.3rem 0 .2rem}
.kpi-note{color:var(--muted);font-size:.72rem}
.avail-btn{width:100%;text-align:left;border:1px solid var(--line);border-radius:12px;background:var(--panel2);padding:1rem;cursor:pointer;transition:border-color .2s}
.avail-btn:hover{border-color:rgba(var(--brand-rgb),.3)}
.avail-btn.is-on{border-color:rgba(var(--brand-rgb),.32);background:rgba(var(--brand-rgb),.06)}
.empty-state{display:grid;place-items:center;min-height:5rem;color:var(--muted);font-size:.85rem;text-align:center;padding:1.25rem}
@media(max-width:860px){.cockpit-grid{grid-template-columns:1fr}.kpi-row{grid-template-columns:repeat(2,1fr)}}
</style>

@php $avStatus = $availability?->status ?? 'offline'; $isOn = in_array($avStatus, ['online','available']); @endphp

<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Shift Control</p>
        <h1 style="margin:0;font-size:clamp(1.6rem,3vw,2.4rem);line-height:1">Schedule &amp; shifts</h1>
        <p class="muted" style="margin:.3rem 0 0">Availability and real shifts assigned by dispatcher.</p>
    </div>
    <div class="status-pill {{ $isOn ? '' : '' }}" style="{{ $isOn ? '' : 'border-color:rgba(148,163,184,.2);background:rgba(148,163,184,.06);color:var(--muted)' }}">
        <span class="status-dot" style="{{ $isOn ? '' : 'background:var(--muted);box-shadow:none' }}"></span>
        <span>{{ now()->format('d.m.Y') }}</span>
    </div>
</div>

<div x-data="{
    isOn: {{ $isOn ? 'true' : 'false' }},
    loading: null,
    async toggle(status) {
        this.loading = status;
        try {
            const route = status ? '{{ route('worker.presence.online') }}' : '{{ route('worker.presence.offline') }}';
            const r = await fetch(route, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            });
            if (r.ok || r.redirected) { window.location.reload(); }
        } catch (e) { console.error(e); } finally { this.loading = null; }
    }
}">

{{-- KPIs --}}
<div class="kpi-row" style="margin-bottom:1rem">
    <div class="kpi-box">
        <div class="kpi-label">Today</div>
        <div class="kpi-value">{{ $todayShifts->count() }}</div>
        <div class="kpi-note">shifts</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-label">Upcoming</div>
        <div class="kpi-value">{{ $upcomingShifts->count() }}</div>
        <div class="kpi-note">next shifts</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-label">History</div>
        <div class="kpi-value">{{ $pastShifts->count() }}</div>
        <div class="kpi-note">past shifts</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-label">Availability</div>
        <div class="kpi-value" style="color:{{ $isOn ? 'var(--green)' : 'var(--muted)' }};font-size:1rem" x-text="isOn ? 'Online' : 'Offline'"></div>
        <div class="kpi-note">current status</div>
    </div>
</div>

<div class="cockpit-grid">
    <div class="main-stack">
        {{-- Availability toggle --}}
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head">
                    <h2 class="panel-title">My availability</h2>
                    <span class="badge badge-blue">Real presence update</span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem">
                    <button type="button" class="avail-btn" :class="isOn && 'is-on'" @click="toggle(true)" :disabled="loading === 'on'">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem">
                            <div>
                                <div style="font-weight:950;font-size:1.05rem;color:var(--text)">Go online</div>
                                <div class="muted" style="font-size:.8rem;margin-top:.2rem" x-text="isOn ? 'Currently active' : 'Click to go online'"></div>
                            </div>
                            <span class="badge" :class="isOn ? 'badge-green' : 'badge-amber'" x-text="loading === 'on' ? '…' : (isOn ? 'on' : 'off')"></span>
                        </div>
                    </button>
                    <button type="button" class="avail-btn" :class="!isOn && 'is-on'" @click="toggle(false)" :disabled="loading === 'off'" style="--avail-color:var(--danger)">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem">
                            <div>
                                <div style="font-weight:950;font-size:1.05rem;color:var(--text)">Go offline</div>
                                <div class="muted" style="font-size:.8rem;margin-top:.2rem" x-text="!isOn ? 'Currently offline' : 'Click to go offline'"></div>
                            </div>
                            <span class="badge" :class="!isOn ? 'badge-green' : 'badge-amber'" x-text="loading === 'off' ? '…' : (!isOn ? 'on' : 'off')"></span>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        {{-- Today's shifts --}}
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Today's shifts</h2></div>
                @if($todayShifts->isEmpty())
                    <div class="empty-state">
                        <div>
                            <div style="font-size:1.6rem;margin-bottom:.4rem">📅</div>
                            No shifts scheduled for today. Shifts are assigned by dispatcher.
                        </div>
                    </div>
                @else
                    <div class="card-list">
                        @foreach($todayShifts as $shift)
                        <div class="item-card">
                            <div>
                                <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:.35rem">
                                    <span class="badge badge-green">{{ $shift->status ?? 'scheduled' }}</span>
                                </div>
                                <div style="font-weight:950;font-size:1.1rem;color:var(--text)">{{ $shift->start_at->format('H:i') }} – {{ $shift->end_at->format('H:i') }}</div>
                                <div class="muted" style="font-size:.82rem;margin-top:.2rem">📍 {{ $shift->zone->name ?? 'Zone not set' }}</div>
                            </div>
                            <span class="badge">Assigned by dispatcher</span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- GPS note --}}
        <div class="panel" style="border-color:rgba(245,189,84,.22)">
            <div class="panel-inner" style="padding:.85rem">
                <p style="margin:0;color:var(--amber);font-size:.82rem;font-weight:850">⚠ Important:</p>
                <p class="muted" style="margin:.3rem 0 0;font-size:.8rem">Go online only when physically ready. Location is requested separately per assignment. Toggling presence here does <em>not</em> share GPS data.</p>
            </div>
        </div>
    </div>

    <aside class="side-stack">
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Upcoming</h2></div>
                @if($upcomingShifts->isEmpty())
                    <div class="empty-state">No upcoming shifts.</div>
                @else
                    <div class="card-list">
                        @foreach($upcomingShifts->take(8) as $shift)
                        <div class="side-card">
                            <div style="font-weight:950;color:var(--text)">{{ $shift->start_at->format('d.m') }} · {{ $shift->start_at->format('H:i') }}–{{ $shift->end_at->format('H:i') }}</div>
                            <div class="muted" style="font-size:.78rem;margin-top:.2rem">{{ $shift->zone->name ?? 'Zone not set' }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">History</h2></div>
                @if($pastShifts->isEmpty())
                    <div class="empty-state">Shift history is empty.</div>
                @else
                    <div class="card-list">
                        @foreach($pastShifts->take(8) as $shift)
                        <div class="side-card">
                            <div style="font-weight:950;color:var(--text)">{{ $shift->start_at->format('d.m') }} · {{ $shift->start_at->format('H:i') }}–{{ $shift->end_at->format('H:i') }}</div>
                            <div class="muted" style="font-size:.78rem;margin-top:.2rem">{{ $shift->zone->name ?? '—' }} · completed</div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Status</h2></div>
                <div class="side-card">
                    <div class="list-row"><span class="muted">Presence</span><strong style="color:{{ $isOn ? 'var(--green)' : 'var(--muted)' }}" x-text="isOn ? 'Online' : 'Offline'"></strong></div>
                    <div class="list-row"><span class="muted">Today</span><strong style="color:var(--text)">{{ now()->format('l') }}</strong></div>
                    <div class="list-row"><span class="muted">Local time</span><strong style="color:var(--text)">{{ now()->format('H:i') }}</strong></div>
                    @if($availability?->updated_at)
                    <div class="list-row"><span class="muted">Last change</span><strong style="color:var(--text)">{{ $availability->updated_at->diffForHumans() }}</strong></div>
                    @endif
                </div>
            </div>
        </section>
    </aside>
</div>
</div>
@endsection
