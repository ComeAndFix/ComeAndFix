<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-clock-history me-2"></i>Bookings History
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        @forelse($orders as $order)
                            <a href="{{ route('customer.orders.show', $order) }}" class="text-decoration-none">
                                <div class="border-bottom p-3 hover-bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">
                                                {{ $order->service ? $order->service->name : 'Custom Service' }}
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-person me-1"></i>{{ $order->tukang->name }}
                                            </p>
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ $order->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'accepted' => 'info',
                                                    'on_progress' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    'rejected' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }} mb-2">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                            <h6 class="fw-bold text-primary mb-0">
                                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                            </h6>
                                        </div>
                                    </div>
                                    
                                    {{-- Rate & Review Button --}}
                                    @if(!$order->review)
                                        <div class="mt-2 px-3 pb-2">
                                            <a href="{{ route('customer.reviews.create', $order) }}" class="btn btn-warning btn-sm w-100">
                                                <i class="bi bi-star me-1"></i> Rate & Review
                                            </a>
                                        </div>
                                    @else
                                        <div class="mt-2 px-3 pb-2">
                                            <div class="alert alert-info mb-0 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> <small>You reviewed this order</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                                <h5 class="text-muted mt-3">No completed bookings yet</h5>
                                <p class="text-muted">Your completed service bookings will appear here.</p>
                                <a href="{{ route('find-tukang') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-search me-1"></i>Find Handyman
                                </a>
                            </div>
                        @endforelse

                        <!-- Pagination -->
                        @if($orders->hasPages())
                            <div class="p-3">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }
    </style>
</x-app-layout>
