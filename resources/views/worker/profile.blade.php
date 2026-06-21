@extends('worker.layout')
@section('title', 'Profile')
@section('content')

@php
    $profile = $user->workerProfile;
    $availability = $user->workerAvailability;
    $capabilityRows = [
        ['🛒','Grocery delivery from stores',(bool)($profile?->can_deliver),'Uses real can_deliver capability'],
        ['🍔','Ready food delivery',(bool)($profile?->can_deliver),'Uses real can_deliver capability'],
        ['🏗','Bulky / construction materials',(bool)($profile?->can_move),'Not configured until worker is approved for moving'],
        ['🤝','Personal errands / assistant',(bool)($profile?->can_run_errands),'Not configured until worker is approved for errands'],
        ['📍','Narvik / Ballangen pilot zone',($profile?->status === 'approved'),'Available after worker approval'],
    ];
@endphp
<style>
.lk-profile-hero{display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:end;border:1px solid rgba(var(--brand-rgb),.2);border-radius:24px;background:radial-gradient(circle at 14% 10%,rgba(var(--brand-rgb),.16),transparent 34%),var(--panel);padding:1.1rem;margin-bottom:1rem;box-shadow:0 22px 60px rgba(0,0,0,.22)}
.lk-avatar{width:64px;height:64px;border-radius:22px;display:grid;place-items:center;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#04120d;font-weight:950;font-size:1.35rem}.lk-service-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.55rem;margin-bottom:1rem}.lk-service-card{border:1px solid var(--line);border-radius:16px;background:var(--panel2);padding:.75rem;min-height:96px}.lk-service-card.is-on{border-color:rgba(var(--brand-rgb),.28);background:rgba(var(--brand-rgb),.08)}.lk-service-card strong{display:block;font-size:.82rem;line-height:1.15}.lk-service-card span{display:block;color:var(--muted);font-size:.7rem;margin-top:.35rem;line-height:1.25}@media(max-width:920px){.lk-service-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.lk-profile-hero{grid-template-columns:1fr}}@media(max-width:520px){.lk-service-grid{grid-template-columns:1fr}}
</style>
<section class="lk-profile-hero" aria-label="Worker readiness profile">
  <div>
    <p class="worker-hero-eyebrow">Worker readiness</p>
    <h1 style="margin:.25rem 0;font-size:clamp(1.8rem,7vw,3rem);line-height:.95">{{ $profile?->display_name ?? $user->name }}</h1>
    <p class="muted" style="margin:0">{{ ucfirst($profile?->status ?? 'profile missing') }} · {{ $availability?->status ?? 'offline' }} · service capabilities from real worker profile.</p>
  </div>
  <div class="lk-avatar" aria-hidden="true">{{ Str::upper(Str::substr($profile?->display_name ?? $user->name ?? 'W',0,1)) }}</div>
</section>
<section class="lk-service-grid" aria-label="Service capabilities">
 @foreach($capabilityRows as $row)
  <article class="lk-service-card {{ $row[2] ? 'is-on' : '' }}"><strong>{{ $row[0] }} {{ $row[1] }}</strong><span>{{ $row[2] ? 'Configured' : $row[3] }}</span></article>
 @endforeach
</section>

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
.a-btn{display:inline-flex;align-items:center;justify-content:center;min-height:2.3rem;border:1px solid var(--line);border-radius:9px;background:rgba(14,30,49,.86);color:var(--text);padding:.45rem .8rem;font-weight:850;text-decoration:none;cursor:pointer;font-size:.82rem;white-space:nowrap;transition:background .15s}
.a-btn-primary{border-color:rgba(var(--brand-rgb),.48);background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff}
.form-field{display:grid;gap:.35rem;margin-bottom:.85rem}
.form-label{font-size:.72rem;font-weight:950;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
.form-hint{font-size:.72rem;color:var(--muted);margin-top:.25rem}
.avatar-box{display:grid;width:96px;height:96px;place-items:center;border-radius:22px;background:linear-gradient(135deg,#45efaa,#4f46e5);font-size:2.5rem;font-weight:950;color:#fff;box-shadow:0 0 40px rgba(var(--brand-rgb),.22);flex-shrink:0}
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
        {{-- BiKuBe pilot info --}}
        <section class="panel" style="border-color:rgba(var(--brand-rgb),.15)">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title" style="color:var(--green)">BiKuBe Pilot</h2></div>
                <div class="side-card">
                    <div class="list-row"><span class="muted">Зона</span><strong style="color:#c8f7e4">Narvik</strong></div>
                    <div class="list-row"><span class="muted">Статус</span>
                        @php $wst = $user->workerProfile?->status ?? 'pending'; @endphp
                        <span class="badge {{ $wst === 'approved' ? 'badge-green' : ($wst === 'suspended' ? '' : 'badge-amber') }}"
                              style="{{ $wst === 'suspended' ? 'border-color:rgba(251,113,133,.3);color:var(--danger)' : '' }}">
                            {{ $wst === 'approved' ? 'Одобрен' : ($wst === 'suspended' ? 'Приостановлен' : 'Ожидает') }}
                        </span>
                    </div>
                    <div class="list-row"><span class="muted">Аккаунт с</span><strong>{{ $user->created_at->format('d.m.Y') }}</strong></div>
                    <div class="list-row"><span class="muted">ID</span><strong style="font-size:.76rem">#{{ $user->id }}</strong></div>
                </div>
            </div>
        </section>

        {{-- Document verification status --}}
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Верификация</h2></div>
                @php
                $docs = [
                    ['label' => 'Личность / ID', 'done' => !is_null($user->workerProfile?->identity_verified_at)],
                    ['label' => 'Профиль заполнен', 'done' => !empty($user->workerProfile?->vehicle_type)],
                    ['label' => 'Аккаунт одобрен', 'done' => ($user->workerProfile?->status ?? '') === 'approved'],
                    ['label' => 'Платёжный профиль', 'done' => false],
                ];
                $doneCount = collect($docs)->where('done', true)->count();
                $pct = count($docs) > 0 ? round($doneCount / count($docs) * 100) : 0;
                @endphp
                <div style="margin-bottom:.85rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem">
                        <span style="color:var(--muted);font-size:.74rem">Готовность</span>
                        <span style="font-size:.78rem;font-weight:900;color:{{ $pct === 100 ? 'var(--green)' : 'var(--amber)' }}">{{ $pct }}%</span>
                    </div>
                    <div style="height:5px;border-radius:999px;background:rgba(148,163,184,.12);overflow:hidden">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $pct === 100 ? 'var(--green)' : 'var(--amber)' }};border-radius:999px;transition:width .4s"></div>
                    </div>
                </div>
                <div style="display:grid;gap:.38rem">
                    @foreach($docs as $doc)
                    <div style="display:flex;align-items:center;gap:.55rem;font-size:.8rem">
                        <span style="width:16px;height:16px;border-radius:50%;border:1.5px solid {{ $doc['done'] ? 'rgba(var(--brand-rgb),.5)' : 'rgba(148,163,184,.25)' }};background:{{ $doc['done'] ? 'rgba(var(--brand-rgb),.12)' : 'transparent' }};display:grid;place-items:center;flex-shrink:0;font-size:.6rem;color:{{ $doc['done'] ? 'var(--green)' : 'var(--muted)' }}">{{ $doc['done'] ? '✓' : '' }}</span>
                        <span style="color:{{ $doc['done'] ? '#d0f0e4' : 'var(--muted)' }}">{{ $doc['label'] }}</span>
                        @if(!$doc['done'])<span style="margin-left:auto;font-size:.62rem;color:var(--amber)">Ожидает</span>@endif
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('worker.payout-reviews.index') }}" class="a-btn" style="width:100%;margin-top:.85rem;justify-content:center;font-size:.8rem">Документы и проверки →</a>
            </div>
        </section>

        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Сохранить</h2></div>
                <button type="submit" class="a-btn a-btn-primary" style="width:100%">Сохранить изменения →</button>
                <a href="{{ route('worker.support.index') }}" class="a-btn" style="width:100%;margin-top:.55rem;justify-content:center">🛟 Служба поддержки</a>
            </div>
        </section>

        @php $avStatus = $user->workerAvailability?->status ?? 'offline'; @endphp
        <section class="panel">
            <div class="panel-inner">
                <div class="panel-head"><h2 class="panel-title">Присутствие</h2></div>
                <div style="display:flex;align-items:center;gap:.65rem;padding:.65rem;border-radius:10px;background:{{ in_array($avStatus,['online','available']) ? 'rgba(var(--brand-rgb),.07)' : 'rgba(148,163,184,.05)' }};border:1px solid {{ in_array($avStatus,['online','available']) ? 'rgba(var(--brand-rgb),.22)' : 'rgba(148,163,184,.12)' }};margin-bottom:.65rem">
                    <span style="width:10px;height:10px;border-radius:50%;background:{{ in_array($avStatus,['online','available']) ? 'var(--green)' : 'var(--muted)' }};flex-shrink:0"></span>
                    <strong style="color:{{ in_array($avStatus,['online','available']) ? 'var(--green)' : 'var(--muted)' }};font-size:.88rem">{{ in_array($avStatus,['online','available']) ? 'Онлайн' : 'Оффлайн' }}</strong>
                </div>
                @if(in_array($avStatus,['online','available']))
                <form method="post" action="{{ route('worker.presence.offline') }}">@csrf
                    <button class="a-btn" type="submit" style="width:100%;border-color:rgba(251,113,133,.4);color:var(--danger)">Уйти оффлайн</button>
                </form>
                @else
                <a href="{{ route('worker.dashboard') }}" class="a-btn a-btn-primary" style="width:100%;text-align:center;text-decoration:none;display:flex;justify-content:center">Выйти в онлайн →</a>
                @endif
            </div>
        </section>
    </aside>
</form>
@endsection
