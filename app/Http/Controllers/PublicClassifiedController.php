<?php

namespace App\Http\Controllers;

use App\Models\ClassifiedCategory;
use App\Models\ClassifiedListing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicClassifiedController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ClassifiedCategory::active()
            ->withCount(['listings' => fn ($query) => $query->visibleToPublic()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = ClassifiedListing::query()
            ->visibleToPublic()
            ->with('category')
            ->latest('published_at');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('category', fn ($query) => $query->where('slug', $category));
        }

        if ($location = trim((string) $request->query('location'))) {
            $query->where('location', 'like', "%{$location}%");
        }

        $featured = (clone $query)->where('is_featured', true)->limit(3)->get();
        $listings = $query->paginate(12)->withQueryString();

        return view('public.classifieds.index', compact('categories', 'featured', 'listings'));
    }

    public function show(ClassifiedListing $listing): View
    {
        abort_unless($listing->newQuery()->whereKey($listing->getKey())->visibleToPublic()->exists(), 404);

        $listing->load('category', 'owner');

        return view('public.classifieds.show', compact('listing'));
    }
}
