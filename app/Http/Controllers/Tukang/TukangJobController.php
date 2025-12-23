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
    public function index(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();
        $filter = $request->query('filter', 'all');

        $query = Order::where('tukang_id', $tukang->id)
            ->with(['customer', 'service', 'completion', 'review'])
            ->latest();

        switch ($filter) {
            case 'ongoing':
                $query->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_ON_PROGRESS]);
                break;
            case 'completed':
                $query->where('status', Order::STATUS_COMPLETED);
                break;
            case 'cancelled':
                $query->whereIn('status', [Order::STATUS_REJECTED, 'cancelled']); // Using string 'cancelled' if constant isn't defined
                break;
            case 'all':
            default:
                // For "All", we might want to exclude strictly cancelled/rejected to keep it clean, 
                // OR show everything. Based on customer view, "All" usually shows everything.
                // Let's show everything for "All".
                break;
        }

        $jobs = $query->paginate(15);

        return view('tukang.jobs.index', compact('jobs', 'filter'));
    }

    public function history()
    {
        $tukang = Auth::guard('tukang')->user();

        // Show COMPLETED jobs only (history)
        $jobs = Order::where('tukang_id', $tukang->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->with(['customer', 'service', 'completion', 'review'])
            ->latest()
            ->paginate(15);

        return view('tukang.jobs.history', compact('jobs'));
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
            return redirect()->route('tukang.jobs.show', $order)->with('error', 'Only orders in progress can be completed');
        }

        return response()
            ->view('tukang.jobs.complete', compact('order'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function submitCompletion(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== Order::STATUS_ON_PROGRESS) {
            return redirect()->route('tukang.jobs.show', $order)
                ->with('error', 'This order has already been completed or is not in progress.');
        }

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
