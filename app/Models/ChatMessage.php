<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'conversation_service_id',
        'sender_type',
        'sender_id',
        'receiver_type',
        'receiver_id',
        'message',
        'message_type',
        'order_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function conversationService()
    {
        return $this->belongsTo(Service::class, 'conversation_service_id');
    }

    public static function generateConversationId($senderId, $senderType, $receiverId, $receiverType)
    {
        $participants = collect([
            $senderType . '_' . $senderId,
            $receiverType . '_' . $receiverId
        ])->sort()->implode('_');

        return hash('sha256', $participants);
    }
}
