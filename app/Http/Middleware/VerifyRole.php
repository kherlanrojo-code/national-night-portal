<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
        public function handle(Request $request, Closure $next, string $role): Response
        {
            // 1. Ensure user is logged in
            if (!Auth::check()) {
                return redirect('/login')->with('error', 'Please login to access this page.');
            }

            // 2. Normalize both roles to lowercase to prevent "Admin" vs "admin" errors
            $userRole = strtolower(Auth::user()->role);
            $requiredRole = strtolower($role);

            // 3. Check if the user's role matches the required role
            if ($userRole !== $requiredRole) {
                return redirect('/login')->with('error', 'Unauthorized. ' . ucfirst($requiredRole) . ' access only.');
            }

            return $next($request);
        }
}