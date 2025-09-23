<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($serviceType)
                {{ __('Find ' . $serviceType . ' Specialists Near You') }}
            @else
                {{ __('Find Tukang Near You') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container-fluid">
            @if($serviceType)
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle"></i>
                    Showing Tukangs specialized in <strong>{{ $serviceType }}</strong>
                    <a href="{{ route('customer.find-tukang') }}" class="btn btn-sm btn-outline-primary ms-2">Show All</a>
                </div>
            @endif

            <div class="row">
                <!-- Map Container -->
                <div class="col-lg-8">
                    <div id="map" style="height: 600px; width: 100%;"></div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div id="tukang-sidebar" class="card shadow-sm" style="height: 600px; overflow-y: auto;">
                        <div class="card-body text-center text-muted">
                            <i class="bi bi-cursor-fill display-4 mb-3"></i>
                            <h5>Select a Tukang</h5>
                            <p>Click on a marker to view Tukang details and portfolio</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([-6.2000, 106.8167], 11);
            const sidebar = document.getElementById('tukang-sidebar');
            const serviceType = '{{ $serviceType }}';

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Build API URL with service filter
            let apiUrl = '{{ route("customer.api.tukangs") }}';
            if (serviceType) {
                apiUrl += '?service_type=' + encodeURIComponent(serviceType);
            }

            // Fetch filtered tukang data
            fetch(apiUrl)
                .then(response => response.json())
                .then(tukangs => {
                    if (tukangs.length === 0) {
                        sidebar.innerHTML = `
                            <div class="card-body text-center text-muted">
                                <i class="bi bi-exclamation-circle display-4 mb-3"></i>
                                <h5>No ${serviceType} Specialists Found</h5>
                                <p>Try expanding your search or browse all services</p>
                                <a href="{{ route('customer.find-tukang') }}" class="btn btn-primary">Show All Tukangs</a>
                            </div>
                        `;
                        return;
                    }

                    tukangs.forEach(t => {
                        if (t.latitude && t.longitude) {
                            const marker = L.marker([t.latitude, t.longitude]).addTo(map);
                            
                            marker.on('click', function() {
                                loadTukangProfile(t.id);
                                marker.bindPopup(`<strong>${t.name}</strong><br>Click to view portfolio`).openPopup();
                            });
                        }
                    });
                });

            // Rest of your existing JavaScript functions remain the same...
            function loadTukangProfile(tukangId) {
                sidebar.innerHTML = `
                    <div class="card-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading Tukang details...</p>
                    </div>
                `;

                fetch(`{{ url('/customer/api/tukangs') }}/${tukangId}`)
                    .then(response => response.json())
                    .then(tukang => {
                        displayTukangProfile(tukang);
                    })
                    .catch(error => {
                        sidebar.innerHTML = `
                            <div class="card-body text-center text-danger">
                                <i class="bi bi-exclamation-triangle display-4 mb-3"></i>
                                <h5>Error Loading Profile</h5>
                                <p>Unable to load Tukang details. Please try again.</p>
                            </div>
                        `;
                    });
            }

            function displayTukangProfile(tukang) {
                let portfolioHtml = '';
                
                if (tukang.portfolios && tukang.portfolios.length > 0) {
                    portfolioHtml = tukang.portfolios.map(p => `
                        <div class="card mb-3">
                            ${p.images && p.images.length > 0 ? `
                                <img src="${p.images[0].image_path}" 
                                     alt="${p.title}" 
                                     class="card-img-top" 
                                     style="height: 150px; object-fit: cover;">
                            ` : ''}
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold">${p.title}</h6>
                                <p class="card-text small text-muted">${p.description}</p>
                                <div class="row text-center">
                                    ${p.cost ? `
                                        <div class="col-6">
                                            <small class="text-muted">Cost</small>
                                            <div class="fw-bold text-success">Rp ${new Intl.NumberFormat('id-ID').format(p.cost)}</div>
                                        </div>
                                    ` : ''}
                                    ${p.duration_days ? `
                                        <div class="col-6">
                                            <small class="text-muted">Duration</small>
                                            <div class="fw-bold">${p.duration_days} days</div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    portfolioHtml = `
                        <div class="text-center py-4">
                            <i class="bi bi-images text-muted display-6 mb-2"></i>
                            <p class="text-muted">No portfolio items available</p>
                        </div>
                    `;
                }

                sidebar.innerHTML = `
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="https://via.placeholder.com/80x80/007bff/ffffff?text=${tukang.name.charAt(0)}" 
                                 alt="${tukang.name}" 
                                 class="rounded-circle mb-2" 
                                 style="width: 80px; height: 80px;">
                            <h5 class="fw-bold mb-1">${tukang.name}</h5>
                            <p class="text-muted small mb-2">${tukang.address || ''}</p>
                            <div class="text-warning mb-2">
                                <i class="bi bi-star-fill"></i>
                                <span class="text-muted ms-1">${tukang.rating || '0.0'} (${tukang.total_reviews || 0} reviews)</span>
                            </div>
                            ${tukang.is_available ? 
                                '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Available</span>' : 
                                '<span class="badge bg-secondary"><i class="bi bi-clock"></i> Busy</span>'
                            }
                        </div>

                        ${tukang.description ? `
                            <div class="mb-4">
                                <h6 class="fw-bold">About</h6>
                                <p class="small text-muted">${tukang.description}</p>
                            </div>
                        ` : ''}

                        ${tukang.specializations ? `
                            <div class="mb-4">
                                <h6 class="fw-bold">Specializations</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    ${tukang.specializations.map(spec => `
                                        <span class="badge bg-primary">${spec}</span>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}

                        <div class="mb-4">
                            <h6 class="fw-bold">Portfolio</h6>
                            ${portfolioHtml}
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-calendar-check"></i> Book Service
                            </button>
                            <button class="btn btn-outline-primary">
                                <i class="bi bi-chat"></i> Send Message
                            </button>
                        </div>
                    </div>
                `;
            }
        });
    </script>
</x-app-layout>