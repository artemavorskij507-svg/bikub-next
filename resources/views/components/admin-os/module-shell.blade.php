@props(['label' => null])
<section {{ $attributes->class('bkb-admin-module') }} @if($label) aria-label="{{ $label }}" @endif>
    {{ $slot }}
</section>
