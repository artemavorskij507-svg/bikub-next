<x-filament-panels::page>
<x-admin-os.module-shell class="dc" label="Dispatch operations command workspace">
    <header class="dc-command">
        <div class="dc-brand">
            <span><i></i> Live operations dispatch</span>
            <h1>Dispatch Center</h1>
            <p>Prioritize orders, verify blockers and coordinate real workers from one operational surface.</p>
        </div>
        <nav class="dc-command-links" aria-label="Dispatch quick links">
            <x-admin-os.action-button :href="route('filament.admin.pages.live-operations-map')" tone="primary">Live map</x-admin-os.action-button>
            @can('admin.support.view')<x-admin-os.action-button :href="route('filament.admin.pages.support-center')">Support</x-admin-os.action-button>@endcan
            <x-admin-os.action-button :href="\App\Filament\Resources\Orders\OrderResource::getUrl('index')">Orders</x-admin-os.action-button>
            @can('admin.people.view')<x-admin-os.action-button :href="\App\Filament\Resources\WorkerProfiles\WorkerProfileResource::getUrl('index')">Workers</x-admin-os.action-button>@endcan
            <x-admin-os.action-button wire:click="$refresh">Refresh</x-admin-os.action-button>
        </nav>
    </header>

    <section class="dc-kpis" aria-label="Dispatch metrics">
        @foreach([
            ['waiting','Waiting dispatch','warning'], ['unassigned','Unassigned','danger'],
            ['active_assignments','Active assignments','success'], ['eligible_workers','Online eligible','cyan'],
            ['support_issues','Support issues','warning'], ['payment_not_ready','Payment issues','danger'],
            ['orders_with_ping','Orders with GPS','cyan'], ['completed_today','Completed today','success'],
            ['active_zones','Active zones','cyan'], ['stale_gps','Stale GPS','warning'],
        ] as [$key,$label,$tone])
            <article class="dc-kpi is-{{ $tone }}"><span>{{ $label }}</span><strong>{{ $metrics[$key] }}</strong><i></i></article>
        @endforeach
    </section>

    <div class="dc-grid">
        <section class="dc-panel dc-queue">
            <header><div><span>Dispatch queue</span><h2>{{ str($queueFilter)->replace('_', ' ')->title() }}</h2></div><strong>{{ $queue->count() }}</strong></header>
            <div class="dc-tabs" role="tablist" aria-label="Dispatch queues">
                @foreach(['waiting'=>'Waiting','unassigned'=>'Unassigned','assigned'=>'Assigned','active'=>'Active','risk'=>'Operational risk','payment'=>'Payment issues','support'=>'Support issues','completed'=>'Completed today'] as $key=>$label)
                    <button type="button" role="tab" wire:click="setQueueFilter('{{ $key }}')" class="{{ $queueFilter === $key ? 'is-active' : '' }}" aria-selected="{{ $queueFilter === $key ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="dc-queue-head"><span>Order / service</span><span>Signals</span><span>Updated</span></div>
            <div class="dc-queue-list">
                @forelse($queue as $order)
                    @php
                        $rowAssignment = $order->activeDispatchAssignment();
                        $rowSupport = $order->supportTickets->whereNotIn('status', ['resolved','closed']);
                        $rowPing = $order->workerLocationPings->first();
                    @endphp
                    <x-admin-os.queue-card wire:click="selectOrder({{ $order->id }})" class="dc-row" :selected="$selectedOrder?->is($order)">
                        <div class="dc-row-main">
                            <strong>{{ $order->order_number }}</strong>
                            <h3>{{ $order->scenario?->title ?? $order->service_scenario_key ?? 'Service scenario missing' }}</h3>
                            <p>{{ str($order->status->value)->replace('_',' ')->title() }} · {{ $rowAssignment?->assignedUser?->name ?? 'Unassigned' }}</p>
                        </div>
                        <div class="dc-row-signals">
                            @if($rowSupport->count())<b class="is-warning">{{ $rowSupport->count() }} support</b>@endif
                            <b class="{{ $rowPing ? 'is-good' : 'is-muted' }}">{{ $rowPing ? 'GPS received' : 'No GPS' }}</b>
                            <b class="{{ in_array($order->payment_status->value, ['pending','failed'], true) ? 'is-danger' : 'is-muted' }}">{{ str($order->payment_status->value)->replace('_',' ')->title() }}</b>
                        </div>
                        <div class="dc-row-time"><x-admin-os.status-badge :value="$order->status->value" /><time>{{ $order->updated_at?->diffForHumans() }}</time></div>
                    </x-admin-os.queue-card>
                @empty
                    <x-admin-os.empty-state title="No orders in this queue." body="Choose another operational queue or wait for a real order state change." />
                @endforelse
            </div>
        </section>

        <main class="dc-panel dc-order">
            @if($selectedOrder)
                <header class="dc-order-head">
                    <div>
                        <span>Selected operation</span>
                        <h2>{{ $selectedOrder->order_number }}</h2>
                        <p>{{ $selectedOrder->scenario?->title ?? $selectedOrder->service_scenario_key ?? 'Service scenario missing' }} · updated {{ $selectedOrder->updated_at?->diffForHumans() }}</p>
                    </div>
                    <div><x-admin-os.status-badge :value="$selectedOrder->status->value" /><x-admin-os.status-badge :value="$assignment?->status ?? 'unassigned'" /></div>
                </header>

                <div class="dc-actionbar">
                    <x-admin-os.action-button :href="\App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$selectedOrder])" tone="primary">Open order</x-admin-os.action-button>
                    <x-admin-os.action-button :href="route('filament.admin.pages.live-operations-map')">Live map</x-admin-os.action-button>
                    @can('admin.support.view')@if($latestSupportTicket)<x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$latestSupportTicket])">Open support</x-admin-os.action-button>@endif@endcan
                    @if($selectedOrder->customer_id === auth()->id())
                        <x-admin-os.action-button :href="route('account.orders.show',$selectedOrder)">Account order</x-admin-os.action-button>
                    @else
                        <button type="button" disabled title="{{ $selectedOrder->customer_id ? 'Customer account access is restricted to the linked owner.' : 'Customer ownership not linked.' }}">Account order unavailable</button>
                    @endif
                </div>

                <section class="dc-facts" aria-label="Selected order facts">
                    <div><span>Customer owner</span><strong>{{ $selectedOrder->customer?->name ?? 'Ownership not linked' }}</strong></div>
                    <div><span>Assigned worker</span><strong>{{ $assignment?->assignedUser?->name ?? 'Unassigned' }}</strong></div>
                    <div><span>Dispatch ready</span><strong>{{ $selectedOrder->isDispatchReady() ? 'Ready' : 'Not marked ready' }}</strong></div>
                    <div><span>Latest quote</span><strong>{{ $selectedOrder->latestPriceQuote()?->status ? str($selectedOrder->latestPriceQuote()->status)->replace('_',' ')->title() : 'Quote missing' }}</strong></div>
                    <div><span>Payment</span><strong>{{ str($selectedOrder->payment_status->value)->replace('_',' ')->title() }}</strong></div>
                    <div><span>GPS</span><strong>{{ $latestPing ? $latestPing->captured_at?->diffForHumans() : 'No real GPS ping' }}</strong></div>
                </section>

                <section class="dc-next">
                    <span>Recommended next action</span>
                    @if(!$assignment && !$selectedOrder->isDispatchReady())
                        <strong>Mark the order dispatch-ready after operational review.</strong>
                    @elseif(!$assignment && $workerCandidates->where('eligible', true)->isEmpty())
                        <strong>No eligible worker. Resolve worker approval, presence or capability blockers.</strong>
                    @elseif(!$assignment)
                        <strong>Assign one eligible worker.</strong>
                    @elseif(!$latestPing)
                        <strong>Assigned worker must send a real GPS ping from the worker cockpit on mobile HTTPS.</strong>
                    @else
                        <strong>Monitor worker progress, support signals and real GPS telemetry.</strong>
                    @endif
                </section>

                <section class="dc-liveops">
                    <div><span>LiveOps Matrix</span><strong>{{ $metrics['active_zones'] }} active zone(s) · {{ $metrics['stale_gps'] }} stale GPS signal(s)</strong><p>Default basemap: {{ str($defaultMapLayer)->title() }}. Order GPS: {{ $latestPing ? 'real telemetry received' : 'no real GPS ping' }}.</p></div>
                    <a href="{{ route('filament.admin.pages.live-operations-map') }}">Open LiveOps Matrix</a>
                </section>

                <section class="dc-controls" aria-label="Dispatch actions">
                    <div>
                        <span>Dispatch readiness</span>
                        @if(!$selectedOrder->isDispatchReady() && in_array($selectedOrder->status, [\App\Enums\OrderStatus::Submitted,\App\Enums\OrderStatus::Accepted], true))
                            <button type="button" wire:click="markReady({{ $selectedOrder->id }})">Mark ready for dispatch</button>
                        @else
                            <button type="button" disabled title="{{ $selectedOrder->isDispatchReady() ? 'Order is already dispatch-ready.' : 'Only submitted or accepted orders can be marked ready.' }}">Mark ready unavailable</button>
                        @endif
                    </div>
                    <div>
                        <span>Support control</span>
                        @can('admin.support.manage')
                            @if($openSupportTickets->isNotEmpty())
                                <button type="button" wire:confirm="An open support ticket exists. Create another ticket?" wire:click="createSupportTicket({{ $selectedOrder->id }}, true)">Create another support ticket</button>
                            @else
                                <button type="button" wire:click="createSupportTicket({{ $selectedOrder->id }})">Create support ticket</button>
                            @endif
                        @else
                            <button type="button" disabled title="Support management permission is required.">Support action unavailable</button>
                        @endcan
                    </div>
                    <div>
                        <span>Lifecycle status</span>
                        <button type="button" disabled title="Active delivery lifecycle transitions are owned by the assigned worker cockpit and validated workflow service.">Worker workflow owns status</button>
                    </div>
                </section>

                <form class="dc-note" wire:submit="addDispatchNote({{ $selectedOrder->id }})">
                    <label><span>Dispatch note</span><input wire:model="dispatchNote" type="text" placeholder="Record a real operational decision or blocker"></label>
                    <button type="submit">Add note</button>
                    @error('dispatchNote')<p>{{ $message }}</p>@enderror
                </form>

                <section class="dc-timeline">
                    <header><span>Dispatch timeline</span><strong>{{ $dispatchEvents->count() }} recent</strong></header>
                    <div>
                        @forelse($dispatchEvents as $event)
                            <article><i></i><div><strong>{{ str($event->event_type)->replace(['dispatch.','worker.','_'],['','',' '])->title() }}</strong><p>{{ $event->note ?: 'Operational event recorded.' }}</p></div><time>{{ $event->created_at?->diffForHumans() }}</time></article>
                        @empty
                            <x-admin-os.empty-state title="No dispatch events yet." body="Dispatch readiness, assignment and notes will appear here." />
                        @endforelse
                    </div>
                </section>
            @else
                <x-admin-os.empty-state title="No active order selected." body="Choose a real order from the dispatch queue." />
            @endif
        </main>

        <aside class="dc-context">
            <section class="dc-panel">
                <header><div><span>Worker eligibility</span><h2>Real candidates</h2></div><strong>{{ $workerCandidates->where('eligible', true)->count() }}</strong></header>
                @if($selectedOrder)
                    <div class="dc-workers">
                        @forelse($workerCandidates as $candidate)
                            <article class="{{ $candidate['eligible'] ? 'is-eligible' : '' }}">
                                <div><b>{{ str($candidate['profile']->display_name ?: $candidate['user']?->name ?: 'W')->substr(0,1)->upper() }}</b><span><strong>{{ $candidate['profile']->display_name ?: $candidate['user']?->name ?: 'Unlinked worker' }}</strong><small>{{ str($candidate['availability'])->title() }} · {{ $candidate['active_assignments'] }} active assignment(s)</small></span></div>
                                <p>{{ $candidate['reason'] }}</p>
                                <small>{{ $candidate['latest_ping'] ? 'Last GPS '.$candidate['latest_ping']->captured_at?->diffForHumans() : 'No real GPS ping' }}</small>
                                @if(!$assignment && $candidate['eligible'] && $candidate['user'])
                                    <button type="button" wire:click="assignWorker({{ $selectedOrder->id }}, {{ $candidate['user']->id }})">Assign worker</button>
                                @endif
                            </article>
                        @empty
                            <p class="dc-panel-empty">No real worker profiles exist.</p>
                        @endforelse
                    </div>
                @else <p class="dc-panel-empty">Select an order to evaluate real worker eligibility.</p>@endif
            </section>

            @if($selectedOrder && $assignment)
                <section class="dc-panel dc-unassign">
                    <header><div><span>Assignment control</span><h2>{{ $assignment->assignedUser?->name ?? 'Assigned worker' }}</h2></div></header>
                    <label><span>Required reason</span><input wire:model="unassignReason" type="text" placeholder="Reason for removing the worker"></label>
                    <button type="button" wire:confirm="Unassign this worker from the order?" wire:click="unassignWorker({{ $selectedOrder->id }})">Unassign worker</button>
                    @error('unassignReason')<p>{{ $message }}</p>@enderror
                </section>
            @endif

            <section class="dc-panel">
                <header><div><span>Operational blockers</span><h2>Readiness</h2></div></header>
                @if($selectedOrder)
                    <dl class="dc-readiness">
                        <div><dt>Support</dt><dd class="{{ $openSupportTickets->isEmpty() ? 'is-good' : 'is-warning' }}">{{ $openSupportTickets->count() }} open</dd></div>
                        <div><dt>Payment provider</dt><dd class="{{ $paymentProviderEnabled ? 'is-good' : 'is-warning' }}">{{ $paymentProviderEnabled ? 'Connected' : 'Not connected' }}</dd></div>
                        <div><dt>Quote</dt><dd class="{{ $selectedOrder->latestPriceQuote() ? 'is-good' : 'is-warning' }}">{{ $selectedOrder->latestPriceQuote()?->status ?? 'Missing' }}</dd></div>
                        <div><dt>GPS telemetry</dt><dd class="{{ $latestPing ? 'is-good' : 'is-warning' }}">{{ $latestPing ? 'Real ping received' : 'No real GPS ping' }}</dd></div>
                        <div><dt>Customer tracking</dt><dd class="{{ $customerTrackingEnabled ? 'is-good' : 'is-muted' }}">{{ $customerTrackingEnabled ? 'Enabled' : 'Disabled' }}</dd></div>
                        <div><dt>Order status actions</dt><dd class="is-muted">Worker lifecycle owns active delivery transitions</dd></div>
                    </dl>
                @else <p class="dc-panel-empty">Select an order to inspect blockers.</p>@endif
            </section>

            @if($latestSupportTicket)
                <section class="dc-panel">
                    <header><div><span>Support context</span><h2>{{ $latestSupportTicket->ticket_number }}</h2></div><x-admin-os.status-badge :value="$latestSupportTicket->priority" /></header>
                    <div class="dc-support"><strong>{{ $latestSupportTicket->subject }}</strong><p>{{ str($latestSupportTicket->status)->replace('_',' ')->title() }} · {{ $latestSupportTicket->assignee?->name ?? 'Unassigned' }}</p>@can('admin.support.view')<a href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$latestSupportTicket]) }}">Open support ticket</a>@else<p>Support view permission is required.</p>@endcan</div>
                </section>
            @endif
        </aside>
    </div>
</x-admin-os.module-shell>
<style>
.fi-page-header{display:none}.dc{--line:rgba(121,158,194,.17);--panel:#071525;--muted:#7891ae;--text:#ecf5ff;display:grid;gap:.68rem;color:var(--text);font-size:.8rem}.dc-command,.dc-panel,.dc-kpi{border:1px solid var(--line);border-radius:7px;background:linear-gradient(145deg,rgba(10,27,45,.97),rgba(4,16,29,.98));box-shadow:0 18px 46px rgba(0,0,0,.18)}.dc-command{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.78rem .9rem}.dc-brand span,.dc-panel>header span,.dc-next span,.dc-controls span,.dc-note span,.dc-unassign label span{color:#34e79a;font-size:.58rem;font-weight:950;text-transform:uppercase}.dc-brand span i{display:inline-block;width:.42rem;height:.42rem;margin-right:.3rem;border-radius:50%;background:#28e296;box-shadow:0 0 12px #28e296;animation:dc-pulse 2s ease-in-out infinite}.dc-brand h1{font-size:1.35rem;font-weight:950}.dc-brand p{color:#8299b4;font-size:.64rem}.dc-command-links,.dc-actionbar{display:flex;flex-wrap:wrap;gap:.35rem}.bkb-cc-action,.dc-actionbar button{display:inline-flex;align-items:center;justify-content:center;min-height:2.25rem;border:1px solid rgba(51,218,167,.3);border-radius:5px;padding:.42rem .62rem;background:#0b2637;color:#dcf8ed;font-size:.64rem;font-weight:850}.bkb-cc-action:hover,.bkb-cc-action:focus-visible,.dc button:not(:disabled):hover,.dc button:not(:disabled):focus-visible{border-color:#2ce49c;outline:none;box-shadow:0 8px 20px rgba(0,0,0,.25)}.bkb-cc-action.is-primary{background:linear-gradient(135deg,#0a9168,#087251);color:#fff}.dc-kpis{display:grid;grid-template-columns:repeat(8,minmax(0,1fr));gap:.5rem}.dc-kpi{position:relative;overflow:hidden;min-height:5.2rem;padding:.65rem .7rem}.dc-kpi span{color:#7c95b2;font-size:.56rem;font-weight:950;text-transform:uppercase}.dc-kpi strong{display:block;margin-top:.42rem;font-size:1.35rem}.dc-kpi i{position:absolute;right:.55rem;bottom:.55rem;width:2.3rem;border-bottom:2px solid #38d9ff;opacity:.4;transform:skewX(-32deg)}.dc-kpi.is-danger strong{color:#ff6c78}.dc-kpi.is-warning strong{color:#f5bd54}.dc-kpi.is-success strong{color:#42e6a0}.dc-kpi.is-cyan strong{color:#62dbff}.dc-grid{display:grid;grid-template-columns:minmax(23rem,.95fr) minmax(32rem,1.45fr) minmax(19rem,.72fr);gap:.58rem;align-items:start}.dc-panel{overflow:hidden}.dc-panel>header{display:flex;align-items:start;justify-content:space-between;gap:.7rem;border-bottom:1px solid var(--line);padding:.66rem .72rem;background:rgba(11,32,51,.63)}.dc-panel>header h2{margin-top:.1rem;font-size:.84rem;font-weight:900}.dc-panel>header>strong{color:#55e6ac}.dc-tabs{display:flex;flex-wrap:wrap;gap:.22rem;border-bottom:1px solid var(--line);padding:.4rem .48rem}.dc-tabs button{appearance:none;border:1px solid var(--line);border-radius:4px;padding:.26rem .4rem;background:transparent;color:#91a7bf;font-size:.58rem}.dc-tabs button.is-active{border-color:rgba(35,224,148,.5);background:rgba(35,224,148,.11);color:#dffcf0}.dc-queue-head,.dc-row{display:grid;grid-template-columns:1fr 6.2rem 5.4rem;gap:.45rem}.dc-queue-head{border-bottom:1px solid var(--line);padding:.35rem .62rem;color:#657f9e;font-size:.52rem;font-weight:900;text-transform:uppercase}.dc-queue-list{max-height:68vh;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(39,225,157,.28) transparent}.dc-row{position:relative;width:100%;border-bottom:1px solid var(--line);padding:.58rem .62rem;color:inherit;text-align:left;transition:background .15s ease,transform .15s ease}.dc-row:before{position:absolute;top:.35rem;bottom:.35rem;left:0;width:2px;background:transparent;content:''}.dc-row:hover,.dc-row.is-selected{background:linear-gradient(90deg,rgba(31,225,147,.11),rgba(43,185,229,.03));transform:translateX(1px)}.dc-row.is-selected:before{background:#28e296;box-shadow:0 0 12px #28e296}.dc-row-main strong{font-size:.68rem}.dc-row-main h3{margin-top:.14rem;font-size:.66rem;font-weight:850}.dc-row-main p,.dc-row-time time{margin-top:.16rem;color:#718aa8;font-size:.55rem}.dc-row-signals,.dc-row-time{display:grid;align-content:center;justify-items:start;gap:.16rem}.dc-row-signals b{color:#849ab3;font-size:.5rem}.dc-row-signals b.is-good{color:#42e6a0}.dc-row-signals b.is-warning{color:#f5bd54}.dc-row-signals b.is-danger{color:#ff6c78}.dc-order{display:grid}.dc-order-head>div:last-child{display:flex;gap:.3rem}.dc-order-head p{margin-top:.15rem;color:#7891ae;font-size:.58rem}.dc-actionbar{border-bottom:1px solid var(--line);padding:.46rem .62rem}.dc-actionbar button:disabled,.dc-controls button:disabled{cursor:not-allowed;opacity:.4}.dc-facts{display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid var(--line)}.dc-facts div{border-right:1px solid var(--line);border-bottom:1px solid var(--line);padding:.48rem .58rem}.dc-facts span{display:block;color:#718aa8;font-size:.5rem;font-weight:900;text-transform:uppercase}.dc-facts strong{display:block;margin-top:.15rem;font-size:.6rem}.dc-next{border-bottom:1px solid var(--line);padding:.58rem .65rem;background:rgba(245,189,84,.06)}.dc-next strong{display:block;margin-top:.18rem;color:#dce9f5;font-size:.64rem}.dc-controls{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid var(--line)}.dc-controls>div{display:grid;gap:.28rem;padding:.55rem .62rem;border-right:1px solid var(--line)}.dc-controls button,.dc-note button,.dc-unassign button{border:1px solid rgba(51,218,167,.3);border-radius:4px;padding:.38rem .5rem;background:#0b2637;color:#dcf8ed;font-size:.59rem;font-weight:850}.dc-note{display:grid;grid-template-columns:1fr auto;gap:.35rem;border-bottom:1px solid var(--line);padding:.52rem .62rem}.dc-note label,.dc-unassign label{display:grid;gap:.15rem}.dc-note input,.dc-unassign input{min-width:0;border:1px solid var(--line);border-radius:4px;padding:.36rem .45rem;background:rgba(2,12,24,.75);color:#e8f4ff;font-size:.6rem;outline:none}.dc-note input:focus,.dc-unassign input:focus{border-color:#28e296;box-shadow:0 0 0 3px rgba(40,226,150,.1)}.dc-note p,.dc-unassign p{grid-column:1/-1;color:#ff7f89;font-size:.55rem}.dc-timeline>header{display:flex;justify-content:space-between;padding:.55rem .65rem;color:#7891ae;font-size:.55rem;font-weight:900;text-transform:uppercase}.dc-timeline>div{max-height:27vh;overflow-y:auto;padding:0 .65rem .45rem}.dc-timeline article{display:grid;grid-template-columns:.35rem 1fr auto;gap:.45rem;border-top:1px solid var(--line);padding:.48rem 0}.dc-timeline article i{width:.28rem;height:1.7rem;border-radius:4px;background:#4bdcab}.dc-timeline article strong{font-size:.6rem}.dc-timeline article p,.dc-timeline article time{margin-top:.12rem;color:#718aa8;font-size:.53rem}.dc-context{display:grid;gap:.58rem}.dc-workers{display:grid;max-height:34vh;overflow:auto;padding:.45rem .55rem}.dc-workers article{display:grid;gap:.28rem;border-bottom:1px solid var(--line);padding:.48rem 0}.dc-workers article>div{display:flex;align-items:center;gap:.4rem}.dc-workers b{display:grid;width:1.65rem;height:1.65rem;place-items:center;border-radius:50%;background:#143047}.dc-workers article.is-eligible b{background:linear-gradient(135deg,#11936b,#07513e)}.dc-workers span{display:grid}.dc-workers strong{font-size:.6rem}.dc-workers small,.dc-workers p{color:#718aa8;font-size:.52rem}.dc-workers button{border:1px solid rgba(51,218,167,.3);border-radius:4px;padding:.3rem .4rem;color:#d9f8eb;font-size:.55rem}.dc-unassign{padding-bottom:.58rem}.dc-unassign label,.dc-unassign>button,.dc-unassign>p{margin:.55rem .6rem 0}.dc-readiness{display:grid;gap:.36rem;padding:.58rem .62rem}.dc-readiness div{display:flex;justify-content:space-between;gap:.6rem;border-bottom:1px solid rgba(121,158,194,.09);padding-bottom:.32rem}.dc-readiness dt{color:#718aa8;font-size:.56rem}.dc-readiness dd{max-width:62%;font-size:.57rem;text-align:right}.dc-readiness dd.is-good{color:#42e6a0}.dc-readiness dd.is-warning{color:#f5bd54}.dc-readiness dd.is-muted{color:#849ab3}.dc-support{padding:.6rem}.dc-support strong{font-size:.62rem}.dc-support p{margin-top:.2rem;color:#7891ae;font-size:.55rem}.dc-support a{display:block;margin-top:.45rem;border:1px solid var(--line);border-radius:4px;padding:.36rem .45rem;color:#d7eee4;font-size:.57rem}.dc-panel-empty{padding:.65rem;color:#7891ae;font-size:.58rem}.bkb-cc-empty{padding:2rem 1rem;text-align:center}.bkb-cc-empty p{color:#91a5c0}.bkb-cc-badge{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:.12rem .32rem;color:#b9cbe0;font-size:.49rem;font-weight:950;text-transform:uppercase}.bkb-cc-badge.is-urgent,.bkb-cc-badge.is-escalated{border-color:rgba(255,93,108,.52);color:#ff8994}@keyframes dc-pulse{0%,100%{opacity:.65;transform:scale(.88)}50%{opacity:1;transform:scale(1.12)}}@media(prefers-reduced-motion:reduce){.dc *{animation:none!important;transition:none!important}}@media(max-width:1500px){.dc-kpis{grid-template-columns:repeat(4,1fr)}.dc-grid{grid-template-columns:minmax(22rem,.9fr) minmax(30rem,1.3fr)}.dc-context{grid-column:1/-1;grid-template-columns:repeat(3,1fr)}}@media(max-width:1050px){.dc-command{align-items:start;flex-direction:column}.dc-grid,.dc-context{grid-template-columns:1fr}.dc-queue-list,.dc-timeline>div,.dc-workers{max-height:none}.dc-kpis{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.dc-kpis,.dc-facts,.dc-controls{grid-template-columns:1fr}.dc-row,.dc-queue-head{grid-template-columns:1fr 5rem}.dc-row-signals,.dc-queue-head span:nth-child(2){display:none}.dc-note{grid-template-columns:1fr}.dc-command-links{display:grid;width:100%;grid-template-columns:1fr 1fr}}
@media(min-width:651px){.dc-controls{grid-template-columns:repeat(3,1fr)}}
</style>
<style>
.dc-liveops{display:flex;align-items:center;justify-content:space-between;gap:.7rem;border-bottom:1px solid var(--line);padding:.55rem .65rem;background:rgba(56,217,255,.045);animation:dc-live 4s ease-in-out infinite}.dc-liveops span{color:#50d9ff;font-size:.52rem;font-weight:950;text-transform:uppercase}.dc-liveops strong{display:block;margin-top:.12rem;font-size:.6rem}.dc-liveops p{margin-top:.12rem;color:#7891ae;font-size:.52rem}.dc-liveops a{border:1px solid rgba(56,217,255,.3);border-radius:4px;padding:.38rem .5rem;color:#dff8ff;font-size:.56rem;font-weight:850;white-space:nowrap}@keyframes dc-live{50%{border-color:rgba(56,217,255,.35);box-shadow:inset 0 0 25px rgba(56,217,255,.035)}}@media(prefers-reduced-motion:reduce){.dc-liveops{animation:none}}
</style>
</x-filament-panels::page>
