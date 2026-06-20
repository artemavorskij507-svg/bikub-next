<section class="cj-card cj-next"><p class="worker-hero-eyebrow">One next action</p>
@if(($executionState['next_action']['available'] ?? false) && filled($executionState['next_action']['route'] ?? null))
<form method="post" action="{{ route($executionState['next_action']['route'], $order) }}">@csrf<button class="worker-btn is-primary cj-primary" type="submit">{{ $executionState['next_action']['label'] }}</button></form>
<p class="muted">This action is recorded in the order and dispatch audit trail.</p>
@else
<h3>{{ $executionState['next_action']['label'] ?? 'No worker action available' }}</h3><p class="muted">{{ $executionState['next_action']['reason'] ?? 'Customer/admin confirmation may be required before lifecycle completion.' }}</p>
@endif
</section>