<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminOperator
{
    public function handle(Request $request, Closure $next)
    {
        abort_if($request->user()?->workerProfile?->status === 'approved', 403, 'Worker accounts cannot access Admin Operations data.');
        return $next($request);
    }
}
