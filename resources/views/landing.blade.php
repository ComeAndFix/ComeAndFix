<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Come&Fix - Connect with skilled handymen for all your home repair needs. Fast, reliable, and affordable service at your fingertips.">
    <title>Come&Fix - Your Trusted Home Repair Solution</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Jost:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    @vite(['resources/css/components/navigation.css', 'resources/css/landing.css'])
</head>
<body>
    <!-- Navigation -->
    <nav class="landing-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix Logo" style="width: 40px; height: 40px;">
                <span>Come&Fix</span>
            </div>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#services">Services</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('customer.login') }}" class="btn-login">Login</a>
                <a href="{{ route('customer.register') }}" class="btn-signup">Get Started</a>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <div class="nav-logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix Logo" style="width: 40px; height: 40px;">
                <span>Come&Fix</span>
            </div>
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="mobile-menu-links">
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#services">Services</a>
            <div class="mobile-menu-actions">
                <a href="{{ route('customer.login') }}" class="btn-login">Login</a>
                <a href="{{ route('customer.register') }}" class="btn-signup">Get Started</a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Find Skilled Handymen
                    <span class="gradient-text">In Minutes</span>
                </h1>
                <p class="hero-description">
                    Connect with verified, professional handymen for all your home repair needs. 
                    We've got you covered with fast, reliable service.
                </p>
                <div class="hero-cta">
                    <a href="{{ route('customer.register') }}" class="btn-primary">
                        <span>Get Started</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#how-it-works" class="btn-secondary">
                        <i class="bi bi-play-circle"></i>
                        <span>See How It Works</span>
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <div class="floating-card card-1">
                        <i class="bi bi-hammer"></i>
                        <div>
                            <div class="card-title">Expert Craftsmen</div>
                            <div class="card-subtitle">Verified & Rated</div>
                        </div>
                    </div>
                    <div class="floating-card card-2">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <div>
                            <div class="card-title">Fast Response</div>
                            <div class="card-subtitle">Within 30 Minutes</div>
                        </div>
                    </div>
                    <div class="floating-card card-3">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <div class="card-title">100% Secure</div>
                            <div class="card-subtitle">Protected Payments</div>
                        </div>
                    </div>
                    <!-- Main illustration placeholder -->
                    <div class="hero-illustration" id="heroIllustration"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-container">
            <div class="section-header">
                <div class="section-badge">Why Choose Us</div>
                <h2 class="section-title">Everything You Need For Home Repairs</h2>
                <p class="section-description">
                    We've built the most comprehensive platform to connect you with skilled professionals
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="feature-title">Easy Discovery</h3>
                    <p class="feature-description">
                        Browse through hundreds of verified handymen near you. Filter by service type, rating, and distance.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h3 class="feature-title">Verified Professionals</h3>
                    <p class="feature-description">
                        All our tukangs are verified and reviewed by customers.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3 class="feature-title">Real-Time Chat</h3>
                    <p class="feature-description">
                        Communicate directly with handymen, share photos, and discuss project details instantly.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h3 class="feature-title">Secure Payments</h3>
                    <p class="feature-description">
                        Pay safely through our platform with multiple payment options and transaction protection.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h3 class="feature-title">Location-Based</h3>
                    <p class="feature-description">
                        Find handymen closest to you with our smart location matching for faster service.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3 class="feature-title">Job Tracking</h3>
                    <p class="feature-description">
                        Track your projects from booking to completion with real-time status updates.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-container">
            <div class="section-header">
                <div class="section-badge">Simple Process</div>
                <h2 class="section-title">How Come&Fix Works</h2>
                <p class="section-description">
                    Get your home repairs done in just a few simple steps
                </p>
            </div>
            <div class="steps-container">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="step-title">Find a Tukang</h3>
                    <p class="step-description">
                        Browse our map or search by service type to find the perfect handyman near you
                    </p>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <h3 class="step-title">Discuss Details</h3>
                    <p class="step-description">
                        Chat directly with the tukang, share photos, and agree on pricing and timeline
                    </p>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3 class="step-title">Book & Pay</h3>
                    <p class="step-description">
                        Confirm your booking and make secure payment through our platform
                    </p>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h3 class="step-title">Get It Done</h3>
                    <p class="step-description">
                        Sit back while the professional completes your job and leave a review
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="section-container">
            <div class="section-header">
                <div class="section-badge">Our Services</div>
                <h2 class="section-title">We Cover All Your Home Repair Needs</h2>
                <p class="section-description">
                    Find experts for every job you need
                </p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-droplet"></i>
                    </div>
                    <h3 class="service-title">Plumbing</h3>
                    <p class="service-description">Leaks, installations, repairs, and maintenance</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-lightning"></i>
                    </div>
                    <h3 class="service-title">Electrical</h3>
                    <p class="service-description">Wiring, fixtures, outlets, and troubleshooting</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-paint-bucket"></i>
                    </div>
                    <h3 class="service-title">Painting</h3>
                    <p class="service-description">Interior, exterior, and decorative painting</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-snow"></i>
                    </div>
                    <h3 class="service-title">HVAC</h3>
                    <p class="service-description">AC repair, installation, and maintenance</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h3 class="service-title">Appliance Repair</h3>
                    <p class="service-description">Fix and maintain all your home appliances</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Fix Your Home?</h2>
                <p class="cta-description">
                    Join thousands of satisfied customers who trust Come&Fix for all their home repair needs
                </p>
                <div class="cta-buttons">
                    <a href="{{ route('customer.register') }}" class="btn-cta-primary">
                        <span>Get Started Now</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ route('customer.login') }}" class="btn-cta-secondary">
                        Already have an account? <b><u>Login</u></b>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo.svg') }}" alt="Come & Fix Logo" style="width: 32px; height: 32px;">
                        <span>Come&Fix</span>
                    </div>
                    <p class="footer-description">
                        Connecting homeowners with skilled professionals for all repair and maintenance needs.
                    </p>
                </div>
                <div class="footer-links">
                    <h4 class="footer-title">Company</h4>
                    <a href="#features">About Us</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#services">Services</a>
                </div>
                <div class="footer-links">
                    <h4 class="footer-title">For Customers</h4>
                    <a href="{{ route('customer.register') }}">Sign Up</a>
                    <a href="{{ route('customer.login') }}">Login</a>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=comeandfix1@gmail.com&su=Customer%20Support%20Request" target="_blank" rel="noopener noreferrer">Help Center</a>
                </div>
                <div class="footer-links">
                    <h4 class="footer-title">For Tukangs</h4>
                    <a href="{{ route('tukang.register') }}">Become a Tukang</a>
                    <a href="{{ route('tukang.login') }}">Tukang Login</a>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=comeandfix1@gmail.com&su=Tukang%20Support%20Request" target="_blank" rel="noopener noreferrer">Support</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Come&Fix. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuClose = document.getElementById('mobileMenuClose');

        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        mobileMenuClose.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-menu-links a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        let lastScroll = 0;
        const nav = document.querySelector('.landing-nav');

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe all feature cards, steps, and service cards
        document.querySelectorAll('.feature-card, .step-item, .service-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
