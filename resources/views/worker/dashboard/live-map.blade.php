<section class="wv2-map-stage" aria-labelledby="dashboard-v2-title">
  <div id="worker-live-map" class="wv2-map" role="application" aria-label="Interactive Narvik and Ballangen worker map"></div>
  <div class="wv2-vignette" aria-hidden="true"></div>
  <header class="wv2-appbar" aria-label="Worker app bar">
    <a class="wv2-brand wv2-glass" href="{{ route('worker.dashboard') }}">
      <span class="wv2-logo">B</span><span><strong>BiKuBe Worker</strong><small>Courier cockpit</small></span>
    </a>
    <nav class="wv2-nav wv2-glass" aria-label="Worker primary navigation">
      <a class="is-active" href="{{ route('worker.dashboard') }}">🏠 <span>Dashboard</span></a>
      <a href="{{ route('worker.orders.index') }}">📦 <span>Orders</span></a>
      <a href="{{ route('worker.schedule.index') }}">🗓 <span>Schedule</span></a>
      <a href="{{ route('worker.wallet.index') }}">💳 <span>Wallet</span></a>
      <a href="{{ route('worker.support.index') }}">🛟 <span>Help</span></a>
    </nav>
    <div class="wv2-presence wv2-glass"><span class="wv2-presence-dot"></span>{{ $online ? 'Online' : 'Offline' }}</div>
  </header>
  <section class="wv2-state-card wv2-glass" aria-labelledby="dashboard-v2-title">
    <p class="wv2-kicker">Pilot zone · Narvik / Ballangen</p>
    <h2 id="dashboard-v2-title">{{ $uiState }}</h2>
    <div class="wv2-primary">
      @if($activeOrder)
        {{ $activeOrder->order_number }} · {{ $activeOrder->scenario?->title ?? $activeOrder->service_scenario_key ?? 'Assigned service' }}
      @else
        {{ $online ? 'You are online and waiting for assignment.' : 'Swipe online when ready for work.' }}
      @endif
    </div>
  </section>
  <div class="wv2-status-pill wv2-glass"><span>{{ $online ? 'Online' : 'Offline' }}</span><b>{{ $gpsState }}</b></div>
  <aside class="wv2-locgate wv2-glass" aria-label="Location readiness">
    <strong>{{ request()->secure() ? 'Location can be requested' : 'HTTPS needed for browser GPS' }}</strong>
    <p>BiKuBe asks for a real GPS ping only after your action. Presence and background tracking are separate.</p>
  </aside>
  <section class="wv2-services" aria-label="BiKuBe service readiness">
    @foreach($serviceLanes as $lane)
      <article class="wv2-service wv2-glass {{ $lane['enabled'] ? 'is-on' : 'is-off' }}">
        <b>{{ $lane['icon'] }} {{ $lane['label'] }}</b>
        <span>{{ $lane['enabled'] ? $lane['key'].' · '.$lane['note'] : $lane['note'] }}</span>
      </article>
    @endforeach
  </section>
  <nav class="wv2-mobile-nav wv2-glass" aria-label="Worker bottom navigation">
    <a class="is-active" href="{{ route('worker.dashboard') }}">🏠<span>Home</span></a>
    <a href="{{ route('worker.orders.index') }}">📦<span>Orders</span></a>
    <a href="{{ route('worker.wallet.index') }}">💳<span>Wallet</span></a>
    <a href="{{ route('worker.support.index') }}">🛟<span>Help</span></a>
    <a href="{{ route('worker.profile.index') }}">👤<span>More</span></a>
  </nav>
</section>