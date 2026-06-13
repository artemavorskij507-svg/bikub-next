<x-filament-panels::page>
<x-admin-os.module-shell class="lm" label="Live operations map command workspace">
    <header class="lm-command">
        <div class="lm-brand">
            <span><i></i> Real GPS operations</span>
            <h1>Live Operations Map</h1>
            <p>Verified browser telemetry only. No ping means no worker marker.</p>
        </div>
        <nav class="lm-command-links" aria-label="Live map quick links">
            <x-admin-os.action-button :href="route('filament.admin.pages.dispatch-center')" tone="primary">Dispatch Center</x-admin-os.action-button>
            @if($assignment?->order)<x-admin-os.action-button :href="\App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$assignment->order])">Active order</x-admin-os.action-button>@endif
            @can('admin.support.view')@if($latestSupportTicket)<x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$latestSupportTicket])">Support ticket</x-admin-os.action-button>@endif@endcan
        </nav>
    </header>

    <section class="lm-kpis" aria-label="Map telemetry metrics">
        @foreach([
            ['pings','Real GPS pings','cyan'], ['active_assignments','Active assignments','success'],
            ['workers_with_ping','Workers with ping','cyan'], ['orders_with_ping','Orders with ping','cyan'],
            ['stale_pings','Stale pings > 2 min','warning'],
        ] as [$key,$label,$tone])
            <article class="lm-kpi is-{{ $tone }}"><span>{{ $label }}</span><strong>{{ $metrics[$key] }}</strong><i></i></article>
        @endforeach
        <article class="lm-kpi {{ $metrics['customer_tracking'] ? 'is-success' : 'is-muted' }}"><span>Customer tracking</span><strong>{{ $metrics['customer_tracking'] ? 'Enabled' : 'Disabled' }}</strong><i></i></article>
    </section>

    <div class="lm-grid">
        <main class="lm-map-panel">
            <header>
                <div><span>Operations map</span><h2>Real worker telemetry</h2></div>
                <div class="lm-map-legend"><b><i class="is-live"></i> Real ping</b><b><i class="is-stale"></i> Stale &gt; 2 min</b></div>
            </header>
            <div class="lm-map-stage">
                <div id="live-operations-map" aria-label="Live operations map"></div>
                <div id="live-map-empty" class="lm-map-empty">
                    <span>No real GPS ping yet</span>
                    <strong>Map center only — no worker marker.</strong>
                    <p>Worker must open the assigned order on mobile HTTPS, stay online and send a real location ping.</p>
                    <div>
                        @if($assignment?->order)<a href="{{ route('worker.orders.show',$assignment->order) }}">Open worker order</a>@endif
                        <a href="{{ route('filament.admin.pages.dispatch-center') }}">Open Dispatch Center</a>
                    </div>
                </div>
                <p id="live-map-status" class="lm-map-status">Loading real telemetry...</p>
            </div>
            <footer>
                <span>OSM map center: {{ number_format($mapDefaults['lat'],4) }}, {{ number_format($mapDefaults['lng'],4) }} · zoom {{ $mapDefaults['zoom'] }}</span>
                <strong>Auto-refresh every 12 seconds</strong>
            </footer>
        </main>

        <aside class="lm-rail">
            <section class="lm-panel">
                <header><div><span>Current operation</span><h2>{{ $assignment?->order?->order_number ?? 'No active assignment' }}</h2></div>@if($assignment)<x-admin-os.status-badge :value="$assignment->status" />@endif</header>
                @if($assignment?->order)
                    <dl class="lm-details">
                        <div><dt>Worker</dt><dd>{{ $assignment->assignedUser?->name ?? $assignment->assignedUser?->email ?? 'Worker missing' }}</dd></div>
                        <div><dt>Presence</dt><dd>{{ str($assignment->assignedUser?->workerAvailability?->status ?? 'offline')->title() }}</dd></div>
                        <div><dt>Worker profile</dt><dd>{{ str($assignment->assignedUser?->workerProfile?->status ?? 'missing')->title() }}</dd></div>
                        <div><dt>Order status</dt><dd>{{ str($assignment->order->status->value)->replace('_',' ')->title() }}</dd></div>
                        <div><dt>Support tickets</dt><dd>{{ $assignment->order->supportTickets->whereNotIn('status',['resolved','closed'])->count() }} open</dd></div>
                        <div><dt>Order GPS pings</dt><dd>{{ $assignment->order->workerLocationPings->count() }}</dd></div>
                    </dl>
                    <div class="lm-links">
                        <a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$assignment->order]) }}">Open order <span>Inspect</span></a>
                        <a href="{{ route('worker.orders.show',$assignment->order) }}">Worker order URL <span>Open</span></a>
                    </div>
                @else
                    <x-admin-os.empty-state title="No active assignment." body="Open Dispatch Center to review waiting and unassigned orders." />
                @endif
            </section>

            <section class="lm-panel">
                <header><div><span>GPS state</span><h2>{{ $latestPing ? 'Real telemetry received' : 'Waiting for first ping' }}</h2></div></header>
                <dl class="lm-details">
                    <div><dt>GPS tracking flow</dt><dd class="{{ $gpsTrackingEnabled ? 'is-good' : 'is-warning' }}">{{ $gpsTrackingEnabled ? 'Enabled' : 'Disabled' }}</dd></div>
                    <div><dt>Latest ping</dt><dd>{{ $latestPing?->captured_at?->diffForHumans() ?? 'No real GPS ping' }}</dd></div>
                    <div><dt>Accuracy</dt><dd>{{ $latestPing?->accuracy_meters !== null ? number_format((float)$latestPing->accuracy_meters,0).' m' : 'Unavailable' }}</dd></div>
                    <div><dt>Max accepted accuracy</dt><dd>{{ number_format($mapDefaults['max_accuracy']) }} m</dd></div>
                    <div><dt>Customer visibility</dt><dd class="{{ $metrics['customer_tracking'] ? 'is-good' : 'is-muted' }}">{{ $metrics['customer_tracking'] ? 'Enabled by settings' : 'Disabled' }}</dd></div>
                </dl>
                <div class="lm-required">
                    <span>Required action</span>
                    <strong>{{ $latestPing ? 'Monitor real telemetry freshness.' : 'Complete mobile HTTPS GPS UAT.' }}</strong>
                    <p>{{ $latestPing ? 'Markers refresh from the protected database endpoint.' : 'Location permission, online presence and acceptable accuracy are required.' }}</p>
                </div>
            </section>

            @if($latestSupportTicket)
                <section class="lm-panel">
                    <header><div><span>Support signal</span><h2>{{ $latestSupportTicket->ticket_number }}</h2></div><x-admin-os.status-badge :value="$latestSupportTicket->priority" /></header>
                    <div class="lm-support"><strong>{{ $latestSupportTicket->subject }}</strong><p>{{ str($latestSupportTicket->status)->replace('_',' ')->title() }} · {{ $latestSupportTicket->assignee?->name ?? 'Unassigned' }}</p>@can('admin.support.view')<a href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$latestSupportTicket]) }}">Open support ticket</a>@else<p>Support view permission is required.</p>@endcan</div>
                </section>
            @endif

            @if($assignment?->order)
                <section class="lm-panel lm-uat">
                    <header><div><span>Mobile GPS UAT</span><h2>Secure browser required</h2></div></header>
                    <p>Phone browsers normally require HTTPS and explicit location permission. Localhost on this PC is not a phone-access URL.</p>
                    <label for="mobile-uat-url">LAN candidate — HTTPS may still be required</label>
                    <input id="mobile-uat-url" readonly value="http://192.168.11.138:8090/worker/orders/{{ $assignment->order->id }}">
                    <button id="copy-mobile-uat-url" type="button">Copy URL</button>
                </section>
            @endif
        </aside>
    </div>
</x-admin-os.module-shell>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
.fi-page-header{display:none}.lm{--line:rgba(121,158,194,.17);--panel:#071525;--muted:#7891ae;--text:#ecf5ff;display:grid;gap:.68rem;color:var(--text);font-size:.8rem}.lm-command,.lm-panel,.lm-map-panel,.lm-kpi{border:1px solid var(--line);border-radius:7px;background:linear-gradient(145deg,rgba(10,27,45,.97),rgba(4,16,29,.98));box-shadow:0 18px 46px rgba(0,0,0,.18)}.lm-command{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.78rem .9rem}.lm-brand span,.lm-panel>header span,.lm-map-panel>header span,.lm-required span,.lm-uat label{color:#34e79a;font-size:.58rem;font-weight:950;text-transform:uppercase}.lm-brand span i{display:inline-block;width:.42rem;height:.42rem;margin-right:.3rem;border-radius:50%;background:#28e296;box-shadow:0 0 12px #28e296;animation:lm-pulse 2s ease-in-out infinite}.lm-brand h1{font-size:1.35rem;font-weight:950}.lm-brand p{color:#8299b4;font-size:.64rem}.lm-command-links{display:flex;flex-wrap:wrap;gap:.35rem}.bkb-cc-action{display:inline-flex;align-items:center;justify-content:center;min-height:2.25rem;border:1px solid rgba(51,218,167,.3);border-radius:5px;padding:.42rem .62rem;background:#0b2637;color:#dcf8ed;font-size:.64rem;font-weight:850}.bkb-cc-action:hover,.bkb-cc-action:focus-visible,.lm a:focus-visible,.lm button:focus-visible{border-color:#2ce49c;outline:none;box-shadow:0 0 0 2px rgba(44,228,156,.2)}.bkb-cc-action.is-primary{background:linear-gradient(135deg,#0a9168,#087251)}.lm-kpis{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:.5rem}.lm-kpi{position:relative;overflow:hidden;min-height:5.2rem;padding:.65rem .7rem}.lm-kpi span{color:#7c95b2;font-size:.56rem;font-weight:950;text-transform:uppercase}.lm-kpi strong{display:block;margin-top:.42rem;font-size:1.25rem}.lm-kpi i{position:absolute;right:.55rem;bottom:.55rem;width:2.3rem;border-bottom:2px solid #38d9ff;opacity:.4;transform:skewX(-32deg)}.lm-kpi.is-warning strong{color:#f5bd54}.lm-kpi.is-success strong{color:#42e6a0}.lm-kpi.is-cyan strong{color:#62dbff}.lm-kpi.is-muted strong{color:#91a3b8}.lm-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(19rem,.3fr);gap:.58rem;align-items:start}.lm-map-panel{overflow:hidden}.lm-map-panel>header,.lm-panel>header{display:flex;align-items:start;justify-content:space-between;gap:.7rem;border-bottom:1px solid var(--line);padding:.66rem .72rem;background:rgba(11,32,51,.63)}.lm-map-panel>header h2,.lm-panel>header h2{margin-top:.1rem;font-size:.84rem;font-weight:900}.lm-map-legend{display:flex;gap:.55rem;color:#8ea4bd;font-size:.53rem}.lm-map-legend b{display:flex;align-items:center;gap:.25rem}.lm-map-legend i{width:.45rem;height:.45rem;border-radius:50%;background:#42e6a0;box-shadow:0 0 8px rgba(66,230,160,.6)}.lm-map-legend i.is-stale{background:#f5bd54;box-shadow:0 0 8px rgba(245,189,84,.5)}.lm-map-stage{position:relative;height:clamp(36rem,calc(100vh - 18rem),54rem);background:#06111f}.lm-map-stage #live-operations-map{height:100%}.lm-map-empty{position:absolute;z-index:450;top:1rem;left:50%;width:min(34rem,calc(100% - 2rem));transform:translateX(-50%);border:1px solid rgba(52,211,153,.28);border-radius:6px;padding:.8rem .9rem;background:rgba(5,17,31,.93);box-shadow:0 18px 50px rgba(0,0,0,.3);backdrop-filter:blur(10px)}.lm-map-empty span{color:#42e6a0;font-size:.57rem;font-weight:950;text-transform:uppercase}.lm-map-empty strong{display:block;margin-top:.18rem;font-size:.78rem}.lm-map-empty p{margin-top:.18rem;color:#91a7bf;font-size:.6rem}.lm-map-empty div{display:flex;gap:.35rem;margin-top:.5rem}.lm-map-empty a{border:1px solid rgba(51,218,167,.3);border-radius:4px;padding:.35rem .45rem;color:#dcf8ed;font-size:.57rem;font-weight:850}.lm-map-status{position:absolute;z-index:500;bottom:.75rem;left:.75rem;border:1px solid var(--line);border-radius:4px;padding:.4rem .5rem;background:rgba(5,17,31,.92);color:#a5b8cd;font-size:.55rem}.lm-map-panel>footer{display:flex;justify-content:space-between;gap:.8rem;border-top:1px solid var(--line);padding:.42rem .65rem;color:#718aa8;font-size:.53rem}.lm-map-panel>footer strong{color:#51dda7}.lm-rail{display:grid;gap:.58rem}.lm-panel{overflow:hidden}.lm-details{display:grid;gap:.36rem;padding:.58rem .62rem}.lm-details div{display:flex;justify-content:space-between;gap:.6rem;border-bottom:1px solid rgba(121,158,194,.09);padding-bottom:.32rem}.lm-details dt{color:#718aa8;font-size:.56rem}.lm-details dd{max-width:62%;font-size:.57rem;text-align:right}.lm-details dd.is-good{color:#42e6a0}.lm-details dd.is-warning{color:#f5bd54}.lm-details dd.is-muted{color:#849ab3}.lm-links{display:grid;gap:.3rem;border-top:1px solid var(--line);padding:.55rem}.lm-links a{display:flex;justify-content:space-between;border:1px solid var(--line);border-radius:4px;padding:.36rem .42rem;color:#c9daea;font-size:.56rem}.lm-links span{color:#46dda5}.lm-required{margin:.2rem .6rem .6rem;border:1px solid rgba(245,189,84,.24);border-radius:5px;padding:.55rem;background:rgba(245,189,84,.06)}.lm-required strong{display:block;margin-top:.18rem;font-size:.61rem}.lm-required p,.lm-support p,.lm-uat p{margin-top:.2rem;color:#7891ae;font-size:.55rem;line-height:1.45}.lm-support{padding:.58rem}.lm-support strong{font-size:.61rem}.lm-support a{display:block;margin-top:.45rem;border:1px solid var(--line);border-radius:4px;padding:.35rem .42rem;color:#d7eee4;font-size:.56rem}.lm-uat{padding-bottom:.58rem}.lm-uat>p,.lm-uat label,.lm-uat input,.lm-uat button{margin:.52rem .58rem 0}.lm-uat label{display:block}.lm-uat input{width:calc(100% - 1.16rem);border:1px solid var(--line);border-radius:4px;padding:.38rem .44rem;background:#06111f;color:#d7e5f2;font-size:.55rem}.lm-uat button{border:1px solid rgba(51,218,167,.3);border-radius:4px;padding:.35rem .48rem;color:#dcf8ed;font-size:.56rem}.bkb-cc-badge{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:.12rem .32rem;color:#b9cbe0;font-size:.49rem;font-weight:950;text-transform:uppercase}.bkb-cc-empty{padding:1.5rem .8rem;text-align:center}.bkb-cc-empty p{color:#91a5c0}@keyframes lm-pulse{0%,100%{opacity:.65;transform:scale(.88)}50%{opacity:1;transform:scale(1.12)}}@media(prefers-reduced-motion:reduce){.lm *{animation:none!important;transition:none!important}}@media(max-width:1250px){.lm-kpis{grid-template-columns:repeat(3,1fr)}.lm-grid{grid-template-columns:1fr}.lm-rail{grid-template-columns:repeat(2,1fr)}}@media(max-width:760px){.lm-command{align-items:start;flex-direction:column}.lm-command-links{display:grid;width:100%;grid-template-columns:1fr}.lm-kpis,.lm-rail{grid-template-columns:1fr 1fr}.lm-map-stage{height:32rem}.lm-map-panel>footer{flex-direction:column}}@media(max-width:520px){.lm-kpis,.lm-rail{grid-template-columns:1fr}.lm-map-empty div{flex-direction:column}}
</style>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{const defaults=@json($mapDefaults),status=document.getElementById('live-map-status'),empty=document.getElementById('live-map-empty'),el=document.getElementById('live-operations-map'),copy=document.getElementById('copy-mobile-uat-url'),url=document.getElementById('mobile-uat-url');const map=L.map(el,{center:[defaults.lat,defaults.lng],zoom:defaults.zoom});const markers=L.layerGroup().addTo(map);let previousCount=0;L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);async function refresh(){try{const response=await fetch(@json(route('admin.live-operations-map.data')),{headers:{Accept:'application/json'},cache:'no-store'});if(!response.ok)throw new Error('Telemetry endpoint returned '+response.status);const data=await response.json();markers.clearLayers();empty.style.display=data.count?'none':'block';if(!data.count){status.textContent='Map center only — no worker marker. Checking every 12 seconds.';previousCount=0;return}const bounds=[];data.markers.forEach(m=>{bounds.push([m.latitude,m.longitude]);const age=m.captured_at?Math.max(0,Math.round((Date.now()-new Date(m.captured_at).getTime())/60000)):null;const stale=age!==null&&age>2;const popup=[`<strong>${escapeHtml(m.worker.name||'Worker #'+m.worker.id)}</strong>`,escapeHtml(m.worker.email||''),`Order: ${escapeHtml(m.order_number||'No linked order')}`,`Order status: ${escapeHtml(m.order_status||'Unknown')}`,`Presence: ${escapeHtml(m.presence_status)}`,`Accuracy: ${m.accuracy_meters??'Unknown'} m`,`Captured: ${escapeHtml(m.captured_at||m.created_at||'Unknown')}`,stale?'Status: stale ping':'Status: current ping'].join('<br>');L.marker([m.latitude,m.longitude],{opacity:stale ? .68 : 1}).addTo(markers).bindPopup(popup)});if(previousCount===0)map.fitBounds(bounds,{padding:[32,32],maxZoom:16});previousCount=data.count;status.textContent=`Showing ${data.count} real marker(s). Auto-refresh: 12 seconds.`}catch(error){status.textContent='Live map unavailable: '+error.message}}copy?.addEventListener('click',async()=>{try{await navigator.clipboard.writeText(url.value);copy.textContent='Copied'}catch{url.select();copy.textContent='Select and copy URL'}});refresh();setInterval(refresh,12000)});function escapeHtml(value){const div=document.createElement('div');div.textContent=value;return div.innerHTML}
</script>
@endpush
</x-filament-panels::page>
