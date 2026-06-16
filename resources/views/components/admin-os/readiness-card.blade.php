@props([
    'title',
    'value' => null,
    'tone' => 'review',
    'body' => null,
    'href' => null,
    'action' => 'Open',
])

<article {{ $attributes->class("bkb-os-readiness-card is-$tone") }}>
    <span>{{ $title }}</span>
    @if ($value !== null)
        <strong>{{ $value }}</strong>
    @endif
    @if ($body)
        <p>{{ $body }}</p>
    @endif
    @if ($href)
        <a href="{{ $href }}">{{ $action }}</a>
    @endif
</article>
