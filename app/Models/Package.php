<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'is_active',
        'max_halls',
        'max_bookings_per_month',
        'priority_support',
        'custom_branding',
        'advanced_analytics',
        'features'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'features' => 'array',
        'priority_support' => 'boolean',
        'custom_branding' => 'boolean',
        'advanced_analytics' => 'boolean'
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'package_features')
                    ->withPivot('settings', 'is_enabled')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ريال';
    }

    public function getDurationTextAttribute()
    {
        if ($this->duration_days == 30) {
            return 'شهري';
        } elseif ($this->duration_days == 365) {
            return 'سنوي';
        } else {
            return $this->duration_days . ' يوم';
        }
    }

    public function hasFeature(string $feature): bool
    {
        return $this->features[$feature] ?? false;
    }

    public function getLimit(string $limit): int
    {
        return $this->limits[$limit] ?? 0;
    }
}
