<x-filament-panels::page>
    <div class="bkb-support-kpis">
        @foreach($metrics as $label => $value)
            <div class="bkb-support-kpi">
                <span>{{ str($label)->replace('_', ' ')->title() }}</span>
                <strong>{{ $value }}</strong>
            </div>
        @endforeach
    </div>

    <div class="bkb-support-toolbar">
        <p>Latest ticket activity: <strong>{{ $latestTicketAt ? \Illuminate\Support\Carbon::parse($latestTicketAt)->diffForHumans() : 'No tickets yet' }}</strong></p>
        <a href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('index') }}">Open ticket operations</a>
    </div>

    <div class="bkb-support-queues">
        @foreach($queues as $title => $tickets)
            <section class="bkb-support-queue">
                <header><h2>{{ $title }}</h2><span>{{ $tickets->count() }}</span></header>
                <div>
                    @forelse($tickets as $ticket)
                        <article class="bkb-support-ticket">
                            <div class="bkb-support-ticket-head">
                                <div><strong>{{ $ticket->ticket_number }}</strong><p>{{ $ticket->subject }}</p></div>
                                <span class="is-{{ $ticket->priority }}">{{ str($ticket->priority)->title() }}</span>
                            </div>
                            <div class="bkb-support-ticket-meta">
                                <span>{{ str($ticket->status)->replace('_', ' ')->title() }}</span>
                                <span>{{ str($ticket->category)->replace('_', ' ')->title() }}</span>
                                @if($ticket->order)<span>{{ $ticket->order->order_number }}</span>@endif
                                <span>{{ $ticket->assignee?->name ?? 'Unassigned' }}</span>
                                <span>{{ $ticket->last_message_at?->diffForHumans() ?? $ticket->updated_at?->diffForHumans() }}</span>
                            </div>
                            <div class="bkb-support-ticket-actions">
                                <a href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view', ['record' => $ticket]) }}">View ticket</a>
                                @if($ticket->assigned_to_id !== auth()->id())<button type="button" wire:click="assignToMe({{ $ticket->id }})">Assign to me</button>@endif
                            </div>
                        </article>
                    @empty
                        <p class="bkb-support-empty">No {{ str($title)->lower() }} tickets.</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <audio id="bkb-support-alert" preload="auto" src="{{ asset('audio/support/support-alert.mp3') }}"></audio>
    <script>
        (() => {
            const storageKey = 'bkb-support-activity';
            const audio = document.getElementById('bkb-support-alert');
            let initialized = false;

            async function checkSupportActivity() {
                try {
                    const response = await fetch('{{ route('admin.support.activity') }}', {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) return;
                    const current = await response.json();
                    const previous = JSON.parse(localStorage.getItem(storageKey) || 'null');

                    if (initialized && previous && (
                        current.latest_ticket_id > previous.latest_ticket_id ||
                        current.latest_message_id > previous.latest_message_id
                    )) {
                        audio?.play().catch(() => {});
                    }

                    localStorage.setItem(storageKey, JSON.stringify(current));
                    initialized = true;
                } catch (_) {
                    // Polling failure stays silent; the dashboard remains usable.
                }
            }

            checkSupportActivity();
            window.setInterval(checkSupportActivity, 15000);
        })();
    </script>

    <style>
        .bkb-support-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem}
        .bkb-support-kpi,.bkb-support-queue,.bkb-support-toolbar{border:1px solid rgba(148,163,184,.18);background:rgba(8,18,34,.76);border-radius:8px}
        .bkb-support-kpi{padding:.9rem 1rem}.bkb-support-kpi span{display:block;color:#8da3c1;font-size:.72rem;font-weight:800;text-transform:uppercase}.bkb-support-kpi strong{display:block;margin-top:.25rem;color:#f8fafc;font-size:1.55rem}
        .bkb-support-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1rem}.bkb-support-toolbar p{color:#91a5c0}.bkb-support-toolbar a,.bkb-support-ticket-actions a,.bkb-support-ticket-actions button{border:1px solid rgba(52,211,153,.35);border-radius:6px;padding:.45rem .7rem;color:#b7f7d8;background:rgba(5,78,62,.28);font-weight:700}
        .bkb-support-queues{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.bkb-support-queue>header{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(148,163,184,.14);padding:.8rem 1rem}.bkb-support-queue h2{color:#f8fafc;font-size:1rem;font-weight:800}.bkb-support-queue header span{color:#6ee7b7;font-weight:800}
        .bkb-support-ticket{padding:.85rem 1rem;border-bottom:1px solid rgba(148,163,184,.1)}.bkb-support-ticket:last-child{border-bottom:0}.bkb-support-ticket-head{display:flex;justify-content:space-between;gap:1rem}.bkb-support-ticket-head strong{color:#f1f5f9}.bkb-support-ticket-head p{margin-top:.15rem;color:#b4c3d7}.bkb-support-ticket-head>span{font-size:.72rem;font-weight:800;text-transform:uppercase;color:#a7f3d0}.bkb-support-ticket-head>span.is-urgent{color:#fb7185}
        .bkb-support-ticket-meta{display:flex;flex-wrap:wrap;gap:.35rem .75rem;margin-top:.55rem;color:#7890af;font-size:.75rem}.bkb-support-ticket-actions{display:flex;gap:.5rem;margin-top:.7rem;font-size:.78rem}.bkb-support-empty{padding:1.3rem 1rem;color:#91a5c0}
        @media(max-width:1000px){.bkb-support-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.bkb-support-queues{grid-template-columns:1fr}}@media(max-width:560px){.bkb-support-kpis{grid-template-columns:1fr}.bkb-support-toolbar{align-items:flex-start;flex-direction:column}}
    </style>
</x-filament-panels::page>
