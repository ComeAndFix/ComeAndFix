<div id="paymentModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
            </div>
            <div class="modal-body">
                <div class="order-details mb-4">
                    <h6>Order Summary</h6>
                    <div id="orderDetails"></div>
                    <hr>
                    <div class="total-amount">
                        <strong>Total: Rp <span id="totalAmount">0</span></strong>
                    </div>
                </div>

                <div class="payment-methods">
                    <h6>Select Payment Method</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="credit_card">
                        <label class="form-check-label" for="creditCard">
                            <i class="bi bi-credit-card me-2"></i>Credit Card
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="gopay" value="gopay">
                        <label class="form-check-label" for="gopay">
                            <i class="bi bi-phone me-2"></i>GoPay
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="bank_transfer">
                        <label class="form-check-label" for="bankTransfer">
                            <i class="bi bi-bank me-2"></i>Bank Transfer
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel Payment</button>
                <button type="button" class="btn btn-primary" id="payButton" disabled>Pay Now</button>
            </div>
        </div>
    </div>
</div>
