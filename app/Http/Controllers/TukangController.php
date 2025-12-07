<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\Tukang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TukangController extends Controller
{
    public function dashboard(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        if (!$tukang) {
            return redirect()->route('tukang.login');
        }

        // Get all available services for filter dropdown
        $availableServices = \App\Models\Service::where('is_active', true)->orderBy('name')->get();

        // Get incoming job requests from chat messages grouped by customer and service
        $allMessages = ChatMessage::where('receiver_type', 'App\Models\Tukang')
            ->where('receiver_id', $tukang->id)
            ->where('sender_type', 'App\Models\Customer')
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by customer_id and conversation_service_id to avoid duplicates
        $jobRequests = $allMessages->groupBy(function($message) {
                return $message->sender_id . '-' . ($message->conversation_service_id ?? 'general');
            })
            ->map(function($group) use ($tukang) {
                // Get the latest message for each customer-service combination
                $latestMessage = $group->first();
                
                // Get service info if service_id exists
                if ($latestMessage->conversation_service_id) {
                    $latestMessage->service = \App\Models\Service::find($latestMessage->conversation_service_id);
                }
                
                // Check for active or in-progress orders (should hide these)
                $hasActiveOrder = \App\Models\Order::where('tukang_id', $tukang->id)
                    ->where('customer_id', $latestMessage->sender_id)
                    ->where('service_id', $latestMessage->conversation_service_id)
                    ->whereIn('status', ['accepted', 'on_progress'])
                    ->exists();
                
                if ($hasActiveOrder) {
                    $latestMessage->should_hide = true;
                    return $latestMessage;
                }
                
                // Check for completed orders
                $completedOrder = \App\Models\Order::where('tukang_id', $tukang->id)
                    ->where('customer_id', $latestMessage->sender_id)
                    ->where('service_id', $latestMessage->conversation_service_id)
                    ->where('status', 'completed')
                    ->orderBy('updated_at', 'desc')
                    ->first();
                
                // If there's a completed order, check if message is AFTER completion
                if ($completedOrder) {
                    // Show as new request if message came after order completion
                    $latestMessage->should_hide = $latestMessage->created_at <= $completedOrder->updated_at;
                } else {
                    // No completed order, show the message
                    $latestMessage->should_hide = false;
                }
                
                return $latestMessage;
            })
            ->filter(function($message) {
                // Only show messages without active orders or messages after completion
                return !$message->should_hide;
            })
            ->values();

        // Apply filters
        if ($request->filled('customer_name')) {
            $searchName = $request->get('customer_name');
            $jobRequests = $jobRequests->filter(function($jobRequest) use ($searchName) {
                return stripos($jobRequest->sender->name, $searchName) !== false;
            });
        }

        if ($request->filled('service_filter')) {
            $serviceId = $request->get('service_filter');
            $jobRequests = $jobRequests->filter(function($jobRequest) use ($serviceId) {
                return $jobRequest->conversation_service_id == $serviceId;
            });
        }

        // Sort by most recent
        $jobRequests = $jobRequests->sortByDesc('created_at')->take(10)->values();

        // Count new messages
        $newMessagesCount = ChatMessage::where('receiver_type', 'App\Models\Tukang')
            ->where('receiver_id', $tukang->id)
            ->whereNull('read_at')
            ->count();

        // Count active jobs (order proposals that are not rejected or completed)
        $activeJobsCount = \App\Models\Order::where('tukang_id', $tukang->id)
            ->whereIn('status', ['pending', 'accepted', 'on_progress'])
            ->count();

        // Get scheduled jobs for calendar (paid orders with work_datetime)
        $scheduledJobs = \App\Models\Order::where('tukang_id', $tukang->id)
            ->where('payment_status', 'paid')
            ->whereIn('status', ['accepted', 'on_progress'])
            ->whereNotNull('work_datetime')
            ->with(['customer', 'service'])
            ->get()
            ->groupBy(function($order) {
                return $order->work_datetime->format('Y-m-d');
            });

        // Calculate wallet balance (all completed orders)
        $completedOrders = \App\Models\Order::where('tukang_id', $tukang->id)
            ->where('status', 'completed')
            ->with(['additionalItems', 'customItems'])
            ->get();
        
        $walletBalance = $completedOrders->sum(function($order) {
            return $order->total_price;
        });

        // Calculate this month's income
        $monthlyOrders = \App\Models\Order::where('tukang_id', $tukang->id)
            ->where('status', 'completed')
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->with(['additionalItems', 'customItems'])
            ->get();
        
        $monthlyIncome = $monthlyOrders->sum(function($order) {
            return $order->total_price;
        });

        return view('tukang.dashboard', compact('jobRequests', 'newMessagesCount', 'activeJobsCount', 'scheduledJobs', 'walletBalance', 'monthlyIncome', 'availableServices'));
    }

    public function profile()
    {
        $tukang = Auth::guard('tukang')->user();
        return view('tukang.profile', compact('tukang'));
    }

    public function updateProfile(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'specializations' => ['required', 'array', 'min:1'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $tukang->update($request->only([
            'name', 'phone', 'address', 'city', 'postal_code',
            'specializations', 'years_experience', 'hourly_rate', 'description'
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function jobs()
    {
        $tukang = Auth::guard('tukang')->user();

        // Get job requests (messages from customers)
        $jobRequests = ChatMessage::where('receiver_type', 'App\Models\Tukang')
            ->where('receiver_id', $tukang->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('conversation_id')
            ->map(function ($conversationMessages) {
                return $conversationMessages->first();
            })
            ->values();

        return view('tukang.jobs', compact('jobRequests'));
    }

    public function toggleAvailability(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        $tukang->update([
            'is_available' => !$tukang->is_available
        ]);

        return response()->json([
            'success' => true,
            'is_available' => $tukang->is_available
        ]);
    }
}
