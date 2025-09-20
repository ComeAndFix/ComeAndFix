<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandymanService extends Model
{
    use HasFactory;

    protected $fillable = [
        'handyman_id',
        'service_id',
        'custom_rate',
        'description'
    ];

    protected $casts = [
        'custom_rate' => 'decimal:2'
    ];

    public function handyman()
    {
        return $this->belongsTo(Handyman::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
