<section class="wv2-card" aria-labelledby="active-job-title">
  <p class="wv2-card-kicker">Active assignment</p>
  @if($activeOrder)
    <h3 id="active-job-title">{{ $activeOrder->order_number }}</h3>
    <p class="muted">{{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key ?? 'Assigned service' }}</p>
    <div class="wv2-job-meta">
      <div><span class="wv2-card-kicker">Status</span><strong>{{ str($activeOrder->status?->value ?? 'unknown')->replace('_',' ')->title() }}</strong></div>
      <div><span class="wv2-card-kicker">Assignment</span><strong>{{ str($activeAssignment?->status ?? 'assigned')->replace('_',' ')->title() }}</strong></div>
      <div><span class="wv2-card-kicker">Pickup</span><strong>{{ $pickup ?: 'No real address captured' }}</strong></div>
      <div><span class="wv2-card-kicker">Drop-off</span><strong>{{ $dropoff ?: 'No real address captured' }}</strong></div>
    </div>
    <a class="worker-btn is-primary" href="{{ route('worker.orders.show', $activeOrder) }}">Open Current Job</a>
  @else
    <h3 id="active-job-title">Waiting mode</h3>
    <div class="wv2-waiting"><div><strong>No active assignment</strong><p class="muted">You are online. Keep the cockpit open; real work appears here when dispatch assigns it.</p></div></div>
  @endif
</section>