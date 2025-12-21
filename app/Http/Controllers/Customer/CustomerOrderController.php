<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = Order::where('customer_id', Auth::guard('customer')->id());

        switch ($filter) {
            case 'ongoing':
                $query->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_ON_PROGRESS]);
                break;
            case 'completed':
                $query->where('status', Order::STATUS_COMPLETED);
                break;
            case 'cancelled':
                $query->whereIn('status', ['cancelled', 'rejected']);
                break;
            default:
                // For 'all', we show everything relevant to the user's history
                // You might or might not want to show 'pending' here, depending on your flow.
                // Assuming we show everything except maybe strictly internal states if any.
                // Or stick to the original list: accepted, on_progress, completed, plus cancelled/rejected
                $query->whereIn('status', [
                    Order::STATUS_ACCEPTED, 
                    Order::STATUS_ON_PROGRESS, 
                    Order::STATUS_COMPLETED,
                    'cancelled',
                    'rejected'
                ]);
                break;
        }

        $orders = $query->with(['tukang', 'service', 'review', 'additionalItems', 'customItems'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
            
        return view('customer.orders.index', compact('orders', 'filter'));
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
