<section class="cj-card"><p class="worker-hero-eyebrow">Navigation</p><h2>Navigate</h2><p class="muted">External apps calculate their own routes. BiKuBe does not provide fake ETA, fake route preview, or internal turn-by-turn navigation.</p>
@php $destination = $navigationTarget; @endphp
@if($destination)
<div class="kv"><span>Destination</span><strong id="nav-destination">{{ $destination }}</strong></div><div class="kv"><span>Target logic</span><strong>{{ $hasPickedUp ? 'Drop-off after pickup' : 'Pickup before pickup' }}</strong></div>
<label>Preferred navigation app<select id="nav-preferred" class="worker-btn"><option value="google">Google Maps</option><option value="apple">Apple Maps</option><option value="waze">Waze</option><option value="here">HERE</option></select></label>
<div class="actions"><a class="worker-btn is-primary" id="nav-primary" href="#" target="_blank" rel="noopener">Navigate</a><button class="worker-btn" id="copy-address" type="button">Copy address</button></div>
<div class="actions cj-nav-secondary"><a data-app="google" href="#">Google Maps</a><a data-app="apple" href="#">Apple Maps</a><a data-app="waze" href="#">Waze</a><a data-app="here" href="#">HERE</a></div>
@else<p class="muted">No real pickup/drop-off address is captured. Contact support or dispatcher.</p>@endif</section>