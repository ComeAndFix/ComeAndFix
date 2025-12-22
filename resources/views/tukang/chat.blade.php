<x-app-layout>
    @include('components.payment-popup')

    @push('styles')
        @vite(['resources/css/components/chat.css'])
    @endpush

    <div class="chat-page-wrapper">
        <div class="chat-container">
            <!-- Header -->
            <div class="chat-header">
                <button onclick="history.back()" class="btn btn-outline-secondary btn-sm rounded-pill me-2">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
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
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="messages-container">
                <div id="messages">
                    @php $currentGroupDate = null; @endphp
                    @foreach($messages as $index => $message)
                        @php
                            $msgDate = $message->created_at->format('Y-m-d');
                            $isToday = $msgDate === now()->format('Y-m-d');
                            $isYesterday = $msgDate === now()->subDay()->format('Y-m-d');
                            $displayDate = $isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $message->created_at->format('d M Y'));
                        @endphp

                        @if($currentGroupDate !== $msgDate)
                            @if($currentGroupDate !== null)
                                </div> <!-- Close previous date-messages -->
                                </div> <!-- Close previous date-section -->
                            @endif

                            <div class="date-section">
                                <div class="chat-date-divider">
                                    <span>{{ $displayDate }}</span>
                                </div>
                                <div class="date-messages d-flex flex-column gap-3">
                            @php $currentGroupDate = $msgDate; @endphp
                        @endif

                        @if($message->message_type === 'order_proposal' && $message->order)
                            <div class="order-proposal-card sent" data-order-id="{{ $message->order->id }}" style="cursor: pointer;" onclick="window.location.href='{{ route('tukang.jobs.show', $message->order->uuid) }}?from_chat=true'">
                                <div class="proposal-badge">
                                    <i class="bi bi-briefcase-fill"></i> Order Proposal Sent
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
                                </div>

                                <div class="proposal-price-tag" style="display: block;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="price-label">{{ ($message->order->additionalItems && $message->order->additionalItems->count() > 0) || ($message->order->customItems && $message->order->customItems->count() > 0) ? 'Total Estimate' : 'Base Price' }}</span>
                                        <span class="price-amount">Rp {{ number_format($message->order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="text-end mt-2">
                                        <a href="javascript:void(0)" onclick="event.stopPropagation(); toggleDetails('{{ $message->order->uuid }}')" id="toggle-btn-{{ $message->order->uuid }}" class="text-muted small text-decoration-none" style="font-size: 0.8rem;">
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

                                <div class="w-100 text-center mt-3">
                                    <span class="status-badge status-badge-{{ $message->order->status === 'accepted' || $message->order->status === 'completed' ? 'success' : ($message->order->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucwords(str_replace('_', ' ', $message->order->status)) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="message-wrapper {{ $message->sender_type === 'App\Models\Tukang' ? 'sent' : 'received' }}">
                                <div class="message-bubble">
                                    {{ $message->message }}
                                </div>
                                <div class="message-time">
                                    {{ $message->created_at->format('H:i') }}
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($currentGroupDate !== null)
                        </div> <!-- Close last date-messages -->
                        </div> <!-- Close last date-section -->
                    @endif
                </div>
            </div>

            <!-- Input Form -->
            <div class="chat-input-area">
                <div class="d-flex flex-column gap-3">
                    @if($receiverType === 'customer')
                        <button type="button" class="btn btn-outline-brand-orange rounded-pill w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#orderProposalModal">
                            <i class="bi bi-plus-lg me-2"></i> Create Order Proposal
                        </button>
                    @endif
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
    </div>

    <!-- Order Proposal Modal -->
    @if($receiverType === 'customer')
        <div class="modal fade" id="orderProposalModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title font-jost fw-bold fs-4">Create Order Proposal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="order-proposal-form">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $receiver->id }}">
                            <input type="hidden" name="conversation_id" value="{{ $conversationId }}">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="service-select" class="form-label">Service Type *</label>
                                    <select id="service-select" name="service_id" class="form-select" required>
                                        <option value="">Loading services...</option>
                                    </select>
                                    <input type="text" id="service-text" class="form-control bg-light text-muted fw-semibold border-dashed" readonly style="display: none; cursor: not-allowed;">
                                    <input type="hidden" id="service-id-hidden" name="service_id_hidden">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="service-price" class="form-label">Base Rate (Rp) *</label>
                                    <input type="text" id="service-price" name="price" class="form-control bg-light text-muted fw-semibold border-dashed" placeholder="0" readonly style="cursor: not-allowed;" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="service-description" class="form-label">Detailed Work Description</label>
                                <textarea id="service-description" name="service_description" class="form-control" rows="3" placeholder="Explain clearly what work will be performed..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="customer-address" class="form-label">Service Location</label>
                                <textarea id="customer-address" name="working_address" class="form-control" rows="2" placeholder="Where should the service be performed?">{{ $receiver->address ?? '' }}</textarea>
                                <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Pre-filled with customer's address.</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="work-datetime" class="form-label">Scheduled Date & Time</label>
                                    <input type="datetime-local" id="work-datetime" name="work_datetime" class="form-control">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="expires-hours" class="form-label">Proposal Expiration *</label>
                                    <select id="expires-hours" name="expires_in_hours" class="form-select" required>
                                        <option value="24">24 Hours</option>
                                        <option value="48" selected>48 Hours (2 Days)</option>
                                        <option value="72">72 Hours (3 Days)</option>
                                        <option value="168">1 Week</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Items -->
                            <div class="mb-4">
                                <label class="form-label">Predefined Labor/Supply Items</label>
                                <div class="item-list-container">
                                    <div id="additional-items-list" class="row">
                                        @foreach(config('order_items.predefined_items') as $index => $item)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check d-flex flex-column align-items-start">
                                                <div class="d-flex align-items-center mb-1">
                                                    <input class="form-check-input additional-item-checkbox me-2" 
                                                           type="checkbox" 
                                                           id="item-{{ $index }}"
                                                           data-item-name="{{ $item['name'] }}"
                                                           data-item-price="{{ $item['default_price'] }}"
                                                           data-item-unit="{{ $item['unit'] }}">
                                                    <label class="form-check-label small fw-bold" for="item-{{ $index }}">
                                                        {{ $item['name'] }}
                                                    </label>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="number" 
                                                           class="form-control form-control-sm item-quantity" 
                                                           id="quantity-{{ $index }}"
                                                           min="1" 
                                                           value="1" 
                                                           disabled
                                                           style="width: 70px;">
                                                    <span class="text-muted small">@ Rp {{ number_format($item['default_price'], 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Items -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label m-0">Custom Line Items</label>
                                    <button type="button" id="add-custom-item-btn" class="btn btn-sm btn-outline-brand-orange rounded-pill px-3">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom
                                    </button>
                                </div>
                                <div id="custom-items-container"></div>
                            </div>

                            <!-- Pricing Breakdown -->
                            <div class="mb-0">
                                <div class="proposal-summary-card">
                                    <div class="summary-title">
                                        <i class="bi bi-calculator-fill"></i> Estimated Cost Summary
                                    </div>
                                    <div class="summary-row">
                                        <span>Base Service Rate</span>
                                        <span id="base-price-display">Rp 0</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Service Items Total</span>
                                        <span id="additional-items-price">Rp 0</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Custom Items Total</span>
                                        <span id="custom-items-price">Rp 0</span>
                                    </div>
                                    <div class="summary-total">
                                        <span>Total Estimate</span>
                                        <span id="total-price-display">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" form="order-proposal-form" class="btn btn-brand-orange rounded-pill px-5 fw-bold">
                            Send Proposal
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
                        const basePriceInput = document.getElementById('service-price');
                        basePriceInput.value = formatIDR(price);
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
                const basePriceInput = document.getElementById('service-price');
                basePriceInput.value = formatIDR(price);
                updatePriceSummary();
            });

            // Currency formatting helpers
            function formatIDR(amount) {
                return new Intl.NumberFormat('id-ID').format(amount);
            }

            function parseCurrency(value) {
                return parseFloat(value.replace(/[^0-9]/g, '')) || 0;
            }

            // Handle currency input formatting
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('currency-input')) {
                    const value = parseCurrency(e.target.value);
                    e.target.value = formatIDR(value);
                    updatePriceSummary();
                }
            });


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
                customItemDiv.className = 'custom-item-row mb-4 p-4 border-0 bg-white shadow-sm rounded-4';
                customItemDiv.id = customItemId;
                customItemDiv.innerHTML = `
                    <div class="row align-items-end mb-3">
                        <div class="col-md-5">
                            <label class="form-label small">Item Name *</label>
                            <input type="text" class="form-control custom-item-name" placeholder="Labor, Supply, etc." required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Price (Rp) *</label>
                            <input type="text" class="form-control custom-item-price currency-input" placeholder="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Qty *</label>
                            <input type="number" class="form-control custom-item-quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger rounded-pill w-100 remove-custom-item" data-item-id="${customItemId}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label small">Short Description (Optional)</label>
                            <textarea class="form-control custom-item-description" rows="2" placeholder="Briefly explain what this item is for..."></textarea>
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
                const basePrice = parseCurrency(document.getElementById('service-price').value);
                
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
                    const price = parseCurrency(row.querySelector('.custom-item-price').value);
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
                                item_price: parseCurrency(price),
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
                        price: parseCurrency(document.getElementById('service-price').value),
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

                        // Redirect to job detail if payment is completed
                        if (e.order.payment_status === 'paid') {
                            showSuccessAlert('Payment verified! Redirecting to job details...');
                            setTimeout(() => {
                                window.location.href = `/jobs/${e.order.uuid}`;
                            }, 2000);
                        }
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
                
                const totalPrice = calculateTotalPrice(order);
                
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

                const hasAdditionalOrCustomItems = (order.additional_items && order.additional_items.length > 0) || 
                                                   (order.custom_items && order.custom_items.length > 0);
                
                const orderDiv = document.createElement('div');
                orderDiv.className = 'order-proposal-card sent';
                orderDiv.setAttribute('data-order-id', order.id);
                orderDiv.style.cursor = 'pointer';
                orderDiv.onclick = function() { window.location.href = '/jobs/' + (order.uuid || order.id) + '?from_chat=true'; };
                orderDiv.innerHTML = `
                    <div class="proposal-badge">
                        <i class="bi bi-briefcase-fill"></i> Order Proposal Sent
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
                    </div>

                    <div class="proposal-price-tag" style="display: block;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-label">${hasAdditionalOrCustomItems ? 'Total Estimate' : 'Base Price'}</span>
                            <span class="price-amount">Rp ${parseInt(totalPrice).toLocaleString('id-ID')}</span>
                        </div>
                        
                        <div class="text-end mt-2">
                            <a href="javascript:void(0)" onclick="event.stopPropagation(); toggleDetails('${order.uuid || order.id}')" id="toggle-btn-${order.uuid || order.id}" class="text-muted small text-decoration-none" style="font-size: 0.8rem;">
                                Click to see details <i class="bi bi-chevron-down"></i>
                            </a>
                        </div>

                        <div id="details-${order.uuid || order.id}" class="mt-3 pt-3 border-top" style="display: none; border-color: #eee !important;">
                            ${detailsHtml}
                        </div>
                    </div>

                    <div class="w-100 text-center mt-3">
                        <span class="status-badge status-badge-warning">Pending</span>
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
                let statusClass = 'info';

                switch(order.status) {
                    case 'accepted':
                        statusText = 'Order Accepted by Customer! 🎉';
                        statusClass = 'success';
                        break;
                    case 'on_progress':
                        statusText = 'Order Paid! You can start working now 🛠️';
                        statusClass = 'success';
                        break;
                    case 'rejected':
                        statusText = 'Order Rejected by Customer';
                        statusClass = 'danger';
                        break;
                    case 'completed':
                        statusText = 'Work marked as completed! ✨';
                        statusClass = 'success';
                        break;
                    default:
                        // Replace underscore with space and capitalize
                        const formattedStatus = order.status.replace(/_/g, ' ')
                            .split(' ')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');
                        statusText = `Order ${formattedStatus}`;
                }

                let actionBtn = '';
                if (order.status === 'on_progress') {
                    actionBtn = `
                        <div class="mt-2">
                            <a href="/jobs/${order.uuid}" class="btn btn-brand-orange btn-sm rounded-pill px-3">
                                <i class="bi bi-eye-fill me-1"></i> View Job Details
                            </a>
                        </div>
                    `;
                }

                statusDiv.innerHTML = `
                    <span class="status-badge status-badge-${statusClass}">
                        ${statusText} • #${order.order_number}
                    </span>
                    ${actionBtn}
                `;
                messagesContainer.appendChild(statusDiv);
                scrollToBottom();
            }

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
