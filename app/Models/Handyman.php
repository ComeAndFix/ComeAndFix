<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handyman extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'bio',
        'phone',
        'experience_years',
        'rating',
        'total_reviews',
        'address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'profile_image',
        'is_verified',
        'is_available'
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_verified' => 'boolean',
        'is_available' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'handyman_services')
            ->withPivot('custom_rate', 'description')
            ->withTimestamps();
    }

    public function handymanServices()
    {
        return $this->hasMany(HandymanService::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function getDistanceFrom($latitude, $longitude)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($this->latitude - $latitude);
        $dLon = deg2rad($this->longitude - $longitude);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($latitude)) * cos(deg2rad($this->latitude)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }
}
