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

        <!-- Popular Services -->
        <section id="services" class="py-5 bg-body">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-body">Popular Services</h2>
                    <p class="text-body-secondary">Choose from our most requested handyman services</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('find-tukang', ['service_type' => 'Plumbing']) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body text-center p-4">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-wrench text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-body">Plumbing</h5>
                                    <p class="text-body-secondary small mb-3">Pipe repairs, leaks, installations</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('find-tukang', ['service_type' => 'Electricity']) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body text-center p-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-lightning text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-body">Electrical</h5>
                                    <p class="text-body-secondary small mb-3">Wiring, outlets, lighting fixes</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('find-tukang', ['service_type' => 'HVAC']) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body text-center p-4">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-fan text-info" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-body">AC Service</h5>
                                    <p class="text-body-secondary small mb-3">Installation, repair, maintenance</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('find-tukang', ['service_type' => 'Painting']) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body text-center p-4">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-paint-bucket text-warning" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-body">Painting</h5>
                                    <p class="text-body-secondary small mb-3">Interior, exterior, touch-ups</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('find-tukang', ['service_type' => 'Carpentry']) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body text-center p-4">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-hammer text-danger" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-body">Carpentry</h5>
                                    <p class="text-body-secondary small mb-3">Furniture repair, custom work</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-tools text-secondary" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold text-body">Appliance Repair</h5>
                                <p class="text-body-secondary small mb-3">Washing machine, fridge, etc.</p>
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


        <!-- Recent Orders -->
        <section id="recent-orders" class="py-5 bg-light">
            <div class="container">
                <div class="row align-items-center mb-4">
                    <div class="col">
                        <h2 class="fw-bold mb-0">Active Orders</h2>
                    </div>
                    <div class="col-auto">
                        <a href="#" class="btn btn-outline-primary">View All Orders</a>
                    </div>
                </div>

                <div class="row g-4">
                    @forelse($recentOrders as $order)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-bold mb-1">{{ $order->service->name }}</h5>
                                            <small class="text-muted">Order #{{ $order->order_number }}</small>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="badge bg-{{ $order->status_color }} mb-1">{{ ucfirst($order->status) }}</span>
                                            @if($order->payment_status)
                                                <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} small">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        @if($order->service_description)
                                            <p class="small text-muted mb-2">{{ $order->service_description }}</p>
                                        @endif

                                        @if($order->service_details)
                                            <div class="small text-muted mb-2">
                                                <strong>Service Details:</strong>
                                                <ul class="list-unstyled ms-3 mb-2">
                                                    @foreach($order->service_details as $key => $value)
                                                        <li>- {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-primary fw-bold">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                                            <small class="text-muted">Created: {{ $order->created_at->format('d M Y H:i') }}</small>
                                        </div>

                                        @if($order->accepted_at)
                                            <div class="small text-muted">
                                                Accepted: {{ $order->accepted_at->format('d M Y H:i') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                 style="width: 32px; height: 32px;">
                                                {{ substr($order->tukang->name, 0, 1) }}
                                            </div>
                                            <span class="small">{{ $order->tukang->name }}</span>
                                        </div>
                                        <a href="{{ route('chat.show', ['receiverType' => 'tukang', 'receiverId' => $order->tukang_id]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-chat"></i> Contact
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <i class="bi bi-bag x-lg text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted">No orders yet</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a scroll_to parameter in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const scrollTo = urlParams.get('scroll_to');

            if (scrollTo === 'recent-orders') {
                const element = document.getElementById('recent-orders');
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>

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
