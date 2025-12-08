<nav class="navbar navbar-expand-lg navbar-light bg-body border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold text-primary" href="@if(Auth::guard('tukang')->check()) {{ route('tukang.dashboard') }} @elseif(Auth::guard('customer')->check()) {{ route('dashboard') }} @else {{ route('dashboard') }} @endif">
            <i class="bi bi-tools me-2"></i>Come&Fix
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @if(Auth::guard('tukang')->check())
                    <!-- Tukang Navigation -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tukang.dashboard') ? 'active' : '' }}" href="{{ route('tukang.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tukang.jobs.*') ? 'active' : '' }}" href="{{ route('tukang.jobs.index') }}">
                            <i class="bi bi-briefcase me-1"></i>Active Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tukang.chatrooms.*') ? 'active' : '' }}" href="{{ route('tukang.chatrooms.index') }}">
                            <i class="bi bi-chat-dots me-1"></i>Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tukang.finance.*') ? 'active' : '' }}" href="{{ route('tukang.finance.index') }}">
                            <i class="bi bi-wallet2 me-1"></i>Finance
                        </a>
                    </li>
                @elseif(Auth::guard('customer')->check())
                    <!-- Customer Navigation -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-house me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                            <i class="bi bi-briefcase me-1"></i>My Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
                            <i class="bi bi-chat-dots me-1"></i>Messages
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Right side items -->
            <div class="d-flex align-items-center">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="btn btn-outline-secondary btn-sm me-3" title="Toggle Dark/Light Mode">
                    <i id="themeIcon" class="bi bi-moon-fill"></i>
                </button>

                <!-- Notifications -->
                <div class="dropdown me-3">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            3
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <!-- Notification items... -->
                    </ul>
                </div>

                <!-- User menu -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold;">
                            @if(Auth::guard('tukang')->check())
                                {{ substr(Auth::guard('tukang')->user()->name, 0, 1) }}
                            @elseif(Auth::guard('customer')->check())
                                {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>
                        <span class="d-none d-lg-inline">
                            @if(Auth::guard('tukang')->check())
                                {{ Auth::guard('tukang')->user()->name }}
                            @elseif(Auth::guard('customer')->check())
                                {{ Auth::guard('customer')->user()->name }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="@if(Auth::guard('tukang')->check()) {{ route('tukang.profile') }} @else {{ route('profile.show') }} @endif">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        {{--                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>--}}
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
            </div>
        </div>
    </div>
</nav>
