<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function index()
    {
        // Get active and completed orders
        $orders = Order::where('customer_id', Auth::guard('customer')->id())
            ->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_ON_PROGRESS, Order::STATUS_COMPLETED])
            ->with(['tukang', 'service', 'review', 'additionalItems', 'customItems'])
            ->latest()
            ->paginate(10);
            
        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['tukang', 'service', 'completion', 'additionalItems', 'customItems']);

        return view('customer.orders.show', compact('order'));
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized access to order');
        }
    }
}
