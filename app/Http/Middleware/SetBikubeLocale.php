<?php
namespace App\Http\Middleware;use App\Services\Localization\LocaleManager;use Closure;use Illuminate\Http\Request;
class SetBikubeLocale{public function handle(Request $request,Closure $next){$m=app(LocaleManager::class);$locale=$request->session()->get('bikube_locale',$m->defaultLocale());$m->setLocale($locale);return $next($request);}}
