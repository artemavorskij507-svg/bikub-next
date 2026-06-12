@php
    $ticket = $getRecord()->load(['order','customer','workerProfile','workerDocument','dispatchAssignment','creator','assignee','messages.author','events.actor','assignments.assignee','assignments.assignedBy']);
    $activity = collect($ticket->messages)->map(fn ($item) => ['kind' => 'message', 'at' => $item->created_at, 'item' => $item])
        ->merge(collect($ticket->events)->map(fn ($item) => ['kind' => 'event', 'at' => $item->created_at, 'item' => $item]))
        ->sortByDesc('at');
@endphp

<div class="bkb-ticket-view">
    <section class="bkb-ticket-kpis">
        <div><span>Status</span><strong class="is-{{ $ticket->status }}">{{ str($ticket->status)->replace('_', ' ')->title() }}</strong></div>
        <div><span>Priority</span><strong class="is-{{ $ticket->priority }}">{{ str($ticket->priority)->title() }}</strong></div>
        <div><span>Assigned</span><strong>{{ $ticket->assignee?->name ?? 'Unassigned' }}</strong></div>
        <div><span>Last activity</span><strong>{{ $ticket->last_message_at?->diffForHumans() ?? $ticket->updated_at?->diffForHumans() }}</strong></div>
    </section>

    <div class="bkb-ticket-layout">
        <aside class="bkb-ticket-rail">
            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Ticket summary</span>
                <h2>{{ $ticket->subject }}</h2>
                <p>{{ $ticket->summary ?: 'No summary provided.' }}</p>
                <dl>
                    <div><dt>Number</dt><dd>{{ $ticket->ticket_number }}</dd></div>
                    <div><dt>Category</dt><dd>{{ str($ticket->category)->replace('_', ' ')->title() }}</dd></div>
                    <div><dt>Source</dt><dd>{{ str($ticket->source)->title() }}</dd></div>
                    <div><dt>Visibility</dt><dd>{{ str($ticket->visibility)->replace('_', ' ')->title() }}</dd></div>
                    <div><dt>Created by</dt><dd>{{ $ticket->creator?->name ?? 'System' }}</dd></div>
                    <div><dt>Created</dt><dd>{{ $ticket->created_at?->format('Y-m-d H:i') }}</dd></div>
                </dl>
            </section>

            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Linked context</span>
                <div class="bkb-ticket-links">
                    @if($ticket->order)<a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$ticket->order]) }}">Order {{ $ticket->order->order_number }} <span>Open order</span></a>@endif
                    @if($ticket->customer)<p>Customer: {{ $ticket->customer->name }} · {{ $ticket->customer->email }}</p>@endif
                    @if($ticket->workerProfile)<a href="{{ \App\Filament\Resources\WorkerProfiles\WorkerProfileResource::getUrl('view',['record'=>$ticket->workerProfile]) }}">Worker {{ $ticket->workerProfile->display_name ?? '#'.$ticket->worker_profile_id }} <span>Open profile</span></a>@endif
                    @if($ticket->workerDocument)<a href="{{ \App\Filament\Resources\WorkerDocuments\WorkerDocumentResource::getUrl('edit',['record'=>$ticket->workerDocument]) }}">Worker document #{{ $ticket->worker_document_id }} <span>Review document</span></a>@endif
                    @if(!$ticket->order && !$ticket->workerDocument)<p>No linked operational record.</p>@endif
                </div>
            </section>

            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Attachments</span>
                @forelse($ticket->getMedia('support_ticket_attachments') as $media)
                    <p>{{ $media->file_name }} · {{ $media->human_readable_size }}</p>
                @empty
                    <p class="bkb-ticket-empty">No ticket attachments.</p>
                @endforelse
            </section>

            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Assignment history</span>
                @forelse($ticket->assignments as $assignment)
                    <div class="bkb-ticket-assignment"><strong>{{ $assignment->assignee?->name }}</strong><span>{{ str($assignment->status)->title() }} · {{ $assignment->assigned_at?->format('Y-m-d H:i') }}</span></div>
                @empty
                    <p class="bkb-ticket-empty">Unassigned. Use Assign me or Assign above.</p>
                @endforelse
            </section>
        </aside>

        <main class="bkb-ticket-panel bkb-ticket-timeline">
            <header><div><span class="bkb-ticket-eyebrow">Activity</span><h2>Messages and events</h2></div><strong>{{ $activity->count() }} item(s)</strong></header>
            @forelse($activity as $row)
                @php($item = $row['item'])
                <article class="bkb-ticket-activity is-{{ $row['kind'] }}">
                    <i></i>
                    <div>
                        <header>
                            <strong>{{ $row['kind'] === 'message' ? str($item->visibility)->replace('_',' ')->upper() : str($item->event_type)->replace('_',' ')->title() }}</strong>
                            <time>{{ $row['at']?->format('Y-m-d H:i') }}</time>
                        </header>
                        <span>{{ $row['kind'] === 'message' ? ($item->author?->name ?? 'System') : ($item->actor?->name ?? 'System') }}</span>
                        <p>{{ $row['kind'] === 'message' ? $item->body : ($item->description ?: 'No event note.') }}</p>
                        @if($row['kind'] === 'message' && $item->getMedia('support_message_attachments')->isNotEmpty())
                            <small>{{ $item->getMedia('support_message_attachments')->pluck('file_name')->join(', ') }}</small>
                        @endif
                    </div>
                </article>
            @empty
                <div class="bkb-ticket-empty-state"><strong>No activity recorded</strong><p>Add an internal note or assign the ticket to begin the operational timeline.</p></div>
            @endforelse
        </main>
    </div>
</div>

<style>
.bkb-ticket-view{display:grid;gap:1rem}.bkb-ticket-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem}.bkb-ticket-kpis>div,.bkb-ticket-panel{border:1px solid rgba(148,163,184,.18);background:rgba(8,18,34,.72);border-radius:8px}.bkb-ticket-kpis>div{padding:.85rem 1rem}.bkb-ticket-kpis span,.bkb-ticket-eyebrow{display:block;color:#8da3c1;font-size:.72rem;font-weight:800;text-transform:uppercase}.bkb-ticket-kpis strong{display:block;margin-top:.3rem;color:#f4f7fb}.bkb-ticket-kpis strong.is-open{color:#4ade80}.bkb-ticket-kpis strong.is-urgent{color:#fb7185}.bkb-ticket-layout{display:grid;grid-template-columns:minmax(18rem,.72fr) minmax(0,1.5fr);gap:1rem;align-items:start}.bkb-ticket-rail{display:grid;gap:1rem}.bkb-ticket-panel{padding:1rem}.bkb-ticket-panel h2{margin:.35rem 0 .5rem;color:#f8fafc;font-size:1.05rem}.bkb-ticket-panel p{color:#a9bad1}.bkb-ticket-panel dl{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1rem}.bkb-ticket-panel dt{color:#7890af;font-size:.72rem;text-transform:uppercase;font-weight:800}.bkb-ticket-panel dd{margin:.2rem 0 0;color:#e5edf8}.bkb-ticket-links{display:grid;gap:.6rem;margin-top:.8rem}.bkb-ticket-links a{display:flex;justify-content:space-between;border:1px solid rgba(52,211,153,.25);padding:.7rem;color:#d9fbe9;border-radius:6px}.bkb-ticket-links a span{color:#6ee7b7;font-size:.78rem}.bkb-ticket-assignment{display:grid;gap:.2rem;margin-top:.75rem}.bkb-ticket-assignment span,.bkb-ticket-empty{color:#91a5c0}.bkb-ticket-timeline>header{display:flex;justify-content:space-between;align-items:start;border-bottom:1px solid rgba(148,163,184,.14);padding-bottom:.8rem}.bkb-ticket-timeline>header>strong{color:#6ee7b7}.bkb-ticket-activity{display:grid;grid-template-columns:10px 1fr;gap:.8rem;padding:1rem 0;border-bottom:1px solid rgba(148,163,184,.11)}.bkb-ticket-activity i{width:9px;height:9px;margin-top:.35rem;border-radius:50%;background:#34d399;box-shadow:0 0 0 4px rgba(52,211,153,.12)}.bkb-ticket-activity.is-event i{background:#60a5fa;box-shadow:0 0 0 4px rgba(96,165,250,.12)}.bkb-ticket-activity header{display:flex;justify-content:space-between;gap:1rem}.bkb-ticket-activity header strong{color:#f1f5f9;font-size:.82rem}.bkb-ticket-activity time,.bkb-ticket-activity span{color:#7890af;font-size:.78rem}.bkb-ticket-activity p{margin-top:.4rem;color:#c8d4e4}.bkb-ticket-empty-state{padding:3rem 1rem;text-align:center}.bkb-ticket-empty-state strong{color:#e7eef8}.bkb-ticket-empty-state p{margin-top:.35rem}@media(max-width:950px){.bkb-ticket-kpis{grid-template-columns:1fr 1fr}.bkb-ticket-layout{grid-template-columns:1fr}}@media(max-width:560px){.bkb-ticket-kpis,.bkb-ticket-panel dl{grid-template-columns:1fr}}
</style>
