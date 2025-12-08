<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Welcome Text and Heading -->
    <div style="margin-bottom: 2.5rem;">
        <p class="welcome-text">WELCOME BACK!</p>
        <h1 class="login-heading">LOGIN</h1>
    </div>

    <form method="POST" action="{{ route('customer.login') }}">
        @csrf

        <!-- Email Address -->
        <div class="input-wrapper">
            <input 
                id="email" 
                class="custom-input" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="Email address"
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="input-wrapper">
            <input 
                id="password" 
                class="custom-input" 
                type="password" 
                name="password" 
                placeholder="Password"
                required 
                autocomplete="current-password" 
            />
            <button 
                type="button" 
                class="password-toggle" 
                onclick="togglePassword()"
                aria-label="Toggle password visibility"
            >
                <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="remember-forgot-row">
            <label for="remember_me" class="remember-me-label">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="custom-checkbox" 
                    name="remember"
                >
                <span>Remember Me</span>
            </label>
            <a href="#" onclick="alert('Password reset functionality coming soon!'); return false;" class="link-orange">
                Forgot password?
            </a>
        </div>

        <!-- Login Button -->
        <button type="submit" class="btn-orange">
            Login
        </button>
    </form>

    <!-- Register Link (will be moved to bottom section) -->
    <div id="register-link-content" class="register-link">
        Don't have an account? 
        <a href="{{ route('customer.register') }}" class="link-orange">Register</a>
    </div>

    <script>
        // Move register link to bottom section
        document.addEventListener('DOMContentLoaded', function() {
            const registerLink = document.getElementById('register-link-content');
            const bottomSection = document.getElementById('bottom-section');
            if (registerLink && bottomSection) {
                bottomSection.appendChild(registerLink);
            }
        });

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
</x-guest-layout>
