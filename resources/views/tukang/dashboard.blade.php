<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Dashboard Header -->
        <section class="bg-primary text-white py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="h3 fw-bold mb-2">
                            <i class="bi bi-tools me-2"></i>
                            Handyman Dashboard
                        </h1>
                        <p class="mb-0">Welcome back, {{ Auth::user()->name }}! Here's your job overview for today.</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <div class="badge bg-success fs-6 me-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Available
                        </div>
                        <button class="btn btn-outline-light btn-sm">
                            <i class="bi bi-gear me-1"></i>
                            Settings
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Job Overview Cards -->
        <section class="py-4 bg-light border-bottom">
            <div class="container">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-clock-history display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">0</h3>
                                <p class="mb-0 small">New Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-play-circle display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">0</h3>
                                <p class="mb-0 small">Active Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">0</h3>
                                <p class="mb-0 small">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-currency-dollar display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">Rp 0</h3>
                                <p class="mb-0 small">This Month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-4">
            <div class="container">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Incoming Jobs -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="bi bi-inbox text-warning me-2"></i>
                                        Incoming Job Requests
                                    </h5>
                                    <span class="badge bg-warning">0 New</span>
                                </div>
                            </div>
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                <p class="text-muted">No incoming job requests at the moment.</p>
                            </div>
                        </div>

                        <!-- Active Jobs -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-play-circle text-info me-2"></i>
                                    My Active Jobs
                                </h5>
                            </div>
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-play-circle display-4 text-muted mb-3"></i>
                                <p class="text-muted">No active jobs at the moment.</p>
                            </div>
                        </div>

                        <!-- Recent Completed Jobs -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Recent Completed Jobs
                                    </h5>
                                    <a href="#" class="btn btn-outline-primary btn-sm">View All</a>
                                </div>
                            </div>
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-check-circle display-4 text-muted mb-3"></i>
                                <p class="text-muted">No completed jobs yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Profile Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    My Profile
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="https://via.placeholder.com/80x80/007bff/ffffff?text=T" alt="Profile" class="rounded-circle mb-3" style="width: 80px; height: 80px;">
                                <h6 class="fw-bold mb-1">{{ Auth::user()->name }}</h6>
                                <p class="text-muted small mb-2">Professional Handyman</p>
                                <div class="text-warning mb-3">
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <span class="text-muted ms-1">0.0 (0 reviews)</span>
                                </div>
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <h6 class="mb-0">0</h6>
                                        <small class="text-muted">Jobs</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="mb-0">0%</h6>
                                        <small class="text-muted">Success</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="mb-0">0Y</h6>
                                        <small class="text-muted">Experience</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm w-100">Edit Profile</button>
                            </div>
                        </div>

                        <!-- Earnings Overview -->
{{--                        <div class="card border-0 shadow-sm mb-4">--}}
{{--                            <div class="card-header bg-white border-bottom">--}}
{{--                                <h5 class="mb-0 fw-bold">--}}
{{--                                    <i class="bi bi-graph-up text-success me-2"></i>--}}
{{--                                    Earnings Overview--}}
{{--                                </h5>--}}
{{--                            </div>--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="row text-center mb-3">--}}
{{--                                    <div class="col-6">--}}
{{--                                        <h5 class="text-success fw-bold mb-1">Rp 0</h5>--}}
{{--                                        <small class="text-muted">This Month</small>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-6">--}}
{{--                                        <h5 class="text-primary fw-bold mb-1">Rp 0</h5>--}}
{{--                                        <small class="text-muted">This Week</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex justify-content-between mb-2">--}}
{{--                                    <small>Today</small>--}}
{{--                                    <small class="fw-bold">Rp 0</small>--}}
{{--                                </div>--}}
{{--                                <div class="progress mb-3" style="height: 8px;">--}}
{{--                                    <div class="progress-bar bg-success" style="width: 0%"></div>--}}
{{--                                </div>--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-6">--}}
{{--                                        <button class="btn btn-outline-success btn-sm w-100">Payment History</button>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-6">--}}
{{--                                        <button class="btn btn-success btn-sm w-100">Withdraw</button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <!-- Today's Schedule -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-calendar-check text-info me-2"></i>
                                    Today's Schedule
                                </h5>
                            </div>
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-calendar-check display-4 text-muted mb-3"></i>
                                <p class="text-muted">No scheduled jobs for today.</p>
                                <a href="#" class="btn btn-outline-info btn-sm">View Full Calendar</a>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-lightning text-warning me-2"></i>
                                    Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-headset me-2"></i>Contact Support
                                    </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="bi bi-question-circle me-2"></i>Help & FAQ
                                    </button>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Report Issue
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .badge {
            font-size: 0.75rem;
        }

        .progress {
            border-radius: 10px;
        }

        .btn-group .dropdown-menu {
            border-radius: 8px;
        }
    </style>
</x-app-layout>
