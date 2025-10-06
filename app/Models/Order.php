<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'tukang_id',
        'service_id',
        'conversation_id',
        'service_description',
        'price',
        'status',
        'expires_at',
        'accepted_at',
        'service_details'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'price' => 'decimal:2',
        'service_details' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tukang()
    {
        return $this->belongsTo(Tukang::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function tukangService()
    {
        return $this->hasOneThrough(
            TukangService::class,
            Service::class,
            'id', // Foreign key on services table
            'service_id', // Foreign key on tukang_services table
            'service_id', // Local key on orders table
            'id' // Local key on services table
        )->where('tukang_services.tukang_id', $this->tukang_id);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function canBeAccepted()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function getServiceTitleAttribute()
    {
        return $this->service->name ?? 'Custom Service';
    }
}
