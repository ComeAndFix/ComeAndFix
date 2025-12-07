<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Order Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>{{ $order->service->name }}</h5>
                                <p class="text-muted">Order #{{ $order->order_number }}</p>
                                <p><strong>Tukang:</strong> {{ $order->tukang->name }}</p>
                                <p><strong>Price:</strong> Rp {{ number_format($order->price, 0, ',', '.') }}</p>
                                <span class="badge bg-{{ $order->status_color }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                        </div>

                        @if($order->completion)
                            <hr>
                            <h5 class="mb-3">Completion Proof</h5>

                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p>{{ $order->completion->description }}</p>
                            </div>

                            <div class="mb-3">
                                <strong>Working Duration:</strong>
                                <p>{{ $order->completion->working_duration }} minutes</p>
                            </div>

                            <div class="mb-3">
                                <strong>Photos:</strong>
                                <div class="row g-2">
                                    @foreach($order->completion->photos as $photo)
                                        <div class="col-md-3">
                                            <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Completion photo">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if($order->status === 'completed')
                                <div class="alert alert-success mt-3">
                                    <i class="bi bi-check-circle"></i> Order completed successfully!
                                </div>
                                
                                @if(!$order->hasReview())
                                    <div class="mt-3">
                                        <a href="{{ route('customer.reviews.create', $order) }}" class="btn btn-warning">
                                            <i class="bi bi-star"></i> Rate & Review Tukang
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-info mt-3">
                                        <i class="bi bi-check-circle-fill"></i> You have already reviewed this order.
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
