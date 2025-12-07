<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-briefcase me-2"></i>My Bookings
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
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                                <h5 class="text-muted mt-3">No bookings yet</h5>
                                <p class="text-muted">You haven't made any service bookings yet.</p>
                                <a href="{{ route('tukang-map') }}" class="btn btn-primary mt-3">
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
