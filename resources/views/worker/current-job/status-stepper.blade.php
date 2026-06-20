<section class="cj-card"><p class="worker-hero-eyebrow">Status stepper</p><div class="cj-steps">
@foreach($executionState['steps'] as $step)
    @php $state = $step['complete'] ? 'done' : ($step['available'] ? 'current' : 'locked'); @endphp
    <div class="cj-step {{ $state }}"><span>{{ $step['complete'] ? '✓' : ($step['available'] ? '•' : '锁') }}</span><div><strong>{{ $step['label'] }}</strong><p>{{ $step['complete'] ? 'Recorded' : ($step['available'] ? 'Next available action' : $step['reason']) }}</p></div></div>
@endforeach
</div></section>