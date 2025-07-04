<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'package_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'amount_paid',
        'auto_renew',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'auto_renew' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'expired' => 'منتهي الصلاحية',
            'cancelled' => 'ملغي',
            'pending' => 'في الانتظار',
            default => 'غير محدد'
        };
    }

    public function getPaymentStatusTextAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'مدفوع',
            'pending' => 'في الانتظار',
            'failed' => 'فشل',
            'refunded' => 'مسترد',
            default => 'غير محدد'
        };
    }
}
