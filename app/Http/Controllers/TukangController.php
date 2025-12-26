<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\Tukang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class TukangController extends Controller
{
    public function dashboard(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        if (!$tukang) {
            return redirect()->route('tukang.login');
        }

        // Get all available services for filter dropdown (only ones this tukang offers)
        // Matching by name from specializations array since pivot table might not be populated
        $specializations = $tukang->specializations ?? [];
        $availableServices = \App\Models\Service::where('is_active', true)
            ->whereIn('name', $specializations)
            ->orderBy('name')
            ->get();

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
            ->filter(function($message) use ($tukang) {
                // Primary Filter: Only show conversations with UNREAD messages
                // This satisfies the requirement to "remove if read" and "show if new"
                // It also fixes the issue where messages about active orders were being hidden even if unread
                $hasUnread = \App\Models\ChatMessage::where('conversation_id', $message->conversation_id)
                    ->where('receiver_type', 'App\Models\Tukang')
                    ->where('receiver_id', $tukang->id)
                    ->whereNull('read_at')
                    ->exists();
                
                return $hasUnread;
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

        // Calculate wallet balance (all completed orders minus withdrawals)
        $completedOrders = \App\Models\Order::where('tukang_id', $tukang->id)
            ->where('status', 'completed')
            ->with(['additionalItems', 'customItems'])
            ->get();
        
        $totalEarnings = $completedOrders->sum(function($order) {
            return $order->total_price;
        });
        
        $withdrawnAmount = \Illuminate\Support\Facades\Session::get('tukang_withdrawn_' . $tukang->id, 0);
        $walletBalance = $totalEarnings - $withdrawnAmount;

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

        // Get active job for display (similar to customer dashboard)
        $activeJob = \App\Models\Order::where('tukang_id', $tukang->id)
            ->whereIn('status', ['accepted', 'on_progress'])
            ->with(['customer', 'service'])
            ->orderByRaw("FIELD(status, 'on_progress', 'accepted')")
            ->latest()
            ->first();

        return view('tukang.dashboard', compact('jobRequests', 'newMessagesCount', 'activeJobsCount', 'scheduledJobs', 'walletBalance', 'monthlyIncome', 'availableServices', 'activeJob'));
    }

    public function completeProfile(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'years_experience' => 'required|integer|min:0',
            'description' => 'required|string',
        ]);

        $tukang = Auth::guard('tukang')->user();
        
        $tukang->update([
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'years_experience' => $request->years_experience,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

    public function profile()
    {
        $tukang = Auth::guard('tukang')->user();
        
        // Calculate rating statistics
        $reviews = \App\Models\Review::whereHas('order', function($query) use ($tukang) {
            $query->where('tukang_id', $tukang->id);
        })->get();
        
        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating') ?? 0;
        $ratingDistribution = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
        
        // Get all available services for editing
        $availableServices = \App\Models\Service::where('is_active', true)->orderBy('name')->get();
        
        return view('tukang.profile', compact('tukang', 'totalReviews', 'averageRating', 'ratingDistribution', 'availableServices'));
    }

    public function updateProfile(Request $request)
    {
        $tukang = Auth::guard('tukang')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'specializations' => ['required', 'array', 'min:1'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $data = $request->only([
            'name', 'phone', 'address', 'city', 'postal_code',
            'latitude', 'longitude',
            'specializations', 'years_experience', 'hourly_rate', 'description'
        ]);

        if ($request->hasFile('profile_image')) {
            if ($tukang->profile_image) {
                Storage::delete($tukang->profile_image);
            }
            $path = $request->file('profile_image')->store('profile-photos');
            $data['profile_image'] = $path;
        }

        $tukang->update($data);

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
