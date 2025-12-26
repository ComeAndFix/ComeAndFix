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
        
        // Get only active orders (non-completed and non-expired pending)
        $recentOrders = Order::where('customer_id', $customerId)
            ->where(function($query) {
                // Include orders that are accepted or on_progress
                $query->whereIn('status', ['accepted', 'on_progress'])
                      // OR orders that are pending AND not expired
                      ->orWhere(function($q) {
                          $q->where('status', 'pending')
                            ->where('expires_at', '>', now());
                      });
            })
            ->with(['service', 'tukang', 'additionalItems', 'customItems'])
            ->orderByRaw('CASE WHEN work_datetime IS NULL THEN 1 ELSE 0 END') // Put null dates last
            ->orderBy('work_datetime', 'asc') // Sort by nearest date first
            ->take(4)
            ->get();

        return view('customer.dashboard', compact('recentOrders'));
    }
}
