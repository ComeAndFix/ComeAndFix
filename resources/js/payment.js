class PaymentHandler {
    constructor() {
        this.currentOrder = null;
        this.initialized = false;
        // Bind methods to maintain context
        this.handlePaymentSuccess = this.handlePaymentSuccess.bind(this);
        this.handlePaymentPending = this.handlePaymentPending.bind(this);
        this.handlePaymentError = this.handlePaymentError.bind(this);
        this.handlePaymentClose = this.handlePaymentClose.bind(this);
        this.preventModalClose = this.preventModalClose.bind(this);
    }

    init() {
        if (this.initialized) return;

        console.log('PaymentHandler initializing...');
        this.setupEventListeners();
        this.initialized = true;
        console.log('PaymentHandler initialized successfully');
    }

    setupEventListeners() {
        const paymentModal = document.getElementById('paymentModal');
        if (paymentModal) {
            paymentModal.addEventListener('shown.bs.modal', () => {
                this.setupModalEventListeners();
            });
        }
    }

    setupModalEventListeners() {
        const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
        const payButton = document.getElementById('payButton');
        const cancelButton = document.querySelector('#paymentModal [data-bs-dismiss="modal"]');

        paymentMethods.forEach(radio => {
            radio.addEventListener('change', () => {
                // Remove active class from all cards
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    card.classList.remove('active');
                });

                // Add active class to selected card
                radio.closest('.payment-method-card').classList.add('active');

                if (payButton) {
                    payButton.disabled = false;
                }
            });
        });

        if (payButton && !payButton.hasAttribute('data-listener-attached')) {
            payButton.addEventListener('click', () => {
                this.processPayment();
            });
            payButton.setAttribute('data-listener-attached', 'true');
        }

        // Add cancel button listener
        if (cancelButton && !cancelButton.hasAttribute('data-cancel-listener')) {
            cancelButton.addEventListener('click', () => {
                this.cancelPayment();
            });
            cancelButton.setAttribute('data-cancel-listener', 'true');
        }
    }

    showPaymentModal(orderData) {
        console.log('Showing payment modal for order:', orderData);

        if (!this.initialized) {
            this.init();
        }

        this.currentOrder = orderData;

        const modalElement = document.getElementById('paymentModal');
        if (!modalElement) {
            console.error('Payment modal not found!');
            return;
        }

        // Populate order details
        const orderDetailsElement = document.getElementById('orderDetails');
        const totalAmountElement = document.getElementById('totalAmount');

        if (orderDetailsElement) {
            orderDetailsElement.innerHTML = this.buildOrderHTML(orderData);
        }

        if (totalAmountElement) {
            const amount = orderData.total_amount || orderData.price || 0;
            totalAmountElement.textContent = parseFloat(amount).toLocaleString('id-ID');
        }

        // Reset payment method selection and disable pay button
        const payButton = document.getElementById('payButton');
        if (payButton) {
            payButton.disabled = true;
        }

        const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
        paymentMethods.forEach(radio => {
            radio.checked = false;
        });

        // Prevent closing with escape key or clicking outside
        const modalOptions = {
            backdrop: 'static',
            keyboard: false
        };

        try {
            const modal = new bootstrap.Modal(modalElement, modalOptions);

            // Remove any existing event listeners
            modalElement.removeEventListener('hide.bs.modal', this.preventModalClose);

            // Add event listener to prevent modal from closing
            modalElement.addEventListener('hide.bs.modal', this.preventModalClose);

            modal.show();
        } catch (error) {
            console.error('Error showing modal:', error);
        }
    }

    cancelPayment() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
        if (modal) {
            // Remove the preventModalClose listener
            const modalElement = document.getElementById('paymentModal');
            modalElement.removeEventListener('hide.bs.modal', this.preventModalClose);

            modal.hide();

            // Show cancel message
            this.showMessage('Payment cancelled. You can pay later by clicking the Pay Now button.', 'info');
        }
    }

    preventModalClose(event) {
        event.preventDefault();
        event.stopPropagation();
    }

    buildOrderHTML(orderData) {
        let html = '<div class="order-summary-list">';
        if (orderData.items && orderData.items.length > 0) {
            orderData.items.forEach(item => {
                const isBase = item.is_base === true;
                html += `
                    <div class="summary-item d-flex justify-content-between align-items-center mb-2 ${isBase ? 'base-service-item pt-1' : 'opacity-75'}">
                        <span class="${isBase ? 'fw-bold text-dark fs-6' : 'text-muted small'}">${item.name} ${isBase ? '' : `<span class="smaller">(x${item.quantity || 1})</span>`}</span>
                        <span class="${isBase ? 'fw-bold text-dark fs-6' : 'fw-semibold text-muted'}">Rp ${parseFloat(item.price || 0).toLocaleString('id-ID')}</span>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span class="text-muted">${orderData.service_name || 'Service'}</span>
                    <span class="fw-bold text-dark">Rp ${parseFloat(orderData.total_amount).toLocaleString('id-ID')}</span>
                </div>
            `;
        }

        if (orderData.description) {
            html += `<div class="mt-2 pt-2 border-top border-dashed"><small class="text-muted italic">"${orderData.description}"</small></div>`;
        }

        html += '</div>';
        return html;
    }

    async processPayment() {
        const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;

        if (!selectedPaymentMethod) {
            this.showErrorMessage('Please select a payment method');
            return;
        }

        if (!this.currentOrder || !this.currentOrder.id) {
            this.showErrorMessage('Invalid order data');
            return;
        }

        const payButton = document.getElementById('payButton');
        const originalText = payButton.innerHTML;
        payButton.disabled = true;
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }

            const amount = parseFloat(this.currentOrder.total_amount || this.currentOrder.price);
            if (isNaN(amount) || amount <= 0) {
                throw new Error('Invalid payment amount');
            }

            const response = await fetch('/payments/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_id: this.currentOrder.id,
                    payment_method: selectedPaymentMethod,
                    amount: amount
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Payment processing failed');
            }

            const result = await response.json();

            if (result.success && result.data.snap_token) {
                // Remove the preventModalClose listener so we can hide the modal
                const modalElement = document.getElementById('paymentModal');
                if (modalElement) {
                    modalElement.removeEventListener('hide.bs.modal', this.preventModalClose);
                }

                // Close the current modal
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }

                // Open Midtrans Snap
                this.showOverlay();
                window.snap.pay(result.data.snap_token, {
                    onSuccess: (result) => {
                        this.handlePaymentSuccess(result);
                    },
                    onPending: (result) => {
                        this.handlePaymentPending(result);
                    },
                    onError: (result) => {
                        this.handlePaymentError(result);
                    },
                    onClose: () => {
                        this.handlePaymentClose();
                    }
                });
            } else {
                this.showErrorMessage(result.error || 'Payment failed. Please try again.');
            }
        } catch (error) {
            console.error('Payment processing error:', error);
            this.showErrorMessage(error.message || 'An error occurred during payment processing.');
        } finally {
            payButton.disabled = false;
            payButton.innerHTML = originalText;
        }
    }

    handlePaymentSuccess(result) {
        console.log('Payment success:', result);

        this.updateOverlayStatus('success', 'Payment Successful!', 'Your order has been updated. Reloading...');
        this.updatePayButtonToPaid();

        setTimeout(() => {
            if (this.currentOrder && this.currentOrder.id) {
                window.location.href = `/orders/${this.currentOrder.id}`;
            } else {
                window.location.reload();
            }
        }, 2500);
    }

    handlePaymentPending(result) {
        console.log('Payment pending:', result);
        this.updateOverlayStatus('pending', 'Payment Pending', 'Waiting for verification. Please wait...');
        this.pollPaymentStatus(result.order_id);
    }

    handlePaymentError(result) {
        console.error('Payment error:', result);
        this.updateOverlayStatus('error', 'Payment Failed', 'Something went wrong. Please try again.');

        setTimeout(() => {
            this.hideOverlay();
        }, 2000);
    }

    handlePaymentClose() {
        console.log('Payment window closed by user');
        this.updateOverlayStatus('error', 'Payment Cancelled', 'You closed the payment window. Click anywhere to dismiss.');

        // Add one-time click listener to overlay for manual dismissal
        const overlay = document.getElementById('paymentProcessingOverlay');
        if (overlay) {
            const dismiss = () => {
                this.hideOverlay();
                overlay.removeEventListener('click', dismiss);
            };
            overlay.addEventListener('click', dismiss);
        }

        setTimeout(() => {
            this.hideOverlay();
        }, 3000);
    }

    // Overlay Management
    showOverlay() {
        const overlay = document.getElementById('paymentProcessingOverlay');
        if (overlay) {
            overlay.style.display = 'flex';
            overlay.style.background = 'rgba(0, 0, 0, 0.25)';
            overlay.style.backdropFilter = 'blur(5px)';
            overlay.style.cursor = 'default';
            this.updateOverlayStatus('processing');
        }
    }

    hideOverlay() {
        const overlay = document.getElementById('paymentProcessingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    updateOverlayStatus(status, title = '', message = '') {
        const overlay = document.getElementById('paymentProcessingOverlay');
        const spinner = overlay?.querySelector('.payment-spinner');
        const successIcon = overlay?.querySelector('.success-icon');
        const errorIcon = overlay?.querySelector('.error-icon');
        const titleEl = document.getElementById('paymentOverlayTitle');
        const messageEl = document.getElementById('paymentOverlayMessage');

        if (!overlay) return;

        // Reset all icons
        if (spinner) spinner.style.display = 'none';
        if (successIcon) successIcon.style.display = 'none';
        if (errorIcon) errorIcon.style.display = 'none';

        if (status === 'processing') {
            if (spinner) spinner.style.display = 'block';
            if (titleEl) titleEl.textContent = title || 'Processing Payment';
            if (messageEl) messageEl.textContent = message || 'Please don\'t close this window...';
        } else if (status === 'pending') {
            if (spinner) spinner.style.display = 'block';
            if (titleEl) titleEl.textContent = title || 'Verification Pending';
            if (messageEl) messageEl.textContent = message || 'We are verifying your payment...';
        } else if (status === 'success') {
            if (successIcon) successIcon.style.display = 'block';
            if (titleEl) titleEl.textContent = title || 'Payment Success!';
            if (messageEl) messageEl.textContent = message || 'Redirecting...';
        } else if (status === 'error') {
            if (errorIcon) errorIcon.style.display = 'block';
            if (titleEl) titleEl.textContent = title || 'Payment Issue';
            if (messageEl) messageEl.textContent = message || 'Transaction was not completed.';
            // Make background darker but remove blur so text is super clear
            overlay.style.background = 'rgba(0, 0, 0, 0.7)';
            overlay.style.backdropFilter = 'none';
            overlay.style.cursor = 'pointer';
        }
    }

    async pollPaymentStatus(paymentId, attempts = 0) {
        if (attempts >= 10) {
            this.showMessage('Payment status check timed out. Please refresh the page.', 'warning');
            return;
        }

        try {
            const response = await fetch(`/payments/${paymentId}/status`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success && result.payment_status === 'completed') {
                this.updateOverlayStatus('success', 'Payment Verified!', 'Your order has been updated. Reloading...');

                // Update the Pay Now button to show Paid status immediately
                this.updatePayButtonToPaid();

                setTimeout(() => {
                    if (this.currentOrder && this.currentOrder.id) {
                        window.location.href = `/orders/${this.currentOrder.id}`;
                    } else {
                        window.location.reload();
                    }
                }, 2000);
            } else if (result.payment_status === 'pending') {
                setTimeout(() => {
                    this.pollPaymentStatus(paymentId, attempts + 1);
                }, 3000);
            } else {
                this.updateOverlayStatus('error', 'Verification Failed', 'Could not confirm payment status.');
                setTimeout(() => {
                    this.hideOverlay();
                    this.showErrorMessage('Payment verification failed.');
                }, 2000);
            }
        } catch (error) {
            console.error('Error polling payment status:', error);
        }
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'danger');
    }

    showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10001; max-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}-fill me-2"></i>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }

    updatePayButtonToPaid() {
        if (!this.currentOrder || !this.currentOrder.id) {
            console.warn('No current order to update');
            return;
        }

        // Find the order element by order ID
        const orderElement = document.querySelector(`[data-order-id="${this.currentOrder.id}"]`);
        if (!orderElement) {
            console.warn('Order element not found');
            return;
        }

        // Find all Pay Now buttons for this order
        const payButtons = orderElement.querySelectorAll('button[onclick*="showPaymentForOrder"]');

        payButtons.forEach(button => {
            // Find the parent container of the button
            const container = button.closest('.mt-2') || button.parentElement;

            if (container) {
                // Replace the entire container with the Paid badge
                container.innerHTML = `
                    <span class="badge bg-info">On Progress</span>
                    <span class="badge bg-success ms-1">
                        <i class="bi bi-check-circle"></i> Paid
                    </span>
                `;
            }
        });

        console.log('Updated Pay Now button to Paid status');
    }
}

// Make PaymentHandler globally available
window.PaymentHandler = PaymentHandler;

// Initialize payment handler immediately
console.log('Creating PaymentHandler instance...');
window.paymentHandler = new PaymentHandler();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded, ensuring PaymentHandler is initialized...');
    if (!window.paymentHandler) {
        window.paymentHandler = new PaymentHandler();
    }
    window.paymentHandler.init();
});

// Global function to add payment success message to chat
window.addPaymentSuccessMessage = function (orderData) {
    const messagesContainer = document.getElementById('messages');
    if (messagesContainer) {
        const statusDiv = document.createElement('div');
        statusDiv.className = 'order-status mb-3 text-center';
        statusDiv.innerHTML = `
            <div class="d-inline-block bg-success text-white p-2 rounded">
                <small>💳 Payment completed for Order #${orderData.order_number || orderData.id}</small>
            </div>
        `;
        messagesContainer.appendChild(statusDiv);

        const scrollFunction = window.scrollToBottom || function () {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        };
        scrollFunction();
    }
};
