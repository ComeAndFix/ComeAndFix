<x-app-layout>
    @vite(['resources/css/customer/profile.css'])

    <div class="profile-page-wrapper">
        <div class="profile-container">
            <h1 class="page-title">
                <i class="bi bi-person-circle text-brand-orange"></i>
                My Profile
            </h1>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle alert-icon"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle alert-icon"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle alert-icon"></i>
                    <div>
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- View Mode -->
            <div id="view-mode" class="view-mode-container">
                <div class="profile-grid">
                    <!-- Sidebar -->
                    <div class="profile-sidebar">
                        <!-- Profile Card -->
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-photo-wrapper">
                                    @if($customer->profile_image_url)
                                        <img src="{{ $customer->profile_image_url }}" alt="Profile Photo" class="profile-photo">
                                    @else
                                        <div class="profile-placeholder">
                                            {{ $customer->initials }}
                                        </div>
                                    @endif
                                </div>
                                
                                <h2 class="profile-name">{{ $customer->name }}</h2>
                                
                                <div class="profile-contact">
                                    <i class="bi bi-envelope"></i>
                                    <span>{{ $customer->email }}</span>
                                </div>
                                
                                @if($customer->phone)
                                    <div class="profile-contact">
                                        <i class="bi bi-telephone"></i>
                                        <span>{{ $customer->phone }}</span>
                                    </div>
                                @endif

                                <!-- Member Since -->
                                <div class="member-since">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Member since {{ $customer->created_at->format('F Y') }}</span>
                                </div>

                                <!-- Quick Actions -->
                                <div class="quick-actions">
                                    <button type="button" class="action-btn action-btn-primary" onclick="toggleEditMode()">
                                        <i class="bi bi-pencil"></i>
                                        <span>Edit Profile</span>
                                    </button>
                                    <a href="{{ route('profile.reset-password') }}" class="action-btn action-btn-secondary">
                                        <i class="bi bi-key"></i>
                                        <span>Change Password</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Email Verification Status Card -->
                        <div class="status-card">
                            <h3 class="status-header">Email Verification</h3>
                            <div class="status-row">
                                <span>Status</span>
                                @if($customer->email_verified_at)
                                    <span class="status-badge status-verified">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Verified
                                    </span>
                                @else
                                    <span class="status-badge status-unverified">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        Unverified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="profile-main">
                        <!-- Personal Information -->
                        <div class="profile-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="bi bi-person-fill text-brand-orange"></i>
                                    Personal Information
                                </h3>
                            </div>

                            <div class="field-grid">
                                <div class="field-group">
                                    <label class="field-label">Name</label>
                                    <input type="text" class="field-value" value="{{ $customer->name }}" disabled>
                                </div>

                                <div class="field-group">
                                    <label class="field-label">Phone</label>
                                    <input type="text" class="field-value" value="{{ $customer->phone ?? '-' }}" disabled>
                                </div>

                                <div class="field-group full-width">
                                    <label class="field-label">Email</label>
                                    <input type="text" class="field-value" value="{{ $customer->email }}" disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="profile-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="bi bi-geo-alt-fill text-brand-orange"></i>
                                    Location Information
                                </h3>
                            </div>

                            <div class="field-grid">
                                <div class="field-group full-width">
                                    <label class="field-label">Address</label>
                                    <input type="text" class="field-value" value="{{ $customer->address ?? '-' }}" disabled>
                                </div>

                                <div class="field-group">
                                    <label class="field-label">City</label>
                                    <input type="text" class="field-value" value="{{ $customer->city ?? '-' }}" disabled>
                                </div>

                                <div class="field-group">
                                    <label class="field-label">Postal Code</label>
                                    <input type="text" class="field-value" value="{{ $customer->postal_code ?? '-' }}" disabled>
                                </div>
                            </div>

                            <div class="map-container disabled" id="map-container">
                                <div id="profile-map"></div>
                            </div>
                            
                            @if($customer->latitude && $customer->longitude)
                                <p style="margin-top: 0.75rem; color: #6C757D; font-size: 0.875rem;">
                                    <i class="bi bi-pin-map"></i>
                                    <strong>Coordinates:</strong> {{ number_format($customer->latitude, 6) }}, {{ number_format($customer->longitude, 6) }}
                                </p>
                            @else
                                <p style="margin-top: 0.75rem; color: #6C757D; font-size: 0.875rem; font-style: italic;">
                                    <i class="bi bi-exclamation-circle"></i>
                                    Location not set. Please edit your profile to set your location.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Mode -->
            <div id="edit-mode" class="edit-mode-container">
                <div class="profile-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-pencil-square text-brand-orange"></i>
                            Edit Profile
                        </h3>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo Upload -->
                        <div class="photo-upload-section">
                            <div class="photo-upload-wrapper">
                                @if($customer->profile_image_url)
                                    <img src="{{ $customer->profile_image_url }}" alt="Profile Photo" class="profile-photo" id="edit-profile-photo">
                                @else
                                    <div class="profile-placeholder" id="edit-profile-placeholder">
                                        {{ $customer->initials }}
                                    </div>
                                    <img src="" alt="Profile Photo" class="profile-photo" id="edit-profile-photo" style="display: none;">
                                @endif
                                
                                <label for="profile_image" class="photo-upload-label">
                                    <i class="bi bi-camera"></i>
                                </label>
                                <input type="file" name="profile_image" id="profile_image" class="photo-upload-input" accept="image/png, image/jpeg, image/jpg">
                            </div>
                            <p class="photo-upload-hint">Click the camera icon to upload a new photo (JPG, PNG, max 1MB)</p>
                        </div>

                        <!-- Personal Information Fields -->
                        <div class="field-grid">
                            <div class="field-group">
                                <label class="field-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="field-value editable" value="{{ old('name', $customer->name) }}" required>
                            </div>

                            <div class="field-group">
                                <label class="field-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="field-value editable" value="{{ old('phone', $customer->phone) }}" required>
                            </div>

                            <div class="field-group full-width">
                                <label class="field-label">Email</label>
                                <input type="email" class="field-value" value="{{ $customer->email }}" disabled>
                                <small style="color: #6C757D; font-size: 0.75rem; margin-top: 0.25rem; display: block;">Email cannot be changed</small>
                            </div>

                            <div class="field-group full-width">
                                <label class="field-label">Address</label>
                                <input type="text" name="address" class="field-value editable" value="{{ old('address', $customer->address) }}">
                            </div>

                            <div class="field-group">
                                <label class="field-label">City</label>
                                <input type="text" name="city" class="field-value editable" value="{{ old('city', $customer->city) }}">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Postal Code</label>
                                <input type="text" name="postal_code" class="field-value editable" value="{{ old('postal_code', $customer->postal_code) }}">
                            </div>
                        </div>

                        <!-- Hidden Location Fields -->
                        <input type="hidden" name="latitude" id="latitude" value="{{ $customer->latitude }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $customer->longitude }}">

                        <!-- Location Map Section -->
                        <div class="field-group full-width" style="margin-top: 1.5rem;">
                            <label class="field-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Your Location
                            </label>
                            
                            <div class="map-container" id="edit-map-container" style="display: none;">
                                <div id="edit-profile-map"></div>
                            </div>
                            
                            <div id="edit-map-instruction" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: #FFF3E0; border-left: 4px solid var(--brand-orange); border-radius: 8px;">
                                <i class="bi bi-info-circle" style="color: var(--brand-orange); margin-right: 0.5rem;"></i>
                                <small style="color: var(--brand-dark);">
                                    <strong>Tip:</strong> Click anywhere on the map or drag the marker to set your new location
                                </small>
                            </div>
                            
                            <button type="button" id="use-current-location-btn" class="use-current-location-btn" style="display: none;">
                                <i class="bi bi-crosshair"></i>
                                <span>Use Current Location</span>
                            </button>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="form-btn form-btn-save">
                                <i class="bi bi-check-circle"></i>
                                <span>Save Changes</span>
                            </button>
                            <button type="button" class="form-btn form-btn-cancel" onclick="toggleEditMode()">
                                <i class="bi bi-x-circle"></i>
                                <span>Cancel</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        /* Custom marker styles for user location */
        .user-location-marker {
            width: 24px;
            height: 24px;
            background: var(--brand-orange);
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .user-location-marker::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        /* Spinning animation for loading state */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .spin {
            animation: spin 1s linear infinite;
        }
    </style>
    
    <script>
        let viewMap;
        let editMap;
        let viewMarker;
        let editMarker;
        let isEditMode = false;

        // Initialize view mode map
        function initViewMap() {
            const lat = parseFloat(document.getElementById('latitude').value) || -6.2088;
            const lng = parseFloat(document.getElementById('longitude').value) || 106.8456;
            
            const position = [lat, lng];

            // Initialize Leaflet map for view mode
            viewMap = L.map('profile-map', {
                zoomControl: true,
                dragging: false,
                touchZoom: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                boxZoom: false,
                keyboard: false
            }).setView(position, 15);

            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(viewMap);

            // Create custom icon for user location
            const userIcon = L.divIcon({
                className: 'user-location-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // Add marker for user location
            viewMarker = L.marker(position, {
                icon: userIcon,
                draggable: false
            }).addTo(viewMap);

            viewMarker.bindPopup('<strong>Your Location</strong>').openPopup();
        }

        // Initialize edit mode map
        function initEditMap() {
            const lat = parseFloat(document.getElementById('latitude').value) || -6.2088;
            const lng = parseFloat(document.getElementById('longitude').value) || 106.8456;
            
            const position = [lat, lng];

            // Initialize Leaflet map for edit mode
            editMap = L.map('edit-profile-map', {
                zoomControl: true,
                dragging: true,
                touchZoom: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                boxZoom: true,
                keyboard: true
            }).setView(position, 15);

            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(editMap);

            // Create custom icon for user location
            const userIcon = L.divIcon({
                className: 'user-location-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // Add marker for user location
            editMarker = L.marker(position, {
                icon: userIcon,
                draggable: true
            }).addTo(editMap);

            editMarker.bindPopup('<strong>Your Location</strong>').openPopup();

            // Update coordinates when marker is dragged
            editMarker.on('dragend', function(event) {
                const position = editMarker.getLatLng();
                document.getElementById('latitude').value = position.lat;
                document.getElementById('longitude').value = position.lng;
            });

            // Allow clicking on map to place new pinpoint
            editMap.on('click', function(e) {
                const newPosition = e.latlng;
                
                // Move marker to clicked position
                editMarker.setLatLng(newPosition);
                
                // Update coordinates
                document.getElementById('latitude').value = newPosition.lat;
                document.getElementById('longitude').value = newPosition.lng;
                
                // Pan map to new position
                editMap.panTo(newPosition);
                
                // Show popup
                editMarker.bindPopup('<strong>New Location</strong>').openPopup();
            });
        }

        function toggleEditMode() {
            const viewMode = document.getElementById('view-mode');
            const editMode = document.getElementById('edit-mode');
            const editMapContainer = document.getElementById('edit-map-container');
            const editMapInstruction = document.getElementById('edit-map-instruction');
            const useLocationBtn = document.getElementById('use-current-location-btn');
            
            if (viewMode.classList.contains('hidden')) {
                // Switch to view mode
                viewMode.classList.remove('hidden');
                editMode.classList.remove('active');
                isEditMode = false;
                
                // Hide edit map elements
                editMapContainer.style.display = 'none';
                editMapInstruction.style.display = 'none';
                useLocationBtn.style.display = 'none';
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Reinitialize view map after a short delay
                setTimeout(() => {
                    if (viewMap) {
                        viewMap.invalidateSize();
                    }
                }, 100);
            } else {
                // Switch to edit mode
                viewMode.classList.add('hidden');
                editMode.classList.add('active');
                isEditMode = true;
                
                // Show edit map elements
                editMapContainer.style.display = 'block';
                editMapInstruction.style.display = 'block';
                useLocationBtn.style.display = 'block';
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Initialize or refresh edit map after a short delay
                setTimeout(() => {
                    if (!editMap) {
                        initEditMap();
                    } else {
                        editMap.invalidateSize();
                        const lat = parseFloat(document.getElementById('latitude').value) || -6.2088;
                        const lng = parseFloat(document.getElementById('longitude').value) || 106.8456;
                        editMap.setView([lat, lng], 15);
                        editMarker.setLatLng([lat, lng]);
                    }
                }, 100);
            }
        }

        // Use Current Location button
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize view map on page load
            initViewMap();
            
            // Profile Image Preview
            const profileImageInput = document.getElementById('profile_image');
            
            if (profileImageInput) {
                profileImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (1MB = 1048576 bytes)
                        if (file.size > 1048576) {
                            alert('File size must be less than 1MB');
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                        if (!validTypes.includes(file.type)) {
                            alert('Only JPG, JPEG, and PNG files are allowed');
                            this.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewImg = document.getElementById('edit-profile-photo');
                            const placeholder = document.getElementById('edit-profile-placeholder');
                            
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

            // Use Current Location button handler
            const useLocationBtn = document.getElementById('use-current-location-btn');
            if (useLocationBtn) {
                useLocationBtn.addEventListener('click', function() {
                    const btn = this;
                    const originalText = btn.innerHTML;
                    
                    // Check if geolocation is supported
                    if (!navigator.geolocation) {
                        alert('Geolocation is not supported by your browser');
                        return;
                    }
                    
                    // Disable button and show loading state
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i><span>Getting location...</span>';
                    
                    // Get current position
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            const newPosition = [lat, lng];
                            
                            // Update marker position
                            if (editMarker) {
                                editMarker.setLatLng(newPosition);
                            }
                            
                            // Update coordinates
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;
                            
                            // Pan map to new position
                            if (editMap) {
                                editMap.setView(newPosition, 15);
                            }
                            
                            // Show popup
                            if (editMarker) {
                                editMarker.bindPopup('<strong>Current Location</strong>').openPopup();
                            }
                            
                            // Reset button
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        function(error) {
                            // Handle errors
                            let errorMessage = 'Unable to get your location. ';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += 'Please allow location access in your browser settings.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += 'Location information is unavailable.';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += 'The request timed out.';
                                    break;
                                default:
                                    errorMessage += 'An unknown error occurred.';
                            }
                            alert(errorMessage);
                            
                            // Reset button
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                });
            }
        });
    </script>
</x-app-layout>
