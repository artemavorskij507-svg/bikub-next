@php($assignment = $this->getCurrentAssignment())
<x-filament-panels::page>
<main class="bkb-admin-shell bkb-operator">
    <header class="bkb-operator-head">
        <div>
            <h1>Live Operations Map</h1>
            <p>Latest verified browser location per worker and assigned order.</p>
        </div>
        <div class="bkb-action-row">
            <a class="bkb-card-link" href="{{ route('filament.admin.pages.dispatch-center') }}">Dispatch Center</a>
            @if($assignment?->order)
                <a class="bkb-card-link" href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $assignment->order]) }}">Assigned order</a>
            @endif
        </div>
    </header>

    <section class="bkb-foundation-strip">
        <article><span>Real pings</span><strong>{{ $this->getPingCount() }}</strong></article>
        <article><span>Latest ping</span><strong>{{ $this->getLatestPingAt() }}</strong></article>
        <article><span>Active assignments</span><strong>{{ $this->getActiveAssignmentCount() }}</strong></article>
        <article><span>Orders with pings</span><strong>{{ $this->getOrdersWithPingsCount() }}</strong></article>
        <article><span>Customer tracking</span><strong>Not exposed</strong></article>
    </section>

    <section class="bkb-live-map-workbench">
        <article class="bkb-live-map-stage">
            <div id="live-operations-map"></div>
            <div id="live-map-empty" class="bkb-live-map-empty">
                <div>
                    <span class="bkb-card-eyebrow">GPS telemetry</span>
                    <h2>No real GPS pings yet</h2>
                    <p>Map center only — no worker marker yet. Open the worker cockpit on a phone or HTTPS URL and tap <strong>Send real GPS ping now</strong>.</p>
                    <div class="bkb-action-row">
                        @if($assignment?->order)
                            <a class="bkb-card-link" href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $assignment->order]) }}">Open assigned order</a>
                        @else
                            <span class="bkb-status-badge">No assigned order available</span>
                        @endif
                        <a class="bkb-card-link" href="{{ route('filament.admin.pages.dispatch-center') }}">Open Dispatch Center</a>
                    </div>
                </div>
            </div>
            <p id="live-map-status" class="bkb-live-map-status">Loading real telemetry...</p>
        </article>

        <aside class="bkb-os-card">
            <h2>Current operation</h2>
            @if($assignment?->order)
                <dl class="bkb-module-meta">
                    <div><dt>Worker</dt><dd>{{ $assignment->assignedUser?->name ?? $assignment->assignedUser?->email }}</dd></div>
                    <div><dt>Presence</dt><dd>{{ str($assignment->assignedUser?->workerAvailability?->status ?? 'offline')->title() }}</dd></div>
                    <div><dt>Order</dt><dd>{{ $assignment->order->order_number }}</dd></div>
                    <div><dt>Order status</dt><dd>{{ str($assignment->order->status->value)->replace('_', ' ')->title() }}</dd></div>
                    <div><dt>GPS status</dt><dd>{{ $this->getPingCount() ? 'Real telemetry available' : 'No ping yet' }}</dd></div>
                    <div><dt>Worker order URL</dt><dd>{{ route('worker.orders.show', $assignment->order) }}</dd></div>
                </dl>
                <a class="bkb-card-link" href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $assignment->order]) }}">Open order telemetry</a>
            @else
                <p>No active assignment is available.</p>
            @endif
            <div class="bkb-required-action">
                <span>Required action</span>
                <strong>Send first real GPS ping</strong>
                <p>Worker must open the assigned order on mobile HTTPS and allow precise location.</p>
            </div>
            @if($assignment?->order)
                <details class="bkb-uat-details">
                    <summary>Mobile GPS UAT instructions</summary>
                    <p>Current server is localhost-only. Use approved HTTPS staging or tunnel before opening this URL on a phone.</p>
                    <label for="mobile-uat-url">Detected LAN candidate</label>
                    <input id="mobile-uat-url" class="bkb-uat-url" readonly value="http://192.168.11.138:8090/worker/orders/{{ $assignment->order->id }}">
                    <button id="copy-mobile-uat-url" class="bkb-card-link" type="button">Copy URL</button>
                    <ol class="bkb-uat-steps">
                        <li>Open worker order on phone through HTTPS.</li>
                        <li>Login and tap Send real GPS ping now.</li>
                        <li>Allow precise location and wait up to 12 seconds.</li>
                    </ol>
                </details>
            @endif
        </aside>
    </section>
</main>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
.bkb-operator-head{padding-block:.35rem .8rem}.bkb-operator-head h1{font-size:1.8rem}.bkb-foundation-strip{grid-template-columns:repeat(5,minmax(0,1fr));gap:.65rem;margin-block:.75rem}.bkb-foundation-strip article{min-height:4.5rem;padding:.75rem 1rem}.bkb-live-map-workbench{display:grid;align-items:start;gap:1rem}.bkb-live-map-stage{position:relative;height:clamp(32rem,calc(100vh - 19rem),48rem);overflow:hidden;border:1px solid rgba(148,163,184,.2);border-radius:.75rem;background:#07111f}.bkb-live-map-stage #live-operations-map{height:100%;min-height:0}.bkb-live-map-empty{position:absolute;z-index:450;top:1rem;left:50%;width:min(34rem,calc(100% - 2rem));transform:translateX(-50%);padding:1rem 1.25rem;border:1px solid rgba(52,211,153,.25);border-radius:.65rem;background:rgba(7,17,31,.9);box-shadow:0 18px 50px rgba(0,0,0,.28);text-align:left;backdrop-filter:blur(10px)}.bkb-live-map-empty>div{max-width:none}.bkb-live-map-empty h2{margin:.2rem 0;font-size:1.1rem;color:#fff}.bkb-live-map-empty p{margin:.25rem 0 .7rem;color:#a9b8ce;font-size:.82rem}.bkb-live-map-empty .bkb-action-row{gap:.45rem}.bkb-live-map-status{position:absolute;left:1rem;bottom:1rem;z-index:500;margin:0;border:1px solid rgba(148,163,184,.2);border-radius:.45rem;background:rgba(7,17,31,.9);padding:.55rem .75rem;color:#b8c6d9;font-size:.75rem}.bkb-live-map-workbench aside{max-height:clamp(32rem,calc(100vh - 19rem),48rem);overflow:auto;padding:1rem}.bkb-live-map-workbench aside h2{font-size:1.05rem}.bkb-module-meta{gap:.25rem}.bkb-module-meta div{padding:.55rem 0}.bkb-required-action{margin-top:1rem;padding:.85rem;border:1px solid rgba(52,211,153,.22);border-radius:.55rem;background:rgba(16,185,129,.07)}.bkb-required-action span{display:block;color:#6ee7b7;font-size:.68rem;font-weight:800;text-transform:uppercase}.bkb-required-action strong{display:block;margin-top:.2rem;color:#fff}.bkb-required-action p{margin:.35rem 0 0;color:#a9b8ce;font-size:.78rem}.bkb-uat-details{margin-top:.75rem;border-top:1px solid rgba(148,163,184,.18);padding-top:.75rem;color:#a9b8ce;font-size:.78rem}.bkb-uat-details summary{cursor:pointer;color:#d8e4f2;font-weight:800}.bkb-uat-details label{display:block;margin-top:.75rem;color:#6ee7b7;font-size:.68rem;font-weight:800;text-transform:uppercase}.bkb-uat-url{width:100%;margin:.35rem 0 .55rem;border:1px solid rgba(148,163,184,.25);border-radius:.4rem;background:#07111f;padding:.55rem;color:#d8e4f2;font-size:.72rem}.bkb-uat-steps{display:grid;gap:.35rem;margin:.75rem 0 0;padding-left:1.1rem;color:#a9b8ce;font-size:.75rem}@media(min-width:1100px){.bkb-live-map-workbench{grid-template-columns:minmax(0,1fr) 22rem}}@media(max-width:900px){.bkb-foundation-strip{grid-template-columns:repeat(2,minmax(0,1fr))}.bkb-live-map-stage{height:32rem}.bkb-live-map-workbench aside{max-height:none;overflow:visible}}
</style>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{const status=document.getElementById('live-map-status'),empty=document.getElementById('live-map-empty'),el=document.getElementById('live-operations-map'),copy=document.getElementById('copy-mobile-uat-url'),url=document.getElementById('mobile-uat-url');const map=L.map(el,{center:[68.4385,17.4272],zoom:10});const markers=L.layerGroup().addTo(map);let previousCount=0;L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);async function refresh(){try{const response=await fetch(@json(route('admin.live-operations-map.data')),{headers:{Accept:'application/json'},cache:'no-store'});if(!response.ok)throw new Error('Telemetry endpoint returned '+response.status);const data=await response.json();markers.clearLayers();empty.style.display=data.count?'none':'grid';if(!data.count){status.textContent='Map center only — no worker marker yet. Checking every 12 seconds.';previousCount=0;return}const bounds=[];data.markers.forEach(m=>{bounds.push([m.latitude,m.longitude]);const popup=[`<strong>${escapeHtml(m.worker.name||'Worker #'+m.worker.id)}</strong>`,escapeHtml(m.worker.email||''),`Order: ${escapeHtml(m.order_number||'No linked order')}`,`Order status: ${escapeHtml(m.order_status||'Unknown')}`,`Presence: ${escapeHtml(m.presence_status)}`,`Accuracy: ${m.accuracy_meters??'Unknown'} m`,`Captured: ${escapeHtml(m.captured_at||m.created_at||'Unknown')}`].join('<br>');L.marker([m.latitude,m.longitude]).addTo(markers).bindPopup(popup)});if(previousCount===0)map.fitBounds(bounds,{padding:[32,32],maxZoom:16});previousCount=data.count;status.textContent=`Showing ${data.count} marker(s) from real database pings. Auto-refresh: 12 seconds.`}catch(error){status.textContent='Live map unavailable: '+error.message}}copy?.addEventListener('click',async()=>{try{await navigator.clipboard.writeText(url.value);copy.textContent='Copied'}catch{url.select();copy.textContent='Select and copy URL'}});refresh();setInterval(refresh,12000)});function escapeHtml(value){const div=document.createElement('div');div.textContent=value;return div.innerHTML}
</script>
@endpush
</x-filament-panels::page>
