<x-filament-panels::page>
<div class="bkb-cc">
    <header class="bkb-cc-head">
        <div><span>Support Operations Hub</span><h1>Support Center</h1><p>Real ticket queues, conversations, assignments and linked operational context.</p></div>
        <div class="bkb-cc-head-actions">
            <x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('create')" tone="primary">Create ticket</x-admin-os.action-button>
            <x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('index')">All tickets</x-admin-os.action-button>
        </div>
    </header>

    <section class="bkb-cc-kpis" aria-label="Support metrics">
        @foreach(['open'=>'Open tickets','urgent'=>'Urgent','escalated'=>'Escalated','waiting_customer'=>'Waiting customer','waiting_worker'=>'Waiting worker','resolved_today'=>'Resolved today','avg_first_response'=>'Average first response'] as $key=>$label)
            <x-admin-os.kpi-card :label="$label" :value="$metrics[$key]" :tone="in_array($key,['urgent','escalated']) ? 'risk' : ($key === 'resolved_today' ? 'success' : 'default')" />
        @endforeach
    </section>

    <div class="bkb-cc-workspace">
        <section class="bkb-cc-panel bkb-cc-queue">
            <header><div><span>Priority queue</span><h2>Active tickets</h2></div><strong>{{ $activeQueue->count() }}</strong></header>
            <nav aria-label="Ticket queue shortcuts">
                @foreach(['Open'=>'open','Mine'=>'assigned_to_me','Urgent'=>'urgent','Unassigned'=>'unassigned','Waiting customer'=>'pending_customer','Waiting worker'=>'pending_worker'] as $label=>$filter)
                    <a href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('index', ['tableFilters' => [$filter => ['isActive' => true]]]) }}">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="bkb-cc-queue-list">
                @forelse($activeQueue as $ticket)
                    <a class="bkb-cc-ticket {{ $selectedTicket?->is($ticket) ? 'is-selected' : '' }}" href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$ticket]) }}">
                        <div><strong>{{ $ticket->ticket_number }}</strong><x-admin-os.status-badge :value="$ticket->priority" /></div>
                        <h3>{{ $ticket->subject }}</h3>
                        <p>{{ str($ticket->category)->replace('_',' ')->title() }} @if($ticket->order) · {{ $ticket->order->order_number }} @endif</p>
                        <footer><x-admin-os.status-badge :value="$ticket->status" /><span>{{ $ticket->assignee?->name ?? 'Unassigned' }}</span><time>{{ ($ticket->last_message_at ?? $ticket->updated_at)?->diffForHumans() }}</time></footer>
                    </a>
                @empty
                    <x-admin-os.empty-state title="No active support tickets." body="New operational tickets will appear here." />
                @endforelse
            </div>
        </section>

        <main class="bkb-cc-panel bkb-cc-focus">
            @if($selectedTicket)
                <header><div><span>Selected priority ticket</span><h2>{{ $selectedTicket->subject }}</h2><p>{{ $selectedTicket->ticket_number }}</p></div><div><x-admin-os.status-badge :value="$selectedTicket->status" /><x-admin-os.status-badge :value="$selectedTicket->priority" /></div></header>
                <div class="bkb-cc-summary">{{ $selectedTicket->summary ?: 'No summary provided.' }}</div>
                <div class="bkb-cc-actions">
                    <x-admin-os.action-button :href="\App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view',['record'=>$selectedTicket])" tone="primary">Open full ticket</x-admin-os.action-button>
                    @if($selectedTicket->assigned_to_id !== auth()->id())<x-admin-os.action-button wire:click="assignToMe({{ $selectedTicket->id }})">Assign to me</x-admin-os.action-button>@endif
                    @if($selectedTicket->order)<x-admin-os.action-button :href="\App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$selectedTicket->order])">Open order</x-admin-os.action-button>@endif
                </div>
                <section class="bkb-cc-thread"><h3>Latest activity</h3>
                    @forelse($selectedTicket->messages->sortByDesc('created_at')->take(5) as $message)
                        <x-admin-os.timeline-item :label="str($message->visibility)->replace('_',' ')->upper()" :author="$message->author?->name ?? 'System'" :time="$message->created_at?->diffForHumans()" tone="message"><p>{{ $message->body }}</p></x-admin-os.timeline-item>
                    @empty
                        @foreach($selectedTicket->events->take(5) as $event)
                            <x-admin-os.timeline-item :label="str($event->event_type)->replace('_',' ')->title()" :author="$event->actor?->name ?? 'System'" :time="$event->created_at?->diffForHumans()"><p>{{ $event->description ?: 'Operational event recorded.' }}</p></x-admin-os.timeline-item>
                        @endforeach
                    @endforelse
                </section>
            @else
                <x-admin-os.empty-state title="No active support tickets." body="Resolved and closed tickets remain available in ticket operations." />
            @endif
        </main>

        <aside class="bkb-cc-side">
            <x-admin-os.context-panel title="Ticket context">
                @if($selectedTicket)
                    <dl><div><dt>Category</dt><dd>{{ str($selectedTicket->category)->replace('_',' ')->title() }}</dd></div><div><dt>Assigned</dt><dd>{{ $selectedTicket->assignee?->name ?? 'Unassigned' }}</dd></div><div><dt>Customer</dt><dd>{{ $selectedTicket->customer?->email ?? 'Not linked' }}</dd></div><div><dt>Worker</dt><dd>{{ $selectedTicket->workerProfile?->display_name ?? 'Not linked' }}</dd></div><div><dt>Document</dt><dd>{{ $selectedTicket->workerDocument?->document_type ?? 'Not linked' }}</dd></div><div><dt>Attachments</dt><dd>{{ $selectedTicket->getMedia('support_ticket_attachments')->count() }}</dd></div></dl>
                @else <p>No selected ticket context.</p> @endif
            </x-admin-os.context-panel>
            <x-admin-os.context-panel title="Support team">
                @forelse($supportAgents as $agent)<div class="bkb-cc-agent"><span>{{ str($agent->name)->substr(0,1)->upper() }}</span><div><strong>{{ $agent->name }}</strong><small>{{ $agent->assigned_support_tickets_count }} active assigned</small></div></div>@empty<p>No support-capable users.</p>@endforelse
            </x-admin-os.context-panel>
            <x-admin-os.context-panel title="Operations status"><dl><div><dt>Database notifications</dt><dd>Configured</dd></div><div><dt>Sound polling</dt><dd>Active every 15 seconds</dd></div><div><dt>External channels</dt><dd>Email/SMS/realtime deferred</dd></div></dl></x-admin-os.context-panel>
        </aside>
    </div>
</div>
<audio id="bkb-support-alert" preload="auto" src="{{ asset('audio/support/support-alert.mp3') }}"></audio>
<script>
(()=>{const key='bkb-support-activity',audio=document.getElementById('bkb-support-alert');let ready=false;async function poll(){try{const r=await fetch('{{ route('admin.support.activity') }}',{headers:{Accept:'application/json'},credentials:'same-origin'});if(!r.ok)return;const now=await r.json(),prev=JSON.parse(localStorage.getItem(key)||'null');if(ready&&prev&&(now.latest_ticket_id>prev.latest_ticket_id||now.latest_message_id>prev.latest_message_id||now.unread_notifications>prev.unread_notifications)){await audio?.play().catch(()=>{});localStorage.setItem(key,JSON.stringify(now));setTimeout(()=>location.reload(),700);return}localStorage.setItem(key,JSON.stringify(now));ready=true}catch(_){}}poll();setInterval(poll,15000)})();
</script>
<style>
.bkb-cc{display:grid;gap:1rem;color:#e8f0fa}.bkb-cc-head{display:flex;align-items:end;justify-content:space-between;gap:1rem;border-bottom:1px solid var(--bkb-border);padding-bottom:1rem}.bkb-cc-head span,.bkb-cc-panel>header span{color:var(--bkb-emerald);font-size:.7rem;font-weight:900;text-transform:uppercase}.bkb-cc-head h1{font-size:1.8rem;font-weight:900}.bkb-cc-head p{color:var(--bkb-muted)}.bkb-cc-head-actions,.bkb-cc-actions{display:flex;flex-wrap:wrap;gap:.5rem}.bkb-cc-action{display:inline-flex;align-items:center;min-height:2.35rem;border:1px solid var(--bkb-border-strong);border-radius:6px;padding:.45rem .75rem;background:rgba(12,30,50,.8);color:#d8fbea;font-size:.78rem;font-weight:800}.bkb-cc-action.is-primary{background:#0a805d;color:white}.bkb-cc-kpis{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:.6rem}.bkb-cc-kpi,.bkb-cc-panel{border:1px solid var(--bkb-border);border-radius:8px;background:rgba(7,18,32,.86)}.bkb-cc-kpi{padding:.75rem}.bkb-cc-kpi span{display:block;color:var(--bkb-soft);font-size:.66rem;font-weight:900;text-transform:uppercase}.bkb-cc-kpi strong{display:block;margin-top:.3rem;font-size:1.28rem}.bkb-cc-kpi.is-risk strong{color:#ff7b86}.bkb-cc-kpi.is-success strong{color:#62e5a7}.bkb-cc-workspace{display:grid;grid-template-columns:minmax(17rem,.8fr) minmax(25rem,1.4fr) minmax(16rem,.65fr);gap:.8rem;align-items:start}.bkb-cc-panel>header{display:flex;justify-content:space-between;gap:1rem;border-bottom:1px solid var(--bkb-border);padding:.8rem}.bkb-cc-panel h2{font-size:1rem;font-weight:900}.bkb-cc-panel-body{padding:.8rem}.bkb-cc-queue nav{display:flex;gap:.3rem;overflow:auto;padding:.55rem;border-bottom:1px solid var(--bkb-border)}.bkb-cc-queue nav a{white-space:nowrap;border:1px solid var(--bkb-border);border-radius:5px;padding:.28rem .5rem;color:var(--bkb-muted);font-size:.68rem}.bkb-cc-queue-list{max-height:64vh;overflow:auto}.bkb-cc-ticket{display:grid;gap:.35rem;border-bottom:1px solid var(--bkb-border);padding:.7rem;color:inherit}.bkb-cc-ticket:hover,.bkb-cc-ticket.is-selected{background:rgba(34,229,138,.07)}.bkb-cc-ticket>div,.bkb-cc-ticket footer{display:flex;align-items:center;justify-content:space-between;gap:.4rem}.bkb-cc-ticket h3{font-size:.82rem;font-weight:800}.bkb-cc-ticket p,.bkb-cc-ticket footer{color:var(--bkb-soft);font-size:.68rem}.bkb-cc-badge{display:inline-flex;border:1px solid var(--bkb-border);border-radius:999px;padding:.15rem .4rem;color:#b9cbe0;font-size:.61rem;font-weight:900;text-transform:uppercase}.bkb-cc-badge.is-urgent,.bkb-cc-badge.is-escalated{border-color:rgba(255,93,108,.45);color:#ff8994}.bkb-cc-focus>header{align-items:start}.bkb-cc-focus>header>div:last-child{display:flex;gap:.35rem}.bkb-cc-summary,.bkb-cc-actions,.bkb-cc-thread{padding:.85rem}.bkb-cc-summary{color:var(--bkb-muted)}.bkb-cc-thread h3{margin-bottom:.5rem;font-size:.78rem;font-weight:900;text-transform:uppercase}.bkb-cc-timeline-item{display:grid;grid-template-columns:8px 1fr;gap:.65rem;border-top:1px solid var(--bkb-border);padding:.7rem 0}.bkb-cc-timeline-item i{width:7px;height:7px;margin-top:.3rem;border-radius:50%;background:var(--bkb-cyan)}.bkb-cc-timeline-item.is-message i{background:var(--bkb-emerald)}.bkb-cc-timeline-item header{display:flex;justify-content:space-between;gap:.5rem}.bkb-cc-timeline-item strong{font-size:.72rem}.bkb-cc-timeline-item span,.bkb-cc-timeline-item time{color:var(--bkb-soft);font-size:.65rem}.bkb-cc-timeline-item p{margin-top:.25rem;color:#c3d1e2;font-size:.78rem}.bkb-cc-side{display:grid;gap:.8rem}.bkb-cc-panel dl{display:grid;gap:.55rem}.bkb-cc-panel dl div{display:flex;justify-content:space-between;gap:.5rem}.bkb-cc-panel dt{color:var(--bkb-soft);font-size:.68rem}.bkb-cc-panel dd{font-size:.72rem;text-align:right}.bkb-cc-agent{display:flex;gap:.55rem;padding:.45rem 0;border-bottom:1px solid var(--bkb-border)}.bkb-cc-agent>span{display:grid;width:1.8rem;height:1.8rem;place-items:center;border-radius:50%;background:#0a805d;font-weight:900}.bkb-cc-agent div{display:grid}.bkb-cc-agent small{color:var(--bkb-soft)}.bkb-cc-empty{padding:2rem 1rem;text-align:center}.bkb-cc-empty p{color:var(--bkb-muted)}@media(max-width:1250px){.bkb-cc-kpis{grid-template-columns:repeat(4,1fr)}.bkb-cc-workspace{grid-template-columns:minmax(17rem,.8fr) 1.4fr}.bkb-cc-side{grid-column:1/-1;grid-template-columns:repeat(3,1fr)}}@media(max-width:850px){.bkb-cc-head{align-items:start;flex-direction:column}.bkb-cc-kpis,.bkb-cc-workspace,.bkb-cc-side{grid-template-columns:1fr}.bkb-cc-side{grid-column:auto}}
</style>
</x-filament-panels::page>
