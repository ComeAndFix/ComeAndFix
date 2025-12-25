<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Tukang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rules;

    class TukangAuthController extends Controller
    {
        public function showLogin()
        {
            return view('auth.tukang.login');
        }

        public function showRegister()
        {
            return view('auth.tukang.register');
        }

        public function register(Request $request)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:tukangs'],
                'phone' => ['required', 'string', 'max:255'],
                // Removed address, city, postal_code, years_experience, hourly_rate validations
                'specializations' => ['required', 'array', 'min:1'],
                'description' => ['nullable', 'string'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $tukang = Tukang::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                // Location info removed from input, defaulting to null
                'address' => null,
                'city' => null,
                'postal_code' => null,
                'specializations' => $request->specializations,
                'years_experience' => 0, // Defaulting to 0/null as removed from input
                'description' => $request->description,
                'password' => Hash::make($request->password),
                // hourly_rate and business_name removed
            ]);

            // Check if email verification is enabled
            if (config('app.email_verification_enabled', true)) {
                $tukang->sendEmailVerificationNotification();
                Auth::guard('tukang')->login($tukang);
                return redirect()->route('tukang.verification.notice');
            } else {
                // Auto-verify the tukang if email verification is disabled
                $tukang->markEmailAsVerified();
                Auth::guard('tukang')->login($tukang);
                return redirect()->route('tukang.dashboard');
            }
        }

        public function login(Request $request)
        {
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::guard('tukang')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
                $request->session()->regenerate();

                $user = Auth::guard('tukang')->user();
                
                // Only check email verification if it's enabled
                if (config('app.email_verification_enabled', true) && !$user->hasVerifiedEmail()) {
                    return redirect()->route('tukang.verification.notice');
                }

                return redirect()->intended(route('tukang.dashboard'));
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        public function logout(Request $request)
        {
            Auth::guard('tukang')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/');
        }
    }
