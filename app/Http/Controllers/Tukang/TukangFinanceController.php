<?php

namespace App\Http\Controllers\Tukang;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TukangFinanceController extends Controller
{
    public function index()
    {
        $tukang = Auth::guard('tukang')->user();

        // Calculate wallet balance (all completed orders)
        $completedOrders = Order::where('tukang_id', $tukang->id)
            ->where('status', 'completed')
            ->with(['additionalItems', 'customItems'])
            ->get();
        
        $totalEarnings = $completedOrders->sum(function($order) {
            return $order->total_price;
        });

        // Get withdrawn amount from session (dummy storage)
        $withdrawnAmount = Session::get('tukang_withdrawn_' . $tukang->id, 0);
        $walletBalance = $totalEarnings - $withdrawnAmount;

        // Monthly income breakdown (last 6 months)
        $monthlyBreakdown = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthOrders = Order::where('tukang_id', $tukang->id)
                ->where('status', 'completed')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->with(['additionalItems', 'customItems', 'customer', 'service'])
                ->get();
            
            $monthlyBreakdown[] = [
                'month' => $date->format('F Y'),
                'orders' => $monthOrders,
                'total' => $monthOrders->sum(function($order) {
                    return $order->total_price;
                }),
                'count' => $monthOrders->count()
            ];
        }

        return view('tukang.finance.index', compact('walletBalance', 'totalEarnings', 'withdrawnAmount', 'monthlyBreakdown'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000'
        ]);

        $tukang = Auth::guard('tukang')->user();

        // Calculate current balance
        $completedOrders = Order::where('tukang_id', $tukang->id)
            ->where('status', 'completed')
            ->with(['additionalItems', 'customItems'])
            ->get();
        
        $totalEarnings = $completedOrders->sum(function($order) {
            return $order->total_price;
        });

        $withdrawnAmount = Session::get('tukang_withdrawn_' . $tukang->id, 0);
        $currentBalance = $totalEarnings - $withdrawnAmount;

        // Check if sufficient balance
        if ($request->amount > $currentBalance) {
            return redirect()->back()->with('error', 'Insufficient balance');
        }

        // Dummy withdraw - just add to session
        $newWithdrawnAmount = $withdrawnAmount + $request->amount;
        Session::put('tukang_withdrawn_' . $tukang->id, $newWithdrawnAmount);

        return redirect()->route('tukang.finance.index')
            ->with('success', 'Withdrawal successful! Rp ' . number_format($request->amount, 0, ',', '.') . ' has been withdrawn.');
    }
}
