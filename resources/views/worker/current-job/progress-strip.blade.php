<nav class="cjx-strip" aria-label="Current job progress">
  @foreach($executionState['steps'] as $step)
    @php $state = $step['complete'] ? 'is-done' : ($step['available'] ? 'is-current' : 'is-locked'); @endphp
    <span class="cjx-step {{ $state }}">{{ $step['complete'] ? '✓' : ($step['available'] ? '●' : '○') }} {{ $step['label'] }}</span>
  @endforeach
</nav>