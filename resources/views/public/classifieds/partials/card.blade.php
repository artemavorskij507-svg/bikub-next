<article class="mk-ad-card">
    <a href="{{ route('public.classifieds.show', $listing) }}" class="mk-ad-image" style="background-image:url('{{ $listing->image_path ? asset($listing->image_path) : asset('images/bikube/home/scenario-classifieds.png') }}')">
        @if($listing->is_featured)
            <span>{{ __('bikube.classifieds.market.featured_badge') }}</span>
        @endif
    </a>
    <div class="mk-ad-body">
        <a href="{{ route('public.classifieds.show', $listing) }}" class="mk-ad-title">{{ $listing->title }}</a>
        <strong class="mk-ad-price">{{ $listing->formattedPrice() }}</strong>
        <div class="mk-ad-meta">
            <span>{{ $listing->location }}</span>
            <span>{{ $listing->category ? (trans()->has("bikube.classifieds.categories.{$listing->category->slug}") ? __("bikube.classifieds.categories.{$listing->category->slug}") : $listing->category->name) : __('bikube.classifieds.market.no_category') }}</span>
        </div>
    </div>
</article>
