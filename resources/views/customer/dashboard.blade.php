@push('styles')
    @vite(['resources/css/customer/dashboard.css'])
    <style>
        body {
            background: #F1F2F4 !important;
        }
    </style>
@endpush

<x-app-layout>
    <!-- Top Section: Hero + Orders (Light Gray Background) -->
    <div style="background: #F1F2F4; padding-bottom: 1rem; margin-top: -2rem; padding-top: 2rem;">
        <!-- Hero Section -->
        <section class="hero-section" style="background-image: url('{{ asset('images/workshop-tools.png') }}');" role="banner">
            <div class="hero-content">
                <p class="hero-greeting">Hello! Welcome back,</p>
                <h1 class="hero-name">{{ strtoupper(Auth::guard('customer')->user()->name) }}</h1>
            </div>
        </section>

        <!-- Active Orders Section (only show if there are active orders) -->
        @if($recentOrders->count() > 0)
        <section class="orders-section" aria-label="Active Orders">
            <div style="max-width: 1200px; margin: 0 auto;">
                <h2 class="section-title">Your Ongoing Order</h2>
                
                @foreach($recentOrders->take(1) as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="order-card" aria-label="View order details for {{ $order->service->name }}">
                    <div class="order-info">
                        <p class="order-type-label">Order Type</p>
                        <h3 class="order-type">{{ $order->service->name }}</h3>
                        
                        <div class="order-badges">
                            <span class="order-badge status" role="status">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                            @if($order->payment_status)
                            <span class="order-badge payment" role="status">{{ ucwords($order->payment_status) }}</span>
                            @endif
                        </div>
                        
                        <div class="order-tukang">
                            <img src="{{ $order->tukang->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="Profile photo of {{ $order->tukang->name }}" class="tukang-avatar">
                            <div>
                                <p class="tukang-label">Tukang</p>
                                <p class="tukang-name">{{ $order->tukang->name }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-arrow" aria-hidden="true">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif
    </div>

    <!-- Bottom Section: Services + Info (White Background) -->
    <div style="background: #FFFFFF; min-height: 60vh;">
        <!-- Main Content: Services on Left, Info on Right -->
        <section class="bottom-section" style="padding: 3rem 2rem;">
            <div class="main-content-grid" style="max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr; gap: 4rem;">
                
                <!-- LEFT: Services Section -->
                <div class="services-section" role="region" aria-label="Available Services">
                    <h2 class="section-title">{{ $recentOrders->count() > 0 ? 'Services We Offer' : 'Choose Service' }}</h2>
                    <p class="section-subtitle">Choose from a selection of our available handyman services</p>
                    
                    <div class="services-grid">
                        <!-- Plumbing -->
                        <a href="{{ route('find-tukang', ['service_type' => 'Plumbing']) }}" class="service-card" aria-label="Find plumbing services">
                            <div class="service-icon-wrapper" aria-hidden="true">
                                <i class="bi bi-droplet-fill service-icon"></i>
                            </div>
                            <h3 class="service-name">PLUMBING</h3>
                            <p class="service-description">Pipe repairs, leaks, installations</p>
                        </a>
                        
                        <!-- Electricity -->
                        <a href="{{ route('find-tukang', ['service_type' => 'Electricity']) }}" class="service-card" aria-label="Find electricity services">
                            <div class="service-icon-wrapper" aria-hidden="true">
                                <i class="bi bi-lightning-charge-fill service-icon"></i>
                            </div>
                            <h3 class="service-name">ELECTRICITY</h3>
                            <p class="service-description">Wiring, outlets, lighting fixes</p>
                        </a>
                        
                        <!-- AC Unit -->
                        <a href="{{ route('find-tukang', ['service_type' => 'HVAC']) }}" class="service-card" aria-label="Find AC and HVAC services">
                            <div class="service-icon-wrapper" aria-hidden="true">
                                <i class="bi bi-snow service-icon"></i>
                            </div>
                            <h3 class="service-name">AC UNIT</h3>
                            <p class="service-description">Installation, repairs, maintenance</p>
                        </a>
                        
                        <!-- Painting -->
                        <a href="{{ route('find-tukang', ['service_type' => 'Painting']) }}" class="service-card" aria-label="Find painting services">
                            <div class="service-icon-wrapper" aria-hidden="true">
                                <i class="bi bi-paint-bucket service-icon"></i>
                            </div>
                            <h3 class="service-name">PAINTING</h3>
                            <p class="service-description">Interior, exterior, touch-ups</p>
                        </a>
                        
                        <!-- Appliance Repair -->
                        <a href="{{ route('find-tukang', ['service_type' => 'Appliance Repair']) }}" class="service-card" aria-label="Find appliance repair services">
                            <div class="service-icon-wrapper" aria-hidden="true">
                                <i class="bi bi-tools service-icon"></i>
                            </div>
                            <h3 class="service-name">APPLIANCE REPAIR</h3>
                            <p class="service-description">Washing machine, fridge, etc</p>
                        </a>
                    </div>
                </div>
                
                <!-- RIGHT: Find Trusted Handymen + How It Works -->
                <div style="display: flex; flex-direction: column; gap: 3rem;">
                    
                    <!-- Find Trusted Handymen -->
                    <div role="region" aria-label="About Our Services">
                        <h2 class="section-title">Find trusted<br>handymen near you</h2>
                        <p class="section-subtitle" style="margin-bottom: 0;">
                            Get your home repairs done by professionals. <strong>Quick, reliable,</strong> and <strong>affordable</strong> services at your doorstep.
                        </p>
                    </div>
                    
                    <!-- How It Works -->
                    <div class="how-it-works-section" style="padding: 0;" role="region" aria-label="How It Works">
                        <h3 class="section-title" style="font-size: 1.5rem; margin-bottom: 1.5rem;">How it Works</h3>
                        
                        <div class="steps-container">
                            <div class="step">
                                <div class="step-number" aria-label="Step 1">1</div>
                                <div class="step-content">
                                    <h4>Step 1 - <strong>Choose your service</strong></h4>
                                    <p>Choose the services you need from the panel on the left.</p>
                                </div>
                            </div>
                            
                            <div class="step">
                                <div class="step-number" aria-label="Step 2">2</div>
                                <div class="step-content">
                                    <h4>Step 2 - <strong>Choose your tukang</strong></h4>
                                    <p>Choose your preferred Tukang based on their portfolio.</p>
                                </div>
                            </div>
                            
                            <div class="step">
                                <div class="step-number" aria-label="Step 3">3</div>
                                <div class="step-content">
                                    <h4>Step 3 - <strong>Describe and negotiate</strong></h4>
                                    <p>Describe and negotiate with the Tukang to find your desired price.</p>
                                </div>
                            </div>
                            
                            <div class="step">
                                <div class="step-number" aria-label="Step 4">
                                    <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix logo" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                                <div class="step-content">
                                    <h4>Step 4 - <strong>Come&Fix!</strong></h4>
                                    <p>Your Tukang will Come&Fix your problems for you!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
