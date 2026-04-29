<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has admin role
        // This is a simple implementation - adjust based on your role system
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
