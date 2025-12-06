<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Dashboard Header -->
        <section class="bg-primary text-white py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="h3 fw-bold mb-2">
                            <i class="bi bi-tools me-2"></i>
                            Handyman Dashboard
                        </h1>
                        <p class="mb-0">Welcome back, {{ Auth::guard('tukang')->user()->name }}! Here's your job overview for today.</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <div class="badge bg-success fs-6 me-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Available
                        </div>
                        <button class="btn btn-outline-light btn-sm">
                            <i class="bi bi-gear me-1"></i>
                            Settings
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Job Overview Cards -->
        <section class="py-4 bg-light border-bottom">
            <div class="container">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark border-0 h-100 cursor-pointer" onclick="showNewChats()">
                            <div class="card-body text-center">
                                <i class="bi bi-chat-dots display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1" id="new-messages-count">{{ $newMessagesCount }}</h3>
                                <p class="mb-0 small">New Messages</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-play-circle display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">0</h3>
                                <p class="mb-0 small">Active Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">0</h3>
                                <p class="mb-0 small">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white border-0 h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-currency-dollar display-6 mb-2"></i>
                                <h3 class="fw-bold mb-1">Rp 0</h3>
                                <p class="mb-0 small">This Month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-4">
            <div class="container">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Chat Messages Container -->
                        <div class="card border-0 shadow-sm mb-4" id="chat-container" style="display: none;">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-chat-dots me-2"></i>
                                    Recent Messages
                                </h5>
                                <button class="btn btn-sm btn-outline-secondary" onclick="hideChatContainer()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div id="messages-list">
                                    <!-- Messages will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- Incoming Job Requests -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-inbox me-2"></i>
                                    Incoming Job Requests
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div id="job-requests-list">
                                    @forelse($recentMessages as $message)
                                        <div class="border-bottom p-3 hover-bg-light cursor-pointer" onclick="openChat('customer', {{ $message->sender_id }})">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                                    {{ substr($message->sender->name, 0, 1) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <h6 class="mb-0 fw-bold">{{ $message->sender->name }}</h6>
                                                        <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="mb-1 text-muted">{{ Str::limit($message->message, 60) }}</p>
                                                    @if(!$message->read_at)
                                                        <span class="badge bg-warning text-dark">New</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox display-4 mb-2"></i>
                                            <p>No job requests yet</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-bold">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('tukang.jobs.index') }}" class="btn btn-primary">
                                        <i class="bi bi-list-task me-2"></i>View My Jobs
                                    </a>
                                    <button class="btn btn-primary" onclick="showNewChats()">
                                        <i class="bi bi-chat-dots me-2"></i>View Messages
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-calendar-plus me-2"></i>Update Schedule
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-person-gear me-2"></i>Edit Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .cursor-pointer { cursor: pointer; }
        .hover-bg-light:hover { background-color: #f8f9fa; }
    </style>

    <script>
            function showNewChats() {
                const chatContainer = document.getElementById('chat-container');
                chatContainer.style.display = 'block';
                loadRecentMessages();
            }

            function hideChatContainer() {
                const chatContainer = document.getElementById('chat-container');
                chatContainer.style.display = 'none';
            }

            function openChat(receiverType, receiverId) {
                window.location.href = `/tukang/chat/${receiverType}/${receiverId}`;
            }

            function loadRecentMessages() {
                const messagesList = document.getElementById('messages-list');
                messagesList.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>';

                fetch('{{ route("tukang.messages.recent") }}')
                    .then(response => response.json())
                    .then(messages => {
                        if (messages.length === 0) {
                            messagesList.innerHTML = `
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-chat-dots display-4 mb-2"></i>
                                    <p>No messages yet</p>
                                </div>
                            `;
                            return;
                        }

                        messagesList.innerHTML = messages.map(message => `
                            <div class="border-bottom p-3 hover-bg-light cursor-pointer" onclick="openChat('customer', ${message.sender_id})">
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                        ${message.sender.name.charAt(0)}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-bold">${message.sender.name}</h6>
                                            <small class="text-muted">${formatTime(message.created_at)}</small>
                                        </div>
                                        <p class="mb-1 text-muted">${message.message.substring(0, 60)}${message.message.length > 60 ? '...' : ''}</p>
                                        ${!message.read_at ? '<span class="badge bg-warning text-dark">New</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    })
                    .catch(error => {
                        console.error('Error loading messages:', error);
                        messagesList.innerHTML = '<div class="text-center py-3 text-danger">Error loading messages</div>';
                    });
            }

            function formatTime(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = now - date;
                const hours = Math.floor(diff / (1000 * 60 * 60));

                if (hours < 1) return 'Just now';
                if (hours < 24) return `${hours}h ago`;
                return date.toLocaleDateString();
            }

            document.addEventListener('DOMContentLoaded', function() {
                setInterval(function() {
                    fetch('{{ route("tukang.messages.recent") }}')
                        .then(response => response.json())
                        .then(messages => {
                            const unreadCount = messages.filter(msg => !msg.read_at).length;
                            document.getElementById('new-messages-count').textContent = unreadCount;
                        })
                        .catch(error => console.error('Error refreshing message count:', error));
                }, 30000);
            });
    </script>
</x-app-layout>
