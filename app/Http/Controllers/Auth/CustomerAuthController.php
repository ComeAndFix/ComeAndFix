<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\Customer;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rules;
    use Illuminate\Support\Facades\Auth;

    class CustomerAuthController extends Controller
    {
        public function showLogin()
        {
            return view('auth.customer.login');
        }

        public function showRegister()
        {
            return view('auth.customer.register');
        }

        public function register(Request $request)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
                'phone' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Check if email verification is enabled
            if (config('app.email_verification_enabled', true)) {
                $customer->sendEmailVerificationNotification();
                Auth::guard('customer')->login($customer);
                return redirect()->route('customer.verification.notice');
            } else {
                // Auto-verify the customer if email verification is disabled
                $customer->markEmailAsVerified();
                Auth::guard('customer')->login($customer);
                return redirect()->route('dashboard');
            }
        }

        public function login(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::guard('customer')->attempt($credentials)) {
                $request->session()->regenerate();
                
                $customer = Auth::guard('customer')->user();
                
                // Only check email verification if it's enabled
                if (config('app.email_verification_enabled', true) && !$customer->hasVerifiedEmail()) {
                    return redirect()->route('customer.verification.notice');
                }

                return redirect()->route('dashboard');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        public function logout(Request $request)
        {
            Auth::guard('customer')->logout(); // or 'tukang' for TukangAuthController
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/');
        }
    }
