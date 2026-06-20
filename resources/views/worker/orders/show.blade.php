@extends('worker.layout')
@section('title', 'Current Job')
@section('content')
@php
    $completionProof = $order->completionProofs->first();
    $intake = $order->metadata['intake'] ?? [];
    $pickup  = $intake['pickup_address'] ?? $intake['vehicle_location'] ?? $intake['task_location'] ?? null;
    $dropoff = $intake['dropoff_address'] ?? $intake['destination_address'] ?? null;
@endphp
@push('styles')
<style>.cj-layout{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(320px,.8fr);gap:1rem}.cj-main,.cj-side{display:grid;gap:1rem}.cj-card{border:1px solid var(--line);border-radius:18px;background:var(--panel);padding:1rem;box-shadow:0 18px 48px rgba(0,0,0,.18)}.cj-hero{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;background:linear-gradient(145deg,rgba(12,31,50,.96),rgba(5,16,29,.96))}.cj-hero h1{margin:.2rem 0;font-size:clamp(2rem,4vw,3rem)}.cj-truth{max-width:56rem}.cj-head-actions{display:flex;gap:.5rem;align-items:center}.cj-next{border-color:rgba(var(--brand-rgb),.32)}.cj-primary{width:100%;min-height:3.2rem;font-size:1rem}.cj-steps{display:grid;gap:.6rem}.cj-step{display:grid;grid-template-columns:2rem 1fr;gap:.7rem;border:1px solid var(--line2);border-radius:13px;padding:.75rem}.cj-step>span{display:grid;place-items:center;width:2rem;height:2rem;border-radius:999px;background:rgba(148,163,184,.1);font-weight:950}.cj-step.done>span,.cj-step.current>span{background:rgba(var(--brand-rgb),.14);color:var(--green)}.cj-step p{margin:.15rem 0 0;color:var(--muted);font-size:.82rem}.cj-grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.6rem;margin:.8rem 0}.cj-grid3 div{border:1px solid var(--line2);border-radius:12px;padding:.7rem}.cj-grid3 span{display:block;color:var(--muted);font-size:.7rem;text-transform:uppercase;font-weight:900}.cj-nav-secondary a{color:var(--green);font-weight:800;text-decoration:none;padding:.45rem}.cj-side textarea{width:100%;border:1px solid var(--line);border-radius:12px;background:#071120;color:var(--text);padding:.75rem}.cj-side select{width:100%;margin:.4rem 0 .7rem}@media(max-width:980px){.cj-layout{grid-template-columns:1fr}.cj-hero{display:block}.cj-grid3{grid-template-columns:1fr}.cj-head-actions{margin-top:1rem}}</style>
@endpush
<div class="cj-layout">
 <main class="cj-main">
  @include('worker.current-job.header')
  @include('worker.current-job.next-action')
  @include('worker.current-job.status-stepper')
  @include('worker.current-job.gps-panel')
  @include('worker.current-job.navigation-panel')
 </main>
 <aside class="cj-side">
  @include('worker.current-job.completion-proof')
  @include('worker.current-job.support-panel')
  @include('worker.current-job.order-details')
  @include('worker.current-job.timeline')
 </aside>
</div>
@push('scripts')
<script>
(function(){
 const orderId=@json($order->id), pingUrl=@json(route('worker.location-pings.store'));
 const sync=document.getElementById('gps-sync'), perm=document.getElementById('gps-permission'), last=document.getElementById('gps-last'), acc=document.getElementById('gps-accuracy');
 if(navigator.permissions){navigator.permissions.query({name:'geolocation'}).then(s=>{perm.textContent=s.state;s.onchange=()=>perm.textContent=s.state}).catch(()=>{})}
 async function share(){ if(!window.isSecureContext){sync.textContent='Sync status: failed — HTTPS / secure context is required.';return} if(!navigator.geolocation){sync.textContent='Sync status: unavailable — browser geolocation is not supported.';return} sync.textContent='Sync status: requesting permission…'; navigator.geolocation.getCurrentPosition(async pos=>{try{sync.textContent='Sync status: sending real GPS ping…'; const res=await fetch(pingUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:JSON.stringify({order_id:orderId,latitude:pos.coords.latitude,longitude:pos.coords.longitude,accuracy_meters:pos.coords.accuracy,heading:pos.coords.heading,speed_mps:pos.coords.speed,captured_at:new Date(pos.timestamp).toISOString(),consent:true})}); const data=await res.json(); if(res.ok){sync.textContent='Sync status: synced — one real manual GPS ping recorded. No background tracking is active.'; last.textContent='just now'; acc.textContent=data.accuracy_meters+' m'} else {sync.textContent='Sync status: failed — '+Object.values(data.errors||{error:data.message||'server rejected the ping'}).flat().join(' ')}}catch(e){sync.textContent='Sync status: failed — server request failed.'}}, err=>{sync.textContent=err.code===1?'Sync status: denied — location permission denied.':'Sync status: failed — '+err.message},{enableHighAccuracy:true,timeout:15000,maximumAge:0}) }
 document.getElementById('gps-share')?.addEventListener('click',share); document.getElementById('gps-refresh')?.addEventListener('click',share);
 const dest=document.getElementById('nav-destination')?.textContent||'', sel=document.getElementById('nav-preferred'), primary=document.getElementById('nav-primary');
 function url(app){const q=encodeURIComponent(dest);return app==='apple'?'https://maps.apple.com/?q='+q:app==='waze'?'https://www.waze.com/ul?q='+q:app==='here'?'https://wego.here.com/directions/mix/'+q:'https://www.google.com/maps/search/?api=1&query='+q}
 function update(){if(primary&&sel){localStorage.setItem('bkb_worker_nav_app',sel.value);primary.href=url(sel.value)}} if(sel){sel.value=localStorage.getItem('bkb_worker_nav_app')||'google'; sel.addEventListener('change',update); update()} document.querySelectorAll('[data-app]').forEach(a=>{a.href=url(a.dataset.app);a.target='_blank';a.rel='noopener'}); document.getElementById('copy-address')?.addEventListener('click',()=>navigator.clipboard?.writeText(dest));
})();
</script>
@endpush
@endsection
