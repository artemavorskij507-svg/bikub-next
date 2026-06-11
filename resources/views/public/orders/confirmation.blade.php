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
    </div>
</article>
@endsection
