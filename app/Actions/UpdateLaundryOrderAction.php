<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateLaundryOrderAction
{
    public function handle(Order $order, array $data, int $userId, ApplyInventoryConsumptionAction $inventoryConsumption): void
    {
        DB::transaction(function () use ($order, $data, $userId, $inventoryConsumption): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            $oldPaid = BigDecimal::of((string) $order->amount_paid);
            $weight = BigDecimal::of((string) $data['weight_kg']);
            [$wholeLoads, $remainder] = $weight->quotientAndRemainder(8);
            $loads = (int) $wholeLoads->plus($remainder->isZero() ? 0 : 1)->toInt();
            $services = Service::query()->whereKey($data['service_ids'])->get()->keyBy('id');
            if ($services->count() !== count($data['service_ids'])) {
                throw ValidationException::withMessages(['service_ids' => 'One or more selected services are unavailable.']);
            }

            $customer = Customer::query()->where('is_active', true)->findOrFail($data['customer_id']);
            $lineItems = $services->map(function (Service $service) use ($loads, $weight): array {
                $price = $service->price_per_load ?? $service->price;
                $subtotal = match ($service->pricing_type) {
                    'per_kg' => BigDecimal::of((string) $price)->multipliedBy($weight)->toScale(2, RoundingMode::HalfUp),
                    'flat_rate' => BigDecimal::of((string) $price)->toScale(2, RoundingMode::HalfUp),
                    default => BigDecimal::of((string) $price)->multipliedBy($loads)->toScale(2, RoundingMode::HalfUp),
                };

                return [
                    'service_id' => $service->id,
                    'loads' => $loads,
                    'price_per_load' => (string) $price,
                    'subtotal' => (string) $subtotal,
                ];
            });
            $total = $lineItems->reduce(
                fn (BigDecimal $sum, array $item): BigDecimal => $sum->plus($item['subtotal']),
                BigDecimal::zero()
            )->toScale(2, RoundingMode::HalfUp);

            if ($total->isLessThan($oldPaid)) {
                throw ValidationException::withMessages([
                    'service_ids' => 'The revised order total cannot be less than payments already recorded. Record a refund first.',
                ]);
            }

            $before = $this->snapshot($order);
            $inventoryConsumption->restorePreviousConsumption($order, $userId);

            $order->update([
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'weight_kg' => (string) $weight,
                'number_of_loads' => $loads,
                'services' => $services->pluck('name')->implode(', '),
                'total_price' => (string) $total,
            ]);

            $order->orderServices()->delete();
            $order->orderServices()->createMany($lineItems->all());
            $order->itemDetails()->delete();
            $order->itemDetails()->createMany($data['item_details'] ?? []);
            $inventoryConsumption->consume($order, $services->modelKeys(), $loads, $userId);

            $order->editHistories()->create([
                'changed_by' => $userId,
                'before' => $before,
                'after' => $this->snapshot($order->fresh(['orderServices.service', 'itemDetails'])),
                'reason' => $data['reason'],
            ]);
        });
    }

    private function snapshot(Order $order): array
    {
        $order->loadMissing(['orderServices.service', 'itemDetails']);

        return [
            'customer_id' => $order->customer_id,
            'customer_name' => $order->customer_name,
            'weight_kg' => $order->weight_kg,
            'number_of_loads' => $order->number_of_loads,
            'total_price' => $order->total_price,
            'services' => $order->orderServices->map(fn ($line): array => [
                'name' => $line->service?->name,
                'loads' => $line->loads,
                'price_per_load' => $line->price_per_load,
                'subtotal' => $line->subtotal,
            ])->all(),
            'items' => $order->itemDetails->map(fn ($item): array => [
                'name' => $item->item_name,
                'quantity' => $item->quantity,
            ])->all(),
        ];
    }
}
