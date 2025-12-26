<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,uuid',
                'payment_method' => 'required|in:qris,virtual_account',
                'amount' => 'required|numeric|min:0'
            ]);

            $order = Order::where('uuid', $validated['order_id'])
                ->with(['service', 'additionalItems', 'customItems'])
                ->firstOrFail();

            if ($order->customer_id !== auth()->guard('customer')->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to order'
                ], 403);
            }

            if (abs((float)$request->amount - (float)$order->customer_total) > 1) { // Allow small float difference
                return response()->json([
                    'success' => false,
                    'error' => 'Payment amount does not match order price',
                    'debug' => [
                        'request_amount' => $request->amount,
                        'order_customer_total' => $order->customer_total,
                        'order_subtotal' => $order->subtotal,
                        'platform_fee' => $order->platform_fee
                    ]
                ], 422);
            }

            DB::beginTransaction();

            try {
                $customer = auth()->guard('customer')->user();

                $midtransResult = $this->midtransService->createTransaction($order, $customer, $validated['payment_method']);

                if (!$midtransResult['success']) {
                    throw new \Exception($midtransResult['error']);
                }

                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'status' => Payment::STATUS_PENDING,
                    'transaction_id' => $midtransResult['transaction_id'],
                    'snap_token' => $midtransResult['snap_token'],
                    'expired_at' => now()->addMinutes(15)
                ]);

                $order->update([
                    'status' => Order::STATUS_ACCEPTED,
                    'payment_status' => Order::PAYMENT_STATUS_UNPAID
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment initiated successfully',
                    'data' => [
                        'payment_id' => $payment->id,
                        'snap_token' => $payment->snap_token,
                        'transaction_id' => $payment->transaction_id,
                        'order_status' => $order->status
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        try {
            $notification = json_decode($request->getContent());

            $transactionId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;

            $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();
            $order = $payment->order;

            $status = $this->midtransService->handleNotification($notification);

            $payment->update([
                'status' => $status,
                'payment_response' => (array) $notification
            ]);

            if ($status === 'completed') {
                $order->update([
                    'status' => Order::STATUS_ON_PROGRESS,
                    'payment_status' => Order::PAYMENT_STATUS_PAID
                ]);
                
                broadcast(new \App\Events\OrderStatusUpdated($order->load('service')));
            } elseif ($status === 'failed') {
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_FAILED
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Payment notification error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function checkStatus(Request $request, $paymentId)
    {
        try {
            $payment = Payment::with('order')->findOrFail($paymentId);

            if ($payment->order->customer_id !== auth()->guard('customer')->id()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $result = $this->midtransService->checkTransactionStatus($payment->transaction_id);

            if ($result['success']) {
                $status = $this->midtransService->handleNotification($result['status']);

                $payment->update(['status' => $status]);

                if ($status === 'completed' && $payment->order->payment_status !== Order::PAYMENT_STATUS_PAID) {
                    $payment->order->update([
                        'status' => Order::STATUS_ON_PROGRESS,
                        'payment_status' => Order::PAYMENT_STATUS_PAID
                    ]);
                    
                    broadcast(new \App\Events\OrderStatusUpdated($payment->order->load('service')));
                }

                return response()->json([
                    'success' => true,
                    'payment_status' => $status,
                    'order_status' => $payment->order->status
                ]);
            }

            return response()->json($result, 500);

        } catch (\Exception $e) {
            Log::error('Check payment status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to check payment status'
            ], 500);
        }
    }
}
