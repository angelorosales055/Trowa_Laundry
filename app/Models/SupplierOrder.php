<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'supplier_id',
        'status',
        'total_cost',
        'recorded_by',
        'received_at',
        'items_payload',
        'notes',
        'expense_id',
    ];

    protected function casts(): array
    {
        return [
            'total_cost' => 'decimal:2',
            'received_at' => 'datetime',
            'items_payload' => 'array',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
