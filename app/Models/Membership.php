<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'is_active',
        'benefits',
        'max_halls',
        'max_bookings_per_month',
        'priority_support',
        'custom_branding',
        'advanced_analytics'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'benefits' => 'array',
        'priority_support' => 'boolean',
        'custom_branding' => 'boolean',
        'advanced_analytics' => 'boolean'
    ];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'membership_feature')
                    ->withPivot('settings', 'is_enabled')
                    ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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

    public function hasFeature(string $featureName): bool
    {
        return $this->features()->where('name', $featureName)->exists();
    }

    public function getFeatureSettings(string $featureName): ?array
    {
        $feature = $this->features()->where('name', $featureName)->first();
        return $feature ? $feature->pivot->settings : null;
    }
}
