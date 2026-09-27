<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInventoryUsage extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'inventory_item_id', 'quantity_per_load'];

    protected function casts(): array
    {
        return ['quantity_per_load' => 'decimal:3'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
