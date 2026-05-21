<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class EnsureUserHasValidRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user has no role, assign customer role
            if (!$user->id_role) {
                $customerRole = Role::where('role_name', 'customer')->first();
                $user->update([
                    'id_role' => $customerRole ? $customerRole->id_role : 2,
                ]);
            }
            
            // Ensure is_guest is set to false for authenticated users
            if ($user->is_guest === null || $user->is_guest === true) {
                $user->update(['is_guest' => false]);
            }
        }
        
        return $next($request);
    }
}
