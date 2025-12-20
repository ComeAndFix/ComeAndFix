<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomerVerifyEmail;

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
}
