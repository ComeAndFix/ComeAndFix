<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\TukangVerifyEmail;

class Tukang extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'specializations',
        'description',
        'years_experience',
        'profile_image',
        'is_verified',
        'is_available',
        'is_active',
        'rating',
        'total_reviews',
        'is_active',
        'rating',
        'total_reviews',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'specializations' => 'array',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new TukangVerifyEmail);
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function getDistanceFrom($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $dLat = deg2rad($this->latitude - $latitude);
        $dLon = deg2rad($this->longitude - $longitude);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($latitude)) * cos(deg2rad($this->latitude)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    // Relationship with services through tukang_services table
    public function services()
    {
        return $this->belongsToMany(Service::class, 'tukang_services')
            ->withPivot('custom_rate', 'description')
            ->withTimestamps();
    }

    public function tukangServices()
    {
        return $this->hasMany(TukangService::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function updateAverageRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $totalReviews = $this->reviews()->count();

        $this->update([
            'rating' => $avgRating ? round($avgRating, 2) : null,
            'total_reviews' => $totalReviews,
        ]);
    }
}
