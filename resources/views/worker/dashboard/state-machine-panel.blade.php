<section class="wv2-floating is-state" aria-labelledby="state-machine-title">
  <p class="wv2-card-kicker">State</p>
  <h3 id="state-machine-title">{{ $uiState }}</h3>
  @foreach(['Offline','Waiting','Assigned','Navigate to Pickup','Navigate to Dropoff','Completion Proof'] as $state)
    <div class="wv2-step {{ $uiState === $state ? 'is-current' : '' }}">
      <i>{{ $uiState === $state ? '●' : '○' }}</i>
      <div><strong>{{ $state }}</strong><br><span>{{ $uiState === $state ? 'Current projection from real data' : 'Not active now' }}</span></div>
    </div>
  @endforeach
</section>