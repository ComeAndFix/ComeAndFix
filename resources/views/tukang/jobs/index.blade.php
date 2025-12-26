<x-app-layout>
    @push('styles')
        @vite(['resources/css/components/order-list.css', 'resources/css/tukang/finance.css'])
    @endpush

    <div class="finance-container">
        <!-- Header -->
        <div class="finance-header">

            <h1 class="page-title">
                <i class="bi bi-briefcase-fill text-brand-orange me-3"></i>Active Jobs
            </h1>
            <p class="page-subtitle">Manage your ongoing and completed orders</p>
        </div>

        <div class="bookings-container p-0 m-0 w-100" style="max-width: none;">
            <div class="filter-pills mb-4">
                <a href="{{ route('tukang.jobs.index', ['filter' => 'all']) }}" class="filter-pill {{ $filter === 'all' ? 'active' : '' }}">
                    All Jobs
                </a>
                <a href="{{ route('tukang.jobs.index', ['filter' => 'ongoing']) }}" class="filter-pill {{ $filter === 'ongoing' ? 'active' : '' }}">
                    Ongoing
                </a>
                <a href="{{ route('tukang.jobs.index', ['filter' => 'completed']) }}" class="filter-pill {{ $filter === 'completed' ? 'active' : '' }}">
                    Completed
                </a>
                <a href="{{ route('tukang.jobs.index', ['filter' => 'cancelled']) }}" class="filter-pill {{ $filter === 'cancelled' ? 'active' : '' }}">
                   Cancelled
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @forelse($jobs as $job)
                <div class="booking-card-wrapper {{ in_array($job->status, ['accepted', 'on_progress']) ? 'active-order-glow' : '' }}">
                    <a href="{{ route('tukang.jobs.show', $job) }}" class="booking-card">
                        <div class="booking-header">
                            <div class="service-info">
                                <div class="service-icon-box">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <div class="service-details">
                                    <h3>{{ $job->service->name }}</h3>
                                    <div class="booking-date">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $job->work_datetime ? $job->work_datetime->format('d M Y, H:i') : 'Date not set' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="booking-price">
                                    Rp {{ number_format($job->total_price ?? $job->price, 0, ',', '.') }}
                                </div>
                                @if($job->payment_status)
                                    <x-payment-badge :status="$job->payment_status" size="sm" class="mt-2" />
                                @endif
                            </div>
                        </div>

                        <div class="booking-divider"></div>

                        <div class="booking-footer">
                            <div class="tukang-info">
                                <span class="text-muted me-2 small">Client:</span>
                                <span class="tukang-name">{{ $job->customer->name }}</span>
                            </div>

                            <x-status-badge :status="$job->status === 'pending' && $job->isExpired() ? 'expired' : $job->status" />
                        </div>
                    </a>

                    {{-- Action Area for Tukang --}}
                    @if($job->status === 'on_progress' && !$job->completion)
                        <div class="action-area">
                            <a href="{{ route('tukang.jobs.complete', $job) }}" class="btn-review" style="background-color: var(--brand-orange); color: white;">
                                <i class="bi bi-check-circle-fill me-1"></i> Submit Completion Proof
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h3 class="mt-3 fw-bold text-muted">No Active Jobs</h3>
                    <p class="text-muted">You don't have any active jobs or proposals at the moment.</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $jobs->links() }}
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
