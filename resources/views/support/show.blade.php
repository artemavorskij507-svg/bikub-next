@extends($portal === 'account' ? 'layouts.account-shell' : 'worker.layout')

@section('title', $ticket->ticket_number)

@section('content')
@php($isAccount = $portal === 'account')

{{-- Page header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <div>
        @if($isAccount)
            <span class="shell-eyebrow">Support ticket</span>
        @else
            <p class="worker-hero-eyebrow">Support ticket</p>
        @endif
        <h1 style="margin:.35rem 0 .25rem;font-size:clamp(1.4rem,3vw,2rem);font-weight:950">{{ $ticket->ticket_number }}</h1>
        <p style="{{ $isAccount ? 'color:var(--shell-muted)' : 'color:var(--muted)' }};margin:0">
            {{ str($ticket->subject)->limit(80) }}
        </p>
    </div>
    <div style="display:flex;gap:.65rem;align-items:center">
        <span style="display:inline-flex;border:1px solid rgba(37,220,145,.28);border-radius:999px;background:rgba(37,220,145,.08);padding:.25rem .65rem;color:var(--bkb-accent,#25dc91);font-size:.72rem;font-weight:900;text-transform:uppercase">
            {{ str($ticket->status)->replace('_', ' ')->title() }}
        </span>
        <a href="{{ route($portal.'.support.index') }}"
           style="padding:.5rem .8rem;border-radius:6px;border:1px solid {{ $isAccount ? 'var(--shell-line)' : 'var(--line)' }};text-decoration:none;color:inherit;font-size:.82rem">
            ← Tickets
        </a>
    </div>
</div>

{{-- Ticket summary --}}
@if($isAccount)
    <article class="shell-card" style="margin-bottom:1rem">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem">
            <div style="padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.8rem">Priority</span>
                <strong style="display:block;margin-top:.2rem">{{ str($ticket->priority)->title() }}</strong>
            </div>
            <div style="padding:.5rem 0;border-bottom:1px solid var(--shell-line)">
                <span style="color:var(--shell-muted);font-size:.8rem">Status</span>
                <strong style="display:block;margin-top:.2rem">{{ str($ticket->status)->replace('_', ' ')->title() }}</strong>
            </div>
        </div>
        @if($ticket->summary)
            <p style="margin:.85rem 0 0;font-size:.9rem">{{ $ticket->summary }}</p>
        @endif
    </article>
@else
    <article class="worker-card" style="margin-bottom:1rem">
        <div class="kv">
            <span>Priority</span><strong>{{ str($ticket->priority)->title() }}</strong>
        </div>
        <div class="kv" style="border:none">
            <span>Status</span><strong>{{ str($ticket->status)->replace('_', ' ')->title() }}</strong>
        </div>
        @if($ticket->summary)
            <p class="muted" style="margin:.75rem 0 0;font-size:.85rem">{{ $ticket->summary }}</p>
        @endif
    </article>
@endif

{{-- Messages --}}
<section style="margin-bottom:1rem">
    <p style="margin:0 0 .75rem;font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:{{ $isAccount ? 'var(--shell-muted)' : 'var(--muted)' }}">
        Messages
    </p>

    @forelse($ticket->messages as $message)
        @if($isAccount)
            <article class="shell-card" style="margin-bottom:.65rem">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.65rem">
                    <strong style="font-size:.82rem;color:var(--bkb-accent,#25dc91)">Customer visible</strong>
                    <small style="color:var(--shell-muted);font-size:.75rem">{{ $message->created_at?->format('d M Y H:i') }}</small>
                </div>
                <p style="margin:0;font-size:.9rem;line-height:1.6">{{ $message->body }}</p>
            </article>
        @else
            <article class="worker-card" style="margin-bottom:.65rem">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
                    <strong style="font-size:.82rem;color:var(--green)">Visible to worker</strong>
                    <small class="muted" style="font-size:.75rem">{{ $message->created_at?->format('d M Y H:i') }}</small>
                </div>
                <p class="muted" style="margin:0;font-size:.9rem;line-height:1.6">{{ $message->body }}</p>
            </article>
        @endif
    @empty
        @if($isAccount)
            <article class="shell-card" style="text-align:center;padding:2rem 1rem;color:var(--shell-muted)">
                No messages yet. Use the form below to reply.
            </article>
        @else
            <article class="worker-card" style="text-align:center;padding:2rem 1rem">
                <p class="muted">No messages yet.</p>
            </article>
        @endif
    @endforelse
</section>

{{-- Reply form --}}
<audio id="support-message-sent" preload="auto" src="{{ asset('audio/support/message-sent.mp3') }}"></audio>

@if($isAccount)
    <section class="shell-card">
        <p style="margin:0 0 .85rem;font-size:.8rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--shell-muted)">Reply</p>
        <form id="support-reply-form" class="shell-form" style="margin:0" method="post"
              action="{{ route($portal.'.support.reply', $ticket) }}">
            @csrf
            <label>
                Your message
                <textarea name="body" required rows="5" placeholder="Describe your issue clearly…"></textarea>
            </label>
            <button type="submit" class="shell-primary">Send reply</button>
        </form>
    </section>
@else
    <section class="worker-card">
        <p class="worker-hero-eyebrow" style="margin-bottom:.75rem">Reply</p>
        <form id="support-reply-form" method="post" action="{{ route($portal.'.support.reply', $ticket) }}">
            @csrf
            <label>
                Your message
                <textarea name="body" required rows="5" placeholder="Describe your issue clearly…"></textarea>
            </label>
            <button class="btn primary" style="width:100%;margin-top:.65rem">Send reply</button>
        </form>
    </section>
@endif

@push('scripts')
<script>
document.getElementById('support-reply-form')?.addEventListener('submit', function () {
    document.getElementById('support-message-sent')?.play().catch(function () {});
});
</script>
@endpush
@endsection
