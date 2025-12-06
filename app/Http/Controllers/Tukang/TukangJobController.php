<?php

namespace App\Http\Controllers\Tukang;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TukangJobController extends Controller
{
    public function index()
    {
        $tukang = Auth::guard('tukang')->user();

        $jobs = Order::where('tukang_id', $tukang->id)
            ->whereIn('status', [Order::STATUS_ON_PROGRESS, Order::STATUS_COMPLETED])
            ->with(['customer', 'service', 'completion', 'review'])
            ->latest()
            ->paginate(10);

        return view('tukang.jobs.index', compact('jobs'));
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['customer', 'service', 'completion', 'review']);

        return view('tukang.jobs.show', compact('order'));
    }

    public function completeForm(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== Order::STATUS_ON_PROGRESS) {
            return redirect()->back()->with('error', 'Only orders in progress can be completed');
        }

        return view('tukang.jobs.complete', compact('order'));
    }

    public function submitCompletion(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'description' => 'required|string|min:20',
            'working_duration' => 'required|integer|min:1',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('completion-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // Create or update completion
        $completion = OrderCompletion::updateOrCreate(
            ['order_id' => $order->id],
            [
                'description' => $validated['description'],
                'working_duration' => $validated['working_duration'],
                'photos' => $photoPaths,
                'submitted_at' => now(),
            ]
        );

        // Automatically mark order as completed
        $order->update([
            'status' => Order::STATUS_COMPLETED
        ]);

        return redirect()->route('tukang.jobs.show', $order)
            ->with('success', 'Completion proof submitted successfully. Order is now completed!');
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->tukang_id !== Auth::guard('tukang')->id()) {
            abort(403, 'Unauthorized access to order');
        }
    }
}
