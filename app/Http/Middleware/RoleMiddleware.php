<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        $userRole = strtolower(session('user_role'));

        foreach ($roles as $role) {
            $target = strtolower($role);
            
            if ($userRole === $target) {
                return $next($request);
            }
            
            // Authors, Both, and Admins can access author-level routes
            if ($target === 'author' && ($userRole === 'author' || $userRole === 'both' || $userRole === 'admin')) {
                return $next($request);
            }
            
            // Only Admins can access admin routes
            if ($target === 'admin' && $userRole === 'admin') {
                return $next($request);
            }
        }

        return redirect('/home')->with('error', 'You do not have authorization to view that section.');
    }
}
