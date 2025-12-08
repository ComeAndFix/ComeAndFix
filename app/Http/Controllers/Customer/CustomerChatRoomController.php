<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class CustomerChatRoomController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        // Get all conversations for this customer
        $conversations = ChatMessage::where(function($query) use ($customer) {
                $query->where('sender_type', 'App\Models\Customer')
                    ->where('sender_id', $customer->id);
            })
            ->orWhere(function($query) use ($customer) {
                $query->where('receiver_type', 'App\Models\Customer')
                    ->where('receiver_id', $customer->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by conversation partner (tukang)
        $chatRooms = $conversations->groupBy(function($message) use ($customer) {
            // Determine the other person in the conversation
            if ($message->sender_type === 'App\Models\Customer' && $message->sender_id === $customer->id) {
                return $message->receiver_type . '_' . $message->receiver_id;
            } else {
                return $message->sender_type . '_' . $message->sender_id;
            }
        })->map(function($messages) use ($customer) {
            $latestMessage = $messages->first();
            
            // Get the tukang (conversation partner)
            if ($latestMessage->sender_type === 'App\Models\Tukang') {
                $tukang = $latestMessage->sender;
            } else {
                $tukang = $latestMessage->receiver;
            }
            
            // Get service if exists
            $service = null;
            if ($latestMessage->conversation_service_id) {
                $service = \App\Models\Service::find($latestMessage->conversation_service_id);
            }
            
            // Count unread messages
            $unreadCount = $messages->where('receiver_type', 'App\Models\Customer')
                ->where('receiver_id', $customer->id)
                ->whereNull('read_at')
                ->count();
            
            return (object) [
                'tukang' => $tukang,
                'service' => $service,
                'latest_message' => $latestMessage,
                'unread_count' => $unreadCount,
                'created_at' => $latestMessage->created_at,
            ];
        })->sortByDesc('created_at')->values();

        return view('customer.chat-rooms.index', compact('chatRooms'));
    }
}
