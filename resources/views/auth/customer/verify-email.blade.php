@push('styles')
    @vite(['resources/css/auth/verify-email.css'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

<x-guest-layout>
    <!-- Verify Email Heading -->
    <div style="margin-bottom: 2rem;">
        <p class="verify-subheading">PLEASE VERIFY</p>
        <h1 class="verify-heading">YOUR EMAIL</h1>
    </div>

    <!-- Icon -->
    <div class="verify-icon-container">
        <div class="verify-icon-circle">
            <i class="bi bi-envelope-check"></i>
        </div>
    </div>

    <!-- Status Message -->
    @if (session('status') == 'verification-link-sent')
        <div class="status-message">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ __('A new verification link has been sent to your email.') }}
        </div>
    @endif

    <!-- Description Text -->
    <div class="verify-text">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    <div class="action-container">
        <form method="POST" action="{{ route('customer.verification.send') }}">
            @csrf
            <button type="submit" class="btn-orange">
                {{ __('Resend Verification Email') }}
            </button>
        </form>
    </div>

    <!-- Logout Link -->
    <div id="logout-link-content" class="logout-link">
        <form method="POST" action="{{ route('customer.logout') }}">
            @csrf
            <span>Wrong email?</span>
            <button type="submit" class="link-orange" style="background: none; border: none; padding: 0; font: inherit; cursor: pointer;">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    <script>
        // Move logout link to bottom section
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.getElementById('logout-link-content');
            const bottomSection = document.getElementById('bottom-section');
            if (logoutLink && bottomSection) {
                bottomSection.appendChild(logoutLink);
            }
        });
    </script>
</x-guest-layout>
