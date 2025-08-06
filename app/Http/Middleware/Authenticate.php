<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import the Str class

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // If the request path starts with 'tukang/', redirect to the tukang login route.
        if ($request->is('tukang/*')) {
            return route('tukang.login');
        }

        // If the request path starts with 'customer/', redirect to the customer login route.
        if ($request->is('customer/*')) {
            return route('customer.login');
        }

        // Otherwise, redirect to the default login route.
        return route('login');
    }
}
