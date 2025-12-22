<x-app-layout>
    <div class="review-page-container">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('customer.orders.show', $order->uuid) }}" class="btn btn-outline-secondary rounded-pill me-3 btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <h1 class="h3 fw-bold mb-0">Rate & Review</h1>
                <p class="text-muted mb-0 small">Share your experience with {{ $order->tukang->name }}</p>
            </div>
        </div>

        <div class="row">
            <!-- LEFT COLUMN: Review Form -->
            <div class="col-lg-8">
                <div class="review-card">
                    <form action="{{ route('customer.reviews.store', $order->uuid) }}" method="POST" id="reviewForm">
                        @csrf
                        
                        <!-- Rating Section -->
                        <div class="mb-5">
                            <h5 class="fw-bold mb-3">How would you rate this service?</h5>
                            <div class="star-rating-container text-center py-4">
                                <div class="star-rating mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="d-none" required>
                                        <label for="star{{ $i }}" class="star-label" data-rating="{{ $i }}">
                                            <i class="bi bi-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                <div id="ratingText" class="rating-text">Click to rate</div>
                            </div>
                            @error('rating')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Text -->
                        <div class="mb-4">
                            <label for="review_text" class="form-label fw-bold">Share your experience (Optional)</label>
                            <textarea 
                                name="review_text" 
                                id="review_text" 
                                class="form-control" 
                                rows="6" 
                                placeholder="Tell us about your experience with this service. What did you like? What could be improved?"
                                maxlength="1000"
                            >{{ old('review_text') }}</textarea>
                            <small class="text-muted">Maximum 1000 characters</small>
                            @error('review_text')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-brand-orange rounded-pill px-5 fw-bold" id="submitBtn" disabled>
                                <i class="bi bi-send me-1"></i> Submit Review
                            </button>
                            <a href="{{ route('customer.orders.show', $order->uuid) }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN: Order Summary -->
            <div class="col-lg-4">
                <!-- Order Info Card -->
                <div class="review-card">
                    <h6 class="fw-bold mb-3">Order Summary</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Service</small>
                        <p class="mb-0 fw-semibold">{{ $order->service->name }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Order Number</small>
                        <p class="mb-0 fw-semibold">#{{ $order->order_number }}</p>
                    </div>

                    @if($order->completion)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Duration</small>
                            <p class="mb-0 fw-semibold">{{ $order->completion->working_duration }} minutes</p>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Completed On</small>
                            <p class="mb-0 fw-semibold">{{ $order->completion->submitted_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif

                    <div class="pt-3 border-top">
                        <small class="text-muted d-block mb-1">Total Paid</small>
                        <p class="mb-0 fw-bold fs-5 text-brand-orange">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Tukang Info Card -->
                <div class="review-card mt-3">
                    <div class="text-center">
                        <img src="{{ $order->tukang->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                             class="rounded-circle mb-3" 
                             style="width: 80px; height: 80px; object-fit: cover;" 
                             alt="{{ $order->tukang->name }}">
                        <h6 class="fw-bold mb-1">{{ $order->tukang->name }}</h6>
                        <div class="text-warning mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($order->tukang->rating))
                                    <i class="bi bi-star-fill"></i>
                                @elseif($i - 0.5 <= $order->tukang->rating)
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                            <span class="text-muted ms-1 small">({{ $order->tukang->rating }})</span>
                        </div>
                        <small class="text-muted">{{ $order->tukang->total_reviews }} reviews</small>
                    </div>
                </div>

                <!-- Completion Photos -->
                @if($order->completion && $order->completion->photos && count($order->completion->photos) > 0)
                    <div class="review-card mt-3">
                        <h6 class="fw-bold mb-3">Completion Photos</h6>
                        <div class="row g-2">
                            @foreach($order->completion->photos as $photo)
                                <div class="col-6">
                                    <img src="{{ Storage::url($photo) }}" 
                                         class="img-fluid rounded" 
                                         style="width: 100%; height: 100px; object-fit: cover; cursor: pointer;"
                                         alt="Completion photo"
                                         onclick="window.open('{{ Storage::url($photo) }}', '_blank')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .review-page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .review-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .star-rating-container {
            background: linear-gradient(135deg, #fff5e6 0%, #ffe8cc 100%);
            border-radius: 12px;
        }

        .star-rating {
            font-size: 3rem;
            display: inline-flex;
            gap: 0.5rem;
        }

        .star-label {
            cursor: pointer;
            color: #e0e0e0;
            transition: all 0.2s ease;
        }

        .star-label:hover,
        .star-label.active {
            color: #ffc107;
            transform: scale(1.1);
        }

        .star-label.active {
            animation: starPulse 0.3s ease;
        }

        @keyframes starPulse {
            0% { transform: scale(1.1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1.1); }
        }

        .rating-text {
            font-size: 1.1rem;
            color: #666;
            font-weight: 500;
            min-height: 30px;
        }

        .rating-text.rated {
            color: var(--brand-orange);
            font-weight: 600;
        }

        .form-control:focus {
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.15);
        }

        @media (max-width: 991px) {
            .review-page-container {
                padding: 1rem;
            }

            .review-card {
                padding: 1.5rem;
            }

            .star-rating {
                font-size: 2.5rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = document.querySelectorAll('.star-label');
            const ratingText = document.getElementById('ratingText');
            const submitBtn = document.getElementById('submitBtn');
            const radioInputs = document.querySelectorAll('input[name="rating"]');
            
            let currentRating = 0;
            
            const ratingTexts = {
                1: '⭐ Poor - Very dissatisfied',
                2: '⭐⭐ Fair - Somewhat dissatisfied',
                3: '⭐⭐⭐ Good - Satisfied',
                4: '⭐⭐⭐⭐ Very Good - Very satisfied',
                5: '⭐⭐⭐⭐⭐ Excellent - Extremely satisfied'
            };

            // Star click handler
            labels.forEach((label, index) => {
                label.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    currentRating = rating;
                    
                    // Update radio input
                    document.getElementById(`star${rating}`).checked = true;
                    
                    // Update visual state
                    updateStars(rating);
                    
                    // Update text
                    ratingText.textContent = ratingTexts[rating];
                    ratingText.classList.add('rated');
                    
                    // Enable submit button
                    submitBtn.disabled = false;
                });
                
                // Hover effect
                label.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    updateStars(rating, true);
                });
            });

            // Reset on mouse leave
            document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                updateStars(currentRating);
            });

            function updateStars(rating, isHover = false) {
                labels.forEach((label, index) => {
                    const icon = label.querySelector('i');
                    if (index < rating) {
                        icon.classList.remove('bi-star');
                        icon.classList.add('bi-star-fill');
                        if (!isHover) {
                            label.classList.add('active');
                        }
                    } else {
                        icon.classList.remove('bi-star-fill');
                        icon.classList.add('bi-star');
                        if (!isHover) {
                            label.classList.remove('active');
                        }
                    }
                });
            }

            // Form validation
            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                if (currentRating === 0) {
                    e.preventDefault();
                    alert('Please select a rating before submitting.');
                    return false;
                }
            });
        });
    </script>
</x-app-layout>
