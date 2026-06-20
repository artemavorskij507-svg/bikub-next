<section class="cj-card cj-hero">
    <div>
        <p class="worker-hero-eyebrow">Current Job</p>
        <h1>{{ $order->order_number }}</h1>
        <p class="muted">{{ $order->scenario?->title ?? $order->service_scenario_key }} · Assignment {{ str($activeAssignment?->status ?? 'assigned')->replace('_',' ')->title() }}</p>
        <p class="muted cj-truth">Only real assigned work is shown. No fake GPS, fake route, fake ETA or fake completion is generated.</p>
    </div>
    <div class="cj-head-actions"><span class="status-pill ok">{{ str($order->status->value)->replace('_',' ')->title() }}</span><a class="worker-btn" href="{{ route('worker.orders.index') }}">Orders</a></div>
</section>