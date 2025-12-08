<x-app-layout>
    <div class="container py-4">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <a href="{{ route('tukang.dashboard') }}" class="btn btn-outline-secondary mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <h2 class="fw-bold mb-0">My Jobs</h2>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            @forelse($jobs as $job)
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="fw-bold">{{ $job->service->name }}</h5>
                                    <p class="text-muted mb-2">Order #{{ $job->order_number }}</p>
                                    <p class="mb-2">Customer: {{ $job->customer->name }}</p>
                                    <span class="badge bg-{{ $job->status_color }}">{{ ucfirst($job->status) }}</span>
                                    
                                    @if($job->review)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <div class="d-flex align-items-center mb-2">
                                                <strong class="me-2">Customer Review:</strong>
                                                <div class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $job->review->rating)
                                                            <i class="bi bi-star-fill"></i>
                                                        @else
                                                            <i class="bi bi-star"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="text-dark ms-1">({{ $job->review->rating }}/5)</span>
                                                </div>
                                            </div>
                                            @if($job->review->review_text)
                                                <p class="mb-0 small text-muted">"{{ $job->review->review_text }}"</p>
                                            @endif
                                        </div>
                                    @elseif($job->status === 'completed')
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> Waiting for customer review
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="fw-bold text-primary mb-2">Rp {{ number_format($job->total_price, 0, ',', '.') }}</p>

                                    @if($job->status === 'on_progress' && !$job->completion)
                                        <a href="{{ route('tukang.jobs.complete', $job) }}" class="btn btn-primary btn-sm">
                                            Submit Completion
                                        </a>
                                    @endif

                                    <a href="{{ route('tukang.jobs.show', $job) }}" class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No jobs found</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $jobs->links() }}
        </div>
    </div>
</x-app-layout>
