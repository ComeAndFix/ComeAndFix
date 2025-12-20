<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerEmailVerificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $customer = Customer::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('id'), (string) $customer->getKey())) {
            abort(403);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($customer->getEmailForVerification()))) {
            abort(403);
        }

        if ($customer->hasVerifiedEmail()) {
            return redirect()->route('customer.login')->with('success', 'Email already verified!');
        }

        if ($customer->markEmailAsVerified()) {
            event(new Verified($customer));
        }

        return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
    }
}
