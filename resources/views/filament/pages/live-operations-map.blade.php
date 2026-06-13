<x-filament-panels::page>
<x-admin-os.module-shell class="mx" label="BiKuBe LiveOps Matrix">
    <header class="mx-head">
        <div><span class="mx-kicker"><i></i> LiveOps Matrix · polling {{ $mapDefaults['refresh_seconds'] }}s</span><h1>Live Operations Map</h1><p>Real GPS, active zones and operational context. No inferred markers or routes.</p></div>
        <nav aria-label="LiveOps quick links">
            <x-admin-os.action-button :href="route('filament.admin.pages.dispatch-center')" tone="primary">Dispatch Center</x-admin-os.action-button>
            @if($assignment?->order)<x-admin-os.action-button :href="\App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$assignment->order])">Active order</x-admin-os.action-button>@endif
            <button type="button" id="mx-refresh">Refresh map</button>
        </nav>
    </header>

    <section class="mx-kpis" aria-label="Live operations metrics">
        @foreach([
            ['pings','Real GPS pings','cyan'],['active_assignments','Active assignments','green'],
            ['workers_with_ping','Workers with ping','green'],['orders_with_ping','Orders with ping','cyan'],
            ['stale_pings','Stale GPS','amber'],['zones','Active zones','purple'],
        ] as [$key,$label,$tone])
            <article class="mx-kpi is-{{ $tone }}"><span>{{ $label }}</span><strong>{{ $metrics[$key] }}</strong><i></i></article>
        @endforeach
    </section>

    <div class="mx-workspace">
        <aside class="mx-left">
            <section class="mx-panel">
                <header><div><span>Map layers</span><h2>Basemap provider</h2></div><b id="mx-layer-state">{{ str($mapDefaults['default_layer'])->title() }}</b></header>
                <div id="mx-layers" class="mx-layer-grid" role="radiogroup" aria-label="Basemap layer">
                    @foreach(['standard'=>'Standard','satellite'=>'Satellite','hybrid'=>'Hybrid','terrain'=>'Terrain'] as $key=>$label)
                        <button type="button" data-layer="{{ $key }}" role="radio" aria-checked="{{ $mapDefaults['default_layer']===$key?'true':'false' }}" @disabled(!in_array($key,$mapDefaults['enabled_layers'],true))>{{ $label }}<small>{{ $key === 'standard' ? 'OpenStreetMap' : ($key === 'terrain' ? 'OpenTopoMap' : 'Esri') }}</small></button>
                    @endforeach
                </div>
            </section>

            <section class="mx-panel">
                <header><div><span>Visibility</span><h2>Entity filters</h2></div><b id="mx-visible-count">0</b></header>
                <div class="mx-filters">
                    @foreach(['workers'=>'Workers','orders'=>'Active orders','customers'=>'Customers','support'=>'Support issues','payment_issues'=>'Payment issues','stale_gps'=>'Stale GPS','zones'=>'Zones'] as $key=>$label)
                        <label><input type="checkbox" data-filter="{{ $key }}" checked><span>{{ $label }}</span><b data-count="{{ $key }}">0</b></label>
                    @endforeach
                </div>
            </section>

            <section class="mx-panel">
                <header><div><span>Active geofences</span><h2>Operation zones</h2></div><b>{{ $activeZones->count() }}</b></header>
                <div class="mx-zone-list">
                    @forelse($activeZones as $zone)
                        <article><i style="--zone:{{ $zone->color }}"></i><div><strong>{{ $zone->name }}</strong><span>{{ str($zone->type)->replace('_',' ')->title() }} · {{ $zone->radius_meters ? number_format($zone->radius_meters).' m' : 'Point' }}</span></div></article>
                    @empty
                        <p>No active operation zones. Right-click the map to create a real zone.</p>
                    @endforelse
                </div>
            </section>

            <section class="mx-panel">
                <header><div><span>Fleet queue</span><h2>Active orders</h2></div><b>{{ $activeOrders->count() }}</b></header>
                <div class="mx-zone-list">
                    @forelse($activeOrders as $activeOrder)
                        <article><i style="--zone:#38d9ff"></i><div><strong>{{ $activeOrder->order_number }}</strong><span>{{ str($activeOrder->status->value)->replace('_',' ')->title() }} · {{ $activeOrder->activeDispatchAssignment()?->assignedUser?->name ?? 'Unassigned' }} · {{ $activeOrder->workerLocationPings->isNotEmpty() ? 'GPS received' : 'Coordinates/GPS missing' }}</span></div></article>
                    @empty <p>No active orders.</p> @endforelse
                </div>
            </section>
        </aside>

        <main class="mx-map-panel live-processing-glow" aria-label="Live operations map workspace">
            <div class="mx-mapbar">
                <div><span>Verified operations surface</span><strong id="mx-map-summary">Loading real map data...</strong></div>
                <div class="mx-legend"><b><i class="worker"></i> Worker GPS</b><b><i class="stale"></i> Stale</b><b><i class="zone"></i> Zone</b></div>
            </div>
            <div class="mx-stage" wire:ignore>
                <div id="live-operations-map" aria-label="Interactive LiveOps operations map"></div>
                <div id="mx-empty" class="mx-empty"><strong>No real GPS ping yet</strong><span>Map center only. Worker must open the assigned order on HTTPS/mobile and send location.</span></div>
                <div id="mx-change" class="mx-change" role="status" aria-live="polite">Live data updated</div>
                <div id="mx-context" class="mx-context" role="menu" aria-label="Map actions" tabindex="-1">
                    <header><span>Map context</span><strong id="mx-coordinates">—</strong></header>
                    <button type="button" role="menuitem" data-action="create_service_zone">Create service zone here</button>
                    <button type="button" role="menuitem" data-action="create_no_go_zone">Create no-go zone here</button>
                    <button type="button" role="menuitem" data-action="create_priority_zone">Create priority zone here</button>
                    <button type="button" role="menuitem" data-action="create_support_incident">Create support incident here</button>
                    <button type="button" role="menuitem" data-action="add_dispatch_note" @if(!$assignment?->order) data-disabled-reason="Select an active order first." @endif title="{{ $assignment?->order ? 'Create a real dispatch event for the selected active order.' : 'Select an active order first.' }}">Add dispatch note at location</button>
                    <button type="button" role="menuitem" data-action="create_support_ticket" @if(!$assignment?->order) data-disabled-reason="Select an order first or create a support incident zone." @endif title="{{ $assignment?->order ? 'Create a real support ticket linked to the selected active order.' : 'Select an order first or create a support incident zone.' }}">Create support ticket at location</button>
                    <button type="button" role="menuitem" data-action="search_nearby" data-disabled-reason="Nearby search requires order/worker coordinates. No real coordinates available yet." title="Nearby search requires order/worker coordinates. No real coordinates available yet.">Search nearby orders/workers<small>No real coordinates available yet</small></button>
                    <button type="button" role="menuitem" data-action="copy">Copy coordinates</button>
                    <a id="mx-external-map" role="menuitem" href="#" target="_blank" rel="noopener">Open in external map</a>
                    <output id="mx-context-status" aria-live="polite"></output>
                    <input id="mx-coordinate-fallback" readonly aria-label="Selected coordinates">
                </div>
            </div>
            <footer><span id="mx-status">Loading protected map endpoint...</span><strong>Right-click map for operational actions</strong></footer>
        </main>

        <aside class="mx-right">
            <section class="mx-panel live-processing-glow">
                <header><div><span>Current operation</span><h2>{{ $assignment?->order?->order_number ?? 'No active assignment' }}</h2></div>@if($assignment)<x-admin-os.status-badge :value="$assignment->status" />@endif</header>
                @if($assignment?->order)
                    <dl>
                        <div><dt>Worker</dt><dd>{{ $assignment->assignedUser?->name ?? 'Worker missing' }}</dd></div>
                        <div><dt>Presence</dt><dd>{{ str($assignment->assignedUser?->workerAvailability?->status ?? 'offline')->title() }}</dd></div>
                        <div><dt>Order status</dt><dd>{{ str($assignment->order->status->value)->replace('_',' ')->title() }}</dd></div>
                        <div><dt>Real GPS</dt><dd class="{{ $latestPing?'good':'warn' }}">{{ $latestPing?->captured_at?->diffForHumans() ?? 'No ping' }}</dd></div>
                        <div><dt>Support</dt><dd>{{ $assignment->order->supportTickets->whereNotIn('status',['resolved','closed'])->count() }} open</dd></div>
                    </dl>
                    <div class="mx-actions"><a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$assignment->order]) }}">Open order</a><a href="{{ route('worker.orders.show',$assignment->order) }}">Worker cockpit</a></div>
                @else <x-admin-os.empty-state title="No active assignment." body="Open Dispatch Center to select an operational order." /> @endif
            </section>

            <section class="mx-panel">
                <header><div><span>GPS readiness</span><h2>{{ $latestPing ? 'Telemetry received' : 'Awaiting real ping' }}</h2></div></header>
                <dl>
                    <div><dt>Tracking flow</dt><dd class="{{ $gpsTrackingEnabled?'good':'warn' }}">{{ $gpsTrackingEnabled?'Enabled':'Disabled' }}</dd></div>
                    <div><dt>Accepted accuracy</dt><dd>{{ number_format($mapDefaults['max_accuracy']) }} m</dd></div>
                    <div><dt>Stale threshold</dt><dd>{{ $mapDefaults['stale_seconds'] }} sec</dd></div>
                    <div><dt>Customer tracking</dt><dd class="muted">{{ $metrics['customer_tracking']?'Enabled':'Disabled' }}</dd></div>
                </dl>
            </section>

            <section class="mx-panel">
                <header><div><span>Fleet roster</span><h2>Approved workers</h2></div><b>{{ $fleetWorkers->count() }}</b></header>
                <div class="mx-zone-list">
                    @forelse($fleetWorkers as $profile)
                        <article><i style="--zone:{{ in_array($profile->user?->workerAvailability?->status,['online','available'],true) ? '#10b981' : '#64748b' }}"></i><div><strong>{{ $profile->display_name ?: $profile->user?->name ?: 'Unlinked worker' }}</strong><span>{{ str($profile->user?->workerAvailability?->status ?? 'offline')->title() }} · {{ $profile->user?->locationPings?->isNotEmpty() ? 'Real GPS received' : 'No GPS ping yet' }}</span></div></article>
                    @empty <p>No approved worker profiles.</p> @endforelse
                </div>
            </section>

            <section id="mx-zone-editor" class="mx-panel mx-editor {{ $activeContextEditor === 'zone' ? 'open' : '' }}">
                <header><div><span>Context action</span><h2>Create persisted zone</h2></div><button type="button" wire:click="closeContextEditor" aria-label="Close zone editor">×</button></header>
                <form wire:submit="createZone">
                    <input type="hidden" id="mx-context-lat" wire:model="contextLat">
                    <input type="hidden" id="mx-context-lng" wire:model="contextLng">
                    <label>Name<input wire:model="zoneName" required maxlength="150" placeholder="Operational zone name"></label>
                    <label>Type<select wire:model="zoneType">@foreach(['service_area','priority_area','no_go_area','temporary_busy_area','pickup_hotspot','support_incident'] as $type)<option value="{{ $type }}">{{ str($type)->replace('_',' ')->title() }}</option>@endforeach</select></label>
                    <label>Radius meters<input wire:model="zoneRadius" type="number" min="25" max="50000"></label>
                    <label>Operational note<textarea wire:model="zoneNote" rows="2" placeholder="Reason and operating instruction"></textarea></label>
                    <button type="submit">Create real zone</button>
                    <p>Circle/point zones are supported. Polygon drawing is deferred because no approved drawing plugin is installed.</p>
                </form>
            </section>
            <section id="mx-dispatch-editor" class="mx-panel mx-editor {{ $activeContextEditor === 'dispatch' ? 'open' : '' }}">
                <header><div><span>Context action</span><h2>Add dispatch note</h2></div><button type="button" wire:click="closeContextEditor" aria-label="Close dispatch note editor">?</button></header>
                <form wire:submit="addDispatchNoteAtLocation"><label>Dispatch note<textarea wire:model="dispatchLocationNote" rows="3" required maxlength="2000" placeholder="Operational note for the selected location"></textarea></label><button type="submit">Record dispatch event</button><p>Creates a real dispatch event for the selected active order and assignment.</p></form>
            </section>
            <section id="mx-support-editor" class="mx-panel mx-editor {{ $activeContextEditor === 'support' ? 'open' : '' }}">
                <header><div><span>Context action</span><h2>Create location support ticket</h2></div><button type="button" wire:click="closeContextEditor" aria-label="Close support ticket editor">?</button></header>
                <form wire:submit="createSupportAtLocation"><label>Subject<input wire:model="supportSubject" required maxlength="255" placeholder="Location incident subject"></label><label>Priority<select wire:model="supportPriority"><option value="low">Low</option><option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option></select></label><label>Category<select wire:model="supportCategory"><option value="delivery_issue">Delivery issue</option><option value="order_issue">Order issue</option><option value="worker_issue">Worker issue</option><option value="system_issue">System issue</option><option value="other">Other</option></select></label><label>Optional internal note<textarea wire:model="supportInternalNote" rows="3" maxlength="5000" placeholder="Visible only inside Admin OS"></textarea></label><button type="submit">Create real support ticket</button><p>No customer or worker reply is created automatically.</p></form>
            </section>
        </aside>
    </div>
</x-admin-os.module-shell>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
.fi-page-header{display:none}.mx{--line:rgba(119,157,194,.18);--panel:rgba(5,18,32,.96);--muted:#7892ae;display:grid;gap:.55rem;color:#edf7ff;font-size:.75rem}.mx-head,.mx-panel,.mx-map-panel,.mx-kpi{border:1px solid var(--line);border-radius:7px;background:linear-gradient(145deg,rgba(9,28,45,.98),rgba(3,14,27,.98));box-shadow:0 16px 42px rgba(0,0,0,.25)}.mx-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.7rem .8rem}.mx-head nav,.mx-actions{display:flex;flex-wrap:wrap;gap:.3rem}.mx-head button,.mx-actions a,.mx-context button,.mx-context a,.mx-editor button{border:1px solid rgba(44,225,157,.32);border-radius:4px;padding:.38rem .5rem;background:#0b2738;color:#e2fff3;font-size:.57rem;font-weight:850}.mx-kicker,.mx-panel header span,.mx-mapbar span{color:#35e69b;font-size:.52rem;font-weight:950;text-transform:uppercase}.mx-kicker i{display:inline-block;width:.4rem;height:.4rem;margin-right:.25rem;border-radius:50%;background:#2be59b;box-shadow:0 0 12px #2be59b;animation:mxPulse 2s ease-in-out infinite}.mx-head h1{font-size:1.2rem;font-weight:950}.mx-head p{color:#839bb6;font-size:.58rem}.mx-kpis{display:grid;grid-template-columns:repeat(6,1fr);gap:.45rem}.mx-kpi{position:relative;min-height:4.5rem;overflow:hidden;padding:.55rem .62rem}.mx-kpi span{color:#7891ad;font-size:.5rem;font-weight:900;text-transform:uppercase}.mx-kpi strong{display:block;margin-top:.34rem;font-size:1.15rem}.mx-kpi i{position:absolute;right:.5rem;bottom:.45rem;width:2.2rem;border-bottom:2px solid #38d9ff;opacity:.38;transform:skewX(-30deg)}.mx-kpi.is-green strong{color:#3fe39b}.mx-kpi.is-cyan strong{color:#61dafa}.mx-kpi.is-amber strong{color:#f5bd54}.mx-kpi.is-purple strong{color:#c18aff}.mx-workspace{display:grid;grid-template-columns:15rem minmax(36rem,1fr) 18rem;gap:.5rem;align-items:start}.mx-left,.mx-right{display:grid;gap:.5rem}.mx-panel{overflow:hidden}.mx-panel header,.mx-mapbar{display:flex;align-items:start;justify-content:space-between;gap:.5rem;border-bottom:1px solid var(--line);padding:.55rem .6rem;background:rgba(11,33,52,.62)}.mx-panel h2,.mx-mapbar strong{font-size:.7rem;font-weight:900}.mx-panel header>b{color:#44e3a2;font-size:.62rem}.mx-layer-grid{display:grid;grid-template-columns:1fr 1fr;gap:.3rem;padding:.45rem}.mx-layer-grid button{display:grid;gap:.08rem;border:1px solid var(--line);border-radius:4px;padding:.4rem;background:#061421;color:#d8e7f5;text-align:left;font-size:.57rem}.mx-layer-grid button[aria-checked=true]{border-color:#2ce49c;background:rgba(44,228,156,.1);box-shadow:inset 0 0 18px rgba(44,228,156,.06)}.mx-layer-grid small{color:#7089a4;font-size:.47rem}.mx-filters{display:grid;padding:.35rem .45rem}.mx-filters label{display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:.35rem;border-bottom:1px solid rgba(119,157,194,.1);padding:.32rem .1rem;color:#b7c9da;font-size:.55rem}.mx-filters input{accent-color:#2ce49c}.mx-filters b{color:#5edca8}.mx-zone-list{display:grid;max-height:13rem;overflow:auto;padding:.35rem .45rem}.mx-zone-list article{display:grid;grid-template-columns:.3rem 1fr;gap:.35rem;border-bottom:1px solid rgba(119,157,194,.1);padding:.36rem 0}.mx-zone-list i{width:.22rem;border-radius:9px;background:var(--zone)}.mx-zone-list strong,.mx-zone-list span{display:block;font-size:.54rem}.mx-zone-list span,.mx-zone-list p{color:#728ca7}.mx-map-panel{overflow:hidden}.mx-mapbar{min-height:3.2rem}.mx-legend{display:flex;flex-wrap:wrap;gap:.45rem;color:#91a6bd;font-size:.48rem}.mx-legend b{display:flex;align-items:center;gap:.18rem}.mx-legend i{width:.42rem;height:.42rem;border-radius:50%;background:#3ce09c}.mx-legend .stale{background:#8b99aa}.mx-legend .zone{border:1px solid #f5bd54;background:transparent}.mx-stage{position:relative;height:clamp(40rem,calc(100vh - 13rem),62rem);background:#06111e}.mx-stage #live-operations-map{height:100%}.mx-empty,.mx-change{position:absolute;z-index:500;border:1px solid var(--line);border-radius:5px;background:rgba(4,16,29,.92);backdrop-filter:blur(12px)}.mx-empty{top:1rem;left:50%;width:min(31rem,calc(100% - 2rem));transform:translateX(-50%);padding:.65rem .75rem;text-align:center}.mx-empty strong,.mx-empty span{display:block}.mx-empty strong{color:#f2c35d}.mx-empty span{margin-top:.12rem;color:#9aafc5;font-size:.53rem}.mx-change{right:.65rem;bottom:.65rem;padding:.38rem .5rem;color:#50e5a7;font-size:.52rem;opacity:0;transform:translateY(5px);transition:.2s}.mx-change.show{opacity:1;transform:none}.mx-context{position:absolute;z-index:1000;display:none;width:14rem;border:1px solid rgba(58,224,166,.32);border-radius:5px;padding:.3rem;background:rgba(3,15,28,.98);box-shadow:0 18px 55px rgba(0,0,0,.5)}.mx-context.open{display:grid}.mx-context header{display:grid;gap:.1rem;border-bottom:1px solid var(--line);padding:.35rem}.mx-context header span{color:#35e69b;font-size:.47rem;text-transform:uppercase}.mx-context header strong{font-size:.55rem}.mx-context button,.mx-context a{display:grid;gap:.08rem;border:0;border-bottom:1px solid rgba(119,157,194,.1);border-radius:0;background:transparent;text-align:left}.mx-context button:hover,.mx-context a:hover,.mx-context button:focus-visible,.mx-context a:focus-visible{background:rgba(44,228,156,.1);outline:none}.mx-context button:disabled,.mx-context [data-disabled-reason]{cursor:not-allowed;color:#657c95;opacity:.58}.mx-context button small{color:#657c95;font-size:.43rem;font-weight:650}.mx-context [aria-busy=true]{pointer-events:none;opacity:.65}.mx-context output{min-height:1.3rem;padding:.3rem;color:#55e6aa;font-size:.49rem}.mx-context input{width:100%;border:1px solid var(--line);border-radius:3px;padding:.25rem;background:#061421;color:#9fb4c8;font-size:.48rem}.mx-map-panel footer{display:flex;justify-content:space-between;gap:.5rem;border-top:1px solid var(--line);padding:.4rem .55rem;color:#758da8;font-size:.5rem}.mx-map-panel footer strong{color:#43dfa2}.mx-panel dl{display:grid;padding:.45rem .55rem}.mx-panel dl div{display:flex;justify-content:space-between;gap:.5rem;border-bottom:1px solid rgba(119,157,194,.1);padding:.32rem 0}.mx-panel dt{color:#7690aa}.mx-panel dd{max-width:58%;text-align:right}.mx-panel dd.good{color:#3fe39b}.mx-panel dd.warn{color:#f5bd54}.mx-panel dd.muted{color:#8294a8}.mx-actions{padding:.45rem}.mx-actions a{flex:1;text-align:center}.mx-editor{position:fixed;z-index:1600;display:none;top:50%;left:50%;width:min(30rem,calc(100vw - 2rem));max-height:calc(100vh - 2rem);overflow:auto;transform:translate(-50%,-50%);box-shadow:0 28px 90px rgba(0,0,0,.75)}.mx-editor.open{display:block}.mx-editor header button{border:0;background:transparent;font-size:1rem}.mx-editor form{display:grid;gap:.4rem;padding:.5rem}.mx-editor label{display:grid;gap:.15rem;color:#839bb5;font-size:.52rem}.mx-editor input,.mx-editor select,.mx-editor textarea{border:1px solid var(--line);border-radius:4px;padding:.38rem;background:#061421;color:#e5f2fc;font-size:.56rem}.mx-editor p{color:#718aa5;font-size:.49rem}.live-processing-glow{animation:mxBorder 4s ease-in-out infinite}.live-warning-glow{animation:mxWarn 2.4s ease-in-out infinite}@keyframes mxPulse{50%{opacity:.55;transform:scale(.78)}}@keyframes mxBorder{50%{border-color:rgba(48,220,169,.38);box-shadow:0 0 28px rgba(36,207,156,.08)}}@keyframes mxWarn{50%{border-color:rgba(245,189,84,.55);box-shadow:0 0 30px rgba(245,189,84,.1)}}@media(prefers-reduced-motion:reduce){.mx *{animation:none!important;transition:none!important}}@media(max-width:1450px){.mx-workspace{grid-template-columns:14rem minmax(35rem,1fr)}.mx-right{grid-column:1/-1;grid-template-columns:repeat(3,1fr)}}@media(max-width:1050px){.mx-kpis{grid-template-columns:repeat(3,1fr)}.mx-workspace{grid-template-columns:1fr}.mx-left,.mx-right{grid-template-columns:repeat(2,1fr)}.mx-map-panel{grid-row:1}.mx-stage{height:38rem}}@media(max-width:650px){.mx-head{align-items:start;flex-direction:column}.mx-kpis,.mx-left,.mx-right{grid-template-columns:1fr}.mx-stage{height:32rem}}
/* LiveOps viewport stabilization */
.mx{min-width:0}.mx-kpis{grid-template-columns:repeat(6,minmax(0,1fr))}.mx-kpi,.mx-panel,.mx-map-panel{min-width:0}
.mx-workspace{grid-template-columns:minmax(13rem,15rem) minmax(0,1fr) minmax(15rem,18rem);min-width:0}
.mx-left,.mx-right{min-width:0;max-height:calc(100vh - 8rem);overflow:auto;scrollbar-width:thin}
.mx-map-panel{scroll-margin-top:5rem}.mx-stage{height:clamp(32rem,calc(100vh - 14rem),48rem);min-height:32rem}
.mx-stage #live-operations-map{width:100%;height:100%;isolation:isolate}.leaflet-container{background:#06111e}
.mx-context{z-index:1200;max-height:calc(100% - 1rem);overflow:auto}
@media(max-width:1250px){.mx-workspace{grid-template-columns:14rem minmax(0,1fr)}.mx-right{grid-column:1/-1;grid-template-columns:repeat(3,minmax(0,1fr));max-height:none;overflow:visible}}
@media(max-width:1050px){.mx-workspace{grid-template-columns:1fr}.mx-left,.mx-right{grid-template-columns:repeat(2,minmax(0,1fr));max-height:none;overflow:visible}.mx-map-panel{grid-row:1}}
@media(max-width:650px){.mx-kpis,.mx-left,.mx-right{grid-template-columns:1fr}.mx-stage{height:32rem}}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(()=>{const init=()=>{const container=document.getElementById('live-operations-map');if(!container||container.dataset.leafletReady==='1'||typeof L==='undefined')return;window.__bkbLiveOpsMapDestroy?.();container.dataset.leafletReady='1';const d=@json($mapDefaults),endpoint=@json(route('admin.live-operations-map.data')),map=L.map(container,{center:[d.lat,d.lng],zoom:d.zoom,zoomControl:true}),groups={workers:L.layerGroup().addTo(map),zones:L.layerGroup().addTo(map)},filters={};let lastSignature='',ctx=null,currentLayer=null,destroyed=false;
const tiles={standard:L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}),satellite:L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,attribution:'Tiles &copy; Esri'}),hybrid:L.layerGroup([L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,attribution:'Tiles &copy; Esri'}),L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,attribution:'Labels &copy; Esri'})]),terrain:L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',{maxZoom:17,attribution:'Map data &copy; OpenStreetMap contributors, SRTM | Map style &copy; OpenTopoMap'})};
function layer(name){if(currentLayer)map.removeLayer(currentLayer);currentLayer=tiles[name]||tiles.standard;currentLayer.addTo(map);document.querySelectorAll('[data-layer]').forEach(b=>{b.setAttribute('aria-checked',b.dataset.layer===name?'true':'false')});document.getElementById('mx-layer-state').textContent=name[0].toUpperCase()+name.slice(1);sessionStorage.setItem('bkb-map-layer',name)}
const resize=()=>!destroyed&&container.isConnected&&map.invalidateSize();layer(sessionStorage.getItem('bkb-map-layer')||d.default_layer||'standard');setTimeout(resize,100);window.addEventListener('resize',resize);document.querySelectorAll('[data-layer]').forEach(b=>b.addEventListener('click',()=>!b.disabled&&layer(b.dataset.layer)));
document.querySelectorAll('[data-filter]').forEach(i=>{filters[i.dataset.filter]=i.checked;i.addEventListener('change',()=>{filters[i.dataset.filter]=i.checked;applyVisibility()})});
function applyVisibility(){filters.workers?map.addLayer(groups.workers):map.removeLayer(groups.workers);filters.zones?map.addLayer(groups.zones):map.removeLayer(groups.zones)}
function esc(v){const e=document.createElement('div');e.textContent=v??'';return e.innerHTML}function zoneShape(z){const c=z.coordinates||{},opts={color:z.color||'#22d3ee',weight:2,fillOpacity:.16};return z.geometry_type==='circle'?L.circle([c.lat,c.lng],{...opts,radius:z.radius_meters}):L.circleMarker([c.lat,c.lng],{...opts,radius:8})}
async function refresh(){if(destroyed||!container.isConnected)return;try{const r=await fetch(endpoint,{headers:{Accept:'application/json'},cache:'no-store'});if(!r.ok)throw Error('protected endpoint '+r.status);const data=await r.json(),sig=JSON.stringify([data.count,data.zones?.length,data.refreshed_at?.slice(0,16)]);if(destroyed||!container.isConnected)return;groups.workers.clearLayers();groups.zones.clearLayers();(data.markers||[]).forEach(m=>{const icon=L.divIcon({className:'',html:`<span style="display:grid;width:24px;height:24px;place-items:center;border:2px solid ${m.stale?'#8492a6':'#39e49e'};border-radius:50%;background:#071826;color:#fff;box-shadow:0 0 18px ${m.stale?'#8492a688':'#39e49e88'}">↗</span>`,iconSize:[24,24]});L.marker([m.latitude,m.longitude],{icon}).addTo(groups.workers).bindPopup(`<b>${esc(m.worker.name||m.worker.email)}</b><br>Order: ${esc(m.order_number||'Not linked')}<br>Presence: ${esc(m.presence_status)}<br>Accuracy: ${esc(m.accuracy_meters??'Unknown')} m<br>Captured: ${esc(m.captured_at||m.created_at)}`)});(data.zones||[]).forEach(z=>zoneShape(z).addTo(groups.zones).bindPopup(`<b>${esc(z.name)}</b><br>${esc(z.type.replaceAll('_',' '))}<br>${z.radius_meters?esc(z.radius_meters)+' m':'Point'}<br>Created by: ${esc(z.created_by||'System')}`));Object.entries(data.counts||{}).forEach(([k,v])=>document.querySelector(`[data-count="${k}"]`)?.replaceChildren(String(v)));document.getElementById('mx-visible-count').textContent=(data.count||0)+(data.zones?.length||0);document.getElementById('mx-empty').style.display=data.count?'none':'block';document.getElementById('mx-status').textContent=`${data.count} real worker marker(s), ${data.zones?.length||0} active zone(s). Refreshed ${new Date().toLocaleTimeString()}`;document.getElementById('mx-map-summary').textContent=`${data.count} verified marker(s) · ${data.zones?.length||0} active zone(s)`;if(lastSignature&&lastSignature!==sig){const x=document.getElementById('mx-change');x.classList.add('show');setTimeout(()=>x.classList.remove('show'),1800)}lastSignature=sig}catch(e){if(!destroyed)document.getElementById('mx-status').textContent='Live map unavailable: '+e.message}}
const menu=document.getElementById('mx-context'),status=document.getElementById('mx-context-status'),fallback=document.getElementById('mx-coordinate-fallback');const closeMenu=()=>menu.classList.remove('open'),closeEditors=()=>document.querySelectorAll('.mx-editor').forEach(e=>e.classList.remove('open')),showStatus=(m,bad=false)=>{status.textContent=m;status.style.color=bad?'#f5bd54':'#55e6aa'},component=()=>{let node=container;while(node&&!node.hasAttribute?.('wire:id'))node=node.parentElement;return node&&window.Livewire?.find(node.getAttribute('wire:id'))},runServerAction=async action=>{const c=component();if(!c){showStatus('Error: LiveOps action connection unavailable. Refresh the page.',true);return}menu.setAttribute('aria-busy','true');showStatus('Opening action...');try{const result=await c.call('handleMapContextAction',action,ctx.lat,ctx.lng);const bad=result?.status!=='opened';showStatus(result?.message||'Error: action unavailable.',bad);if(result?.status==='opened')closeMenu()}catch(e){showStatus('Error: '+(e?.message||'action unavailable.'),true)}finally{menu.removeAttribute('aria-busy')}};
L.DomEvent.disableClickPropagation(menu);L.DomEvent.disableScrollPropagation(menu);['mousedown','mouseup','click','dblclick','pointerdown','pointerup'].forEach(name=>menu.addEventListener(name,e=>e.stopPropagation()));
map.on('contextmenu',e=>{ctx=e.latlng;const value=`${ctx.lat.toFixed(6)}, ${ctx.lng.toFixed(6)}`;fallback.value=value;showStatus('Choose an operational action.');const p=map.latLngToContainerPoint(ctx),stage=document.querySelector('.mx-stage');menu.classList.add('open');menu.style.left=Math.max(8,Math.min(p.x,stage.clientWidth-menu.offsetWidth-8))+'px';menu.style.top=Math.max(8,Math.min(p.y,stage.clientHeight-menu.offsetHeight-8))+'px';document.getElementById('mx-coordinates').textContent=value;document.getElementById('mx-external-map').href=`https://www.openstreetmap.org/?mlat=${ctx.lat}&mlon=${ctx.lng}#map=16/${ctx.lat}/${ctx.lng}`;menu.querySelector('[role=menuitem]:not([data-disabled-reason])')?.focus({preventScroll:true})});map.on('click',closeMenu);document.addEventListener('pointerdown',e=>{if(menu.classList.contains('open')&&!menu.contains(e.target)&&!container.contains(e.target))closeMenu()});document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeMenu();closeEditors()}if(menu.classList.contains('open')&&['ArrowDown','ArrowUp','Home','End'].includes(e.key)){e.preventDefault();const items=[...menu.querySelectorAll('[role=menuitem]:not([data-disabled-reason])')],i=items.indexOf(document.activeElement);const n=e.key==='Home'?0:e.key==='End'?items.length-1:e.key==='ArrowDown'?(i+1)%items.length:(i-1+items.length)%items.length;items[n]?.focus()}});
menu.addEventListener('click',async e=>{e.preventDefault();const item=e.target.closest('[role=menuitem]');if(!item)return;if(item.dataset.disabledReason){showStatus('Disabled: '+item.dataset.disabledReason,true);return}if(item.tagName==='A'){window.open(item.href,'_blank','noopener');showStatus('Opened external OpenStreetMap.');closeMenu();return}if(!ctx)return showStatus('Error: Right-click the map first.',true);if(item.dataset.action==='copy'){const value=`${ctx.lat.toFixed(6)}, ${ctx.lng.toFixed(6)}`;try{await navigator.clipboard.writeText(value);showStatus('Copied coordinates.')}catch(err){fallback.value=value;fallback.focus();fallback.select();showStatus('Clipboard unavailable. Coordinates selected for manual copy.',true)}return}await runServerAction(item.dataset.action)});const actionComplete=e=>{closeEditors();closeMenu();showStatus(e.detail?.message||'Action completed.');refresh()};window.addEventListener('liveops-action-completed',actionComplete);document.getElementById('mx-refresh').addEventListener('click',refresh);refresh();const timer=setInterval(refresh,Math.max(10,d.refresh_seconds||12)*1000);const destroy=()=>{if(destroyed)return;destroyed=true;clearInterval(timer);window.removeEventListener('resize',resize);window.removeEventListener('liveops-action-completed',actionComplete);map.dragging.disable();map.off();map.remove();delete window.__bkbLiveOpsMapDestroy;container.dataset.leafletReady='0';document.removeEventListener('livewire:navigating',destroy)};window.__bkbLiveOpsMapDestroy=destroy;document.addEventListener('livewire:navigating',destroy,{once:true})};document.addEventListener('DOMContentLoaded',init,{once:true});document.addEventListener('livewire:navigated',init)})();
</script>
@endpush
</x-filament-panels::page>
