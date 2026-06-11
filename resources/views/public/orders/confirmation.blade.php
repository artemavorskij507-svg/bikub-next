@extends('public.layouts.app')
@section('content')
<article>
    <header class="content-hero">
        <p class="eyebrow">Request received</p>
        <h1>{{ $order->order_number }}</h1>
        <p class="subtitle">Payment, dispatch and live tracking are not connected yet.</p>
    </header>
    <div class="content-body">
        <dl class="request-summary">
            <div><dt>Service</dt><dd>{{ $order->scenario?->title ?? $order->service_scenario_key }}</dd></div>
            <div><dt>Status</dt><dd>{{ ucfirst($order->status->value) }}</dd></div>
            <div><dt>Payment status</dt><dd>{{ str_replace('_', ' ', ucfirst($order->payment_status->value)) }}</dd></div>
        </dl>
        @if(!empty($order->metadata['intake']))
            <h2>Submitted details</h2>
            <dl class="request-summary">
                @foreach($order->metadata['intake'] as $key => $value)
                    <div>
                        <dt>{{ $order->scenario?->fields->firstWhere('field_key', $key)?->label ?? str($key)->replace('_', ' ')->title() }}</dt>
                        <dd>{{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value === '1' ? 'Yes' : ($value === '0' ? 'No' : $value)) }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif
        @php($quote = $order->latestPriceQuote())
        @if($quote)
            <h2>Price estimate</h2>
            <dl class="request-summary">
                <div><dt>Pricing status</dt><dd>{{ str_replace('_', ' ', ucfirst($quote->status)) }}</dd></div>
                @foreach($quote->breakdown ?? [] as $line)<div><dt>{{ $line['label'] }}</dt><dd>{{ number_format((float) $line['amount'], 2) }} {{ $quote->currency }}</dd></div>@endforeach
                <div><dt>Subtotal</dt><dd>{{ number_format((float) $quote->subtotal, 2) }} {{ $quote->currency }}</dd></div>
                <div><dt>Fees / discounts / tax</dt><dd>{{ number_format((float) $quote->fees_total, 2) }} / {{ number_format((float) $quote->discounts_total, 2) }} / {{ number_format((float) $quote->tax_total, 2) }} {{ $quote->currency }}</dd></div>
                <div><dt>Estimated total</dt><dd>{{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}</dd></div>
            </dl>
            <p><strong>Payment provider is not connected yet.</strong> This is an estimate/request quote, not a paid booking.</p>
        @endif
    </div>
</article>
@endsection
