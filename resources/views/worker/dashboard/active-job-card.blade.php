<section class="wv2-sheet wv2-glass" aria-labelledby="active-job-title">
  @if($activeOrder)
    <p class="wv2-kicker">Current Job</p>
    <h3 id="active-job-title">{{ $activeOrder->order_number }}</h3>
    <p>{{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key ?? 'Assigned service' }}</p>
    <div class="wv2-sheet-actions"><a class="worker-btn is-primary" href="{{ route('worker.orders.show', $activeOrder) }}">Navigate / Current Job</a></div>
  @else
    <p class="wv2-kicker">{{ $online ? 'Waiting mode' : 'Ready check' }}</p>
    <h3 id="active-job-title">No active assignment</h3>
    <p>{{ $online ? 'You are online and waiting for assignment.' : 'Swipe online when ready for work.' }}</p>
  @endif
</section>