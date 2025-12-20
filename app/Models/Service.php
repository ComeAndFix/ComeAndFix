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

    // Replace handymen with tukangs
    public function tukangs()
    {
        return $this->belongsToMany(Tukang::class, 'tukang_services')
            ->withPivot('custom_rate', 'description')
            ->withTimestamps();
    }

    public function tukangServices()
    {
        return $this->hasMany(TukangService::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
