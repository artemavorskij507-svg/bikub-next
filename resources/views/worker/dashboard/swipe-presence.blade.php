<section class="wv2-bottom wv2-glass {{ $online ? 'is-waiting' : 'is-offline' }}" aria-label="Worker presence control">
  <form id="presence-form" method="POST" action="{{ $online ? route('worker.presence.offline') : route('worker.presence.online') }}">@csrf</form>
  <div class="wv2-swipe {{ $online ? 'is-offline' : '' }}" data-swipe-presence data-form="presence-form" role="group" aria-label="{{ $online ? 'Swipe to go offline' : 'Swipe to go online' }}">
    <div class="wv2-swipe-track">{{ $online ? 'Slide to go offline' : 'Slide to go online' }}</div>
    <button class="wv2-swipe-knob" type="button" aria-label="{{ $online ? 'Go offline' : 'Go online' }}">→</button>
  </div>
  <p class="wv2-swipe-status">{{ $online ? 'Online and waiting. GPS remains explicit.' : 'Main action: go online when ready.' }}</p>
  <button id="wv2-locate" class="worker-btn wv2-live-btn" type="button">Share current position</button>
  <p id="wv2-gps-status" class="wv2-swipe-status">One real GPS ping only. HTTPS required for browser GPS permission.</p>
</section>