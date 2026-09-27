<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEditHistory extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'changed_by', 'before', 'after', 'reason'];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
