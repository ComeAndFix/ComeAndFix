<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Submit Job Completion</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>{{ $order->service->name }}</h5>
                            <p class="text-muted">Order #{{ $order->order_number }}</p>

                            @if($order->completion && $order->completion->isRejected())
                                <div class="alert alert-warning">
                                    <strong>Previous submission was rejected:</strong><br>
                                    {{ $order->completion->rejection_reason }}
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('tukang.jobs.submitCompletion', $order) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Work Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $order->completion?->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 20 characters</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Working Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" name="working_duration" class="form-control @error('working_duration') is-invalid @enderror" value="{{ old('working_duration', $order->completion?->working_duration) }}" required min="1">
                                @error('working_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Photos <span class="text-danger">*</span></label>
                                <input type="file" name="photos[]" class="form-control @error('photos.*') is-invalid @enderror" multiple accept="image/jpeg,image/jpg,image/png" required>
                                @error('photos.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload at least one photo (JPEG, JPG, PNG, max 2MB each)</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Submit Completion</button>
                                <a href="{{ route('tukang.jobs.show', $order) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
