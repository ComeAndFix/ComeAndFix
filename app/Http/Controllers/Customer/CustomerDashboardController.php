<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $recentOrders = Order::where('customer_id', auth()->guard('customer')->id())
            ->where('status', 'on_progress')
            ->with(['service', 'tukang'])
            ->latest()
            ->take(4)
            ->get();

        return view('customer.dashboard', compact('recentOrders'));
    }
}
