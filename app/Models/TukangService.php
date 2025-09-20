<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TukangService extends Model
{
    use HasFactory;

    protected $fillable = [
        'tukang_id',
        'service_id',
        'custom_rate',
        'description'
    ];

    protected $casts = [
        'custom_rate' => 'decimal:2'
    ];

    public function tukang()
    {
        return $this->belongsTo(Tukang::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}