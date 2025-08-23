<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Come&Fix') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <!-- Welcome Section (Left Side) -->
        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center text-white p-5 position-relative"
             style="background-image: url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=2969&auto=format&fit=crop'); background-size: cover; background-position: center;">

            <!-- Overlay -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(13, 110, 253, 0.2);"></div>

            <!-- Content -->
            <div class="position-relative">
                <div class="text-center">
                    <a class="navbar-brand fw-bold text-white fs-1 mb-4 d-inline-block" href="/">
                        <i class="bi bi-tools me-2"></i>Come&Fix
                    </a>
                    <h1 class="fw-bold mb-3">Welcome Back!</h1>
                    <p class="lead" style="max-width: 400px;">
                        Your one-stop solution for finding reliable handy-persons. Let's get things fixed.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Section (Right Side) -->
        <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center p-4" style="background-color: #f8f9fa;">
            <div class="card shadow-sm border-0" style="width: 100%; max-width: 450px;">
                <div class="card-body p-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
