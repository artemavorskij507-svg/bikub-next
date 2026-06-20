<section class="cj-card"><p class="worker-hero-eyebrow">Completion proof</p>
@if($completionProof)
<div class="kv"><span>Status</span><strong>{{ str($completionProof->status)->replace('_',' ')->title() }}</strong></div><p class="muted">{{ $completionProof->worker_note }}</p>
@elseif($proofEligibility['allowed'] ?? false)
<form method="post" action="{{ route('worker.orders.completion-proof.submit', $order) }}">@csrf<label>Completion note<textarea name="worker_note" required maxlength="5000" rows="4" placeholder="Describe the real completed work and handover.">{{ old('worker_note') }}</textarea></label><button class="worker-btn is-primary" type="submit">Submit completion proof</button></form><p class="muted">Text proof only. Photo/media proof is not enabled in Wave 3B.</p>
@else<p class="muted">{{ $proofEligibility['reason'] ?? 'Completion proof is not available yet.' }}</p>@endif</section>