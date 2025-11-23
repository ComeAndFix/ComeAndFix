<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderProposalSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['customer', 'tukang', 'service']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat.' . $this->order->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'service' => [
                    'id' => $this->order->service->id,
                    'name' => $this->order->service->name,
                    'icon' => $this->order->service->icon,
                    'color' => $this->order->service->color,
                ],
                'service_description' => $this->order->service_description,
                'price' => $this->order->price,
                'status' => $this->order->status,
                'expires_at' => $this->order->expires_at->timestamp * 1000,
                'service_details' => $this->order->service_details,
                'tukang' => [
                    'id' => $this->order->tukang->id,
                    'name' => $this->order->tukang->name,
                ],
                'customer' => [
                    'id' => $this->order->customer->id,
                    'name' => $this->order->customer->name,
                ]
            ]
        ];
    }
}
