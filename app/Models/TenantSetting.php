<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSetting extends Model
{
    protected $fillable = [
        'timezone',
        'currency',
        'branding',
        'notification_settings',
        'business_hours',
    ];

    protected $casts = [
        'branding' => 'array',
        'notification_settings' => 'array',
        'business_hours' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
