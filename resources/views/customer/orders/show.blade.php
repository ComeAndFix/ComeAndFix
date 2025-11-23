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
                                <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>

                        @if($order->completion)
                            <hr>
                            <h5 class="mb-3">Completion Proof</h5>

                            <div class="mb-3">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $order->completion->status === 'approved' ? 'success' : ($order->completion->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->completion->status) }}
                                </span>
                            </div>

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

                            @if($order->completion->isPending())
                                <div class="d-flex gap-2 mt-4">
                                    <form action="{{ route('customer.orders.approveCompletion', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Approve Completion
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </div>
                            @elseif($order->completion->isRejected())
                                <div class="alert alert-warning mt-3">
                                    <strong>Rejection Reason:</strong><br>
                                    {{ $order->completion->rejection_reason }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('customer.orders.rejectCompletion', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Completion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                            <small class="text-muted">Minimum 10 characters</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
