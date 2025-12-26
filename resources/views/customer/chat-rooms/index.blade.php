<x-app-layout>
    <div class="messages-page-container">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0">Messages</h1>
                <p class="text-muted mb-0 small">Your conversations with handymen</p>
            </div>
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

    <style>
        .messages-page-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .chat-rooms-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .chat-room-item {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .chat-room-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
            border: 1px solid #f0f0f0;
        }

        .chat-room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-color: var(--brand-orange);
        }

        .chat-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-orange) 0%, #ff8c42 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .unread-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            border: 2px solid white;
        }

        .chat-name {
            font-weight: 700;
            color: #1a1a1a;
            font-size: 1rem;
        }

        .chat-time {
            color: #999;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .chat-service {
            color: var(--brand-orange);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .chat-preview {
            color: #666;
            font-size: 0.9rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-arrow {
            color: #ccc;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }

        .chat-room-card:hover .chat-arrow {
            color: var(--brand-orange);
            transform: translateX(4px);
        }

        .min-width-0 {
            min-width: 0;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            font-size: 5rem;
            color: #e0e0e0;
            margin-bottom: 1.5rem;
        }

        .empty-state-title {
            color: #666;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .empty-state-text {
            color: #999;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .messages-page-container {
                padding: 1rem;
            }

            .chat-room-card {
                padding: 1rem;
            }

            .chat-avatar {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }

            .empty-state {
                padding: 3rem 1.5rem;
            }
        }
    </style>
</x-app-layout>
