<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirectIfAuthenticated
{
    /**
     * Handle an incoming request for guest-only routes.
     * If admin is already authenticated, redirect to dashboard.
     * Otherwise, allow request to proceed (show login page).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin-cms')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}