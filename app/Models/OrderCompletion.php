<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCompletion extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'order_id',
        'description',
        'working_duration',
        'photos',
        'status',
        'rejection_reason',
        'submitted_at',
        'reviewed_at'
    ];

    protected $casts = [
        'photos' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'working_duration' => 'integer'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
