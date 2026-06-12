@props(['href' => null, 'tone' => 'default'])
@if($href)
<a href="{{ $href }}" {{ $attributes->class("bkb-cc-action is-$tone") }}>{{ $slot }}</a>
@else
<button type="button" {{ $attributes->class("bkb-cc-action is-$tone") }}>{{ $slot }}</button>
@endif
