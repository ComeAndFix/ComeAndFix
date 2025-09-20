<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Header -->
        <section class="bg-primary text-white py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="h3 fw-bold mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            Find Available Tukangs
                        </h1>
                        <p class="mb-0">
                            @if($serviceType)
                                Showing tukangs specialized in: <span class="badge bg-light text-dark">{{ $serviceType }}</span>
                            @else
                                Browse all available tukangs in your area
                            @endif
                        </p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <button id="locate-me" class="btn btn-outline-light">
                            <i class="bi bi-crosshair me-1"></i>
                            Find My Location
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map and Details -->
        <section class="py-0">
            <div class="container-fluid">
                <div class="row g-0">
                    <!-- Map -->
                    <div class="col-lg-8">
                        <div id="map" style="height: 600px; width: 100%;"></div>
                    </div>
                    
                    <!-- Tukang Details Sidebar -->
                    <div class="col-lg-4">
                        <div class="p-4 h-100" style="max-height: 600px; overflow-y: auto; background-color: #f8f9fa;">
                            <div id="tukang-list">
                                <div class="text-center py-5">
                                    <i class="bi bi-search display-4 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Loading tukangs...</h5>
                                    <div class="spinner-border text-primary mt-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="tukang-details" style="display: none;">
                                <!-- Tukang details will be loaded here -->
                            </div>

                            <div id="back-to-list" style="display: none;">
                                <button class="btn btn-outline-secondary btn-sm mb-3" onclick="showTukangList()">
                                    <i class="bi bi-arrow-left me-1"></i> Back to List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Portfolio Modal -->
    <div class="modal fade" id="portfolioModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-images me-2"></i>
                        Portfolio
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="portfolio-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <!-- Leaflet CSS (OpenStreetMap) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>

    <style>
        .tukang-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #dee2e6;
        }
        
        .tukang-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            border-color: #007bff;
        }

        .tukang-card.selected {
            border-color: #007bff;
            background-color: #e7f1ff;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .leaflet-popup-content {
            margin: 12px 16px;
            font-size: 14px;
            line-height: 1.4;
        }

        .custom-marker {
            cursor: pointer;
        }

        .portfolio-image {
            transition: transform 0.2s ease;
        }

        .portfolio-image:hover {
            transform: scale(1.05);
        }

        #map {
            border-radius: 0;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- Leaflet JavaScript (OpenStreetMap) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    <script>
        let map;
        let markers = [];
        let userLocationMarker = null;
        let userLocation = null;
        let selectedTukang = null;
        let allTukangs = [];

        // Initialize map with Leaflet and OpenStreetMap
        function initMap() {
            // Default to Jakarta coordinates
            const defaultLocation = [-6.2088, 106.8456];
            
            map = L.map('map').setView(defaultLocation, 12);
            
            // Add OpenStreetMap tiles (completely free!)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Load tukangs when map is ready
            loadTukangs();
        }

        function loadTukangs() {
            const params = new URLSearchParams({
                service_type: '{{ $serviceType ?? '' }}'
            });

            if (userLocation) {
                params.append('lat', userLocation[0]);
                params.append('lng', userLocation[1]);
            }

            fetch(`{{ route('customer.api.tukangs') }}?${params}`)
                .then(response => response.json())
                .then(tukangs => {
                    allTukangs = tukangs;
                    clearMarkers();
                    
                    tukangs.forEach(tukang => {
                        addTukangMarker(tukang);
                    });

                    updateTukangList(tukangs);

                    // Fit map to show all markers if we have tukangs
                    if (tukangs.length > 0) {
                        const group = new L.featureGroup(markers);
                        if (userLocationMarker) {
                            group.addLayer(userLocationMarker);
                        }
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                })
                .catch(error => {
                    console.error('Error loading tukangs:', error);
                    document.getElementById('tukang-list').innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                            <h5 class="mt-3 text-danger">Error loading tukangs</h5>
                            <p class="text-muted">Please try refreshing the page</p>
                        </div>
                    `;
                });
        }

        function addTukangMarker(tukang) {
            // Custom marker icon for tukangs
            const tukangIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="
                    background: linear-gradient(135deg, #007bff, #0056b3);
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    border: 3px solid white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                    font-size: 12px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    cursor: pointer;
                ">${tukang.name.charAt(0).toUpperCase()}</div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });

            const marker = L.marker([parseFloat(tukang.latitude), parseFloat(tukang.longitude)], {
                icon: tukangIcon
            }).addTo(map);

            // Popup content
            const popupContent = `
                <div style="min-width: 200px;">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(tukang.name)}&background=007bff&color=fff&size=40" 
                             class="rounded-circle me-2" alt="${tukang.name}" width="40" height="40">
                        <div>
                            <h6 class="fw-bold mb-0">${tukang.name}</h6>
                            <small class="text-muted">${tukang.city}</small>
                        </div>
                    </div>
                    ${tukang.distance ? `<p class="small text-success mb-2"><i class="bi bi-geo-alt"></i> ${tukang.distance} km away</p>` : ''}
                    <div class="mb-2">
                        ${tukang.specializations.slice(0, 2).map(spec => 
                            `<span class="badge bg-primary" style="font-size: 10px;">${spec}</span>`
                        ).join(' ')}
                        ${tukang.specializations.length > 2 ? `<span class="badge bg-secondary" style="font-size: 10px;">+${tukang.specializations.length - 2} more</span>` : ''}
                    </div>
                    <button class="btn btn-primary btn-sm w-100" onclick="showTukangDetails(${JSON.stringify(tukang).replace(/"/g, '&quot;')})">
                        <i class="bi bi-eye me-1"></i> View Details
                    </button>
                </div>
            `;

            marker.bindPopup(popupContent, {
                maxWidth: 250,
                className: 'custom-popup'
            });
            
            marker.on('click', () => {
                showTukangDetails(tukang);
                highlightTukangInList(tukang.id);
            });

            markers.push(marker);
        }

        function clearMarkers() {
            markers.forEach(marker => {
                map.removeLayer(marker);
            });
            markers = [];
        }

        function showTukangDetails(tukang) {
            selectedTukang = tukang;
            
            const detailsHtml = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(tukang.name)}&background=007bff&color=fff&size=60" 
                                 class="rounded-circle me-3" alt="${tukang.name}" width="60" height="60">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">${tukang.name}</h5>
                                <p class="text-muted mb-1"><i class="bi bi-geo-alt"></i> ${tukang.city}</p>
                                ${tukang.distance ? `<small class="text-success"><i class="bi bi-pin-map"></i> ${tukang.distance} km away</small>` : ''}
                            </div>
                            <div class="text-end">
                                <span class="badge ${tukang.is_available ? 'bg-success' : 'bg-secondary'} mb-1">
                                    ${tukang.is_available ? 'Available' : 'Busy'}
                                </span>
                                <br>
                                <small class="text-muted">${tukang.years_experience} years exp.</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-tools"></i> Specializations:</h6>
                            <div>
                                ${tukang.specializations.map(spec => 
                                    `<span class="badge bg-primary me-1 mb-1">${spec}</span>`
                                ).join('')}
                            </div>
                        </div>
                        
                        ${tukang.description ? `
                            <div class="mb-3">
                                <h6><i class="bi bi-info-circle"></i> About:</h6>
                                <p class="small">${tukang.description}</p>
                            </div>
                        ` : ''}
                        
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-primary w-100" onclick="viewPortfolio(${tukang.id})">
                                    <i class="bi bi-images me-1"></i>
                                    Portfolio
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success w-100" onclick="contactTukang(${tukang.id})">
                                    <i class="bi bi-chat-dots me-1"></i>
                                    Contact
                                </button>
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-2">
                            <div class="col-12">
                                <button class="btn btn-outline-primary w-100" onclick="centerMapOnTukang(${tukang.latitude}, ${tukang.longitude})">
                                    <i class="bi bi-map me-1"></i>
                                    Show on Map
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('tukang-list').style.display = 'none';
            document.getElementById('tukang-details').innerHTML = detailsHtml;
            document.getElementById('tukang-details').style.display = 'block';
            document.getElementById('back-to-list').style.display = 'block';
        }

        function showTukangList() {
            document.getElementById('tukang-details').style.display = 'none';
            document.getElementById('back-to-list').style.display = 'none';
            document.getElementById('tukang-list').style.display = 'block';
            selectedTukang = null;
            
            // Remove highlighting from all cards
            document.querySelectorAll('.tukang-card').forEach(card => {
                card.classList.remove('selected');
            });
        }

        function highlightTukangInList(tukangId) {
            // Remove previous highlighting
            document.querySelectorAll('.tukang-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add highlighting to selected card
            const selectedCard = document.querySelector(`[data-tukang-id="${tukangId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
        }

        function centerMapOnTukang(lat, lng) {
            map.setView([lat, lng], 16);
            
            // Find and open the popup for this tukang
            markers.forEach(marker => {
                const markerLatLng = marker.getLatLng();
                if (Math.abs(markerLatLng.lat - lat) < 0.0001 && Math.abs(markerLatLng.lng - lng) < 0.0001) {
                    marker.openPopup();
                }
            });
        }

        function updateTukangList(tukangs) {
            if (tukangs.length === 0) {
                document.getElementById('tukang-list').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-exclamation-circle display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">No tukangs found</h5>
                        <p class="text-muted">Try adjusting your search criteria or expanding your search area</p>
                    </div>
                `;
                return;
            }

            const listHtml = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Available Tukangs</h5>
                    <span class="badge bg-primary">${tukangs.length} found</span>
                </div>
                <div class="row g-2">
                    ${tukangs.map((tukang, index) => `
                        <div class="col-12">
                            <div class="card tukang-card h-100" data-tukang-id="${tukang.id}" onclick="showTukangDetails(${JSON.stringify(tukang).replace(/"/g, '&quot;')})">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(tukang.name)}&background=007bff&color=fff&size=40" 
                                             class="rounded-circle me-2" alt="${tukang.name}" width="40" height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">${tukang.name}</h6>
                                            <small class="text-muted"><i class="bi bi-geo-alt"></i> ${tukang.city}</small>
                                            ${tukang.distance ? `<br><small class="text-success"><i class="bi bi-pin-map"></i> ${tukang.distance} km away</small>` : ''}
                                        </div>
                                        <div class="text-end">
                                            <span class="badge ${tukang.is_available ? 'bg-success' : 'bg-secondary'}" style="font-size: 10px;">
                                                ${tukang.is_available ? 'Available' : 'Busy'}
                                            </span>
                                            <br>
                                            <small class="text-muted">${tukang.years_experience}y exp.</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        ${tukang.specializations.slice(0, 3).map(spec => 
                                            `<span class="badge bg-light text-dark me-1" style="font-size: 9px;">${spec}</span>`
                                        ).join('')}
                                        ${tukang.specializations.length > 3 ? `<span class="badge bg-secondary" style="font-size: 9px;">+${tukang.specializations.length - 3}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            
            document.getElementById('tukang-list').innerHTML = listHtml;
        }

        function viewPortfolio(tukangId) {
            // Show loading in modal
            document.getElementById('portfolio-content').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading portfolio...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading portfolio...</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('portfolioModal'));
            modal.show();

            fetch(`{{ url('customer/api/tukangs') }}/${tukangId}`)
                .then(response => response.json())
                .then(tukang => {
                    const portfolioHtml = generatePortfolioHtml(tukang.portfolios);
                    document.getElementById('portfolio-content').innerHTML = portfolioHtml;
                })
                .catch(error => {
                    console.error('Error loading portfolio:', error);
                    document.getElementById('portfolio-content').innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                            <h5 class="mt-3 text-danger">Error loading portfolio</h5>
                            <p class="text-muted">Please try again later</p>
                        </div>
                    `;
                });
        }

        function generatePortfolioHtml(portfolios) {
            if (!portfolios || portfolios.length === 0) {
                return `
                    <div class="text-center py-5">
                        <i class="bi bi-images display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No portfolio items yet</h5>
                        <p class="text-muted">This tukang hasn't uploaded any portfolio items yet.</p>
                    </div>
                `;
            }

            return `
                <div class="row g-4">
                    ${portfolios.map(portfolio => `
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                ${portfolio.images && portfolio.images.length > 0 ? `
                                    <div class="position-relative overflow-hidden" style="height: 200px;">
                                        <img src="${portfolio.images[0].image_path}" 
                                             class="card-img-top portfolio-image w-100 h-100" 
                                             style="object-fit: cover; cursor: pointer;"
                                             alt="${portfolio.images[0].alt_text || portfolio.title}"
                                             onclick="showImageGallery(${JSON.stringify(portfolio.images).replace(/"/g, '&quot;')})">
                                        ${portfolio.images.length > 1 ? `
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-dark bg-opacity-75">
                                                    <i class="bi bi-images"></i> ${portfolio.images.length}
                                                </span>
                                            </div>
                                        ` : ''}
                                    </div>
                                ` : `
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                        <i class="bi bi-image display-4 text-muted"></i>
                                    </div>
                                `}
                                
                                <div class="card-body">
                                    <h5 class="card-title">${portfolio.title}</h5>
                                    ${portfolio.description ? `<p class="card-text">${portfolio.description}</p>` : ''}
                                    
                                    <div class="row g-2 mt-2">
                                        ${portfolio.cost ? `
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-currency-dollar text-success me-1"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Cost</small>
                                                        <strong>$${parseFloat(portfolio.cost).toLocaleString()}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        ` : ''}
                                        ${portfolio.duration_days ? `
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-clock text-info me-1"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Duration</small>
                                                        <strong>${portfolio.duration_days} days</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        ` : ''}
                                        ${portfolio.completed_at ? `
                                            <div class="col-12 mt-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar-check text-primary me-1"></i>
                                                    <small class="text-muted">Completed: ${new Date(portfolio.completed_at).toLocaleDateString()}</small>
                                                </div>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        function showImageGallery(images) {
            // You can implement a lightbox gallery here
            // For now, just open the first image in a new tab
            if (images && images.length > 0) {
                window.open(images[0].image_path, '_blank');
            }
        }

        function contactTukang(tukangId) {
            // Implement contact functionality
            alert('Contact functionality will be implemented here');
        }

        // Locate user
        document.getElementById('locate-me').addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Finding...';
            button.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = [position.coords.latitude, position.coords.longitude];
                    
                    // Remove existing user location marker
                    if (userLocationMarker) {
                        map.removeLayer(userLocationMarker);
                    }
                    
                    // Add user location marker
                    const userIcon = L.divIcon({
                        className: 'user-marker',
                        html: `<div style="
                            background: linear-gradient(135deg, #28a745, #20c997);
                            width: 24px;
                            height: 24px;
                            border-radius: 50%;
                            border: 3px solid white;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                            position: relative;
                        ">
                            <div style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 8px;
                                height: 8px;
                                background: white;
                                border-radius: 50%;
                            "></div>
                        </div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });

                    userLocationMarker = L.marker(userLocation, { icon: userIcon })
                        .addTo(map)
                        .bindPopup('<strong>Your Location</strong>')
                        .openPopup();
                    
                    map.setView(userLocation, 14);
                    
                    // Reload tukangs with user location
                    loadTukangs();

                    button.innerHTML = '<i class="bi bi-check me-1"></i>Located!';
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 2000);
                    
                }, function(error) {
                    console.error('Geolocation error:', error);
                    button.innerHTML = '<i class="bi bi-x me-1"></i>Failed';
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 2000);
                    
                    let errorMessage = 'Unable to get your location.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please enable location permissions.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                    }
                    alert(errorMessage);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                });
            } else {
                button.innerHTML = '<i class="bi bi-x me-1"></i>Not supported';
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
                alert('Geolocation is not supported by this browser.');
            }
        });

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (map) {
                map.invalidateSize();
            }
        });
    </script>
    @endpush
</x-app-layout>