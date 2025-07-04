<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'is_active',
        'default_settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_settings' => 'array'
    ];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_features')
                    ->withPivot('settings', 'is_enabled')
                    ->withTimestamps();
    }

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'membership_feature')
                    ->withPivot('settings', 'is_enabled')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBasic($query)
    {
        return $query->where('category', 'basic');
    }

    public function scopeAdvanced($query)
    {
        return $query->where('category', 'advanced');
    }

    public function scopePremium($query)
    {
        return $query->where('category', 'premium');
    }

    public function getCategoryTextAttribute(): string
    {
        return match($this->category) {
            'basic' => 'أساسي',
            'advanced' => 'متقدم',
            'premium' => 'مميز',
            default => 'غير محدد'
        };
    }
}
