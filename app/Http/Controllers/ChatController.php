<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\OrderProposalSent;
use App\Events\OrderStatusUpdated;
use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Tukang;
use App\Models\TukangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function show($receiverType, $receiverId)
    {
        try{
            $receiver = $receiverType === 'tukang' ?
                Tukang::findOrFail($receiverId) :
                Customer::findOrFail($receiverId);

            $customer = Auth::guard('customer')->user();

            if(!$customer){
                return redirect()->route('customer.login')->with('error', 'Please log in to access the chat.');
            }

            // SECURITY: Customers can only chat with tukangs, not other customers
            if ($receiverType !== 'tukang') {
                abort(403, 'Unauthorized access to this conversation.');
            }

            $conversationId = ChatMessage::generateConversationId(
                $customer->id,
                'App\Models\Customer',
                $receiver->id,
                'App\Models\\' . ucfirst($receiverType)
            );

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->with(['sender', 'receiver', 'order.service', 'order.additionalItems', 'order.customItems'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            ChatMessage::where('conversation_id', $conversationId)
                ->where('receiver_type', 'App\Models\Customer')
                ->where('receiver_id', $customer->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return view('customer.chat', compact('receiver', 'messages', 'conversationId', 'receiverType'));
        }catch(\Exception $e){
            Log::error('Error loading chat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load chat. Please try again later.');
        }
    }

    public function sendMessage(Request $request)
    {
        try{
            $request->validate([
                'message' => 'required|string|max:1000',
                'receiver_id' => 'required|integer',
                'receiver_type' => 'required|in:tukang,customer',
                'service_type' => 'nullable|string',
            ]);

            $customer = Auth::guard('customer')->user();

            if(!$customer){
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $receiverClass = 'App\Models\\' . ucfirst($request->receiver_type);
            $receiver = $receiverClass::findOrFail($request->receiver_id);

            $conversationId = ChatMessage::generateConversationId(
                $customer->id,
                'App\Models\Customer',
                $receiver->id,
                $receiverClass
            );

            // Determine conversation service ID
            $conversationServiceId = null;
            
            if ($request->service_type) {
                // New service_type provided - use it for THIS message
                $service = \App\Models\Service::where('name', 'LIKE', $request->service_type . '%')
                    ->where('is_active', true)
                    ->first();
                if ($service) {
                    $conversationServiceId = $service->id;
                }
            }
            
            // If no service from request, use existing conversation service
            if (!$conversationServiceId) {
                $existingMessage = ChatMessage::where('conversation_id', $conversationId)
                    ->whereNotNull('conversation_service_id')
                    ->orderBy('created_at', 'desc')
                    ->first();
                if ($existingMessage) {
                    $conversationServiceId = $existingMessage->conversation_service_id;
                }
            }

            $message = ChatMessage::create([
                'conversation_id' => $conversationId,
                'conversation_service_id' => $conversationServiceId,
                'sender_type' => 'App\Models\Customer',
                'sender_id' => $customer->id,
                'receiver_type' => $receiverClass,
                'receiver_id' => $receiver->id,
                'message' => $request->message,
            ]);

            $message->load(['sender', 'receiver']);

            try{
                broadcast(new MessageSent($message))->toOthers();
            }
            catch(\Exception $e){
                Log::error('Broadcasting error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }catch(\Exception $e){
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to send message. Please try again later.'], 500);
        }
    }

    public function getMessages($conversationId)
    {
        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function showForTukang($receiverType, $receiverId)
    {
        try {
            $receiver = $receiverType === 'customer' ?
                Customer::findOrFail($receiverId) :
                Tukang::findOrFail($receiverId);

            $tukang = Auth::guard('tukang')->user();

            if (!$tukang) {
                return redirect()->route('tukang.login');
            }

            // SECURITY: Tukangs can only chat with customers, not other tukangs
            if ($receiverType !== 'customer') {
                abort(403, 'Unauthorized access to this conversation.');
            }

            $conversationId = ChatMessage::generateConversationId(
                $tukang->id,
                'App\Models\Tukang',
                $receiver->id,
                'App\Models\\' . ucfirst($receiverType)
            );

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->with(['sender', 'receiver', 'order.service', 'order.additionalItems', 'order.customItems'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            ChatMessage::where('conversation_id', $conversationId)
                ->where('receiver_type', 'App\Models\Tukang')
                ->where('receiver_id', $tukang->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Extract service context - PRIORITY: service_id > service_type > conversation messages
            $serviceId = request()->query('service_id');
            $serviceType = request()->query('service_type');
            $selectedService = null;
            
            // Priority 1: service_id from URL (from incoming job request click)
            if ($serviceId) {
                $selectedService = \App\Models\Service::find($serviceId);
            }
            // Priority 2: service_type from URL
            elseif ($serviceType) {
                // Use LIKE to match service names (e.g., "Plumbing" matches "Plumbing Services")
                $selectedService = \App\Models\Service::where('name', 'LIKE', $serviceType . '%')
                    ->where('is_active', true)
                    ->first();
            }
            // Priority 3: existing conversation service
            else {
                $messageWithService = ChatMessage::where('conversation_id', $conversationId)
                    ->whereNotNull('conversation_service_id')
                    ->first();
                if ($messageWithService && $messageWithService->conversation_service_id) {
                    $selectedService = \App\Models\Service::find($messageWithService->conversation_service_id);
                }
            }

            // check for active accepted/on_progress order
            $hasActiveOrder = Order::where('customer_id', $receiver->id)
                ->where('tukang_id', $tukang->id)
                ->whereIn('status', ['accepted', 'on_progress'])
                ->exists();

            // check for pending proposal
            $pendingProposal = Order::where('customer_id', $receiver->id)
                ->where('tukang_id', $tukang->id)
                ->where('status', 'pending')
                ->first();

            return view('tukang.chat', compact('receiver', 'messages', 'conversationId', 'receiverType', 'selectedService', 'hasActiveOrder', 'pendingProposal'));
        } catch (\Exception $e) {
            Log::error('Tukang chat show error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load chat');
        }
    }

    public function sendMessageFromTukang(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'receiver_id' => 'required|integer',
                'receiver_type' => 'required|in:customer,tukang',
            ]);

            $tukang = Auth::guard('tukang')->user();

            if (!$tukang) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $receiverClass = 'App\Models\\' . ucfirst($request->receiver_type);
            $receiver = $receiverClass::findOrFail($request->receiver_id);

            $conversationId = ChatMessage::generateConversationId(
                $tukang->id,
                'App\Models\Tukang',
                $receiver->id,
                $receiverClass
            );

            $message = ChatMessage::create([
                'conversation_id' => $conversationId,
                'sender_type' => 'App\Models\Tukang',
                'sender_id' => $tukang->id,
                'receiver_type' => $receiverClass,
                'receiver_id' => $receiver->id,
                'message' => $request->message,
            ]);

            $message->load(['sender', 'receiver']);

            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                Log::error('Tukang broadcasting error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Tukang send message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message'
            ], 500);
        }
    }

    public function getRecentMessagesForTukang()
    {
        try {
            $tukang = Auth::guard('tukang')->user();

            if (!$tukang) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $messages = ChatMessage::where('receiver_type', 'App\Models\Tukang')
                ->where('receiver_id', $tukang->id)
                ->with(['sender'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->groupBy('conversation_id')
                ->map(function ($conversationMessages) {
                    return $conversationMessages->first();
                })
                ->values();

            return response()->json($messages);
        } catch (\Exception $e) {
            Log::error('Error getting recent messages for tukang: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load messages'], 500);
        }
    }

    // Order-related methods
    public function sendOrderProposal(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'conversation_id' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'service_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'expires_in_hours' => 'required|integer|min:1|max:168',
            'service_details' => 'nullable|array',
            'work_datetime' => 'required|date|after:today',
            'additional_items' => 'nullable|array',
            'additional_items.*.item_name' => 'required_with:additional_items|string',
            'additional_items.*.item_price' => 'required_with:additional_items|numeric|min:0',
            'additional_items.*.quantity' => 'required_with:additional_items|integer|min:1',
            'custom_items' => 'nullable|array',
            'custom_items.*.item_name' => 'required_with:custom_items|string',
            'custom_items.*.item_price' => 'required_with:custom_items|numeric|min:0',
            'custom_items.*.quantity' => 'required_with:custom_items|integer|min:1',
            'custom_items.*.description' => 'nullable|string',
            'working_address' => 'required|string'
        ]);

        try {
            $tukangId = Auth::guard('tukang')->id();

            // Check for existing active order
            $activeOrderExists = Order::where('customer_id', $request->customer_id)
                ->where('tukang_id', $tukangId)
                ->whereIn('status', ['accepted', 'on_progress'])
                ->exists();

            if ($activeOrderExists) {
                return response()->json([
                    'success' => false,
                    'error' => 'You already have an active order with this customer. Please complete it before creating a new proposal.'
                ], 400);
            }

            // Check for existing pending proposal that is NOT expired
            $pendingProposalExists = Order::where('customer_id', $request->customer_id)
                ->where('tukang_id', $tukangId)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->exists();

            if ($pendingProposalExists) {
                return response()->json([
                    'success' => false,
                    'error' => 'You already have a valid pending proposal with this customer. Please wait for their response or cancel the existing proposal first.'
                ], 400);
            }

            // Check for scheduling conflicts with ANY customer at the same time
            // We check for orders that are accepted, on_progress, OR pending (and not expired)
            // matching the exact start time.
            $conflictingOrder = Order::where('tukang_id', $tukangId)
                ->where('work_datetime', $request->work_datetime)
                ->where(function($q) {
                    $q->whereIn('status', ['accepted', 'on_progress'])
                      ->orWhere(function($subQ) {
                          $subQ->where('status', 'pending')
                               ->where('expires_at', '>', now());
                      });
                })
                ->first();

            if ($conflictingOrder) {
                // Verify if it's the exact same time (Carbon instance comparison handled by DB query usually, 
                // but let's be descriptive in the error)
                $timeFormatted = \Carbon\Carbon::parse($request->work_datetime)->format('d M Y, H:i');
                return response()->json([
                    'success' => false,
                    'error' => "You already have a scheduled job or pending proposal at {$timeFormatted}. Please choose a different time."
                ], 400);
            }

            // Use working_address from request, or fallback to customer's address
            $workingAddress = $request->working_address;
            if (empty($workingAddress)) {
                $customer = Customer::findOrFail($request->customer_id);
                $workingAddress = $customer->address;
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $request->customer_id,
                'tukang_id' => $tukangId,
                'service_id' => $request->service_id,
                'conversation_id' => $request->conversation_id,
                'service_description' => $request->service_description,
                'price' => $request->price,
                'expires_at' => now()->addHours((int) $request->expires_in_hours),
                'work_datetime' => $request->work_datetime,
                'working_address' => $workingAddress,
                'service_details' => $request->service_details,
                'status' => 'pending'
            ]);

            // Save additional items if provided
            if ($request->has('additional_items') && is_array($request->additional_items)) {
                foreach ($request->additional_items as $item) {
                    $order->additionalItems()->create([
                        'item_name' => $item['item_name'],
                        'item_price' => $item['item_price'],
                        'quantity' => $item['quantity']
                    ]);
                }
            }

            // Save custom items if provided
            if ($request->has('custom_items') && is_array($request->custom_items)) {
                foreach ($request->custom_items as $item) {
                    $order->customItems()->create([
                        'item_name' => $item['item_name'],
                        'item_price' => $item['item_price'],
                        'quantity' => $item['quantity'],
                        'description' => $item['description'] ?? null
                    ]);
                }
            }

            ChatMessage::create([
                'conversation_id' => $request->conversation_id,
                'sender_type' => 'App\\Models\\Tukang',
                'sender_id' => $tukangId,
                'receiver_type' => 'App\\Models\\Customer',
                'receiver_id' => $request->customer_id,
                'message' => "Order proposal sent: #{$order->order_number}",
                'message_type' => 'order_proposal',
                'order_id' => $order->id
            ]);

            $order->load(['service', 'customer', 'tukang', 'additionalItems', 'customItems']);

            try {
                broadcast(new OrderProposalSent($order));
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast order proposal: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order proposal sent successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending order proposal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to send order proposal'
            ], 500);
        }
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'datetime' => 'required|date'
        ]);

        $tukangId = Auth::guard('tukang')->id();
        
        $conflictingOrder = Order::where('tukang_id', $tukangId)
            ->where('work_datetime', $request->datetime)
            ->where(function($q) {
                $q->whereIn('status', ['accepted', 'on_progress'])
                  ->orWhere(function($subQ) {
                      $subQ->where('status', 'pending')
                           ->where('expires_at', '>', now());
                  });
            })
            ->first();

        if ($conflictingOrder) {
            return response()->json([
                'available' => false,
                'message' => 'You already have a job scheduled for this time.'
            ]);
        }

        return response()->json([
            'available' => true
        ]);
    }
    
    public function getTukangServices()
    {
        try {
            $tukang = Auth::guard('tukang')->user();

            // First try to get tukang-specific services
            $tukangServices = TukangService::with('service')
                ->where('tukang_id', $tukang->id)
                ->get();

            if ($tukangServices->isNotEmpty()) {
                // Return tukang-specific services
                $services = $tukangServices->map(function ($tukangService) {
                    return [
                        'id' => $tukangService->service->id,
                        'name' => $tukangService->service->name,
                        'icon' => $tukangService->service->icon,
                        'color' => $tukangService->service->color,
                        'base_price' => $tukangService->service->base_price,
                        'custom_rate' => $tukangService->custom_rate,
                        'description' => $tukangService->description ?: $tukangService->service->description,
                    ];
                });
            } else {
                // Fallback to all available services
                $allServices = \App\Models\Service::where('is_active', true)->get();

                $services = $allServices->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'icon' => $service->icon,
                        'color' => $service->color,
                        'base_price' => $service->base_price,
                        'custom_rate' => null,
                        'description' => $service->description,
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tukang services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch services'
            ], 500);
        }
    }

    public function acceptOrder(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        if (!$order->canBeAccepted()) {
            $errorMsg = 'Order cannot be accepted (expired or already processed)';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMsg
                ], 400);
            }
            return redirect()->back()->with('error', $errorMsg);
        }

        try {
            $order->update([
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            broadcast(new OrderStatusUpdated($order));

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order accepted successfully',
                    'order' => $order->load(['service', 'tukang'])
                ]);
            }

            // Redirect for regular form submissions
            return redirect()->route('customer.orders.show', $order->uuid)
                ->with('success', 'Order accepted successfully! Please proceed with payment.');

        } catch (\Exception $e) {
            Log::error('Error accepting order: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to accept order'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to accept order. Please try again.');
        }
    }

    public function rejectOrder(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        if ($order->status !== 'pending') {
            $errorMsg = 'Order cannot be rejected';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMsg
                ], 400);
            }
            return redirect()->back()->with('error', $errorMsg);
        }

        try {
            $order->update(['status' => 'rejected']);

            broadcast(new OrderStatusUpdated($order));

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order rejected',
                    'order' => $order->load(['service', 'tukang'])
                ]);
            }

            // Redirect for regular form submissions
            return redirect()->route('customer.orders.index')
                ->with('success', 'Order proposal has been declined.');

        } catch (\Exception $e) {
            Log::error('Error rejecting order: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to reject order'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to reject order. Please try again.');
        }
    }

    public function cancelPendingProposal(Request $request, Order $order)
    {
        try {
            $tukang = Auth::guard('tukang')->user();

            if (!$tukang) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            // Verify the order belongs to this tukang
            if ($order->tukang_id !== $tukang->id) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            // Verify the order is still pending
            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'error' => 'Only pending proposals can be cancelled'
                ], 400);
            }

            // Update order status to rejected (cancelled by tukang)
            $order->update(['status' => 'rejected']);

            // Broadcast the status update
            try {
                broadcast(new OrderStatusUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast order cancellation: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Proposal cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling pending proposal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to cancel proposal'
            ], 500);
        }
    }
}
