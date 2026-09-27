<?php

namespace App\Actions;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\ServiceInventoryUsage;
use Brick\Math\BigDecimal;
use Illuminate\Validation\ValidationException;

class ApplyInventoryConsumptionAction
{
    public function consume(Order $order, array $serviceIds, int $loads, int $userId): void
    {
        $usageRows = ServiceInventoryUsage::query()
            ->whereIn('service_id', $serviceIds)
            ->get(['inventory_item_id', 'quantity_per_load']);

        $quantities = [];
        foreach ($usageRows as $usage) {
            $consumption = BigDecimal::of((string) $usage->quantity_per_load)->multipliedBy($loads);
            $quantities[$usage->inventory_item_id] = ($quantities[$usage->inventory_item_id] ?? BigDecimal::zero())->plus($consumption);
        }

        foreach ($quantities as $itemId => $quantity) {
            $item = InventoryItem::query()->lockForUpdate()->findOrFail($itemId);
            $remaining = BigDecimal::of((string) $item->quantity_on_hand)->minus($quantity);
            if ($remaining->isNegative()) {
                throw ValidationException::withMessages([
                    'service_ids' => "Insufficient {$item->name} stock to create this order.",
                ]);
            }

            $item->update(['quantity_on_hand' => (string) $remaining]);
            $item->movements()->create([
                'order_id' => $order->id,
                'recorded_by' => $userId,
                'movement_type' => 'usage',
                'quantity_change' => (string) $quantity->negated(),
                'notes' => "Consumed for order {$order->order_number}.",
            ]);
        }
    }

    public function restorePreviousConsumption(Order $order, int $userId): void
    {
        $movements = $order->inventoryMovements()->get();
        $quantities = [];

        foreach ($movements as $movement) {
            $quantities[$movement->inventory_item_id] = ($quantities[$movement->inventory_item_id] ?? BigDecimal::zero())
                ->plus((string) $movement->quantity_change);
        }

        foreach ($quantities as $itemId => $netChange) {
            if (! $netChange->isNegative()) {
                continue;
            }

            $item = InventoryItem::query()->lockForUpdate()->findOrFail($itemId);
            $quantity = $netChange->abs();
            $item->update([
                'quantity_on_hand' => (string) BigDecimal::of((string) $item->quantity_on_hand)->plus($quantity),
            ]);
            $item->movements()->create([
                'order_id' => $order->id,
                'recorded_by' => $userId,
                'movement_type' => 'usage_reversal',
                'quantity_change' => (string) $quantity,
                'notes' => "Reversed previous service usage while editing order {$order->order_number}.",
            ]);
        }
    }
}
