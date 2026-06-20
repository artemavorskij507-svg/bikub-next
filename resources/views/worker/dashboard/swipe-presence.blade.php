<section class="wv2-card" aria-labelledby="presence-title">
  <p class="wv2-card-kicker">Presence</p>
  <h3 id="presence-title">{{ $online ? 'Swipe to go offline' : 'Swipe to go online' }}</h3>
  <form id="presence-form" method="POST" action="{{ $online ? route('worker.presence.offline') : route('worker.presence.online') }}">@csrf</form>
  <div class="wv2-swipe {{ $online ? 'is-offline' : '' }}" data-swipe-presence data-form="presence-form" role="group" aria-label="{{ $online ? 'Swipe to go offline' : 'Swipe to go online' }}">
    <div class="wv2-swipe-track">{{ $online ? 'Slide to go offline' : 'Slide to go online' }}</div>
    <button class="wv2-swipe-knob" type="button" aria-label="{{ $online ? 'Go offline' : 'Go online' }}">→</button>
  </div>
  <p class="muted wv2-swipe-status">Presence uses existing server routes. GPS remains separate and explicit.</p>
  @unless($profileApproved)<p class="worker-error" style="border-radius:12px;padding:.65rem;margin:.7rem 0 0">Worker profile must be approved before going online.</p>@endunless
</section>