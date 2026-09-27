<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'quantity_on_hand', 'low_stock_threshold', 'is_active'];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'decimal:3',
            'low_stock_threshold' => 'decimal:3',
            'is_active' => 'boolean',
        ];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class)->latest();
    }
}
