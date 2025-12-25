<x-app-layout>

    <div class="order-details-container">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            @if(request('from_chat'))
                <button onclick="history.back()" class="btn btn-outline-secondary rounded-pill me-3 btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Chat
                </button>
            @else
                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary rounded-pill me-3 btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            @endif
            <div>
                <h1 class="h3 fw-bold mb-0">Order Details</h1>
                <p class="text-muted mb-0 small">Order #{{ $order->order_number }}</p>
            </div>
        </div>

        <!-- Progress Tracker or Cancellation Notice -->
        @if(in_array($order->status, ['rejected', 'cancelled']))
            <!-- Cancellation Notice -->
            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-x-circle-fill fs-1 me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <strong>Order {{ ucfirst($order->status) }}</strong>
                        </h5>
                        <p class="mb-0">
                            @if($order->status === 'rejected')
                                This order proposal has been declined and is no longer active.
                            @else
                                This order has been cancelled and is no longer active.
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="badge bg-danger rounded-pill px-3 py-2">
                            <i class="bi bi-slash-circle me-1"></i> {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
        @else
        @if($order->status === 'completed' && $order->hasReview())
            <!-- Completion & Review Notice -->
            <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill fs-1 me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <strong>Service Completed & Reviewed</strong>
                        </h5>
                        <p class="mb-0">
                            Thank you for using Come&Fix! This order is now closed.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="bi bi-star-fill me-1"></i> Reviewed
                        </span>
                    </div>
                </div>
            </div>
        @else
            <!-- Progress Tracker Logic -->
            @php
                $steps = [
                    ['label' => 'Awaiting Approval', 'status' => 'pending'],
                    ['label' => 'Order Accepted', 'status' => 'accepted'],
                    ['label' => 'Payment', 'status' => 'paid'],
                    ['label' => 'Come&Fix', 'status' => 'on_progress'],
                    ['label' => 'Completed', 'status' => 'completed'],
                ];
                
                // Determine current step index
                $currentIndex = 0;
                if ($order->status == 'completed') {
                    $currentIndex = 4;
                } elseif ($order->status == 'on_progress') {
                    $currentIndex = 3; // Working
                } elseif ($order->status == 'accepted' && $order->payment_status == 'paid') {
                     $currentIndex = 3; // Paid, moving to On Progress
                } elseif ($order->status == 'accepted') {
                    $currentIndex = 2; // Needs payment
                } elseif ($order->status == 'pending') {
                    $currentIndex = 0; // Awaiting customer approval
                }

                // Calculate progress bar width
                $progressWidth = ($currentIndex / (count($steps) - 1)) * 100;
            @endphp

            <div class="progress-track">
                <div class="progress-fill" style="width: {{ $progressWidth }};"></div>
                
                @foreach($steps as $index => $step)
                    @php
                        $isActive = $index == $currentIndex;
                        $isCompleted = $index < $currentIndex;
                        $circleContent = $isCompleted ? '<i class="bi bi-check-lg"></i>' : ($index + 1);
                        $isSpecial = $step['label'] === 'Come&Fix';
                    @endphp
                    <div class="progress-step {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                        <div class="step-circle">{!! $circleContent !!}</div>
                        <div class="step-label" style="{{ $isSpecial ? 'color: var(--brand-orange) !important; font-weight: 800; font-size: 1.1rem;' : '' }}">
                            {{ $step['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @endif


        <div class="row">
            <!-- LEFT COLUMN: Main Details -->
            <div class="col-lg-8">
                <!-- Service Details -->
                <div class="order-card">
                    <div class="section-header">
                        <span>Service Information</span>
                        <span class="badge bg-{{ $order->status_color }} rounded-pill px-3">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="fw-bold text-brand-orange mb-1">{{ $order->service->name }}</h5>
                            <p class="text-muted small mb-0"><i class="bi bi-calendar-event me-1"></i> {{ $order->work_datetime ? $order->work_datetime->format('d M Y, H:i') : 'Date not set' }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1 text-muted small">Location</p>
                            <p class="fw-semibold mb-0" style="font-size: 0.9rem;">{{ $order->working_address ?? 'No address provided' }}</p>
                        </div>
                    </div>

                    @if($order->service_description)
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <p class="text-muted small mb-1 fw-bold">Description:</p>
                        <p class="mb-0 small">{{ $order->service_description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Pending Proposal Actions (Only if Pending) -->
                @if($order->status === 'pending')
                <div class="order-card">
                    <div class="section-header">
                        <span>Proposal Status</span>
                        <span class="badge bg-warning rounded-pill px-3"><i class="bi bi-clock-history me-1"></i> Awaiting Your Response</span>
                    </div>
                    
                    <div class="alert alert-info mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>{{ $order->tukang->name }}</strong> has sent you an order proposal. Please review the details and accept or decline the offer.
                        @if($order->expires_at)
                            <br><small class="text-muted mt-1 d-block">This proposal expires on {{ $order->expires_at->format('d M Y, H:i') }}</small>
                        @endif
                    </div>

                    <div class="d-flex gap-3 justify-content-center">
                        <form action="{{ route('order.accept', $order->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold">
                                <i class="bi bi-check-circle me-1"></i> Accept Proposal
                            </button>
                        </form>
                        
                        <form action="{{ route('order.reject', $order->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-5">
                                <i class="bi bi-x-circle me-1"></i> Decline
                            </button>
                        </form>
                    </div>
                </div>
                @endif


                <!-- Completion Status (Only if Completed) -->
                @if($order->completion)
                <div class="order-card">
                    <div class="section-header">
                        <span>Work Result</span>
                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Finished</span>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-muted small mb-2">Completion Note</p>
                        <p>{{ $order->completion->description }}</p>
                    </div>

                    @if($order->completion->photos && count($order->completion->photos) > 0)
                    <div>
                        <p class="text-muted small mb-2">Photos</p>
                        <div class="photo-grid">
                            @foreach($order->completion->photos as $photo)
                                <div class="photo-wrapper">
                                    <img src="{{ \App\Helpers\StorageHelper::url($photo) }}" alt="Completion Photo">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($order->status === 'completed')
                        @if(!$order->hasReview())
                            <div class="mt-4 pt-3 border-top text-center">
                                <a href="{{ route('customer.reviews.create', $order) }}" class="btn btn-warning rounded-pill px-4 fw-bold">
                                    <i class="bi bi-star-fill me-1"></i> Rate & Review Work
                                </a>
                            </div>
                        @else
                            <div class="mt-4 pt-3 border-top text-center">
                                <div class="alert alert-info d-inline-block mb-0 py-2 px-4 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> You've verified & reviewed this job
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: Summary & Payment -->
            <div class="col-lg-4">
                <!-- Tukang Profile -->
                <div class="order-card pt-0 pb-0 overflow-hidden">
                    <div class="p-3 border-bottom text-center bg-light">
                        <span class="small text-muted fw-bold">HANDLED BY</span>
                    </div>
                    <div class="p-4 text-center">
                        <img src="{{ $order->tukang->profile_photo_url ?? asset('images/default-avatar.png') }}" class="tukang-avatar mb-3" style="width: 80px; height: 80px;" alt="{{ $order->tukang->name }}">
                        <h5 class="fw-bold mb-1">{{ $order->tukang->name }}</h5>
                        <div class="text-warning small mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="text-muted ms-1">(4.8)</span>
                        </div>
                        <a href="{{ route('chat.show', ['receiverType' => 'tukang', 'receiverId' => $order->tukang_id]) }}" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                            <i class="bi bi-chat-dots me-1"></i> Chat Tukang
                        </a>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="order-card p-4">
                    <h5 class="fw-bold mb-4">Payment Summary</h5>
                    
                    <!-- Base -->
                    <div class="info-row">
                        <span class="info-label">Base Service</span>
                        <span class="info-value">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                    </div>

                    <!-- Additional Items -->
                    @if($order->additionalItems && $order->additionalItems->count() > 0)
                        <div class="info-row mt-3 mb-2">
                            <span class="small fw-bold text-muted">Additional Items</span>
                        </div>
                        @foreach($order->additionalItems as $item)
                        <div class="info-row ps-2 border-start border-2">
                            <span class="info-label small">{{ $item->item_name }} (x{{ $item->quantity }})</span>
                            <span class="info-value small">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    @endif

                    <!-- Custom Items -->
                    @if($order->customItems && $order->customItems->count() > 0)
                        <div class="info-row mt-3 mb-2">
                            <span class="small fw-bold text-muted">Custom Items</span>
                        </div>
                        @foreach($order->customItems as $item)
                        <div class="info-row ps-2 border-start border-2">
                            <span class="info-label small">{{ $item->item_name }} (x{{ $item->quantity }})</span>
                            <span class="info-value small">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    @endif
                    
                    <div class="price-total info-row">
                        <span>Total Paid</span>
                        <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>

                     @if($order->payment_status !== 'paid' && $order->status === 'accepted')
                        <div class="mt-3">
                             <a href="{{ route('chat.show', ['receiverType' => 'tukang', 'receiverId' => $order->tukang_id]) }}" class="btn btn-brand-orange w-100 rounded-pill fw-bold">
                                Pay in Chat
                            </a>
                        </div>
                    @elseif($order->payment_status === 'paid')
                        <div class="mt-3 text-center">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success">
                                <i class="bi bi-shield-check me-1"></i> Payment Verified
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
