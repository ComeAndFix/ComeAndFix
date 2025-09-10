<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Profile Header -->
        <section class="bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="container py-5 text-white">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <img src="{{ $handyman->profile_image ?? 'https://via.placeholder.com/200x200/007bff/ffffff?text=' . substr($handyman->user->name, 0, 1) }}"
                             alt="{{ $handyman->user->name }}"
                             class="rounded-circle border border-white border-3 mb-3"
                             style="width: 200px; height: 200px; object-fit: cover;">

                        @if($handyman->is_verified)
                            <div class="badge bg-success fs-6 mb-2">
                                <i class="bi bi-patch-check"></i> Verified Professional
                            </div>
                        @endif
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-8">
                                <h1 class="h2 fw-bold mb-2">{{ $handyman->user->name }}</h1>
                                @if($handyman->business_name)
                                    <h4 class="text-white-75 mb-3">{{ $handyman->business_name }}</h4>
                                @endif

                                <div class="d-flex align-items-center mb-3">
                                    <div class="text-warning me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($handyman->rating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i - 0.5 <= $handyman->rating)
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="me-2">{{ number_format($handyman->rating, 1) }}</span>
                                    <span class="text-white-75">({{ $handyman->total_reviews }} reviews)</span>
                                </div>

                                <p class="mb-3">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $handyman->address }}, {{ $handyman->city }}, {{ $handyman->state }}
                                </p>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($handyman->services as $service)
                                        <span class="badge bg-white text-primary px-3 py-2">
                                            <i class="{{ $service->icon }}"></i> {{ $service->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stats-card bg-white bg-opacity-20 rounded p-3 text-center">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h4 fw-bold text-white">{{ $handyman->experience_years }}</div>
                                            <div class="small text-white-75">Years Experience</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="h4 fw-bold text-white">{{ $handyman->portfolios->count() }}</div>
                                            <div class="small text-white-75">Projects Done</div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        @if($handyman->is_available)
                                            <span class="badge bg-success fs-6">
                                                <i class="bi bi-circle-fill"></i> Available Now
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6">
                                                <i class="bi bi-circle-fill"></i> Busy
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 d-grid gap-2">
                                        <button class="btn btn-success btn-lg">
                                            <i class="bi bi-calendar-check"></i> Book Service
                                        </button>
                                        <button class="btn btn-outline-light">
                                            <i class="bi bi-chat"></i> Send Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Navigation Tabs -->
        <section class="bg-body border-bottom">
            <div class="container">
                <ul class="nav nav-tabs border-0" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-body" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab">
                            Services & Rates
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-body" id="portfolio-tab" data-bs-toggle="tab" data-bs-target="#portfolio" type="button" role="tab">
                            Portfolio
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-body" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                            Reviews
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-body" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">
                            About
                        </button>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Tab Content -->
        <section class="py-5 bg-body">
            <div class="container">
                <div class="tab-content" id="profileTabsContent">
                    <!-- Services Tab -->
                    <div class="tab-pane fade show active" id="services" role="tabpanel">
                        <div class="row g-4">
                            @foreach($handyman->handymanServices as $handymanService)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rounded-circle bg-primary text-white p-2 me-3">
                                                    <i class="{{ $handymanService->service->icon }}"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-body">{{ $handymanService->service->name }}</h6>
                                                    <div class="text-success fw-bold">
                                                        ${{ number_format($handymanService->custom_rate ?? $handymanService->service->base_price, 0) }}/hour
                                                    </div>
                                                </div>
                                            </div>

                                            @if($handymanService->description)
                                                <p class="text-body-secondary small">{{ $handymanService->description }}</p>
                                            @else
                                                <p class="text-body-secondary small">{{ $handymanService->service->description }}</p>
                                            @endif

                                            <button class="btn btn-outline-primary btn-sm w-100">
                                                <i class="bi bi-plus-circle"></i> Add to Quote
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Portfolio Tab -->
                    <div class="tab-pane fade" id="portfolio" role="tabpanel">
                        @if($handyman->portfolios->count() > 0)
                            <div class="row g-4">
                                @foreach($handyman->portfolios as $portfolio)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border-0 shadow-sm">
                                            @if($portfolio->images->count() > 0)
                                                <div class="position-relative">
                                                    <img src="{{ $portfolio->images->first()->image_path }}"
                                                         alt="{{ $portfolio->title }}"
                                                         class="card-img-top"
                                                         style="height: 200px; object-fit: cover;">
                                                    @if($portfolio->images->count() > 1)
                                                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark">
                                                            <i class="bi bi-images"></i> {{ $portfolio->images->count() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="card-body">
                                                <h6 class="fw-bold text-body">{{ $portfolio->title }}</h6>
                                                <p class="text-body-secondary small">{{ Str::limit($portfolio->description, 100) }}</p>

                                                <div class="row text-center border-top pt-3">
                                                    @if($portfolio->cost)
                                                        <div class="col-6">
                                                            <div class="small text-body-secondary">Cost</div>
                                                            <div class="fw-bold text-success">${{ number_format($portfolio->cost) }}</div>
                                                        </div>
                                                    @endif
                                                    @if($portfolio->duration_days)
                                                        <div class="col-6">
                                                            <div class="small text-body-secondary">Duration</div>
                                                            <div class="fw-bold text-body">{{ $portfolio->duration_days }} days</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-images display-1 text-muted mb-3"></i>
                                <h4 class="text-body">No Portfolio Items</h4>
                                <p class="text-body-secondary">This handyman hasn't added any portfolio items yet.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="text-center py-5">
                            <i class="bi bi-star-half display-1 text-muted mb-3"></i>
                            <h4 class="text-body">Reviews Coming Soon</h4>
                            <p class="text-body-secondary">Review system will be implemented in the next phase.</p>
                        </div>
                    </div>

                    <!-- About Tab -->
                    <div class="tab-pane fade" id="about" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="fw-bold text-body mb-3">About {{ $handyman->user->name }}</h5>
                                        <p class="text-body">{{ $handyman->bio }}</p>

                                        <hr class="my-4">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="fw-bold text-body">Experience</h6>
                                                <p class="text-body-secondary">{{ $handyman->experience_years }} years in the field</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="fw-bold text-body">Specialties</h6>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($handyman->services->take(3) as $service)
                                                        <span class="badge bg-light text-dark">{{ $service->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-body mb-3">Contact Information</h6>

                                        <div class="mb-3">
                                            <div class="small text-body-secondary">Phone</div>
                                            <div class="text-body">{{ $handyman->phone }}</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="small text-body-secondary">Email</div>
                                            <div class="text-body">{{ $handyman->user->email }}</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="small text-body-secondary">Service Area</div>
                                            <div class="text-body">{{ $handyman->city }}, {{ $handyman->state }}</div>
                                        </div>

                                        <hr>

                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary">
                                                <i class="bi bi-telephone"></i> Call Now
                                            </button>
                                            <button class="btn btn-outline-primary">
                                                <i class="bi bi-envelope"></i> Send Email
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        /* Light mode styles */
        .card {
            background-color: #ffffff;
            border-color: #dee2e6;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .stats-card .text-white {
            color: #ffffff !important;
        }

        .stats-card .text-white-75 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        /* Dark mode styles */
        [data-bs-theme="dark"] .card {
            background-color: #2d3748 !important;
            border-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(255, 255, 255, 0.1) !important;
        }

        [data-bs-theme="dark"] .text-body {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .text-body-secondary {
            color: #a0aec0 !important;
        }

        [data-bs-theme="dark"] .bg-body {
            background-color: #1a202c !important;
        }

        [data-bs-theme="dark"] .nav-link {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .nav-link.active {
            color: #3182ce !important;
            border-bottom-color: #3182ce !important;
        }

        [data-bs-theme="dark"] .badge.bg-light {
            background-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }

        /* Ensure white text stays white in stats card */
        .stats-card .text-white,
        [data-bs-theme="dark"] .stats-card .text-white {
            color: #ffffff !important;
        }

        .stats-card .text-white-75,
        [data-bs-theme="dark"] .stats-card .text-white-75 {
            color: rgba(255, 255, 255, 0.75) !important;
        }
    </style>
</x-app-layout>
