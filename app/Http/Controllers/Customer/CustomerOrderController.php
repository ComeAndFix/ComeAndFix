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

    public function approveCompletion(Order $order)
    {
        $this->authorizeOrder($order);

        $completion = $order->completion;

        if (!$completion || !$completion->isPending()) {
            return redirect()->back()->with('error', 'No pending completion to approve');
        }

        $completion->update([
            'status' => OrderCompletion::STATUS_APPROVED,
            'reviewed_at' => now()
        ]);

        $order->update([
            'status' => Order::STATUS_COMPLETED
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Completion proof approved successfully!');
    }

    public function rejectCompletion(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $completion = $order->completion;

        if (!$completion || !$completion->isPending()) {
            return redirect()->back()->with('error', 'No pending completion to reject');
        }

        $completion->update([
            'status' => OrderCompletion::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Completion proof rejected. Tukang will resubmit.');
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized access to order');
        }
    }
}
