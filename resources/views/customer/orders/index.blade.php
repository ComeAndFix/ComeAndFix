<x-app-layout>
    <div class="bookings-page-wrapper">
        <div class="bookings-container">
            <h1 class="page-title">
                <i class="bi bi-calendar-check-fill text-brand-orange"></i>
                My Bookings
            </h1>

            @forelse($orders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="booking-card">
                    <div class="booking-header">
                        <div class="service-info">
                            <div class="service-icon-box">
                                <i class="bi bi-tools"></i>
                            </div>
                            <div class="service-details">
                                <h3>{{ $order->service ? $order->service->name : 'Custom Service' }}</h3>
                                <div class="booking-date">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $order->created_at->format('d M Y • H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="booking-price">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </div>
                            <span class="status-badge {{ $order->status }} mt-2">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="booking-divider"></div>

                    <div class="booking-footer">
                        <div class="tukang-info">
                            <img src="{{ $order->tukang->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                                 alt="{{ $order->tukang->name }}" 
                                 class="tukang-avatar-small">
                            <span class="tukang-name">{{ $order->tukang->name }}</span>
                        </div>
                        
                        <div class="text-muted small">
                            Order #{{ $order->order_number }}
                        </div>
                    </div>

                    {{-- Rate & Review Section --}}
                    @if($order->status === 'completed')
                        <div class="action-area">
                            @if(!$order->review)
                                <a href="{{ route('customer.reviews.create', $order) }}" class="btn-review">
                                    <i class="bi bi-star-fill me-1"></i> Rate & Review
                                </a>
                            @else
                                <div class="reviewed-badge">
                                    <i class="bi bi-check-circle-fill me-1"></i> You reviewed this order
                                </div>
                            @endif
                        </div>
                    @endif
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h3 class="mt-3 fw-bold text-muted">No Bookings Found</h3>
                    <p class="text-muted">You haven't made any bookings yet.</p>
                    <a href="{{ route('find-tukang') }}" class="btn btn-brand-orange mt-3 px-4 py-2 rounded-pill">
                        Find a Handyman
                    </a>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="p-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
