<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-chat-dots me-2"></i>My Conversations
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        @forelse($chatRooms as $room)
                            <a href="{{ route('chat.show', ['receiverType' => 'tukang', 'receiverId' => $room->tukang->id]) }}{{ $room->service ? '?service_id=' . $room->service->id : '' }}" 
                               class="text-decoration-none">
                                <div class="border-bottom p-3 hover-bg-light">
                                    <div class="d-flex align-items-start">
                                        <!-- Avatar -->
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                             style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;">
                                            {{ substr($room->tukang->name, 0, 1) }}
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="mb-0 fw-bold text-dark">{{ $room->tukang->name }}</h6>
                                                <small class="text-muted">{{ $room->latest_message->created_at->diffForHumans() }}</small>
                                            </div>
                                            
                                            @if($room->service)
                                                <p class="mb-1 text-muted small">
                                                    <i class="bi bi-tools me-1"></i>{{ $room->service->name }}
                                                </p>
                                            @endif
                                            
                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 400px;">
                                                {{ $room->latest_message->message }}
                                            </p>
                                            
                                            @if($room->unread_count > 0)
                                                <span class="badge bg-primary mt-1">{{ $room->unread_count }} new</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-chat-dots text-muted" style="font-size: 4rem;"></i>
                                <h5 class="text-muted mt-3">No conversations yet</h5>
                                <p class="text-muted">Start chatting with handymen to see conversations here</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-house me-1"></i>Go to Dashboard
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }
    </style>
</x-app-layout>
