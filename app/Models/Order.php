<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_ON_PROGRESS = 'on_progress';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

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
        'service_details',
        'payment_status'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'price' => 'decimal:2',
        'service_details' => 'array',
        'payment_status' => 'string'
    ];

    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_ON_PROGRESS => 'info',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_COMPLETED => 'primary',
            default => 'secondary'
        };
    }
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
            'id',
            'service_id',
            'service_id',
            'id'
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

    public function completion()
    {
        return $this->hasOne(OrderCompletion::class);
    }

    public function hasCompletionProof()
    {
        return $this->completion()->exists();
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function hasReview()
    {
        return $this->review()->exists();
    }
}
