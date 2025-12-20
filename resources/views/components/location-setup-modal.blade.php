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
            <div id="locationError" class="location-error" style="display: none;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div class="location-error-text"></div>
            </div>

            <!-- Address Input Fields -->
            <div class="location-form-section">
                <div class="location-form-label">Address Information</div>
                
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

                <div class="location-input-row">
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
                </div>
            </div>

            <!-- Map Section -->
            <div class="location-form-section">
                <div class="location-form-label">Pin Your Location on Map</div>
                
                <div class="location-map-container">
                    <div id="locationMap" style="width: 100%; height: 100%;"></div>
                    <div class="location-map-loading" id="mapLoading">
                        <div class="location-spinner"></div>
                        <p style="color: var(--text-gray); font-size: 0.875rem; font-family: 'Inter', sans-serif;">
                            Loading map...
                        </p>
                    </div>
                </div>

                <div class="location-address-display">
                    <div class="location-address-label">Detected Coordinates</div>
                    <div class="location-address-text" id="locationAddress">
                        <i class="bi bi-geo-alt"></i>
                        <span class="location-address-placeholder">Click "Detect My Location" to get GPS coordinates</span>
                    </div>
                </div>
            </div>

            <div class="location-modal-actions">
                <button type="button" class="location-btn location-btn-secondary" id="skipLocationBtn">
                    Skip for Now
                </button>
                <button type="button" class="location-btn location-btn-primary" id="detectLocationBtn">
                    <i class="bi bi-crosshair"></i> Detect My Location
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
    const errorDisplay = document.getElementById('locationError');
    const errorText = errorDisplay.querySelector('.location-error-text');
    const detectBtn = document.getElementById('detectLocationBtn');
    const skipBtn = document.getElementById('skipLocationBtn');
    
    // Address input fields
    const addressInput = document.getElementById('addressInput');
    const cityInput = document.getElementById('cityInput');
    const postalCodeInput = document.getElementById('postalCodeInput');
    
    let map = null;
    let marker = null;
    let currentLat = null;
    let currentLng = null;
    let locationDetected = false;

    // Initialize map with default view (Jakarta)
    function initMap() {
        map = L.map(mapElement).setView([-6.2088, 106.8456], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        mapLoading.style.display = 'none';
    }

    // Show error message
    function showError(message) {
        errorText.textContent = message;
        errorDisplay.style.display = 'flex';
    }

    // Hide error message
    function hideError() {
        errorDisplay.style.display = 'none';
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
        locationDetected = true;

        // Update map view
        map.setView([lat, lng], 15);

        // Remove old marker if exists
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);

        // Get and display address
        detectBtn.disabled = true;
        detectBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Getting coordinates...';
        
        const address = await getAddressFromCoords(lat, lng);
        addressDisplay.innerHTML = `<i class="bi bi-geo-alt"></i><span>${address}</span>`;
        
        detectBtn.disabled = false;
        detectBtn.innerHTML = '<i class="bi bi-save"></i> Save Location';
    }

    // Detect user's location
    function detectLocation() {
        hideError();
        
        if (!navigator.geolocation) {
            showError('Geolocation is not supported by your browser');
            return;
        }

        detectBtn.disabled = true;
        detectBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Detecting...';

        navigator.geolocation.getCurrentPosition(
            async function(position) {
                await updateLocation(position.coords.latitude, position.coords.longitude);
            },
            function(error) {
                detectBtn.disabled = false;
                detectBtn.innerHTML = '<i class="bi bi-crosshair"></i> Use My Location';
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        showError('Location access denied. Please enable location permissions in your browser settings.');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        showError('Location information is unavailable.');
                        break;
                    case error.TIMEOUT:
                        showError('Location request timed out. Please try again.');
                        break;
                    default:
                        showError('An unknown error occurred while detecting your location.');
                }
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
        hideError();
        
        // Validate address fields
        const address = addressInput.value.trim();
        const city = cityInput.value.trim();
        const postalCode = postalCodeInput.value.trim();
        
        if (!address) {
            showError('Please enter your full home address');
            addressInput.focus();
            return;
        }
        
        if (!city) {
            showError('Please enter your city');
            cityInput.focus();
            return;
        }
        
        if (!postalCode) {
            showError('Please enter your postal code');
            postalCodeInput.focus();
            return;
        }
        
        if (!currentLat || !currentLng) {
            showError('Please detect your GPS location by clicking "Detect My Location"');
            return;
        }

        detectBtn.disabled = true;
        detectBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

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
                showError(data.message || 'Failed to save location');
                detectBtn.disabled = false;
                detectBtn.innerHTML = '<i class="bi bi-save"></i> Save Location';
            }
        } catch (error) {
            console.error('Save error:', error);
            showError('An error occurred while saving your location');
            detectBtn.disabled = false;
            detectBtn.innerHTML = '<i class="bi bi-save"></i> Save Location';
        }
    }

    // Event listeners
    detectBtn.addEventListener('click', function() {
        if (locationDetected) {
            saveLocation();
        } else {
            detectLocation();
        }
    });

    skipBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Initialize map after a short delay
    setTimeout(initMap, 100);
});
</script>
@endif
