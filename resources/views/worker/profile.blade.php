@extends('worker.layout')
@section('title', 'Profile')
@section('content')
<style>
.cockpit-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,330px);gap:1rem;align-items:start}
.main-stack,.side-stack{display:grid;gap:.75rem}
.panel{border:1px solid var(--line);border-radius:14px;background:var(--panel)}
.panel-inner{padding:1rem}
.panel-head{display:flex;justify-content:space-between;align-items:center;gap:.75rem;margin-bottom:.85rem}
.panel-title{font-size:.96rem;font-weight:950;margin:0}
.badge{display:inline-flex;align-items:center;border:1px solid var(--line);border-radius:999px;padding:.15rem .52rem;font-size:.68rem;font-weight:900;letter-spacing:.04em;color:var(--muted);background:rgba(148,163,184,.08)}
.badge-green{border-color:rgba(52,230,154,.32);background:rgba(52,230,154,.1);color:var(--green)}
.badge-blue{border-color:rgba(85,217,255,.28);background:rgba(85,217,255,.08);color:var(--blue)}
.badge-amber{border-color:rgba(245,189,84,.28);background:rgba(245,189,84,.08);color:var(--amber)}
.status-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .75rem;border-radius:999px;border:1px solid rgba(52,230,154,.28);background:rgba(52,230,154,.08);color:var(--green);font-size:.75rem;font-weight:900;white-space:nowrap}
.status-dot{width:.45rem;height:.45rem;border-radius:999px;background:var(--green);box-shadow:0 0 8px rgba(52,230,154,.6);flex-shrink:0}
.side-card{border:1px solid var(--line);border-radius:11px;background:var(--panel2);padding:.75rem}
.list-row{display:flex;justify-content:space-between;align-items:center;gap:.75rem;padding:.55rem 0;border-bottom:1px solid var(--line)}
.list-row:last-child{border-bottom:none}
.card-list{display:grid;gap:.55rem}
.a-btn{display:inline-flex;align-items:center;justify-content:center;min-height:2.3rem;border:1px solid var(--line);border-radius:9px;background:rgba(14,30,49,.86);color:var(--text);padding:.45rem .8rem;font-weight:850;text-decoration:none;cursor:pointer;font-size:.82rem;white-space:nowrap;transition:background .15s}
.a-btn-primary{border-color:rgba(52,230,154,.48);background:linear-gradient(135deg,#25c889,#0c7c5b);color:#fff}
.form-field{display:grid;gap:.35rem;margin-bottom:.85rem}
.form-label{font-size:.72rem;font-weight:950;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
.form-hint{font-size:.72rem;color:var(--muted);margin-top:.25rem}
.avatar-box{display:grid;width:96px;height:96px;place-items:center;border-radius:22px;background:linear-gradient(135deg,#45efaa,#4f46e5);font-size:2.5rem;font-weight:950;color:#fff;box-shadow:0 0 40px rgba(52,230,154,.22);flex-shrink:0}
@media(max-width:860px){.cockpit-grid{grid-template-columns:1fr}}
</style>

<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Worker Identity</p>
        <h1 style="margin:0;font-size:clamp(1.6rem,3vw,2.4rem);line-height:1">Worker profile</h1>
        <p class="muted" style="margin:.3rem 0 0">Personal data, vehicle and operational context.</p>
    </div>
    <div class="status-pill">
        <span class="status-dot"></span>
        <span>{{ $user->workerProfile ? 'Profile found' : 'No worker profile' }}</span>
    </div>
</div>

<form method="POST" action="{{ route('worker.profile.update') }}" class="cockpit-grid">
    @csrf
    @method('PATCH')

    <div class="main-stack">
        {{-- Basic info --}}
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head">
                    <h2 class="panel-title">Basic information</h2>
                    <span class="badge badge-blue">Email locked</span>
                </div>

                <div style="display:grid;grid-template-columns:100px 1fr;gap:1.25rem;align-items:start">
                    <div class="side-card" style="text-align:center;padding:1rem .75rem">
                        <div class="avatar-box" style="margin:0 auto .65rem">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                        <div style="font-weight:950;font-size:.88rem;color:var(--text);word-break:break-word">{{ $user->name }}</div>
                        <div class="muted" style="font-size:.72rem;margin:.2rem 0 0">{{ $user->workerProfile?->status ?? 'Worker' }}</div>
                    </div>

                    <div>
                        <div class="form-field">
                            <label class="form-label">Full name</label>
                            <input class="lk-input" type="text" name="name" value="{{ old('name', $user->name) }}" required style="width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.65rem .75rem">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled readonly style="width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.65rem .75rem;opacity:.48;cursor:not-allowed">
                            <span class="form-hint">Email cannot be changed from here — protects account ownership and recovery.</span>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->workerProfile?->phone ?? '') }}" placeholder="+47 ..." style="width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.65rem .75rem">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Vehicle --}}
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head">
                    <h2 class="panel-title">Work vehicle</h2>
                    <span class="badge badge-blue">Dispatch context</span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-field" style="margin:0">
                        <label class="form-label">Vehicle type</label>
                        <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $user->workerProfile?->vehicle_type ?? '') }}" placeholder="car, bicycle, van" style="width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.65rem .75rem">
                    </div>
                    <div class="form-field" style="margin:0">
                        <label class="form-label">Service area</label>
                        <input type="text" name="service_area" value="{{ old('service_area', $user->workerProfile?->service_area ?? '') }}" placeholder="Oslo, Bærum…" style="width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.65rem .75rem">
                    </div>
                </div>
                <div class="form-field" style="margin-top:.85rem;margin-bottom:0">
                    <label class="form-label">Notes</label>
                    <textarea name="worker_notes" rows="3" placeholder="Work restrictions, availability, important details…" style="width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.65rem .75rem;resize:vertical">{{ old('worker_notes', $user->workerProfile?->display_name ?? '') }}</textarea>
                </div>
            </div>
        </section>
    </div>

    <aside class="side-stack">
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Data check</h2></div>
                <div class="side-card">
                    <div class="list-row"><span class="muted">User ID</span><strong style="color:var(--text)">#{{ $user->id }}</strong></div>
                    <div class="list-row"><span class="muted">Worker profile</span>
                        <span class="badge {{ $user->workerProfile ? 'badge-green' : 'badge-amber' }}">{{ $user->workerProfile ? 'found' : 'missing' }}</span>
                    </div>
                    <div class="list-row"><span class="muted">Status</span><strong style="color:var(--text)">{{ ucfirst($user->workerProfile?->status ?? '—') }}</strong></div>
                    <div class="list-row"><span class="muted">Worker type</span><strong style="color:var(--text)">{{ ucfirst($user->workerProfile?->worker_type ?? '—') }}</strong></div>
                    <div class="list-row"><span class="muted">Member since</span><strong style="color:var(--text)">{{ $user->created_at->format('d M Y') }}</strong></div>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Save</h2></div>
                <p class="muted" style="font-size:.82rem;margin:0 0 .85rem">This is a real action: updates user profile and WorkerProfile via the controller.</p>
                <button type="submit" class="a-btn a-btn-primary" style="width:100%">Save changes →</button>
                <a href="{{ route('worker.support.index') }}" class="a-btn" style="width:100%;margin-top:.55rem;justify-content:center">⚙️ Open support ticket</a>
            </div>
        </section>

        @php $avStatus = $user->workerAvailability?->status ?? 'offline'; @endphp
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Presence</h2></div>
                <div class="side-card" style="text-align:center">
                    <div style="font-size:2rem;margin-bottom:.5rem">{{ in_array($avStatus,['online','available']) ? '🟢' : '🔴' }}</div>
                    <strong style="color:var(--text)">{{ ucfirst($avStatus) }}</strong>
                    <p class="muted" style="margin:.3rem 0 0;font-size:.78rem">Current presence status</p>
                </div>
                <div style="display:flex;gap:.5rem;margin-top:.75rem">
                    @if(in_array($avStatus,['online','available']))
                    <form method="post" action="{{ route('worker.presence.offline') }}" style="flex:1">
                        @csrf
                        <button class="a-btn" type="submit" style="width:100%;border-color:rgba(251,113,133,.4);color:var(--danger)">Go offline</button>
                    </form>
                    @else
                    <form method="post" action="{{ route('worker.presence.online') }}" style="flex:1">
                        @csrf
                        <button class="a-btn a-btn-primary" type="submit" style="width:100%">Go online</button>
                    </form>
                    @endif
                </div>
            </div>
        </section>
    </aside>
</form>
@endsection
