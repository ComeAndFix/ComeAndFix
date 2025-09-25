<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on the guard type
                if ($guard === 'customer') {
                    return redirect()->route('dashboard');
                } elseif ($guard === 'tukang') {
                    return redirect()->route('tukang.dashboard');
                }

                // Default redirect
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
