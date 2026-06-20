<section class="wv2-card" aria-labelledby="state-machine-title">
  <p class="wv2-card-kicker">State machine</p>
  <h3 id="state-machine-title">Current worker state</h3>
  @foreach(['Offline','Waiting','Assigned','Navigate to Pickup','Navigate to Dropoff','Completion Proof'] as $state)
    <div class="wv2-step {{ $uiState === $state ? 'is-current' : '' }}">
      <i>{{ $uiState === $state ? '●' : '○' }}</i>
      <div><strong>{{ $state }}</strong><br><span class="muted">{{ $uiState === $state ? 'Current projection from real worker/order data' : 'Not active now' }}</span></div>
    </div>
  @endforeach
</section>