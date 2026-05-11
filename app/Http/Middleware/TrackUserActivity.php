<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Track user activity if authenticated
        if (auth()->check()) {
            $user = auth()->user();
            
            // Only update if last activity was more than 5 minutes ago
            if ($user->last_activity_at === null || $user->last_activity_at->addMinutes(5)->isPast()) {
                $user->updateLastActivity();
            }
        }

        return $next($request);
    }
}
