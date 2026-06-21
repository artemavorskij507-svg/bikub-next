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
    $sheetPrimaryLabel = $activeOrder ? $state['primary'] : ($actualOnline ? 'Slide to go offline' : 'Slide to go online');
    $sheetMarketLabel = $activeOrder ? 'Assignment mode' : ($actualOnline ? 'Available for dispatch' : 'Offline');
    $sheetMarketCopy = $activeOrder
        ? 'A real dispatch assignment is active. Keep the map visible and continue from Current Job.'
        : ($actualOnline
            ? 'You are visible to dispatch. BiKuBe does not invent demand, ETA or bonuses before the real engine exists.'
            : 'Go online when you are physically ready to accept real Narvik/Ballangen work.');

    $mapPayload = [
        'center' => $pilotCenter,
        'zoom' => (int)($mapConfig['default_zoom'] ?? 11),
        'csrf' => csrf_token(),
        'pingUrl' => route('worker.location-pings.store'),
        'online' => $actualOnline,
        'secure' => request()->secure(),
        'lastPing' => $lastPing ? ['lat'=>(float)$lastPing->latitude,'lng'=>(float)$lastPing->longitude,'accuracy'=>(float)$lastPing->accuracy_meters,'label'=>$gpsAgeLabel] : null,
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQeq9Uq4ik9xkclle2n2E1Jb2fYFhI4=" crossorigin="">
<style>
html,body{overflow:hidden!important}body.worker-dashboard-mapfirst{background:#050d18}body.worker-dashboard-mapfirst .worker-shell{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-sidebar,body.worker-dashboard-mapfirst .worker-topbar,body.worker-dashboard-mapfirst .worker-bottom,body.worker-dashboard-mapfirst .worker-tablet-nav{display:none!important}body.worker-dashboard-mapfirst .worker-main{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-content{padding:0!important;min-height:100dvh}.city{position:fixed;inset:0;overflow:hidden;background:#050d18;color:#eef7ff}.city-map{position:absolute!important;inset:0!important;width:100vw!important;height:100dvh!important;z-index:1}.leaflet-container{background:#07111f;overflow:hidden!important}.leaflet-pane,.leaflet-tile,.leaflet-marker-icon,.leaflet-marker-shadow,.leaflet-tile-container,.leaflet-pane>svg,.leaflet-pane>canvas,.leaflet-zoom-box,.leaflet-image-layer,.leaflet-layer{position:absolute!important;left:0;top:0}.leaflet-container img.leaflet-tile{max-width:none!important;max-height:none!important;width:256px!important;height:256px!important;display:block!important}.leaflet-tile{filter:brightness(1.02) saturate(.9) contrast(1.04) hue-rotate(150deg);opacity:.72!important}.leaflet-map-pane,.leaflet-tile-pane,.leaflet-overlay-pane,.leaflet-marker-pane{position:absolute!important}.leaflet-control-container{position:absolute;z-index:20}.city-backdrop{position:absolute;inset:0;z-index:11;background:rgba(2,8,16,.18);opacity:0;pointer-events:none;transition:opacity .22s ease}.city-backdrop.is-visible{opacity:1;pointer-events:auto}.city-shade{position:absolute;inset:0;z-index:2;pointer-events:none;background:radial-gradient(circle at 24% 16%,rgba(48,222,151,.10),transparent 28%),linear-gradient(180deg,rgba(2,8,16,.08),transparent 42%,rgba(2,8,16,.22)),linear-gradient(90deg,rgba(2,8,16,.12),transparent 46%,rgba(2,8,16,.28))}.glass{border:1px solid rgba(170,196,220,.14);background:rgba(8,18,32,.56);backdrop-filter:blur(22px);box-shadow:0 22px 70px rgba(0,0,0,.25)}.topbar{position:absolute;z-index:10;top:max(.7rem,env(safe-area-inset-top));left:.75rem;right:.75rem;display:flex;align-items:center;justify-content:space-between;gap:.7rem;pointer-events:none}.brand{pointer-events:auto;display:flex;align-items:center;gap:.55rem;border-radius:999px;padding:.42rem .72rem;color:#fff;text-decoration:none}.logo{display:grid;place-items:center;width:34px;height:34px;border-radius:999px;background:linear-gradient(135deg,#27f083,#39ccff);color:#04120d;font-weight:1000}.brand strong{display:block;font-size:.86rem;line-height:1}.brand small{display:block;color:#9fb4cc;font-size:.62rem;font-weight:850}.top-actions{display:flex;align-items:center;gap:.42rem;pointer-events:auto}.status{display:flex;align-items:center;gap:.38rem;border-radius:999px;padding:.52rem .72rem;font-size:.78rem;font-weight:950;color:#ffbec8}.status i{width:.5rem;height:.5rem;border-radius:99px;background:#fb7185;box-shadow:0 0 12px rgba(251,113,133,.7)}.status.is-online{color:#b9ffe1}.status.is-online i{background:#27f083;box-shadow:0 0 14px rgba(39,240,131,.8)}.bell{display:grid;place-items:center;width:42px;height:42px;border-radius:999px;text-decoration:none;color:#eaf3ff;font-size:1rem}.location-card{position:absolute;z-index:8;top:5.2rem;left:.75rem;width:min(390px,calc(100vw - 1.5rem));border-radius:22px;padding:.86rem}.location-card p{margin:0;color:#a9bad1;font-size:.82rem;line-height:1.42}.location-card strong{display:block;font-size:.98rem;margin-bottom:.25rem}.location-card .fix{display:inline-flex;margin-top:.7rem;min-height:2.35rem;align-items:center;border-radius:999px;border:1px solid rgba(57,204,255,.25);background:rgba(57,204,255,.10);color:#dff8ff;text-decoration:none;padding:0 .85rem;font-weight:950;font-size:.8rem}.sheet{position:absolute;z-index:12;left:50%;bottom:0;transform:translate3d(-50%,calc(100% - var(--peek)),0);width:min(430px,calc(100vw - 1rem));height:min(86dvh,760px);border-radius:30px 30px 0 0;padding:.72rem .78rem calc(1rem + env(safe-area-inset-bottom));transition:transform .34s cubic-bezier(.22,1,.36,1);will-change:transform;touch-action:none}.sheet.is-dragging{transition:none}.sheet[data-state="collapsed"]{transform:translate3d(-50%,calc(100% - var(--peek)),0)}.sheet[data-state="half"]{transform:translate3d(-50%,calc(100% - var(--half)),0)}.sheet[data-state="expanded"]{transform:translate3d(-50%,0,0)}.sheet-handle{width:64px;height:5px;border-radius:99px;background:rgba(179,202,222,.42);margin:.1rem auto .62rem;cursor:grab}.sheet-mini{display:flex;align-items:center;justify-content:space-between;gap:.7rem;padding:.12rem .08rem .56rem}.sheet-mini b{display:block;font-size:.95rem}.sheet-mini small{display:block;color:#91a6bf;font-size:.66rem;font-weight:850}.sheet-grabber{display:grid;place-items:center;min-width:38px;height:38px;border-radius:999px;background:rgba(255,255,255,.055);color:#dff8ff;font-weight:1000}.sheet[data-state="expanded"] .sheet-mini{display:none}.sheet-scroll{height:calc(100% - 18px);overflow:auto;overscroll-behavior:contain;padding:0 .06rem 1.1rem;scrollbar-width:thin}.sheet-scroll::-webkit-scrollbar{width:6px}.sheet-scroll::-webkit-scrollbar-thumb{background:rgba(170,196,220,.22);border-radius:999px}.sheet-head{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem}.sheet h1{margin:0;font-size:1.35rem;letter-spacing:-.03em}.muted{color:#98abc2}.micro{font-size:.72rem;line-height:1.35}.chip{display:inline-flex;align-items:center;gap:.35rem;border:1px solid rgba(170,196,220,.13);background:rgba(255,255,255,.035);border-radius:999px;padding:.28rem .55rem;font-size:.7rem;font-weight:950;color:#dfeeff}.state-title{display:flex;justify-content:space-between;gap:.8rem;align-items:flex-start;margin-top:.72rem;border:1px solid rgba(39,240,131,.18);background:linear-gradient(135deg,rgba(39,240,131,.11),rgba(57,204,255,.055));border-radius:22px;padding:.82rem}.state-title small{display:block;color:#86f7c7;font-size:.6rem;font-weight:1000;letter-spacing:.14em}.state-title strong{display:block;font-size:1.18rem}.state-title p{margin:.18rem 0 0;color:#adc0d4;font-size:.78rem}.state-title>span{border-radius:999px;background:rgba(3,12,21,.45);padding:.3rem .55rem;color:#dffaf0;font-size:.68rem;font-weight:950;white-space:nowrap}.lifecycle{display:flex;gap:.3rem;overflow:auto;margin-top:.72rem;padding:.25rem;border-radius:999px;border:1px solid rgba(170,196,220,.10);background:rgba(255,255,255,.025)}.lifecycle span{flex:0 0 auto;border-radius:999px;padding:.28rem .52rem;color:#8fa4bd;font-size:.62rem;font-weight:950;text-transform:uppercase}.lifecycle span.is-now{background:rgba(39,240,131,.14);color:#baffdf;box-shadow:inset 0 0 0 1px rgba(39,240,131,.2)}.demand{margin-top:.72rem;border-radius:22px;padding:.86rem;background:linear-gradient(135deg,rgba(255,255,255,.055),rgba(255,255,255,.018));border:1px solid rgba(170,196,220,.12)}.demand-label{display:flex;align-items:center;justify-content:space-between;gap:.75rem}.demand strong{font-size:1rem}.bars{display:flex;align-items:end;gap:.32rem}.bars i{display:block;width:.38rem;border-radius:99px;background:rgba(158,179,204,.28)}.bars.is-disabled i{background:rgba(148,163,184,.18)!important}.bars i:nth-child(1){height:.64rem;background:#27f083}.bars i:nth-child(2){height:.95rem}.bars i:nth-child(3){height:1.25rem}.timeline{display:grid;grid-template-columns:repeat(7,1fr);gap:.27rem;margin-top:.72rem;align-items:end;height:62px;border-bottom:1px dashed rgba(170,196,220,.2)}.timeline.is-disabled{opacity:.42}.timeline i{display:block;border-radius:7px 7px 0 0;background:linear-gradient(180deg,rgba(183,197,211,.52),rgba(183,197,211,.16));min-height:10px}.timeline i:nth-child(3){height:31px}.timeline i:nth-child(4){height:38px}.timeline i:nth-child(5){height:34px}.timeline i:nth-child(6){height:30px}.boost{margin-top:.72rem;border:1px solid rgba(245,189,84,.16);background:linear-gradient(90deg,rgba(245,189,84,.11),rgba(255,255,255,.035));border-radius:18px;padding:.75rem}.primary-work{margin-top:.78rem}.primary-work h2,.quick h2{font-size:.82rem;margin:0 0 .4rem;color:#eef7ff}.work-card{position:relative;display:block;text-decoration:none;color:#eef7ff;border:1px solid rgba(57,204,255,.18);background:rgba(57,204,255,.06);border-radius:18px;padding:.78rem 2rem .78rem .82rem}.work-card b,.qrow b{display:block}.work-card small,.qrow small{display:block;margin-top:.16rem;color:#91a6bf;font-size:.68rem;font-weight:800}.work-card span{position:absolute;right:.82rem;top:50%;transform:translateY(-50%);color:#8cdfff}.work-card.is-empty{border-color:rgba(170,196,220,.12);background:rgba(255,255,255,.035)}.quick{margin-top:.78rem}.qrow{display:flex;align-items:center;justify-content:space-between;gap:.8rem;min-height:3.25rem;text-decoration:none;color:#eef7ff;border-bottom:1px solid rgba(170,196,220,.11);font-size:.88rem;font-weight:900}.qrow>span{color:#eef7ff}.qrow em{font-style:normal;color:#8094ad}.qrow.is-disabled{opacity:.55;pointer-events:none}.planned-offline{display:flex;align-items:center;justify-content:space-between;gap:.7rem;margin-top:.72rem;border:1px solid rgba(170,196,220,.12);background:rgba(255,255,255,.035);border-radius:18px;padding:.75rem}.planned-offline button{border:1px solid rgba(170,196,220,.16);background:rgba(255,255,255,.045);color:#dcecff;border-radius:999px;padding:.45rem .72rem;font-weight:950}.services{display:grid;grid-template-columns:1fr 1fr;gap:.45rem;margin-top:.78rem}.svc{border:1px solid rgba(170,196,220,.12);background:rgba(255,255,255,.035);border-radius:16px;padding:.62rem}.svc.is-on{border-color:rgba(39,240,131,.24);background:rgba(39,240,131,.07)}.svc b{display:block;font-size:.72rem}.svc small{display:block;margin-top:.22rem;color:#9fb4cc;font-size:.62rem;font-weight:850}.swipe{position:sticky;bottom:0;margin-top:.85rem;border-radius:999px;padding:7px;height:64px;background:linear-gradient(90deg,rgba(6,18,32,.94),rgba(9,32,45,.94));border:1px solid rgba(57,204,255,.28);box-shadow:inset 0 0 26px rgba(57,204,255,.06),0 20px 44px rgba(0,0,0,.22);overflow:hidden;touch-action:none}.swipe::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(39,240,131,.12),rgba(57,204,255,.18));transform:scaleX(var(--progress,0));transform-origin:left;transition:transform .08s}.swipe-label{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;color:#eaffff;letter-spacing:.01em}.knob{position:relative;z-index:2;width:50px;height:50px;border-radius:999px;border:0;background:linear-gradient(135deg,#38d9ff,#27f083);color:#03120d;font-size:1.35rem;font-weight:1000;box-shadow:0 12px 36px rgba(39,240,131,.28);cursor:grab;transition:transform .08s}.knob:focus-visible{outline:3px solid rgba(57,204,255,.55);outline-offset:3px}.nav-mini{display:none}.zone-label{font-size:.72rem;font-weight:950;color:#d8f9ff;text-shadow:0 2px 9px #000}.marker-last{display:grid;place-items:center;width:28px;height:28px;border-radius:999px;background:#38d9ff;color:#03120d;font-weight:1000;box-shadow:0 0 0 9px rgba(56,217,255,.16)}@media(min-width:900px){.sheet{right:1.15rem;left:auto;transform:translate3d(0,calc(100% - var(--peek)),0);width:420px}.sheet[data-state="collapsed"]{transform:translate3d(0,calc(100% - var(--peek)),0)}.sheet[data-state="half"]{transform:translate3d(0,calc(100% - var(--half)),0)}.sheet[data-state="expanded"]{transform:translate3d(0,0,0)}.location-card{left:1.15rem}.topbar{left:1.15rem;right:1.15rem}}@media(max-width:520px){.sheet{--peek:178px!important;--half:590px!important}.topbar{left:.55rem;right:.55rem}.brand small{display:none}.location-card{left:.55rem;right:.55rem;width:auto;top:4.75rem}.location-card strong{font-size:.9rem}.sheet{width:calc(100vw - .7rem);height:78dvh}.sheet h1{font-size:1.22rem}.services{display:flex;overflow-x:auto;scroll-snap-type:x mandatory}.svc{min-width:155px;scroll-snap-align:start}.nav-mini{display:flex;position:absolute;z-index:11;left:.55rem;right:.55rem;bottom:.45rem;gap:.2rem;justify-content:space-around;border-radius:22px;padding:.3rem}.nav-mini a{display:flex;flex-direction:column;align-items:center;gap:.05rem;border-radius:15px;padding:.38rem .42rem;text-decoration:none;color:#dce9f6;font-size:.6rem;font-weight:900}.nav-mini a.is-active{background:rgba(39,240,131,.13);color:#27f083}}

/* Mobile cockpit correction: Pinterest/Wolt style composition, no clipped sheets. */
.city-map:after{content:"";position:absolute;inset:0;pointer-events:none;background:radial-gradient(circle at 45% 38%,rgba(57,204,255,.10),transparent 24%),radial-gradient(circle at 60% 64%,rgba(39,240,131,.08),transparent 28%)}
.location-card .fix{border:0;cursor:pointer;font-family:inherit}.location-card .fix:disabled{opacity:.58;cursor:not-allowed}
.sheet[data-state="collapsed"] .sheet-scroll{display:none}.sheet[data-state="collapsed"]{height:auto;min-height:126px;padding-bottom:calc(.86rem + env(safe-area-inset-bottom))}.sheet[data-state="collapsed"] .sheet-handle{margin-bottom:.7rem}.sheet[data-state="collapsed"] .sheet-mini{padding-bottom:.12rem}.sheet[data-state="collapsed"] .sheet-mini b{font-size:1.02rem}.sheet[data-state="collapsed"] .sheet-mini small{font-size:.74rem}.sheet-head .chip{max-width:112px;text-align:center;white-space:normal}.location-card{transition:transform .24s ease,opacity .24s ease}.city:has(.sheet[data-state="expanded"]) .location-card{transform:translateY(-8px);opacity:.18;pointer-events:none}
@media(max-width:520px){
  html,body{height:100%;overflow:hidden!important;background:#050d18!important}
  .city{height:100svh;min-height:100svh;overflow:hidden;background:linear-gradient(180deg,#06131e 0%,#030912 58%,#050b13 100%)}
  .city-map{height:100svh!important;opacity:.34;filter:saturate(.82) contrast(1.05) brightness(.82)}
  .city-shade{background:radial-gradient(circle at 48% 34%,rgba(57,204,255,.09),transparent 28%),linear-gradient(180deg,rgba(4,13,23,.22),rgba(4,10,18,.72) 54%,rgba(2,7,12,.95))}
  .topbar{top:calc(.62rem + env(safe-area-inset-top));left:.82rem;right:.82rem}.brand{padding:.38rem .58rem}.brand .logo{width:38px;height:38px}.brand strong{font-size:.95rem}.status{padding:.48rem .62rem;font-size:.78rem}.bell{width:40px;height:40px}
  .location-card{top:calc(4.7rem + env(safe-area-inset-top));left:.82rem;right:.82rem;width:auto;border-radius:24px;padding:1rem 1.05rem;background:rgba(7,17,30,.58)}
  .location-card strong{font-size:1.03rem}.location-card p{font-size:.84rem;line-height:1.46}.location-card .fix{min-height:2.65rem;padding:0 .95rem}
  .sheet{left:.62rem;right:.62rem;bottom:calc(5.7rem + env(safe-area-inset-bottom));width:auto!important;height:min(68svh,620px);border-radius:28px 28px 24px 24px;padding:.62rem .9rem .9rem;transform:translate3d(0,calc(100% - var(--peek)),0)!important;box-shadow:0 -18px 60px rgba(0,0,0,.34)}
  .sheet[data-state="collapsed"]{--peek:112px!important;transform:translate3d(0,calc(100% - 112px),0)!important;min-height:112px}.sheet[data-state="half"]{transform:translate3d(0,calc(100% - min(430px,48svh)),0)!important}.sheet[data-state="expanded"]{height:calc(100svh - 7.6rem - env(safe-area-inset-top) - env(safe-area-inset-bottom));transform:translate3d(0,0,0)!important}
  .sheet-scroll{height:calc(100% - 22px);padding-bottom:1.4rem}.sheet h1{font-size:1.28rem}.sheet-head{align-items:center}.state-title{padding:.76rem;border-radius:20px}.state-title>span{font-size:.62rem;max-width:90px;overflow:hidden;text-overflow:ellipsis}.lifecycle{padding:.2rem}.lifecycle span{font-size:.56rem}.demand{padding:.72rem}.qrow{min-height:3rem}.planned-offline{padding:.68rem}.services{padding-bottom:.6rem}
  .swipe{height:58px;margin-top:.72rem}.knob{width:44px;height:44px}.swipe-label{font-size:.88rem;padding-left:2.4rem;justify-content:center;text-align:center}
  .nav-mini{display:flex;position:fixed;z-index:30;left:.82rem;right:.82rem;bottom:calc(.62rem + env(safe-area-inset-bottom));height:68px;align-items:center;gap:.25rem;justify-content:space-around;border-radius:25px;padding:.38rem;background:rgba(5,14,25,.82);backdrop-filter:blur(24px);box-shadow:0 14px 50px rgba(0,0,0,.36)}
  .nav-mini a{flex:1;min-width:0;height:54px;justify-content:center;color:#dce9f6;font-size:.62rem}.nav-mini a.is-active{background:rgba(39,240,131,.16);box-shadow:inset 0 0 0 1px rgba(39,240,131,.1)}
}
@media(min-width:900px){.sheet[data-state="collapsed"]{transform:translate3d(0,calc(100% - 126px),0)}}

</style>
@endpush

<div class="city" data-city-cockpit>
    <div id="worker-live-map" class="city-map" role="application" aria-label="Live Narvik worker map"></div>
    <div class="city-shade" aria-hidden="true"></div>
    <div class="city-backdrop" data-sheet-backdrop aria-hidden="true"></div>

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
        <button class="fix" type="button" data-enable-location data-ping-url="{{ route('worker.location-pings.store') }}" data-csrf="{{ csrf_token() }}">{{ request()->secure() ? 'Enable phone GPS' : 'HTTPS required for GPS' }}</button>
    </aside>

    <section class="sheet glass" data-sheet data-state="collapsed" style="--peek:218px;--half:min(610px,72dvh)" aria-label="Worker control sheet">
        <div class="sheet-handle" data-sheet-handle aria-hidden="true"></div>
        <div class="sheet-mini" data-sheet-mini>
            <span><b>{{ $state['title'] }} · {{ $sheetMarketLabel }}</b><small>{{ $activeOrder ? 'Open current job while keeping map context.' : $sheetPrimaryLabel }}</small></span>
            <span class="sheet-grabber">⌃</span>
        </div>
        <div class="sheet-scroll">
            <div class="sheet-head">
                <div><h1>Narvik</h1><p class="muted micro" style="margin:.15rem 0 0">{{ $sheetMarketCopy }}</p></div>
                <span class="chip">{{ $sheetMarketLabel }}</span>
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
                <div class="demand-label"><div><strong>{{ $orders->count() ? 'Assigned work active' : 'Demand forecast not connected' }}</strong><p class="muted micro" style="margin:.2rem 0 0">{{ $orders->count() ? 'You have real assigned work from dispatch. Open Current Job for the next action.' : 'Hourly demand bars are intentionally disabled until a real demand engine exists.' }}</p></div><div class="bars {{ $orders->count() ? '' : 'is-disabled' }}" aria-hidden="true"><i></i><i></i><i></i></div></div>
                <div class="timeline {{ $orders->count() ? '' : 'is-disabled' }}" aria-hidden="true"><i style="height:18px"></i><i style="height:22px"></i><i></i><i></i><i></i><i></i><i style="height:16px"></i></div>
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
            <div class="quick">
                <h2>Fast work controls</h2>
                @if($orders->skip(1)->first())
                    <a class="qrow" href="{{ route('worker.orders.show', $orders->skip(1)->first()) }}"><span><b>Previous assignment</b><small>Review the latest real assigned job.</small></span><em>›</em></a>
                @else
                    <div class="qrow is-disabled"><span><b>Previous assignment</b><small>No previous real assignment available for this worker.</small></span><em>—</em></div>
                @endif
                <div class="planned-offline"><span><b>Scheduled offline</b><small class="muted micro">Not configured yet. Use manual offline until schedule rules are connected.</small></span><button type="button" disabled>Disabled</button></div>
                <a class="qrow" href="{{ route('worker.support.index') }}"><span><b>Support</b><small>One-tap operational help and tickets.</small></span><em>›</em></a>
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
            <div class="swipe" data-swipe-presence data-form="presence-form" @if($activeOrder) data-url="{{ route('worker.orders.show', $activeOrder) }}" @endif role="group" aria-label="{{ $sheetPrimaryLabel }}">
                <div class="swipe-label">{{ $sheetPrimaryLabel }}</div>
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
 const sheet=document.querySelector('[data-sheet]'), handle=document.querySelector('[data-sheet-handle]'), mini=document.querySelector('[data-sheet-mini]'), backdrop=document.querySelector('[data-sheet-backdrop]');
 if(sheet&&handle){let startY=0,startT=0,current='collapsed';const order=['collapsed','half','expanded'];const vh=()=>window.innerHeight||document.documentElement.clientHeight;const offset=s=>s==='expanded'?0:(s==='half'?Math.max(0,vh()-(Math.min(610,vh()*.72))):Math.max(0,vh()-218));const set=s=>{current=s;sheet.dataset.state=s;backdrop?.classList.toggle('is-visible',s==='expanded')};const down=e=>{startY=e.clientY;startT=offset(current);sheet.classList.add('is-dragging');e.currentTarget.setPointerCapture?.(e.pointerId)};const move=e=>{if(!sheet.classList.contains('is-dragging'))return;const next=Math.max(0,Math.min(vh()-130,startT+e.clientY-startY));const x=window.matchMedia('(min-width:900px)').matches?'0':'-50%';sheet.style.transform='translate3d('+x+','+next+'px,0)'};const up=e=>{if(!sheet.classList.contains('is-dragging'))return;sheet.classList.remove('is-dragging');sheet.style.transform='';const dy=e.clientY-startY;const idx=order.indexOf(current);if(dy<-44)set(order[Math.min(idx+1,2)]);else if(dy>44)set(order[Math.max(idx-1,0)]);else set(current)};[handle,mini].filter(Boolean).forEach(node=>{node.addEventListener('pointerdown',down);node.addEventListener('pointermove',move);node.addEventListener('pointerup',up);node.addEventListener('click',()=>set(current==='collapsed'?'half':current==='half'?'expanded':'half'))});backdrop?.addEventListener('click',()=>set('half'));sheet.addEventListener('dblclick',e=>{if(e.target.closest('a,button,input,textarea,select'))return;set(current==='expanded'?'half':'expanded')});}
 document.querySelectorAll('[data-enable-location]').forEach(btn=>{btn.addEventListener('click',async()=>{if(!window.isSecureContext||!navigator.geolocation){alert('Телефон не покажет запрос геолокации на http://185.230.64.8. Нужен HTTPS-домен или HTTPS-настройка сервера.');return;}btn.disabled=true;btn.textContent='Requesting GPS…';navigator.geolocation.getCurrentPosition(async pos=>{try{const res=await fetch(btn.dataset.pingUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':btn.dataset.csrf,'Accept':'application/json'},body:JSON.stringify({latitude:pos.coords.latitude,longitude:pos.coords.longitude,accuracy_meters:pos.coords.accuracy,heading:pos.coords.heading,speed_mps:pos.coords.speed,consent:'accepted'})});btn.textContent=res.ok?'GPS shared once':'GPS save failed';}catch(e){btn.textContent='GPS save failed';}},err=>{btn.disabled=false;btn.textContent=err.code===1?'GPS denied':'GPS unavailable';},{enableHighAccuracy:true,timeout:12000,maximumAge:0});});});
 document.querySelectorAll('[data-swipe-presence]').forEach(root=>{const knob=root.querySelector('.knob'),form=document.getElementById(root.dataset.form),label=root.querySelector('.swipe-label');let drag=false,start=0,x=0,max=0;const set=v=>{x=Math.max(0,Math.min(v,max));knob.style.transform='translateX('+x+'px)';root.style.setProperty('--progress',max?x/max:0)};const reset=()=>{set(0)};knob.addEventListener('pointerdown',e=>{drag=true;start=e.clientX;max=root.clientWidth-knob.clientWidth-14;knob.setPointerCapture(e.pointerId)});knob.addEventListener('pointermove',e=>{if(drag)set(e.clientX-start)});knob.addEventListener('pointerup',()=>{if(!drag)return;drag=false;if(x>max*.74){label.textContent=root.dataset.url?'Opening current job…':'Updating presence…'; if(root.dataset.url){window.location.href=root.dataset.url}else{form?.submit()}}else reset()});knob.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();if(root.dataset.url){window.location.href=root.dataset.url}else{form?.submit()}})});
})();
</script>
@endpush
@endsection
