@props(['items' => []])

<section {{ $attributes->class('bkb-os-action-matrix') }} aria-label="Action readiness matrix">
    @foreach ($items as $item)
        <article class="is-{{ $item['tone'] ?? 'review' }}">
            <div>
                <span>{{ $item['status'] ?? 'Review' }}</span>
                <strong>{{ $item['name'] }}</strong>
                <p>{{ $item['requirement'] ?? '' }}</p>
            </div>
            <div>
                <small>{{ $item['blocker'] ?? 'No blocker reported.' }}</small>
                @if (! empty($item['url']))
                    <a href="{{ $item['url'] }}">{{ $item['action'] ?? 'Open' }}</a>
                @else
                    <button type="button" disabled title="{{ $item['blocker'] ?? 'Route is not available.' }}">{{ $item['action'] ?? 'Unavailable' }}</button>
                @endif
            </div>
        </article>
    @endforeach
</section>
