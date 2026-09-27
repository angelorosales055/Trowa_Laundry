<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Payment $payment): void {
            if (blank($payment->reference_number)) {
                $payment->reference_number = 'PAY-'.now()->format('Ymd').'-'.Str::upper((string) Str::uuid());
            }
        });
    }

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_status',
        'reference_number',
        'received_by',
        'paid_at',
        'notes',
        'related_payment_id',
        'refunded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function originalPayment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'related_payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(self::class, 'related_payment_id')->where('payment_status', 'refunded');
    }
}
