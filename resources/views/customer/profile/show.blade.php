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

            <!-- Personal Information Section -->
        <div class="profile-section">
            <div class="section-header">
                <h3 class="section-title">Personal Information</h3>
                <div class="button-group">
                    <button type="button" class="edit-btn" id="edit-personal-btn">
                        <i class="bi bi-pencil"></i>
                        <span>Edit Profile</span>
                    </button>
                    <button type="button" class="save-btn" id="save-personal-btn" style="display: none;">
                        <i class="bi bi-check-lg"></i>
                        <span>Save Changes</span>
                    </button>
                    <button type="button" class="cancel-btn" id="cancel-personal-btn" style="display: none;">
                        <i class="bi bi-x-lg"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </div>

            <form id="personal-info-form" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label class="field-label">Name</label>
                    <input type="text" name="name" class="field-value" value="{{ old('name', $customer->name) }}" disabled>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Email Address</label>
                    <div style="display: flex; align-items: center;">
                        <input type="email" name="email" class="field-value" value="{{ old('email', $customer->email) }}" readonly style="flex: 1;">
                        @if($customer->email_verified_at)
                            <span class="verified-badge">
                                <i class="bi bi-check-circle-fill"></i>
                                Verified
                            </span>
                        @else
                            <span class="unverified-badge">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                Unverified
                            </span>
                        @endif
                    </div>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Phone Number</label>
                    <input type="text" name="phone" class="field-value" value="{{ old('phone', $customer->phone ?? '') }}" disabled>
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Member Since</label>
                    <input type="text" class="field-value" value="{{ $customer->created_at->format('F d, Y') }}" disabled>
                </div>
            </form>
        </div>

        <!-- Location Information Section -->
        <div class="profile-section">
            <div class="section-header">
                <h3 class="section-title">Location Information</h3>
                <div class="button-group">
                    <button type="button" class="edit-btn" id="edit-location-btn">
                        <i class="bi bi-pencil"></i>
                        <span>Edit Location</span>
                    </button>
                    <button type="button" class="save-btn" id="save-location-btn" style="display: none;">
                        <i class="bi bi-check-lg"></i>
                        <span>Save Changes</span>
                    </button>
                    <button type="button" class="cancel-btn" id="cancel-location-btn" style="display: none;">
                        <i class="bi bi-x-lg"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </div>

            <form id="location-info-form" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <!-- Hidden fields for personal info (required by validation) -->
                <input type="hidden" name="name" value="{{ $customer->name }}">
                <input type="hidden" name="email" value="{{ $customer->email }}">
                <input type="hidden" name="phone" value="{{ $customer->phone ?? '' }}">

                <div class="field-group">
                    <label class="field-label">Address</label>
                    <input type="text" name="address" class="field-value" value="{{ old('address', $customer->address ?? '') }}" disabled>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field-row">
                    <div class="field-group">
                        <label class="field-label">City</label>
                        <input type="text" name="city" class="field-value" value="{{ old('city', $customer->city ?? '') }}" disabled>
                        @error('city')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Postal Code</label>
                        <input type="text" name="postal_code" class="field-value" value="{{ old('postal_code', $customer->postal_code ?? '') }}" disabled>
                        @error('postal_code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="latitude" id="latitude" value="{{ $customer->latitude }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ $customer->longitude }}">

                <div class="map-container disabled" id="map-container">
                    <div id="profile-map"></div>
                </div>
                
                <div id="map-instruction" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: #FFF3E0; border-left: 4px solid var(--brand-orange); border-radius: 8px;">
                    <i class="bi bi-info-circle" style="color: var(--brand-orange); margin-right: 0.5rem;"></i>
                    <small style="color: var(--brand-dark);">
                        <strong>Tip:</strong> Click anywhere on the map or drag the marker to set your new location
                    </small>
                </div>
                
                <button type="button" id="use-current-location-btn" class="use-current-location-btn" style="display: none;">
                    <i class="bi bi-crosshair"></i>
                    <span>Use Current Location</span>
                </button>
            </form>
        </div>

        <!-- Security Section -->
        <div class="profile-section security-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-shield-lock-fill text-brand-orange"></i>
                    Security
                </h3>
            </div>

            <div class="security-card">
                <div class="security-icon-box">
                    <i class="bi bi-key-fill"></i>
                </div>
                <div class="security-content">
                    <h4 class="security-title">Password</h4>
                    <p class="security-description">Keep your account secure by using a strong password</p>
                    <div class="password-display">
                        <i class="bi bi-lock-fill me-2"></i>
                        <span class="password-dots">••••••••••••</span>
                    </div>
                </div>
                <div class="security-action">
                    <a href="{{ route('profile.reset-password') }}" class="reset-password-btn-new">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Change Password</span>
                    </a>
                </div>
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
        let map;
        let marker;
        let isEditingPersonal = false;
        let isEditingLocation = false;

        // Initialize map
        function initMap() {
            const lat = parseFloat(document.getElementById('latitude').value) || -6.2088;
            const lng = parseFloat(document.getElementById('longitude').value) || 106.8456;
            
            const position = [lat, lng];

            // Initialize Leaflet map
            map = L.map('profile-map', {
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
            }).addTo(map);

            // Create custom icon for user location
            const userIcon = L.divIcon({
                className: 'user-location-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // Add marker for user location
            marker = L.marker(position, {
                icon: userIcon,
                draggable: false
            }).addTo(map);

            marker.bindPopup('<strong>Your Location</strong>').openPopup();
        }

        // Personal Info Edit Controls
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            
            document.getElementById('edit-personal-btn').addEventListener('click', function() {
                // Prevent editing if location is being edited
                if (isEditingLocation) {
                    alert('Please save or cancel your location changes before editing personal information.');
                    return;
                }
                
                isEditingPersonal = true;
                togglePersonalEdit(true);
            });

            document.getElementById('cancel-personal-btn').addEventListener('click', function() {
                isEditingPersonal = false;
                togglePersonalEdit(false);
                // Reset form
                document.getElementById('personal-info-form').reset();
                location.reload();
            });

            document.getElementById('save-personal-btn').addEventListener('click', function() {
                document.getElementById('personal-info-form').submit();
            });
        });

        function togglePersonalEdit(editing) {
            const form = document.getElementById('personal-info-form');
            const inputs = form.querySelectorAll('input[name]:not([type="hidden"])');
            
            inputs.forEach(input => {
                // Skip email field - it's always readonly
                if (input.name === 'email') {
                    return;
                }
                
                input.disabled = !editing;
                if (editing) {
                    input.classList.add('editable');
                } else {
                    input.classList.remove('editable');
                }
            });

            document.getElementById('edit-personal-btn').style.display = editing ? 'none' : 'inline-flex';
            document.getElementById('save-personal-btn').style.display = editing ? 'inline-flex' : 'none';
            document.getElementById('cancel-personal-btn').style.display = editing ? 'inline-flex' : 'none';
            
            // Disable location edit button when editing personal info
            const locationEditBtn = document.getElementById('edit-location-btn');
            if (editing) {
                locationEditBtn.style.opacity = '0.5';
                locationEditBtn.style.pointerEvents = 'none';
            } else {
                locationEditBtn.style.opacity = '1';
                locationEditBtn.style.pointerEvents = 'auto';
            }
        }

        // Location Info Edit Controls
        document.getElementById('edit-location-btn').addEventListener('click', function() {
            // Prevent editing if personal info is being edited
            if (isEditingPersonal) {
                alert('Please save or cancel your personal information changes before editing location.');
                return;
            }
            
            isEditingLocation = true;
            toggleLocationEdit(true);
        });

        document.getElementById('cancel-location-btn').addEventListener('click', function() {
            isEditingLocation = false;
            toggleLocationEdit(false);
            // Reset form
            document.getElementById('location-info-form').reset();
            location.reload();
        });

        document.getElementById('save-location-btn').addEventListener('click', function() {
            document.getElementById('location-info-form').submit();
        });

        // Use Current Location button
        document.getElementById('use-current-location-btn').addEventListener('click', function() {
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
                    marker.setLatLng(newPosition);
                    
                    // Update coordinates
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    
                    // Pan map to new position
                    map.setView(newPosition, 15);
                    
                    // Show popup
                    marker.bindPopup('<strong>Current Location</strong>').openPopup();
                    
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

        function toggleLocationEdit(editing) {
            const form = document.getElementById('location-info-form');
            const inputs = form.querySelectorAll('input[name]:not([type="hidden"])');
            const mapContainer = document.getElementById('map-container');
            
            inputs.forEach(input => {
                input.disabled = !editing;
                if (editing) {
                    input.classList.add('editable');
                } else {
                    input.classList.remove('editable');
                }
            });

            if (editing) {
                mapContainer.classList.remove('disabled');
                
                // Enable map interactions
                map.dragging.enable();
                map.touchZoom.enable();
                map.scrollWheelZoom.enable();
                map.doubleClickZoom.enable();
                map.boxZoom.enable();
                map.keyboard.enable();
                
                // Make marker draggable
                marker.dragging.enable();
                
                // Update coordinates when marker is dragged
                marker.on('dragend', function(event) {
                    const position = marker.getLatLng();
                    document.getElementById('latitude').value = position.lat;
                    document.getElementById('longitude').value = position.lng;
                });
                
                // Allow clicking on map to place new pinpoint
                map.on('click', function(e) {
                    const newPosition = e.latlng;
                    
                    // Move marker to clicked position
                    marker.setLatLng(newPosition);
                    
                    // Update coordinates
                    document.getElementById('latitude').value = newPosition.lat;
                    document.getElementById('longitude').value = newPosition.lng;
                    
                    // Pan map to new position
                    map.panTo(newPosition);
                    
                    // Show popup
                    marker.bindPopup('<strong>New Location</strong>').openPopup();
                });
            } else {
                mapContainer.classList.add('disabled');
                
                // Disable map interactions
                map.dragging.disable();
                map.touchZoom.disable();
                map.scrollWheelZoom.disable();
                map.doubleClickZoom.disable();
                map.boxZoom.disable();
                map.keyboard.disable();
                
                // Make marker non-draggable
                marker.dragging.disable();
                
                // Remove dragend event
                marker.off('dragend');
                
                // Remove map click event
                map.off('click');
            }

            document.getElementById('edit-location-btn').style.display = editing ? 'none' : 'inline-flex';
            document.getElementById('save-location-btn').style.display = editing ? 'inline-flex' : 'none';
            document.getElementById('cancel-location-btn').style.display = editing ? 'inline-flex' : 'none';
            
            // Show/hide map instruction
            const mapInstruction = document.getElementById('map-instruction');
            mapInstruction.style.display = editing ? 'block' : 'none';
            
            // Show/hide use current location button
            const useLocationBtn = document.getElementById('use-current-location-btn');
            useLocationBtn.style.display = editing ? 'block' : 'none';
            
            // Disable personal edit button when editing location
            const personalEditBtn = document.getElementById('edit-personal-btn');
            if (editing) {
                personalEditBtn.style.opacity = '0.5';
                personalEditBtn.style.pointerEvents = 'none';
            } else {
                personalEditBtn.style.opacity = '1';
                personalEditBtn.style.pointerEvents = 'auto';
            }
        }
    </script>
</x-app-layout>
