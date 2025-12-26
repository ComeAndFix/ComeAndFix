<nav class="main-nav">
    <!-- Logo -->
    <a href="@if(Auth::guard('tukang')->check()) {{ route('tukang.dashboard') }} @elseif(Auth::guard('customer')->check()) {{ route('dashboard') }} @else {{ route('dashboard') }} @endif" class="nav-logo">
        <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix Logo" style="width: 40px; height: 40px;">
        <span>Come&Fix</span>
    </a>

    <!-- Navigation Menu -->
    @if(Auth::guard('customer')->check())
        <ul class="nav-menu">
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
        </ul>
    @elseif(Auth::guard('tukang')->check())
        <ul class="nav-menu">
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
        </ul>
    @endif

    <!-- Profile Section -->
    <div class="nav-profile-section">
        @if(Auth::guard('customer')->check())
            <div class="nav-user-details">
                <a href="{{ route('profile.show') }}" class="nav-user-name">
                    {{ Auth::guard('customer')->user()->name }}
                </a>
                <form method="POST" action="{{ route('customer.logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="nav-logout-btn">
                        Logout
                    </button>
                </form>
            </div>
            <a href="{{ route('profile.show') }}">
                @if(Auth::guard('customer')->user()->profile_image)
                    <img src="{{ Auth::guard('customer')->user()->profile_image }}" alt="Profile" class="nav-user-avatar">
                @else
                    <div class="nav-user-avatar-placeholder">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
            </a>
        @elseif(Auth::guard('tukang')->check())
            <div class="nav-user-details">
                <a href="{{ route('tukang.profile.show') }}" class="nav-user-name">
                    {{ Auth::guard('tukang')->user()->name }}
                </a>
                <form method="POST" action="{{ route('tukang.logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="nav-logout-btn">
                        Logout
                    </button>
                </form>
            </div>
            <a href="{{ route('tukang.profile.show') }}">
                @if(Auth::guard('tukang')->user()->profile_image)
                    <img src="{{ Auth::guard('tukang')->user()->profile_image }}" alt="Profile" class="nav-user-avatar">
                @else
                    <div class="nav-user-avatar-placeholder">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
            </a>
        @endif
    </div>
</nav>
