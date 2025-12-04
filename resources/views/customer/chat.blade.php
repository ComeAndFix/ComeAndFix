<x-app-layout>
    @include('components.payment-popup')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with {{ $receiver->name }}
            </h2>
            <a href="{{ route('find-tukang') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Map
            </a>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden d-flex flex-column" style="height: 80vh;">
                <!-- Header -->
                <div class="bg-primary text-white p-4 border-b flex-shrink-0">
                    <div class="flex items-center">
                        <div class="rounded-circle me-3 bg-light text-primary d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px; font-weight: bold;">
                            {{ substr($receiver->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $receiver->name }}</h5>
                            <small class="opacity-75">{{ ucfirst($receiverType) }}</small>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="messages-container" class="flex-1 p-4 overflow-y-auto">
                    <div id="messages">
                        @foreach($messages as $message)
                            @if($message->message_type === 'order_proposal' && $message->order)
                                <div class="order-proposal mb-3 text-start" data-order-id="{{ $message->order->id }}">
                                    <div class="d-inline-block bg-warning text-dark p-3 rounded border" style="max-width: 75%;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-briefcase me-2"></i>
                                            <strong>Order Proposal</strong>
                                        </div>
                                        <div class="order-details">
                                            <div><strong>Service:</strong> {{ $message->order->service ? $message->order->service->name : 'Service' }}</div>
                                            <div><strong>Base Price:</strong> Rp {{ number_format($message->order->price, 0, ',', '.') }}</div>
                                            @if($message->order->work_datetime)
                                                <div><strong>Work Date:</strong> {{ $message->order->work_datetime->format('d M Y H:i') }}</div>
                                            @endif
                                            @if($message->order->working_address)
                                                <div><strong>Working Address:</strong> {{ $message->order->working_address }}</div>
                                            @endif
                                            @if($message->order->service_description)
                                                <div><strong>Description:</strong> {{ $message->order->service_description }}</div>
                                            @endif
                                            @if($message->order->additionalItems && $message->order->additionalItems->count() > 0)
                                                <div class="mt-2">
                                                    <strong>Additional Items:</strong>
                                                    <ul class="mb-0 mt-1 small">
                                                        @foreach($message->order->additionalItems as $item)
                                                            <li>{{ $item->item_name }} ({{ $item->quantity }}x) - Rp {{ number_format($item->item_price * $item->quantity, 0, ',', '.') }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            @if($message->order->customItems && $message->order->customItems->count() > 0)
                                                <div class="mt-2">
                                                    <strong>Custom Items:</strong>
                                                    <ul class="mb-0 mt-1 small">
                                                        @foreach($message->order->customItems as $item)
                                                            <li>
                                                                {{ $item->item_name }} ({{ $item->quantity }}x) - Rp {{ number_format($item->item_price * $item->quantity, 0, ',', '.') }}
                                                                @if($item->description)
                                                                    <small class="text-muted d-block">{{ $item->description }}</small>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            @if(($message->order->additionalItems && $message->order->additionalItems->count() > 0) || ($message->order->customItems && $message->order->customItems->count() > 0))
                                                <div class="mt-2"><strong>Total Price:</strong> Rp {{ number_format($message->order->total_price, 0, ',', '.') }}</div>
                                            @endif
                                            <div class="mt-2">
                                                <small>Order #{{ $message->order->order_number }}</small><br>
                                                <small>Expires: {{ $message->order->expires_at->format('d M Y H:i') }}</small>
                                            </div>
                                        </div>
                                        @if($message->order->status === 'pending' && !$message->order->isExpired())
                                            <div class="d-flex gap-2 mt-3">
                                                <button type="button" class="btn btn-success btn-sm" onclick="acceptOrder({{ $message->order->id }})">
                                                    <i class="bi bi-check-circle"></i> Accept
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectOrder({{ $message->order->id }})">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </div>
                                        @elseif($message->order->status === 'accepted' && $message->order->payment_status !== 'paid')
                                            <div class="mt-2">
                                                <span class="badge bg-success">Accepted</span>
                                                <button type="button" class="btn btn-primary btn-sm ms-2" onclick="showPaymentForOrder({{ $message->order->id }}, {{ json_encode([
                                                    'id' => $message->order->id,
                                                    'service_name' => $message->order->service ? $message->order->service->name : 'Service',
                                                    'total_amount' => $message->order->total_price,
                                                    'description' => $message->order->service_description ?? '',
                                                    'order_number' => $message->order->order_number,
                                                    'items' => [[
                                                        'name' => $message->order->service ? $message->order->service->name : 'Service',
                                                        'quantity' => 1,
                                                        'price' => number_format($message->order->total_price, 0, ',', '.')
                                                    ]]
                                                ]) }})">
                                                    <i class="bi bi-credit-card"></i> Pay Now
                                                </button>
                                            </div>
                                        @elseif($message->order->payment_status === 'paid')
                                            <div class="mt-2">
                                                <span class="badge bg-info">On Progress</span>
                                                <span class="badge bg-success ms-1">
                                                    <i class="bi bi-check-circle"></i> Paid
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                <span class="badge bg-{{ $message->order->status === 'accepted' ? 'success' : ($message->order->status === 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($message->order->status) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="message mb-3 {{ $message->sender_type === 'App\Models\Customer' ? 'text-end' : 'text-start' }}">
                                    <div class="d-inline-block {{ $message->sender_type === 'App\Models\Customer' ? 'bg-primary text-white' : 'bg-light' }} p-3 rounded" style="max-width: 75%;">
                                        <div class="message-text">{{ $message->message }}</div>
                                        <small class="d-block mt-1 {{ $message->sender_type === 'App\Models\Customer' ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Input Form -->
                <div class="border-top p-3 flex-shrink-0">
                    <form id="message-form" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" id="receiver-id" value="{{ $receiver->id }}">
                        <input type="hidden" id="receiver-type" value="{{ $receiverType }}">
                        <input type="text"
                               id="message-input"
                               class="form-control"
                               placeholder="Type your message..."
                               required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
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
                window.Echo.channel(`chat.${conversationId}`)
                    .listen('MessageSent', (e) => {
                        console.log('New message received:', e);
                        const currentUserId = {{ Auth::guard('customer')->user()->id }};
                        if (e.message && e.message.sender_id !== currentUserId) {
                            addMessageToChat(e.message, false);
                            scrollToBottom();
                        }
                    })
                    .listen('OrderProposalSent', (e) => {
                        console.log('Order proposal received:', e);
                        showOrderProposal(e.order);
                    })
                    .listen('OrderStatusUpdated', (e) => {
                        console.log('Order status updated event received:', e);
                        updateOrderStatus(e.order);
                        showOrderStatusUpdate(e.order);
                    })
                    .error((error) => {
                        console.error('WebSocket error:', error);
                    });
            } else {
                console.error('Echo not available');
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
                        addMessageToChat(data.message, true);
                        messageInput.value = '';
                        scrollToBottom();
                    } else {
                        showErrorAlert('Failed to send message: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    showErrorAlert('Network error. Please check your connection and try again.');
                }
            });

            function updateOrderStatus(order) {
                const orderElement = document.querySelector(`[data-order-id="${order.id}"]`);
                if (!orderElement) return;

                const buttonsContainer = orderElement.querySelector('.d-flex.gap-2') ||
                    orderElement.querySelector('.mt-2:last-child');

                if (buttonsContainer) {
                    const statusDiv = document.createElement('div');
                    statusDiv.className = 'mt-2';
                    
                    // Calculate total price
                    const totalPrice = calculateOrderTotal(order);

                    if (order.status === 'accepted' && order.payment_status !== 'paid') {
                        statusDiv.innerHTML = `
                            <span class="badge bg-success">Accepted</span>
                            <button type="button" class="btn btn-primary btn-sm ms-2" onclick="showPaymentForOrder(${order.id}, ${JSON.stringify({
                            id: order.id,
                            service_name: order.service ? order.service.name : 'Service',
                            total_amount: totalPrice,
                            description: order.service_description || '',
                            order_number: order.order_number,
                            items: [{
                                name: order.service ? order.service.name : 'Service',
                                quantity: 1,
                                price: parseFloat(totalPrice).toLocaleString('id-ID')
                            }]
                        }).replace(/"/g, '&quot;')})">
                                <i class="bi bi-credit-card"></i> Pay Now
                            </button>
                        `;
                    } else if (order.payment_status === 'paid') {
                        statusDiv.innerHTML = `
                            <span class="badge bg-info">On Progress</span>
                            <span class="badge bg-success ms-1">
                                <i class="bi bi-check-circle"></i> Paid
                            </span>
                        `;
                    } else {
                        const badgeClass = order.status === 'rejected' ? 'bg-danger' : 'bg-warning';
                        statusDiv.innerHTML = `<span class="badge ${badgeClass}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>`;
                    }

                    buttonsContainer.replaceWith(statusDiv);
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
                if(document.querySelector(`[data-order-id="${order.id}"]`)){
                    return;
                }

                // Build additional items HTML
                let additionalItemsHtml = '';
                if (order.additional_items && order.additional_items.length > 0) {
                    additionalItemsHtml = '<div class="mt-2"><strong>Additional Items:</strong><ul class="mb-0 mt-1 small">';
                    order.additional_items.forEach(item => {
                        additionalItemsHtml += `<li>${item.item_name} (${item.quantity}x) - Rp ${(item.item_price * item.quantity).toLocaleString('id-ID')}</li>`;
                    });
                    additionalItemsHtml += '</ul></div>';
                }
                
                // Build custom items HTML
                let customItemsHtml = '';
                if (order.custom_items && order.custom_items.length > 0) {
                    customItemsHtml = '<div class="mt-2"><strong>Custom Items:</strong><ul class="mb-0 mt-1 small">';
                    order.custom_items.forEach(item => {
                        customItemsHtml += `<li>${item.item_name} (${item.quantity}x) - Rp ${(item.item_price * item.quantity).toLocaleString('id-ID')}`;
                        if (item.description) {
                            customItemsHtml += `<small class="text-muted d-block">${item.description}</small>`;
                        }
                        customItemsHtml += `</li>`;
                    });
                    customItemsHtml += '</ul></div>';
                }
                
                // Calculate total price
                let totalPrice = parseFloat(order.price) || 0;
                if (order.additional_items) {
                    order.additional_items.forEach(item => {
                        totalPrice += (parseFloat(item.item_price) || 0) * (parseInt(item.quantity) || 1);
                    });
                }
                if (order.custom_items) {
                    order.custom_items.forEach(item => {
                        totalPrice += (parseFloat(item.item_price) || 0) * (parseInt(item.quantity) || 1);
                    });
                }

                const orderDiv = document.createElement('div');
                orderDiv.className = 'order-proposal mb-3 text-start';
                orderDiv.setAttribute('data-order-id', order.id);

                orderDiv.innerHTML = `
                    <div class="d-inline-block bg-warning border p-3 rounded" style="max-width: 75%;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-briefcase me-2"></i>
                            <strong>Order Proposal</strong>
                        </div>
                        <div class="order-details">
                            <div><strong>Service:</strong> ${order.service ? order.service.name : 'Service'}</div>
                            <div><strong>Base Price:</strong> Rp ${parseInt(order.price).toLocaleString('id-ID')}</div>
                            ${order.work_datetime ? `<div><strong>Work Date:</strong> ${formatDateTime(order.work_datetime)}</div>` : ''}
                            ${order.working_address ? `<div><strong>Working Address:</strong> ${order.working_address}</div>` : ''}
                            ${order.service_description ? `<div><strong>Description:</strong> ${order.service_description}</div>` : ''}
                            ${additionalItemsHtml}
                            ${customItemsHtml}
                            ${(order.additional_items?.length > 0 || order.custom_items?.length > 0) ? `<div class="mt-2"><strong>Total Price:</strong> Rp ${totalPrice.toLocaleString('id-ID')}</div>` : ''}
                            <div class="mt-2">
                                <small>Order #${order.order_number}</small><br>
                                <small>Expires: ${formatDateTime(order.expires_at)}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="acceptOrder(${order.id})">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="rejectOrder(${order.id})">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    </div>
                `;

                messagesContainer.appendChild(orderDiv);
                scrollToBottom();
            }

            function showOrderStatusUpdate(order) {
                const statusDiv = document.createElement('div');
                statusDiv.className = 'order-status mb-3 text-center';

                let statusText = '';
                let statusClass = '';

                switch(order.status) {
                    case 'accepted':
                        if (order.payment_status === 'paid') {
                            statusText = 'Payment completed! Order is now in progress 🎉';
                            statusClass = 'bg-success text-white';
                        } else {
                            statusText = 'You accepted the order! Please complete payment 💳';
                            statusClass = 'bg-success text-white';
                        }
                        break;
                    case 'rejected':
                        statusText = 'You rejected the order';
                        statusClass = 'bg-danger text-white';
                        break;
                    default:
                        statusText = `Order ${order.status}`;
                        statusClass = 'bg-info text-white';
                }

                statusDiv.innerHTML = `
                    <div class="d-inline-block ${statusClass} p-2 rounded">
                        <small>${statusText} - Order #${order.order_number}</small>
                    </div>
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
                        
                        const orderData = {
                            id: orderId,
                            service_name: data.order.service ? data.order.service.name : 'Service',
                            total_amount: totalPrice,
                            description: data.order.service_description || '',
                            order_number: data.order.order_number,
                            items: [{
                                name: data.order.service ? data.order.service.name : 'Service',
                                quantity: 1,
                                price: parseFloat(totalPrice).toLocaleString('id-ID')
                            }]
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

                const messageDiv = document.createElement('div');
                messageDiv.className = `message mb-3 ${isSender ? 'text-end' : 'text-start'}`;
                messageDiv.innerHTML = `
                    <div class="d-inline-block ${isSender ? 'bg-primary text-white' : 'bg-light'} p-3 rounded" style="max-width: 75%;">
                        <div class="message-text">${escapeHtml(message.message)}</div>
                        <small class="d-block mt-1 ${isSender ? 'text-white-50' : 'text-muted'}">
                            ${formatTime(message.created_at)}
                        </small>
                    </div>
                `;
                messagesContainer.appendChild(messageDiv);
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
                container.scrollTop = container.scrollHeight;
            }

            scrollToBottom();

            if (!csrfToken) {
                console.error('CSRF token not found');
                showErrorAlert('Security token missing. Please refresh the page.');
            }
        });
    </script>

    <style>
        .d-flex.flex-column {
            display: flex !important;
            flex-direction: column !important;
        }

        .flex-1 {
            flex: 1 !important;
            min-height: 0;
        }

        .flex-shrink-0 {
            flex-shrink: 0 !important;
        }

        #messages-container {
            overflow-y: auto;
            max-height: 100%;
        }

        .order-proposal {
            animation: slideInLeft 0.3s ease-out;
        }

        .order-status {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @media (max-height: 600px) {
            .bg-white.shadow-lg.rounded-lg {
                height: 85vh !important;
            }
        }

        @media (max-height: 500px) {
            .bg-white.shadow-lg.rounded-lg {
                height: 90vh !important;
            }
        }
    </style>

    @vite(['resources/js/app.js'])
</x-app-layout>
