@extends($portal === 'account' ? 'layouts.account-shell' : 'worker.layout')
@section('title', ucfirst($portal).' support')
@section('content')
<header><span class="shell-eyebrow">{{ ucfirst($portal) }} support</span><h1>Support tickets</h1>@if($portal === 'account')<a class="shell-primary" href="{{ route('account.support.create') }}">Create support ticket</a>@endif</header>
<section class="shell-grid cards">@forelse($tickets as $ticket)<article class="shell-card"><strong>{{ $ticket->ticket_number }} · {{ $ticket->subject }}</strong><p>{{ str($ticket->status)->replace('_',' ')->title() }} · {{ str($ticket->priority)->title() }}</p><a href="{{ route($portal.'.support.show',$ticket) }}">Open ticket</a></article>@empty<div class="shell-card">No support tickets.</div>@endforelse</section>
@endsection
