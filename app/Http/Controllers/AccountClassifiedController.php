<?php

namespace App\Http\Controllers;

use App\Models\ClassifiedCategory;
use App\Models\ClassifiedListing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountClassifiedController extends Controller
{
    public function index(Request $request): View
    {
        $listings = ClassifiedListing::query()
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('account.classifieds.index', compact('listings'));
    }

    public function create(): View
    {
        return view('account.classifieds.create', [
            'categories' => ClassifiedCategory::active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'classified_category_id' => ['nullable', 'exists:classified_categories,id'],
            'title' => ['required', 'string', 'min:4', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:4000'],
            'price_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'condition' => ['nullable', 'string', 'max:80'],
            'location' => ['required', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
        ]);

        $listing = ClassifiedListing::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'listing_number' => $this->generateNumber(),
            'slug' => $this->generateSlug($validated['title']),
            'currency' => 'NOK',
            'status' => ClassifiedListing::STATUS_PENDING,
            'contact_name' => $validated['contact_name'] ?: $request->user()->name,
            'contact_email' => $validated['contact_email'] ?: $request->user()->email,
        ]);

        return redirect()
            ->route('account.classifieds.index')
            ->with('status', __('bikube.classifieds.account.created_pending', ['number' => $listing->listing_number]));
    }

    private function generateNumber(): string
    {
        do {
            $number = 'CLS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (ClassifiedListing::where('listing_number', $number)->exists());

        return $number;
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'classified';
        $slug = $base;
        $counter = 2;

        while (ClassifiedListing::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
