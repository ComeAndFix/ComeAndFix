<nav class="main-nav">
    <!-- Logo -->
    <a href="@if(Auth::guard('tukang')->check()) {{ route('tukang.dashboard') }} @elseif(Auth::guard('customer')->check()) {{ route('dashboard') }} @else {{ route('dashboard') }} @endif" class="nav-logo">
        <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix Logo" style="width: 40px; height: 40px;">
        <span>Come&Fix</span>
    </a>

    <!-- Navigation Menu -->
    @if(Auth::guard('customer')->check())
        <ul class="nav-menu" id="nav-menu">
            <!-- Close Button (inside menu) -->
            <li class="mobile-menu-close">
                <button class="mobile-close-btn" id="mobile-close-btn" aria-label="Close menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <span>Close</span>
                </button>
            </li>

            <!-- Mobile Profile Section (inside menu) -->
            <li class="mobile-profile-section">
                <a href="{{ route('profile.show') }}" class="mobile-profile-link">
                    @if(Auth::guard('customer')->user()->profile_image_url)
                        <img src="{{ Auth::guard('customer')->user()->profile_image_url }}" alt="Profile" class="mobile-profile-avatar">
                    @else
                        <div class="mobile-profile-avatar-placeholder">
                            {{ Auth::guard('customer')->user()->initials }}
                        </div>
                    @endif
                    <div class="mobile-profile-info">
                        <span class="mobile-profile-name">{{ Auth::guard('customer')->user()->name }}</span>
                        <span class="mobile-profile-role">Tap to view profile</span>
                    </div>
                </a>
            </li>

            <!-- Navigation Items -->
            <li>
                <a href="{{ route('dashboard') }}#services" class="nav-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Services
                </a>
            </li>
            <li>
                <a href="{{ route('customer.orders.index') }}" class="nav-menu-item {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                    My Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('chat.index') }}" class="nav-menu-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    Messages
                </a>
            </li>

            <!-- Mobile Logout -->
            <li class="mobile-logout-section">
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit" class="mobile-logout-btn">
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    @elseif(Auth::guard('tukang')->check())
        <ul class="nav-menu" id="nav-menu">
            <!-- Close Button (inside menu) -->
            <li class="mobile-menu-close">
                <button class="mobile-close-btn" id="mobile-close-btn" aria-label="Close menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <span>Close</span>
                </button>
            </li>

            <!-- Mobile Profile Section (inside menu) -->
            <li class="mobile-profile-section">
                <a href="{{ route('tukang.profile.show') }}" class="mobile-profile-link">
                    @if(Auth::guard('tukang')->user()->profile_image_url)
                        <img src="{{ Auth::guard('tukang')->user()->profile_image_url }}" alt="Profile" class="mobile-profile-avatar">
                    @else
                        <div class="mobile-profile-avatar-placeholder">
                            {{ Auth::guard('tukang')->user()->initials }}
                        </div>
                    @endif
                    <div class="mobile-profile-info">
                        <span class="mobile-profile-name">{{ Auth::guard('tukang')->user()->name }}</span>
                        <span class="mobile-profile-role">Tap to view profile</span>
                    </div>
                </a>
            </li>

            <!-- Navigation Items -->
            <li>
                <a href="{{ route('tukang.dashboard') }}" class="nav-menu-item {{ request()->routeIs('tukang.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('tukang.jobs.index') }}" class="nav-menu-item {{ request()->routeIs('tukang.jobs.*') ? 'active' : '' }}">
                    Active Jobs
                </a>
            </li>
            <li>
                <a href="{{ route('tukang.chatrooms.index') }}" class="nav-menu-item {{ request()->routeIs('tukang.chatrooms.*') ? 'active' : '' }}">
                    Messages
                </a>
            </li>
            <li>
                <a href="{{ route('tukang.finance.index') }}" class="nav-menu-item {{ request()->routeIs('tukang.finance.*') ? 'active' : '' }}">
                    Finance
                </a>
            </li>

            <!-- Mobile Logout -->
            <li class="mobile-logout-section">
                <form method="POST" action="{{ route('tukang.logout') }}">
                    @csrf
                    <button type="submit" class="mobile-logout-btn">
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    @endif

    <!-- Profile Section (Desktop Only) -->
    <div class="nav-profile-section">
        @if(Auth::guard('customer')->check())
            <div class="nav-user-details">
                <a href="{{ route('profile.show') }}" class="nav-user-name">
                    {{ \Illuminate\Support\Str::words(Auth::guard('customer')->user()->name, 2, '...') }}
                </a>
                <form method="POST" action="{{ route('customer.logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="nav-logout-btn">
                        Logout
                    </button>
                </form>
            </div>
            <a href="{{ route('profile.show') }}">
                @if(Auth::guard('customer')->user()->profile_image_url)
                    <img src="{{ Auth::guard('customer')->user()->profile_image_url }}" alt="Profile" class="nav-user-avatar">
                @else
                    <div class="nav-user-avatar-placeholder" style="display: flex; align-items: center; justify-content: center; background-color: var(--brand-orange); color: white; border-radius: 50%; width: 40px; height: 40px; font-weight: 600;">
                        {{ Auth::guard('customer')->user()->initials }}
                    </div>
                @endif
            </a>
        @elseif(Auth::guard('tukang')->check())
            <div class="nav-user-details">
                <a href="{{ route('tukang.profile.show') }}" class="nav-user-name">
                    {{ \Illuminate\Support\Str::words(Auth::guard('tukang')->user()->name, 2, '...') }}
                </a>
                <form method="POST" action="{{ route('tukang.logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="nav-logout-btn">
                        Logout
                    </button>
                </form>
            </div>
            <a href="{{ route('tukang.profile.show') }}">
                @if(Auth::guard('tukang')->user()->profile_image_url)
                    <img src="{{ Auth::guard('tukang')->user()->profile_image_url }}" alt="Profile" class="nav-user-avatar">
                @else
                    <div class="nav-user-avatar-placeholder" style="display: flex; align-items: center; justify-content: center; background-color: var(--brand-orange); color: white; border-radius: 50%; width: 40px; height: 40px; font-weight: 600;">
                        {{ Auth::guard('tukang')->user()->initials }}
                    </div>
                @endif
            </a>
        @endif
    </div>

    <!-- Hamburger Button (Mobile Only) - Moved to the right -->
    <button class="hamburger-btn" id="hamburger-btn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
</nav>

<script>
    // Hamburger menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const navMenu = document.getElementById('nav-menu');
        const overlay = document.getElementById('mobile-menu-overlay');
        const closeBtn = document.getElementById('mobile-close-btn');

        if (hamburgerBtn && navMenu && overlay) {
            hamburgerBtn.addEventListener('click', function() {
                hamburgerBtn.classList.toggle('active');
                navMenu.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
            });

            // Close menu when clicking overlay
            overlay.addEventListener('click', function() {
                hamburgerBtn.classList.remove('active');
                navMenu.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });

            // Close menu when clicking close button
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // Close menu when clicking a menu item (except profile link)
            const menuItems = navMenu.querySelectorAll('.nav-menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });
        }
    });
</script>
