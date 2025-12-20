<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCustomItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_name',
        'item_price',
        'quantity',
        'description'
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->item_price * $this->quantity;
    }
}
