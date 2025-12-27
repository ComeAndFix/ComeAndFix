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
            <!-- Mobile-Only Back Button -->
            <div class="mobile-back-header">
                <a href="{{ route('dashboard') }}" class="mobile-back-btn">
                    <i class="bi bi-chevron-left"></i>
                    <span>Back</span>
                </a>
            </div>
            
            <!-- Header -->
            <div class="sidebar-header">
                <h1 class="sidebar-title">Find Your Tukang!</h1>
                <p class="sidebar-subtitle">
                    @if($serviceType)
                        Showing specialization for: <span class="specialization">{{ $serviceType }}</span>
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
        
        <!-- Tukang Details Popup -->
        <div class="tukang-popup" id="tukangPopup">
            <div class="popup-content">
                <div class="popup-loading" id="popupLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <div class="popup-body" id="popupBody" style="display: none;">
                    <!-- Close Button (Mobile) -->
                    <button class="popup-close-btn" onclick="closeTukangPopup()" aria-label="Close popup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    
                    <!-- Scrollable Content -->
                    <div class="popup-scrollable-area">
                        <!-- Profile Header -->
                        <div class="popup-header">
                            <div class="popup-avatar" id="popupAvatar"></div>
                            <div class="popup-header-info">
                                <h3 class="popup-name" id="popupName"></h3>
                                <div class="popup-rating" id="popupRating"></div>
                            </div>
                        </div>
                        
                        <!-- Specializations -->
                        <div class="popup-section">
                            <div class="popup-specializations" id="popupSpecializations"></div>
                        </div>
                        
                        <!-- Previous Works -->
                        <div class="popup-section">
                            <h4 class="popup-section-title">Previous Works</h4>
                            <div class="popup-portfolio" id="popupPortfolio"></div>
                        </div>
                    </div>

                    <!-- Fixed Footer -->
                    <div class="popup-footer">
                        <button class="popup-chat-btn" id="popupChatBtn">
                            <i class="bi bi-chat-dots"></i> Chat and Order
                        </button>
                    </div>
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
        const isMobile = window.innerWidth <= 768;

        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize map on desktop
            if (!isMobile) {
                initializeMap();
            }
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
            
            // Add map interaction listeners to close popup
            map.on('dragstart', closeTukangPopup);
            map.on('zoomstart', closeTukangPopup);
            map.on('click', function(e) {
                // Only close if clicking on the map itself, not on markers
                if (e.originalEvent.target === e.originalEvent.currentTarget) {
                    closeTukangPopup();
                }
            });
            
            // Reposition popup on window resize (handles zoom changes)
            window.addEventListener('resize', function() {
                if (selectedTukangId && !isMobile) {
                    const popup = document.getElementById('tukangPopup');
                    if (popup.classList.contains('active')) {
                        positionPopup(selectedTukangId);
                    }
                }
            });
        }

        function getUserLocationAndLoadTukangs() {
            // Use customer's stored location from database
            @if($customer && $customer->latitude && $customer->longitude)
                userLocation = {
                    lat: {{ $customer->latitude }},
                    lng: {{ $customer->longitude }}
                };
                
                // Add user marker (only on desktop)
                if (!isMobile) {
                    addUserMarker(userLocation.lat, userLocation.lng);
                    // Center map on user location
                    map.setView([userLocation.lat, userLocation.lng], 13);
                }
                
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
                        // Only add markers on desktop
                        if (!isMobile) {
                            addTukangMarkers();
                        }
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
                            ${tukang.profile_image_url ? 
                                `<img src="${tukang.profile_image_url}" alt="${tukang.name}">` : 
                                `<div class="nav-user-avatar-placeholder" style="display: flex; align-items: center; justify-content: center; background-color: var(--brand-orange); color: white; border-radius: 50%; width: 48px; height: 48px; font-weight: 600; font-size: 1.2rem;">${tukang.initials || initial}</div>`
                            }
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
            
            // Update markers and center map (only on desktop)
            if (!isMobile) {
                Object.entries(markers).forEach(([id, marker]) => {
                    const markerElement = marker.getElement();
                    if (parseInt(id) === tukangId) {
                        markerElement.classList.add('active');
                        marker.setZIndexOffset(1000); // Bring to front
                        // Pan to marker
                        map.panTo(marker.getLatLng());
                    } else {
                        markerElement.classList.remove('active');
                        marker.setZIndexOffset(0); // Reset
                    }
                });
            }
            
            // Show popup after a delay to allow map to center
            setTimeout(() => {
                showTukangPopup(tukangId);
            }, 300);
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
        
        function showTukangPopup(tukangId) {
            const popup = document.getElementById('tukangPopup');
            const popupLoading = document.getElementById('popupLoading');
            const popupBody = document.getElementById('popupBody');
            
            const isAlreadyActive = popup.classList.contains('active');
            
            if (isAlreadyActive) {
                // If switching, fade out the current content slightly first
                popupBody.style.opacity = '0.5';
                popupBody.style.transform = 'scale(0.98)';
                popupBody.style.transition = 'all 0.2s ease';
            } else {
                // First time opening
                popup.classList.add('active');
                popupLoading.style.display = 'flex';
                popupBody.style.display = 'none';
            }
            
            // Position popup (only on desktop, mobile uses fixed positioning)
            if (!isMobile) {
                positionPopup(tukangId);
            }
            
            // Fetch tukang details
            fetch(`/api/tukangs/${tukangId}`, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(tukang => {
                console.log('Tukang details:', tukang);
                populatePopup(tukang);
                
                // Reset styles and show content
                popupLoading.style.display = 'none';
                popupBody.style.display = 'flex';
                popupBody.style.opacity = '1';
                popupBody.style.transform = 'scale(1)';
                
                // Reposition popup after content is loaded and rendered (only on desktop)
                if (!isMobile) {
                    // Use setTimeout to ensure DOM has updated with actual content height
                    setTimeout(() => {
                        positionPopup(tukangId);
                    }, 50);
                }
            })
            .catch(error => {
                console.error('Error loading tukang details:', error);
                if (!isAlreadyActive) popup.classList.remove('active');
            });
        }
        
        function positionPopup(tukangId) {
            const popup = document.getElementById('tukangPopup');
            const popupContent = popup.querySelector('.popup-content');
            const marker = markers[tukangId];
            
            if (marker) {
                const markerLatLng = marker.getLatLng();
                const point = map.latLngToContainerPoint(markerLatLng);
                
                // Get popup dimensions
                const popupWidth = 420; // From CSS
                // Use the smaller of actual height or max-height constraint (85vh)
                const maxAllowedHeight = window.innerHeight * 0.85;
                const actualHeight = popupContent.offsetHeight;
                const popupHeight = actualHeight > 0 ? Math.min(actualHeight, maxAllowedHeight) : maxAllowedHeight;
                
                // Get viewport dimensions
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                
                // Calculate initial position (to the right of marker)
                let offsetX = 50; // Distance from marker
                let offsetY = -150; // Vertical centering adjustment
                
                let left = point.x + offsetX;
                let top = point.y + offsetY;
                
                // Boundary checking - ensure popup stays within viewport
                // Check right edge
                if (left + popupWidth > viewportWidth - 20) {
                    // Position to the left of marker instead
                    left = point.x - popupWidth - offsetX;
                }
                
                // Check left edge
                if (left < 20) {
                    left = 20;
                }
                
                // Check bottom edge - CRITICAL for preventing button cutoff
                if (top + popupHeight > viewportHeight - 20) {
                    top = viewportHeight - popupHeight - 20;
                }
                
                // Check top edge
                if (top < 20) {
                    top = 20;
                }
                
                popup.style.left = `${left}px`;
                popup.style.top = `${top}px`;
            }
        }
        
        function populatePopup(tukang) {
            const serviceType = '{{ $serviceType }}';
            
            // Avatar
            const initial = tukang.name.charAt(0).toUpperCase();
            if (tukang.profile_image_url) {
                document.getElementById('popupAvatar').innerHTML = `<img src="${tukang.profile_image_url}" alt="${tukang.name}">`;
            } else {
                document.getElementById('popupAvatar').innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; background-color: var(--brand-orange); color: white; border-radius: 50%; width: 100%; height: 100%; font-weight: 600; font-size: 2rem;">
                        ${tukang.initials || initial}
                    </div>`;
            }
            
            // Name
            document.getElementById('popupName').textContent = tukang.name;
            
            // Rating
            const rating = parseFloat(tukang.rating) || 0;
            const reviews = tukang.total_reviews || 0;
            document.getElementById('popupRating').innerHTML = `
                <i class="bi bi-star-fill"></i>
                ${rating.toFixed(1)} (${reviews} Reviews)
            `;
            
            // Specializations
            const specializations = Array.isArray(tukang.specializations) 
                ? tukang.specializations 
                : (tukang.specializations ? JSON.parse(tukang.specializations) : []);
            
            const specializationsHtml = specializations.map(spec => 
                `<span class="specialization-badge">${spec}</span>`
            ).join('');
            document.getElementById('popupSpecializations').innerHTML = specializationsHtml;
            
            // Portfolio
            const portfolioContainer = document.getElementById('popupPortfolio');
            if (tukang.portfolios && tukang.portfolios.length > 0) {
                const portfolioHtml = tukang.portfolios.slice(0, 3).map(portfolio => {
                    console.log('Portfolio item:', portfolio);
                    const imageUrl = portfolio.images && portfolio.images.length > 0 
                        ? portfolio.images[0].image_path
                        : 'https://via.placeholder.com/200x150/E0E0E0/757575?text=No+Image';
                    
                    return `
                        <div class="card mb-3 border-0 shadow-sm" style="overflow: hidden;">
                            <!-- Section 1: Details -->
                            <div class="card-body p-3 bg-white border-bottom">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <img src="${imageUrl}" class="rounded" style="width: 65px; height: 65px; object-fit: cover;" alt="${portfolio.title}">
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="small text-uppercase fw-bold text-primary mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Job Details</div>
                                        <h6 class="fw-bold text-dark mb-1 text-truncate">${portfolio.title}</h6>
                                        <p class="mb-0 text-muted small" style="font-size: 0.85rem; line-height: 1.3;">
                                            <i class="bi bi-chat-left-text me-1"></i> "${portfolio.description ? portfolio.description.substring(0, 80) + (portfolio.description.length > 80 ? '...' : '') : ''}"
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Rating & Review -->
                            <div class="card-footer p-3 bg-light border-0">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-uppercase fw-bold text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px;">Customer Feedback</span>
                                        ${portfolio.rating ? `
                                            <div class="d-flex align-items-center text-warning small">
                                                ${[...Array(5)].map((_, i) => {
                                                    const rating = parseFloat(portfolio.rating);
                                                    if (i < Math.floor(rating)) return '<i class="bi bi-star-fill"></i>';
                                                    if (i === Math.floor(rating) && rating % 1 >= 0.5) return '<i class="bi bi-star-half"></i>';
                                                    return '<i class="bi bi-star"></i>';
                                                }).join('')}
                                                <span class="text-muted ms-2 fw-semibold" style="font-size: 0.8rem;">(${parseFloat(portfolio.rating).toFixed(1)})</span>
                                            </div>
                                        ` : '<span class="text-muted small fst-italic">No Rating</span>'}
                                    </div>
                                    <div class="review-text">
                                        ${portfolio.review_comment ? `
                                            <p class="mb-0 small text-dark fst-italic">
                                                "${portfolio.review_comment}"
                                            </p>
                                        ` : '<p class="mb-0 small text-muted fst-italic">No written review provided.</p>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                portfolioContainer.innerHTML = portfolioHtml;
            } else {
                portfolioContainer.innerHTML = '<p class="no-portfolio">No previous work available</p>';
            }
            
            // Chat button
            const chatBtn = document.getElementById('popupChatBtn');
            chatBtn.onclick = function() {
                openChat(tukang.id);
            };
        }
        
        function closeTukangPopup() {
            const popup = document.getElementById('tukangPopup');
            popup.classList.remove('active');
        }
        
        function openChat(tukangId) {
            const serviceType = '{{ $serviceType }}';
            let url = `/chat/tukang/${tukangId}`;
            if (serviceType) {
                url += `?service_type=${encodeURIComponent(serviceType)}`;
            }
            window.location.href = url;
        }
    </script>
</body>
</html>
