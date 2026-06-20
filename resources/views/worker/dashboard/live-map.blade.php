<section class="wv2-hero" aria-labelledby="dashboard-v2-title">
  <div class="wv2-map-frame">
    <div id="worker-live-map" class="wv2-map" role="application" aria-label="Interactive Narvik and Ballangen worker map"></div>
    <div class="wv2-map-overlay">
      <div class="wv2-top">
        <div class="wv2-title">
          <p class="worker-hero-eyebrow">BiKuBe Worker Cockpit · Dashboard V2</p>
          <h2 id="dashboard-v2-title">{{ $uiState }}</h2>
          @if($uiState === 'Waiting')<div class="wv2-waiting-primary">You are online and waiting for assignment.</div>@else<p class="muted">Real map, real worker state, real GPS only. No fake ETA, no fake route, no fake orders.</p>@endif
        </div>
        <div class="wv2-state"><span class="worker-chip-dot"></span>{{ $online ? 'Online' : 'Offline' }}</div>
      </div>
      <div class="wv2-bottom">
        <div class="wv2-panel is-map-status">
          <p class="worker-hero-eyebrow">Live operations map</p>
          <h3>Narvik / Ballangen working zone</h3>
          <p class="muted">Keep app open. Share current position only if operations requests it.</p>
          <div class="wv2-metrics">
            <div class="wv2-metric"><span>GPS state</span><strong>{{ $gpsState }}</strong></div>
            <div class="wv2-metric"><span>Last ping</span><strong>{{ $gpsAgeLabel }}</strong></div>
            <div class="wv2-metric"><span>Accuracy</span><strong>{{ $gpsAccuracyLabel }}</strong></div>
          </div>
        </div>
        <div class="wv2-panel is-map-action">
          <button id="wv2-locate" class="worker-btn wv2-live-btn" type="button">Share current position</button>
          <p id="wv2-gps-status" class="muted" style="margin:.55rem 0 0;max-width:22rem">One-time GPS only. No watchPosition and no background GPS.</p>
        </div>
      </div>
    </div>
  </div>
</section>