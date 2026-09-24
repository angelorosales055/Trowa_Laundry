<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'item_name', 'quantity'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
