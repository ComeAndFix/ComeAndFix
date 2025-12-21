<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tukang;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class TukangEmailVerificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $tukang = Tukang::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('id'), (string) $tukang->getKey())) {
            abort(403);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($tukang->getEmailForVerification()))) {
            abort(403);
        }

        if ($tukang->hasVerifiedEmail()) {
            return redirect()->route('tukang.login')->with('success', 'Email already verified!');
        }

        if ($tukang->markEmailAsVerified()) {
            event(new Verified($tukang));
        }

        return redirect()->route('tukang.dashboard')->with('success', 'Email verified successfully!');
    }
}
