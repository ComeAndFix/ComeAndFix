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
        
        // Get on_progress orders and completed orders without reviews
        $recentOrders = Order::where('customer_id', $customerId)
            ->where(function($query) {
                $query->where('status', 'on_progress')
                      ->orWhere(function($q) {
                          $q->where('status', 'completed')
                            ->doesntHave('review');
                      });
            })
            ->with(['service', 'tukang', 'completion', 'review'])
            ->latest()
            ->take(4)
            ->get();

        return view('customer.dashboard', compact('recentOrders'));
    }
}
