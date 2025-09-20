<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'handyman_id',
        'title',
        'description',
        'cost',
        'duration_days',
        'completed_at'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'completed_at' => 'date'
    ];

    public function handyman()
    {
        return $this->belongsTo(Handyman::class);
    }

    public function images()
    {
        return $this->hasMany(PortfolioImage::class)->orderBy('sort_order');
    }
}
