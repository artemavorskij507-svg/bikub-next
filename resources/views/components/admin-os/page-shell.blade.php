@props([
    'eyebrow' => 'BiKuBe OS',
    'title',
    'subtitle' => null,
    'status' => null,
    'primaryHref' => null,
    'primaryLabel' => null,
    'secondaryHref' => null,
    'secondaryLabel' => null,
])

<main {{ $attributes->class('bkb-os-page-shell') }} aria-labelledby="bkb-os-page-title">
    <section class="bkb-os-page-hero">
        <div class="bkb-os-page-hero__copy">
            <p class="bkb-os-eyebrow">{{ $eyebrow }}</p>
            <h1 id="bkb-os-page-title">{{ $title }}</h1>
            @if ($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
            <div class="bkb-os-page-hero__actions">
                @if ($primaryHref && $primaryLabel)
                    <a class="bkb-os-button bkb-os-button--primary" href="{{ $primaryHref }}">{{ $primaryLabel }}</a>
                @endif
                @if ($secondaryHref && $secondaryLabel)
                    <a class="bkb-os-button" href="{{ $secondaryHref }}">{{ $secondaryLabel }}</a>
                @endif
            </div>
        </div>

        @if ($status || isset($aside))
            <aside class="bkb-os-page-hero__status">
                @if ($status)
                    <span>Current posture</span>
                    <strong>{{ $status }}</strong>
                @endif
                {{ $aside ?? '' }}
            </aside>
        @endif
    </section>

    {{ $slot }}
</main>
