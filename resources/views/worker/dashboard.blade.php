@extends('worker.layout')
@section('title', 'Worker City Cockpit')
@section('body-class', 'worker-dashboard-mapfirst')
@section('content')
@php
    $profile = $user->workerProfile;
    $availabilityStatus = $availability?->status ?? 'offline';
    $actualOnline = in_array($availabilityStatus, ['online', 'available'], true);
    $pilotCenter = [(float)($mapConfig['center_lat'] ?? 68.4385), (float)($mapConfig['center_lng'] ?? 17.4272)];
    $hasLastPing = (bool) $lastPing;
    $gpsAgeLabel = $lastPing ? $lastPing->created_at->diffForHumans() : 'No real GPS ping yet';
    $locationState = request()->secure() ? ($hasLastPing ? 'Last real GPS: '.$gpsAgeLabel : 'Location not shared yet') : 'HTTPS required for browser GPS';
    $capabilities = [
        ['icon'=>'🛒','label'=>'Grocery delivery','state'=>$profile?->can_deliver ? 'active/configured' : 'not configured yet', 'on'=>(bool)($profile?->can_deliver)],
        ['icon'=>'🍔','label'=>'Ready food','state'=>$profile?->can_deliver ? 'active/configured' : 'not configured yet', 'on'=>(bool)($profile?->can_deliver)],
        ['icon'=>'🏗','label'=>'Bulky / materials','state'=>$profile?->can_move ? 'active/configured' : 'partner approval needed', 'on'=>(bool)($profile?->can_move)],
        ['icon'=>'🤝','label'=>'Errands','state'=>$profile?->can_run_errands ? 'active/configured' : 'not configured yet', 'on'=>(bool)($profile?->can_run_errands)],
        ['icon'=>'🛠','label'=>'GLF ByGG','state'=>'partner approval + legal/accounting check needed', 'on'=>false],
    ];
    $activeNextAction = $activeOrder ? app(\App\Services\Workers\WorkerOrderWorkflowService::class)->nextAction($activeOrder) : null;
    $currentState = ! $actualOnline ? 'offline' : 'waiting';
    if ($activeOrder) {
        $status = (string) ($activeOrder->status->value ?? $activeOrder->status ?? '');
        $action = (string) ($activeNextAction['key'] ?? '');
        $currentState = match (true) {
            str_contains($action, 'accept') => 'offer',
            str_contains($action, 'arrived_pickup') || str_contains($action, 'start') => 'navigate_pickup',
            str_contains($action, 'picked_up') => 'pickup',
            str_contains($action, 'arrived_dropoff') => 'navigate_client',
            str_contains($action, 'proof') || str_contains($action, 'complete') => 'proof',
            str_contains($status, 'complete') => 'completed',
            default => 'accepted',
        };
    }
    $stateLabels = [
        'offline' => ['title'=>'Offline', 'subtitle'=>'Not visible for new dispatch assignments.', 'primary'=>'Swipe to go online'],
        'waiting' => ['title'=>'Waiting', 'subtitle'=>'Online and waiting for a real assignment.', 'primary'=>'Swipe to go offline'],
        'offer' => ['title'=>'Offer received', 'subtitle'=>'A real dispatch assignment needs worker decision.', 'primary'=>'Open assignment'],
        'accepted' => ['title'=>'Accepted', 'subtitle'=>'Assignment accepted. Follow the next audited action.', 'primary'=>'Open current job'],
        'navigate_pickup' => ['title'=>'Navigate to pickup', 'subtitle'=>'Go to pickup/task start location. No fake ETA is shown.', 'primary'=>'Open current job'],
        'pickup' => ['title'=>'Pickup checklist', 'subtitle'=>'Confirm pickup/task-start only when it really happened.', 'primary'=>'Open current job'],
        'navigate_client' => ['title'=>'Navigate to client', 'subtitle'=>'Continue to client/dropoff/task location.', 'primary'=>'Open current job'],
        'delivery' => ['title'=>'Delivery / task', 'subtitle'=>'Complete the real service step by step.', 'primary'=>'Open current job'],
        'proof' => ['title'=>'Proof required', 'subtitle'=>'Submit real completion proof before client confirmation.', 'primary'=>'Open proof'],
        'completed' => ['title'=>'Completed', 'subtitle'=>'Job completed. Return to waiting when ready.', 'primary'=>'Back online'],
    ];
    $state = $stateLabels[$currentState] ?? $stateLabels['offline'];
    $coreFlow = [
        ['key'=>'onboarding','label'=>'Onboarding'],
        ['key'=>'offline','label'=>'Go online'],
        ['key'=>'waiting','label'=>'Waiting'],
        ['key'=>'offer','label'=>'Offer'],
        ['key'=>'accepted','label'=>'Accept'],
        ['key'=>'navigate_pickup','label'=>'Pickup route'],
        ['key'=>'pickup','label'=>'Pickup'],
        ['key'=>'navigate_client','label'=>'Client route'],
        ['key'=>'proof','label'=>'Proof'],
        ['key'=>'completed','label'=>'Complete'],
    ];
    $serviceScreens = [
        ['label'=>'Orders','route'=>'worker.orders.index','items'=>'Active · Upcoming · History'],
        ['label'=>'Work','route'=>'worker.schedule.index','items'=>'Availability · Break · Zones'],
        ['label'=>'Money','route'=>'worker.wallet.index','items'=>'Wallet · Payout · Tax'],
        ['label'=>'Support','route'=>'worker.support.index','items'=>'FAQ · Ticket · Emergency'],
        ['label'=>'Profile','route'=>'worker.profile.index','items'=>'Vehicle · BankID · Documents'],
        ['label'=>'Settings','route'=>'worker.profile.index','items'=>'GPS · Privacy · Language'],
    ];

    $mapPayload = [
        'center' => $pilotCenter,
        'zoom' => (int)($mapConfig['default_zoom'] ?? 11),
        'csrf' => csrf_token(),
        'pingUrl' => route('worker.location-pings.store'),
        'online' => $actualOnline,
        'lastPing' => $lastPing ? ['lat'=>(float)$lastPing->latitude,'lng'=>(float)$lastPing->longitude,'accuracy'=>(float)$lastPing->accuracy_meters,'label'=>$gpsAgeLabel] : null,
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQeq9Uq4ik9xkclle2n2E1Jb2fYFhI4=" crossorigin="">
<style>
html,body{overflow:hidden!important}body.worker-dashboard-mapfirst{background:#050d18}body.worker-dashboard-mapfirst .worker-shell{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-sidebar,body.worker-dashboard-mapfirst .worker-topbar,body.worker-dashboard-mapfirst .worker-bottom,body.worker-dashboard-mapfirst .worker-tablet-nav{display:none!important}body.worker-dashboard-mapfirst .worker-main{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-content{padding:0!important;min-height:100dvh}.city{position:fixed;inset:0;overflow:hidden;background:#050d18;color:#eef7ff}.city-map{position:absolute!important;inset:0!important;width:100vw!important;height:100dvh!important;z-index:1}.leaflet-container{background:#07111f;overflow:hidden!important}.leaflet-pane,.leaflet-tile,.leaflet-marker-icon,.leaflet-marker-shadow,.leaflet-tile-container,.leaflet-pane>svg,.leaflet-pane>canvas,.leaflet-zoom-box,.leaflet-image-layer,.leaflet-layer{position:absolute!important;left:0;top:0}.leaflet-container img.leaflet-tile{max-width:none!important;max-height:none!important;width:256px!important;height:256px!important;display:block!important}.leaflet-tile{filter:brightness(1.02) saturate(.9) contrast(1.04) hue-rotate(150deg);opacity:.72!important}.leaflet-map-pane,.leaflet-tile-pane,.leaflet-overlay-pane,.leaflet-marker-pane{position:absolute!important}.leaflet-control-container{position:absolute;z-index:20}.city-shade{position:absolute;inset:0;z-index:2;pointer-events:none;background:radial-gradient(circle at 24% 16%,rgba(48,222,151,.10),transparent 28%),linear-gradient(180deg,rgba(2,8,16,.08),transparent 42%,rgba(2,8,16,.22)),linear-gradient(90deg,rgba(2,8,16,.12),transparent 46%,rgba(2,8,16,.28))}.glass{border:1px solid rgba(170,196,220,.14);background:rgba(8,18,32,.56);backdrop-filter:blur(22px);box-shadow:0 22px 70px rgba(0,0,0,.25)}.topbar{position:absolute;z-index:10;top:max(.7rem,env(safe-area-inset-top));left:.75rem;right:.75rem;display:flex;align-items:center;justify-content:space-between;gap:.7rem;pointer-events:none}.brand{pointer-events:auto;display:flex;align-items:center;gap:.55rem;border-radius:999px;padding:.42rem .72rem;color:#fff;text-decoration:none}.logo{display:grid;place-items:center;width:34px;height:34px;border-radius:999px;background:linear-gradient(135deg,#27f083,#39ccff);color:#04120d;font-weight:1000}.brand strong{display:block;font-size:.86rem;line-height:1}.brand small{display:block;color:#9fb4cc;font-size:.62rem;font-weight:850}.top-actions{display:flex;align-items:center;gap:.42rem;pointer-events:auto}.status{display:flex;align-items:center;gap:.38rem;border-radius:999px;padding:.52rem .72rem;font-size:.78rem;font-weight:950;color:#ffbec8}.status i{width:.5rem;height:.5rem;border-radius:99px;background:#fb7185;box-shadow:0 0 12px rgba(251,113,133,.7)}.status.is-online{color:#b9ffe1}.status.is-online i{background:#27f083;box-shadow:0 0 14px rgba(39,240,131,.8)}.bell{display:grid;place-items:center;width:42px;height:42px;border-radius:999px;text-decoration:none;color:#eaf3ff;font-size:1rem}.location-card{position:absolute;z-index:8;top:5.2rem;left:.75rem;width:min(390px,calc(100vw - 1.5rem));border-radius:22px;padding:.86rem}.location-card p{margin:0;color:#a9bad1;font-size:.82rem;line-height:1.42}.location-card strong{display:block;font-size:.98rem;margin-bottom:.25rem}.location-card .fix{display:inline-flex;margin-top:.7rem;min-height:2.35rem;align-items:center;border-radius:999px;border:1px solid rgba(57,204,255,.25);background:rgba(57,204,255,.10);color:#dff8ff;text-decoration:none;padding:0 .85rem;font-weight:950;font-size:.8rem}.sheet{position:absolute;z-index:12;left:50%;bottom:0;transform:translate3d(-50%,calc(100% - var(--peek)),0);width:min(430px,calc(100vw - 1rem));height:min(86dvh,760px);border-radius:30px 30px 0 0;padding:.72rem .78rem calc(1rem + env(safe-area-inset-bottom));transition:transform .34s cubic-bezier(.22,1,.36,1);will-change:transform;touch-action:none}.sheet[data-state="half"]{transform:translate3d(-50%,calc(100% - var(--half)),0)}.sheet[data-state="expanded"]{transform:translate3d(-50%,0,0)}.sheet-handle{width:52px;height:4px;border-radius:99px;background:rgba(179,202,222,.42);margin:.1rem auto .72rem}.sheet-scroll{height:calc(100% - 18px);overflow:auto;overscroll-behavior:contain;padding:0 .06rem 1.1rem;scrollbar-width:thin}.sheet-scroll::-webkit-scrollbar{width:6px}.sheet-scroll::-webkit-scrollbar-thumb{background:rgba(170,196,220,.22);border-radius:999px}.sheet-head{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem}.sheet h1{margin:0;font-size:1.35rem;letter-spacing:-.03em}.muted{color:#98abc2}.micro{font-size:.72rem;line-height:1.35}.chip{display:inline-flex;align-items:center;gap:.35rem;border:1px solid rgba(170,196,220,.13);background:rgba(255,255,255,.035);border-radius:999px;padding:.28rem .55rem;font-size:.7rem;font-weight:950;color:#dfeeff}.state-title{display:flex;justify-content:space-between;gap:.8rem;align-items:flex-start;margin-top:.72rem;border:1px solid rgba(39,240,131,.18);background:linear-gradient(135deg,rgba(39,240,131,.11),rgba(57,204,255,.055));border-radius:22px;padding:.82rem}.state-title small{display:block;color:#86f7c7;font-size:.6rem;font-weight:1000;letter-spacing:.14em}.state-title strong{display:block;font-size:1.18rem}.state-title p{margin:.18rem 0 0;color:#adc0d4;font-size:.78rem}.state-title>span{border-radius:999px;background:rgba(3,12,21,.45);padding:.3rem .55rem;color:#dffaf0;font-size:.68rem;font-weight:950;white-space:nowrap}.lifecycle{display:flex;gap:.3rem;overflow:auto;margin-top:.72rem;padding:.25rem;border-radius:999px;border:1px solid rgba(170,196,220,.10);background:rgba(255,255,255,.025)}.lifecycle span{flex:0 0 auto;border-radius:999px;padding:.28rem .52rem;color:#8fa4bd;font-size:.62rem;font-weight:950;text-transform:uppercase}.lifecycle span.is-now{background:rgba(39,240,131,.14);color:#baffdf;box-shadow:inset 0 0 0 1px rgba(39,240,131,.2)}.demand{margin-top:.72rem;border-radius:22px;padding:.86rem;background:linear-gradient(135deg,rgba(255,255,255,.055),rgba(255,255,255,.018));border:1px solid rgba(170,196,220,.12)}.demand-label{display:flex;align-items:center;justify-content:space-between;gap:.75rem}.demand strong{font-size:1rem}.bars{display:flex;align-items:end;gap:.32rem}.bars i{display:block;width:.38rem;border-radius:99px;background:rgba(158,179,204,.28)}.bars i:nth-child(1){height:.64rem;background:#27f083}.bars i:nth-child(2){height:.95rem}.bars i:nth-child(3){height:1.25rem}.timeline{display:grid;grid-template-columns:repeat(7,1fr);gap:.27rem;margin-top:.72rem;align-items:end;height:62px;border-bottom:1px dashed rgba(170,196,220,.2)}.timeline i{display:block;border-radius:7px 7px 0 0;background:linear-gradient(180deg,rgba(183,197,211,.52),rgba(183,197,211,.16));min-height:10px}.timeline i:nth-child(3){height:31px}.timeline i:nth-child(4){height:38px}.timeline i:nth-child(5){height:34px}.timeline i:nth-child(6){height:30px}.boost{margin-top:.72rem;border:1px solid rgba(245,189,84,.16);background:linear-gradient(90deg,rgba(245,189,84,.11),rgba(255,255,255,.035));border-radius:18px;padding:.75rem}.primary-work{margin-top:.78rem}.primary-work h2,.quick h2{font-size:.82rem;margin:0 0 .4rem;color:#eef7ff}.work-card{position:relative;display:block;text-decoration:none;color:#eef7ff;border:1px solid rgba(57,204,255,.18);background:rgba(57,204,255,.06);border-radius:18px;padding:.78rem 2rem .78rem .82rem}.work-card b,.qrow b{display:block}.work-card small,.qrow small{display:block;margin-top:.16rem;color:#91a6bf;font-size:.68rem;font-weight:800}.work-card span{position:absolute;right:.82rem;top:50%;transform:translateY(-50%);color:#8cdfff}.work-card.is-empty{border-color:rgba(170,196,220,.12);background:rgba(255,255,255,.035)}.quick{margin-top:.78rem}.qrow{display:flex;align-items:center;justify-content:space-between;gap:.8rem;min-height:3.25rem;text-decoration:none;color:#eef7ff;border-bottom:1px solid rgba(170,196,220,.11);font-size:.88rem;font-weight:900}.qrow>span{color:#eef7ff}.qrow em{font-style:normal;color:#8094ad}.services{display:grid;grid-template-columns:1fr 1fr;gap:.45rem;margin-top:.78rem}.svc{border:1px solid rgba(170,196,220,.12);background:rgba(255,255,255,.035);border-radius:16px;padding:.62rem}.svc.is-on{border-color:rgba(39,240,131,.24);background:rgba(39,240,131,.07)}.svc b{display:block;font-size:.72rem}.svc small{display:block;margin-top:.22rem;color:#9fb4cc;font-size:.62rem;font-weight:850}.swipe{position:sticky;bottom:0;margin-top:.85rem;border-radius:999px;padding:7px;height:64px;background:linear-gradient(90deg,rgba(6,18,32,.94),rgba(9,32,45,.94));border:1px solid rgba(57,204,255,.28);box-shadow:inset 0 0 26px rgba(57,204,255,.06),0 20px 44px rgba(0,0,0,.22);overflow:hidden;touch-action:none}.swipe::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(39,240,131,.12),rgba(57,204,255,.18));transform:scaleX(var(--progress,0));transform-origin:left;transition:transform .08s}.swipe-label{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;color:#eaffff;letter-spacing:.01em}.knob{position:relative;z-index:2;width:50px;height:50px;border-radius:999px;border:0;background:linear-gradient(135deg,#38d9ff,#27f083);color:#03120d;font-size:1.35rem;font-weight:1000;box-shadow:0 12px 36px rgba(39,240,131,.28);cursor:grab;transition:transform .08s}.knob:focus-visible{outline:3px solid rgba(57,204,255,.55);outline-offset:3px}.nav-mini{display:none}.zone-label{font-size:.72rem;font-weight:950;color:#d8f9ff;text-shadow:0 2px 9px #000}.marker-last{display:grid;place-items:center;width:28px;height:28px;border-radius:999px;background:#38d9ff;color:#03120d;font-weight:1000;box-shadow:0 0 0 9px rgba(56,217,255,.16)}@media(min-width:900px){.sheet{right:1.15rem;left:auto;transform:translate3d(0,calc(100% - var(--peek)),0);width:420px}.sheet[data-state="half"]{transform:translate3d(0,calc(100% - var(--half)),0)}.sheet[data-state="expanded"]{transform:translate3d(0,0,0)}.location-card{left:1.15rem}.topbar{left:1.15rem;right:1.15rem}}@media(max-width:520px){.sheet{--peek:178px!important;--half:590px!important}.topbar{left:.55rem;right:.55rem}.brand small{display:none}.location-card{left:.55rem;right:.55rem;width:auto;top:4.75rem}.location-card strong{font-size:.9rem}.sheet{width:calc(100vw - .7rem);height:78dvh}.sheet h1{font-size:1.22rem}.services{display:flex;overflow-x:auto;scroll-snap-type:x mandatory}.svc{min-width:155px;scroll-snap-align:start}.nav-mini{display:flex;position:absolute;z-index:11;left:.55rem;right:.55rem;bottom:.45rem;gap:.2rem;justify-content:space-around;border-radius:22px;padding:.3rem}.nav-mini a{display:flex;flex-direction:column;align-items:center;gap:.05rem;border-radius:15px;padding:.38rem .42rem;text-decoration:none;color:#dce9f6;font-size:.6rem;font-weight:900}.nav-mini a.is-active{background:rgba(39,240,131,.13);color:#27f083}}
</style>
@endpush

<div class="city" data-city-cockpit>
    <div id="worker-live-map" class="city-map" role="application" aria-label="Live Narvik worker map"></div>
    <div class="city-shade" aria-hidden="true"></div>

    <header class="topbar" aria-label="BiKuBe Worker top bar">
        <a class="brand glass" href="{{ route('worker.dashboard') }}"><span class="logo">B</span><span><strong>BiKuBe</strong><small>Narvik City OS</small></span></a>
        <div class="top-actions">
            <div class="status glass {{ $actualOnline ? 'is-online' : '' }}"><i></i> {{ $state['title'] }}</div>
            <a class="bell glass" href="{{ route('worker.notifications.index') }}" aria-label="Notifications">🔔</a>
        </div>
    </header>

    <aside class="location-card glass" aria-label="Location readiness">
        <strong>{{ request()->secure() ? 'Location is not active yet' : 'Location access needs HTTPS' }}</strong>
        <p>{{ $locationState }}. BiKuBe never fakes worker position; GPS starts only after your explicit action.</p>
        <a class="fix" href="{{ route('worker.support.index') }}">Fix location access</a>
    </aside>

    <section class="sheet glass" data-sheet data-state="half" style="--peek:172px;--half:610px" aria-label="Worker control sheet">
        <div class="sheet-handle" data-sheet-handle aria-hidden="true"></div>
        <div class="sheet-scroll">
            <div class="sheet-head">
                <div><h1>Narvik</h1><p class="muted micro" style="margin:.15rem 0 0">{{ $state['subtitle'] }}</p></div>
                <span class="chip">State: {{ $state['title'] }}</span>
            </div>
            <div class="state-title" aria-label="Current worker state">
                <div><small>LIVE STATE MACHINE</small><strong>{{ $state['title'] }}</strong><p>{{ $state['subtitle'] }}</p></div>
                <span>{{ $activeOrder ? 'Order #'.$activeOrder->id : 'No active order' }}</span>
            </div>
            <div class="lifecycle core-flow" aria-label="BiKuBe worker workday flow">
                @foreach($coreFlow as $step)
                    <span class="{{ $step['key'] === $currentState ? 'is-now' : '' }}">{{ $step['label'] }}</span>
                @endforeach
            </div>

            <div class="demand" aria-label="Service demand signal">
                <div class="demand-label"><div><strong>{{ $orders->count() ? 'Assigned work active' : 'Demand signal unavailable' }}</strong><p class="muted micro" style="margin:.2rem 0 0">{{ $orders->count() ? 'You have real assigned work from dispatch. Open Current assignment for next action.' : 'Live demand engine is not active yet. No demand level is invented.' }}</p></div><div class="bars" aria-hidden="true"><i></i><i></i><i></i></div></div>
                <div class="timeline" aria-hidden="true"><i style="height:18px"></i><i style="height:22px"></i><i></i><i></i><i></i><i></i><i style="height:16px"></i></div>
            </div>

            @unless($activeOrder)
                <div class="boost"><strong>Boost campaigns will appear here.</strong><p class="muted micro" style="margin:.22rem 0 0">Weather boosts, quests and extra pay require real campaign rules.</p></div>
            @endunless

            <div class="primary-work">
                <h2>Current work</h2>
                @if($activeOrder)
                    <a class="work-card" href="{{ route('worker.orders.show', $activeOrder) }}">
                        <b>{{ $state['primary'] }}</b>
                        <small>{{ $activeOrder->scenario?->name ?? 'Scenario order' }} · #{{ $activeOrder->id }}</small>
                        <span>›</span>
                    </a>
                @else
                    <div class="work-card is-empty">
                        <b>{{ $actualOnline ? 'Waiting for assignment' : 'Not on the line' }}</b>
                        <small>{{ $actualOnline ? 'Dispatch has not assigned a real order to this worker.' : 'Swipe online when ready for real work.' }}</small>
                    </div>
                @endif
            </div>
            <div class="quick service-map">
                <h2>Service screens · secondary</h2>
                @foreach($serviceScreens as $screen)
                    @if(Route::has($screen['route']))
                        <a class="qrow" href="{{ route($screen['route']) }}"><span><b>{{ $screen['label'] }}</b><small>{{ $screen['items'] }}</small></span><em>›</em></a>
                    @endif
                @endforeach
            </div>

            <div class="services" aria-label="BiKuBe V1 services">
                @foreach($capabilities as $service)
                    <article class="svc {{ $service['on'] ? 'is-on' : '' }}"><b>{{ $service['icon'] }} {{ $service['label'] }}</b><small>{{ $service['state'] }}</small></article>
                @endforeach
            </div>

            <form id="presence-form" method="POST" action="{{ $actualOnline ? route('worker.presence.offline') : route('worker.presence.online') }}">@csrf</form>
            <div class="swipe" data-swipe-presence data-form="presence-form" @if($activeOrder) data-url="{{ route('worker.orders.show', $activeOrder) }}" @endif role="group" aria-label="{{ $activeOrder ? $state['primary'] : ($actualOnline ? 'Swipe to go offline' : 'Swipe to go online') }}">
                <div class="swipe-label">{{ $activeOrder ? $state['primary'] : ($actualOnline ? 'Swipe to go offline' : 'Swipe to go online') }}</div>
                <button class="knob" type="button" aria-label="{{ $activeOrder ? $state['primary'] : ($actualOnline ? 'Go offline' : 'Go online') }}">›</button>
            </div>
        </div>
    </section>

    <nav class="nav-mini glass" aria-label="Worker app navigation">
        <a class="is-active" href="{{ route('worker.dashboard') }}">⌂<span>Home</span></a>
        <a href="{{ route('worker.orders.index') }}">📦<span>Orders</span></a>
        <a href="{{ route('worker.wallet.index') }}">💳<span>Wallet</span></a>
        <a href="{{ route('worker.support.index') }}">🛟<span>Help</span></a>
        <a href="{{ route('worker.profile.index') }}">👤<span>More</span></a>
    </nav>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function(){
 const cfg=@json($mapPayload), el=document.getElementById('worker-live-map'); if(!el||!window.L)return;
 const map=L.map(el,{zoomControl:false,attributionControl:false,dragging:true,tap:true}).setView(cfg.center,cfg.zoom||11);
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
 const refresh=()=>{el.style.setProperty('width','100vw','important');el.style.setProperty('height','100dvh','important');map.getContainer().style.setProperty('width','100vw','important');map.getContainer().style.setProperty('height','100dvh','important');map.invalidateSize(true); map.setView(cfg.center,cfg.zoom||11,{animate:false})}; refresh(); requestAnimationFrame(refresh); setTimeout(refresh,0); setTimeout(refresh,250); setTimeout(refresh,1000); setTimeout(refresh,1800); window.addEventListener('resize',refresh);
 const narvik=[[68.54,17.18],[68.49,17.62],[68.35,17.69],[68.31,17.25]], ballangen=[[68.41,16.75],[68.35,17.05],[68.22,16.98],[68.26,16.68]];
 L.polygon(narvik,{color:'#27f083',weight:2,fillColor:'#27f083',fillOpacity:.10,dashArray:'8 8'}).addTo(map); L.polygon(ballangen,{color:'#38d9ff',weight:2,fillColor:'#38d9ff',fillOpacity:.08,dashArray:'8 8'}).addTo(map);
 L.marker([68.4385,17.4272],{interactive:false,icon:L.divIcon({className:'zone-label',html:'Narvik / Ballangen pilot zone'})}).addTo(map);
 if(cfg.lastPing){L.marker([cfg.lastPing.lat,cfg.lastPing.lng],{icon:L.divIcon({className:'',html:'<span class="marker-last">●</span>',iconSize:[28,28],iconAnchor:[14,14]})}).addTo(map).bindPopup('Last real GPS ping: '+cfg.lastPing.label);}
 const sheet=document.querySelector('[data-sheet]'), handle=document.querySelector('[data-sheet-handle]');
 if(sheet&&handle){let startY=0,current='half';const states=['collapsed','half','expanded'];const set=s=>{current=s;sheet.dataset.state=s};handle.addEventListener('pointerdown',e=>{startY=e.clientY;handle.setPointerCapture(e.pointerId)});handle.addEventListener('pointerup',e=>{const dy=e.clientY-startY;if(dy<-35)set(current==='collapsed'?'half':'expanded');else if(dy>35)set(current==='expanded'?'half':'collapsed');else set(current==='half'?'expanded':'half')});sheet.addEventListener('dblclick',()=>set(current==='expanded'?'half':'expanded'));}
 document.querySelectorAll('[data-swipe-presence]').forEach(root=>{const knob=root.querySelector('.knob'),form=document.getElementById(root.dataset.form),label=root.querySelector('.swipe-label');let drag=false,start=0,x=0,max=0;const set=v=>{x=Math.max(0,Math.min(v,max));knob.style.transform='translateX('+x+'px)';root.style.setProperty('--progress',max?x/max:0)};const reset=()=>{set(0)};knob.addEventListener('pointerdown',e=>{drag=true;start=e.clientX;max=root.clientWidth-knob.clientWidth-14;knob.setPointerCapture(e.pointerId)});knob.addEventListener('pointermove',e=>{if(drag)set(e.clientX-start)});knob.addEventListener('pointerup',()=>{if(!drag)return;drag=false;if(x>max*.74){label.textContent=root.dataset.url?'Opening current job…':'Updating presence…'; if(root.dataset.url){window.location.href=root.dataset.url}else{form?.submit()}}else reset()});knob.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();if(root.dataset.url){window.location.href=root.dataset.url}else{form?.submit()}})});
})();
</script>
@endpush
@endsection
