<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApprovedWorker
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->workerProfile?->status !== 'approved') {
            return response()->view('worker.blocked', status: 403);
        }

        return $next($request);
    }
}
