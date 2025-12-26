<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomerVerifyEmail;
use Illuminate\Support\Facades\Storage;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'password',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomerVerifyEmail);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            if (filter_var($this->profile_image, FILTER_VALIDATE_URL)) {
                return $this->profile_image;
            }
            
            $azureUrl = config('filesystems.disks.azure.url');
            if ($azureUrl) {
                return rtrim($azureUrl, '/') . '/' . ltrim($this->profile_image, '/');
            }

            return Storage::disk('azure')->url($this->profile_image);
        }
        return null;
    }

    public function getInitialsAttribute()
    {
        $name = $this->name;
        $words = explode(' ', $name);
        $initials = '';
        
        if (count($words) >= 1) {
            $initials .= strtoupper(substr($words[0], 0, 1));
        }
        
        if (count($words) > 1) {
            $initials .= strtoupper(substr(end($words), 0, 1));
        }
        
        return $initials;
    }
}
