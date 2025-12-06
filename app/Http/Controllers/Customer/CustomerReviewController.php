<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReviewController extends Controller
{
    public function create(Order $order)
    {
        $this->authorizeOrder($order);

        // Check if order is completed
        if ($order->status !== Order::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'You can only review completed orders');
        }

        // Check if already reviewed
        if ($order->hasReview()) {
            return redirect()->back()->with('error', 'You have already reviewed this order');
        }

        $order->load(['tukang', 'service', 'completion']);

        return view('customer.reviews.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        // Check if order is completed
        if ($order->status !== Order::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'You can only review completed orders');
        }

        // Check if already reviewed
        if ($order->hasReview()) {
            return redirect()->back()->with('error', 'You have already reviewed this order');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Create review
        Review::create([
            'order_id' => $order->id,
            'customer_id' => Auth::guard('customer')->id(),
            'tukang_id' => $order->tukang_id,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
        ]);

        // Update tukang's average rating
        $order->tukang->updateAverageRating();

        return redirect()->route('dashboard')
            ->with('success', 'Thank you for your review!');
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized access to order');
        }
    }
}
