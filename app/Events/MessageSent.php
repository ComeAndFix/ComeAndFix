<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load(['sender', 'receiver', 'conversationService']);
    }

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('chat.' . $this->message->conversation_id),
        ];

        if (str_contains($this->message->receiver_type, 'Tukang')) {
            $channels[] = new PrivateChannel('tukang.' . $this->message->receiver_id);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'message' => $this->message->message,
                'sender_id' => $this->message->sender_id,
                'sender_type' => $this->message->sender_type,
                'receiver_id' => $this->message->receiver_id,
                'receiver_type' => $this->message->receiver_type,
                'created_at' => $this->message->created_at->timestamp * 1000,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'type' => $this->message->sender_type,
                    'city' => $this->message->sender->city ?? null,
                ],
                'receiver' => [
                    'id' => $this->message->receiver->id,
                    'name' => $this->message->receiver->name,
                    'type' => $this->message->receiver_type,
                ],
                'message_type' => $this->message->message_type,
                'image_url' => $this->message->image_url,
                'service' => $this->message->conversationService ? [
                    'id' => $this->message->conversationService->id,
                    'name' => $this->message->conversationService->name,
                ] : null,
            ]
        ];
    }
}
