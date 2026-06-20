@extends('worker.layout')
@section('title', 'Worker Dashboard')
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
        ['label' => 'Worker profile', 'ok' => $profileApproved, 'detail' => str($profileStatus)->replace('_',' ')->title()],
        ['label' => 'Presence', 'ok' => $online, 'detail' => $online ? 'Online — visible to dispatch' : 'Offline — not receiving work'],
        ['label' => 'GPS', 'ok' => $gpsFresh, 'detail' => $gpsAgeLabel.' · '.$gpsAccuracyLabel],
        ['label' => 'Payout profile', 'ok' => $payoutReady, 'detail' => $payoutReady ? 'Ready for settlement review' : 'Manual review required'],
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
.wv2{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(340px,.72fr);gap:1rem}.wv2-main,.wv2-side{display:grid;gap:1rem}.wv2-hero{position:relative;overflow:hidden;border:1px solid rgba(148,163,184,.15);border-radius:28px;background:linear-gradient(145deg,rgba(9,23,39,.98),rgba(4,10,19,.98));box-shadow:0 30px 90px rgba(0,0,0,.34);padding:1rem}.wv2-map-frame{position:relative;min-height:620px;border-radius:24px;overflow:hidden;border:1px solid rgba(85,217,255,.2);background:#06111e}.wv2-map{position:absolute;inset:0;z-index:1}.leaflet-container{background:#06111e;color:#dce9f6}.leaflet-tile{filter:brightness(.62) saturate(.7) contrast(1.15) hue-rotate(165deg)}.wv2-map-overlay{position:absolute;z-index:5;inset:1rem;pointer-events:none;display:flex;flex-direction:column;justify-content:space-between}.wv2-top{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start}.wv2-title{max-width:38rem;border:1px solid rgba(148,163,184,.16);background:linear-gradient(135deg,rgba(5,14,25,.88),rgba(8,24,39,.74));backdrop-filter:blur(16px);border-radius:20px;padding:1rem}.wv2-title h2{margin:.15rem 0;font-size:clamp(2.1rem,4vw,4rem);line-height:.95;letter-spacing:-.055em}.wv2-title p{margin:.45rem 0 0}.wv2-state{display:flex;align-items:center;gap:.5rem;border:1px solid rgba(var(--brand-rgb),.25);background:rgba(5,14,25,.82);backdrop-filter:blur(14px);border-radius:999px;padding:.55rem .75rem;font-weight:950;color:var(--green);box-shadow:0 10px 30px rgba(0,0,0,.22)}.wv2-bottom{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;align-items:end}.wv2-panel{pointer-events:auto;border:1px solid rgba(148,163,184,.16);background:rgba(5,14,25,.88);backdrop-filter:blur(18px);border-radius:22px;padding:1rem}.wv2-panel h3{margin:.25rem 0 0;font-size:1.2rem}.wv2-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.6rem;margin-top:.8rem}.wv2-metric{border:1px solid rgba(148,163,184,.12);background:rgba(255,255,255,.04);border-radius:14px;padding:.7rem}.wv2-metric span,.wv2-card-kicker{display:block;color:var(--muted);font-size:.67rem;font-weight:950;text-transform:uppercase;letter-spacing:.08em}.wv2-metric strong{display:block;margin-top:.25rem}.wv2-card{border:1px solid var(--line);border-radius:22px;background:linear-gradient(145deg,rgba(12,25,42,.92),rgba(5,13,24,.94));box-shadow:0 20px 62px rgba(0,0,0,.26);padding:1rem}.wv2-card h3{margin:.25rem 0 .35rem;font-size:1.28rem}.wv2-action-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.6rem}.wv2-action{min-height:76px;justify-content:flex-start}.wv2-step{display:grid;grid-template-columns:2rem 1fr;gap:.7rem;padding:.7rem;border:1px solid rgba(148,163,184,.11);border-radius:15px;margin-top:.5rem}.wv2-step i{display:grid;place-items:center;width:2rem;height:2rem;border-radius:999px;background:rgba(148,163,184,.09);font-style:normal;font-weight:950}.wv2-step.is-current{border-color:rgba(var(--brand-rgb),.35);background:rgba(var(--brand-rgb),.07)}.wv2-step.is-current i{background:rgba(var(--brand-rgb),.16);color:var(--green)}.wv2-swipe{position:relative;user-select:none;touch-action:none;overflow:hidden;border:1px solid rgba(var(--brand-rgb),.28);border-radius:999px;background:rgba(3,12,22,.9);height:64px;display:flex;align-items:center;padding:6px}.wv2-swipe.is-offline{border-color:rgba(251,113,133,.3)}.wv2-swipe-track{position:absolute;inset:0;display:grid;place-items:center;font-weight:950;color:#dce9f6;letter-spacing:.02em}.wv2-swipe-knob{position:relative;z-index:2;width:52px;height:52px;border-radius:999px;border:0;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff;font-size:1.2rem;font-weight:950;box-shadow:0 12px 34px rgba(var(--brand-rgb),.28);cursor:grab}.wv2-swipe.is-offline .wv2-swipe-knob{background:linear-gradient(135deg,#fb7185,#b91c1c)}.wv2-swipe-status{margin:.55rem 0 0;font-size:.82rem}.wv2-job-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.5rem;margin:.8rem 0}.wv2-job-meta div{border:1px solid rgba(148,163,184,.1);border-radius:13px;padding:.6rem}.wv2-waiting{display:grid;place-items:center;text-align:center;min-height:210px;border:1px dashed rgba(148,163,184,.18);border-radius:18px;background:rgba(255,255,255,.025)}.wv2-live-btn{border-color:rgba(85,217,255,.35);background:rgba(85,217,255,.1)}.wv2-marker-worker,.wv2-marker-last,.wv2-marker-stop{display:grid;place-items:center;width:30px;height:30px;border-radius:999px;color:#03120d;font-weight:950}.wv2-marker-worker{background:var(--green);box-shadow:0 0 0 10px rgba(var(--brand-rgb),.18),0 0 34px rgba(var(--brand-rgb),.5)}.wv2-marker-last{background:#55d9ff}.wv2-marker-stop{background:#f5bd54}.wv2-zone-label{font-size:.76rem;font-weight:950;color:#dce9f6;text-shadow:0 2px 8px #000}.wv2-loading{position:absolute;z-index:20;inset:auto 1rem 1rem 1rem;border-radius:14px;background:rgba(5,14,25,.88);border:1px solid rgba(148,163,184,.16);padding:.7rem;color:var(--muted)}
@media(max-width:1180px){.wv2{grid-template-columns:1fr}.wv2-map-frame{min-height:560px}.wv2-bottom{grid-template-columns:1fr}.wv2-side{grid-template-columns:repeat(2,minmax(0,1fr))}.wv2-side .wv2-card:first-child{grid-column:1/-1}}
@media(max-width:720px){.wv2{gap:.75rem}.wv2-map-frame{min-height:calc(100dvh - 190px);border-radius:20px}.wv2-map-overlay{inset:.65rem}.wv2-title{padding:.75rem;border-radius:16px}.wv2-title h2{font-size:2.15rem}.wv2-top{display:grid}.wv2-bottom{align-items:stretch}.wv2-metrics{grid-template-columns:1fr}.wv2-side{grid-template-columns:1fr}.wv2-action-grid{grid-template-columns:1fr}.wv2-job-meta{grid-template-columns:1fr}.wv2-panel{padding:.75rem;border-radius:17px}.wv2-state{justify-self:start}.worker-content{padding:.6rem .55rem 6.2rem!important}}
</style>
@endpush

<div class="wv2" data-dashboard-v2>
  <main class="wv2-main">
    @include('worker.dashboard.live-map')
  </main>
  <aside class="wv2-side">
    @include('worker.dashboard.swipe-presence')
    @include('worker.dashboard.state-machine-panel')
    @include('worker.dashboard.active-job-card')
    @include('worker.dashboard.readiness-panel')
    @include('worker.dashboard.quick-actions')
  </aside>
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
 const narvik=[[68.54,17.18],[68.49,17.62],[68.35,17.69],[68.31,17.25]];
 const ballangen=[[68.41,16.75],[68.35,17.05],[68.22,16.98],[68.26,16.68]];
 L.polygon(narvik,{color:'#34e69a',weight:2,fillColor:'#34e69a',fillOpacity:.09,dashArray:'8 8'}).addTo(map);
 L.polygon(ballangen,{color:'#55d9ff',weight:2,fillColor:'#55d9ff',fillOpacity:.08,dashArray:'8 8'}).addTo(map);
 L.marker([68.4385,17.4272],{interactive:false,icon:L.divIcon({className:'wv2-zone-label',html:'Narvik / Ballangen work zone'})}).addTo(map);
 const icon=(cls,txt)=>L.divIcon({className:'',html:'<span class="'+cls+'">'+txt+'</span>',iconSize:[30,30],iconAnchor:[15,15]});
 let bounds=[];
 function addMarker(point, cls, txt, popup){ if(!point) return; const m=L.marker([point.lat,point.lng],{icon:icon(cls,txt)}).addTo(map).bindPopup(popup); bounds.push([point.lat,point.lng]); return m; }
 addMarker(cfg.lastPing,'wv2-marker-last','●','Last real GPS ping: '+(cfg.lastPing?.label||''));
 addMarker(cfg.pickup,'wv2-marker-stop','P','Pickup marker from real coordinates');
 addMarker(cfg.dropoff,'wv2-marker-stop','D','Drop-off marker from real coordinates');
 if(bounds.length){ map.fitBounds(bounds,{padding:[80,80],maxZoom:14}); }
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
