<x-app-layout>
    @push('styles')
        @vite(['resources/css/tukang/finance.css'])
    @endpush

    <div class="finance-container">
        <!-- Header -->
        <div class="finance-header">
            <h1 class="page-title">
                <i class="bi bi-chat-dots-fill text-brand-orange me-3"></i>Messages
            </h1>
            <p class="page-subtitle">Your conversations with handymen about your orders</p>
        </div>

        <!-- Chat Rooms List -->
        <div class="chat-rooms-list d-flex flex-column gap-3">
            @forelse($chatRooms as $room)
                <a href="{{ route('chat.show', ['receiverType' => 'tukang', 'receiverId' => $room->tukang->id]) }}{{ $room->service ? '?service_id=' . $room->service->id : '' }}" 
                   class="wallet-card p-4 border rounded-4 shadow-sm text-decoration-none text-dark d-block position-relative"
                   style="border-radius: 20px; transition: transform 0.2s; border: 1px solid #f0f0f0;">
                    
                    <div class="d-flex align-items-center">
                        <!-- Avatar -->
                        <div class="position-relative me-4">
                            <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3);">
                                {{ substr($room->tukang->name, 0, 1) }}
                            </div>
                            @if($room->unread_count > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white border-2" style="padding: 0.5em 0.7em;">
                                    {{ $room->unread_count }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="fw-bold mb-0 text-dark">{{ $room->tukang->name }}</h5>
                                <small class="text-muted fw-semibold" style="font-size: 0.8rem;">
                                    {{ $room->latest_message->created_at->diffForHumans() }}
                                </small>
                            </div>
                            
                            @if($room->service)
                                <div class="mb-1 text-uppercase fw-bold" style="font-size: 0.75rem; color: #F57C00; letter-spacing: 0.5px;">
                                    <i class="bi bi-tools me-1"></i>{{ $room->service->name }}
                                </div>
                            @endif
                            
                            <p class="text-muted mb-0 text-truncate" style="max-width: 90%;">
                                {{ $room->latest_message->message }}
                            </p>
                        </div>

                        <!-- Arrow -->
                        <div class="ms-3 text-muted opacity-50">
                            <i class="bi bi-chevron-right fs-4"></i>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-5">
                    <div style="width: 100px; height: 100px; background: #FFF3E0; border-radius: 50%; display: flex; align-items-center; justify-content: center; margin: 0 auto 2rem;">
                        <i class="bi bi-chat-heart text-brand-orange" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark">No Messages Yet</h4>
                    <p class="text-muted">Start chatting with handymen to see your conversations here.</p>
                    <a href="{{ route('find-tukang') }}" class="btn btn-primary mt-3 px-5 py-3 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #FF9800, #F57C00); border: none;">
                        <i class="bi bi-search me-2"></i> Find Handymen
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Extra Styles for hover effects -->
    <style>
        .wallet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
            border-color: #FF9800 !important;
        }
    </style>
</x-app-layout>
