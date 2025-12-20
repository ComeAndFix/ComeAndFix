<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = null;
        $dashboard = 'dashboard';

        if ($request->user('customer')) {
            $user = $request->user('customer');
            $dashboard = 'dashboard';
        } elseif ($request->user('tukang')) {
            $user = $request->user('tukang');
            $dashboard = 'tukang.dashboard';
        } else {
            $user = $request->user();
        }

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->intended(route($dashboard, absolute: false));
        }

        if ($user) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('status', 'verification-link-sent');
    }
}
