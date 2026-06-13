@php
    $ticket = $getRecord()->load([
        'order', 'customer', 'workerProfile', 'workerDocument', 'dispatchAssignment',
        'creator', 'assignee', 'messages.author', 'events.actor',
        'assignments.assignee', 'assignments.assignedBy',
    ]);
    $activity = collect($ticket->messages)->map(fn ($item) => ['kind' => 'message', 'at' => $item->created_at, 'item' => $item])
        ->merge(collect($ticket->events)->map(fn ($item) => ['kind' => 'event', 'at' => $item->created_at, 'item' => $item]))
        ->sortByDesc('at');
    $ticketMedia = $ticket->getMedia('support_ticket_attachments');
    $messageMediaCount = $ticket->messages->sum(fn ($message) => $message->getMedia('support_message_attachments')->count());
@endphp

<x-admin-os.module-shell class="bkb-ticket-view" label="Support ticket operational view">
    <header class="bkb-ticket-hero">
        <div>
            <span class="bkb-ticket-eyebrow">Support operation · {{ $ticket->ticket_number }}</span>
            <h1>{{ $ticket->subject }}</h1>
            <p>{{ str($ticket->category)->replace('_', ' ')->title() }} · {{ str($ticket->source)->replace('_', ' ')->title() }} channel · Created {{ $ticket->created_at?->diffForHumans() }}</p>
        </div>
        <div class="bkb-ticket-hero-state">
            <span class="is-{{ $ticket->priority }}">{{ str($ticket->priority)->title() }}</span>
            <span class="is-{{ $ticket->status }}">{{ str($ticket->status)->replace('_', ' ')->title() }}</span>
            <a href="{{ \App\Filament\Pages\SupportCenter::getUrl() }}">Command center</a>
        </div>
    </header>

    <section class="bkb-ticket-kpis" aria-label="Ticket metrics">
        <div><span>Status</span><strong class="is-{{ $ticket->status }}">{{ str($ticket->status)->replace('_', ' ')->title() }}</strong></div>
        <div><span>Priority</span><strong class="is-{{ $ticket->priority }}">{{ str($ticket->priority)->title() }}</strong></div>
        <div><span>Assigned</span><strong>{{ $ticket->assignee?->name ?? 'Unassigned' }}</strong></div>
        <div><span>Messages</span><strong>{{ $ticket->messages->count() }}</strong></div>
        <div><span>Events</span><strong>{{ $ticket->events->count() }}</strong></div>
        <div><span>Attachments</span><strong>{{ $ticketMedia->count() + $messageMediaCount }}</strong></div>
        <div><span>Last activity</span><strong>{{ $ticket->last_message_at?->diffForHumans() ?? $ticket->updated_at?->diffForHumans() }}</strong></div>
    </section>

    <div class="bkb-ticket-layout">
        <main class="bkb-ticket-panel bkb-ticket-timeline">
            <header>
                <div><span class="bkb-ticket-eyebrow">Conversation and audit</span><h2>Messages and operational events</h2></div>
                <strong>{{ $activity->count() }} items</strong>
            </header>
            <div class="bkb-ticket-thread">
                @forelse($activity as $row)
                    @php($item = $row['item'])
                    <article class="bkb-ticket-activity is-{{ $row['kind'] }} {{ $row['kind'] === 'message' ? 'is-'.$item->visibility : '' }}">
                        <i></i>
                        <div>
                            <header>
                                <div>
                                    <strong>{{ $row['kind'] === 'message' ? ($item->author?->name ?? 'System') : str($item->event_type)->replace('_',' ')->title() }}</strong>
                                    <span>{{ $row['kind'] === 'message' ? str($item->visibility)->replace('_',' ')->upper() : 'SYSTEM EVENT' }}</span>
                                </div>
                                <time>{{ $row['at']?->format('Y-m-d H:i') }}</time>
                            </header>
                            <p>{{ $row['kind'] === 'message' ? $item->body : ($item->description ?: 'Operational event recorded.') }}</p>
                            @if($row['kind'] === 'message' && $item->getMedia('support_message_attachments')->isNotEmpty())
                                <div class="bkb-ticket-attachment-links">
                                    @foreach($item->getMedia('support_message_attachments') as $media)
                                        <a href="{{ route('admin.support.attachments.download', $media) }}">{{ $media->file_name }} <span>{{ $media->human_readable_size }}</span></a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-admin-os.empty-state title="No activity recorded" body="Use the permitted ticket actions above to add an internal note, assign the ticket, or record a reply." />
                @endforelse
            </div>
        </main>

        <aside class="bkb-ticket-rail">
            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Operator brief</span>
                <h2>{{ $ticket->summary ? 'Issue summary' : 'Summary required' }}</h2>
                <p>{{ $ticket->summary ?: 'No summary provided. Review the linked context and activity before taking action.' }}</p>
                <dl>
                    <div><dt>Number</dt><dd>{{ $ticket->ticket_number }}</dd></div>
                    <div><dt>Visibility</dt><dd>{{ str($ticket->visibility)->replace('_', ' ')->title() }}</dd></div>
                    <div><dt>Created by</dt><dd>{{ $ticket->creator?->name ?? 'System' }}</dd></div>
                    <div><dt>Created</dt><dd>{{ $ticket->created_at?->format('Y-m-d H:i') }}</dd></div>
                </dl>
            </section>

            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Linked operational context</span>
                <div class="bkb-ticket-links">
                    @if($ticket->order)
                        <a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view',['record'=>$ticket->order]) }}"><span>Order {{ $ticket->order->order_number }}</span><b>Open</b></a>
                        <p>Order status: {{ str($ticket->order->status)->replace('_',' ')->title() }}</p>
                        @if(filled($ticket->order->payment_status ?? null))<p>Payment: {{ str($ticket->order->payment_status)->replace('_',' ')->title() }}</p>@endif
                    @endif
                    @if($ticket->customer)<p>Customer: {{ $ticket->customer->name }} · {{ $ticket->customer->email }}</p>@endif
                    @if($ticket->workerProfile)<a href="{{ \App\Filament\Resources\WorkerProfiles\WorkerProfileResource::getUrl('view',['record'=>$ticket->workerProfile]) }}"><span>Worker {{ $ticket->workerProfile->display_name ?? '#'.$ticket->worker_profile_id }}</span><b>Open</b></a>@endif
                    @if($ticket->workerDocument)<a href="{{ \App\Filament\Resources\WorkerDocuments\WorkerDocumentResource::getUrl('edit',['record'=>$ticket->workerDocument]) }}"><span>Worker document #{{ $ticket->worker_document_id }}</span><b>Review</b></a>@endif
                    @if($ticket->dispatchAssignment)<p>Dispatch assignment: #{{ $ticket->dispatch_assignment_id }} · {{ str($ticket->dispatchAssignment->status)->replace('_',' ')->title() }}</p>@endif
                    @if(!$ticket->order && !$ticket->workerDocument && !$ticket->workerProfile)<p>No linked operational record.</p>@endif
                </div>
            </section>

            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Protected attachments</span>
                <div class="bkb-ticket-attachment-links">
                    @forelse($ticketMedia as $media)
                        <a href="{{ route('admin.support.attachments.download', $media) }}">{{ $media->file_name }} <span>{{ $media->human_readable_size }}</span></a>
                    @empty
                        <p class="bkb-ticket-empty">No ticket attachments.</p>
                    @endforelse
                </div>
            </section>

            <section class="bkb-ticket-panel">
                <span class="bkb-ticket-eyebrow">Assignment history</span>
                @forelse($ticket->assignments as $assignment)
                    <div class="bkb-ticket-assignment">
                        <strong>{{ $assignment->assignee?->name ?? 'Unknown user' }}</strong>
                        <span>{{ str($assignment->status)->title() }} · {{ $assignment->assigned_at?->format('Y-m-d H:i') }}</span>
                        @if($assignment->released_at)<small>Released {{ $assignment->released_at->format('Y-m-d H:i') }}{{ $assignment->release_reason ? ' · '.$assignment->release_reason : '' }}</small>@endif
                    </div>
                @empty
                    <p class="bkb-ticket-empty">Unassigned. Use the Assign action above when an owner is known.</p>
                @endforelse
            </section>
        </aside>
    </div>
</x-admin-os.module-shell>

<style>
.bkb-ticket-view{--tv-line:rgba(117,154,191,.18);--tv-panel:rgba(6,19,34,.92);display:grid;gap:.65rem;color:#eaf4ff}.bkb-ticket-hero,.bkb-ticket-kpis>div,.bkb-ticket-panel{border:1px solid var(--tv-line);border-radius:7px;background:linear-gradient(145deg,rgba(10,28,47,.96),rgba(4,15,28,.98));box-shadow:0 16px 40px rgba(0,0,0,.17)}.bkb-ticket-hero{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem .9rem}.bkb-ticket-eyebrow{display:block;color:#39e7a0;font-size:.58rem;font-weight:950;text-transform:uppercase}.bkb-ticket-hero h1{margin-top:.18rem;font-size:1.15rem;font-weight:950}.bkb-ticket-hero p{margin-top:.16rem;color:#8098b5;font-size:.62rem}.bkb-ticket-hero-state{display:flex;align-items:center;gap:.35rem}.bkb-ticket-hero-state span{border:1px solid var(--tv-line);border-radius:999px;padding:.18rem .4rem;color:#c4d5e7;font-size:.53rem;font-weight:950;text-transform:uppercase}.bkb-ticket-hero-state span.is-urgent,.bkb-ticket-hero-state span.is-escalated{border-color:rgba(255,100,113,.48);color:#ff8490}.bkb-ticket-hero-state a{border:1px solid rgba(57,231,160,.35);border-radius:4px;padding:.4rem .55rem;color:#dffbef;font-size:.6rem;font-weight:900}.bkb-ticket-hero-state a:hover,.bkb-ticket-hero-state a:focus-visible{border-color:#39e7a0;outline:none}.bkb-ticket-kpis{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:.45rem}.bkb-ticket-kpis>div{min-width:0;padding:.58rem .65rem}.bkb-ticket-kpis span{display:block;color:#748da9;font-size:.53rem;font-weight:900;text-transform:uppercase}.bkb-ticket-kpis strong{display:block;overflow:hidden;margin-top:.22rem;color:#edf7ff;font-size:.67rem;text-overflow:ellipsis;white-space:nowrap}.bkb-ticket-kpis strong.is-open,.bkb-ticket-kpis strong.is-resolved{color:#48e4a5}.bkb-ticket-kpis strong.is-urgent,.bkb-ticket-kpis strong.is-escalated{color:#ff7986}.bkb-ticket-layout{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(18rem,.62fr);gap:.65rem;align-items:start}.bkb-ticket-panel{overflow:hidden}.bkb-ticket-panel>header{display:flex;align-items:start;justify-content:space-between;gap:.8rem;border-bottom:1px solid var(--tv-line);padding:.7rem .75rem;background:rgba(11,32,52,.62)}.bkb-ticket-panel h2{margin-top:.15rem;font-size:.82rem;font-weight:900}.bkb-ticket-panel>header>strong{color:#4be5aa;font-size:.63rem}.bkb-ticket-thread{max-height:68vh;overflow:auto;padding:.3rem .72rem;scrollbar-width:thin;scrollbar-color:rgba(57,231,160,.28) transparent}.bkb-ticket-activity{display:grid;grid-template-columns:.42rem 1fr;gap:.55rem;border-bottom:1px solid rgba(117,154,191,.11);padding:.65rem 0}.bkb-ticket-activity>i{width:.34rem;height:2rem;border-radius:5px;background:#48dba3;box-shadow:0 0 12px rgba(72,219,163,.25)}.bkb-ticket-activity.is-event>i{background:#54c7f2}.bkb-ticket-activity.is-internal>i{background:#ad7cff}.bkb-ticket-activity>div{border:1px solid var(--tv-line);border-radius:6px;padding:.55rem .62rem;background:rgba(8,24,40,.74)}.bkb-ticket-activity.is-internal>div{border-color:rgba(173,124,255,.28);background:rgba(92,51,145,.1)}.bkb-ticket-activity header{display:flex;align-items:start;justify-content:space-between;gap:.6rem}.bkb-ticket-activity header div{display:flex;align-items:center;gap:.35rem}.bkb-ticket-activity header strong{font-size:.63rem}.bkb-ticket-activity header span{border-radius:3px;padding:.1rem .24rem;background:rgba(123,153,187,.12);color:#8ca5c1;font-size:.46rem;font-weight:900}.bkb-ticket-activity time{color:#718aa7;font-size:.53rem}.bkb-ticket-activity p{margin-top:.3rem;color:#c4d2e2;font-size:.64rem;line-height:1.48}.bkb-ticket-rail{position:sticky;top:4.5rem;display:grid;gap:.55rem}.bkb-ticket-rail .bkb-ticket-panel{padding:.7rem}.bkb-ticket-rail .bkb-ticket-panel p{margin-top:.32rem;color:#99adc3;font-size:.61rem;line-height:1.45}.bkb-ticket-rail dl{display:grid;grid-template-columns:1fr 1fr;gap:.55rem;margin-top:.65rem}.bkb-ticket-rail dt{color:#718aa7;font-size:.52rem;font-weight:900;text-transform:uppercase}.bkb-ticket-rail dd{margin-top:.12rem;color:#dce8f4;font-size:.58rem}.bkb-ticket-links,.bkb-ticket-attachment-links{display:grid;gap:.35rem;margin-top:.55rem}.bkb-ticket-links a,.bkb-ticket-attachment-links a{display:flex;align-items:center;justify-content:space-between;gap:.5rem;border:1px solid var(--tv-line);border-radius:5px;padding:.4rem .45rem;color:#c9daea;font-size:.58rem}.bkb-ticket-links a:hover,.bkb-ticket-links a:focus-visible,.bkb-ticket-attachment-links a:hover,.bkb-ticket-attachment-links a:focus-visible{border-color:rgba(57,231,160,.5);color:#effff8;outline:none}.bkb-ticket-links a b,.bkb-ticket-attachment-links a span{color:#46dda5;font-size:.52rem}.bkb-ticket-assignment{display:grid;gap:.12rem;border-top:1px solid var(--tv-line);margin-top:.48rem;padding-top:.48rem}.bkb-ticket-assignment strong{font-size:.61rem}.bkb-ticket-assignment span,.bkb-ticket-assignment small,.bkb-ticket-empty{color:#7d95b0;font-size:.55rem}.bkb-cc-empty{padding:2.5rem 1rem;text-align:center}.bkb-cc-empty p{margin-top:.25rem;color:#91a5c0}@media(prefers-reduced-motion:reduce){.bkb-ticket-view *{transition:none!important}}@media(max-width:1200px){.bkb-ticket-kpis{grid-template-columns:repeat(4,1fr)}.bkb-ticket-layout{grid-template-columns:1fr}.bkb-ticket-rail{position:static;grid-template-columns:1fr 1fr}}@media(max-width:720px){.bkb-ticket-hero{align-items:start;flex-direction:column}.bkb-ticket-hero-state{flex-wrap:wrap}.bkb-ticket-kpis,.bkb-ticket-rail{grid-template-columns:1fr 1fr}.bkb-ticket-activity header,.bkb-ticket-activity header div{align-items:start;flex-direction:column}.bkb-ticket-activity time{margin-top:.2rem}}@media(max-width:480px){.bkb-ticket-kpis,.bkb-ticket-rail,.bkb-ticket-rail dl{grid-template-columns:1fr}}
</style>
