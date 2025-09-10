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
                'address' => ['nullable', 'string'],
                'city' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['nullable', 'string', 'max:10'],
                'specializations' => ['required', 'array', 'min:1'],
                'years_experience' => ['nullable', 'integer', 'min:0'],
                'hourly_rate' => ['nullable', 'numeric', 'min:0'],
                'description' => ['nullable', 'string'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $tukang = Tukang::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'specializations' => $request->specializations,
                'years_experience' => $request->years_experience ?? 0,
                'hourly_rate' => $request->hourly_rate,
                'description' => $request->description,
                'password' => Hash::make($request->password),
            ]);

            $tukang->sendEmailVerificationNotification();

            return redirect()->route('tukang.login')->with('success', 'Registration successful! Please check your email to verify your account before logging in.');
        }

       public function login(Request $request)
       {
           $request->validate([
               'email' => ['required', 'email'],
               'password' => ['required'],
           ]);

           if (Auth::guard('tukang')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
               $user = Auth::guard('tukang')->user();

               if (!$user->hasVerifiedEmail()) {
                   Auth::guard('tukang')->logout();
                   return back()->withErrors([
                       'email' => 'Please verify your email address before logging in.',
                   ])->onlyInput('email');
               }

               $request->session()->regenerate();
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
