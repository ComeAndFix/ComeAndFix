<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Come&Fix') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite([
        'resources/css/app.css',
        'resources/css/components/buttons.css',
        'resources/css/components/forms.css',
        'resources/css/auth/login.css',
        'resources/js/app.js'
    ])
    
    <!-- Page-specific styles -->
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Workshop Image -->
        <div class="login-image-section" style="background-image: url('{{ asset('images/workshop-tools.png') }}');">
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-section">
            <div style="width: 100%; max-width: 480px; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                <!-- Top Bar: Logo and Tukang Link -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Brand Logo -->
                    <a href="/" class="brand-logo">
                        <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix Logo" style="width: 40px; height: 40px;">
                        <span>Come&Fix</span>
                    </a>
                    
                    <!-- Tukang/Customer Link -->
                    <div class="auth-top-nav" style="display: flex; align-items: center; gap: 0.5rem;">
                        @if(request()->routeIs('tukang.*'))
                            <span style="color: #666666; font-size: 0.875rem; font-family: 'Inter', sans-serif;">Are you a Customer?</span>
                            <a href="{{ route('customer.login') }}" class="link-orange">Customer Page</a>
                        @else
                            <span style="color: #666666; font-size: 0.875rem; font-family: 'Inter', sans-serif;">Are you a Tukang?</span>
                            <a href="{{ route('tukang.login') }}" class="link-orange">Tukang Page</a>
                        @endif
                    </div>
                </div>

                <!-- Middle Content Slot -->
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 2rem 0;">
                    {{ $slot }}
                </div>

                <!-- Bottom: Register Link (will be moved here from login.blade.php) -->
                <div id="bottom-section">
                    <!-- This will be filled by the login page -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
