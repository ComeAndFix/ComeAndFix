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
                // New service_type provided - look up and UPDATE conversation service
                $service = \App\Models\Service::where('name', 'LIKE', $request->service_type . '%')
                    ->where('is_active', true)
                    ->first();
                if ($service) {
                    $conversationServiceId = $service->id;
                    
                    // Update all existing messages in this conversation to the new service
                    ChatMessage::where('conversation_id', $conversationId)
                        ->update(['conversation_service_id' => $conversationServiceId]);
                }
            }
            
            // If no service from request, use existing conversation service
            if (!$conversationServiceId) {
                $existingMessage = ChatMessage::where('conversation_id', $conversationId)
                    ->whereNotNull('conversation_service_id')
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

            // Extract service context from query parameter or conversation
            $serviceType = request()->query('service_type');
            $selectedService = null;
            
            if ($serviceType) {
                // Use LIKE to match service names (e.g., "Plumbing" matches "Plumbing Services")
                $selectedService = \App\Models\Service::where('name', 'LIKE', $serviceType . '%')
                    ->where('is_active', true)
                    ->first();
            }
            
            // If no service from URL, check conversation's stored service
            if (!$selectedService) {
                $messageWithService = ChatMessage::where('conversation_id', $conversationId)
                    ->whereNotNull('conversation_service_id')
                    ->first();
                if ($messageWithService && $messageWithService->conversation_service_id) {
                    $selectedService = \App\Models\Service::find($messageWithService->conversation_service_id);
                }
            }

            return view('tukang.chat', compact('receiver', 'messages', 'conversationId', 'receiverType', 'selectedService'));
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
            'service_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'expires_in_hours' => 'required|integer|min:1|max:168',
            'service_details' => 'nullable|array',
            'work_datetime' => 'nullable|date',
            'additional_items' => 'nullable|array',
            'additional_items.*.item_name' => 'required_with:additional_items|string',
            'additional_items.*.item_price' => 'required_with:additional_items|numeric|min:0',
            'additional_items.*.quantity' => 'required_with:additional_items|integer|min:1',
            'custom_items' => 'nullable|array',
            'custom_items.*.item_name' => 'required_with:custom_items|string',
            'custom_items.*.item_price' => 'required_with:custom_items|numeric|min:0',
            'custom_items.*.quantity' => 'required_with:custom_items|integer|min:1',
            'custom_items.*.description' => 'nullable|string'
        ]);

        try {
            $tukangId = Auth::guard('tukang')->id();

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
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        if (!$order->canBeAccepted()) {
            return response()->json([
                'success' => false,
                'error' => 'Order cannot be accepted (expired or already processed)'
            ], 400);
        }

        try {
            $order->update([
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            broadcast(new OrderStatusUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully',
                'order' => $order->load(['service', 'tukang'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to accept order'
            ], 500);
        }
    }

    public function rejectOrder(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'error' => 'Order cannot be rejected'
            ], 400);
        }

        try {
            $order->update(['status' => 'rejected']);

            broadcast(new OrderStatusUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Order rejected',
                'order' => $order->load(['service', 'tukang'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to reject order'
            ], 500);
        }
    }
}
