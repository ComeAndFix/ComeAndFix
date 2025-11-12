<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'payment_method' => 'required|in:credit_card,gopay,bank_transfer',
                'amount' => 'required|numeric|min:0'
            ]);

            $order = Order::findOrFail($validated['order_id']);

            // Verify order belongs to authenticated customer
            if ($order->customer_id !== auth()->guard('customer')->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to order'
                ], 403);
            }

            // Verify payment amount matches order price
            if ((float)$request->amount !== (float)$order->price) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment amount does not match order price'
                ], 422);
            }

            // Begin transaction
            DB::beginTransaction();

            try {
                // Create payment record
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'transaction_id' => Str::uuid()
                ]);

                // Update order status
                $order->update([
                    'status' => Order::STATUS_ON_PROGRESS,
                    'payment_status' => Order::PAYMENT_STATUS_PAID
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'data' => [
                        'payment_id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'order_status' => $order->status
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
