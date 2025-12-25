<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            // Convert string role to UserRole enum
            try {
                $requiredRole = UserRole::from($role);
                if ($user->hasRole($requiredRole)) {
                    return $next($request);
                }
            } catch (\ValueError $e) {
                // Invalid role value, continue to next role
                continue;
            }
        }

        // User doesn't have any required role
        abort(403, 'Unauthorized access. You do not have the required role.');
    }
}
