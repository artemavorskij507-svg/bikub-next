<section class="cjx-sheet cjx-glass" aria-label="Current job next action">
  <div class="cjx-action-head"><div><p class="cjx-kicker">One next action</p><h1>{{ $primaryLabel }}</h1><p>No fake route, ETA, GPS movement or completion is generated.</p></div><span class="status-pill ok">{{ $executionLabel }}</span></div>
  @if(($executionState['next_action']['available'] ?? false) && filled($executionState['next_action']['route'] ?? null))
    <form method="post" action="{{ route($executionState['next_action']['route'], $order) }}">@csrf<button class="worker-btn is-primary cjx-primary" type="submit">{{ $primaryLabel }}</button></form>
  @else
    <p class="muted">{{ $executionState['next_action']['reason'] ?? 'Customer/admin confirmation may be required before lifecycle completion.' }}</p>
  @endif
  <div class="cjx-secondary">
    @if($navigationTarget)<a class="worker-btn" id="nav-primary" href="#" target="_blank" rel="noopener">Navigate</a><button class="worker-btn" id="copy-address" type="button">Copy address</button>@endif
    <button class="worker-btn" id="gps-share" type="button">Share GPS</button>
  </div>
</section>