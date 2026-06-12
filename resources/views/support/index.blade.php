<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Support</title><style>body{font:16px system-ui;max-width:900px;margin:auto;padding:24px;background:#f4f7fb;color:#172033}a{color:#087f5b}.card{background:#fff;border:1px solid #dce3ec;padding:18px;margin:12px 0;border-radius:8px}.meta{color:#64748b;font-size:14px}</style></head><body>
<h1>{{ ucfirst($portal) }} support</h1>
@if($portal === 'account')<p><a href="{{ route('account.support.create') }}">Create support ticket</a></p>@endif
@forelse($tickets as $ticket)<a href="{{ route($portal.'.support.show', $ticket) }}"><div class="card"><strong>{{ $ticket->ticket_number }} · {{ $ticket->subject }}</strong><p class="meta">{{ str($ticket->status)->replace('_',' ')->title() }} · {{ str($ticket->priority)->title() }}</p></div></a>@empty<div class="card">No support tickets.</div>@endforelse
</body></html>
