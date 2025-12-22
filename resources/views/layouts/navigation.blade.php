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

    <!-- Profile Icon -->
    <div class="dropdown">
        <a href="#" class="nav-profile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person nav-profile-icon"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="@if(Auth::guard('tukang')->check()) {{ route('tukang.profile') }} @else {{ route('profile.show') }} @endif">
                    <i class="bi bi-person me-2"></i>Profile
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                @if(Auth::guard('customer')->check())
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                @elseif(Auth::guard('tukang')->check())
                    <form method="POST" action="{{ route('tukang.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                @endif
            </li>
        </ul>
    </div>
</nav>
