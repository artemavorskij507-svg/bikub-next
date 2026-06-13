@extends($portal === 'account' ? 'layouts.account-shell' : 'worker.layout')
@section('title',$ticket->ticket_number)
@section('content')
<a href="{{ route($portal.'.support.index') }}">Back to support</a><h1>{{ $ticket->ticket_number }}</h1><article class="shell-card"><strong>{{ $ticket->subject }}</strong><p>{{ $ticket->summary }}</p><p>{{ str($ticket->status)->replace('_',' ')->title() }} · {{ str($ticket->priority)->title() }}</p></article>
<h2>Visible messages</h2><section class="shell-grid">@forelse($ticket->messages as $message)<article class="shell-card"><strong>{{ ucfirst($portal) }} visible</strong><p>{{ $message->body }}</p><small>{{ $message->created_at }}</small></article>@empty<div class="shell-card">No visible messages.</div>@endforelse</section>
<audio id="support-message-sent" preload="auto" src="{{ asset('audio/support/message-sent.mp3') }}"></audio><form id="support-reply-form" class="shell-form shell-card" method="post" action="{{ route($portal.'.support.reply',$ticket) }}">@csrf<label>Reply<textarea name="body" required rows="5"></textarea></label><button type="submit">Send reply</button></form>
<script>document.getElementById('support-reply-form')?.addEventListener('submit',()=>document.getElementById('support-message-sent')?.play().catch(()=>{}));</script>
@endsection
