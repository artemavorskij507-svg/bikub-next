@php
    /** @var \Prunacatalin\FilamentLocaleSwitcher\LocaleSwitchPlugin $plugin */
    /** @var string $current */

    $flagSvg = [
        'nb' => 'https://flagcdn.com/no.svg',
        'en' => 'https://flagcdn.com/gb.svg',
        'ru' => 'https://flagcdn.com/ru.svg',
        'uk' => 'https://flagcdn.com/ua.svg',
    ];

    $currentFlag = $flagSvg[$current] ?? null;
@endphp

<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="fi-locale-switcher"
    style="
        position: relative;
        display: inline-flex;
        align-items: center;
        z-index: 50;
    "
>
    <button
        type="button"
        @click="open = ! open"
        class="fi-icon-btn"
        style="
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            min-width: 3.25rem;
            height: 2.25rem;
            padding: 0 .65rem;
            border-radius: .7rem;
            line-height: 1;
        "
        :aria-expanded="open"
        aria-haspopup="menu"
        title="{{ $plugin->getLabel($current) }}"
    >
        @if ($currentFlag)
            <img
                src="{{ $currentFlag }}"
                alt=""
                style="
                    width: 1.25rem;
                    height: .9rem;
                    border-radius: .15rem;
                    object-fit: cover;
                    box-shadow: 0 0 0 1px rgba(255,255,255,.15);
                "
            >
        @endif

        <span style="font-size: .75rem; font-weight: 700; text-transform: uppercase;">
            {{ strtoupper($current) === 'NB' ? 'NO' : strtoupper($current) }}
        </span>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition.origin.top.right
        role="menu"
        class="fi-dropdown-panel"
        style="
            position: absolute;
            inset-inline-end: 0;
            top: calc(100% + .5rem);
            z-index: 100;
            min-width: 13rem;
            border-radius: .8rem;
            padding: .35rem 0;
            box-shadow: 0 18px 40px rgba(0,0,0,.35);
            background: var(--gray-900, #111827);
            border: 1px solid rgba(255,255,255,.10);
            overflow: hidden;
        "
    >
        @foreach ($plugin->getLocales() as $code)
            @php
                $active = $code === $current;
                $flag = $flagSvg[$code] ?? null;
                $displayCode = strtoupper($code) === 'NB' ? 'NO' : strtoupper($code);
            @endphp

            <form method="POST" action="{{ route('filament-locale-switcher.switch', ['locale' => $code]) }}" style="margin:0;">
                @csrf

                <button
                    type="submit"
                    class="fi-dropdown-list-item"
                    style="
                        display: flex;
                        align-items: center;
                        gap: .75rem;
                        width: 100%;
                        padding: .7rem .9rem;
                        font-size: .9rem;
                        text-align: start;
                        background: {{ $active ? 'rgba(16,185,129,.10)' : 'transparent' }};
                        border: 0;
                        cursor: pointer;
                        color: {{ $active ? 'var(--primary-400, #34d399)' : 'inherit' }};
                        font-weight: {{ $active ? '700' : '500' }};
                    "
                    role="menuitem"
                >
                    @if ($flag)
                        <img
                            src="{{ $flag }}"
                            alt=""
                            style="
                                width: 1.35rem;
                                height: .95rem;
                                border-radius: .15rem;
                                object-fit: cover;
                                box-shadow: 0 0 0 1px rgba(255,255,255,.14);
                            "
                        >
                    @else
                        <span style="width: 1.35rem; font-weight: 700;">{{ $displayCode }}</span>
                    @endif

                    <span style="flex:1;">{{ $plugin->getLabel($code) }}</span>

                    @if ($active)
                        <span aria-hidden="true" style="font-weight: 800;">✓</span>
                    @endif
                </button>
            </form>
        @endforeach
    </div>
</div>