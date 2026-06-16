@extends($portal === 'account' ? 'layouts.account-shell' : 'worker.layout')

@section('title', ucfirst($portal).' support')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <div>
        @if($portal === 'account')
            <span class="shell-eyebrow">Account support</span>
        @else
            <p class="worker-hero-eyebrow">Worker support</p>
        @endif
        <h1 style="margin:.35rem 0 .4rem;font-size:clamp(1.6rem,4vw,2.4rem);font-weight:950">Support tickets</h1>
        <p style="{{ $portal === 'worker' ? 'color:var(--muted)' : 'color:var(--shell-muted)' }}">
            Open a ticket for any issue with orders, payments or account access.
        </p>
    </div>
    @if($portal === 'account')
        <a class="shell-primary" href="{{ route('account.support.create') }}"
           style="display:inline-flex;align-items:center;text-decoration:none;padding:.65rem 1.1rem;border-radius:6px">
            New ticket
        </a>
    @endif
</div>

@forelse($tickets as $ticket)
    <a href="{{ route($portal.'.support.show', $ticket) }}"
       style="display:block;text-decoration:none;color:inherit;margin-bottom:.65rem">
        @if($portal === 'account')
            <article class="shell-card" style="display:grid;grid-template-columns:1fr auto;gap:.85rem;align-items:center">
                <div>
                    <strong>{{ $ticket->ticket_number }}</strong>
                    <p style="margin:.2rem 0 0;color:var(--shell-muted);font-size:.85rem">{{ str($ticket->subject)->limit(72) }}</p>
                </div>
                <div style="text-align:right">
                    <span style="display:inline-flex;border:1px solid rgba(37,220,145,.24);border-radius:999px;background:rgba(37,220,145,.06);padding:.2rem .55rem;color:var(--bkb-accent,#25dc91);font-size:.72rem;font-weight:900;text-transform:uppercase">
                        {{ str($ticket->status)->replace('_', ' ')->title() }}
                    </span>
                    <p style="margin:.25rem 0 0;font-size:.75rem;color:var(--shell-muted)">{{ str($ticket->priority)->title() }}</p>
                </div>
            </article>
        @else
            <article class="worker-card" style="display:grid;grid-template-columns:1fr auto;gap:.85rem;align-items:center">
                <div>
                    <strong>{{ $ticket->ticket_number }}</strong>
                    <p class="muted" style="margin:.2rem 0 0;font-size:.85rem">{{ str($ticket->subject)->limit(72) }}</p>
                </div>
                <span class="worker-status-pill">{{ str($ticket->status)->replace('_', ' ')->title() }}</span>
            </article>
        @endif
    </a>
@empty
    @if($portal === 'account')
        <article class="shell-card" style="text-align:center;padding:3rem 1.5rem">
            <p style="margin:0 0 1rem;color:var(--shell-muted)">No support tickets yet.</p>
            <a class="shell-primary" href="{{ route('account.support.create') }}"
               style="display:inline-flex;text-decoration:none;padding:.65rem 1.1rem;border-radius:6px">
                Create first ticket
            </a>
        </article>
    @else
        <article class="worker-card worker-empty">
            <div>
                <i>⌁</i>
                <h3>No support tickets</h3>
                <p class="muted">No tickets have been opened from this worker account.</p>
            </div>
        </article>
    @endif
@endforelse

@endsection
