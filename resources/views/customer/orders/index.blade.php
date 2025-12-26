@push('styles')
    @vite(['resources/css/components/order-list.css', 'resources/css/tukang/finance.css'])
    <style>
        /* Specific overrides for this page if needed, but rely on finance.css mostly */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-badge.pending { background: #E9E6D7; color: #857D5C; }
        .status-badge.accepted { background: #DBEAFE; color: #1E40AF; }
        .status-badge.on_progress { background: #DCFCE7; color: #166534; }
        .status-badge.completed { background: #F3F4F6; color: #374151; }
        .status-badge.cancelled { background: #FEE2E2; color: #991B1B; }
        .status-badge.rejected { background: #FEE2E2; color: #991B1B; }

        @keyframes pulse-border {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 159, 28, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 159, 28, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 159, 28, 0);
            }
        }
        
        .active-order-glow {
            border: 2px solid #FF9F1C !important;
            background: #FFFCF7;
            animation: pulse-border 2s infinite;
        }
    </style>
@endpush

<x-app-layout>
    <div class="finance-container">
        <!-- Header -->
        <div class="finance-header">
            <h1 class="page-title">
                <i class="bi bi-calendar-check-fill text-brand-orange me-3"></i>My Bookings
            </h1>
            <p class="page-subtitle">Manage your ongoing and completed orders</p>
        </div>

        <div class="bookings-list-content">
            <div class="filter-pills d-flex gap-3 mb-4 overflow-auto pb-2">
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
                    <div class="wallet-card active-order-glow mb-4 p-4 border rounded-4 shadow-sm position-relative overflow-hidden">
                        <a href="{{ route('customer.orders.show', $order) }}" class="text-decoration-none text-dark d-block">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="wallet-icon-wrapper m-0" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                        <i class="{{ $order->service->icon_class ?? 'bi bi-tools' }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold mb-1 fs-5" style="color: #2C2C2C;">{{ $order->service->name ?? 'Service' }}</h3>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock me-1"></i> {{ $order->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold fs-5" style="color: #FF9F1C;">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #F1F2F4;">

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $order->tukang->profile_image ? \App\Helpers\StorageHelper::url($order->tukang->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($order->tukang->name) }}" alt="{{ $order->tukang->name }}" class="rounded-circle border" style="width: 32px; height: 32px;">
                                    <span class="fw-semibold small text-dark">{{ $order->tukang->name }}</span>
                                </div>
                                <span class="status-badge {{ ($order->status === 'pending' && $order->isExpired()) ? 'rejected' : $order->status }}">
                                    {{ ($order->status === 'pending' && $order->isExpired()) ? 'Expired' : ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
                
                @if($historyOrders->count() > 0)
                    <div class="section-title mb-3 mt-5">
                        <h5 class="fw-bold text-muted small text-uppercase"><i class="bi bi-clock-history me-2"></i>History</h5>
                    </div>
                @endif
            @endif

            @foreach($historyOrders as $order)
                <div class="wallet-card mb-3 p-4 border rounded-4 shadow-sm" style="border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <a href="{{ route('customer.orders.show', $order) }}" class="text-decoration-none text-dark d-block">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #FF9800; font-size: 1.5rem;">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold mb-1 fs-6" style="color: #2C2C2C;">{{ $order->service ? $order->service->name : 'Custom Service' }}</h3>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $order->created_at->format('d M Y • H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-5 text-dark">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                                <span class="status-badge {{ ($order->status === 'pending' && $order->isExpired()) ? 'rejected' : $order->status }} mt-2 d-inline-block">
                                    {{ ($order->status === 'pending' && $order->isExpired()) ? 'Expired' : ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>

                        <hr style="border-color: #F1F2F4;">

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $order->tukang->profile_image ? \App\Helpers\StorageHelper::url($order->tukang->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($order->tukang->name) }}" 
                                     alt="{{ $order->tukang->name }}" 
                                     class="rounded-circle border"
                                     style="width: 32px; height: 32px;">
                                <span class="fw-semibold small text-dark">{{ $order->tukang->name }}</span>
                            </div>
                            
                            <div class="text-muted small">
                                Order #{{ $order->order_number }}
                            </div>
                        </div>
                    </a>

                    {{-- Rate & Review Section --}}
                    @if($order->status === 'completed')
                        <div class="mt-3 pt-3 border-top">
                            @if(!$order->review)
                                <a href="{{ route('customer.reviews.create', $order) }}" class="btn btn-warning w-100 fw-bold border-0" style="background-color: #FFC107; border-radius: 12px; padding: 0.75rem;">
                                    <i class="bi bi-star-fill me-1"></i> Rate & Review
                                </a>
                            @else
                                <div class="text-center w-100 p-2 rounded bg-light text-success fw-bold small" style="background: #E0F7FA; color: #006064;">
                                    <i class="bi bi-check-circle-fill me-1"></i> You reviewed this order
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

            @if($activeOrders->count() === 0 && $historyOrders->count() === 0)
                <div class="text-center py-5">
                    <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="bi bi-journal-x text-muted" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                    <h3 class="fw-bold text-dark">No Bookings Found</h3>
                    <p class="text-muted">You haven't made any bookings yet.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-2 px-4 py-2 rounded-12 fw-bold" style="background: linear-gradient(135deg, #FF9800, #F57C00); border:none; border-radius: 12px;">
                        Choose a Service
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="p-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
