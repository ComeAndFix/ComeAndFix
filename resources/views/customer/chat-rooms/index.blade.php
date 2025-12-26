<x-app-layout>
    @push('styles')
        @vite(['resources/css/tukang/finance.css', 'resources/css/customer/messages.css'])
    @endpush

    <div class="finance-container">
        <!-- Header -->
        <div class="finance-header">
            <h1 class="page-title">
                <i class="bi bi-chat-dots-fill text-brand-orange me-3"></i>Messages
            </h1>
            <p class="page-subtitle">Your conversations with handymen</p>
        </div>

        <!-- Chat Rooms List -->
        <div class="chat-rooms-list">
            @forelse($chatRooms as $room)
                <a href="{{ route('chat.show', ['receiverType' => 'tukang', 'receiverId' => $room->tukang->id]) }}{{ $room->service ? '?service_id=' . $room->service->id : '' }}" 
                   class="chat-room-item">
                    <div class="chat-room-card">
                        <div class="d-flex align-items-start">
                            <!-- Avatar -->
                            <div class="position-relative me-3">
                                <div class="chat-avatar">
                                    {{ substr($room->tukang->name, 0, 1) }}
                                </div>
                                @if($room->unread_count > 0)
                                    <span class="unread-badge">{{ $room->unread_count }}</span>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="chat-name mb-0">{{ $room->tukang->name }}</h6>
                                    <small class="chat-time">{{ $room->latest_message->created_at->diffForHumans() }}</small>
                                </div>
                                
                                @if($room->service)
                                    <div class="chat-service mb-1">
                                        <i class="bi bi-tools me-1"></i>{{ $room->service->name }}
                                    </div>
                                @endif
                                
                                <p class="chat-preview mb-0">
                                    {{ $room->latest_message->message }}
                                </p>
                            </div>

                            <!-- Arrow -->
                            <div class="ms-3 chat-arrow">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h5 class="empty-state-title">No conversations yet</h5>
                    <p class="empty-state-text">Start chatting with handymen to see conversations here</p>
                    <a href="{{ route('find-tukang') }}" class="btn btn-brand-orange rounded-pill px-4 mt-3">
                        <i class="bi bi-search me-1"></i> Find Handymen
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
