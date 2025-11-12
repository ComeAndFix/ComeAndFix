// Payment popup functionality
class PaymentHandler {
    constructor() {
        this.currentOrder = null;
        this.initialized = false;
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
        let html = '<div class="order-items">';
        if (orderData.items && orderData.items.length > 0) {
            orderData.items.forEach(item => {
                html += `
                    <div class="order-item d-flex justify-content-between mb-2">
                        <span>${item.name} x ${item.quantity || 1}</span>
                        <span>Rp ${typeof item.price === 'string' ? item.price : parseFloat(item.price).toLocaleString('id-ID')}</span>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="order-item d-flex justify-content-between mb-2">
                    <span>${orderData.service_name || 'Service'}</span>
                    <span>Rp ${parseFloat(orderData.total_amount).toLocaleString('id-ID')}</span>
                </div>
            `;
        }

        if (orderData.description) {
            html += `<div class="mt-2"><small class="text-muted">${orderData.description}</small></div>`;
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

            // Add validation for amount
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

            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                if (modal) {
                    modal.hide();
                }
                this.showSuccessMessage('Payment completed successfully!');

                // Update order status and payment status in UI
                const orderElement = document.querySelector(`[data-order-id="${this.currentOrder.id}"]`);
                if (orderElement) {
                    const statusBadge = orderElement.querySelector('.badge');
                    if (statusBadge) {
                        statusBadge.className = 'badge bg-info';
                        statusBadge.textContent = 'On Progress';
                    }
                }

                if (typeof window.addPaymentSuccessMessage === 'function') {
                    window.addPaymentSuccessMessage(this.currentOrder);
                }

                // Delay reload to show success message
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'danger');
    }

    showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
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
}

// Make PaymentHandler globally available
window.PaymentHandler = PaymentHandler;

// Initialize payment handler immediately
console.log('Creating PaymentHandler instance...');
window.paymentHandler = new PaymentHandler();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, ensuring PaymentHandler is initialized...');
    if (!window.paymentHandler) {
        window.paymentHandler = new PaymentHandler();
    }
    window.paymentHandler.init();
});

// Global function to add payment success message to chat
window.addPaymentSuccessMessage = function(orderData) {
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

        const scrollFunction = window.scrollToBottom || function() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        };
        scrollFunction();
    }
};
