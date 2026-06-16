<?php

namespace App\Http\Middleware;

use App\Services\Localization\AdminUiTranslator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslateAdminUi
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $isAdminRequest = $request->is('admin') || $request->is('admin/*');
        $isAdminLivewireRequest = $request->is('livewire-*/update')
            && str_contains((string) $request->header('referer'), '/admin');

        if ((! $isAdminRequest && ! $isAdminLivewireRequest) || ! $response instanceof Response) {
            return $response;
        }

        return app(AdminUiTranslator::class)->translateResponse($response);
    }
}
