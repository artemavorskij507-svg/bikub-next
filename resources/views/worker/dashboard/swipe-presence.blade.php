<section class="wv2-bottom-dock" aria-label="Worker presence and map status">
  <div class="wv2-status-strip" aria-label="Real operational status">
    <div class="wv2-pill"><span>GPS</span><strong>{{ $gpsState }}</strong></div>
    <div class="wv2-pill"><span>Last ping</span><strong>{{ $gpsAgeLabel }}</strong></div>
    <div class="wv2-pill"><span>Accuracy</span><strong>{{ $gpsAccuracyLabel }}</strong></div>
    <div class="wv2-pill"><span>Work zone</span><strong>Narvik / Ballangen</strong></div>
  </div>
  <div class="wv2-swipe-wrap">
    <p class="wv2-card-kicker">{{ $online ? 'Online — visible to dispatch' : 'Offline — not receiving work' }}</p>
    <form id="presence-form" method="POST" action="{{ $online ? route('worker.presence.offline') : route('worker.presence.online') }}">@csrf</form>
    <div class="wv2-swipe {{ $online ? 'is-offline' : '' }}" data-swipe-presence data-form="presence-form" role="group" aria-label="{{ $online ? 'Swipe to go offline' : 'Swipe to go online' }}">
      <div class="wv2-swipe-track">{{ $online ? 'Slide to go offline' : 'Slide to go online' }}</div>
      <button class="wv2-swipe-knob" type="button" aria-label="{{ $online ? 'Go offline' : 'Go online' }}">→</button>
    </div>
    <p class="wv2-swipe-status">Swipe is the only presence control. GPS is explicit and one-time.</p>
  </div>
</section>