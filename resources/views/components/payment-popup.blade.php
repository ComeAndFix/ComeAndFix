<!-- Midtrans Snap Script -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<div id="paymentModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-jost fw-bold fs-4">Secure Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-4">
                <div class="payment-summary-section">
                    <h6 class="font-jost fw-bold mb-3 text-muted text-uppercase small" style="letter-spacing: 1px;">Pricing Details</h6>
                    <div id="orderDetails"></div>
                    
                    <div class="payment-total-row d-flex justify-content-between align-items-center mt-4">
                        <span class="fw-bold text-dark fs-5">Total Amount</span>
                        <span class="font-jost fw-900 text-brand-orange fs-2">Rp <span id="totalAmount">0</span></span>
                    </div>
                </div>

                <div class="payment-selection">
                    <h6 class="font-jost fw-bold mb-3">Select Payment Method</h6>
                    <div class="payment-method-grid">
                        <label class="payment-method-card" for="qris">
                            <input type="radio" name="paymentMethod" id="qris" value="qris">
                            <i class="bi bi-qr-code"></i>
                            <div class="payment-method-label">QRIS</div>
                            <div class="payment-method-desc">Gopay, OVO, Dana, etc.</div>
                        </label>

                        <label class="payment-method-card" for="virtualAccount">
                            <input type="radio" name="paymentMethod" id="virtualAccount" value="virtual_account">
                            <i class="bi bi-bank"></i>
                            <div class="payment-method-label">Virtual Account</div>
                            <div class="payment-method-desc">BCA, BNI, BRI, Mandiri</div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-4 justify-content-center">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-brand-orange rounded-pill px-5 fw-bold" id="payButton" disabled>
                    Complete Payment
                </button>
            </div>
        </div>
    </div>
</div>
