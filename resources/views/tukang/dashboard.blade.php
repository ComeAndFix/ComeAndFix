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
        .scale-hover {
            transition: transform 0.2s ease;
        }
        .scale-hover:hover {
            transform: translateY(-3px);
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
                    
                    <!-- Availability Toggle Moved -->
                </div>
            </div>
        </section>

        <div class="container" style="max-width: 1200px;">
            <!-- Status Control Section (New) -->
            <section class="mb-5">
                <div id="status-card" class="d-flex align-items-center justify-content-between p-4 rounded-4 shadow-sm" 
                     style="background: white; border-left: 8px solid {{ Auth::guard('tukang')->user()->is_available ? '#10B981' : '#EF4444' }}; transition: all 0.3s ease;">
                    
                    
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--brand-dark);">Current Status</h5>
                        <p id="availability-text" class="mb-0 fw-medium {{ Auth::guard('tukang')->user()->is_available ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">
                            {{ Auth::guard('tukang')->user()->is_available ? 'Available for New Jobs' : 'Currently Unavailable' }}
                        </p>
                        <p class="text-muted small mb-0 mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="availability-info">
                                {{ Auth::guard('tukang')->user()->is_available 
                                    ? 'Customers can find you on the map.' 
                                    : 'You are hidden from customer searches.' }}
                            </span>
                        </p>
                    </div>


                    <div class="form-check form-switch custom-switch">
                        <input class="form-check-input" type="checkbox" id="availability-toggle" style="width: 3.5rem; height: 1.75rem; cursor: pointer;" 
                               {{ Auth::guard('tukang')->user()->is_available ? 'checked' : '' }} 
                               onchange="toggleAvailability(this)">
                    </div>
                    <!-- Hidden indicator for JS compatibility -->
                    <div id="availability-indicator" style="display: none;"></div>
                </div>
            </section>

            <!-- Calendar & Scheduled Jobs Section -->
            <section class="mb-5">
                <div class="row g-4">
                    <!-- Left: Calendar -->
                    <div class="col-lg-5">
                        <div class="dashboard-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-calendar-check me-2 text-warning"></i>Schedule
                                </h5>
                                <div class="d-flex gap-2">
                                    <button id="today-btn" class="btn btn-sm bg-white border shadow-sm rounded-pill px-3 fw-bold text-primary" onclick="goToToday()" style="height: 38px; font-size: 0.85rem;">
                                        Today
                                    </button>
                                    <div class="d-flex align-items-center bg-white rounded-pill border px-1 py-1 shadow-sm">
                                        <button class="btn btn-sm btn-icon text-muted hover-bg-light" onclick="changeMonth(-1)" style="width: 28px; height: 28px; padding: 0; border-radius: 50%;">
                                            <i class="bi bi-chevron-left small"></i>
                                        </button>
                                        <div class="fw-bold px-2 small" id="current-month-display" style="min-width: 110px; text-align: center; cursor: default;"></div>
                                        <button class="btn btn-sm btn-icon text-muted hover-bg-light" onclick="changeMonth(1)" style="width: 28px; height: 28px; padding: 0; border-radius: 50%;">
                                            <i class="bi bi-chevron-right small"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="job-calendar" class="calendar-container"></div>
                        </div>
                    </div>

                    <!-- Right: Active Orders List -->
                    <div class="col-lg-7">
                        <div class="dashboard-card h-100" style="background: #f8f9fa; border: none; box-shadow: none;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0" id="selected-date-title">
                                    Upcoming Jobs
                                </h5>
                            </div>
                            
                            <div id="selected-date-jobs" class="d-flex flex-column gap-2 requests-scroll p-2" style="max-height: 400px; overflow-y: auto;">
                                <!-- Job cards will be injected here by JS -->
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-event display-6 mb-3 d-block opacity-50"></i>
                                    <p>Select a date to view scheduled jobs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

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
                                     data-sender-id="{{ $request->sender_id }}"
                                     onclick="this.remove(); openChatWithService('customer', {{ $request->sender_id }}, {{ $request->conversation_service_id ?? 'null' }})">
                                    
                                    <!-- Left: Avatar -->
                                    <div class="request-avatar me-3" style="overflow: hidden; padding: 0;">
                                        @if($request->sender->profile_image_url)
                                            <img src="{{ $request->sender->profile_image_url }}" alt="{{ $request->sender->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            {{ substr($request->sender->name, 0, 1) }}
                                        @endif
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
        function toggleAvailability(checkbox) {
            const statusCard = document.getElementById('status-card');
            const text = document.getElementById('availability-text');
            const infoText = document.getElementById('availability-info');
            
            // The checkbox is already changed when this event fires, so we check its NEW state
            const isNowAvailable = checkbox.checked;
            
            // Confirm dialog
            const action = isNowAvailable ? 'make yourself AVAILABLE' : 'make yourself UNAVAILABLE';
            if (!confirm(`Are you sure you want to ${action}? This will affect your visibility to customers.`)) {
                // User cancelled, revert checkbox
                checkbox.checked = !isNowAvailable;
                return;
            }

            const newState = isNowAvailable;
            
            // Helper to update UI
            const updateUI = (available) => {
                const color = available ? '#10B981' : '#EF4444'; // Green : Red
                const textClass = available ? 'text-success' : 'text-danger';
                const statusText = available ? 'Available for New Jobs' : 'Currently Unavailable';
                const infoMsg = available ? 'Customers can find you on the map.' : 'You are hidden from customer searches.';
                
                // Update elements
                statusCard.style.borderLeftColor = color;
                
                // Icon wrapper removed - no need to update background color
                
                text.textContent = statusText;
                text.className = `mb-0 fw-medium ${textClass}`;
                
                if(infoText) infoText.textContent = infoMsg;
            };

            // Optimistic update
            updateUI(newState);
            
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
                    checkbox.checked = !newState;
                    updateUI(!newState);
                    alert('Failed to update availability. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert on error
                checkbox.checked = !newState;
                updateUI(!newState);
                alert('An error occurred. Please check your connection.');
            });
        }

        // Calendar Implementation
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
            // Select today default or first available
            const today = new Date().toISOString().split('T')[0];
            selectDate(today);
        });

        const scheduledJobs = @json($scheduledJobs);
        // Create a route template string where we can replace the placeholder
        const jobRouteBase = "{{ route('tukang.jobs.show', ['order' => 'PLACEHOLDER']) }}";

        let selectedDate = null;
        let currentCalendarDate = new Date();

        function changeMonth(offset) {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() + offset);
            renderCalendar();
        }

        function goToToday() {
            currentCalendarDate = new Date();
            renderCalendar();
            // Optional: Select today as well
            const today = currentCalendarDate.toISOString().split('T')[0];
            selectDate(today);
        }

        function renderCalendar() {
            const calendar = document.getElementById('job-calendar');
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();
            
            // Check if current view is this month
            const realToday = new Date();
            const isCurrentMonth = realToday.getMonth() === month && realToday.getFullYear() === year;
            const todayBtn = document.getElementById('today-btn');
            if(todayBtn) {
                todayBtn.style.display = isCurrentMonth ? 'none' : 'block';
            }
            
            const monthEl = document.getElementById('current-month-display');
            if(monthEl) monthEl.textContent = currentCalendarDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = firstDay.getDay(); // 0 = Sunday

            let html = '<div class="calendar-grid" style="grid-template-columns: repeat(7, 1fr); gap: 0.5rem;">';
            ['S', 'M', 'T', 'W', 'T', 'F', 'S'].forEach(day => {
                html += `<div class="calendar-day-header text-center small py-1" style="font-size: 0.75rem; color: #6c757d;">${day}</div>`;
            });

            for (let i = 0; i < startDay; i++) {
                html += '<div></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                const hasJob = scheduledJobs[dateStr] !== undefined;
                
                let dayClasses = "calendar-day d-flex align-items-center justify-content-center flex-column position-relative";
                let style = "aspect-ratio: 1; border-radius: 12px; cursor: pointer; transition: all 0.2s;";
                let content = `<span style="font-size: 0.9rem;">${day}</span>`;
                let clickAction = `onclick="selectDate('${dateStr}')"`;
                
                // Visual feedback for interaction
                style += " user-select: none;";

                if (hasJob) {
                    content += `<div style="width: 4px; height: 4px; background: #f59e0b; border-radius: 50%; margin-top: 4px;"></div>`;
                }

                // Highlight selected date if it's in this month
                let extraStyles = "";
                let dotStyle = "";
                
                if (selectedDate === dateStr) {
                    extraStyles = "background: #f59e0b; color: white; font-weight: bold;";
                    if (hasJob) {
                        // Invert dot color when selected
                         content = `<span style="font-size: 0.9rem;">${day}</span><div style="width: 4px; height: 4px; background: white; border-radius: 50%; margin-top: 4px;"></div>`;
                    }
                }

                html += `<div class="${dayClasses}" 
                             id="day-${dateStr}"
                             style="${style} ${extraStyles}" 
                             ${clickAction}>
                             ${content}
                         </div>`;
            }
            html += '</div>';
            calendar.innerHTML = html;
        }

        function selectDate(dateStr) {
            // Update selected state visual
            if (selectedDate) {
                const el = document.getElementById(`day-${selectedDate}`);
                if (el) {
                    el.style.background = '';
                    el.style.color = '';
                    el.style.fontWeight = '';
                    const dot = el.querySelector('div');
                    if(dot) dot.style.background = '#f59e0b';
                }
            }
            
            selectedDate = dateStr;
            const el = document.getElementById(`day-${dateStr}`);
            if (el) {
                el.style.background = '#f59e0b';
                el.style.color = 'white';
                el.style.fontWeight = 'bold';
                const dot = el.querySelector('div');
                if(dot) dot.style.background = 'white';
            }

            // Update List
            const listContainer = document.getElementById('selected-date-jobs');
            const title = document.getElementById('selected-date-title');
            
            if(title) {
                const dateObj = new Date(dateStr);
                // Fix timezone by creating date from parts
                const parts = dateStr.split('-');
                const localDate = new Date(parts[0], parts[1]-1, parts[2]);
                title.textContent = localDate.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
            }

            if(listContainer) {
                listContainer.innerHTML = '';
                const jobs = scheduledJobs[dateStr];
                
                if (jobs && jobs.length > 0) {
                    // Sort jobs by time (ascending)
                    jobs.sort((a, b) => {
                        return new Date(a.work_datetime) - new Date(b.work_datetime);
                    });

                    jobs.forEach(job => {
                        const uuid = job.uuid || job.id;
                        const link = jobRouteBase.replace('PLACEHOLDER', uuid);
                        const dateTime = new Date(job.work_datetime);
                        const timeStr = dateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        // Status badge logic - using our status-badge classes
                        const statusClass = job.status.toLowerCase().replace(' ', '_');
                        const statusLabel = job.status.replace('_', ' ').split(' ').map(word => 
                            word.charAt(0).toUpperCase() + word.slice(1)
                        ).join(' ');
                        
                        const card = `
                            <a href="${link}" class="text-decoration-none text-dark scale-hover d-block w-100">
                                <div class="card border-0 shadow-sm rounded-4 w-100" style="background: white;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #d97706);">
                                                <i class="bi bi-briefcase fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1" style="min-width: 0; overflow: hidden;">
                                                <div class="d-flex justify-content-between align-items-start mb-1 w-100">
                                                    <h6 class="fw-bold mb-0 text-truncate me-2" style="max-width: 150px;">${job.service.name}</h6>
                                                    <span class="status-badge ${statusClass} flex-shrink-0" style="font-size: 0.65rem; white-space: nowrap; padding: 0.35rem 0.7rem;">${statusLabel}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <p class="mb-0 text-muted small text-truncate me-2" style="max-width: 140px;">
                                                        <i class="bi bi-person me-1"></i> ${job.customer.name}
                                                    </p>
                                                    <small class="fw-bold text-dark flex-shrink-0 text-nowrap">
                                                        <i class="bi bi-clock me-1"></i>${timeStr}
                                                    </small>
                                                </div>
                                            </div>
                                            <i class="bi bi-chevron-right text-muted flex-shrink-0 ms-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        `;
                        listContainer.innerHTML += card;
                    });
                } else {
                    listContainer.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="bi bi-calendar-event display-6" style="color: #e5e7eb;"></i>
                            </div>
                            <h6 class="fw-bold text-secondary">No Jobs Scheduled</h6>
                            <p class="small text-muted mb-0">You have no active orders for this date.</p>
                        </div>
                    `;
                }
            }
        }
        // Message Listener
        document.addEventListener('DOMContentLoaded', () => {
            const tukangId = {{ Auth::guard('tukang')->id() }};
            
            if (window.Echo) {
                window.Echo.private(`tukang.${tukangId}`)
                    .listen('MessageSent', (e) => {
                        console.log('New message received:', e.message);
                        
                        // Only add if it's from a customer
                        if (e.message.sender.type.includes('Customer')) {
                            const requestsList = document.getElementById('job-requests-list');
                            const senderId = e.message.sender.id;
                            
                            // Check for existing card
                            const existingCard = requestsList.querySelector(`.request-card[data-sender-id="${senderId}"]`);
                            
                            if (existingCard) {
                                // Update existing card
                                existingCard.querySelector('.request-message').textContent = e.message.message;
                                existingCard.querySelector('.request-message').classList.add('fw-bold', 'text-dark');
                                existingCard.querySelector('.request-time').textContent = 'Just now';
                                existingCard.querySelector('.request-time').classList.add('text-primary', 'fw-bold');
                                
                                // Add glow effect
                                existingCard.classList.add('new-message-glow');
                                
                                // Move to top
                                requestsList.prepend(existingCard);
                            } else {
                                // Remove empty state if present
                                const emptyState = requestsList.querySelector('.text-center');
                                if (emptyState) {
                                    emptyState.remove();
                                }
                                
                                // Prepare service badge HTML
                                let serviceHtml = '';
                                if (e.message.service) {
                                    serviceHtml = `
                                        <div class="service-badge">
                                            <i class="bi bi-wrench"></i>
                                            ${e.message.service.name}
                                        </div>
                                    `;
                                } else {
                                    serviceHtml = `
                                        <div class="service-badge" style="background: rgba(107, 114, 128, 0.1); color: #4b5563;">
                                            <i class="bi bi-chat-dots"></i>
                                            General Inquiry
                                        </div>
                                    `;
                                }
                                
                                // City badge
                                let cityHtml = '';
                                if (e.message.sender.city) {
                                    cityHtml = `
                                        <div class="city-badge">
                                            <i class="bi bi-geo-alt"></i> ${e.message.sender.city}
                                        </div>
                                    `;
                                }
                                
                                // Create URL
                                let openChatUrl = `/tukang/chat/customer/${e.message.sender.id}`;
                                if (e.message.service && e.message.service.id) {
                                    openChatUrl += `?service_id=${e.message.service.id}`;
                                }
                                
                                const cardHtml = `
                                    <div class="request-card new-message-glow" 
                                         data-sender-id="${senderId}"
                                         onclick="this.remove(); window.location.href='${openChatUrl}'">
                                        
                                        <div class="request-avatar me-3">
                                            ${e.message.sender.name.charAt(0)}
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="request-header">
                                                <h6 class="request-name mb-0">${e.message.sender.name}</h6>
                                                <small class="request-time text-primary fw-bold">Just now</small>
                                            </div>

                                            ${serviceHtml}
                                            
                                            <p class="request-message mb-1 fw-bold text-dark">
                                                ${e.message.message}
                                            </p>

                                            ${cityHtml}
                                        </div>

                                        <div class="ms-3 align-self-center">
                                            <i class="bi bi-chevron-right request-arrow"></i>
                                        </div>
                                    </div>
                                `;
                                
                                // Prepend new card
                                requestsList.insertAdjacentHTML('afterbegin', cardHtml);
                                
                                // Highlight animation
                                const newCard = requestsList.firstElementChild;
                                newCard.style.animation = 'slideDown 0.5s ease-out';
                            }
                        }
                    });
            }
        });
        
        // Add CSS for animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .new-message-glow {
                border-left: 4px solid var(--brand-orange) !important;
                background-color: #fffaf0;
            }
        `;
        document.head.appendChild(style);
    </script>
    <script>
        // Handle Back/Forward Cache (bfcache) restoration
        window.addEventListener('pageshow', function(event) {
            // Check if the list is empty (e.g. if user clicked item, navigated away, then came back)
            const requestsList = document.getElementById('job-requests-list');
            if (requestsList && requestsList.children.length === 0) {
                 const emptyStateHtml = `
                    <div class="text-center py-5">
                        <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <i class="bi bi-inbox text-muted" style="font-size: 2.5rem; opacity: 0.7;"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No New Requests</h6>
                        <p class="text-muted small">You're all caught up! Check back later.</p>
                    </div>
                `;
                requestsList.innerHTML = emptyStateHtml;
            }
        });
    </script>
</x-app-layout>
