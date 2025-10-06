<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 class="fw-bold mb-2">Rate Your Experience</h3>
                            <p class="text-muted">How was your service?</p>
                        </div>

                        <!-- Order Details -->
                        <div class="bg-light rounded p-3 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-wrench text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Kitchen Faucet Repair</h6>
                                    <small class="text-muted">March 15, 2024 • Order #ORD-001</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">Rp. 850.000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Handyman Info -->
                        <div class="d-flex align-items-center mb-4">
                            <img src="/images/handyman-avatar.jpg" alt="John Smith" class="rounded-circle me-3" style="width: 60px; height: 60px;">
                            <div class="flex-grow-1">
                                <h5 class="mb-1 fw-bold">John Smith</h5>
                                <div class="d-flex align-items-center">
                                    <div class="text-warning me-2">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-half"></i>
                                    </div>
                                    <span class="fw-bold me-1">4.8</span>
                                    <small class="text-muted">(127 reviews)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Section -->
                        <form id="ratingForm">
                            <div class="text-center mb-4">
                                <p class="mb-3 fw-semibold">Rate this service:</p>
                                <div class="star-rating mb-3">
                                    <i class="bi bi-star star" data-rating="1"></i>
                                    <i class="bi bi-star star" data-rating="2"></i>
                                    <i class="bi bi-star star" data-rating="3"></i>
                                    <i class="bi bi-star star" data-rating="4"></i>
                                    <i class="bi bi-star star" data-rating="5"></i>
                                </div>
                                <div id="ratingText" class="text-muted small">Tap to rate</div>
                            </div>

                            <!-- Review Text -->
                            <div class="mb-4">
                                <label for="reviewText" class="form-label fw-semibold">Write a review (optional)</label>
                                <textarea class="form-control" id="reviewText" rows="4" placeholder="Share your experience with other customers..."></textarea>
                                <div class="form-text">Your review helps other customers make better decisions.</div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    Submit Review
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                    Skip for Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .star-rating {
            font-size: 2.5rem;
            cursor: pointer;
        }

        .star {
            color: #e0e0e0;
            transition: all 0.2s ease;
            margin: 0 5px;
        }

        .star:hover,
        .star.active {
            color: #ffc107;
            transform: scale(1.1);
        }

        .star.active {
            animation: pulse 0.3s ease;
        }

        @keyframes pulse {
            0% { transform: scale(1.1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1.1); }
        }

        .photo-preview {
            position: relative;
            display: inline-block;
            margin: 5px;
        }

        .photo-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .photo-preview .remove-photo {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingText = document.getElementById('ratingText');
            const submitBtn = document.getElementById('submitBtn');
            const photosInput = document.getElementById('photos');
            const photoPreview = document.getElementById('photoPreview');

            let currentRating = 0;
            const ratingTexts = {
                1: 'Poor - Very dissatisfied',
                2: 'Fair - Somewhat dissatisfied',
                3: 'Good - Satisfied',
                4: 'Very Good - Very satisfied',
                5: 'Excellent - Extremely satisfied'
            };

            // Star rating functionality
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(rating);
                });

                star.addEventListener('click', function() {
                    currentRating = parseInt(this.dataset.rating);
                    highlightStars(currentRating);
                    ratingText.textContent = ratingTexts[currentRating];
                    ratingText.className = 'fw-bold text-warning';
                    submitBtn.disabled = false;
                });
            });

            document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                highlightStars(currentRating);
            });

            function highlightStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('bi-star');
                        star.classList.add('bi-star-fill', 'active');
                    } else {
                        star.classList.remove('bi-star-fill', 'active');
                        star.classList.add('bi-star');
                    }
                });
            }

            // Photo upload functionality
            photosInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                photoPreview.innerHTML = '';

                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const photoDiv = document.createElement('div');
                            photoDiv.className = 'photo-preview';
                            photoDiv.innerHTML = `
                                <img src="${e.target.result}" alt="Preview">
                                <button type="button" class="remove-photo" onclick="removePhoto(${index})">×</button>
                            `;
                            photoPreview.appendChild(photoDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            // Form submission
            document.getElementById('ratingForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData();
                formData.append('rating', currentRating);
                formData.append('review', document.getElementById('reviewText').value);

                // Add photos
                const files = photosInput.files;
                for (let i = 0; i < files.length; i++) {
                    formData.append('photos[]', files[i]);
                }

                // Submit to backend
                fetch('/submit-review', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message and redirect
                            alert('Thank you for your review!');
                            window.location.href = '/dashboard';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again.');
                    });
            });
        });

        function removePhoto(index) {
            const photosInput = document.getElementById('photos');
            const dt = new DataTransfer();
            const files = Array.from(photosInput.files);

            files.forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });

            photosInput.files = dt.files;
            photosInput.dispatchEvent(new Event('change'));
        }
    </script>
</x-app-layout>
