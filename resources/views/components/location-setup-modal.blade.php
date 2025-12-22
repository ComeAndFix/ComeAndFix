@if(Auth::guard('customer')->check() && (!Auth::guard('customer')->user()->latitude || !Auth::guard('customer')->user()->longitude))
<div class="location-modal-overlay" id="locationModal">
    <div class="location-modal">
        <div class="location-modal-header">
            <div class="location-modal-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h2 class="location-modal-title">Set Your Location</h2>
            <p class="location-modal-subtitle">
                Help us find the best handymen near you by setting your location
            </p>
        </div>

        <div class="location-modal-body">
            <div class="location-content-grid">
                <!-- Left Column: Address Input Fields -->
                <div class="location-form-column">
                    <div class="location-form-section">
                        <div class="location-form-label">Address Information</div>
                        
                        <div class="location-input-group">
                            <div class="location-input-wrapper">
                                <i class="bi bi-house-door"></i>
                                <input 
                                    type="text" 
                                    id="addressInput" 
                                    class="location-input" 
                                    placeholder="Full home address (e.g., Jl. Sudirman No. 123)"
                                    required
                                />
                            </div>
                            <small class="location-input-error" id="addressError" style="display: none;"></small>
                        </div>

                        <div class="location-input-row">
                            <div class="location-input-group">
                                <div class="location-input-wrapper">
                                    <i class="bi bi-building"></i>
                                    <input 
                                        type="text" 
                                        id="cityInput" 
                                        class="location-input" 
                                        placeholder="City"
                                        required
                                    />
                                </div>
                                <small class="location-input-error" id="cityError" style="display: none;"></small>
                            </div>
                            <div class="location-input-group">
                                <div class="location-input-wrapper">
                                    <i class="bi bi-mailbox"></i>
                                    <input 
                                        type="text" 
                                        id="postalCodeInput" 
                                        class="location-input" 
                                        placeholder="Postal Code"
                                        required
                                    />
                                </div>
                                <small class="location-input-error" id="postalCodeError" style="display: none;"></small>
                            </div>
                        </div>
                    </div>

                    <div class="location-address-display">
                        <div class="location-address-label">Selected Coordinates</div>
                        <div class="location-address-text" id="locationAddress">
                            <i class="bi bi-geo-alt"></i>
                            <span class="location-address-placeholder">Click on the map or use "Use My Location"</span>
                        </div>
                        <small class="location-input-error" id="coordinatesError" style="display: none;"></small>
                    </div>
                </div>

                <!-- Right Column: Map Section -->
                <div class="location-map-column">
                    <div class="location-form-section">
                        <div class="location-form-label">Pin Your Location on Map</div>
                        <div class="location-form-helper">
                            <i class="bi bi-info-circle"></i>
                            Click anywhere on the map to set your location
                        </div>
                        
                        <div class="location-map-container">
                            <div id="locationMap" style="width: 100%; height: 100%;"></div>
                            <div class="location-map-loading" id="mapLoading">
                                <div class="location-spinner"></div>
                                <p style="color: var(--text-gray); font-size: 0.875rem; font-family: 'Inter', sans-serif;">
                                    Loading map...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="location-modal-actions">
                <button type="button" class="location-btn location-btn-secondary" id="useLocationBtn">
                    <i class="bi bi-crosshair"></i> Use My Location
                </button>
                <button type="button" class="location-btn location-btn-primary" id="saveLocationBtn" disabled>
                    <i class="bi bi-save"></i> Save Location
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('locationModal');
    const mapElement = document.getElementById('locationMap');
    const mapLoading = document.getElementById('mapLoading');
    const addressDisplay = document.getElementById('locationAddress');
    const useLocationBtn = document.getElementById('useLocationBtn');
    const saveLocationBtn = document.getElementById('saveLocationBtn');
    
    // Address input fields
    const addressInput = document.getElementById('addressInput');
    const cityInput = document.getElementById('cityInput');
    const postalCodeInput = document.getElementById('postalCodeInput');
    
    // Error message elements
    const addressError = document.getElementById('addressError');
    const cityError = document.getElementById('cityError');
    const postalCodeError = document.getElementById('postalCodeError');
    const coordinatesError = document.getElementById('coordinatesError');
    
    let map = null;
    let marker = null;
    let currentLat = null;
    let currentLng = null;

    // Initialize map with default view (Jakarta)
    function initMap() {
        map = L.map(mapElement).setView([-6.2088, 106.8456], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Add click event listener to map
        map.on('click', function(e) {
            updateLocation(e.latlng.lat, e.latlng.lng);
        });

        mapLoading.style.display = 'none';
    }

    // Show field error
    function showFieldError(input, errorElement, message) {
        input.classList.add('error');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    // Clear field error
    function clearFieldError(input, errorElement) {
        input.classList.remove('error');
        errorElement.style.display = 'none';
    }

    // Clear all errors
    function clearAllErrors() {
        clearFieldError(addressInput, addressError);
        clearFieldError(cityInput, cityError);
        clearFieldError(postalCodeInput, postalCodeError);
        coordinatesError.style.display = 'none';
    }

    // Reverse geocode to get address from coordinates
    async function getAddressFromCoords(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            
            if (data.display_name) {
                return data.display_name;
            }
            return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        } catch (error) {
            console.error('Geocoding error:', error);
            return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    }

    // Update map marker and address display
    async function updateLocation(lat, lng) {
        currentLat = lat;
        currentLng = lng;

        // Update map view
        map.setView([lat, lng], 15);

        // Remove old marker if exists
        if (marker) {
            map.removeLayer(marker);
        }

        // Create custom icon for user location (matching profile page style)
        const userIcon = L.divIcon({
            className: 'user-location-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // Add new marker with custom icon
        marker = L.marker([lat, lng], {
            icon: userIcon
        }).addTo(map);

        // Get and display address
        addressDisplay.innerHTML = '<i class="bi bi-hourglass-split"></i><span style="color: var(--text-gray);">Getting address...</span>';
        
        const address = await getAddressFromCoords(lat, lng);
        addressDisplay.innerHTML = `<i class="bi bi-geo-alt"></i><span>${address}</span>`;
        
        // Enable save button
        saveLocationBtn.disabled = false;
    }

    // Detect user's location
    function detectLocation() {
        clearAllErrors();
        
        if (!navigator.geolocation) {
            coordinatesError.textContent = 'Geolocation is not supported by your browser';
            coordinatesError.style.display = 'block';
            return;
        }

        useLocationBtn.disabled = true;
        useLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Detecting...';

        navigator.geolocation.getCurrentPosition(
            async function(position) {
                await updateLocation(position.coords.latitude, position.coords.longitude);
                useLocationBtn.disabled = false;
                useLocationBtn.innerHTML = '<i class="bi bi-crosshair"></i> Use My Location';
            },
            function(error) {
                useLocationBtn.disabled = false;
                useLocationBtn.innerHTML = '<i class="bi bi-crosshair"></i> Use My Location';
                
                let errorMessage = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Location access denied. Please enable location permissions.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Location request timed out. Please try again.';
                        break;
                    default:
                        errorMessage = 'An unknown error occurred while detecting your location.';
                }
                coordinatesError.textContent = errorMessage;
                coordinatesError.style.display = 'block';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // Save location to database
    async function saveLocation() {
        clearAllErrors();
        
        // Validate address fields
        const address = addressInput.value.trim();
        const city = cityInput.value.trim();
        const postalCode = postalCodeInput.value.trim();
        
        let hasError = false;
        
        if (!address) {
            showFieldError(addressInput, addressError, 'Please enter your full home address');
            hasError = true;
        }
        
        if (!city) {
            showFieldError(cityInput, cityError, 'Please enter your city');
            hasError = true;
        }
        
        if (!postalCode) {
            showFieldError(postalCodeInput, postalCodeError, 'Please enter your postal code');
            hasError = true;
        }
        
        if (!currentLat || !currentLng) {
            coordinatesError.textContent = 'Please select your location on the map or use "Use My Location"';
            coordinatesError.style.display = 'block';
            hasError = true;
        }
        
        if (hasError) {
            return;
        }

        saveLocationBtn.disabled = true;
        saveLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

        try {
            const response = await fetch('{{ route("customer.location.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    address: address,
                    city: city,
                    postal_code: postalCode,
                    latitude: currentLat,
                    longitude: currentLng
                })
            });

            const data = await response.json();

            if (data.success) {
                modal.style.display = 'none';
                // Optional: Show success message
            } else {
                // Show server error as coordinates error
                coordinatesError.textContent = data.message || 'Failed to save location';
                coordinatesError.style.display = 'block';
                saveLocationBtn.disabled = false;
                saveLocationBtn.innerHTML = '<i class="bi bi-save"></i> Save Location';
            }
        } catch (error) {
            console.error('Save error:', error);
            coordinatesError.textContent = 'An error occurred while saving your location';
            coordinatesError.style.display = 'block';
            saveLocationBtn.disabled = false;
            saveLocationBtn.innerHTML = '<i class="bi bi-save"></i> Save Location';
        }
    }

    // Event listeners
    useLocationBtn.addEventListener('click', function() {
        detectLocation();
    });

    saveLocationBtn.addEventListener('click', function() {
        saveLocation();
    });

    // Clear errors when user starts typing
    addressInput.addEventListener('input', function() {
        clearFieldError(addressInput, addressError);
    });

    cityInput.addEventListener('input', function() {
        clearFieldError(cityInput, cityError);
    });

    postalCodeInput.addEventListener('input', function() {
        clearFieldError(postalCodeInput, postalCodeError);
    });

    // Initialize map after a short delay
    setTimeout(initMap, 100);
});
</script>
@endif
