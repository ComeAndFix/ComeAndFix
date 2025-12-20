<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Hero Section -->
        <section class="bg-primary text-white py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold mb-3">Find Trusted Handymen Near You</h1>
                        <p class="lead mb-4">Get your home repairs done by verified professionals. Quick, reliable, and affordable services at your doorstep.</p>

                        <!-- Search Bar -->
                        <div class="card shadow">
                            <div class="card-body p-4">
                                <form class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-lg" placeholder="What service do you need?">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-lg" placeholder="Your location">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-warning btn-lg w-100" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="/images/handyman-hero.svg" alt="Handyman Services" class="img-fluid">
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Stats -->
        <section class="py-4 bg-white border-bottom">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="text-primary fw-bold">500+</h3>
                        <p class="mb-0">Verified Handymen</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-primary fw-bold">10k+</h3>
                        <p class="mb-0">Jobs Completed</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-primary fw-bold">4.8★</h3>
                        <p class="mb-0">Average Rating</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-primary fw-bold">24/7</h3>
                        <p class="mb-0">Support</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Services -->
        <!-- Popular Services -->
        <section id="services" class="py-5 bg-body">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-body">Popular Services</h2>
                    <p class="text-body-secondary">Choose from our most requested handyman services</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-wrench text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Plumbing</h5>
                                <p class="text-body-secondary small mb-3">Pipe repairs, leaks, installations</p>
                                <a href="{{ route('services.show', 'plumbing') }}" class="btn btn-outline-primary btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-lightning text-success" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Electrical</h5>
                                <p class="text-body-secondary small mb-3">Wiring, outlets, lighting fixes</p>
                                <a href="{{ route('services.show', 'electrical') }}" class="btn btn-outline-success btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-fan text-info" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">AC Service</h5>
                                <p class="text-body-secondary small mb-3">Installation, repair, maintenance</p>
                                <a href="{{ route('services.show', 'ac-service') }}" class="btn btn-outline-info btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-paint-bucket text-warning" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Painting</h5>
                                <p class="text-body-secondary small mb-3">Interior, exterior, touch-ups</p>
                                <a href="{{ route('services.show', 'painting') }}" class="btn btn-outline-warning btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-hammer text-danger" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Carpentry</h5>
                                <p class="text-body-secondary small mb-3">Furniture repair, custom work</p>
                                <a href="{{ route('services.show', 'carpentry') }}" class="btn btn-outline-danger btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-tools text-secondary" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Appliance Repair</h5>
                                <p class="text-body-secondary small mb-3">Washing machine, fridge, etc.</p>
                                <a href="{{ route('services.show', 'appliance-repair') }}" class="btn btn-outline-secondary btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-house-gear text-success" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Home Maintenance</h5>
                                <p class="text-body-secondary small mb-3">General repairs, upkeep</p>
                                <a href="{{ route('services.show', 'home-maintenance') }}" class="btn btn-outline-success btn-sm">Book Now</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-plus-circle text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">View All</h5>
                                <p class="text-body-secondary small mb-3">Explore more services</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">See More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">How It Works</h2>
                    <p class="text-muted">Get your job done in 3 simple steps</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold">1</span>
                        </div>
                        <h5 class="fw-bold">Describe Your Job</h5>
                        <p class="text-muted">Tell us what you need done and when</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold">2</span>
                        </div>
                        <h5 class="fw-bold">Get Matched</h5>
                        <p class="text-muted">We'll connect you with qualified handymen</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold">3</span>
                        </div>
                        <h5 class="fw-bold">Job Done</h5>
                        <p class="text-muted">Pay securely after the work is completed</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Emergency Services -->
        <section class="py-5 bg-danger text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Emergency Services Available 24/7
                        </h3>
                        <p class="mb-0">Urgent plumbing, electrical, or HVAC issues? Get immediate help from our emergency response team.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-light btn-lg">
                            <i class="bi bi-telephone-fill me-2"></i>
                            Call Emergency
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Jobs -->
        <section class="py-5">
            <div class="container">
                <div class="row align-items-center mb-4">
                    <div class="col">
                        <h2 class="fw-bold mb-0">Your Recent Bookings</h2>
                    </div>
                    <div class="col-auto">
                        <a href="#" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">Kitchen Faucet Repair</h6>
                                        <small class="text-muted">Plumbing • March 15, 2024</small>
                                    </div>
                                    <span class="badge bg-success">Completed</span>
                                </div>
                                <p class="small text-muted mb-3">Fixed leaky kitchen faucet and replaced worn gaskets</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="/images/handyman-avatar.jpg" alt="Handyman" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                        <span class="small">John Smith</span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary">Rate & Review</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">Living Room Painting</h6>
                                        <small class="text-muted">Painting • March 20, 2024</small>
                                    </div>
                                    <span class="badge bg-warning">In Progress</span>
                                </div>
                                <p class="small text-muted mb-3">Interior painting of living room walls</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="/images/handyman-avatar2.jpg" alt="Handyman" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                        <span class="small">Mike Johnson</span>
                                    </div>
                                    <button class="btn btn-sm btn-primary">Track Progress</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .card {
            transition: all 0.3s ease;
        }
    </style>
</x-app-layout>
