<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'price_per_hour',
        'price_per_day',
        'address',
        'photos',
        'amenities',
        'is_available',
        'image',
    ];

    protected $casts = [
        'photos' => 'array',
        'amenities' => 'array',
        'is_available' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(HallService::class);
    }
}
