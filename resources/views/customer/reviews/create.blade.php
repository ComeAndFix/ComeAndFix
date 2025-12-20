<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="bi bi-star-fill"></i> Rate & Review</h4>
                    </div>
                    <div class="card-body">
                        <!-- Order Information -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h5 class="mb-3">Order Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Service:</strong> {{ $order->service->name }}</p>
                                    <p class="mb-2"><strong>Order #:</strong> {{ $order->order_number }}</p>
                                    <p class="mb-0"><strong>Total Price:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Tukang:</strong> {{ $order->tukang->name }}</p>
                                    @if($order->completion)
                                        <p class="mb-2"><strong>Duration:</strong> {{ $order->completion->working_duration }} minutes</p>
                                        <p class="mb-0"><strong>Completed:</strong> {{ $order->completion->submitted_at->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($order->completion && $order->completion->photos)
                            <div class="mb-4">
                                <h6 class="mb-3">Completion Photos</h6>
                                <div class="row g-2">
                                    @foreach($order->completion->photos as $photo)
                                        <div class="col-md-3">
                                            <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Completion photo">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <hr>

                        <!-- Review Form -->
                        <form action="{{ route('customer.reviews.store', $order) }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fs-5"><strong>Rating <span class="text-danger">*</span></strong></label>
                                <div class="rating-stars d-flex gap-2 mb-2">
                                    <input type="radio" name="rating" value="1" id="star1" class="d-none" required>
                                    <label for="star1" class="star-label fs-1" data-rating="1">
                                        <i class="bi bi-star"></i>
                                    </label>
                                    
                                    <input type="radio" name="rating" value="2" id="star2" class="d-none">
                                    <label for="star2" class="star-label fs-1" data-rating="2">
                                        <i class="bi bi-star"></i>
                                    </label>
                                    
                                    <input type="radio" name="rating" value="3" id="star3" class="d-none">
                                    <label for="star3" class="star-label fs-1" data-rating="3">
                                        <i class="bi bi-star"></i>
                                    </label>
                                    
                                    <input type="radio" name="rating" value="4" id="star4" class="d-none">
                                    <label for="star4" class="star-label fs-1" data-rating="4">
                                        <i class="bi bi-star"></i>
                                    </label>
                                    
                                    <input type="radio" name="rating" value="5" id="star5" class="d-none">
                                    <label for="star5" class="star-label fs-1" data-rating="5">
                                        <i class="bi bi-star"></i>
                                    </label>
                                </div>
                                <small class="text-muted">Click on the stars to rate (1 = Poor, 5 = Excellent)</small>
                                @error('rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="review_text" class="form-label"><strong>Your Review (Optional)</strong></label>
                                <textarea 
                                    name="review_text" 
                                    id="review_text" 
                                    class="form-control" 
                                    rows="5" 
                                    placeholder="Share your experience with this tukang's service..."
                                    maxlength="1000"
                                >{{ old('review_text') }}</textarea>
                                <small class="text-muted">Maximum 1000 characters</small>
                                @error('review_text')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-send"></i> Submit Review
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .star-label {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s ease;
            margin: 0 2px;
        }

        .star-label:hover,
        .star-label.active {
            color: #ffc107;
        }

        .rating-stars input:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: #ffc107;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = document.querySelectorAll('.star-label');
            
            labels.forEach((label, index) => {
                label.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    
                    // Remove active class from all
                    labels.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked and previous stars
                    for (let i = 0; i < rating; i++) {
                        labels[i].classList.add('active');
                    }
                });
                
                // Hover effect
                label.addEventListener('mouseenter', function() {
                    const rating = this.getAttribute('data-rating');
                    for (let i = 0; i < rating; i++) {
                        labels[i].querySelector('i').classList.remove('bi-star');
                        labels[i].querySelector('i').classList.add('bi-star-fill');
                    }
                });
                
                label.addEventListener('mouseleave', function() {
                    labels.forEach(l => {
                        if (!l.classList.contains('active')) {
                            l.querySelector('i').classList.remove('bi-star-fill');
                            l.querySelector('i').classList.add('bi-star');
                        }
                    });
                });
            });
            
            // Update star icons when clicked
            const radioInputs = document.querySelectorAll('input[name="rating"]');
            radioInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const rating = parseInt(this.value);
                    labels.forEach((label, index) => {
                        const icon = label.querySelector('i');
                        if (index < rating) {
                            icon.classList.remove('bi-star');
                            icon.classList.add('bi-star-fill');
                            label.classList.add('active');
                        } else {
                            icon.classList.remove('bi-star-fill');
                            icon.classList.add('bi-star');
                            label.classList.remove('active');
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
