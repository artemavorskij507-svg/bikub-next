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

        if (! $request->is('admin/*') || ! $response instanceof Response) {
            return $response;
        }

        return app(AdminUiTranslator::class)->translateResponse($response);
    }
}
