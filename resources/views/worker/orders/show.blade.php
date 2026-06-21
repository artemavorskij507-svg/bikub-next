@extends('worker.layout')
@section('title', 'Current Job')
@section('body-class', 'current-job-fullscreen')
@section('content')
@php
    $completionProof = $order->completionProofs->first();
    $intake = $order->metadata['intake'] ?? [];
    $pickup  = $intake['pickup_address'] ?? $intake['vehicle_location'] ?? $intake['task_location'] ?? null;
    $dropoff = $intake['dropoff_address'] ?? $intake['destination_address'] ?? null;
    $pickupLat = $intake['pickup_latitude'] ?? $intake['pickup_lat'] ?? null;
    $pickupLng = $intake['pickup_longitude'] ?? $intake['pickup_lng'] ?? null;
    $dropoffLat = $intake['dropoff_latitude'] ?? $intake['dropoff_lat'] ?? $intake['destination_latitude'] ?? null;
    $dropoffLng = $intake['dropoff_longitude'] ?? $intake['dropoff_lng'] ?? $intake['destination_longitude'] ?? null;
    $hasPickedUp = $order->events->contains('event_type', 'worker.picked_up');
    $navigationTarget = $hasPickedUp ? ($dropoff ?: $pickup) : ($pickup ?: $dropoff);
    $gpsFresh = $lastPing?->captured_at?->gt(now()->subMinutes(10)) ?? false;
    $gpsState = $lastPing ? ($gpsFresh ? 'Fresh' : 'Stale') : 'No ping';
    $executionLabel = $executionState['state_label'] ?? $nextAction['state_label'] ?? ($executionState['next_action']['label'] ?? 'Current Job');
    $primaryLabel = $executionState['next_action']['label'] ?? 'No worker action available';
    $mapPayload = [
        'center' => [(float)($pickupLat ?: $dropoffLat ?: $lastPing?->latitude ?: 68.4385), (float)($pickupLng ?: $dropoffLng ?: $lastPing?->longitude ?: 17.4272)],
        'zoom' => 13,
        'csrf' => csrf_token(),
        'pingUrl' => route('worker.location-pings.store'),
        'orderId' => $order->id,
        'lastPing' => $lastPing ? ['lat'=>(float)$lastPing->latitude,'lng'=>(float)$lastPing->longitude,'accuracy'=>(float)$lastPing->accuracy_meters,'label'=>$lastPing->captured_at?->diffForHumans()] : null,
        'pickup' => ($pickupLat && $pickupLng) ? ['lat'=>(float)$pickupLat,'lng'=>(float)$pickupLng,'label'=>'Pickup'] : null,
        'dropoff' => ($dropoffLat && $dropoffLng) ? ['lat'=>(float)$dropoffLat,'lng'=>(float)$dropoffLng,'label'=>'Drop-off'] : null,
    ];
@endphp
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQeq9Uq4ik9xkclle2n2E1Jb2fYFhI4=" crossorigin="">
<style>
html,body{overflow:hidden!important}body.current-job-fullscreen{background:#06111e}body.current-job-fullscreen .worker-sidebar,body.current-job-fullscreen .worker-topbar,body.current-job-fullscreen .worker-bottom{display:none!important}body.current-job-fullscreen .worker-content{padding:0!important;min-height:100dvh}.cjx{position:fixed;inset:0;width:100vw;height:100dvh;overflow:hidden;background:#06111e;color:var(--text)}.cjx-map-stage,.cjx-map{position:absolute!important;inset:0!important;width:100vw!important;height:100dvh!important;min-width:100vw!important;min-height:100dvh!important;max-width:none!important;max-height:none!important}.cjx-map{z-index:1}.leaflet-container{background:#06111e;overflow:hidden!important}.leaflet-pane,.leaflet-tile,.leaflet-marker-icon,.leaflet-marker-shadow,.leaflet-tile-container,.leaflet-pane>svg,.leaflet-pane>canvas,.leaflet-zoom-box,.leaflet-image-layer,.leaflet-layer{position:absolute!important;left:0;top:0}.leaflet-container img.leaflet-tile{max-width:none!important;max-height:none!important;width:256px!important;height:256px!important;display:block!important}.leaflet-tile{filter:brightness(.92) saturate(.92) contrast(1.04) hue-rotate(156deg)}.leaflet-control-container{position:absolute;inset:0;z-index:800;pointer-events:none}.leaflet-control{pointer-events:auto}.cjx-vignette{position:absolute;inset:0;z-index:2;pointer-events:none;background:linear-gradient(180deg,rgba(3,10,18,.22),transparent 16%,transparent 70%,rgba(3,10,18,.38)),radial-gradient(circle at 16% 15%,rgba(var(--brand-rgb),.08),transparent 34%)}.cjx-glass{border:1px solid rgba(148,163,184,.13);background:rgba(5,14,25,.68);backdrop-filter:blur(20px);box-shadow:0 18px 56px rgba(0,0,0,.24)}.cjx-topbar{position:absolute;z-index:20;top:max(.55rem,env(safe-area-inset-top));left:.65rem;right:.65rem;display:flex;align-items:center;justify-content:space-between;gap:.6rem;pointer-events:none}.cjx-topbar>*{pointer-events:auto}.cjx-brand{display:flex;align-items:center;gap:.55rem;border-radius:999px;padding:.38rem .7rem;text-decoration:none;color:#eff6ff}.cjx-logo{display:grid;place-items:center;width:32px;height:32px;border-radius:999px;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));font-weight:950;color:#03130d}.cjx-brand small{display:block;color:var(--muted);font-size:.62rem;font-weight:850}.cjx-job-pill{display:flex;gap:.5rem;align-items:center;border-radius:999px;padding:.5rem .75rem;font-size:.8rem;font-weight:900}.cjx-job-pill b{color:var(--green)}.cjx-strip{position:absolute;z-index:12;top:4.7rem;left:.75rem;right:.75rem;display:flex;gap:.4rem;overflow:hidden;pointer-events:none}.cjx-step{pointer-events:auto;display:flex;align-items:center;gap:.32rem;border-radius:999px;padding:.38rem .58rem;background:rgba(5,14,25,.58);border:1px solid rgba(148,163,184,.11);font-size:.7rem;font-weight:900;color:#b8c7d6;white-space:nowrap}.cjx-step.is-current{background:rgba(var(--brand-rgb),.13);color:var(--green);border-color:rgba(var(--brand-rgb),.24)}.cjx-step.is-done{color:#e5f9ee}.cjx-sheet{position:absolute;z-index:18;left:50%;bottom:max(1.8rem,calc(env(safe-area-inset-bottom) + 1.8rem));transform:translateX(-50%);width:min(560px,calc(100vw - 1.4rem));border-radius:26px;padding:.82rem}.cjx-action-head{display:flex;justify-content:space-between;gap:.75rem;align-items:flex-start}.cjx-kicker{margin:0;color:var(--green);font-size:.64rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.cjx-sheet h1{margin:.05rem 0;font-size:1.25rem;line-height:1.05}.cjx-sheet p{margin:.22rem 0 0;color:var(--muted);font-size:.82rem}.cjx-primary{width:100%;min-height:54px;margin-top:.75rem;font-size:.98rem}.cjx-secondary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.42rem;margin-top:.55rem}.cjx-secondary .worker-btn{min-height:38px;font-size:.75rem}.cjx-drawer{position:absolute;z-index:16;right:.75rem;top:7.2rem;width:min(310px,calc(100vw - 1.5rem));display:grid;gap:.38rem;opacity:.92}.cjx-mini{border-radius:16px;padding:.52rem}.cjx-mini summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:.65rem}.cjx-mini summary::-webkit-details-marker{display:none}.cjx-mini summary strong{font-size:.76rem;color:#dbe8f6}.cjx-mini[open]{background:rgba(5,14,25,.58)}.cjx-mini h3{margin:.05rem 0;font-size:.9rem}.cjx-mini p,.cjx-mini .muted{font-size:.74rem}.cjx-mini textarea{width:100%;min-height:82px;border:1px solid var(--line);border-radius:12px;background:#071120;color:var(--text);padding:.7rem}.cjx-mini .actions{display:flex;gap:.4rem;flex-wrap:wrap}.cjx-marker-worker,.cjx-marker-last,.cjx-marker-stop{display:grid;place-items:center;width:30px;height:30px;border-radius:999px;color:#03120d;font-weight:950}.cjx-marker-worker{background:var(--green);box-shadow:0 0 0 10px rgba(var(--brand-rgb),.18),0 0 34px rgba(var(--brand-rgb),.5)}.cjx-marker-last{background:#55d9ff}.cjx-marker-stop{background:#f5bd54}@media(max-width:900px){.cjx-drawer{display:none}.cjx-strip{right:.75rem;overflow:auto}.cjx-topbar{gap:.4rem}.cjx-brand small{display:none}.cjx-job-pill{font-size:.72rem}.cjx-secondary{grid-template-columns:1fr 1fr}.cjx-sheet{bottom:max(.75rem,env(safe-area-inset-bottom));}}@media(max-width:620px){.cjx-topbar{left:.5rem;right:.5rem}.cjx-brand strong{display:none}.cjx-strip{top:4.25rem}.cjx-sheet{left:.55rem;right:.55rem;transform:none;width:auto;padding:.68rem}.cjx-action-head{display:block}.cjx-secondary{grid-template-columns:1fr}.leaflet-control-zoom{display:none}}
</style>
@endpush
<div class="cjx" data-current-job-fullscreen>
  @include('worker.current-job.map-stage')
  @include('worker.current-job.job-topbar')
  @include('worker.current-job.progress-strip')
  @include('worker.current-job.action-sheet')
  @include('worker.current-job.side-drawer')
</div>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function(){
 const cfg=@json($mapPayload), orderId=@json($order->id), pingUrl=@json(route('worker.location-pings.store'));
 const el=document.getElementById('cjx-map');
 if(el && window.L){
  el.style.width='100vw'; el.style.height='100dvh'; const map=L.map(el,{zoomControl:true,attributionControl:true}).setView(cfg.center,cfg.zoom||13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
  const refresh=()=>{el.style.setProperty('width','100vw','important');el.style.setProperty('height','100dvh','important');map.getContainer().style.setProperty('width','100vw','important');map.getContainer().style.setProperty('height','100dvh','important');map.invalidateSize(true)}; if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',refresh,{once:true})}else{refresh()} requestAnimationFrame(refresh); setTimeout(refresh,0); setTimeout(refresh,250); setTimeout(refresh,1000); window.addEventListener('resize',refresh);
  const icon=(cls,txt)=>L.divIcon({className:'',html:'<span class="'+cls+'">'+txt+'</span>',iconSize:[30,30],iconAnchor:[15,15]}); let bounds=[];
  function add(point,cls,txt,popup){if(!point)return; L.marker([point.lat,point.lng],{icon:icon(cls,txt)}).addTo(map).bindPopup(popup); bounds.push([point.lat,point.lng]);}
  add(cfg.lastPing,'cjx-marker-last','●','Last real worker GPS ping'); add(cfg.pickup,'cjx-marker-stop','P','Pickup marker from real coordinates'); add(cfg.dropoff,'cjx-marker-stop','D','Drop-off marker from real coordinates'); if(bounds.length){map.fitBounds(bounds,{padding:[90,90],maxZoom:14}); setTimeout(refresh,180)}
 }
 const sync=document.getElementById('gps-sync'), perm=document.getElementById('gps-permission'), last=document.getElementById('gps-last'), acc=document.getElementById('gps-accuracy'), ready=document.getElementById('gps-browser-readiness'), state=document.getElementById('gps-state');
 if(ready){ready.textContent=window.isSecureContext?'Ready':'Secure context required'}
 if(navigator.permissions){navigator.permissions.query({name:'geolocation'}).then(s=>{if(perm)perm.textContent=s.state;s.onchange=()=>{if(perm)perm.textContent=s.state}}).catch(()=>{})}
 async function share(){ if(!window.isSecureContext){if(sync)sync.textContent='GPS failed: HTTPS / secure context is required.';return} if(!navigator.geolocation){if(sync)sync.textContent='GPS unavailable in this browser.';return} if(sync)sync.textContent='Requesting browser permission…'; navigator.geolocation.getCurrentPosition(async pos=>{try{if(sync)sync.textContent='Sending one real GPS ping…'; const res=await fetch(pingUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:JSON.stringify({order_id:orderId,latitude:pos.coords.latitude,longitude:pos.coords.longitude,accuracy_meters:pos.coords.accuracy,heading:pos.coords.heading,speed_mps:pos.coords.speed,captured_at:new Date(pos.timestamp).toISOString(),consent:true})}); const data=await res.json(); if(res.ok){if(sync)sync.textContent='GPS synced. One manual ping only; no background tracking.'; if(last)last.textContent='just now'; if(acc)acc.textContent=data.accuracy_meters+' m'; if(state)state.textContent='Fresh'} else {if(sync)sync.textContent='GPS rejected: '+Object.values(data.errors||{error:data.message||'server rejected ping'}).flat().join(' ')}}catch(e){if(sync)sync.textContent='GPS request failed.'}},err=>{if(sync)sync.textContent=err.code===1?'GPS denied by browser.':'GPS failed: '+err.message},{enableHighAccuracy:true,timeout:15000,maximumAge:0})}
 document.getElementById('gps-share')?.addEventListener('click',share); document.getElementById('gps-refresh')?.addEventListener('click',share);
 const dest=document.getElementById('nav-destination')?.textContent||'', sel=document.getElementById('nav-preferred'), primary=document.getElementById('nav-primary');
 function url(app){const q=encodeURIComponent(dest);return app==='apple'?'https://maps.apple.com/?q='+q:app==='waze'?'https://www.waze.com/ul?q='+q:app==='here'?'https://wego.here.com/directions/mix/'+q:'https://www.google.com/maps/search/?api=1&query='+q}
 function update(){if(primary&&sel){localStorage.setItem('bkb_worker_nav_app',sel.value);primary.href=url(sel.value)}} if(sel){sel.value=localStorage.getItem('bkb_worker_nav_app')||'google'; sel.addEventListener('change',update); update()} document.querySelectorAll('[data-app]').forEach(a=>{a.href=url(a.dataset.app);a.target='_blank';a.rel='noopener'}); document.getElementById('copy-address')?.addEventListener('click',()=>navigator.clipboard?.writeText(dest));
})();
</script>
@endpush
@endsection
