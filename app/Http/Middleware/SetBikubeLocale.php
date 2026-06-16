<?php

namespace App\Http\Middleware;

use App\Services\Localization\LocaleManager;
use Closure;
use Illuminate\Http\Request;

class SetBikubeLocale
{
    public function handle(Request $request, Closure $next)
    {
        $manager = app(LocaleManager::class);
        $candidate = $request->query('locale')
            ?? $request->route('locale')
            ?? $request->session()->get('bikube_locale')
            ?? $request->session()->get('locale')
            ?? $request->user()?->preferred_locale
            ?? $request->user()?->locale
            ?? $manager->defaultLocale();

        $locale = is_string($candidate) && $manager->isSupported($candidate)
            ? $candidate
            : $manager->defaultLocale();

        $manager->setLocale($locale);

        if ($request->hasSession()) {
            $request->session()->put('bikube_locale', $locale);
        }

        return $next($request);
    }
}
