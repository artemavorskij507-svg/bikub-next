@extends('worker.layout')
@section('title', 'Worker Dashboard')
@section('body-class', 'worker-dashboard-mapfirst')
@section('content')
@php
    $firstName = trim(str($user->name ?? 'Worker')->before(' ')->toString()) ?: 'Worker';
    $profileStatus = $user->workerProfile?->status ?? 'missing';
    $profileApproved = $profileStatus === 'approved';
    $availabilityStatus = $availability?->status ?? 'offline';
    $online = in_array($availabilityStatus, ['online', 'available'], true);
    $activeAssignment = $activeOrder?->activeDispatchAssignment();
    $events = $activeOrder ? $activeOrder->events->pluck('event_type')->all() : [];
    $hasAccepted = in_array('worker.assignment.accepted', $events, true) || in_array('worker.started', $events, true) || in_array('worker.arrived_pickup', $events, true) || in_array('worker.picked_up', $events, true) || in_array('worker.arrived_dropoff', $events, true);
    $hasPickedUp = in_array('worker.picked_up', $events, true);
    $hasArrivedDropoff = in_array('worker.arrived_dropoff', $events, true);
    $hasCompletionProof = $activeOrder ? $activeOrder->completionProofs->isNotEmpty() : false;
    $uiState = ! $online ? 'Offline' : 'Waiting';
    if ($online && $activeOrder) {
        $uiState = match (true) {
            $hasArrivedDropoff && ! $hasCompletionProof => 'Completion Proof',
            $hasPickedUp => 'Navigate to Dropoff',
            $hasAccepted => 'Navigate to Pickup',
            default => 'Assigned',
        };
    }
    $intake = $activeOrder?->metadata['intake'] ?? [];
    $pickup = $intake['pickup_address'] ?? $intake['vehicle_location'] ?? $intake['task_location'] ?? null;
    $dropoff = $intake['dropoff_address'] ?? $intake['destination_address'] ?? null;
    $pickupLat = $intake['pickup_latitude'] ?? $intake['pickup_lat'] ?? null;
    $pickupLng = $intake['pickup_longitude'] ?? $intake['pickup_lng'] ?? null;
    $dropoffLat = $intake['dropoff_latitude'] ?? $intake['dropoff_lat'] ?? $intake['destination_latitude'] ?? null;
    $dropoffLng = $intake['dropoff_longitude'] ?? $intake['dropoff_lng'] ?? $intake['destination_longitude'] ?? null;
    $lastPingAge = $lastPing?->created_at ? now()->diffInSeconds($lastPing->created_at) : null;
    $staleSeconds = (int) ($mapConfig['stale_seconds'] ?? 120);
    $gpsFresh = ! is_null($lastPingAge) && $lastPingAge <= $staleSeconds;
    $gpsState = $gpsFresh ? 'Fresh' : ($lastPing ? 'Stale' : 'No ping');
    $gpsAgeLabel = $lastPing ? $lastPing->created_at->diffForHumans() : 'No real ping yet';
    $gpsAccuracyLabel = $lastPing?->accuracy_meters ? number_format((float) $lastPing->accuracy_meters, 0).' m' : 'Unavailable';
    $payoutReady = (bool) ($payoutProfile['ready'] ?? false);
    $readiness = [
        ['label' => 'Profile', 'ok' => $profileApproved, 'detail' => str($profileStatus)->replace('_',' ')->title()],
        ['label' => 'Presence', 'ok' => $online, 'detail' => $online ? 'Online' : 'Offline'],
        ['label' => 'GPS', 'ok' => $gpsFresh, 'detail' => $gpsAgeLabel],
        ['label' => 'Payout', 'ok' => $payoutReady, 'detail' => $payoutReady ? 'Ready' : 'Manual review'],
    ];
    $mapPayload = [
        'center' => [(float)($mapConfig['center_lat'] ?? 68.4385), (float)($mapConfig['center_lng'] ?? 17.4272)],
        'zoom' => (int)($mapConfig['default_zoom'] ?? 11),
        'csrf' => csrf_token(),
        'pingUrl' => route('worker.location-pings.store'),
        'orderId' => $activeOrder?->id,
        'online' => $online,
        'lastPing' => $lastPing ? ['lat'=>(float)$lastPing->latitude,'lng'=>(float)$lastPing->longitude,'accuracy'=>(float)$lastPing->accuracy_meters,'label'=>$gpsAgeLabel] : null,
        'pickup' => ($pickupLat && $pickupLng) ? ['lat'=>(float)$pickupLat,'lng'=>(float)$pickupLng,'label'=>'Pickup'] : null,
        'dropoff' => ($dropoffLat && $dropoffLng) ? ['lat'=>(float)$dropoffLat,'lng'=>(float)$dropoffLng,'label'=>'Drop-off'] : null,
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQeq9Uq4ik9xkclle2n2E1Jb2fYFhI4=" crossorigin="">
<style>
body.worker-dashboard-mapfirst{overflow:hidden;background:#05101c}body.worker-dashboard-mapfirst .worker-shell{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-sidebar,body.worker-dashboard-mapfirst .worker-topbar,body.worker-dashboard-mapfirst .worker-bottom{display:none!important}body.worker-dashboard-mapfirst .worker-main{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-content{padding:0!important;min-height:100dvh}.wv2{position:relative;width:100vw;height:100dvh;overflow:hidden;background:#06111e}.wv2-main{position:absolute;inset:0;z-index:1}.wv2-map-stage{position:absolute;inset:0;background:#06111e}.wv2-map{position:absolute;inset:0;z-index:1;width:100%;height:100%}.leaflet-container{background:#06111e;color:#dce9f6}.leaflet-tile{filter:brightness(.64) saturate(.78) contrast(1.18) hue-rotate(160deg)}.wv2-map-vignette{position:absolute;inset:0;z-index:2;pointer-events:none;background:linear-gradient(180deg,rgba(3,10,18,.72),rgba(3,10,18,.08) 22%,rgba(3,10,18,.06) 58%,rgba(3,10,18,.86)),radial-gradient(circle at 18% 18%,rgba(var(--brand-rgb),.18),transparent 30%)}.wv2-appbar{position:absolute;z-index:10;top:max(.8rem,env(safe-area-inset-top));left:.9rem;right:.9rem;display:flex;align-items:center;justify-content:space-between;gap:.8rem;pointer-events:none}.wv2-brand,.wv2-nav,.wv2-presence{pointer-events:auto;border:1px solid rgba(148,163,184,.14);background:rgba(5,14,25,.74);backdrop-filter:blur(18px);box-shadow:0 18px 50px rgba(0,0,0,.24)}.wv2-brand{display:flex;align-items:center;gap:.65rem;border-radius:999px;padding:.48rem .68rem .48rem .5rem}.wv2-logo{display:grid;place-items:center;width:36px;height:36px;border-radius:999px;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#03130d;font-weight:950}.wv2-brand strong{font-size:.88rem;line-height:1}.wv2-brand span:last-child{display:block;color:var(--muted);font-size:.68rem;font-weight:800}.wv2-nav{display:flex;gap:.25rem;border-radius:999px;padding:.32rem}.wv2-nav a{display:flex;align-items:center;gap:.35rem;border-radius:999px;padding:.52rem .72rem;color:#dbe8f6;text-decoration:none;font-weight:900;font-size:.78rem}.wv2-nav a.is-active{background:rgba(var(--brand-rgb),.13);color:var(--green)}.wv2-presence{display:flex;align-items:center;gap:.55rem;border-radius:999px;padding:.5rem .72rem;color:var(--green);font-weight:950}.wv2-presence-dot{width:.52rem;height:.52rem;border-radius:999px;background:var(--green);box-shadow:0 0 12px rgba(var(--brand-rgb),.75)}.wv2-focus{position:absolute;z-index:8;left:1rem;top:6.25rem;width:min(31rem,calc(100vw - 2rem));pointer-events:none}.wv2-focus-card{border:1px solid rgba(148,163,184,.14);border-radius:26px;background:rgba(5,14,25,.76);backdrop-filter:blur(20px);box-shadow:0 24px 80px rgba(0,0,0,.32);padding:1rem}.wv2-kicker{margin:0;color:var(--green);font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.wv2-focus h2{margin:.12rem 0;font-size:clamp(2.3rem,5vw,4.6rem);line-height:.86;letter-spacing:-.07em}.wv2-primary{margin:.7rem 0 0;padding:.7rem .8rem;border-radius:16px;background:rgba(var(--brand-rgb),.12);border:1px solid rgba(var(--brand-rgb),.22);color:#e7fff4;font-size:1rem;font-weight:950}.wv2-sub{margin:.5rem 0 0;color:#a9bdd1}.wv2-controls{position:absolute;z-index:9;right:1rem;top:6.2rem;width:330px;display:grid;gap:.6rem}.wv2-floating{border:1px solid rgba(148,163,184,.14);border-radius:22px;background:rgba(5,14,25,.76);backdrop-filter:blur(20px);box-shadow:0 24px 70px rgba(0,0,0,.26);padding:.82rem}.wv2-floating h3{margin:.18rem 0 .28rem;font-size:1rem}.wv2-bottom-dock{position:absolute;z-index:9;left:1rem;right:1rem;bottom:max(1rem,env(safe-area-inset-bottom));display:flex;justify-content:space-between;align-items:flex-end;gap:.8rem;pointer-events:none}.wv2-bottom-dock>*{pointer-events:auto}.wv2-status-strip{display:flex;gap:.5rem;max-width:55rem;overflow:auto}.wv2-pill{min-width:8.5rem;border:1px solid rgba(148,163,184,.13);border-radius:17px;background:rgba(5,14,25,.72);backdrop-filter:blur(16px);padding:.65rem .75rem}.wv2-pill span,.wv2-card-kicker{display:block;color:var(--muted);font-size:.62rem;font-weight:950;text-transform:uppercase;letter-spacing:.08em}.wv2-pill strong{display:block;margin-top:.18rem}.wv2-swipe-wrap{width:min(390px,calc(100vw - 2rem));border:1px solid rgba(var(--brand-rgb),.22);border-radius:24px;background:rgba(5,14,25,.82);backdrop-filter:blur(18px);box-shadow:0 24px 70px rgba(0,0,0,.28);padding:.75rem}.wv2-swipe{position:relative;user-select:none;touch-action:none;overflow:hidden;border:1px solid rgba(var(--brand-rgb),.30);border-radius:999px;background:rgba(3,12,22,.9);height:58px;display:flex;align-items:center;padding:5px}.wv2-swipe.is-offline{border-color:rgba(251,113,133,.34)}.wv2-swipe-track{position:absolute;inset:0;display:grid;place-items:center;font-weight:950;color:#eff6ff;letter-spacing:.02em}.wv2-swipe-knob{position:relative;z-index:2;width:48px;height:48px;border-radius:999px;border:0;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff;font-size:1.05rem;font-weight:950;box-shadow:0 12px 34px rgba(var(--brand-rgb),.28);cursor:grab}.wv2-swipe.is-offline .wv2-swipe-knob{background:linear-gradient(135deg,#fb7185,#b91c1c)}.wv2-swipe-status{margin:.45rem 0 0;font-size:.76rem;color:var(--muted)}.wv2-live-btn{width:100%;border-color:rgba(85,217,255,.35);background:rgba(85,217,255,.1)}.wv2-action-note{margin:.42rem 0 0;color:var(--muted);font-size:.78rem}.wv2-current-card{position:absolute;z-index:11;left:50%;bottom:max(1rem,env(safe-area-inset-bottom));transform:translateX(-50%);width:min(520px,calc(100vw - 2rem));border:1px solid rgba(var(--brand-rgb),.28);border-radius:26px;background:rgba(5,14,25,.86);backdrop-filter:blur(20px);box-shadow:0 30px 90px rgba(0,0,0,.36);padding:1rem}.wv2-job-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.45rem;margin:.65rem 0}.wv2-job-meta div{border:1px solid rgba(148,163,184,.1);border-radius:13px;padding:.55rem}.wv2-waiting-empty{border:1px solid rgba(var(--brand-rgb),.18);border-radius:18px;background:radial-gradient(circle at 50% 0,rgba(var(--brand-rgb),.13),transparent 45%),rgba(255,255,255,.035);padding:1rem;text-align:center}.wv2-waiting-empty strong{display:block;font-size:1.05rem}.wv2-step{display:flex;align-items:center;gap:.55rem;padding:.42rem .2rem}.wv2-step i{display:grid;place-items:center;width:1.3rem;height:1.3rem;border-radius:999px;background:rgba(148,163,184,.1);font-style:normal;font-size:.7rem}.wv2-step.is-current i{background:rgba(var(--brand-rgb),.18);color:var(--green)}.wv2-step strong{font-size:.86rem}.wv2-step span{display:none}.wv2-marker-worker,.wv2-marker-last,.wv2-marker-stop{display:grid;place-items:center;width:30px;height:30px;border-radius:999px;color:#03120d;font-weight:950}.wv2-marker-worker{background:var(--green);box-shadow:0 0 0 10px rgba(var(--brand-rgb),.18),0 0 34px rgba(var(--brand-rgb),.5)}.wv2-marker-last{background:#55d9ff}.wv2-marker-stop{background:#f5bd54}.wv2-zone-label{font-size:.76rem;font-weight:950;color:#dce9f6;text-shadow:0 2px 8px #000}.wv2-mobile-nav{position:absolute;z-index:20;left:.75rem;right:.75rem;bottom:max(.65rem,env(safe-area-inset-bottom));display:none;grid-template-columns:repeat(5,1fr);gap:.3rem;border:1px solid rgba(148,163,184,.14);border-radius:24px;background:rgba(5,14,25,.84);backdrop-filter:blur(18px);padding:.4rem}.wv2-mobile-nav a{display:flex;flex-direction:column;align-items:center;gap:.1rem;border-radius:16px;padding:.48rem .25rem;text-decoration:none;color:#dbe8f6;font-size:.66rem;font-weight:850}.wv2-mobile-nav a.is-active{background:rgba(var(--brand-rgb),.12);color:var(--green)}@media(max-width:1180px){.wv2-appbar{left:.7rem;right:.7rem}.wv2-nav a span:last-child{display:none}.wv2-controls{top:auto;right:.75rem;bottom:6rem;width:300px}.wv2-focus{top:5.6rem}.wv2-status-strip{display:none}.wv2-bottom-dock{justify-content:flex-end}}@media(max-width:760px){.wv2-appbar{top:.5rem}.wv2-brand strong,.wv2-brand span:last-child,.wv2-nav{display:none}.wv2-brand{padding:.42rem}.wv2-focus{top:4.5rem;left:.65rem;width:calc(100vw - 1.3rem)}.wv2-focus-card{border-radius:20px;padding:.82rem}.wv2-focus h2{font-size:2.55rem}.wv2-sub{display:none}.wv2-controls{left:.65rem;right:.65rem;bottom:5.6rem;width:auto}.wv2-floating{padding:.68rem;border-radius:18px}.wv2-floating.is-state,.wv2-floating.is-readiness{display:none}.wv2-bottom-dock{display:block;left:.65rem;right:.65rem;bottom:5.6rem}.wv2-swipe-wrap{width:100%;padding:.6rem}.wv2-current-card{bottom:5.8rem}.wv2-mobile-nav{display:grid}.leaflet-control-zoom{display:none}}
</style>
@endpush

<div class="wv2" data-dashboard-v2>
  <main class="wv2-main">
    @include('worker.dashboard.live-map')
  </main>
  @include('worker.dashboard.swipe-presence')
  <section class="wv2-controls" aria-label="Compact operational controls">
    @include('worker.dashboard.state-machine-panel')
    @include('worker.dashboard.readiness-panel')
    @include('worker.dashboard.quick-actions')
  </section>
  @include('worker.dashboard.active-job-card')
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function(){
 const cfg=@json($mapPayload), csrf=cfg.csrf;
 const el=document.getElementById('worker-live-map'); if(!el || !window.L) return;
 const map=L.map(el,{zoomControl:false,attributionControl:false}).setView(cfg.center,cfg.zoom||11);
 L.control.zoom({position:'bottomright'}).addTo(map); L.control.attribution({prefix:false,position:'bottomleft'}).addTo(map);
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
 const refresh=()=>map.invalidateSize(); setTimeout(refresh,80); setTimeout(refresh,300); setTimeout(refresh,700); window.addEventListener('resize',refresh);
 const narvik=[[68.54,17.18],[68.49,17.62],[68.35,17.69],[68.31,17.25]];
 const ballangen=[[68.41,16.75],[68.35,17.05],[68.22,16.98],[68.26,16.68]];
 L.polygon(narvik,{color:'#34e69a',weight:2,fillColor:'#34e69a',fillOpacity:.10,dashArray:'8 8'}).addTo(map);
 L.polygon(ballangen,{color:'#55d9ff',weight:2,fillColor:'#55d9ff',fillOpacity:.08,dashArray:'8 8'}).addTo(map);
 L.marker([68.4385,17.4272],{interactive:false,icon:L.divIcon({className:'wv2-zone-label',html:'Narvik / Ballangen pilot zone'})}).addTo(map);
 const icon=(cls,txt)=>L.divIcon({className:'',html:'<span class="'+cls+'">'+txt+'</span>',iconSize:[30,30],iconAnchor:[15,15]});
 let bounds=[];
 function addMarker(point, cls, txt, popup){ if(!point) return; const m=L.marker([point.lat,point.lng],{icon:icon(cls,txt)}).addTo(map).bindPopup(popup); bounds.push([point.lat,point.lng]); return m; }
 addMarker(cfg.lastPing,'wv2-marker-last','●','Last real GPS ping: '+(cfg.lastPing?.label||''));
 addMarker(cfg.pickup,'wv2-marker-stop','P','Pickup marker from real coordinates');
 addMarker(cfg.dropoff,'wv2-marker-stop','D','Drop-off marker from real coordinates');
 if(bounds.length){ map.fitBounds(bounds,{padding:[90,90],maxZoom:14}); setTimeout(refresh,180); }
 const status=document.getElementById('wv2-gps-status'), locate=document.getElementById('wv2-locate');
 locate?.addEventListener('click',()=>{
   if(!cfg.online){ status.textContent='Go online before sharing location.'; return; }
   if(!window.isSecureContext){ status.textContent='Secure context required for browser GPS.'; return; }
   if(!navigator.geolocation){ status.textContent='Browser geolocation unavailable.'; return; }
   status.textContent='Requesting browser permission…';
   navigator.geolocation.getCurrentPosition(async pos=>{
     const p={lat:pos.coords.latitude,lng:pos.coords.longitude};
     addMarker(p,'wv2-marker-worker','W','Current position from explicit browser permission'); map.setView([p.lat,p.lng],14);
     try{
       status.textContent='Sending one real GPS ping…';
       const res=await fetch(cfg.pingUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:JSON.stringify({order_id:cfg.orderId,latitude:p.lat,longitude:p.lng,accuracy_meters:pos.coords.accuracy,heading:pos.coords.heading,speed_mps:pos.coords.speed,captured_at:new Date(pos.timestamp).toISOString(),consent:true})});
       const data=await res.json();
       status.textContent=res.ok?'Current position shared. No background GPS is active.':'GPS rejected: '+Object.values(data.errors||{error:data.message||'server rejected ping'}).flat().join(' ');
     }catch(e){ status.textContent='GPS request failed while contacting server.'; }
   }, err=>{ status.textContent=err.code===1?'Location permission denied.':'GPS failed: '+err.message; }, {enableHighAccuracy:true,timeout:15000,maximumAge:0});
 });
 document.querySelectorAll('[data-swipe-presence]').forEach(root=>{
   const knob=root.querySelector('.wv2-swipe-knob'), form=document.getElementById(root.dataset.form), label=root.querySelector('.wv2-swipe-track'); let dragging=false,start=0,x=0,max=0;
   function set(v){x=Math.max(0,Math.min(v,max)); knob.style.transform='translateX('+x+'px)';}
   function reset(){set(0)}
   knob.addEventListener('pointerdown',e=>{dragging=true;start=e.clientX;max=root.clientWidth-knob.clientWidth-12;knob.setPointerCapture(e.pointerId);});
   knob.addEventListener('pointermove',e=>{if(!dragging)return;set(e.clientX-start);});
   knob.addEventListener('pointerup',()=>{if(!dragging)return;dragging=false;if(x>max*.72){label.textContent='Submitting…'; form?.submit();}else reset();});
   knob.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault(); form?.submit();}});
 });
})();
</script>
@endpush
@endsection
