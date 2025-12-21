<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $customerId = auth()->guard('customer')->id();
        
        // Get only active orders (non-completed)
        $recentOrders = Order::where('customer_id', $customerId)
            ->whereIn('status', ['pending', 'accepted', 'on_progress'])
            ->with(['service', 'tukang', 'additionalItems', 'customItems'])
            ->latest()
            ->take(4)
            ->get();

        return view('customer.dashboard', compact('recentOrders'));
    }
}
