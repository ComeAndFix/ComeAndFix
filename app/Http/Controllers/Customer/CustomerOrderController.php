<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['tukang', 'service', 'completion']);

        return view('customer.orders.show', compact('order'));
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized access to order');
        }
    }
}
