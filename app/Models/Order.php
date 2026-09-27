<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'order_number',
        'customer_id',
        'customer_name',
        'weight_kg',
        'number_of_loads',
        'services',
        'total_price',
        'amount_paid',
        'change',
        'payment_status',
        'order_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change' => 'decimal:2',
            'order_date' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderServices(): HasMany
    {
        return $this->hasMany(OrderService::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('paid_at');
    }

    public function itemDetails(): HasMany
    {
        return $this->hasMany(OrderItemDetail::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function editHistories(): HasMany
    {
        return $this->hasMany(OrderEditHistory::class)->latest();
    }

    public function nextStatuses(): array
    {
        if ($this->status === 'cancelled' || $this->status === 'claimed') {
            return [];
        }

        $next = match ($this->status) {
            'received', 'pending' => ['washing'],
            'washing', 'in-progress' => $this->nextProcessingStatusAfterWashing(),
            'drying' => $this->nextProcessingStatusAfterDrying(),
            'ironing' => $this->requiresFolding() ? ['folding'] : ['ready_for_pickup'],
            'folding' => ['ready_for_pickup'],
            'ready_for_pickup', 'ready' => ['claimed'],
            default => [],
        };

        if (! in_array($this->status, ['claimed', 'cancelled'], true)) {
            $next[] = 'cancelled';
        }

        return $next;
    }

    private function requiresDrying(): bool
    {
        return $this->serviceNames()->contains(fn (string $name): bool => str_contains(strtolower($name), 'dry'));
    }

    private function requiresIroning(): bool
    {
        return $this->serviceNames()->contains(fn (string $name): bool => str_contains(strtolower($name), 'iron'));
    }

    private function nextProcessingStatusAfterWashing(): array
    {
        if ($this->requiresDrying()) {
            return ['drying'];
        }

        return $this->nextProcessingStatusAfterDrying();
    }

    private function nextProcessingStatusAfterDrying(): array
    {
        if ($this->requiresIroning()) {
            return ['ironing'];
        }

        return $this->requiresFolding() ? ['folding'] : ['ready_for_pickup'];
    }

    private function requiresFolding(): bool
    {
        return $this->serviceNames()->contains(fn (string $name): bool => str_contains(strtolower($name), 'fold'));
    }

    /**
     * @return Collection<int, string>
     */
    private function serviceNames(): Collection
    {
        $this->loadMissing('orderServices.service');
        $serviceNames = $this->orderServices->pluck('service.name')->filter();

        if ($serviceNames->isEmpty()) {
            $serviceNames = collect(explode(',', (string) $this->services));
        }

        return $serviceNames->map(fn (string $name): string => trim($name))->filter();
    }
}
