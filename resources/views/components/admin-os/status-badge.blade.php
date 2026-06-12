@props(['value', 'tone' => null])
<span class="bkb-cc-badge is-{{ $tone ?? str($value)->replace('_', '-') }}">{{ str($value)->replace('_', ' ')->title() }}</span>
