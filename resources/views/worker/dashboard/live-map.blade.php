<section class="wv2-map-stage" aria-labelledby="dashboard-v2-title">
  <div id="worker-live-map" class="wv2-map" role="application" aria-label="Interactive Narvik and Ballangen worker map"></div>
  <div class="wv2-map-vignette" aria-hidden="true"></div>
  <header class="wv2-appbar" aria-label="Worker app bar">
    <a class="wv2-brand" href="{{ route('worker.dashboard') }}">
      <span class="wv2-logo">B</span><span><strong>BiKuBe Worker</strong><span>Courier cockpit</span></span>
    </a>
    <nav class="wv2-nav" aria-label="Worker primary navigation">
      <a class="is-active" href="{{ route('worker.dashboard') }}">🏠 <span>Dashboard</span></a>
      <a href="{{ route('worker.orders.index') }}">📦 <span>Orders</span></a>
      <a href="{{ route('worker.schedule.index') }}">🗓 <span>Schedule</span></a>
      <a href="{{ route('worker.wallet.index') }}">💳 <span>Wallet</span></a>
      <a href="{{ route('worker.support.index') }}">🛟 <span>Help</span></a>
    </nav>
    <div class="wv2-presence"><span class="wv2-presence-dot"></span>{{ $online ? 'Online' : 'Offline' }}</div>
  </header>
  <section class="wv2-focus" aria-labelledby="dashboard-v2-title">
    <div class="wv2-focus-card">
      <p class="wv2-kicker">{{ $activeOrder ? 'Active assignment' : 'Pilot zone · Narvik / Ballangen' }}</p>
      <h2 id="dashboard-v2-title">{{ $uiState }}</h2>
      @if($uiState === 'Waiting')
        <div class="wv2-primary">You are online and waiting for assignment.</div>
        <p class="wv2-sub">Keep app open. Share current position only if operations requests it.</p>
      @elseif($uiState === 'Offline')
        <div class="wv2-primary">Swipe online when ready for work.</div>
      @else
        <div class="wv2-primary">Follow the active job steps. Use Current Job for execution.</div>
      @endif
    </div>
  </section>
  <nav class="wv2-mobile-nav" aria-label="Worker bottom navigation">
    <a class="is-active" href="{{ route('worker.dashboard') }}">🏠<span>Home</span></a>
    <a href="{{ route('worker.orders.index') }}">📦<span>Orders</span></a>
    <a href="{{ route('worker.wallet.index') }}">💳<span>Wallet</span></a>
    <a href="{{ route('worker.support.index') }}">🛟<span>Help</span></a>
    <a href="{{ route('worker.profile.index') }}">👤<span>More</span></a>
  </nav>
</section>