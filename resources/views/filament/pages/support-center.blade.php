<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-4">
        @foreach($metrics as $label => $value)
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
                <div class="text-xs font-semibold uppercase text-gray-500">{{ str($label)->replace('_', ' ')->title() }}</div>
                <div class="mt-2 text-2xl font-bold">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-gray-500">Latest ticket activity: {{ $latestTicketAt ? \Illuminate\Support\Carbon::parse($latestTicketAt)->diffForHumans() : 'No tickets yet' }}</p>
        <a class="fi-btn fi-btn-color-primary" href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('index') }}">Open ticket operations</a>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        @foreach($queues as $title => $tickets)
            <section class="rounded-lg border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
                <header class="border-b border-gray-200 px-4 py-3 font-semibold dark:border-white/10">{{ $title }}</header>
                <div class="divide-y divide-gray-200 dark:divide-white/10">
                    @forelse($tickets as $ticket)
                        <a href="{{ \App\Filament\Resources\SupportTickets\SupportTicketResource::getUrl('view', ['record' => $ticket]) }}" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/5">
                            <div class="flex items-start justify-between gap-3">
                                <div><strong>{{ $ticket->ticket_number }}</strong><p class="text-sm">{{ $ticket->subject }}</p></div>
                                <span class="text-xs font-semibold uppercase">{{ str($ticket->priority)->title() }}</span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                                <span>{{ str($ticket->status)->replace('_', ' ')->title() }}</span>
                                <span>{{ str($ticket->category)->replace('_', ' ')->title() }}</span>
                                @if($ticket->order)<span>{{ $ticket->order->order_number }}</span>@endif
                                <span>{{ $ticket->assignee?->name ?? 'Unassigned' }}</span>
                                <span>{{ $ticket->last_message_at?->diffForHumans() ?? $ticket->updated_at?->diffForHumans() }}</span>
                            </div>
                            <div class="mt-3 flex gap-3 text-sm">
                                <span>View / add note / resolve</span>
                                @if($ticket->assigned_to_id !== auth()->id())<button type="button" wire:click.prevent="assignToMe({{ $ticket->id }})">Assign to me</button>@endif
                            </div>
                        </a>
                    @empty
                        <p class="px-4 py-6 text-sm text-gray-500">No {{ str($title)->lower() }} tickets.</p>
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
</x-filament-panels::page>
