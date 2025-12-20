<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'description',
        'working_duration',
        'photos',
        'submitted_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'submitted_at' => 'datetime',
        'working_duration' => 'integer'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
