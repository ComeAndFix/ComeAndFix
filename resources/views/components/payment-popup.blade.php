<!-- Midtrans Snap Script -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

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
                        <input class="form-check-input" type="radio" name="paymentMethod" id="qris" value="qris">
                        <label class="form-check-label" for="qris">
                            <i class="bi bi-qr-code me-2"></i>QRIS (Scan to Pay)
                        </label>
                    </div>
                    <small class="text-muted d-block mb-3">Supported: GoPay, OVO, Dana, ShopeePay, LinkAja, and more</small>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="virtualAccount" value="virtual_account">
                        <label class="form-check-label" for="virtualAccount">
                            <i class="bi bi-bank me-2"></i>Virtual Account
                        </label>
                    </div>
                    <small class="text-muted">Supported: BCA, BNI, BRI, Mandiri, Permata</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel Payment</button>
                <button type="button" class="btn btn-primary" id="payButton">Pay Now</button>
            </div>
        </div>
    </div>
</div>
