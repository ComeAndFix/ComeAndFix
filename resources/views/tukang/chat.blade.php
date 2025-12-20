<x-app-layout>
    @include('components.payment-popup')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with {{ $receiver->name }}
            </h2>
            <a href="{{ route('tukang.chatrooms.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Chats
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
                            <small class="opacity-75">{{ ucwords($receiverType) }}</small>
                        </div>
                    </div>
                </div>

                <!-- Messages Container - This will take the remaining space -->
                <div id="messages-container" class="flex-1 p-4 overflow-y-auto">
                    <div id="messages">
                        @foreach($messages as $message)
                            @if($message->message_type === 'order_proposal' && $message->order)
                                <div class="order-proposal mb-3 text-end" data-order-id="{{ $message->order->id }}">
                                    <div class="d-inline-block bg-success text-white p-3 rounded border" style="max-width: 75%;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-briefcase me-2"></i>
                                            <strong>Order Proposal Sent</strong>
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
                                                                    <small class="text-white-50 d-block">{{ $item->description }}</small>
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
                                                <small>Status: <span class="badge bg-{{ $message->order->status === 'accepted' ? 'success' : ($message->order->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucwords(str_replace('_', ' ', $message->order->status)) }}</span></small><br>
                                                <small>Expires: {{ $message->order->expires_at->format('d M Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="message mb-3 {{ $message->sender_type === 'App\Models\Tukang' ? 'text-end' : 'text-start' }}">
                                    <div class="d-inline-block {{ $message->sender_type === 'App\Models\Tukang' ? 'bg-primary text-white' : 'bg-light' }} p-3 rounded" style="max-width: 75%;">
                                        <div class="message-text">{{ $message->message }}</div>
                                        <small class="d-block mt-1 {{ $message->sender_type === 'App\Models\Tukang' ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Input Form - Fixed at bottom -->
                <div class="border-top p-3 flex-shrink-0">
                    <!-- Order Proposal Button -->
                    @if($receiverType === 'customer')
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#orderProposalModal">
                                <i class="bi bi-briefcase"></i> Send Order Proposal
                            </button>
                        </div>
                    @endif

                    <!-- Message Form -->
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

    <!-- Order Proposal Modal -->
    @if($receiverType === 'customer')
        <div class="modal fade" id="orderProposalModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Order Proposal to {{ $receiver->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="order-proposal-form">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $receiver->id }}">
                            <input type="hidden" name="conversation_id" value="{{ $conversationId }}">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="service-select" class="form-label">Service *</label>
                                    <!-- This will be shown when service is NOT pre-selected -->
                                    <select id="service-select" name="service_id" class="form-select" required>
                                        <option value="">Loading services...</option>
                                    </select>
                                    <!-- This will be shown when service IS pre-selected -->
                                    <input type="text" id="service-text" class="form-control" readonly style="display: none;">
                                    <input type="hidden" id="service-id-hidden" name="service_id_hidden">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="service-price" class="form-label">Price (Rp) *</label>
                                    <input type="number" id="service-price" name="price" class="form-control" min="0" step="1000" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="service-description" class="form-label">Service Description</label>
                                <textarea id="service-description" name="service_description" class="form-control" rows="3" placeholder="Describe the work to be done in detail..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="customer-address" class="form-label">Working Address</label>
                                <textarea id="customer-address" name="working_address" class="form-control" rows="2" placeholder="Enter the address where the work will be performed">{{ $receiver->address ?? '' }}</textarea>
                                <small class="text-muted">Default is customer's registered address. You can edit this if work will be at a different location.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="work-datetime" class="form-label">Work Date & Time</label>
                                    <input type="datetime-local" id="work-datetime" name="work_datetime" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expires-hours" class="form-label">Proposal Valid For *</label>
                                    <select id="expires-hours" name="expires_in_hours" class="form-select" required>
                                        <option value="24">24 hours</option>
                                        <option value="48" selected>48 hours (2 days)</option>
                                        <option value="72">72 hours (3 days)</option>
                                        <option value="168">1 week</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Items Section -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Additional Items (Optional)</label>
                                <div class="border rounded p-3 bg-light">
                                    <div id="additional-items-list" class="row">
                                        @foreach(config('order_items.predefined_items') as $index => $item)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input additional-item-checkbox" 
                                                       type="checkbox" 
                                                       id="item-{{ $index }}"
                                                       data-item-name="{{ $item['name'] }}"
                                                       data-item-price="{{ $item['default_price'] }}"
                                                       data-item-unit="{{ $item['unit'] }}">
                                                <label class="form-check-label" for="item-{{ $index }}">
                                                    {{ $item['name'] }} - Rp {{ number_format($item['default_price'], 0, ',', '.') }}
                                                </label>
                                                <input type="number" 
                                                       class="form-control form-control-sm mt-1 item-quantity" 
                                                       id="quantity-{{ $index }}"
                                                       min="1" 
                                                       value="1" 
                                                       disabled
                                                       style="width: 80px;">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Items Section (Others) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Custom Items</label>
                                <div class="border rounded p-3 bg-light">
                                    <div id="custom-items-container"></div>
                                    <button type="button" id="add-custom-item-btn" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle"></i> Add Custom Item
                                    </button>
                                </div>
                            </div>

                            <!-- Price Summary -->
                            <div class="mb-3">
                                <div class="card bg-info bg-opacity-10">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2">Price Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Base Service Price:</span>
                                            <span id="base-price-display">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Additional Items:</span>
                                            <span id="additional-items-price">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Custom Items:</span>
                                            <span id="custom-items-price">Rp 0</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Price:</span>
                                            <span id="total-price-display">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="service-details" class="mb-3">
                                <!-- Dynamic service details will be loaded here -->
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="order-proposal-form" class="btn btn-success">
                            <i class="bi bi-send-check"></i> Send Proposal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const conversationId = '{{ $conversationId }}';
            const messagesContainer = document.getElementById('messages');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Pre-selected service from map/customer request
            const preselectedServiceId = {{ isset($selectedService) && $selectedService ? $selectedService->id : 'null' }};
            console.log('Preselected Service ID:', preselectedServiceId);

            // Load tukang services when modal opens
            @if($receiverType === 'customer')
            const orderProposalModal = document.getElementById('orderProposalModal');
            if (orderProposalModal && !orderProposalModal.hasAttribute('data-listener-attached')) {
                orderProposalModal.addEventListener('show.bs.modal', loadTukangServices);
                orderProposalModal.setAttribute('data-listener-attached', 'true');
            }

            async function loadTukangServices() {
                try {
                    const response = await fetch('{{ route("tukang.services") }}', {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                if (data.success) {
                    const serviceSelect = document.getElementById('service-select');
                    serviceSelect.innerHTML = '<option value="">Select a service...</option>';

                    data.services.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = service.name;
                        option.dataset.price = service.custom_rate || service.base_price || 0;
                        option.dataset.color = service.color;
                        option.dataset.icon = service.icon;
                        option.dataset.description = service.description || '';
                        serviceSelect.appendChild(option);
                    });
                    
                    // Auto-select and show as text input if service is pre-selected
                    if (preselectedServiceId) {
                        console.log('Auto-selecting service:', preselectedServiceId);
                        serviceSelect.value = preselectedServiceId;
                        
                        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                        const serviceName = selectedOption.textContent;
                        const price = selectedOption.dataset.price || 0;
                        
                        // Hide dropdown, show text input
                        serviceSelect.style.display = 'none';
                        serviceSelect.removeAttribute('required');
                        
                        const serviceText = document.getElementById('service-text');
                        serviceText.value = serviceName;
                        serviceText.style.display = 'block';
                        
                        // Store service_id in hidden field
                        document.getElementById('service-id-hidden').value = preselectedServiceId;
                        
                        // Auto-fill price
                        document.getElementById('service-price').value = price;
                        updatePriceSummary();
                        
                        console.log('Service displayed as text:', serviceName);
                    } else {
                        console.log('No preselected service - service select remains enabled');
                    }
                } else {
                    document.getElementById('service-select').innerHTML = '<option value="">No services available</option>';
                }
            } catch (error) {
                console.error('Error loading services:', error);
                document.getElementById('service-select').innerHTML = '<option value="">Error loading services</option>';
            }
        }

            // Auto-fill price when service is selected
            document.getElementById('service-select').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                document.getElementById('service-price').value = price;
                updatePriceSummary();
            });

            // Update price when base price changes
            document.getElementById('service-price').addEventListener('input', updatePriceSummary);

            // Handle additional items checkboxes
            document.querySelectorAll('.additional-item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const index = this.id.replace('item-', '');
                    const quantityInput = document.getElementById(`quantity-${index}`);
                    quantityInput.disabled = !this.checked;
                    if (!this.checked) {
                        quantityInput.value = 1;
                    }
                    updatePriceSummary();
                });
            });

            // Update price when quantity changes
            document.querySelectorAll('.item-quantity').forEach(input => {
                input.addEventListener('input', updatePriceSummary);
            });

            // Custom item counter
            let customItemCounter = 0;

            // Add custom item button
            document.getElementById('add-custom-item-btn').addEventListener('click', function() {
                const container = document.getElementById('custom-items-container');
                const customItemId = `custom-item-${customItemCounter++}`;
                
                const customItemDiv = document.createElement('div');
                customItemDiv.className = 'custom-item-row mb-3 p-3 border rounded bg-white';
                customItemDiv.id = customItemId;
                customItemDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Item Name *</label>
                            <input type="text" class="form-control custom-item-name" placeholder="e.g., Special Tool" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Price (Rp) *</label>
                            <input type="number" class="form-control custom-item-price" min="0" step="1000" placeholder="0" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Qty *</label>
                            <input type="number" class="form-control custom-item-quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-custom-item" data-item-id="${customItemId}">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control custom-item-description" rows="2" placeholder="Optional details about this item"></textarea>
                        </div>
                    </div>
                `;
                
                container.appendChild(customItemDiv);

                // Add event listeners for price calculation
                customItemDiv.querySelectorAll('.custom-item-price, .custom-item-quantity').forEach(input => {
                    input.addEventListener('input', updatePriceSummary);
                });

                // Add remove button listener
                customItemDiv.querySelector('.remove-custom-item').addEventListener('click', function() {
                    customItemDiv.remove();
                    updatePriceSummary();
                });

                updatePriceSummary();
            });

            // Function to update price summary
            function updatePriceSummary() {
                const basePrice = parseFloat(document.getElementById('service-price').value) || 0;
                
                // Calculate additional items total
                let additionalItemsTotal = 0;
                document.querySelectorAll('.additional-item-checkbox:checked').forEach(checkbox => {
                    const index = checkbox.id.replace('item-', '');
                    const price = parseFloat(checkbox.dataset.itemPrice) || 0;
                    const quantity = parseInt(document.getElementById(`quantity-${index}`).value) || 1;
                    additionalItemsTotal += price * quantity;
                });

                // Calculate custom items total
                let customItemsTotal = 0;
                document.querySelectorAll('.custom-item-row').forEach(row => {
                    const price = parseFloat(row.querySelector('.custom-item-price').value) || 0;
                    const quantity = parseInt(row.querySelector('.custom-item-quantity').value) || 1;
                    customItemsTotal += price * quantity;
                });

                const totalPrice = basePrice + additionalItemsTotal + customItemsTotal;

                // Update display
                document.getElementById('base-price-display').textContent = `Rp ${basePrice.toLocaleString('id-ID')}`;
                document.getElementById('additional-items-price').textContent = `Rp ${additionalItemsTotal.toLocaleString('id-ID')}`;
                document.getElementById('custom-items-price').textContent = `Rp ${customItemsTotal.toLocaleString('id-ID')}`;
                document.getElementById('total-price-display').textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
            }

            // Order proposal form handler
            const orderProposalForm = document.getElementById('order-proposal-form');
            if (orderProposalForm) {
                orderProposalForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const submitButton = document.querySelector('button[form="order-proposal-form"]') ||
                    document.querySelector('#orderProposalModal button[type="submit"]') ||
                    this.querySelector('button[type="submit"]');

                    if(!submitButton){
                        console.error("submit button not found");
                        return;
                    }
                    const originalText = submitButton.innerHTML;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Sending...';

                    const serviceDetails = {};
                    const detailInputs = document.querySelectorAll('#service-details input, #service-details select');
                    detailInputs.forEach(input => {
                        if (input.value) {
                            serviceDetails[input.name] = input.value;
                        }
                    });

                    // Collect additional items
                    const additionalItems = [];
                    document.querySelectorAll('.additional-item-checkbox:checked').forEach(checkbox => {
                        const index = checkbox.id.replace('item-', '');
                        const quantityInput = document.getElementById(`quantity-${index}`);
                        additionalItems.push({
                            item_name: checkbox.dataset.itemName,
                            item_price: parseFloat(checkbox.dataset.itemPrice),
                            quantity: parseInt(quantityInput.value)
                        });
                    });

                    // Collect custom items
                    const customItems = [];
                    document.querySelectorAll('.custom-item-row').forEach(row => {
                        const name = row.querySelector('.custom-item-name').value;
                        const price = row.querySelector('.custom-item-price').value;
                        const quantity = row.querySelector('.custom-item-quantity').value;
                        const description = row.querySelector('.custom-item-description').value;
                        
                        if (name && price) {
                            customItems.push({
                                item_name: name,
                                item_price: parseFloat(price),
                                quantity: parseInt(quantity),
                                description: description || null
                            });
                        }
                    });

                    const formData = {
                        customer_id: document.querySelector('[name="customer_id"]').value,
                        conversation_id: document.querySelector('[name="conversation_id"]').value,
                        service_id: document.getElementById('service-id-hidden').value || document.getElementById('service-select').value,
                        service_description: document.getElementById('service-description').value,
                        price: document.getElementById('service-price').value,
                        expires_in_hours: document.getElementById('expires-hours').value,
                        work_datetime: document.getElementById('work-datetime').value || null,
                        working_address: document.getElementById('customer-address').value || null,
                        service_details: serviceDetails,
                        additional_items: additionalItems.length > 0 ? additionalItems : null,
                        custom_items: customItems.length > 0 ? customItems : null
                    };

                    try {
                        const response = await fetch('{{ route("tukang.order.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(orderProposalModal);
                            modal.hide();

                            // Reset form
                            orderProposalForm.reset();
                            
                            // Clear custom items
                            document.getElementById('custom-items-container').innerHTML = '';
                            
                            // Uncheck and disable additional item quantities
                            document.querySelectorAll('.additional-item-checkbox').forEach(checkbox => {
                                checkbox.checked = false;
                            });
                            document.querySelectorAll('.item-quantity').forEach(input => {
                                input.disabled = true;
                                input.value = 1;
                            });
                            
                            updatePriceSummary();

                            // Show success message in chat
                            showOrderProposalSent(data.order);

                            // Show success toast/alert
                            showSuccessAlert('Order proposal sent successfully!');
                        } else {
                            showErrorAlert(data.error || 'Failed to send order proposal');
                        }
                    } catch (error) {
                        console.error('Error sending order proposal:', error);
                        showErrorAlert('Failed to send order proposal. Please try again.');
                    } finally {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                });
            }
            @endif

            // WebSocket listeners
            if (window.Echo) {
                console.log('Echo listener started for channel:', `chat.${conversationId}`);
                window.Echo.channel(`chat.${conversationId}`)
                    .listen('MessageSent', (e) => {
                        console.log('New message received from WebSocket:', e);
                        const currentUserId = {{ Auth::guard('tukang')->user()->id }};
                        const currentUserType = 'App\\Models\\Tukang';
                        
                        // Only add if it's not from the current user (to avoid duplicates)
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
                        showOrderProposalSent(e.order);
                        scrollToBottom();
                    })
                    .listen('OrderStatusUpdated', (e) => {
                        console.log('Order status updated event received from WebSocket:', e);
                        showOrderStatusUpdate(e.order);
                        scrollToBottom();
                    });
            } else {
                console.error('Echo is not available on window object');
            }

            // Message form handler
            messageForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const message = messageInput.value.trim();
                if (!message) return;

                const receiverId = document.getElementById('receiver-id').value;
                const receiverType = document.getElementById('receiver-type').value;

                try {
                    const response = await fetch('{{ route("tukang.chat.send") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: message,
                            receiver_id: parseInt(receiverId),
                            receiver_type: receiverType
                        })
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
                    showErrorAlert('Network error. Please try again.');
                }
            });

            function showOrderProposalSent(order) {
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
                        customItemsHtml += `<li>${item.item_name} (${item.quantity}x) - Rp ${(item.item_price * item.quantity).toLocaleString('id-ID')}</li>`;
                        if (item.description) {
                            customItemsHtml += `<small class="text-white-50 d-block">${item.description}</small>`;
                        }
                    });
                    customItemsHtml += '</ul></div>';
                }
                
                const orderDiv = document.createElement('div');
                orderDiv.className = 'order-proposal mb-3 text-end';
                orderDiv.setAttribute('data-order-id', order.id);
                orderDiv.innerHTML = `
                <div class="d-inline-block bg-success text-white p-3 rounded border" style="max-width: 75%;">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-briefcase me-2"></i>
                        <strong>Order Proposal Sent</strong>
                    </div>
                    <div class="order-details">
                        <div><strong>Service:</strong> ${order.service ? order.service.name : 'Service'}</div>
                        <div><strong>Base Price:</strong> Rp ${parseInt(order.price).toLocaleString('id-ID')}</div>
                        ${order.work_datetime ? `<div><strong>Work Date:</strong> ${formatDateTime(order.work_datetime)}</div>` : ''}
                        ${order.working_address ? `<div><strong>Working Address:</strong> ${order.working_address}</div>` : ''}
                        ${order.service_description ? `<div><strong>Description:</strong> ${order.service_description}</div>` : ''}
                        ${additionalItemsHtml}
                        ${customItemsHtml}
                        ${(order.additional_items?.length > 0 || order.custom_items?.length > 0) ? `<div class="mt-2"><strong>Total Price:</strong> Rp ${calculateTotalPrice(order).toLocaleString('id-ID')}</div>` : ''}
                        <div class="mt-2">
                            <small>Order #${order.order_number}</small><br>
                            <small>Expires: ${formatDateTime(order.expires_at)}</small>
                        </div>
                    </div>
                </div>
            `;
                messagesContainer.appendChild(orderDiv);
                scrollToBottom();
            }
            
            function calculateTotalPrice(order) {
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

            function showOrderStatusUpdate(order) {
                const statusDiv = document.createElement('div');
                statusDiv.className = 'order-status mb-3 text-center';

                let statusText = '';
                let statusClass = '';

                switch(order.status) {
                    case 'accepted':
                        statusText = 'Order Accepted! 🎉';
                        statusClass = 'bg-success text-white';
                        break;
                    case 'rejected':
                        statusText = 'Order Rejected';
                        statusClass = 'bg-danger text-white';
                        break;
                    default:
                        // Replace underscore with space and capitalize
                        const formattedStatus = order.status.replace(/_/g, ' ')
                            .split(' ')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');
                        statusText = `Order ${formattedStatus}`;
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
                // You can replace this with a toast notification library
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
                // You can replace this with a toast notification library
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
        });
    </script>

    <style>
        .d-flex.flex-column {
            display: flex !important;
            flex-direction: column !important;
        }

        .flex-1 {
            flex: 1 !important;
            min-height: 0; /* Important for proper scrolling */
        }

        .flex-shrink-0 {
            flex-shrink: 0 !important;
        }

        /* Ensure the container takes full height */
        #messages-container {
            overflow-y: auto;
            max-height: 100%;
        }

        /* Order proposal styling */
        .order-proposal {
            animation: slideInRight 0.3s ease-out;
        }

        .order-status {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
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

        /* Responsive adjustments */
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

        /* Improve modal responsiveness */
        @media (max-width: 768px) {
            .modal-lg {
                max-width: 95%;
            }
        }
    </style>

</x-app-layout>
