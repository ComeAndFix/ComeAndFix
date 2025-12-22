<x-app-layout>
    @include('components.payment-popup')
    @push('styles')
        @vite(['resources/css/components/chat.css'])
    @endpush

    <div class="chat-page-wrapper">
        <div class="chat-container">
            <!-- Header -->
            <div class="chat-header">
                <div class="chat-header-avatar">
                    {{ substr($receiver->name, 0, 1) }}
                </div>
                <div class="chat-header-info">
                    <h2 class="chat-header-name">{{ $receiver->name }}</h2>
                    <div class="chat-header-status">
                         <span class="chat-status-dot"></span>
                         {{ ucwords($receiverType) }} • Online
                    </div>
                </div>
                <button onclick="history.back()" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="messages-container">
                <div id="messages">
                    @foreach($messages as $message)
                        @if($message->message_type === 'order_proposal' && $message->order)
                            <div class="order-proposal-card received" data-order-id="{{ $message->order->uuid }}">
                                <div class="proposal-badge">
                                    <i class="bi bi-briefcase-fill"></i> Order Proposal
                                </div>
                                <h3 class="proposal-title">{{ $message->order->service ? $message->order->service->name : 'Service' }}</h3>
                                
                                <div class="proposal-details">
                                    <div class="proposal-detail">
                                        <span class="detail-label">Order Number</span>
                                        <span class="detail-value">#{{ $message->order->order_number }}</span>
                                    </div>
                                    @if($message->order->work_datetime)
                                    <div class="proposal-detail">
                                        <span class="detail-label">Expected Date</span>
                                        <span class="detail-value">{{ $message->order->work_datetime->format('d M Y, H:i') }}</span>
                                    </div>
                                    @endif
                                    @if($message->order->working_address)
                                    <div class="proposal-detail">
                                        <span class="detail-label">Location</span>
                                        <span class="detail-value text-end" style="max-width: 60%">{{ $message->order->working_address }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="proposal-price-tag" style="display: block;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="price-label">{{ ($message->order->additionalItems && $message->order->additionalItems->count() > 0) || ($message->order->customItems && $message->order->customItems->count() > 0) ? 'Total Estimate' : 'Base Price' }}</span>
                                        <span class="price-amount">Rp {{ number_format($message->order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="text-end mt-2">
                                        <a href="javascript:void(0)" onclick="toggleDetails('{{ $message->order->uuid }}')" id="toggle-btn-{{ $message->order->uuid }}" class="text-muted small text-decoration-none" style="font-size: 0.8rem;">
                                            Click to see details <i class="bi bi-chevron-down"></i>
                                        </a>
                                    </div>

                                    <div id="details-{{ $message->order->uuid }}" class="mt-3 pt-3 border-top" style="display: none; border-color: #eee !important;">
                                        {{-- Base Price --}}
                                        <div class="d-flex justify-content-between mb-2 small text-muted">
                                            <span>{{ $message->order->service ? $message->order->service->name : 'Base Service' }}</span>
                                            <span>Rp {{ number_format($message->order->price, 0, ',', '.') }}</span>
                                        </div>

                                        {{-- Additional Items --}}
                                        @if($message->order->additionalItems)
                                            @foreach($message->order->additionalItems as $item)
                                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                                <span>{{ $item->item_name }} (x{{ $item->quantity }})</span>
                                                <span>Rp {{ number_format($item->item_price * $item->quantity, 0, ',', '.') }}</span>
                                            </div>
                                            @endforeach
                                        @endif

                                        {{-- Custom Items --}}
                                        @if($message->order->customItems)
                                            @foreach($message->order->customItems as $item)
                                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                                <span>{{ $item->item_name }} (x{{ $item->quantity }})</span>
                                                <span>Rp {{ number_format($item->item_price * $item->quantity, 0, ',', '.') }}</span>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="proposal-actions">
                                    @if($message->order->status === 'pending' && !$message->order->isExpired())
                                        <button type="button" class="btn btn-success rounded-pill" onclick="acceptOrder('{{ $message->order->uuid }}')">
                                            Accept
                                        </button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill" onclick="rejectOrder('{{ $message->order->uuid }}')">
                                            Decline
                                        </button>
                                    @elseif($message->order->status === 'accepted' && $message->order->payment_status !== 'paid')
                                        <button type="button" class="btn btn-brand-orange rounded-pill w-100 px-4 fw-bold" style="grid-column: span 2" onclick="showPaymentForOrder('{{ $message->order->uuid }}', {{ json_encode([
                                            'id' => $message->order->uuid,
                                            'service_name' => $message->order->service ? $message->order->service->name : 'Service',
                                            'total_amount' => $message->order->total_price,
                                            'description' => $message->order->service_description ?? '',
                                            'order_number' => $message->order->order_number,
                                            'items' => array_merge(
                                                [[
                                                    'name' => $message->order->service ? $message->order->service->name : 'Base Service',
                                                    'quantity' => 1,
                                                    'price' => $message->order->price,
                                                    'is_base' => true
                                                ]],
                                                $message->order->additionalItems->map(fn($item) => [
                                                    'name' => $item->item_name,
                                                    'quantity' => $item->quantity,
                                                    'price' => $item->item_price
                                                ])->toArray(),
                                                $message->order->customItems->map(fn($item) => [
                                                    'name' => $item->item_name,
                                                    'quantity' => $item->quantity,
                                                    'price' => $item->item_price
                                                ])->toArray()
                                            )
                                        ]) }})">
                                            <i class="bi bi-credit-card"></i> Pay Now
                                        </button>
                                    @else
                                        <div class="w-100 text-center" style="grid-column: span 2">
                                            <span class="status-badge status-badge-{{ $message->order->status === 'accepted' || $message->order->status === 'completed' ? 'success' : ($message->order->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucwords(str_replace('_', ' ', $message->order->status)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="message-wrapper {{ $message->sender_type === 'App\Models\Customer' ? 'sent' : 'received' }}">
                                <div class="message-bubble">
                                    {{ $message->message }}
                                </div>
                                <div class="message-time">
                                    {{ $message->created_at->format('H:i') }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Input Form -->
            <div class="chat-input-area">
                <form id="message-form">
                    @csrf
                    <div class="message-form-container">
                        <input type="hidden" id="receiver-id" value="{{ $receiver->id }}">
                        <input type="hidden" id="receiver-type" value="{{ $receiverType }}">
                        <input type="text"
                               id="message-input"
                               class="chat-input"
                               placeholder="Message {{ $receiver->name }}..."
                               autocomplete="off"
                               required>
                        <button type="submit" class="chat-send-btn">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const conversationId = '{{ $conversationId }}';
            const messagesContainer = document.getElementById('messages');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Toggle Details Function
            window.toggleDetails = function(orderId) {
                const detailsDiv = document.getElementById(`details-${orderId}`);
                const toggleBtn = document.getElementById(`toggle-btn-${orderId}`);
                
                if (detailsDiv.style.display === 'none') {
                    detailsDiv.style.display = 'block';
                    toggleBtn.innerHTML = 'Hide details <i class="bi bi-chevron-up"></i>';
                } else {
                    detailsDiv.style.display = 'none';
                    toggleBtn.innerHTML = 'Click to see details <i class="bi bi-chevron-down"></i>';
                }
            };

            // Global function to show payment modal
            window.showPaymentForOrder = function(orderId, orderData) {
                console.log('Showing payment for order:', orderId, orderData);

                if (window.paymentHandler) {
                    window.paymentHandler.showPaymentModal(orderData);
                } else if (window.PaymentHandler) {
                    window.paymentHandler = new window.PaymentHandler();
                    window.paymentHandler.init();
                    window.paymentHandler.showPaymentModal(orderData);
                } else {
                    showErrorAlert('Payment system not available. Please refresh the page.');
                }
            };

            // WebSocket listeners
            if (window.Echo) {
                console.log('Echo listener started for channel:', `chat.${conversationId}`);
                window.Echo.channel(`chat.${conversationId}`)
                    .listen('MessageSent', (e) => {
                        console.log('New message received from WebSocket:', e);
                        const currentUserId = {{ Auth::guard('customer')->user()->id }};
                        const currentUserType = 'App\\Models\\Customer';
                        
                        // Check if message is from others
                        if (e.message && (e.message.sender_id !== currentUserId || e.message.sender_type !== currentUserType)) {
                            console.log('Appending received message to chat');
                            addMessageToChat(e.message, false);
                            scrollToBottom();
                        } else {
                            console.log('Message is from current user, skipping WebSocket append');
                        }
                    })
                    .listen('OrderProposalSent', (e) => {
                        console.log('Order proposal received from WebSocket:', e);
                        showOrderProposal(e.order);
                        scrollToBottom();
                    })
                    .listen('OrderStatusUpdated', (e) => {
                        console.log('Order status updated event received from WebSocket:', e);
                        updateOrderStatus(e.order);
                        showOrderStatusUpdate(e.order);
                        scrollToBottom();
                    })
                    .error((error) => {
                        console.error('WebSocket connection error:', error);
                    });
            } else {
                console.error('Echo is not available on window object');
            }

            // Message form handler
            messageForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const message = messageInput.value.trim();
                if (!message) {
                    return;
                }

                const receiverId = document.getElementById('receiver-id').value;
                const receiverType = document.getElementById('receiver-type').value;
                
                // Extract service_type from URL if present
                const urlParams = new URLSearchParams(window.location.search);
                const serviceType = urlParams.get('service_type');

                const requestBody = {
                    message: message,
                    receiver_id: parseInt(receiverId),
                    receiver_type: receiverType
                };
                
                // Include service_type if available
                if (serviceType) {
                    requestBody.service_type = serviceType;
                }

                try {
                    const response = await fetch('{{ route("chat.send") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(requestBody)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        console.log('Message sent successfully via AJAX:', data.message);
                        addMessageToChat(data.message, true);
                        messageInput.value = '';
                        scrollToBottom();
                    } else {
                        console.error('Failed to send message:', data);
                        showErrorAlert('Failed to send message: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    showErrorAlert('Network error. Please check your connection and try again.');
                }
            });

            function updateOrderStatus(order) {
                const orderElement = document.querySelector(`[data-order-id="${order.uuid}"]`);
                if (!orderElement) return;

                const actionsContainer = orderElement.querySelector('.proposal-actions');
                
                if (actionsContainer) {
                    const totalPrice = calculateOrderTotal(order);
                    let html = '';

                    if (order.status === 'completed') {
                        html = `<div class="w-100 text-center" style="grid-column: span 2"><span class="status-badge status-badge-success">Completed</span></div>`;
                    } else if (order.status === 'accepted' && order.payment_status !== 'paid') {
                        // Construct line items for the payment modal
                        const lineItems = [];
                        
                        // 1. Base Service
                        lineItems.push({
                            name: order.service ? order.service.name : 'Base Service',
                            quantity: 1,
                            price: parseFloat(order.price || 0),
                            is_base: true
                        });

                        // 2. Additional Items
                        if (order.additional_items) {
                            order.additional_items.forEach(item => {
                                lineItems.push({
                                    name: item.item_name,
                                    quantity: parseInt(item.quantity) || 1,
                                    price: parseFloat(item.item_price)
                                });
                            });
                        }

                        // 3. Custom Items
                        if (order.custom_items) {
                            order.custom_items.forEach(item => {
                                lineItems.push({
                                    name: item.item_name,
                                    quantity: parseInt(item.quantity) || 1,
                                    price: parseFloat(item.item_price)
                                });
                            });
                        }

                        const orderData = {
                            id: order.uuid,
                            service_name: order.service ? order.service.name : 'Service',
                            total_amount: totalPrice,
                            description: order.service_description || '',
                            order_number: order.order_number,
                            items: lineItems
                        };

                        html = `
                            <button type="button" class="btn btn-brand-orange rounded-pill w-100 px-4 fw-bold" style="grid-column: span 2" onclick='showPaymentForOrder("${order.uuid}", ${JSON.stringify(orderData).replace(/'/g, "&apos;")})'>
                                <i class="bi bi-credit-card"></i> Pay Now
                            </button>
                        `;
                    } else if (order.payment_status === 'paid' && order.status !== 'completed') {
                        html = `
                            <div class="w-100 text-center" style="grid-column: span 2">
                                <span class="status-badge status-badge-info">On Progress</span>
                                <span class="status-badge status-badge-success ms-1">Paid</span>
                            </div>
                        `;
                    } else {
                        const badgeClass = order.status === 'rejected' ? 'danger' : 'warning';
                        html = `<div class="w-100 text-center" style="grid-column: span 2"><span class="status-badge status-badge-${badgeClass}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></div>`;
                    }

                    actionsContainer.innerHTML = html;
                }
            }
            
            function calculateOrderTotal(order) {
                let total = parseFloat(order.price) || 0;
                
                if (order.additional_items) {
                    order.additional_items.forEach(item => {
                        total += (parseFloat(item.item_price) || 0) * (parseInt(item.quantity) || 1);
                    });
                }
                
                if (order.custom_items) {
                    order.custom_items.forEach(item => {
                        total += (parseFloat(item.item_price) || 0) * (parseInt(item.quantity) || 1);
                    });
                }
                
                return total;
            }


            function showOrderProposal(order) {
                if(document.querySelector(`[data-order-id="${order.uuid}"]`)){
                    return;
                }

                // Calculate total price
                const totalPrice = calculateOrderTotal(order);
                const displayPrice = (totalPrice || order.price);

                // Build details HTML
                let detailsHtml = `
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>${order.service ? order.service.name : 'Base Service'}</span>
                        <span>Rp ${parseInt(order.price).toLocaleString('id-ID')}</span>
                    </div>
                `;

                if (order.additional_items) {
                    order.additional_items.forEach(item => {
                        detailsHtml += `
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>${item.item_name} (x${item.quantity})</span>
                                <span>Rp ${(parseInt(item.item_price) * parseInt(item.quantity)).toLocaleString('id-ID')}</span>
                            </div>
                        `;
                    });
                }

                if (order.custom_items) {
                    order.custom_items.forEach(item => {
                        detailsHtml += `
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>${item.item_name} (x${item.quantity})</span>
                                <span>Rp ${(parseInt(item.item_price) * parseInt(item.quantity)).toLocaleString('id-ID')}</span>
                            </div>
                        `;
                    });
                }

                const orderDiv = document.createElement('div');
                orderDiv.className = 'order-proposal-card received';
                orderDiv.setAttribute('data-order-id', order.uuid);

                orderDiv.innerHTML = `
                    <div class="proposal-badge">
                        <i class="bi bi-briefcase-fill"></i> Order Proposal
                    </div>
                    <h3 class="proposal-title">${order.service ? order.service.name : 'Service'}</h3>
                    
                    <div class="proposal-details">
                        <div class="proposal-detail">
                            <span class="detail-label">Order Number</span>
                            <span class="detail-value">#${order.order_number}</span>
                        </div>
                        ${order.work_datetime ? `
                        <div class="proposal-detail">
                            <span class="detail-label">Expected Date</span>
                            <span class="detail-value">${formatDateTime(order.work_datetime)}</span>
                        </div>` : ''}
                        ${order.working_address ? `
                        <div class="proposal-detail">
                            <span class="detail-label">Location</span>
                            <span class="detail-value text-end" style="max-width: 60%">${order.working_address}</span>
                        </div>` : ''}
                    </div>

                    <div class="proposal-price-tag" style="display: block;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-label">Total Estimate</span>
                            <span class="price-amount">Rp ${parseInt(displayPrice).toLocaleString('id-ID')}</span>
                        </div>
                        
                        <div class="text-end mt-2">
                            <a href="javascript:void(0)" onclick="toggleDetails('${order.uuid}')" id="toggle-btn-${order.uuid}" class="text-muted small text-decoration-none" style="font-size: 0.8rem;">
                                Click to see details <i class="bi bi-chevron-down"></i>
                            </a>
                        </div>

                        <div id="details-${order.uuid}" class="mt-3 pt-3 border-top" style="display: none; border-color: #eee !important;">
                            ${detailsHtml}
                        </div>
                    </div>

                    <div class="proposal-actions">
                        <button type="button" class="btn btn-success rounded-pill" onclick="acceptOrder('${order.uuid}')">
                            Accept
                        </button>
                        <button type="button" class="btn btn-outline-danger rounded-pill" onclick="rejectOrder('${order.uuid}')">
                            Decline
                        </button>
                    </div>
                `;

                messagesContainer.appendChild(orderDiv);
                scrollToBottom();
            }

            function showOrderStatusUpdate(order) {
                const statusDiv = document.createElement('div');
                statusDiv.className = 'order-status mb-3 text-center';

                let statusText = '';
                let statusClass = 'info';

                switch(order.status) {
                    case 'accepted':
                        if (order.payment_status === 'paid') {
                            statusText = 'Payment completed! Order is now in progress 🎉';
                            statusClass = 'success';
                        } else {
                            statusText = 'You accepted the order! Please complete payment 💳';
                            statusClass = 'success';
                        }
                        break;
                    case 'rejected':
                        statusText = 'You rejected the order';
                        statusClass = 'danger';
                        break;
                    case 'completed':
                        statusText = 'Work completed! Hope you are happy with the results ✨';
                        statusClass = 'success';
                        break;
                    default:
                        statusText = `Order ${order.status}`;
                }

                statusDiv.innerHTML = `
                    <span class="status-badge status-badge-${statusClass}">
                        ${statusText} • #${order.order_number}
                    </span>
                `;
                messagesContainer.appendChild(statusDiv);
                scrollToBottom();
            }

            window.acceptOrder = async function(orderId) {
                console.log('Accept order called for ID:', orderId);

                const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
                const acceptBtn = orderElement?.querySelector('button[onclick*="acceptOrder"]');

                if (acceptBtn) {
                    acceptBtn.disabled = true;
                    acceptBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Accepting...';
                }

                try {
                    const response = await fetch(`/order/${orderId}/accept`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    console.log('Accept order response:', data);

                    if (data.success) {
                        showSuccessAlert('Order accepted successfully!');

                        const totalPrice = calculateOrderTotal(data.order);
                        
                        // Construct line items for the payment modal summary
                        const lineItems = [];
                        
                        // 1. Base Service Price
                        lineItems.push({
                            name: data.order.service ? data.order.service.name : 'Base Service',
                            quantity: 1,
                            price: parseFloat(data.order.price || 0),
                            is_base: true
                        });

                        // 2. Additional Items
                        if (data.order.additional_items) {
                            data.order.additional_items.forEach(item => {
                                lineItems.push({
                                    name: item.item_name,
                                    quantity: parseInt(item.quantity) || 1,
                                    price: parseFloat(item.item_price)
                                });
                            });
                        }

                        // 3. Custom Items
                        if (data.order.custom_items) {
                            data.order.custom_items.forEach(item => {
                                lineItems.push({
                                    name: item.item_name,
                                    quantity: parseInt(item.quantity) || 1,
                                    price: parseFloat(item.item_price)
                                });
                            });
                        }

                        const orderData = {
                            id: orderId,
                            service_name: data.order.service ? data.order.service.name : 'Service',
                            total_amount: totalPrice,
                            description: data.order.service_description || '',
                            order_number: data.order.order_number,
                            items: lineItems
                        };

                        setTimeout(() => {
                            if (window.paymentHandler) {
                                window.paymentHandler.showPaymentModal(orderData);
                            } else if (window.PaymentHandler) {
                                window.paymentHandler = new window.PaymentHandler();
                                window.paymentHandler.init();
                                window.paymentHandler.showPaymentModal(orderData);
                            } else {
                                showErrorAlert('Payment system not available. Please refresh the page.');
                            }
                        }, 500);

                    } else {
                        showErrorAlert(data.error || 'Failed to accept order');
                        if (acceptBtn) {
                            acceptBtn.disabled = false;
                            acceptBtn.innerHTML = '<i class="bi bi-check-circle"></i> Accept';
                        }
                    }
                } catch (error) {
                    console.error('Error accepting order:', error);
                    showErrorAlert('Failed to accept order. Please try again.');
                    if (acceptBtn) {
                        acceptBtn.disabled = false;
                        acceptBtn.innerHTML = '<i class="bi bi-check-circle"></i> Accept';
                    }
                }
            };

            window.rejectOrder = async function(orderId) {
                const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
                const rejectBtn = orderElement?.querySelector('button[onclick*="rejectOrder"]');

                if (rejectBtn) {
                    rejectBtn.disabled = true;
                    rejectBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...';
                }

                try {
                    const response = await fetch(`/order/${orderId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showSuccessAlert('Order rejected');
                    } else {
                        showErrorAlert(data.error || 'Failed to reject order');
                        if (rejectBtn) {
                            rejectBtn.disabled = false;
                            rejectBtn.innerHTML = '<i class="bi bi-x-circle"></i> Reject';
                        }
                    }
                } catch (error) {
                    console.error('Error rejecting order:', error);
                    showErrorAlert('Failed to reject order. Please try again.');
                    if (rejectBtn) {
                        rejectBtn.disabled = false;
                        rejectBtn.innerHTML = '<i class="bi bi-x-circle"></i> Reject';
                    }
                }
            };

            function addMessageToChat(message, isSender) {
                console.log('Adding message to chat:', message, 'isSender:', isSender);

                const messageWrapper = document.createElement('div');
                messageWrapper.className = `message-wrapper ${isSender ? 'sent' : 'received'}`;
                messageWrapper.innerHTML = `
                    <div class="message-bubble">
                        ${escapeHtml(message.message)}
                    </div>
                    <div class="message-time">
                        ${formatTime(message.created_at)}
                    </div>
                `;
                messagesContainer.appendChild(messageWrapper);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function formatTime(timestamp) {
                try {
                    let date;
                    if (typeof timestamp === 'number') {
                        date = new Date(timestamp);
                    } else {
                        date = new Date(timestamp);
                    }

                    if (isNaN(date.getTime())) {
                        console.error('Invalid date:', timestamp);
                        return 'Invalid time';
                    }

                    return date.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: 'Asia/Jakarta'
                    });
                } catch (error) {
                    console.error('Error formatting time:', error, timestamp);
                    return 'Invalid time';
                }
            }

            function formatDateTime(timestamp) {
                try {
                    const date = new Date(timestamp);
                    return date.toLocaleString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: 'Asia/Jakarta'
                    });
                } catch (error) {
                    return 'Invalid date';
                }
            }

            function showSuccessAlert(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 5000);
            }

            function showErrorAlert(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 5000);
            }

            function scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) {
                    // Small delay to allow DOM to update
                    setTimeout(() => {
                        container.scrollTop = container.scrollHeight;
                        console.log('Scrolled to bottom, scrollHeight:', container.scrollHeight);
                    }, 50);
                }
            }

            scrollToBottom();

            if (!csrfToken) {
                console.error('CSRF token not found');
                showErrorAlert('Security token missing. Please refresh the page.');
            }
        });
    </script>

    <style>
        /* Small overrides or specific adjustments if needed */
        .chat-container {
            max-width: 1000px;
        }
    </style>

</x-app-layout>
