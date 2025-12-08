<x-app-layout>
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <a href="{{ route('tukang.dashboard') }}" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-chat-dots me-2"></i>Messages
                </h2>
            </div>
        </div>

        <div class="row g-4">
            @forelse($chatRooms as $room)
                <div class="col-12">
                    <a href="{{ route('tukang.chat.show', ['receiverType' => $room->contact_type, 'receiverId' => $room->contact_id]) }}" 
                       class="text-decoration-none">
                        <div class="card border-0 shadow-sm hover-lift">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <!-- Avatar -->
                                    <div class="position-relative">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                             style="width: 56px; height: 56px; font-size: 20px; font-weight: bold;">
                                            {{ substr($room->contact->name, 0, 1) }}
                                        </div>
                                        @if($room->unread_count > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $room->unread_count }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Contact Info -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div>
                                                <h5 class="mb-0 fw-bold text-dark">{{ $room->contact->name }}</h5>
                                                <small class="text-muted">{{ ucwords($room->contact_type) }}</small>
                                            </div>
                                            @if($room->last_message)
                                                <small class="text-muted">{{ $room->last_message->created_at->diffForHumans() }}</small>
                                            @endif
                                        </div>

                                        @if($room->last_message)
                                            <p class="mb-0 text-muted small">
                                                @if($room->last_message->sender_id == Auth::guard('tukang')->id())
                                                    <i class="bi bi-check-all me-1"></i>
                                                @endif
                                                {{ Str::limit($room->last_message->message, 80) }}
                                            </p>
                                        @else
                                            <p class="mb-0 text-muted small fst-italic">No messages yet</p>
                                        @endif

                                        @if($room->unread_count > 0)
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark">
                                                    {{ $room->unread_count }} new {{ Str::plural('message', $room->unread_count) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Arrow -->
                                    <div class="ms-3">
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-chat-dots display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No conversations yet</h5>
                            <p class="text-muted">When customers message you, they'll appear here.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</x-app-layout>
