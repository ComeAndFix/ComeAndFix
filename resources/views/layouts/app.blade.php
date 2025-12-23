<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    @vite([
        'resources/css/app.css',
        'resources/css/components/navigation.css',
        'resources/css/components/location-modal.css',
        'resources/css/components/order-details.css',
        'resources/css/components/order-list.css',
        'resources/js/app.js'
    ])
    
    @stack('styles')

    <!-- Custom Dark Mode CSS -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #121212;
            --bs-body-color: #ffffff;
            --bs-border-color: #343a40;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #1e1e1e !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }

        [data-bs-theme="dark"] .border-bottom {
            border-color: #343a40 !important;
        }

        [data-bs-theme="dark"] .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(255, 255, 255, .075) !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #2d2d2d;
            border-color: #343a40;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #343a40;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #343a40;
        }

        .navbar-nav .nav-link {
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            transform: translateY(-1px);
        }

        #themeToggle, #altThemeToggle {
            transition: all 0.3s ease;
            display: inline-block !important;
            visibility: visible !important;
            border-radius: 4px !important;
        }

        #themeToggle:hover, #altThemeToggle:hover {
            transform: scale(1.1);
        }

        /* Ensure navigation items are visible */
        .navbar-nav {
            display: flex !important;
        }

        .navbar-nav .nav-item {
            display: block !important;
        }

        .navbar-nav .nav-link {
            display: block !important;
            color: var(--bs-nav-link-color) !important;
        }
    </style>
</head>
<body>
@include('layouts.navigation')

<!-- Page Heading -->
@isset($header)
    <header class="bg-body-secondary shadow-sm border-bottom">
        <div class="container py-3">
            {{ $header }}
        </div>
    </header>
@endisset

<!-- Page Content -->
<main>
    {{ $slot }}
</main>

<!-- Location Setup Modal for Customers -->
@include('components.location-setup-modal')

<!-- Onboarding Modal for Tukang -->
@include('components.tukang-onboarding-modal')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Theme Toggle and Navigation Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;

        if (themeToggle && themeIcon) {
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                htmlElement.setAttribute('data-bs-theme', savedTheme);
                updateIcon(savedTheme);
            } else {
                // Default to light theme
                htmlElement.setAttribute('data-bs-theme', 'light');
                updateIcon('light');
            }

            themeToggle.addEventListener('click', function() {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';

                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });

            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill';
                } else {
                    themeIcon.className = 'bi bi-moon-fill';
                }
            }
        } else {
            console.log('Theme toggle elements not found');
        }

        // Smooth scrolling for navbar links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
</body>
</html>
