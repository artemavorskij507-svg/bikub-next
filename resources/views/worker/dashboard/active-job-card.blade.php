<section class="wv2-current-card" aria-labelledby="active-job-title">
  @if($activeOrder)
    <p class="wv2-card-kicker">Current Job</p>
    <h3 id="active-job-title">{{ $activeOrder->order_number }}</h3>
    <p class="muted">{{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key ?? 'Assigned service' }}</p>
    <div class="wv2-job-meta">
      <div><span class="wv2-card-kicker">Pickup</span><strong>{{ $pickup ?: 'No real address captured' }}</strong></div>
      <div><span class="wv2-card-kicker">Drop-off</span><strong>{{ $dropoff ?: 'No real address captured' }}</strong></div>
    </div>
    <a class="worker-btn is-primary" href="{{ route('worker.orders.show', $activeOrder) }}">Navigate / Open Current Job</a>
  @else
    <div class="wv2-waiting-empty">
      <p class="wv2-card-kicker">Waiting mode</p>
      <strong id="active-job-title">No active assignment</strong>
      <p class="muted">{{ $online ? 'You are online and waiting for assignment.' : 'Swipe online when ready for work.' }} No fake dispatches are shown.</p>
    </div>
  @endif
</section>