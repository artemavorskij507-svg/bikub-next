@props(['selected' => false])
<button
    type="button"
    aria-pressed="{{ $selected ? 'true' : 'false' }}"
    {{ $attributes->class(['bkb-admin-queue-card', 'is-selected' => $selected]) }}
>
    {{ $slot }}
</button>
