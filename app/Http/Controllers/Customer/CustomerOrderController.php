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
                // Include pending orders (proposals awaiting customer action) along with accepted and in-progress orders
                $query->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_ON_PROGRESS]);
                break;
            case 'completed':
                $query->where('status', Order::STATUS_COMPLETED);
                break;
            case 'cancelled':
                $query->whereIn('status', ['cancelled', 'rejected']);
                break;
            default:
                // For 'all', show all orders including pending proposals
                $query->whereIn('status', [
                    Order::STATUS_PENDING,
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
