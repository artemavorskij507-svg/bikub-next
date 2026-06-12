@props(['title', 'body' => null])
<div class="bkb-cc-empty">
    <strong>{{ $title }}</strong>
    @if($body)<p>{{ $body }}</p>@endif
</div>
