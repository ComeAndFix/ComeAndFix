<?php

namespace App\Http\Controllers\Tukang;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TukangChatRoomController extends Controller
{
    public function index()
    {
        $tukang = Auth::guard('tukang')->user();

        // Get all unique customer IDs that have messaged this tukang or received messages from this tukang
        $customerIds = ChatMessage::where(function($query) use ($tukang) {
                // Messages sent to tukang from customers
                $query->where('receiver_id', $tukang->id)
                      ->where('receiver_type', 'App\Models\Tukang')
                      ->where('sender_type', 'App\Models\Customer');
            })
            ->orWhere(function($query) use ($tukang) {
                // Messages sent by tukang to customers
                $query->where('sender_id', $tukang->id)
                      ->where('sender_type', 'App\Models\Tukang')
                      ->where('receiver_type', 'App\Models\Customer');
            })
            ->pluck('sender_id')
            ->merge(
                ChatMessage::where('sender_id', $tukang->id)
                    ->where('sender_type', 'App\Models\Tukang')
                    ->where('receiver_type', 'App\Models\Customer')
                    ->pluck('receiver_id')
            )
            ->unique()
            ->values();

        // Build chat rooms for each customer
        $chatRooms = collect();

        foreach ($customerIds as $customerId) {
            $customer = \App\Models\Customer::find($customerId);
            
            if (!$customer) {
                continue;
            }

            // Get last message in this conversation
            $lastMessage = ChatMessage::where(function($q) use ($tukang, $customerId) {
                    $q->where('sender_id', $tukang->id)
                      ->where('sender_type', 'App\Models\Tukang')
                      ->where('receiver_id', $customerId)
                      ->where('receiver_type', 'App\Models\Customer');
                })
                ->orWhere(function($q) use ($tukang, $customerId) {
                    $q->where('sender_id', $customerId)
                      ->where('sender_type', 'App\Models\Customer')
                      ->where('receiver_id', $tukang->id)
                      ->where('receiver_type', 'App\Models\Tukang');
                })
                ->orderBy('created_at', 'desc')
                ->first();

            // Get unread count
            $unreadCount = ChatMessage::where('sender_id', $customerId)
                ->where('sender_type', 'App\Models\Customer')
                ->where('receiver_id', $tukang->id)
                ->where('receiver_type', 'App\Models\Tukang')
                ->whereNull('read_at')
                ->count();

            $chatRooms->push((object) [
                'contact' => $customer,
                'contact_type' => 'customer',
                'contact_id' => $customerId,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ]);
        }

        // Sort by last message time (newest first)
        $chatRooms = $chatRooms->sortByDesc(function($room) {
            return $room->last_message ? $room->last_message->created_at : null;
        })->values();

        return view('tukang.chat-rooms.index', compact('chatRooms'));
    }
}
