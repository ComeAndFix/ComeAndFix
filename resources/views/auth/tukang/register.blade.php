@push('styles')
    @vite(['resources/css/auth/register.css'])
@endpush

<x-guest-layout>
    <!-- Create Account Heading -->
    <div style="margin-bottom: 1.5rem;">
        <p class="register-subheading">CREATE AN</p>
        <h1 class="register-heading">ACCOUNT</h1>
    </div>

    <form method="POST" action="{{ route('tukang.register') }}">
        @csrf

        <!-- Full Name -->
        <div class="input-wrapper">
            <input 
                id="name" 
                class="custom-input" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                placeholder="Full name"
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email and Phone (Two Columns) -->
        <div class="form-row">
            <div class="input-wrapper">
                <input 
                    id="email" 
                    class="custom-input" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="Email"
                    required 
                    autocomplete="username" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="input-wrapper">
                <input 
                    id="phone" 
                    class="custom-input" 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    placeholder="Phone number"
                    required 
                />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <!-- Specializations -->
        <div class="input-wrapper">
            <label style="font-size: 0.875rem; color: var(--text-gray); margin-bottom: 0.75rem; display: block; font-family: 'Inter', sans-serif;">Specializations</label>
            <div class="specialization-pills">
                <div class="specialization-pill">
                    <input type="checkbox" name="specializations[]" value="Plumbing" id="spec_plumbing">
                    <label for="spec_plumbing">Plumbing</label>
                </div>
                <div class="specialization-pill">
                    <input type="checkbox" name="specializations[]" value="Electricity" id="spec_electricity">
                    <label for="spec_electricity">Electricity</label>
                </div>
                <div class="specialization-pill">
                    <input type="checkbox" name="specializations[]" value="HVAC/AC" id="spec_hvac">
                    <label for="spec_hvac">HVAC/AC</label>
                </div>
                <div class="specialization-pill">
                    <input type="checkbox" name="specializations[]" value="Painting" id="spec_painting">
                    <label for="spec_painting">Painting</label>
                </div>
                <div class="specialization-pill">
                    <input type="checkbox" name="specializations[]" value="Appliance Repair" id="spec_appliance_repair">
                    <label for="spec_appliance_repair">Appliance Repair</label>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="input-wrapper">
            <textarea 
                id="description" 
                class="custom-input" 
                name="description" 
                placeholder="Brief description about your services"
                rows="3"
                style="resize: vertical; min-height: 80px;"
            >{{ old('description') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <!-- Password -->
        <div style="margin-bottom: 1.25rem;">
            <div class="input-wrapper" style="margin-bottom: 0;">
                <input 
                    id="password" 
                    class="custom-input" 
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    required 
                    autocomplete="new-password" 
                />
                <button 
                    type="button" 
                    class="password-toggle" 
                    onclick="togglePassword('password', 'eye-icon-password')"
                    aria-label="Toggle password visibility"
                >
                    <svg id="eye-icon-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom: 1.25rem;">
            <div class="input-wrapper" style="margin-bottom: 0;">
                <input 
                    id="password_confirmation" 
                    class="custom-input" 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Confirm password"
                    required 
                    autocomplete="new-password" 
                />
                <button 
                    type="button" 
                    class="password-toggle" 
                    onclick="togglePassword('password_confirmation', 'eye-icon-confirm')"
                    aria-label="Toggle password confirmation visibility"
                >
                    <svg id="eye-icon-confirm" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Register Button -->
        <button type="submit" class="btn-orange">
            Register
        </button>
    </form>

    <!-- Login Link (will be moved to bottom section) -->
    <div id="login-link-content" class="login-link">
        Already have an account? 
        <a href="{{ route('tukang.login') }}" class="link-orange">Login</a>
    </div>

    <script>
        // Move login link to bottom section
        document.addEventListener('DOMContentLoaded', function() {
            const loginLink = document.getElementById('login-link-content');
            const bottomSection = document.getElementById('bottom-section');
            if (loginLink && bottomSection) {
                bottomSection.appendChild(loginLink);
            }
        });

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
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
