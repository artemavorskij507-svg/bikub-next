@extends('layouts.account-shell')
@section('title',$order->order_number)
@section('content')
<a href="{{ route('account.orders.index') }}">Back to orders</a><h1>{{ $order->order_number }}</h1>
<section class="shell-grid cards">
<article class="shell-card"><h2>Order status</h2><p>Status: <strong>{{ str($order->status->value)->replace('_',' ')->title() }}</strong></p><p>Service: {{ $order->scenario?->title ?? $order->service_scenario_key }}</p><p>Estimate: {{ $order->latestPriceQuote()?->total ? number_format((float)$order->latestPriceQuote()->total,2).' '.$order->currency : 'Manual review' }}</p><p>Payment provider: not connected</p><p>Customer live tracking: not exposed</p></article>
<article class="shell-card"><h2>Support</h2><p>{{ $order->supportTickets->count() }} customer-linked ticket(s)</p>@foreach($order->supportTickets as $ticket)<p><a href="{{ route('account.support.show',$ticket) }}">{{ $ticket->ticket_number }} · {{ $ticket->subject }}</a></p>@endforeach</article>
<article class="shell-card"><h2>Billing</h2><p>Online payment is not connected yet.</p>@forelse($order->billingDocuments as $document)<p><strong>{{ $document->document_number }}</strong> · {{ str($document->status)->title() }} · {{ number_format((float)$document->total_amount,2) }} {{ $document->currency }}</p>@empty<p>No customer-visible billing documents.</p>@endforelse</article>
@php($completionProof=$order->completionProofs->first())
<article class="shell-card"><h2>Completion confirmation</h2>
@if($completionProof)
<p>Status: <strong>{{ str($completionProof->status)->replace('_',' ')->title() }}</strong></p>
<p>{{ $completionProof->worker_note }}</p>
@if($completionProof->status === 'submitted')
<form method="post" action="{{ route('account.completion-proofs.accept',$completionProof) }}">@csrf<label for="completion-note">Optional confirmation note</label><textarea id="completion-note" name="note" maxlength="2000"></textarea><button>Confirm completed</button></form>
<form method="post" action="{{ route('account.completion-proofs.dispute',$completionProof) }}">@csrf<label for="dispute-reason">Problem description</label><textarea id="dispute-reason" name="reason" required maxlength="5000"></textarea><button>Report a problem</button></form>
<p>Confirmation records your review only. Payment remains separate.</p>
@elseif($completionProof->status === 'accepted')<p>You confirmed this completion. Payment remains unavailable until the payment provider is ready.</p>
@elseif($completionProof->status === 'disputed')<p>Your dispute was recorded. Continue in Support.</p>
@endif
@else<p>No completion proof has been submitted yet.</p>
@endif
</article>
</section>
@endsection
