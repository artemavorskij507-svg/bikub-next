<header class="cjx-topbar" aria-label="Current job top bar">
  <a class="cjx-brand cjx-glass" href="{{ route('worker.dashboard') }}"><span class="cjx-logo">B</span><span><strong>{{ $order->order_number }}</strong><small>Current Job</small></span></a>
  <div class="cjx-job-pill cjx-glass"><span>{{ str($order->status->value)->replace('_',' ')->title() }}</span><b>{{ $gpsState }}</b></div>
</header>