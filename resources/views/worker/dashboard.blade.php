@extends('worker.layout')
@section('title', 'Worker Dashboard')
@section('body-class', 'worker-dashboard-mapfirst')
@section('content')
@php
    $profileStatus = $user->workerProfile?->status ?? 'missing';
    $profileApproved = $profileStatus === 'approved';
    $availabilityStatus = $availability?->status ?? 'offline';
    $online = in_array($availabilityStatus, ['online', 'available'], true);
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
    $gpsState = $gpsFresh ? 'GPS fresh' : ($lastPing ? 'GPS stale' : 'No GPS');
    $gpsAgeLabel = $lastPing ? $lastPing->created_at->diffForHumans() : 'No real ping yet';
    $payoutReady = (bool) ($payoutProfile['ready'] ?? false);
    $wp = $user->workerProfile;
    $serviceLanes = [
        ['icon'=>'🛒','label'=>'Grocery delivery','key'=>'Active','enabled'=>(bool)($wp?->can_deliver),'state'=>(($wp?->can_deliver) ? 'active/configured' : 'not configured yet')],
        ['icon'=>'🍔','label'=>'Ready food','key'=>'Active','enabled'=>(bool)($wp?->can_deliver),'state'=>(($wp?->can_deliver) ? 'active/configured' : 'not configured yet')],
        ['icon'=>'🏗','label'=>'Bulky/materials','key'=>'Approval','enabled'=>(bool)($wp?->can_move),'state'=>(($wp?->can_move) ? 'active/configured' : 'partner approval needed')],
        ['icon'=>'🤝','label'=>'Personal errands','key'=>'Setup','enabled'=>(bool)($wp?->can_run_errands),'state'=>(($wp?->can_run_errands) ? 'active/configured' : 'not configured yet')],
        ['icon'=>'🛠','label'=>'GLF ByGG tasks','key'=>'Partner','enabled'=>false,'state'=>'partner approval + legal/accounting check needed'],
        ['icon'=>'📍','label'=>'Narvik pilot zone','key'=>'Pilot','enabled'=>$profileApproved,'state'=>$profileApproved ? 'active/configured' : 'approval needed'],
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
html,body{overflow:hidden!important}body.worker-dashboard-mapfirst{background:#06111e}body.worker-dashboard-mapfirst .worker-shell{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-sidebar,body.worker-dashboard-mapfirst .worker-topbar,body.worker-dashboard-mapfirst .worker-bottom{display:none!important}body.worker-dashboard-mapfirst .worker-main{display:block;min-height:100dvh}body.worker-dashboard-mapfirst .worker-content{padding:0!important;min-height:100dvh}.wv2{position:fixed;inset:0;width:100vw;height:100dvh;overflow:hidden;background:#06111e}.wv2-map-stage,.wv2-map{position:absolute!important;inset:0!important;width:100vw!important;height:100dvh!important;min-width:100vw!important;min-height:100dvh!important;max-width:none!important;max-height:none!important}.wv2-map{z-index:1}.leaflet-container{background:#06111e;color:#dce9f6;overflow:hidden!important}.leaflet-pane,.leaflet-tile,.leaflet-marker-icon,.leaflet-marker-shadow,.leaflet-tile-container,.leaflet-pane>svg,.leaflet-pane>canvas,.leaflet-zoom-box,.leaflet-image-layer,.leaflet-layer{position:absolute!important;left:0;top:0}.leaflet-container img.leaflet-tile{max-width:none!important;max-height:none!important;width:256px!important;height:256px!important;display:block!important}.leaflet-tile{filter:brightness(.92) saturate(.92) contrast(1.04) hue-rotate(156deg)}.leaflet-tile-pane{z-index:200}.leaflet-overlay-pane{z-index:400}.leaflet-marker-pane{z-index:600}.leaflet-control-container{position:absolute;inset:0;z-index:800;pointer-events:none}.leaflet-control{pointer-events:auto}.wv2-vignette{position:absolute;inset:0;z-index:2;pointer-events:none;background:linear-gradient(180deg,rgba(3,10,18,.20),transparent 16%,transparent 68%,rgba(3,10,18,.36)),radial-gradient(circle at 14% 18%,rgba(var(--brand-rgb),.08),transparent 32%)}.wv2-appbar{position:absolute;z-index:20;top:max(.55rem,env(safe-area-inset-top));left:.65rem;right:.65rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;pointer-events:none}.wv2-glass{pointer-events:auto;border:1px solid rgba(148,163,184,.12);background:rgba(5,14,25,.58);backdrop-filter:blur(18px);box-shadow:0 12px 36px rgba(0,0,0,.18)}.wv2-brand{display:flex;align-items:center;gap:.5rem;border-radius:999px;padding:.34rem .58rem;text-decoration:none}.wv2-logo{display:grid;place-items:center;width:30px;height:30px;border-radius:999px;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#03130d;font-weight:950}.wv2-brand strong{display:block;font-size:.78rem;line-height:1}.wv2-brand small{display:block;color:var(--muted);font-size:.58rem;font-weight:850}.wv2-nav{display:flex;gap:.16rem;border-radius:999px;padding:.25rem}.wv2-nav a{display:flex;align-items:center;gap:.28rem;border-radius:999px;padding:.42rem .62rem;color:#dbe8f6;text-decoration:none;font-weight:900;font-size:.72rem}.wv2-nav a.is-active{background:rgba(var(--brand-rgb),.13);color:var(--green)}.wv2-presence{display:flex;align-items:center;gap:.42rem;border-radius:999px;padding:.42rem .62rem;color:var(--green);font-weight:950;font-size:.78rem}.wv2-presence-dot{width:.48rem;height:.48rem;border-radius:999px;background:var(--green);box-shadow:0 0 12px rgba(var(--brand-rgb),.75)}.wv2-state-card{position:absolute;z-index:12;top:4.6rem;left:.75rem;width:min(360px,calc(100vw - 1.5rem));border-radius:20px;padding:.72rem;pointer-events:none}.wv2-kicker{margin:0;color:var(--green);font-size:.62rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.wv2-state-card h2{margin:.03rem 0;font-size:clamp(1.9rem,3vw,2.55rem);line-height:.9;letter-spacing:-.055em}.wv2-primary{display:block;margin:.42rem 0 0;padding:.52rem .64rem;border-radius:13px;background:rgba(var(--brand-rgb),.12);border:1px solid rgba(var(--brand-rgb),.22);color:#e7fff4;font-size:.86rem;font-weight:900}.wv2-status-pill{position:absolute;z-index:12;right:.75rem;top:4.6rem;display:flex;align-items:center;gap:.5rem;border-radius:999px;padding:.48rem .66rem;font-weight:900;font-size:.76rem;color:#dbe8f6;max-width:calc(100vw - 1.5rem)}.wv2-status-pill b{color:var(--green)}.wv2-sheet{display:none}.wv2-sheet h3{margin:.1rem 0;font-size:1rem}.wv2-sheet p{margin:.28rem 0 0;color:var(--muted);font-size:.84rem}.wv2-sheet-actions{display:flex;gap:.45rem;margin-top:.62rem}.wv2-bottom.is-assigned{border-color:rgba(var(--brand-rgb),.28)}.wv2-bottom.is-assigned h3{margin:.14rem 0;font-size:1.1rem}.wv2-bottom.is-assigned .wv2-sheet-actions .worker-btn{flex:1;justify-content:center}.wv2-bottom{position:absolute;z-index:18;left:50%;bottom:max(2.3rem,calc(env(safe-area-inset-bottom) + 2.3rem));transform:translateX(-50%);width:min(430px,calc(100vw - 1.4rem));border-radius:24px;padding:.72rem}.wv2-bottom.is-waiting{opacity:.92}.wv2-swipe{position:relative;user-select:none;touch-action:none;overflow:hidden;border:1px solid rgba(var(--brand-rgb),.32);border-radius:999px;background:rgba(3,12,22,.92);height:62px;display:flex;align-items:center;padding:6px}.wv2-swipe.is-offline{border-color:rgba(251,113,133,.34)}.wv2-swipe-track{position:absolute;inset:0;display:grid;place-items:center;font-weight:950;color:#eff6ff;letter-spacing:.02em}.wv2-swipe-knob{position:relative;z-index:2;width:50px;height:50px;border-radius:999px;border:0;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff;font-size:1.1rem;font-weight:950;box-shadow:0 12px 34px rgba(var(--brand-rgb),.28);cursor:grab}.wv2-swipe.is-offline .wv2-swipe-knob{background:linear-gradient(135deg,#fb7185,#b91c1c)}.wv2-swipe-status{margin:.32rem 0 0;font-size:.72rem;color:var(--muted);text-align:center}.wv2-live-btn{display:inline-flex;min-height:42px;border-color:rgba(85,217,255,.35);background:rgba(85,217,255,.10)}.wv2-services{position:absolute;z-index:13;top:12.35rem;left:.75rem;width:min(420px,calc(100vw - 1.5rem));display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.45rem;pointer-events:none}.wv2-service{pointer-events:auto;border-radius:16px;padding:.58rem .62rem}.wv2-service b{display:block;font-size:.78rem;line-height:1.05}.wv2-service span{display:block;color:var(--muted);font-size:.62rem;font-weight:850;margin-top:.16rem}.wv2-service small{display:inline-flex;margin-top:.38rem;border-radius:999px;border:1px solid rgba(148,163,184,.18);padding:.12rem .38rem;color:#dbe8f6;font-size:.56rem;font-weight:950;letter-spacing:.04em;text-transform:uppercase}.wv2-service.is-on small{border-color:rgba(var(--brand-rgb),.28);color:var(--green)}.wv2-service.is-on{border-color:rgba(var(--brand-rgb),.25);background:rgba(var(--brand-rgb),.09)}.wv2-service.is-off{opacity:.72}.wv2-v1-strip{position:absolute;z-index:15;top:10.95rem;left:.75rem;width:min(420px,calc(100vw - 1.5rem));border-radius:999px;padding:.42rem .66rem;color:#dbe8f6;font-size:.68rem;font-weight:950;letter-spacing:.02em}.wv2-v1-strip b{color:var(--green)}.wv2-locgate{position:absolute;z-index:14;right:.75rem;top:7.55rem;width:min(330px,calc(100vw - 1.5rem));border-radius:18px;padding:.72rem}.wv2-locgate strong{display:block;font-size:.88rem}.wv2-locgate p{margin:.28rem 0 0;color:var(--muted);font-size:.75rem;line-height:1.35}.wv2-mobile-nav{position:absolute;z-index:30;left:.55rem;right:.55rem;bottom:max(.45rem,env(safe-area-inset-bottom));display:none;grid-template-columns:repeat(5,1fr);gap:.25rem;border-radius:22px;padding:.32rem}.wv2-mobile-nav a{display:flex;flex-direction:column;align-items:center;gap:.08rem;border-radius:15px;padding:.42rem .2rem;text-decoration:none;color:#dbe8f6;font-size:.62rem;font-weight:850}.wv2-mobile-nav a.is-active{background:rgba(var(--brand-rgb),.12);color:var(--green)}.wv2-marker-worker,.wv2-marker-last,.wv2-marker-stop{display:grid;place-items:center;width:30px;height:30px;border-radius:999px;color:#03120d;font-weight:950}.wv2-marker-worker{background:var(--green);box-shadow:0 0 0 10px rgba(var(--brand-rgb),.18),0 0 34px rgba(var(--brand-rgb),.5)}.wv2-marker-last{background:#55d9ff}.wv2-marker-stop{background:#f5bd54}.wv2-zone-label{font-size:.76rem;font-weight:950;color:#dce9f6;text-shadow:0 2px 8px #000}.leaflet-control-zoom{margin-right:.8rem!important;margin-bottom:7.3rem!important}.leaflet-control-attribution{max-width:45vw;overflow:hidden;white-space:nowrap}@media(max-width:920px){.wv2-nav a span{display:none}.wv2-state-card{top:4.25rem;width:min(340px,calc(100vw - 1.5rem))}.wv2-state-card h2{font-size:2.25rem}.wv2-status-pill{top:4.25rem}.wv2-sheet{display:none}.leaflet-control-zoom{display:none}}@media(max-width:1100px){.wv2-services{top:auto;left:.75rem;right:.75rem;width:auto;grid-template-columns:repeat(3,minmax(0,1fr));bottom:8rem}.wv2-locgate{display:none}}@media(max-width:680px){.wv2-brand strong,.wv2-brand small,.wv2-nav{display:none}.wv2-appbar{top:.45rem;left:.5rem;right:.5rem}.wv2-brand{padding:.34rem}.wv2-state-card{top:3.95rem;left:.55rem;right:.55rem;width:auto;padding:.62rem}.wv2-state-card h2{font-size:2.05rem}.wv2-v1-strip{top:10.9rem;left:.55rem;right:.55rem;width:auto;border-radius:18px;font-size:.62rem;line-height:1.3}.wv2-primary{font-size:.84rem}.wv2-status-pill{top:auto;right:.55rem;bottom:9.25rem}.wv2-sheet{display:none}.wv2-bottom{left:.55rem;right:.55rem;bottom:4.95rem;transform:none;width:auto;padding:.58rem}.wv2-services{left:.55rem;right:.55rem;bottom:13.1rem;grid-template-columns:repeat(2,minmax(0,1fr));gap:.32rem}.wv2-service{padding:.45rem .5rem}.wv2-services{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:.15rem}.wv2-service{min-width:168px;scroll-snap-align:start}.wv2-service:nth-child(n+5){display:block}.wv2-mobile-nav{display:grid}.leaflet-control-attribution{display:none}}
</style>
@endpush

<div class="wv2" data-dashboard-v2>
  @include('worker.dashboard.live-map')
  @include('worker.dashboard.active-job-card')
  @include('worker.dashboard.swipe-presence')
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function(){
 const cfg=@json($mapPayload), csrf=cfg.csrf;
 const el=document.getElementById('worker-live-map'); if(!el || !window.L) return;
 el.style.width='100vw'; el.style.height='100dvh'; const map=L.map(el,{zoomControl:false,attributionControl:false}).setView(cfg.center,cfg.zoom||11);
 L.control.zoom({position:'bottomright'}).addTo(map); L.control.attribution({prefix:false,position:'bottomleft'}).addTo(map);
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
 const refresh=()=>{ el.style.setProperty('width','100vw','important'); el.style.setProperty('height','100dvh','important'); map.getContainer().style.setProperty('width','100vw','important'); map.getContainer().style.setProperty('height','100dvh','important'); map.invalidateSize(true); }; if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',refresh,{once:true});} else {refresh();} requestAnimationFrame(refresh); setTimeout(refresh,0); setTimeout(refresh,250); setTimeout(refresh,1000); window.addEventListener('resize',refresh);
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
