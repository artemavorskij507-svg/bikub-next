<section class="wv2-floating is-readiness" aria-labelledby="readiness-title">
  <p class="wv2-card-kicker">Readiness</p>
  <h3 id="readiness-title">{{ $profileApproved && $online ? 'Ready' : 'Action needed' }}</h3>
  @foreach($readiness as $item)
    <div class="kv"><span class="muted">{{ $item['label'] }}</span><strong style="color:{{ $item['ok'] ? 'var(--green)' : 'var(--amber)' }}">{{ $item['detail'] }}</strong></div>
  @endforeach
</section>