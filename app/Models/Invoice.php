<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id',
        'invoice_number',
        'amount',
        'paid_amount',
        'due_date',
        'status',
        'pdf_path',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }
}
