<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);

        Log::info('Midtrans Config Loaded', [
            'server_key_prefix' => substr(Config::$serverKey, 0, 15),
            'client_key_prefix' => substr(Config::$clientKey, 0, 15),
            'is_production' => Config::$isProduction
        ]);
    }

    public function createTransaction($order, $customer, $paymentMethod = 'qris')
    {
        try {
            $orderId = $order->order_number . '-' . time();

            // Use customer_total which includes the platform fee
            $amount = (int) $order->customer_total;

            $enabledPayments = ['gopay', 'shopeepay', 'other_qris'];
            
            if ($paymentMethod === 'virtual_account') {
                $enabledPayments = ['bca_va', 'bni_va', 'bri_va', 'echannel', 'permata_va'];
            }

            // Build item details
            $itemDetails = [];
            
            // Add base service
            $itemDetails[] = [
                'id' => 'service-' . ($order->service_id ?? 1),
                'price' => (int) $order->price,
                'quantity' => 1,
                'name' => $order->service->name ?? 'Service',
            ];

            // Add additional items
            if ($order->additionalItems && $order->additionalItems->count() > 0) {
                foreach ($order->additionalItems as $item) {
                    $itemDetails[] = [
                        'id' => 'add-' . $item->id,
                        'price' => (int) $item->item_price,
                        'quantity' => (int) $item->quantity,
                        'name' => substr($item->item_name, 0, 50), // Midtrans name limit
                    ];
                }
            }

            // Add custom items
            if ($order->customItems && $order->customItems->count() > 0) {
                foreach ($order->customItems as $item) {
                    $itemDetails[] = [
                        'id' => 'custom-' . $item->id,
                        'price' => (int) $item->item_price,
                        'quantity' => (int) $item->quantity,
                        'name' => substr($item->item_name, 0, 50), // Midtrans name limit
                    ];
                }
            }

            // Add platform fee as a separate line item
            $itemDetails[] = [
                'id' => 'platform-fee',
                'price' => (int) $order->platform_fee,
                'quantity' => 1,
                'name' => 'Platform Fee (10%)',
            ];

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $amount,
                ],
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '087780932198',
                ],
                'item_details' => $itemDetails,
                'enabled_payments' => $enabledPayments,
                'custom_expiry' => [
                    'expiry_duration' => 15,
                    'unit' => 'minute'
                ]
            ];

            Log::info('Creating Midtrans transaction', [
                'order_id' => $orderId,
                'amount' => $amount,
                'subtotal' => $order->subtotal,
                'platform_fee' => $order->platform_fee,
                'customer_total' => $order->customer_total,
                'params' => $params
            ]);

            $snapToken = Snap::getSnapToken($params);

            Log::info('Midtrans snap token created successfully', [
                'snap_token' => $snapToken,
                'transaction_id' => $orderId
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'transaction_id' => $orderId
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans create transaction error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkTransactionStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);

            Log::info('Transaction status checked', [
                'order_id' => $orderId,
                'status' => $status
            ]);

            return [ 
                'success' => true,
                'status' => $status
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans check status error', [
                'order_id' => $orderId,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function handleNotification($notification)
    {
        try {
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            Log::info('Processing Midtrans notification', [
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'order_id' => $notification->order_id ?? null
            ]);

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    return 'completed';
                }
            } else if ($transactionStatus == 'settlement') {
                return 'completed';
            } else if ($transactionStatus == 'pending') {
                return 'pending';
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                return 'failed';
            }

            return 'pending';
        } catch (\Exception $e) {
            Log::error('Midtrans handle notification error', [
                'message' => $e->getMessage()
            ]);
            return 'failed';
        }
    }
}
