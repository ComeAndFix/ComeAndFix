<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Dashboard Header -->
        <section class="bg-primary text-white py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="h3 fw-bold mb-2">
                            <i class="bi bi-tools me-2"></i>
                            Handyman Dashboard
                        </h1>
                        <p class="mb-0">Welcome back, {{ Auth::guard('tukang')->user()->name }}! Here's your job overview for today.</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <button id="availability-toggle" class="badge fs-6 me-2 border-0" 
                                style="cursor: pointer; {{ Auth::guard('tukang')->user()->is_available ? 'background-color: #198754;' : 'background-color: #dc3545;' }}"
                                onclick="toggleAvailability()">
                            <i class="bi {{ Auth::guard('tukang')->user()->is_available ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                            <span id="availability-text">{{ Auth::guard('tukang')->user()->is_available ? 'Available' : 'Unavailable' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Job Overview Cards -->
        <section class="py-4 bg-light border-bottom">
            <div class="container">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('tukang.jobs.index') }}" class="text-decoration-none">
                            <div class="card bg-info text-white border-0 h-100 cursor-pointer">
                                <div class="card-body text-center">
                                    <i class="bi bi-play-circle display-6 mb-2"></i>
                                    <h3 class="fw-bold mb-1">{{ $activeJobsCount }}</h3>
                                    <p class="mb-0 small">Active Jobs</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white border-0 h-100">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bi bi-calendar-check me-2"></i>Job Schedule</h6>
                                <div id="job-calendar" class="calendar-container small"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('tukang.finance.index') }}" class="text-decoration-none">
                            <div class="card bg-success text-white border-0 h-100 cursor-pointer">
                                <div class="card-body text-center">
                                    <i class="bi bi-wallet2 display-6 mb-3"></i>
                                    <h3 class="fw-bold mb-1">Rp {{ number_format($walletBalance, 0, ',', '.') }}</h3>
                                    <p class="mb-3 small">Wallet Balance</p>
                                    <div class="bg-warning rounded p-3">
                                        <i class="bi bi-graph-up mb-2" style="font-size: 1.5rem;"></i>
                                        <h5 class="fw-bold mb-1">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h5>
                                        <p class="mb-0 small">This Month's Income</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-4">
            <div class="container">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">

                        <!-- Incoming Job Requests -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-inbox me-2"></i>
                                    Incoming Job Requests
                                </h5>
                            </div>
                            
                            <!-- Filter Form -->
                            <div class="card-body border-bottom bg-light">
                                <form method="GET" action="{{ route('tukang.dashboard') }}" class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="customer_name" class="form-control form-control-sm" 
                                            placeholder="Search by customer name..." 
                                            value="{{ request('customer_name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <select name="service_filter" class="form-select form-select-sm">
                                            <option value="">All Services</option>
                                            @foreach($availableServices as $service)
                                                <option value="{{ $service->id }}" {{ request('service_filter') == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-funnel"></i> Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="card-body p-0">
                                <div id="job-requests-list">
                                    @forelse($jobRequests as $request)
                                        <div class="border-bottom p-3 hover-bg-light cursor-pointer" 
                                            onclick="openChatWithService('customer', {{ $request->sender_id }}, {{ $request->conversation_service_id ?? 'null' }})">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                                    {{ substr($request->sender->name, 0, 1) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <h6 class="mb-0 fw-bold">{{ $request->sender->name }}</h6>
                                                        <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    @if($request->service)
                                                        <p class="mb-1 text-muted small">
                                                            <i class="bi bi-wrench me-1"></i>
                                                            {{ $request->service->name }}
                                                        </p>
                                                    @else
                                                        <p class="mb-1 text-muted small">
                                                            <i class="bi bi-chat-dots me-1"></i>
                                                            General Inquiry
                                                        </p>
                                                    @endif
                                                    @if($request->sender->address || $request->sender->city)
                                                        <p class="mb-1 text-muted small">
                                                            <i class="bi bi-geo-alt me-1"></i>
                                                            {{ $request->sender->address ?? 'No address' }}@if($request->sender->city), {{ $request->sender->city }}@endif
                                                        </p>
                                                    @endif
                                                    @if(!$request->read_at)
                                                        <span class="badge bg-warning text-dark">New</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">No incoming job requests</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('tukang.jobs.history') }}" class="btn btn-primary">
                                        <i class="bi bi-clock-history me-2"></i>View Jobs History
                                    </a>
                                    <a href="{{ route('tukang.chatrooms.index') }}" class="btn btn-primary">
                                        <i class="bi bi-chat-dots me-2"></i>View Messages
                                    </a>
                                    <a href="{{ route('tukang.profile') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-person-gear me-2"></i>Edit Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Floating Message Button -->
    <a href="{{ route('tukang.chatrooms.index') }}" class="floating-message-btn" id="floatingMessageBtn">
        <i class="bi bi-chat-dots-fill"></i>
        <span class="notification-badge" id="notificationBadge" style="display: none;"></span>
    </a>

    <style>
        .cursor-pointer { cursor: pointer; }
        .hover-bg-light:hover { background-color: #f8f9fa; }

        .floating-message-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }

        .floating-message-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Calendar styles */
        .calendar-container {
            color: white;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }
        .calendar-day-header {
            text-align: center;
            font-size: 10px;
            padding: 2px;
            opacity: 0.7;
        }
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
        }
        .calendar-day.other-month {
            opacity: 0.3;
        }
        .calendar-day.has-job {
            background: rgba(255, 255, 255, 0.2);
            font-weight: bold;
        }
        .calendar-day.has-job::after {
            content: '';
            position: absolute;
            bottom: 2px;
            width: 4px;
            height: 4px;
            background: #ffc107;
            border-radius: 50%;
        }
        .job-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
            display: none;
        }
    </style>

    <script>
            function openChatWithService(receiverType, receiverId, serviceId) {
                let url = `/tukang/chat/${receiverType}/${receiverId}`;
                if (serviceId) {
                    url += `?service_id=${serviceId}`;
                }
                window.location.href = url;
            }

            function openChat(receiverType, receiverId) {
                window.location.href = `/tukang/chat/${receiverType}/${receiverId}`;
            }

            // Update message count
            function updateMessageCount() {
                fetch('{{ route("tukang.messages.recent") }}')
                    .then(response => response.json())
                    .then(messages => {
                        const unreadCount = messages.filter(msg => !msg.read_at).length;
                        const badge = document.getElementById('notificationBadge');
                        
                        if (unreadCount > 0) {
                            badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                            badge.style.display = 'flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error refreshing message count:', error));
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Initial load
                updateMessageCount();
                
                // Update every 30 seconds
                setInterval(updateMessageCount, 30000);

                // Initialize calendar
                renderCalendar();
            });

            // Calendar implementation
            const scheduledJobs = @json($scheduledJobs);

            function renderCalendar() {
                const calendar = document.getElementById('job-calendar');
                const now = new Date();
                const year = now.getFullYear();
                const month = now.getMonth();

                // Get first day of month and last day
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startDay = firstDay.getDay(); // 0 = Sunday

                // Calendar header
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                let html = `<div class="calendar-header">${monthNames[month]} ${year}</div>`;

                // Day headers
                html += '<div class="calendar-grid">';
                ['S', 'M', 'T', 'W', 'T', 'F', 'S'].forEach(day => {
                    html += `<div class="calendar-day-header">${day}</div>`;
                });

                // Empty cells before first day
                for (let i = 0; i < startDay; i++) {
                    html += '<div class="calendar-day other-month"></div>';
                }

                // Days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                    const hasJob = scheduledJobs[dateStr] !== undefined;
                    const jobClass = hasJob ? 'has-job' : '';
                    
                    html += `<div class="calendar-day ${jobClass}" data-date="${dateStr}">${day}</div>`;
                }

                html += '</div>';
                calendar.innerHTML = html;

                // Add hover events to days with jobs
                document.querySelectorAll('.calendar-day.has-job').forEach(dayEl => {
                    dayEl.addEventListener('mouseenter', showJobTooltip);
                    dayEl.addEventListener('mouseleave', hideJobTooltip);
                });
            }

            let tooltip = null;

            function showJobTooltip(e) {
                const date = e.target.dataset.date;
                const jobs = scheduledJobs[date];
                
                if (!jobs || jobs.length === 0) return;

                // Create tooltip if doesn't exist
                if (!tooltip) {
                    tooltip = document.createElement('div');
                    tooltip.className = 'job-tooltip';
                    document.body.appendChild(tooltip);
                }

                // Build tooltip content
                let content = jobs.map(job => {
                    const time = new Date(job.work_datetime).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
                    return `<div style="margin-bottom: 4px;"><strong>${job.customer.name}</strong><br>${job.service.name} - ${time}</div>`;
                }).join('');

                tooltip.innerHTML = content;
                tooltip.style.display = 'block';

                // Position tooltip
                const rect = e.target.getBoundingClientRect();
                tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                tooltip.style.top = rect.bottom + 5 + 'px';
            }

            function hideJobTooltip() {
                if (tooltip) {
                    tooltip.style.display = 'none';
                }
            }

            function toggleAvailability() {
                const toggle = document.getElementById('availability-toggle');
                const text = document.getElementById('availability-text');
                const icon = toggle.querySelector('i');
                
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
                        if (data.is_available) {
                            toggle.style.backgroundColor = '#198754'; // green
                            icon.className = 'bi bi-check-circle me-1';
                            text.textContent = 'Available';
                        } else {
                            toggle.style.backgroundColor = '#dc3545'; // red
                            icon.className = 'bi bi-x-circle me-1';
                            text.textContent = 'Unavailable';
                        }
                        
                        // Show success toast/alert
                        showToast(data.message || 'Availability updated');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to update availability', 'error');
                });
            }

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed top-0 end-0 m-3`;
                toast.style.zIndex = '9999';
                toast.textContent = message;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
    </script>
</x-app-layout>
