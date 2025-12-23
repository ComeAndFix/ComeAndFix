@push('styles')
    @vite(['resources/css/tukang/dashboard.css'])
    <style>
        body {
            background: #F1F2F4 !important;
        }
        /* Custom scrollbar for requests list */
        .requests-scroll {
            max-height: 400px;
            overflow-y: auto;
        }
        .requests-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .requests-scroll::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }
    </style>
@endpush

<x-app-layout>
    <!-- Top Section: Hero + Active Job (Light Gray Background) -->
    <div style="background: #F1F2F4; padding-bottom: 2rem; margin-top: -2rem; padding-top: 2rem;">
        
        <!-- Hero Section -->
        <section class="hero-section" style="background-image: url('{{ asset('images/workshop-tools.png') }}');" role="banner">
            <div class="hero-content w-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="hero-greeting">Hello! Welcome back,</p>
                        <h1 class="hero-name">{{ strtoupper(Auth::guard('tukang')->user()->name) }}</h1>
                    </div>
                    
                    <!-- Availability Toggle -->
                    <button id="availability-toggle" class="availability-btn d-flex align-items-center gap-2 px-4 py-2 shadow-sm" 
                            style="border: 2px solid transparent; transition: all 0.3s; font-size: 1.1rem;"
                            onclick="toggleAvailability()">
                        <div id="availability-indicator" 
                             class="shadow-sm"
                             style="width: 14px; height: 14px; border-radius: 50%; background-color: {{ Auth::guard('tukang')->user()->is_available ? '#10B981' : '#EF4444' }}; transition: background-color 0.3s;"></div>
                        <span id="availability-text" class="fw-bold" style="color: var(--brand-dark);">
                            {{ Auth::guard('tukang')->user()->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </button>
                </div>
            </div>
        </section>

        <div class="container" style="max-width: 1200px;">
            <!-- Active Job Indicator -->
            @if($activeJob)
            <section class="mb-5" aria-label="Active Job">
                <h2 class="section-title">Active Job</h2>
                
                <a href="{{ route('tukang.jobs.show', $activeJob) }}" class="order-card" aria-label="View job details">
                    <div class="order-info">
                        <p class="order-type-label">Current Job</p>
                        <h3 class="order-type">{{ $activeJob->service->name }}</h3>
                        
                        <div class="order-badges">
                            <span class="order-badge status" role="status">{{ ucwords(str_replace('_', ' ', $activeJob->status)) }}</span>
                            @if($activeJob->payment_status == 'paid')
                            <span class="order-badge payment" role="status">Paid</span>
                            @endif
                        </div>
                        
                        <div class="order-customer">
                            <img src="{{ $activeJob->customer->profile_photo_url ?? asset('images/default-avatar.png') }}" class="customer-avatar" alt="Customer">
                            <div>
                                <p class="customer-label">Customer</p>
                                <p class="customer-name">{{ $activeJob->customer->name }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-arrow" aria-hidden="true">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
            </section>
            @endif

            <!-- Main Dashboard Grid -->
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8 d-flex flex-column">
                    <!-- Incoming Job Requests -->
                    <div class="dashboard-card flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-inbox me-2 text-primary"></i>Incoming Requests
                            </h5>
                            <!-- Simple Filter -->
                            <form method="GET" action="{{ route('tukang.dashboard') }}" class="d-flex gap-2">
                                <select name="service_filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                    <option value="">All Services</option>
                                    @foreach($availableServices as $service)
                                        <option value="{{ $service->id }}" {{ request('service_filter') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <div class="requests-list requests-scroll" id="job-requests-list">
                            @forelse($jobRequests as $request)
                                <div class="request-card" 
                                     onclick="openChatWithService('customer', {{ $request->sender_id }}, {{ $request->conversation_service_id ?? 'null' }})">
                                    
                                    <!-- Left: Avatar -->
                                    <div class="request-avatar me-3">
                                        {{ substr($request->sender->name, 0, 1) }}
                                    </div>

                                    <!-- Middle: Content -->
                                    <div class="flex-grow-1">
                                        <div class="request-header">
                                            <h6 class="request-name mb-0">{{ $request->sender->name }}</h6>
                                            <small class="request-time">{{ $request->created_at->diffForHumans() }}</small>
                                        </div>

                                        @if($request->service)
                                            <div class="service-badge">
                                                <i class="bi bi-wrench"></i>
                                                {{ $request->service->name }}
                                            </div>
                                        @else
                                            <div class="service-badge" style="background: rgba(107, 114, 128, 0.1); color: #4b5563;">
                                                <i class="bi bi-chat-dots"></i>
                                                General Inquiry
                                            </div>
                                        @endif
                                        
                                        <p class="request-message mb-1">
                                            {{ $request->message }}
                                        </p>

                                        @if($request->sender->city)
                                            <div class="city-badge">
                                                <i class="bi bi-geo-alt"></i> {{ $request->sender->city }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Right: Arrow -->
                                    <div class="ms-3 align-self-center">
                                        <i class="bi bi-chevron-right request-arrow"></i>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2.5rem; opacity: 0.7;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">No New Requests</h6>
                                    <p class="text-muted small">You're all caught up! Check back later.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4 d-flex flex-column">
                    <!-- Finance Info (Wallet) -->
                    <a href="{{ route('tukang.finance.index') }}" class="text-decoration-none d-block mb-4">
                        <div class="dashboard-card finance-card">
                            <i class="bi bi-wallet2 display-6 mb-4 d-block"></i>
                            <p class="finance-label">Total Earnings</p>
                            <h3 class="finance-amount">Rp {{ number_format($walletBalance, 0, ',', '.') }}</h3>
                            
                            <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="small opacity-75">This Month</span>
                                    <div class="fw-bold">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
                                </div>
                                <i class="bi bi-arrow-right-circle fs-4"></i>
                            </div>
                        </div>
                    </a>

                    <!-- Job Schedule (Calendar) - Moved to Right Column -->
                    <div class="dashboard-card flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-calendar-check me-2 text-warning"></i>Job Schedule
                            </h5>
                        </div>
                        <div id="job-calendar" class="calendar-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openChatWithService(receiverType, receiverId, serviceId) {
            let url = `/tukang/chat/${receiverType}/${receiverId}`;
            if (serviceId) {
                url += `?service_id=${serviceId}`;
            }
            window.location.href = url;
        }

        // Availability Toggle
        function toggleAvailability() {
            const btn = document.getElementById('availability-toggle');
            const indicator = document.getElementById('availability-indicator');
            const text = document.getElementById('availability-text');
            
            // Get current state
            const isCurrentlyAvailable = text.textContent.trim() === 'Available';
            
            // Confirm dialog
            const action = isCurrentlyAvailable ? 'make yourself UNAVAILABLE' : 'make yourself AVAILABLE';
            if (!confirm(`Are you sure you want to ${action}? This will affect your visibility to customers.`)) {
                return;
            }

            const newState = !isCurrentlyAvailable;
            
            // Optimistic update
            if (newState) {
                indicator.style.backgroundColor = '#10B981';
                text.textContent = 'Available';
            } else {
                indicator.style.backgroundColor = '#EF4444';
                text.textContent = 'Unavailable';
            }
            
            fetch('{{ route('tukang.toggle.availability') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Confirmed, do nothing
                } else {
                    // Revert if failed
                    if (!newState) {
                        indicator.style.backgroundColor = '#10B981';
                        text.textContent = 'Available';
                    } else {
                        indicator.style.backgroundColor = '#EF4444';
                        text.textContent = 'Unavailable';
                    }
                    alert('Failed to update availability. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert on error
                if (!newState) {
                        indicator.style.backgroundColor = '#10B981';
                        text.textContent = 'Available';
                    } else {
                        indicator.style.backgroundColor = '#EF4444';
                        text.textContent = 'Unavailable';
                    }
                alert('An error occurred. Please check your connection.');
            });
        }

        // Calendar Implementation
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
        });

        const scheduledJobs = @json($scheduledJobs);
        // Create a route template string where we can replace the placeholder
        const jobRouteBase = "{{ route('tukang.jobs.show', ['order' => 'PLACEHOLDER']) }}";

        function renderCalendar() {
            const calendar = document.getElementById('job-calendar');
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = firstDay.getDay(); // 0 = Sunday

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            let html = `<div class="calendar-header mb-3 fw-bold">${monthNames[month]} ${year}</div>`;

            html += '<div class="calendar-grid">';
            ['S', 'M', 'T', 'W', 'T', 'F', 'S'].forEach(day => {
                html += `<div class="calendar-day-header text-center small py-1">${day}</div>`;
            });

            for (let i = 0; i < startDay; i++) {
                html += '<div></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                const hasJob = scheduledJobs[dateStr] !== undefined;
                const jobClass = hasJob ? 'has-job shadow-sm' : '';
                
                // Add click handler for days with jobs
                let clickAttr = '';
                if (hasJob) {
                    try {
                        const link = getJobLink(scheduledJobs[dateStr]);
                        clickAttr = `onclick="window.location.href='${link}'"`;
                    } catch (e) {
                        console.error('Error generating link for date', dateStr, e);
                    }
                }
                
                const cursorStyle = hasJob ? 'cursor: pointer;' : '';
                
                // Add tooltip logic
                let tooltipHtml = '';
                if (hasJob) {
                    const jobs = scheduledJobs[dateStr];
                    tooltipHtml = `<div class="job-tooltip text-start">`;
                    
                    // Limit to 3 jobs to prevent tooltip from getting too huge
                    const displayJobs = jobs.slice(0, 3);
                    
                    displayJobs.forEach((job, index) => {
                         const date = new Date(job.work_datetime);
                         const time = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                         
                         const mbClass = index < displayJobs.length - 1 ? 'mb-2 pb-2 border-bottom border-secondary' : '';
                         
                         tooltipHtml += `
                            <div class="${mbClass}">
                                <div class="fw-bold text-warning" style="font-size: 0.8rem;">${job.service.name}</div>
                                <div style="font-size: 0.75rem;">${job.customer.name}</div>
                                <div class="text-white-50" style="font-size: 0.7rem;">${time}</div>
                            </div>
                        `;
                    });
                    
                    if(jobs.length > 3) {
                         tooltipHtml += `<div class="mt-1 text-center small text-muted">+${jobs.length - 3} more</div>`;
                    }
                    
                    tooltipHtml += `</div>`;
                }

                html += `<div class="calendar-day ${jobClass} d-flex align-items-center justify-content-center" 
                             style="aspect-ratio: 1; ${cursorStyle}" ${clickAttr} title="">
                             ${day}
                             ${tooltipHtml}
                         </div>`;
            }
            html += '</div>';
            calendar.innerHTML = html;
        }

        function getJobLink(jobs) {
            // Use the first job content if multiple exist
            if(jobs && jobs.length > 0) {
                const job = jobs[0];
                // Use uuid for routing as defined in model getRouteKeyName
                const uuid = job.uuid || job.id; 
                return jobRouteBase.replace('PLACEHOLDER', uuid);
            }
            return '#';
        }
    </script>
</x-app-layout>
