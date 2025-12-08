<x-app-layout>
            <div class="container py-4">
                <div class="row mb-3">
                    <div class="col">
                        <a href="{{ route('tukang.jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Jobs
                        </a>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <!-- Order Details Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Job Details</h4>
                                    <span class="badge bg-{{ $order->status_color }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="fw-bold mb-3">Order Information</h5>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Order Number:</th>
                                                <td>{{ $order->order_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Service:</th>
                                                <td>{{ $order->service->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Base Price:</th>
                                                <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                            </tr>
                                            @if($order->additionalItems->count() > 0 || $order->customItems->count() > 0)
                                            <tr>
                                                <th>Total Price:</th>
                                                <td class="text-success fw-bold fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Payment Status:</th>
                                                <td>
                                                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                        {{ ucwords(str_replace('_', ' ', $order->payment_status ?? 'unpaid')) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created:</th>
                                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                            </tr>
                                            @if($order->accepted_at)
                                                <tr>
                                                    <th>Accepted:</th>
                                                    <td>{{ $order->accepted_at->format('d M Y H:i') }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="fw-bold mb-3">Customer Information</h5>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Name:</th>
                                                <td>{{ $order->customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone:</th>
                                                <td>{{ $order->customer->phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Address:</th>
                                                <td>{{ $order->customer->address }}</td>
                                            </tr>
                                            <tr>
                                                <th>City:</th>
                                                <td>{{ $order->customer->city }}</td>
                                            </tr>
                                        </table>

                                        <div class="mt-3">
                                            <a href="{{ route('tukang.chat.show', ['receiverType' => 'customer', 'receiverId' => $order->customer_id]) }}"
                                               class="btn btn-outline-primary w-100">
                                                <i class="bi bi-chat-dots"></i> Contact Customer
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                @if($order->service_description)
                                    <hr>
                                    <h5 class="fw-bold mb-3">Service Description</h5>
                                    <p class="text-muted">{{ $order->service_description }}</p>
                                @endif

                                @if($order->service_details)
                                    <hr>
                                    <h5 class="fw-bold mb-3">Service Details</h5>
                                    <ul class="list-unstyled">
                                        @foreach($order->service_details as $key => $value)
                                            <li class="mb-2">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if($order->additionalItems->count() > 0 || $order->customItems->count() > 0)
                                    <hr>
                                    <h5 class="fw-bold mb-3">Order Items Breakdown</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Base Service Price</strong></td>
                                                    <td class="text-end">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                                </tr>
                                                @foreach($order->additionalItems as $item)
                                                <tr>
                                                    <td>
                                                        {{ $item->item_name }}
                                                        <small class="text-muted">(x{{ $item->quantity }})</small>
                                                    </td>
                                                    <td class="text-end">Rp {{ number_format($item->item_price * $item->quantity, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                                @foreach($order->customItems as $item)
                                                <tr>
                                                    <td>
                                                        {{ $item->item_name }}
                                                        <small class="text-muted">(x{{ $item->quantity }})</small>
                                                        @if($item->description)
                                                            <br><small class="text-muted">{{ $item->description }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">Rp {{ number_format($item->item_price * $item->quantity, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                                <tr class="table-success">
                                                    <td><strong>TOTAL</strong></td>
                                                    <td class="text-end"><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Completion Section -->
                        @if($order->completion)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Job Completion Proof</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="mt-2">{{ $order->completion->description }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Working Duration:</strong>
                                        <p class="mt-2">{{ $order->completion->working_duration }} minutes</p>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Photos:</strong>
                                        <div class="row g-2 mt-2">
                                            @foreach($order->completion->photos as $photo)
                                                <div class="col-md-4">
                                                    <a href="{{ Storage::url($photo) }}" target="_blank">
                                                        <img src="{{ Storage::url($photo) }}"
                                                             class="img-fluid rounded"
                                                             alt="Completion Photo">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Submitted At:</strong>
                                        <p class="mt-2">{{ $order->completion->submitted_at->format('d M Y H:i') }}</p>
                                    </div>

                                    @if($order->status === 'completed')
                                        <div class="alert alert-success">
                                            <i class="bi bi-check-circle-fill"></i> Job completed successfully!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Customer Review Section -->
                        @if($order->review)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="bi bi-star-fill"></i> Customer Review</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Rating:</strong>
                                        <div class="text-warning fs-4 mt-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $order->review->rating)
                                                    <i class="bi bi-star-fill"></i>
                                                @else
                                                    <i class="bi bi-star"></i>
                                                @endif
                                            @endfor
                                            <span class="text-dark fs-6 ms-2">({{ $order->review->rating }}/5)</span>
                                        </div>
                                    </div>

                                    @if($order->review->review_text)
                                        <div class="mb-3">
                                            <strong>Review:</strong>
                                            <p class="mt-2 text-muted">"{{ $order->review->review_text }}"</p>
                                        </div>
                                    @endif

                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> Reviewed on {{ $order->review->created_at->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'completed')
                            <div class="card shadow-sm mb-4">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-clock-history display-4 text-muted mb-3"></i>
                                    <h6 class="text-muted">Waiting for customer review</h6>
                                    <small class="text-muted">The customer hasn't submitted a review yet.</small>
                                </div>
                            </div>
                        @endif

                        @if(!$order->completion)
                            @if($order->status === \App\Models\Order::STATUS_ON_PROGRESS)
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="bi bi-clipboard-check display-4 text-muted mb-3"></i>
                                        <h5>Ready to complete this job?</h5>
                                        <p class="text-muted">Submit completion proof with photos and description</p>
                                        <a href="{{ route('tukang.jobs.complete', $order) }}" class="btn btn-success btn-lg">
                                            <i class="bi bi-check-circle"></i> Submit Completion Proof
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </x-app-layout>
