<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'base_price',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2'
    ];

    public function handymen()
    {
        return $this->belongsToMany(Handyman::class, 'handyman_services')
            ->withPivot('custom_rate', 'description')
            ->withTimestamps();
    }

    public function handymanServices()
    {
        return $this->hasMany(HandymanService::class);
    }
}
