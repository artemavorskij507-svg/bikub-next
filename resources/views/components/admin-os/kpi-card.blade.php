@props(['label', 'value', 'tone' => 'default'])
<article class="bkb-cc-kpi is-{{ $tone }}">
    <span>{{ $label }}</span>
    <strong>{{ $value }}</strong>
</article>
