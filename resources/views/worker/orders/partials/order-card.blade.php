@php
    $st = $order->status?->value ?? (string) $order->status;
    $assignment = $order->activeDispatchAssignment() ?? $order->dispatchAssignments->first();
    $assignmentStatus = $assignment?->status;
    $intake = $order->metadata['intake'] ?? [];
    $pickup = $intake['pickup_address'] ?? $intake['vehicle_location'] ?? $intake['task_location'] ?? null;
    $dropoff = $intake['dropoff_address'] ?? $intake['destination_address'] ?? null;
    $serviceTitle = $order->scenario?->title ?? Str::of($order->service_scenario_key)->replace(['.', '_', '-'], ' ')->title();
    $quote = $order->priceQuotes->first();
    $price = $quote?->total_nok;
    $currency = $quote?->currency ?? $order->currency ?? 'NOK';
    $paymentStatus = $order->payment_status?->value ?? (is_string($order->payment_status ?? null) ? $order->payment_status : null);
    $window = $intake['delivery_window'] ?? $order->scheduled_at?->format('d.m H:i') ?? null;
    $contactPhone = $intake['contact_phone'] ?? $order->customer_phone ?? null;

    $variant = $variant ?? match (true) {
        in_array($st, ['accepted', 'in_progress'], true) || $assignmentStatus === 'accepted' => 'active',
        $st === 'submitted' || $assignmentStatus === 'assigned' => 'assigned',
        $st === 'completed' => 'completed',
        $st === 'cancelled' => 'cancelled',
        default => 'history',
    };
    $visualVariant = $variant === 'hero' ? 'active' : $variant;
    $eyebrow = match ($visualVariant) {
        'active' => 'Active order',
        'assigned' => 'Assigned to you',
        'completed' => 'Completed order',
        'cancelled' => 'Cancelled order',
        default => 'Order history',
    };
    $statusLabel = match ($visualVariant) {
        'active' => 'Активный',
        'assigned' => 'Назначен',
        'completed' => 'Выполнен',
        'cancelled' => 'Отменён',
        default => $statusLabels[$st] ?? Str::of($st)->replace('_',' ')->title(),
    };
    $ctaLabel = match ($visualVariant) {
        'active' => 'Open Current Job',
        'assigned' => 'Review assignment',
        'completed', 'cancelled', 'history' => 'View details',
        default => 'View details',
    };
    $statusClass = match ($visualVariant) {
        'active', 'completed' => 'ok',
        'assigned' => 'warn',
        'cancelled' => 'danger',
        default => '',
    };

    $pickupLat = $intake['pickup_latitude'] ?? $intake['pickup_lat'] ?? null;
    $pickupLng = $intake['pickup_longitude'] ?? $intake['pickup_lng'] ?? null;
    $dropLat = $intake['dropoff_latitude'] ?? $intake['dropoff_lat'] ?? $intake['destination_latitude'] ?? null;
    $dropLng = $intake['dropoff_longitude'] ?? $intake['dropoff_lng'] ?? $intake['destination_longitude'] ?? null;
    $hasRealCoords = is_numeric($pickupLat) && is_numeric($pickupLng) && is_numeric($dropLat) && is_numeric($dropLng);
    $completedAt = $order->completed_at ?? $order->events->firstWhere('event_type', 'order.completed')?->created_at;
@endphp

<article class="ov2-order-card ov2-order-card--{{ $visualVariant }} {{ $variant === 'hero' ? 'ov2-order-card--hero' : '' }}" data-status="{{ $visualVariant }}" data-search="{{ Str::lower($order->order_number.' '.$serviceTitle.' '.$pickup.' '.$dropoff) }}">
    <div class="ov2-card-topline">
        <span class="ov2-card-eyebrow">{{ $eyebrow }}</span>
        <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>

    <div class="ov2-card-body">
        <div class="ov2-route-pane" aria-label="Route preview">
            @if($hasRealCoords)
                <div class="ov2-real-map">
                    <span class="ov2-map-pin ov2-map-pin--pickup">●</span>
                    <span class="ov2-map-pin ov2-map-pin--drop">◆</span>
                    <div class="ov2-leaflet-preview" data-pickup-lat="{{ $pickupLat }}" data-pickup-lng="{{ $pickupLng }}" data-dropoff-lat="{{ $dropLat }}" data-dropoff-lng="{{ $dropLng }}"></div>
                    <p>Real coordinate preview</p>
                    <small>Pickup and drop-off coordinates are available.</small>
                </div>
            @else
                <div class="ov2-route-empty">
                    <div class="ov2-fake-grid" aria-hidden="true"></div>
                    <span class="ov2-route-dot ov2-route-dot--a" aria-hidden="true"></span>
                    <span class="ov2-route-dot ov2-route-dot--b" aria-hidden="true"></span>
                    <strong>Coordinates will appear after dispatch.</strong>
                    <small>Pickup and drop-off addresses are shown below; no route, ETA or GPS is estimated.</small>
                </div>
            @endif
        </div>

        <div class="ov2-order-core">
            <div class="ov2-identity-row">
                <div class="ov2-order-icon" aria-hidden="true">
                    @if(Str::startsWith($order->service_scenario_key, 'delivery.')) 🛒
                    @elseif(Str::startsWith($order->service_scenario_key, 'moving.')) 📦
                    @elseif(Str::startsWith($order->service_scenario_key, 'eco.')) ♻️
                    @elseif(Str::startsWith($order->service_scenario_key, 'handyman.')) 🛠️
                    @else 📦
                    @endif
                </div>
                <div>
                    <p class="ov2-order-number">{{ $order->order_number }}</p>
                    <p class="ov2-order-service">{{ $serviceTitle }}</p>
                </div>
            </div>

            <div class="ov2-timeline" aria-label="Pickup and drop-off">
                <div class="ov2-timeline-row">
                    <span class="ov2-pin" aria-hidden="true">●</span>
                    <div><span>PICKUP</span><strong>{{ $pickup ?: 'Pickup address not provided' }}</strong></div>
                </div>
                <div class="ov2-timeline-row">
                    <span class="ov2-pin" aria-hidden="true">◆</span>
                    <div><span>DROP-OFF</span><strong>{{ $dropoff ?: 'Drop-off address not provided' }}</strong></div>
                </div>
            </div>

            <div class="ov2-meta-strip">
                <span>{{ $window ? 'Window '.$window : 'Window unavailable' }}</span>
                @if($contactPhone)<span>Contact available</span>@endif
                @if($completedAt && $visualVariant === 'completed')<span>Completed {{ $completedAt->format('d.m H:i') }}</span>@endif
            </div>
        </div>

        <div class="ov2-next-pane">
            <p class="ov2-price">{{ $price !== null ? number_format((float) $price, 0, '.', ' ').' '.$currency : 'Price not calculated' }}</p>
            <p class="ov2-paystate">{{ $paymentStatus ? 'Payment: '.Str::of($paymentStatus)->replace('_',' ') : 'Payment status unavailable' }}</p>
            @if($visualVariant === 'completed' && $order->completionProofs->isEmpty())<p class="ov2-muted">No proof attached yet</p>@endif
            <a class="worker-btn is-primary ov2-primary-cta" href="{{ route('worker.orders.show', $order) }}">{{ $ctaLabel }}</a>
        </div>
    </div>
</article>
