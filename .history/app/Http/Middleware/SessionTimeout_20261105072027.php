<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            // 15 minutes = 900 seconds
            $timeout = 15 * 60;

            // Get last user activity
            $lastActivity = $request->session()->get('last_activity');

            // Check whether session has expired
            if ($lastActivity && (time() - $lastActivity) > $timeout) {

                Auth::logout();

                // Destroy old session
                $request->session()->invalidate();

                // Regenerate CSRF token
                $request->session()->regenerateToken();

                return redirect()
                    ->route('staff.login.form')
                    ->with('status', 'Session expired. Please log in again.');
            }

            // Update last activity
            $request->session()->put('last_activity', time());
        }

        return $next($request);
    }
}