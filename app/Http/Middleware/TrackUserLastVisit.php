<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserLastVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Only update if last visit was more than 1 hour ago or is null
            if (!$user->last_visit_at || now()->diffInMinutes($user->last_visit_at) >= 60) {
                $user->updateLastVisit();
            }
        }

        return $next($request);
    }
}
