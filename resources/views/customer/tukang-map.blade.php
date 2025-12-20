<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Find Your Tukang! - Come & Fix</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Jost:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/css/components/navigation.css', 'resources/css/customer/tukang-map.css'])
</head>
<body>
    <div class="map-page-container">
        <!-- Full Screen Map -->
        <div id="map"></div>
        
        <!-- Navigation Overlay -->
        <div class="map-nav-overlay">
            @include('layouts.navigation')
        </div>
        
        <!-- Tukang List Sidebar -->
        <div class="tukang-list-sidebar">
            <!-- Header -->
            <div class="sidebar-header">
                <h1 class="sidebar-title">Find Your Tukang!</h1>
                <p class="sidebar-subtitle">
                    @if($serviceType)
                        Showing specialization for: <span class="specialization">{{ $serviceType }}</span>
                        <a href="{{ route('find-tukang') }}" class="show-all-btn">
                            Show All Tukang
                        </a>
                    @else
                        Showing all available tukangs near you
                    @endif
                </p>
            </div>
            
            <!-- Sort Section -->
            <div class="sort-section">
                <span class="sort-label">Sorted by</span>
                <div class="sort-dropdown">
                    <button class="sort-button" id="sortButton">
                        <span id="sortLabel">Distance</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="sort-menu" id="sortMenu">
                        <button class="sort-option active" data-sort="distance">
                            <i class="bi bi-geo-alt me-2"></i>Distance
                        </button>
                        <button class="sort-option" data-sort="rating">
                            <i class="bi bi-star me-2"></i>Rating
                        </button>
                    </div>
                </div>
                <button class="sort-direction-toggle" id="sortDirectionToggle" title="Toggle sort direction">
                    <i class="bi bi-sort-down" id="sortDirectionIcon"></i>
                </button>
            </div>
            
            <!-- Tukang List -->
            <div class="tukang-list" id="tukangList">
                <!-- Loading state -->
                <div class="empty-state" id="loadingState">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Finding tukangs near you...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map;
        let markers = {};
        let userMarker = null;
        let tukangsData = [];
        let currentSort = 'distance';
        let sortDirection = 'asc'; // 'asc' or 'desc'
        let selectedTukangId = null;
        let userLocation = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeMap();
            setupSortDropdown();
            setupSortDirectionToggle();
            getUserLocationAndLoadTukangs();
        });

        function initializeMap() {
            // Initialize map centered on Jakarta
            map = L.map('map', {
                zoomControl: false
            }).setView([-6.2000, 106.8167], 11);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add zoom control to bottom right
            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);
        }

        function getUserLocationAndLoadTukangs() {
            // Use customer's stored location from database
            @if($customer && $customer->latitude && $customer->longitude)
                userLocation = {
                    lat: {{ $customer->latitude }},
                    lng: {{ $customer->longitude }}
                };
                
                // Add user marker
                addUserMarker(userLocation.lat, userLocation.lng);
                
                // Center map on user location
                map.setView([userLocation.lat, userLocation.lng], 13);
                
                // Load tukangs with user location
                loadTukangs(userLocation.lat, userLocation.lng);
            @else
                // No stored location, load tukangs without user location
                console.warn('No customer location stored in database');
                loadTukangs();
            @endif
        }

        function addUserMarker(lat, lng) {
            const userIcon = L.divIcon({
                className: 'user-marker',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
            userMarker.bindPopup('<strong>Your Location</strong>');
        }

        function loadTukangs(userLat = null, userLng = null) {
            const serviceType = '{{ $serviceType }}';
            let apiUrl = '{{ route("api.tukangs") }}';
            
            const params = new URLSearchParams();
            if (serviceType) {
                params.append('service_type', serviceType);
            }
            if (userLat && userLng) {
                params.append('lat', userLat);
                params.append('lng', userLng);
            }
            
            if (params.toString()) {
                apiUrl += '?' + params.toString();
            }

            console.log('Fetching tukangs from:', apiUrl);

            fetch(apiUrl, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        console.error('Response is not JSON:', contentType);
                        return response.text().then(text => {
                            console.error('Response body:', text);
                            throw new Error('Response is not JSON');
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Tukangs data received:', data);
                    console.log('Number of tukangs:', data.length);
                    
                    tukangsData = data;
                    
                    if (tukangsData.length === 0) {
                        showEmptyState();
                    } else {
                        sortAndDisplayTukangs();
                        addTukangMarkers();
                    }
                })
                .catch(error => {
                    console.error('Error loading tukangs:', error);
                    showErrorState();
                });
        }

        function sortAndDisplayTukangs() {
            // Sort tukangs based on current sort option
            let sortedTukangs = [...tukangsData];
            
            switch(currentSort) {
                case 'distance':
                    sortedTukangs.sort((a, b) => {
                        const distA = a.distance || Infinity;
                        const distB = b.distance || Infinity;
                        return sortDirection === 'asc' ? distA - distB : distB - distA;
                    });
                    break;
                case 'rating':
                    sortedTukangs.sort((a, b) => {
                        const ratingA = parseFloat(a.rating) || 0;
                        const ratingB = parseFloat(b.rating) || 0;
                        return sortDirection === 'asc' ? ratingA - ratingB : ratingB - ratingA;
                    });
                    break;
            }
            
            displayTukangList(sortedTukangs);
        }

        function displayTukangList(tukangs) {
            const listContainer = document.getElementById('tukangList');
            
            if (tukangs.length === 0) {
                showEmptyState();
                return;
            }
            
            let html = '';
            tukangs.forEach(tukang => {
                const rating = parseFloat(tukang.rating) || 0;
                const reviews = tukang.total_reviews || 0;
                const initial = tukang.name.charAt(0).toUpperCase();
                const distance = tukang.distance !== null && tukang.distance !== undefined 
                    ? tukang.distance.toFixed(1) + ' km' 
                    : 'N/A';
                
                html += `
                    <div class="tukang-item ${selectedTukangId === tukang.id ? 'active' : ''}" 
                         data-tukang-id="${tukang.id}"
                         onclick="selectTukang(${tukang.id})">
                        <div class="tukang-avatar">
                            <img src="https://via.placeholder.com/48x48/${tukang.id % 2 === 0 ? 'FF9800' : '2196F3'}/ffffff?text=${initial}" 
                                 alt="${tukang.name}">
                        </div>
                        <div class="tukang-info">
                            <div class="tukang-name">${tukang.name}</div>
                            <div class="tukang-meta">
                                <div class="tukang-rating">
                                    <i class="bi bi-star-fill"></i>
                                    ${rating.toFixed(1)} (${reviews})
                                </div>
                                <div class="tukang-distance">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    ${distance}
                                </div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right tukang-arrow"></i>
                    </div>
                `;
            });
            
            listContainer.innerHTML = html;
        }

        function addTukangMarkers() {
            // Clear existing markers
            Object.values(markers).forEach(marker => map.removeLayer(marker));
            markers = {};
            
            // Add new markers
            tukangsData.forEach(tukang => {
                if (tukang.latitude && tukang.longitude) {
                    const markerIcon = L.divIcon({
                        className: 'custom-marker',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
                    });
                    
                    const marker = L.marker([tukang.latitude, tukang.longitude], { 
                        icon: markerIcon 
                    }).addTo(map);
                    
                    marker.on('click', function() {
                        selectTukang(tukang.id);
                    });
                    
                    markers[tukang.id] = marker;
                }
            });
        }

        function selectTukang(tukangId) {
            console.log('Tukang clicked:', tukangId);
            
            selectedTukangId = tukangId;
            
            // Update list items
            document.querySelectorAll('.tukang-item').forEach(item => {
                if (parseInt(item.dataset.tukangId) === tukangId) {
                    item.classList.add('active');
                    // Scroll to item
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    item.classList.remove('active');
                }
            });
            
            // Update markers
            Object.entries(markers).forEach(([id, marker]) => {
                const markerElement = marker.getElement();
                if (parseInt(id) === tukangId) {
                    markerElement.classList.add('active');
                    // Pan to marker
                    map.panTo(marker.getLatLng());
                } else {
                    markerElement.classList.remove('active');
                }
            });
        }

        function setupSortDropdown() {
            const sortButton = document.getElementById('sortButton');
            const sortMenu = document.getElementById('sortMenu');
            const sortLabel = document.getElementById('sortLabel');
            const sortOptions = document.querySelectorAll('.sort-option');
            
            // Toggle dropdown
            sortButton.addEventListener('click', function(e) {
                e.stopPropagation();
                sortMenu.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                sortMenu.classList.remove('active');
            });
            
            // Handle sort option selection
            sortOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    // Update active state
                    sortOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update sort type
                    currentSort = this.dataset.sort;
                    // Capitalize first letter
                    sortLabel.textContent = currentSort.charAt(0).toUpperCase() + currentSort.slice(1);
                    
                    // Re-sort and display
                    sortAndDisplayTukangs();
                    
                    // Close dropdown
                    sortMenu.classList.remove('active');
                });
            });
        }

        function setupSortDirectionToggle() {
            const toggleButton = document.getElementById('sortDirectionToggle');
            const toggleIcon = document.getElementById('sortDirectionIcon');
            
            toggleButton.addEventListener('click', function() {
                // Toggle direction
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                
                // Update icon
                if (sortDirection === 'asc') {
                    toggleIcon.className = 'bi bi-sort-down';
                } else {
                    toggleIcon.className = 'bi bi-sort-up';
                }
                
                // Re-sort and display
                sortAndDisplayTukangs();
            });
        }

        function showEmptyState() {
            const listContainer = document.getElementById('tukangList');
            const serviceType = '{{ $serviceType }}';
            
            listContainer.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-circle"></i>
                    <h5>No ${serviceType ? serviceType + ' Specialists' : 'Tukangs'} Found</h5>
                    <p>Try expanding your search or browse all services</p>
                    ${serviceType ? `<a href="{{ route('find-tukang') }}" class="btn btn-primary">Show All Tukangs</a>` : ''}
                </div>
            `;
        }

        function showErrorState() {
            const listContainer = document.getElementById('tukangList');
            
            listContainer.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <h5>Error Loading Tukangs</h5>
                    <p>Unable to load tukang data. Please try again.</p>
                    <button class="btn btn-primary" onclick="getUserLocationAndLoadTukangs()">Retry</button>
                </div>
            `;
        }
    </script>
</body>
</html>
