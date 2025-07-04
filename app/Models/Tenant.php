<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'domain',
        'db_name',
        'package_id',
        'status',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(TenantSetting::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function hasFeature(string $feature): bool
    {
        if (!$this->package) {
            return false;
        }

        $features = json_decode($this->package->features, true);
        return isset($features[$feature]) && $features[$feature];
    }

    public function getLimit(string $limit): int
    {
        if (!$this->package) {
            return 0;
        }

        $limits = json_decode($this->package->limits, true);
        return $limits[$limit] ?? 0;
    }

    public function canCreateHalls(): bool
    {
        $currentHalls = DB::connection($this->db_name)->table('halls')->count();
        return $currentHalls < $this->getLimit('halls');
    }

    public function canCreateBookings(): bool
    {
        $currentBookings = DB::connection($this->db_name)->table('bookings')->count();
        return $currentBookings < $this->getLimit('bookings');
    }
}
