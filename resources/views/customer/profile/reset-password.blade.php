<x-app-layout>
    @vite(['resources/css/customer/profile.css'])

    <div class="profile-page-wrapper">
        <div class="profile-container">
            <h1 class="page-title">
                <i class="bi bi-shield-lock-fill text-brand-orange"></i>
                Reset Password
            </h1>

            <div class="password-reset-container" style="padding-top: 0;">
                <div class="password-reset-card">
                    <h2 class="password-reset-title">Change Your Password</h2>
                    <p class="password-reset-subtitle">Enter your current password and choose a new one to keep your account secure</p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle alert-icon"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle alert-icon"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="password-field-group">
                            <label class="field-label">Current Password</label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="current_password" 
                                    id="current_password"
                                    class="field-value editable @error('current_password') is-invalid @enderror" 
                                    placeholder="Enter your current password"
                                    required
                                >
                                <i class="bi bi-eye password-toggle-icon" onclick="togglePassword('current_password')"></i>
                            </div>
                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="password-field-group">
                            <label class="field-label">New Password</label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    class="field-value editable @error('password') is-invalid @enderror" 
                                    placeholder="Enter your new password"
                                    required
                                >
                                <i class="bi bi-eye password-toggle-icon" onclick="togglePassword('password')"></i>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted" style="font-size: 0.85rem; display: block; margin-top: 0.5rem;">
                                Password must be at least 8 characters long
                            </small>
                        </div>

                        <div class="password-field-group">
                            <label class="field-label">Confirm New Password</label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation"
                                    class="field-value editable @error('password_confirmation') is-invalid @enderror" 
                                    placeholder="Confirm your new password"
                                    required
                                >
                                <i class="bi bi-eye password-toggle-icon" onclick="togglePassword('password_confirmation')"></i>
                            </div>
                            @error('password_confirmation')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="submit-password-btn">
                            <i class="bi bi-key me-2"></i>
                            Update Password
                        </button>
                    </form>

                    <a href="{{ route('profile.show') }}" class="back-to-profile-link">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</x-app-layout>
