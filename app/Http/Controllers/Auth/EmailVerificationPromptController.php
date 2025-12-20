<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user('customer')) {
            return $request->user('customer')->hasVerifiedEmail()
                        ? redirect()->intended(route('dashboard'))
                        : view('auth.customer.verify-email');
        }

        if ($request->user('tukang')) {
            return $request->user('tukang')->hasVerifiedEmail()
                        ? redirect()->intended(route('tukang.dashboard'))
                        : view('auth.tukang.verify-email');
        }

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
