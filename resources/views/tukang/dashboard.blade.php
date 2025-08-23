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
                                <h3 class="fw-bold mb-1">7</h3>
                                <p class="mb-0 small">New Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-play-circle display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">4</h3>
                                <p class="mb-0 small">Active Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">42</h3>
                                <p class="mb-0 small">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-currency-dollar display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">Rp 4.2M</h3>
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
                                    <span class="badge bg-warning">7 New</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <!-- Job Request 1 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">AC Service & Cleaning</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>Menteng, Central Jakarta
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar me-1"></i>Today, 2:00 PM
                                            </p>
                                            <p class="small mb-0">Split AC in bedroom needs cleaning and maintenance. Customer prefers afternoon service.</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h5 class="text-success fw-bold mb-2">Rp 150K</h5>
                                            <button class="btn btn-success btn-sm me-2">Accept</button>
                                            <button class="btn btn-outline-secondary btn-sm">Decline</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Request 2 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Water Pump Installation</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>Kelapa Gading, North Jakarta
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar me-1"></i>Tomorrow, 9:00 AM
                                            </p>
                                            <p class="small mb-0">Install new water pump for house. Materials provided by customer.</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h5 class="text-success fw-bold mb-2">Rp 300K</h5>
                                            <button class="btn btn-success btn-sm me-2">Accept</button>
                                            <button class="btn btn-outline-secondary btn-sm">Decline</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Request 3 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Kitchen Sink Repair</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>Kemang, South Jakarta
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar me-1"></i>Today, 4:00 PM
                                            </p>
                                            <p class="small mb-0">Kitchen sink is clogged and water not draining properly.</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h5 class="text-success fw-bold mb-2">Rp 100K</h5>
                                            <button class="btn btn-success btn-sm me-2">Accept</button>
                                            <button class="btn btn-outline-secondary btn-sm">Decline</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- More Jobs Link -->
                                <div class="p-3 text-center">
                                    <a href="#" class="btn btn-outline-primary btn-sm">View All Requests (7)</a>
                                </div>
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
                            <div class="card-body p-0">
                                <!-- Active Job 1 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Bathroom Tile Repair</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>Sari Wijaya
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-geo-alt me-1"></i>Pondok Indah, South Jakarta
                                            </p>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-info me-2">In Progress</span>
                                                <small class="text-muted">Started 1.5 hours ago</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="btn-group">
                                                <button class="btn btn-outline-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    Update Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">On the Way</a></li>
                                                    <li><a class="dropdown-item" href="#">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="#">Almost Done</a></li>
                                                    <li><a class="dropdown-item" href="#">Completed</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Job 2 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Ceiling Fan Installation</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>Budi Santoso
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-geo-alt me-1"></i>Serpong, Tangerang
                                            </p>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-warning me-2">On the Way</span>
                                                <small class="text-muted">ETA: 25 minutes</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="btn-group">
                                                <button class="btn btn-outline-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    Update Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">On the Way</a></li>
                                                    <li><a class="dropdown-item" href="#">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="#">Almost Done</a></li>
                                                    <li><a class="dropdown-item" href="#">Completed</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Job 3 -->
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Water Heater Repair</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>Indira Putri
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-geo-alt me-1"></i>Kuningan, South Jakarta
                                            </p>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-secondary me-2">Scheduled</span>
                                                <small class="text-muted">Tomorrow 8:00 AM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="btn-group">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    Update Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">On the Way</a></li>
                                                    <li><a class="dropdown-item" href="#">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="#">Almost Done</a></li>
                                                    <li><a class="dropdown-item" href="#">Completed</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                            <div class="card-body p-0">
                                <!-- Completed Job 1 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">House Painting Service</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>Dewi Lestari
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar me-1"></i>March 18, 2024
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">Completed</span>
                                                <div class="text-warning">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <span class="text-muted ms-1">(5.0)</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h6 class="text-success fw-bold mb-1">Rp 650K</h6>
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>Paid
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Job 2 -->
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Electrical Socket Installation</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>Ahmad Rahman
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar me-1"></i>March 17, 2024
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">Completed</span>
                                                <div class="text-warning">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                    <span class="text-muted ms-1">(4.0)</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h6 class="text-success fw-bold mb-1">Rp 200K</h6>
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>Paid
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Job 3 -->
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Air Conditioner Maintenance</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>Lisa Kusuma
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-calendar me-1"></i>March 16, 2024
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">Completed</span>
                                                <div class="text-warning">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <span class="text-muted ms-1">(5.0)</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h6 class="text-success fw-bold mb-1">Rp 180K</h6>
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>Paid
                                            </small>
                                        </div>
                                    </div>
                                </div>
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
                                <p class="text-muted small mb-2">Professional Handyman - Jakarta</p>
                                <div class="text-warning mb-3">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-muted ms-1">4.9 (89 reviews)</span>
                                </div>
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <h6 class="mb-0">134</h6>
                                        <small class="text-muted">Jobs</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="mb-0">96%</h6>
                                        <small class="text-muted">Success</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="mb-0">3Y</h6>
                                        <small class="text-muted">Experience</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm w-100">Edit Profile</button>
                            </div>
                        </div>

                        <!-- Earnings Overview -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-graph-up text-success me-2"></i>
                                    Earnings Overview
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <h5 class="text-success fw-bold mb-1">Rp 4.2M</h5>
                                        <small class="text-muted">This Month</small>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-primary fw-bold mb-1">Rp 1.3M</h5>
                                        <small class="text-muted">This Week</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <small>Today</small>
                                    <small class="fw-bold">Rp 450K</small>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 72%"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-outline-success btn-sm w-100">Payment History</button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-success btn-sm w-100">Withdraw</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Schedule -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-calendar-check text-info me-2"></i>
                                    Today's Schedule
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                            <i class="bi bi-clock text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">9:00 AM</h6>
                                            <p class="small text-muted mb-0">Ceiling Fan - Serpong</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                            <i class="bi bi-clock text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">2:00 PM</h6>
                                            <p class="small text-muted mb-0">AC Service - Menteng</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded p-2 me-3">
                                            <i class="bi bi-clock text-danger"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">4:00 PM</h6>
                                            <p class="small text-muted mb-0">Kitchen Sink - Kemang</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <a href="#" class="btn btn-outline-info btn-sm">View Full Calendar</a>
                                </div>
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
