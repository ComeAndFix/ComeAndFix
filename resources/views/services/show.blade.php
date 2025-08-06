<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Service Header -->
        <section class="bg-primary text-white py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb text-white-50 mb-2">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none">
                                        <i class="bi bi-house"></i> Home
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="text-white-50">Services</span>
                                </li>
                                <li class="breadcrumb-item active text-white">{{ $service->name }}</li>
                            </ol>
                        </nav>
                        <h1 class="h2 mb-2">
                            <i class="{{ $service->icon }} me-2"></i>
                            {{ $service->name }} Services
                        </h1>
                        <p class="mb-0">{{ $service->description }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="bg-white text-primary rounded p-3">
                            <div class="small">Starting from</div>
                            <div class="h4 mb-0">${{ number_format($service->base_price, 0) }}/hour</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Location & Filters -->
        <section class="py-3 bg-body-secondary border-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                            <span class="small text-body">Showing handymen within 25 miles of your location</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="sortBy" id="distance" checked>
                            <label class="btn btn-outline-primary" for="distance">Nearest</label>

                            <input type="radio" class="btn-check" name="sortBy" id="rating">
                            <label class="btn btn-outline-primary" for="rating">Highest Rated</label>

                            <input type="radio" class="btn-check" name="sortBy" id="price">
                            <label class="btn btn-outline-primary" for="price">Best Price</label>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Handymen Listing -->
        <section class="py-5 bg-body">
            <div class="container">
                @if($handymen->count() > 0)
                    <div class="row g-4">
                        @foreach($handymen as $handyman)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm handyman-card bg-body">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                                <img src="{{ $handyman->profile_image ?? 'https://via.placeholder.com/100x100/007bff/ffffff?text=' . substr($handyman->user->name, 0, 1) }}"
                                                     alt="{{ $handyman->user->name }}"
                                                     class="rounded-circle border border-3 border-primary"
                                                     style="width: 100px; height: 100px; object-fit: cover;">
                                                @if($handyman->is_verified)
                                                    <div class="badge bg-success mt-2">
                                                        <i class="bi bi-patch-check"></i> Verified
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <h5 class="fw-bold text-body mb-1">{{ $handyman->user->name }}</h5>
                                                @if($handyman->business_name)
                                                    <p class="text-primary mb-2">{{ $handyman->business_name }}</p>
                                                @endif

                                                <div class="d-flex align-items-center mb-2">
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
                                                    <span class="text-body">{{ number_format($handyman->rating, 1) }}</span>
                                                    <span class="text-body-secondary ms-2">({{ $handyman->total_reviews }} reviews)</span>
                                                </div>

                                                <p class="text-body-secondary mb-2">
                                                    <i class="bi bi-geo-alt"></i>
                                                    {{ $handyman->city }}, {{ $handyman->state }} •
                                                    <span class="text-primary">{{ $handyman->distance }}km away</span>
                                                </p>

                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($handyman->services->take(3) as $service)
                                                        <span class="badge bg-{{ $service->color }} bg-opacity-10 text-{{ $service->color }} border border-{{ $service->color }} border-opacity-25">
                                                            {{ $service->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($handyman->services->count() > 3)
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                            +{{ $handyman->services->count() - 3 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <div class="text-body-secondary small">Experience</div>
                                                <div class="h5 fw-bold text-body">{{ $handyman->experience_years }} years</div>

                                                @if($handyman->is_available)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Available
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Busy
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <a href="{{ route('handymen.show', ['id' => $handyman->id, 'service_id' => $service->id]) }}"
                                                   class="btn btn-primary btn-sm w-100 mb-2">
                                                    View Profile
                                                </a>
                                                <button class="btn btn-outline-primary btn-sm w-100">
                                                    <i class="bi bi-chat"></i> Message
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination placeholder -->
                    <div class="d-flex justify-content-center mt-5">
                        <nav>
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">1</span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-search display-1 text-muted mb-3"></i>
                        <h3 class="text-body">No handymen found</h3>
                        <p class="text-body-secondary">We couldn't find any {{ strtolower($service->name) }} professionals in your area. Try expanding your search radius.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Browse All Services</a>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <style>
        .handyman-card {
            transition: all 0.3s ease;
        }

        .handyman-card:hover {
            transform: translateY(-2px);
        }

        [data-bs-theme="dark"] .handyman-card {
            background-color: var(--bs-dark) !important;
            border-color: var(--bs-border-color) !important;
        }

        [data-bs-theme="dark"] .handyman-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(255, 255, 255, 0.1) !important;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.5);
        }

        [data-bs-theme="dark"] .bg-body-secondary {
            background-color: var(--bs-dark) !important;
        }

        [data-bs-theme="dark"] .text-body {
            color: var(--bs-body-color) !important;
        }

        [data-bs-theme="dark"] .text-body-secondary {
            color: var(--bs-secondary-color) !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get user's location
            if (navigator.geolocation && !{{ $latitude ?? 'null' }} && !{{ $longitude ?? 'null' }}) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Reload page with coordinates
                    const url = new URL(window.location);
                    url.searchParams.set('lat', lat);
                    url.searchParams.set('lng', lng);
                    window.location = url;
                });
            }

            // Sort functionality
            document.querySelectorAll('input[name="sortBy"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Add sorting logic here
                    console.log('Sorting by:', this.id);
                });
            });
        });
    </script>
</x-app-layout>
