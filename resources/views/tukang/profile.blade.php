<x-app-layout>
    <div class="container py-5">
        <div class="row">
            <!-- Left Column - Profile Info -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        @if($tukang->profile_image)
                            <img src="{{ Storage::url($tukang->profile_image) }}" class="rounded-circle mb-3 border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" 
                                 style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold; border: 3px solid white;">
                                {{ substr($tukang->name, 0, 1) }}
                            </div>
                        @endif
                        <h4 class="fw-bold mb-2">{{ $tukang->name }}</h4>
                        <p class="text-muted mb-2">
                            <i class="bi bi-envelope me-1"></i>{{ $tukang->email }}
                        </p>
                        <p class="text-muted mb-3">
                            <i class="bi bi-telephone me-1"></i>{{ $tukang->phone ?? 'No phone' }}
                        </p>
                        
                        <!-- Rating Summary -->
                        <div class="border-top pt-3 mb-3">
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <i class="bi bi-star-fill text-warning me-2" style="font-size: 1.5rem;"></i>
                                <h3 class="mb-0 fw-bold">{{ number_format($averageRating, 1) }}</h3>
                            </div>
                            <p class="text-muted small mb-0">{{ $totalReviews }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</p>
                        </div>

                        <!-- Quick Actions -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('tukang.finance.index') }}" class="btn btn-success">
                                <i class="bi bi-wallet2 me-2"></i>Financial Manager
                            </a>
                            <button type="button" class="btn btn-primary" onclick="toggleEditMode()">
                                <i class="bi bi-pencil me-2"></i><span id="edit-btn-text">Edit Profile</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Availability Status -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Status</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Availability</span>
                            <span class="badge {{ $tukang->is_available ? 'bg-success' : 'bg-danger' }}">
                                {{ $tukang->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details & Edit Form -->
            <div class="col-lg-8">
                <!-- View Mode -->
                <div id="view-mode">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-bold">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Name</label>
                                    <p class="fw-bold">{{ $tukang->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Phone</label>
                                    <p class="fw-bold">{{ $tukang->phone ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Email</label>
                                    <p class="fw-bold">{{ $tukang->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Years of Experience</label>
                                    <p class="fw-bold">{{ $tukang->years_experience ?? 0 }} years</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Address</label>
                                <p class="fw-bold">{{ $tukang->address ?? '-' }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="text-muted small">City</label>
                                    <p class="fw-bold">{{ $tukang->city ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Postal Code</label>
                                    <p class="fw-bold">{{ $tukang->postal_code ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services/Specializations Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-tools me-2"></i>Services Offered</h5>
                        </div>
                        <div class="card-body">
                            @if($tukang->specializations && count($tukang->specializations) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($tukang->specializations as $specialization)
                                        <span class="badge bg-primary fs-6 px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>{{ $specialization }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No services specified</p>
                            @endif
                        </div>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-bold">Rating Breakdown</h5>
                        </div>
                        <div class="card-body">
                            @foreach([5, 4, 3, 2, 1] as $stars)
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2" style="width: 60px;">{{ $stars }} <i class="bi bi-star-fill text-warning"></i></span>
                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $totalReviews > 0 ? ($ratingDistribution[$stars] / $totalReviews * 100) : 0 }}%">
                                        </div>
                                    </div>
                                    <span class="text-muted" style="width: 40px;">{{ $ratingDistribution[$stars] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Edit Mode (Hidden by default) -->
                <div id="edit-mode" style="display: none;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-bold">Edit Profile</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('tukang.profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-12 text-center">
                                        <div class="d-inline-block position-relative">
                                            <div class="rounded-circle overflow-hidden bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 100px; height: 100px; border: 3px solid var(--bs-primary);">
                                                @if($tukang->profile_image)
                                                    <img src="{{ Storage::url($tukang->profile_image) }}" id="tukang-preview-img" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <div id="tukang-preview-placeholder" style="font-size: 2.5rem; font-weight: bold;">
                                                        {{ substr($tukang->name, 0, 1) }}
                                                    </div>
                                                    <img src="" id="tukang-preview-img" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                @endif
                                            </div>
                                            <label for="profile_image" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                   style="width: 32px; height: 32px; cursor: pointer; border: 2px solid white;">
                                                <i class="bi bi-camera"></i>
                                            </label>
                                            <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/png, image/jpeg, image/jpg">
                                        </div>
                                        <div class="small text-muted mt-2">Click camera icon to change</div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $tukang->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $tukang->phone) }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" value="{{ $tukang->email }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Years of Experience</label>
                                        <input type="number" name="years_experience" class="form-control" value="{{ old('years_experience', $tukang->years_experience) }}" min="0">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $tukang->address) }}">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" value="{{ old('city', $tukang->city) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $tukang->postal_code) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Services Offered <span class="text-danger">*</span></label>
                                    <p class="text-muted small">Select all services you can provide</p>
                                    <div class="row">
                                        @foreach($availableServices as $service)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="specializations[]" 
                                                           value="{{ $service->name }}" id="service{{ $service->id }}"
                                                           {{ in_array($service->name, old('specializations', $tukang->specializations ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="service{{ $service->id }}">
                                                        <i class="bi bi-tools me-1"></i>{{ $service->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Save Changes
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleEditMode()">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEditMode() {
            const viewMode = document.getElementById('view-mode');
            const editMode = document.getElementById('edit-mode');
            const btnText = document.getElementById('edit-btn-text');
            
            if (viewMode.style.display === 'none') {
                viewMode.style.display = 'block';
                editMode.style.display = 'none';
                btnText.textContent = 'Edit Profile';
            } else {
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
                btnText.textContent = 'View Profile';
            }
        }

        // Image Preview
        document.addEventListener('DOMContentLoaded', function() {
            const profileImageInput = document.getElementById('profile_image');
            if (profileImageInput) {
                profileImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewImg = document.getElementById('tukang-preview-img');
                            const placeholder = document.getElementById('tukang-preview-placeholder');
                            
                            previewImg.src = e.target.result;
                            previewImg.style.display = 'block';
                            if (placeholder) {
                                placeholder.style.display = 'none';
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        // Show error/success messages
        @if(session('success'))
            const toast = document.createElement('div');
            toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.textContent = '{{ session('success') }}';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        @endif

        @if($errors->any())
            const errorToast = document.createElement('div');
            errorToast.className = 'alert alert-danger position-fixed top-0 end-0 m-3';
            errorToast.style.zIndex = '9999';
            errorToast.innerHTML = '<ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>';
            document.body.appendChild(errorToast);
            setTimeout(() => errorToast.remove(), 5000);
        @endif
    </script>
</x-app-layout>
