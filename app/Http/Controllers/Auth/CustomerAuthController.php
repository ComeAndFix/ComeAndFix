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
                'address' => ['nullable', 'string'],
                'city' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['nullable', 'string', 'max:10'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'password' => Hash::make($request->password),
            ]);

            $customer->sendEmailVerificationNotification();

            return redirect()->route('customer.login')->with('success', 'Registration successful! Please check your email to verify your account before logging in.');
        }

       public function login(Request $request)
       {
           $request->validate([
               'email' => 'required|email',
               'password' => 'required',
           ]);

           $credentials = $request->only('email', 'password');

           if (Auth::guard('customer')->attempt($credentials)) {
               $customer = Auth::guard('customer')->user();

               // Check if email is verified
               if (!$customer->hasVerifiedEmail()) {
                   Auth::guard('customer')->logout();
                   return back()->withErrors([
                       'email' => 'Please verify your email address before logging in.',
                   ])->onlyInput('email');
               }

               $request->session()->regenerate();
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
