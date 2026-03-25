<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, return null to trigger JSON response
        if ($request->is('api/*') || $request->is('api/admin/*')) {
            return null;
        }

        // For admin routes, redirect to admin login
        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        // For other web requests
        return null;
    }
}
