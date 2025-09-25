<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check for tukang routes more broadly
        if ($request->is('*/tukang')) {
            return route('tukang.login');
        }

        return route('customer.login');
    }
}
