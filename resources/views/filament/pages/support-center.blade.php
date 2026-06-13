<x-filament-panels::page>
@php
    $activity = $selectedTicket
        ? collect($selectedTicket->messages)->map(fn ($item) => ['type' => 'message', 'at' => $item->created_at, 'item' => $item])
            ->merge(collect($selectedTicket->events)->map(fn ($item) => ['type' => 'event', 'at' => $item->created_at, 'item' => $item]))
            ->sortByDesc('at')->take(10)
        : collect();
@endphp
<div class="sc">
    <header class="sc-command">
        <div class="sc-brand">
            <span><i></i> Support operations</span>
            <h1>Support Center</h1>
            <p>One command surface for tickets, conversations and operational context.</p>
        </div>
        <label class="sc-search">
            <span>Search support</span>
            <input type="search" wire:model.live.debounce.400ms="search" placeholder="Ticket, order or customer email..." aria-label="Search tickets, orders or customer email">
            <kbd>⌘ K</kbd>
        </label>
        <div class="sc-command-actions">
            <div class="sc-live"><i></i><span>Monitoring</span><strong>15s</strong></div>
            <x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('create')" tone="primary">New ticket</x-admin-os.action-button>
            <x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('index')">Operations</x-admin-os.action-button>
        </div>
    </header>

    <section class="sc-kpis" aria-label="Support metrics">
        @foreach([
            ['open','Open tickets','neutral'], ['urgent','Urgent','danger'], ['escalated','Escalated','warning'],
            ['unassigned','Unassigned','info'], ['waiting_customer','Waiting customer','info'],
            ['resolved_today','Resolved today','success'], ['avg_first_response','Avg. first response','cyan']
        ] as [$key,$label,$tone])
            <article class="sc-kpi is-{{ $tone }}"><span>{{ $label }}</span><strong>{{ $metrics[$key] }}</strong><i></i></article>
        @endforeach
    </section>

    <div class="sc-grid">
        <section class="sc-panel sc-queue">
            <header><div><span>Ticket queue</span><h2>{{ str($queueFilter)->replace('_',' ')->title() }}</h2></div><strong>{{ $activeQueue->count() }}</strong></header>
            <div class="sc-tabs" role="tablist" aria-label="Ticket queues">
                @foreach(['incoming'=>'Incoming','mine'=>'Mine','urgent'=>'Urgent','unassigned'=>'Unassigned','waiting_customer'=>'Waiting customer','waiting_worker'=>'Waiting worker','resolved'=>'Resolved'] as $key=>$label)
                    <button type="button" wire:click="setQueueFilter('{{ $key }}')" class="{{ $queueFilter === $key ? 'is-active' : '' }}" aria-selected="{{ $queueFilter === $key ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="sc-filters" aria-label="Ticket categories">
                @foreach(['all'=>'All','order_issue'=>'Orders','payment_issue'=>'Payments','worker_issue'=>'Workers','document_issue'=>'Documents','system_issue'=>'Technical'] as $key=>$label)
                    <button type="button" wire:click="setCategoryFilter('{{ $key }}')" class="{{ $categoryFilter === $key ? 'is-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="sc-queue-head"><span>Ticket / subject</span><span>Owner</span><span>Updated</span></div>
            <div class="sc-queue-list">
                @forelse($activeQueue as $ticket)
                    <button type="button" wire:click="selectTicket({{ $ticket->id }})" class="sc-row {{ $selectedTicket?->is($ticket) ? 'is-selected' : '' }}">
                        <div class="sc-row-main"><strong>{{ $ticket->ticket_number }}</strong><h3>{{ $ticket->subject }}</h3><p>{{ str($ticket->category)->replace('_',' ')->title() }} @if($ticket->order) · {{ $ticket->order->order_number }} @endif</p></div>
                        <div class="sc-row-owner"><x-admin-os.status-badge :value="$ticket->priority" /><span>{{ $ticket->assignee?->name ?? 'Unassigned' }}</span></div>
                        <div class="sc-row-time"><x-admin-os.status-badge :value="$ticket->status" /><time>{{ ($ticket->last_message_at ?? $ticket->updated_at)?->diffForHumans() }}</time></div>
                    </button>
                @empty
                    <x-admin-os.empty-state title="No tickets in this queue." body="Adjust the queue, category or search query." />
                @endforelse
            </div>
        </section>

        <main class="sc-panel sc-conversation">
            @if($selectedTicket)
                <header class="sc-ticket-head">
                    <div><span>{{ $selectedTicket->ticket_number }}</span><h2>{{ $selectedTicket->subject }}</h2><p>{{ str($selectedTicket->category)->replace('_',' ')->title() }} · Created {{ $selectedTicket->created_at?->diffForHumans() }}</p></div>
                    <div><x-admin-os.status-badge :value="$selectedTicket->priority" /><x-admin-os.status-badge :value="$selectedTicket->status" /></div>
                </header>
                <div class="sc-actionbar">
                    <x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$selectedTicket])" tone="primary">Open full ticket</x-admin-os.action-button>
                    @if($selectedTicket->assigned_to_id !== auth()->id())<x-admin-os.action-button wire:click="assignToMe({{ $selectedTicket->id }})">Assign to me</x-admin-os.action-button>@endif
                    @if($selectedTicket->order)<x-admin-os.action-button :href="\App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$selectedTicket->order])">Open order</x-admin-os.action-button>@endif
                </div>
                @if($selectedTicket->summary)<div class="sc-summary"><span>Issue summary</span><p>{{ $selectedTicket->summary }}</p></div>@endif
                <section class="sc-thread" aria-label="Ticket activity">
                    @forelse($activity as $row)
                        @php($item=$row['item'])
                        <article class="sc-message is-{{ $row['type'] }} {{ $row['type']==='message' ? 'is-'.$item->visibility : '' }}">
                            <div class="sc-avatar">{{ str($row['type']==='message' ? ($item->author?->name ?? 'S') : ($item->actor?->name ?? 'S'))->substr(0,1)->upper() }}</div>
                            <div><header><strong>{{ $row['type']==='message' ? ($item->author?->name ?? 'System') : str($item->event_type)->replace('_',' ')->title() }}</strong><span>{{ $row['type']==='message' ? str($item->visibility)->replace('_',' ')->upper() : 'SYSTEM EVENT' }}</span><time>{{ $row['at']?->diffForHumans() }}</time></header><p>{{ $row['type']==='message' ? $item->body : ($item->description ?: 'Operational event recorded.') }}</p></div>
                        </article>
                    @empty
                        <x-admin-os.empty-state title="No ticket activity yet." body="Add an internal note or linked-party reply below." />
                    @endforelse
                </section>
                <form class="sc-composer" wire:submit="sendMessage">
                    <div class="sc-composer-tabs">
                        <button type="button" wire:click="setComposerMode('internal')" class="{{ $composerMode==='internal'?'is-active':'' }}">Internal note</button>
                        <button type="button" wire:click="setComposerMode('customer')" class="{{ $composerMode==='customer'?'is-active':'' }}" @disabled(!$selectedTicket->customer_id)>Customer reply</button>
                        <button type="button" wire:click="setComposerMode('worker')" class="{{ $composerMode==='worker'?'is-active':'' }}" @disabled(!$selectedTicket->worker_profile_id)>Worker reply</button>
                    </div>
                    <textarea wire:model="messageBody" rows="3" placeholder="{{ $composerMode==='internal' ? 'Write an internal operational note...' : 'Write a visible reply...' }}" aria-label="Support message"></textarea>
                    @error('messageBody')<p class="sc-error">{{ $message }}</p>@enderror
                    <footer><span>{{ $composerMode==='internal' ? 'Visible only in Admin OS' : 'Visibility is explicitly controlled' }}</span><button type="submit">Send message</button></footer>
                </form>
            @else
                <x-admin-os.empty-state title="No ticket selected." body="Choose a ticket from the queue to open the operational workspace." />
            @endif
        </main>

        <aside class="sc-context">
            <section class="sc-panel">
                <header><div><span>Selected ticket</span><h2>Operational context</h2></div></header>
                @if($selectedTicket)
                    <dl class="sc-details">
                        <div><dt>Assigned</dt><dd>{{ $selectedTicket->assignee?->name ?? 'Unassigned' }}</dd></div>
                        <div><dt>Customer</dt><dd>{{ $selectedTicket->customer?->email ?? 'Not linked' }}</dd></div>
                        <div><dt>Worker</dt><dd>{{ $selectedTicket->workerProfile?->display_name ?? 'Not linked' }}</dd></div>
                        <div><dt>Document</dt><dd>{{ $selectedTicket->workerDocument?->document_type ?? 'Not linked' }}</dd></div>
                        <div><dt>Assignment</dt><dd>{{ $selectedTicket->dispatch_assignment_id ? '#'.$selectedTicket->dispatch_assignment_id : 'Not linked' }}</dd></div>
                        <div><dt>Attachments</dt><dd>{{ $selectedTicket->getMedia('support_ticket_attachments')->count() }}</dd></div>
                    </dl>
                    <div class="sc-context-links">
                        @if($selectedTicket->order)<a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$selectedTicket->order]) }}">Order {{ $selectedTicket->order->order_number }} <span>Open</span></a>@endif
                        @if($selectedTicket->workerProfile)<a href="{{ \App\Filament\Resources\WorkerProfiles\WorkerProfileResource::getUrl('view',['record'=>$selectedTicket->workerProfile]) }}">Worker profile <span>Open</span></a>@endif
                        @if($selectedTicket->workerDocument)<a href="{{ \App\Filament\Resources\WorkerDocuments\WorkerDocumentResource::getUrl('edit',['record'=>$selectedTicket->workerDocument]) }}">Worker document <span>Review</span></a>@endif
                    </div>
                @else <p class="sc-panel-empty">Select a ticket to inspect linked records.</p>@endif
            </section>
            <section class="sc-panel">
                <header><div><span>Attention</span><h2>Urgent & escalated</h2></div><strong>{{ $attentionTickets->count() }}</strong></header>
                <div class="sc-attention">
                    @forelse($attentionTickets as $ticket)<button type="button" wire:click="selectTicket({{ $ticket->id }})"><i></i><div><strong>{{ $ticket->ticket_number }}</strong><span>{{ $ticket->subject }}</span></div><time>{{ $ticket->updated_at?->diffForHumans() }}</time></button>@empty<p>No urgent or escalated tickets.</p>@endforelse
                </div>
            </section>
            <section class="sc-panel">
                <header><div><span>Capacity</span><h2>Support team</h2></div><strong>{{ $supportAgents->count() }}</strong></header>
                <div class="sc-agents">@forelse($supportAgents as $agent)<div><b>{{ str($agent->name)->substr(0,1)->upper() }}</b><span><strong>{{ $agent->name }}</strong><small>{{ $agent->assigned_support_tickets_count }} active assigned</small></span></div>@empty<p>No support-capable users.</p>@endforelse</div>
            </section>
            <section class="sc-panel sc-readiness">
                <header><div><span>Readiness</span><h2>Support delivery</h2></div></header>
                <dl><div><dt>Database notifications</dt><dd class="is-good">Configured</dd></div><div><dt>Sound polling</dt><dd class="is-good">15 seconds</dd></div><div><dt>Email/SMS/realtime</dt><dd class="is-muted">Deferred</dd></div></dl>
            </section>
        </aside>
    </div>
</div>
<audio id="bkb-support-alert" preload="auto" src="{{ asset('audio/support/support-alert.mp3') }}"></audio>
<script>
(()=>{const key='bkb-support-activity',audio=document.getElementById('bkb-support-alert');let ready=false;async function poll(){try{const r=await fetch('{{ route('admin.support.activity') }}',{headers:{Accept:'application/json'},credentials:'same-origin'});if(!r.ok)return;const now=await r.json(),prev=JSON.parse(localStorage.getItem(key)||'null');if(ready&&prev&&(now.latest_ticket_id>prev.latest_ticket_id||now.latest_message_id>prev.latest_message_id||now.unread_notifications>prev.unread_notifications)){await audio?.play().catch(()=>{});localStorage.setItem(key,JSON.stringify(now));setTimeout(()=>location.reload(),700);return}localStorage.setItem(key,JSON.stringify(now));ready=true}catch(_){}}poll();setInterval(poll,15000)})();
</script>
<style>
.fi-page-header{display:none}.sc{--line:rgba(121,158,194,.17);--line2:rgba(39,225,157,.35);--panel:#071525;--panel2:#0a1b2d;--muted:#7891ae;--text:#ecf5ff;display:grid;gap:.72rem;color:var(--text);font-size:.8rem}.sc-command,.sc-panel,.sc-kpi{border:1px solid var(--line);border-radius:7px;background:linear-gradient(145deg,rgba(10,27,45,.97),rgba(4,16,29,.98));box-shadow:0 18px 46px rgba(0,0,0,.18)}.sc-command{display:grid;grid-template-columns:auto minmax(17rem,1fr) auto;align-items:center;gap:1rem;padding:.75rem .9rem}.sc-brand span,.sc-panel>header span{color:#34e79a;font-size:.61rem;font-weight:950;text-transform:uppercase}.sc-brand span i,.sc-live i{display:inline-block;width:.42rem;height:.42rem;margin-right:.3rem;border-radius:50%;background:#28e296;box-shadow:0 0 12px #28e296;animation:sc-pulse 2s ease-in-out infinite}.sc-brand h1{font-size:1.35rem;font-weight:950;letter-spacing:0}.sc-brand p{color:#8299b4;font-size:.66rem}.sc-search{position:relative;display:flex;align-items:center;max-width:42rem;width:100%;justify-self:center}.sc-search span{position:absolute;overflow:hidden;width:1px;height:1px}.sc-search input{width:100%;height:2.5rem;border:1px solid var(--line);border-radius:6px;padding:0 3.2rem 0 .8rem;background:rgba(2,12,24,.74);color:#eaf5ff;outline:none;transition:border-color .16s ease,box-shadow .16s ease}.sc-search input:focus{border-color:#21bd88;box-shadow:0 0 0 3px rgba(33,189,136,.12)}.sc-search kbd{position:absolute;right:.6rem;color:#7891ae;font-size:.65rem}.sc-command-actions{display:flex;align-items:center;gap:.4rem}.sc-live{display:flex;align-items:center;gap:.2rem;border:1px solid var(--line);border-radius:5px;padding:.42rem .55rem;color:#91a7bf;font-size:.62rem}.sc-live strong{color:#d9fbea}.bkb-cc-action{display:inline-flex;align-items:center;justify-content:center;min-height:2.35rem;border:1px solid rgba(51,218,167,.3);border-radius:5px;padding:.45rem .65rem;background:#0b2637;color:#dcf8ed;font-size:.68rem;font-weight:850;transition:transform .16s ease,border-color .16s ease,box-shadow .16s ease}.bkb-cc-action:hover,.bkb-cc-action:focus-visible{border-color:#2ce49c;box-shadow:0 8px 20px rgba(0,0,0,.25);outline:none;transform:translateY(-1px)}.bkb-cc-action.is-primary{background:linear-gradient(135deg,#0a9168,#087251);color:#fff}.sc-kpis{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:.55rem}.sc-kpi{position:relative;overflow:hidden;min-height:5.5rem;padding:.7rem .75rem;transition:transform .17s ease,border-color .17s ease}.sc-kpi:hover{border-color:rgba(55,217,255,.4);transform:translateY(-2px)}.sc-kpi span{color:#7c95b2;font-size:.6rem;font-weight:950;text-transform:uppercase}.sc-kpi strong{display:block;margin-top:.45rem;font-size:1.45rem;line-height:1}.sc-kpi i{position:absolute;right:.65rem;bottom:.65rem;width:2.5rem;height:.8rem;border-bottom:2px solid #38d9ff;opacity:.42;transform:skewX(-32deg)}.sc-kpi.is-danger strong{color:#ff6c78}.sc-kpi.is-danger i{border-color:#ff5d6c}.sc-kpi.is-warning strong{color:#f5bd54}.sc-kpi.is-warning i{border-color:#f5bd54}.sc-kpi.is-success strong{color:#42e6a0}.sc-kpi.is-success i{border-color:#42e6a0}.sc-kpi.is-cyan strong{color:#62dbff}.sc-grid{display:grid;grid-template-columns:minmax(25rem,1.15fr) minmax(30rem,1.35fr) minmax(18rem,.72fr);gap:.6rem;align-items:start}.sc-panel{overflow:hidden}.sc-panel>header{display:flex;align-items:start;justify-content:space-between;gap:.7rem;border-bottom:1px solid var(--line);padding:.68rem .75rem;background:rgba(11,32,51,.63)}.sc-panel>header h2{margin-top:.1rem;font-size:.86rem;font-weight:900}.sc-panel>header>strong{color:#55e6ac}.sc-tabs,.sc-filters{display:flex;flex-wrap:wrap;gap:.25rem;padding:.42rem .5rem;border-bottom:1px solid var(--line)}.sc-tabs button,.sc-filters button,.sc-composer-tabs button{border:1px solid var(--line);border-radius:4px;padding:.28rem .45rem;color:#91a7bf;font-size:.61rem;transition:background .15s ease,color .15s ease,border-color .15s ease}.sc-tabs button:hover,.sc-tabs button.is-active,.sc-filters button:hover,.sc-filters button.is-active,.sc-composer-tabs button.is-active{border-color:rgba(35,224,148,.5);background:rgba(35,224,148,.11);color:#dffcf0}.sc-tabs button:focus-visible,.sc-filters button:focus-visible,.sc-composer-tabs button:focus-visible,.sc-row:focus-visible,.sc-attention button:focus-visible{outline:2px solid #38d9ff;outline-offset:-2px}.sc-filters{background:rgba(3,14,27,.45)}.sc-queue-head{display:grid;grid-template-columns:1fr 6.5rem 5.5rem;gap:.5rem;border-bottom:1px solid var(--line);padding:.38rem .65rem;color:#657f9e;font-size:.55rem;font-weight:900;text-transform:uppercase}.sc-queue-list{height:58vh;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(39,225,157,.28) transparent}.sc-row{position:relative;display:grid;width:100%;grid-template-columns:1fr 6.5rem 5.5rem;gap:.5rem;border-bottom:1px solid var(--line);padding:.62rem .65rem;color:inherit;text-align:left;transition:background .15s ease,transform .15s ease}.sc-row:before{position:absolute;top:.38rem;bottom:.38rem;left:0;width:2px;background:transparent;content:''}.sc-row:hover,.sc-row.is-selected{background:linear-gradient(90deg,rgba(31,225,147,.11),rgba(43,185,229,.03));transform:translateX(1px)}.sc-row.is-selected:before{background:#28e296;box-shadow:0 0 12px #28e296}.sc-row-main strong{color:#dcecff;font-size:.7rem}.sc-row-main h3{margin-top:.18rem;font-size:.69rem;font-weight:850}.sc-row-main p,.sc-row-owner span,.sc-row-time time{margin-top:.18rem;color:#718aa8;font-size:.58rem}.sc-row-owner,.sc-row-time{display:grid;align-content:center;justify-items:start;gap:.2rem}.bkb-cc-badge{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:.13rem .34rem;color:#b9cbe0;font-size:.52rem;font-weight:950;text-transform:uppercase}.bkb-cc-badge.is-urgent,.bkb-cc-badge.is-escalated{border-color:rgba(255,93,108,.52);background:rgba(255,93,108,.08);color:#ff8994}.sc-conversation{display:grid;grid-template-rows:auto auto auto minmax(14rem,1fr) auto;min-height:67vh}.sc-ticket-head>div:last-child{display:flex;gap:.3rem}.sc-ticket-head p{margin-top:.18rem;color:#7891ae;font-size:.6rem}.sc-actionbar{display:flex;gap:.35rem;border-bottom:1px solid var(--line);padding:.48rem .65rem}.sc-summary{border-bottom:1px solid var(--line);padding:.6rem .7rem;background:rgba(218,174,40,.06)}.sc-summary span{color:#e9b946;font-size:.55rem;font-weight:950;text-transform:uppercase}.sc-summary p{margin-top:.2rem;color:#c5d3e3}.sc-thread{max-height:48vh;overflow-y:auto;padding:.65rem;scrollbar-width:thin;scrollbar-color:rgba(39,225,157,.28) transparent}.sc-message{display:grid;grid-template-columns:1.8rem 1fr;gap:.5rem;padding:.45rem 0}.sc-avatar{display:grid;width:1.65rem;height:1.65rem;place-items:center;border:1px solid rgba(56,217,255,.25);border-radius:50%;background:#0b2a3d;color:#bdefff;font-size:.62rem;font-weight:950}.sc-message>div:last-child{border:1px solid var(--line);border-radius:6px;padding:.5rem .58rem;background:rgba(10,27,45,.72)}.sc-message.is-internal>div:last-child{border-color:rgba(166,105,255,.28);background:rgba(90,47,145,.12)}.sc-message.is-customer_visible>div:last-child{border-color:rgba(35,224,148,.25)}.sc-message.is-worker_visible>div:last-child{border-color:rgba(56,217,255,.25)}.sc-message header{display:flex;align-items:center;gap:.4rem}.sc-message header strong{font-size:.64rem}.sc-message header span{border-radius:3px;padding:.1rem .25rem;background:rgba(110,132,161,.12);color:#8299b4;font-size:.48rem;font-weight:900}.sc-message header time{margin-left:auto;color:#6e87a5;font-size:.54rem}.sc-message p{margin-top:.25rem;color:#becde0;font-size:.65rem;line-height:1.5}.sc-composer{border-top:1px solid var(--line);padding:.55rem .65rem;background:rgba(3,14,27,.6)}.sc-composer-tabs{display:flex;gap:.25rem;margin-bottom:.38rem}.sc-composer-tabs button:disabled{cursor:not-allowed;opacity:.35}.sc-composer textarea{width:100%;resize:vertical;border:1px solid var(--line);border-radius:5px;padding:.5rem .55rem;background:rgba(2,12,24,.8);color:#e8f4ff;font-size:.66rem;outline:none}.sc-composer textarea:focus{border-color:#28e296;box-shadow:0 0 0 3px rgba(40,226,150,.1)}.sc-composer footer{display:flex;align-items:center;justify-content:space-between;margin-top:.38rem}.sc-composer footer span{color:#718aa8;font-size:.56rem}.sc-composer footer button{border-radius:5px;padding:.38rem .75rem;background:#07865f;color:#fff;font-size:.63rem;font-weight:900}.sc-error{color:#ff7f89;font-size:.58rem}.sc-context{display:grid;gap:.6rem}.sc-details{display:grid;gap:.4rem;padding:.65rem}.sc-details div,.sc-readiness dl div{display:flex;justify-content:space-between;gap:.5rem;border-bottom:1px solid rgba(121,158,194,.09);padding-bottom:.35rem}.sc-details dt,.sc-readiness dt{color:#718aa8;font-size:.59rem}.sc-details dd,.sc-readiness dd{max-width:62%;font-size:.61rem;text-align:right}.sc-context-links{display:grid;gap:.3rem;border-top:1px solid var(--line);padding:.55rem}.sc-context-links a{display:flex;justify-content:space-between;border:1px solid var(--line);border-radius:5px;padding:.4rem .45rem;color:#bad0e4;font-size:.61rem}.sc-context-links a:hover{border-color:rgba(39,225,157,.45);color:#e8fff5}.sc-context-links span{color:#40dfa2}.sc-attention,.sc-agents{padding:.45rem .55rem}.sc-attention button{display:grid;width:100%;grid-template-columns:.4rem 1fr auto;align-items:center;gap:.4rem;border-bottom:1px solid var(--line);padding:.42rem 0;color:inherit;text-align:left}.sc-attention button i{width:.28rem;height:1.8rem;border-radius:4px;background:#ff6571}.sc-attention button div{display:grid;gap:.1rem}.sc-attention button strong{font-size:.6rem}.sc-attention button span,.sc-attention time,.sc-attention p{color:#718aa8;font-size:.54rem}.sc-agents>div{display:flex;align-items:center;gap:.45rem;border-bottom:1px solid var(--line);padding:.45rem 0}.sc-agents b{display:grid;width:1.7rem;height:1.7rem;place-items:center;border-radius:50%;background:linear-gradient(135deg,#11936b,#07513e);font-size:.63rem}.sc-agents span{display:grid}.sc-agents strong{font-size:.62rem}.sc-agents small{color:#718aa8;font-size:.54rem}.sc-readiness dl{display:grid;gap:.42rem;padding:.65rem}.sc-readiness dd.is-good{color:#42e6a0}.sc-readiness dd.is-muted{color:#e0b04c}.sc-panel-empty,.sc-attention>p,.sc-agents>p{padding:.65rem;color:#7891ae;font-size:.61rem}.bkb-cc-empty{padding:2rem 1rem;text-align:center}.bkb-cc-empty p{color:#91a5c0}@keyframes sc-pulse{0%,100%{opacity:.65;transform:scale(.88)}50%{opacity:1;transform:scale(1.12)}}@media(prefers-reduced-motion:reduce){.sc *{animation:none!important;transition:none!important}}@media(max-width:1450px){.sc-grid{grid-template-columns:minmax(22rem,1fr) minmax(28rem,1.35fr)}.sc-context{grid-column:1/-1;grid-template-columns:repeat(4,1fr)}}@media(max-width:1100px){.sc-command{grid-template-columns:1fr auto}.sc-search{grid-column:1/-1;grid-row:2}.sc-kpis{grid-template-columns:repeat(4,1fr)}}@media(max-width:900px){.sc-grid,.sc-context{grid-template-columns:1fr}.sc-queue-list,.sc-thread{height:auto;max-height:none}.sc-conversation{min-height:auto}.sc-command{grid-template-columns:1fr}.sc-command-actions{flex-wrap:wrap}.sc-kpis{grid-template-columns:repeat(2,1fr)}.sc-row,.sc-queue-head{grid-template-columns:1fr 5rem}.sc-row-time,.sc-queue-head span:last-child{display:none}}@media(max-width:560px){.sc-kpis{grid-template-columns:1fr}.sc-command-actions{align-items:stretch;flex-direction:column}.sc-live{justify-content:center}}
</style>
</x-filament-panels::page>
