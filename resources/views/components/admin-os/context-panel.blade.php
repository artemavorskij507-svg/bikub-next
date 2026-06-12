@props(['title'])
<section {{ $attributes->class('bkb-cc-panel') }}>
    <header><h2>{{ $title }}</h2></header>
    <div class="bkb-cc-panel-body">{{ $slot }}</div>
</section>
