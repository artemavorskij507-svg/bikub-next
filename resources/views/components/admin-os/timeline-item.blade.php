@props(['label', 'author' => 'System', 'time' => null, 'tone' => 'event'])
<article class="bkb-cc-timeline-item is-{{ $tone }}">
    <i></i><div><header><strong>{{ $label }}</strong>@if($time)<time>{{ $time }}</time>@endif</header><span>{{ $author }}</span><div>{{ $slot }}</div></div>
</article>
