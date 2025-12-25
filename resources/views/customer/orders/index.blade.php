<x-app-layout>
    <div class="bookings-page-wrapper">
        <div class="bookings-container">
            <h1 class="page-title">
                <i class="bi bi-calendar-check-fill text-brand-orange"></i>
                My Bookings
            </h1>

            <div class="filter-pills">
                <a href="{{ route('customer.orders.index', ['filter' => 'all']) }}" class="filter-pill {{ $filter === 'all' ? 'active' : '' }}">
                    All Orders
                </a>
                <a href="{{ route('customer.orders.index', ['filter' => 'ongoing']) }}" class="filter-pill {{ $filter === 'ongoing' ? 'active' : '' }}">
                    Ongoing
                </a>
                <a href="{{ route('customer.orders.index', ['filter' => 'completed']) }}" class="filter-pill {{ $filter === 'completed' ? 'active' : '' }}">
                    Completed
                </a>
                <a href="{{ route('customer.orders.index', ['filter' => 'cancelled']) }}" class="filter-pill {{ $filter === 'cancelled' ? 'active' : '' }}">
                    Cancelled
                </a>
            </div>

            @php
                $activeOrders = $orders->filter(function($order) {
                    return in_array($order->status, ['accepted', 'on_progress']);
                });
                
                $historyOrders = $orders->filter(function($order) {
                    return !in_array($order->status, ['accepted', 'on_progress']);
                });
            @endphp

            @if($activeOrders->count() > 0)
                <div class="section-title mb-3">
                    <h5 class="fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-brand-orange me-2"></i>Active Orders</h5>
                </div>
                
                @foreach($activeOrders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}" class="booking-card active-order-glow mb-4">
                        <div class="booking-header">
                            <div class="service-info">
                                <div class="service-icon-box">
                                    <i class="{{ $order->service->icon_class ?? 'bi bi-tools' }}"></i>
                                </div>
                                <div class="service-details">
                                    <h3>{{ $order->service->name ?? 'Service' }}</h3>
                                    <div class="booking-date">
                                        <i class="bi bi-clock"></i> {{ $order->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="booking-price">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="booking-divider"></div>

                        <div class="booking-footer">
                            <div class="tukang-info">
                                <img src="{{ $order->tukang->user->avatar_url ?? 'https://ui-avatars.com/api/?name=Tukang' }}" alt="Tukang" class="tukang-avatar-small">
                                <span class="tukang-name">{{ $order->tukang->user->name ?? 'Tukang Name' }}</span>
                            </div>
                            <span class="status-badge {{ ($order->status === 'pending' && $order->isExpired()) ? 'rejected' : $order->status }}">
                                {{ ($order->status === 'pending' && $order->isExpired()) ? 'Expired' : ucwords(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                    </a>
                @endforeach
                
                @if($historyOrders->count() > 0)
                    <div class="section-title mb-3 mt-5">
                        <h5 class="fw-bold text-muted small text-uppercase"><i class="bi bi-clock-history me-2"></i>History</h5>
                    </div>
                @endif
            @endif

            @forelse($historyOrders as $order)
                <div class="booking-card-wrapper">
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
                                <span class="status-badge {{ ($order->status === 'pending' && $order->isExpired()) ? 'rejected' : $order->status }} mt-2">
                                    {{ ($order->status === 'pending' && $order->isExpired()) ? 'Expired' : ucfirst(str_replace('_', ' ', $order->status)) }}
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
                    </a>

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
                </div>
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
