<x-filament-panels::page>
<main class="bkb-admin-shell bkb-operator">
    <header class="bkb-operator-head">
        <div><h1>Live Operations Map</h1><p>Latest real browser location ping per worker and order. No simulated markers or routes.</p></div>
        <a class="bkb-card-link" href="{{ route('filament.admin.pages.dispatch-center') }}">Open Dispatch Center</a>
    </header>
    <section class="bkb-foundation-strip">
        <article><span>Real pings</span><strong>{{ $this->getPingCount() }}</strong></article>
        <article><span>Latest ping</span><strong>{{ $this->getLatestPingAt() }}</strong></article>
        <article><span>Customer tracking</span><strong>Not exposed</strong></article>
    </section>
    <section class="bkb-os-card">
        <div id="live-map-empty" class="bkb-honesty-panel"><div><span>GPS state</span><strong>No real worker location pings yet.</strong></div><div><span>Required action</span><strong>Worker must explicitly allow precise location from the active order page.</strong></div></div>
        <div id="live-operations-map" style="display:none;height:min(65vh,720px);min-height:460px;border:1px solid rgba(148,163,184,.25);border-radius:10px;overflow:hidden"></div>
        <p id="live-map-status" class="bkb-card-eyebrow">Loading real telemetry…</p>
    </section>
    <section class="bkb-honesty-panel"><div><span>Marker source</span><strong>worker_location_pings only</strong></div><div><span>Unavailable</span><strong>Route lines · simulated movement · customer live tracking</strong></div></section>
</main>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded',async()=>{const status=document.getElementById('live-map-status'),empty=document.getElementById('live-map-empty'),el=document.getElementById('live-operations-map');try{const response=await fetch(@json(route('admin.live-operations-map.data')),{headers:{Accept:'application/json'}});if(!response.ok)throw new Error('Telemetry endpoint returned '+response.status);const data=await response.json();if(!data.count){status.textContent='No marker rendered because no real GPS ping exists.';return}empty.style.display='none';el.style.display='block';const map=L.map(el);L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);const bounds=[];data.markers.forEach(m=>{bounds.push([m.latitude,m.longitude]);const popup=[`<strong>${escapeHtml(m.worker.name||'Worker #'+m.worker.id)}</strong>`,escapeHtml(m.worker.email||''),`Order: ${escapeHtml(m.order_number||'No linked order')}`,`Order status: ${escapeHtml(m.order_status||'Unknown')}`,`Presence: ${escapeHtml(m.presence_status)}`,`Accuracy: ${m.accuracy_meters??'Unknown'} m`,`Captured: ${escapeHtml(m.captured_at||m.created_at||'Unknown')}`].join('<br>');L.marker([m.latitude,m.longitude]).addTo(map).bindPopup(popup)});map.fitBounds(bounds,{padding:[32,32],maxZoom:16});status.textContent=`Showing ${data.count} marker(s) from real database pings.`}catch(error){status.textContent='Live map unavailable: '+error.message}});function escapeHtml(value){const div=document.createElement('div');div.textContent=value;return div.innerHTML}
</script>
@endpush
</x-filament-panels::page>
