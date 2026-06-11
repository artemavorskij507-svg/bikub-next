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
            <hr>
            <p class="bkb-card-eyebrow">Required action</p>
            <p>Worker must open the assigned order on mobile HTTPS and allow precise location.</p>
            <p class="bkb-card-eyebrow">Mobile GPS UAT still required. Browser geolocation may require HTTPS and explicit user permission.</p>
        </aside>
    </section>
</main>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
.bkb-live-map-workbench{display:grid;gap:1rem}.bkb-live-map-stage{position:relative;min-height:32rem;overflow:hidden;border:1px solid rgba(148,163,184,.2);border-radius:.75rem;background:#07111f}.bkb-live-map-stage #live-operations-map{height:min(68vh,720px);min-height:32rem}.bkb-live-map-empty{position:absolute;inset:0;display:grid;place-items:center;padding:2rem;background:radial-gradient(circle at center,rgba(16,185,129,.08),transparent 52%),#07111f;text-align:center}.bkb-live-map-empty>div{max-width:36rem}.bkb-live-map-empty h2{margin:.5rem 0;font-size:1.75rem;color:#fff}.bkb-live-map-empty p{color:#a9b8ce}.bkb-live-map-status{position:absolute;left:1rem;bottom:1rem;z-index:500;margin:0;border:1px solid rgba(148,163,184,.2);border-radius:.45rem;background:rgba(7,17,31,.9);padding:.55rem .75rem;color:#b8c6d9;font-size:.75rem}@media(min-width:1100px){.bkb-live-map-workbench{grid-template-columns:minmax(0,1fr) 21rem}.bkb-live-map-workbench aside{align-self:start}}
</style>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded',async()=>{const status=document.getElementById('live-map-status'),empty=document.getElementById('live-map-empty'),el=document.getElementById('live-operations-map');const map=L.map(el,{center:[68.4385,17.4272],zoom:10});L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);try{const response=await fetch(@json(route('admin.live-operations-map.data')),{headers:{Accept:'application/json'}});if(!response.ok)throw new Error('Telemetry endpoint returned '+response.status);const data=await response.json();if(!data.count){status.textContent='Map center only — no worker marker yet.';return}empty.style.display='none';const bounds=[];data.markers.forEach(m=>{bounds.push([m.latitude,m.longitude]);const popup=[`<strong>${escapeHtml(m.worker.name||'Worker #'+m.worker.id)}</strong>`,escapeHtml(m.worker.email||''),`Order: ${escapeHtml(m.order_number||'No linked order')}`,`Order status: ${escapeHtml(m.order_status||'Unknown')}`,`Presence: ${escapeHtml(m.presence_status)}`,`Accuracy: ${m.accuracy_meters??'Unknown'} m`,`Captured: ${escapeHtml(m.captured_at||m.created_at||'Unknown')}`].join('<br>');L.marker([m.latitude,m.longitude]).addTo(map).bindPopup(popup)});map.fitBounds(bounds,{padding:[32,32],maxZoom:16});status.textContent=`Showing ${data.count} marker(s) from real database pings.`}catch(error){status.textContent='Live map unavailable: '+error.message}});function escapeHtml(value){const div=document.createElement('div');div.textContent=value;return div.innerHTML}
</script>
@endpush
</x-filament-panels::page>
