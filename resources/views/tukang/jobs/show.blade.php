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
            <button onclick="history.back()" class="btn btn-outline-secondary rounded-pill me-3 btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </button>
            <div>
                <h1 class="h3 fw-bold mb-0">Job Details</h1>
                <p class="text-muted mb-0 small">Order #{{ $order->order_number }}</p>
            </div>
        </div>

        <!-- Progress Tracker or Cancellation Notice -->
        @if(in_array($order->status, ['rejected', 'cancelled']))
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
                            The order has been {{ $order->status }}.
                        </p>
                    </div>
                </div>
            </div>
        @else
            @if($order->status === 'completed')
                <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill fs-1 me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">
                                <strong>Job Completed Successfully</strong>
                            </h5>
                            <p class="mb-0">
                                Great work! This job is marked as completed.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Progress Tracker Logic -->
                @php
                    $steps = [
                        ['label' => 'Awaiting Approval', 'status' => 'pending'],
                        ['label' => 'Accepted', 'status' => 'accepted'],
                        ['label' => 'Payment', 'status' => 'paid'],
                        ['label' => 'Work in Progress', 'status' => 'on_progress'],
                        ['label' => 'Completed', 'status' => 'completed'],
                    ];
                    
                    $currentIndex = 0;
                    if ($order->status == 'completed') {
                        $currentIndex = 4;
                    } elseif ($order->status == 'on_progress') {
                        $currentIndex = 3;
                    } elseif ($order->status == 'accepted' && $order->payment_status == 'paid') {
                         $currentIndex = 3; // Paid, moving to On Progress (or waiting for Tukang to start)
                    } elseif ($order->status == 'accepted') {
                        $currentIndex = 2; // Needs payment
                    } elseif ($order->status == 'pending') {
                        $currentIndex = 0;
                    }

                    $progressWidth = ($currentIndex / (count($steps) - 1)) * 100;
                @endphp

                <div class="progress-track">
                    <div class="progress-fill" style="width: {{ $progressWidth }};"></div>
                    
                    @foreach($steps as $index => $step)
                        @php
                            $isActive = $index == $currentIndex;
                            $isCompleted = $index < $currentIndex;
                            $circleContent = $isCompleted ? '<i class="bi bi-check-lg"></i>' : ($index + 1);
                            $isSpecial = $step['status'] === 'on_progress';
                        @endphp
                        <div class="progress-step {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                            <div class="step-circle">{!! $circleContent !!}</div>
                            <div class="step-label" style="{{ $isSpecial ? 'color: var(--brand-orange) !important; font-weight: 800;' : '' }}">
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
                        <span>Job Information</span>
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

                    @if($order->service_details)
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Service Details</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($order->service_details as $key => $value)
                                <li class="mb-2 small">
                                    <strong class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Action Section: Completion -->
                @if(!$order->completion && $order->status === \App\Models\Order::STATUS_ON_PROGRESS && $order->status !== 'completed')
                    <div class="order-card">
                        <div class="section-header">
                            <span>Job Action</span>
                            <span class="badge bg-primary rounded-pill px-3"><i class="bi bi-tools me-1"></i> In Progress</span>
                        </div>
                        
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-check display-1 text-brand-orange mb-3 d-block" style="opacity: 0.2"></i>
                            <h5 class="fw-bold">Ready to complete this job?</h5>
                            <p class="text-muted mb-4">Once the work is done, submit the completion proof with photos and a description.</p>
                            
                            <a href="{{ route('tukang.jobs.complete', $order) }}" class="btn btn-brand-orange btn-lg rounded-pill px-5 fw-bold">
                                <i class="bi bi-check-circle me-2"></i> Submit Completion Proof
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Work Result (If Completed) -->
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
                                    <a href="{{ \App\Helpers\StorageHelper::url($photo) }}" target="_blank">
                                        <img src="{{ \App\Helpers\StorageHelper::url($photo) }}" alt="Completion Photo">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Customer Review (If Exists) -->
                @if($order->review)
                <div class="order-card">
                    <div class="section-header">
                        <span>Customer Review</span>
                        <div class="text-warning small">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $order->review->rating ? '-fill' : '' }}"></i>
                            @endfor
                            <span class="text-muted ms-1">({{ $order->review->rating }}.0)</span>
                        </div>
                    </div>
                    
                    @if($order->review->review_text)
                    <div class="p-3 bg-light rounded-3">
                         <p class="mb-0 fst-italic text-muted">"{{ $order->review->review_text }}"</p>
                    </div>
                    @else
                    <p class="text-muted font-italic mb-0">No written review provided.</p>
                    @endif
                </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: Customer & Summary -->
            <div class="col-lg-4">
                <!-- Customer Profile -->
                <div class="order-card pt-0 pb-0 overflow-hidden">
                    <div class="p-3 border-bottom text-center bg-light">
                        <span class="small text-muted fw-bold">CUSTOMER</span>
                    </div>
                    <div class="p-4 text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                {{ substr($order->customer->name, 0, 1) }}
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $order->customer->name }}</h5>
                        <p class="text-muted small mb-3">{{ $order->customer->city ?? 'City not set' }}</p>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('tukang.chat.show', ['receiverType' => 'customer', 'receiverId' => $order->customer_id]) }}" class="btn btn-primary btn-sm rounded-pill">
                                <i class="bi bi-chat-dots me-1"></i> Chat Customer
                            </a>
                        </div>

                        <div class="mt-4 text-start">
                            <small class="text-muted d-block mb-1">PHONE</small>
                            <span class="fw-semibold">{{ $order->customer->phone }}</span>
                            
                            <hr class="my-3">
                            
                            <small class="text-muted d-block mb-1">ADDRESS</small>
                            <span class="fw-semibold small">{{ $order->customer->address ?? 'No address provided' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="order-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Payment Summary</h5>
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 border border-success">Paid</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 border border-warning">Unpaid</span>
                        @endif
                    </div>
                    
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
                        <span>Total Amount</span>
                        <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
