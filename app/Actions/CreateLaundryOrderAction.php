<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateLaundryOrderAction
{
    public function handle(array $data, int $staffId, ApplyInventoryConsumptionAction $inventoryConsumption): Order
    {
        return DB::transaction(function () use ($data, $staffId, $inventoryConsumption): Order {
            $weight = BigDecimal::of((string) $data['weight_kg']);
            [$wholeLoads, $remainder] = $weight->quotientAndRemainder(8);
            $loads = (int) $wholeLoads->plus($remainder->isZero() ? 0 : 1)->toInt();
            $services = Service::query()
                ->whereKey($data['service_ids'])
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            abort_unless($services->count() === count($data['service_ids']), 422, 'One or more selected services are unavailable.');

            $customer = isset($data['customer_id'])
                ? Customer::query()->where('is_active', true)->findOrFail($data['customer_id'])
                : Customer::query()->create([
                    'name' => $data['customer_name'],
                    'address' => $data['address'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'phone' => $data['contact_number'] ?? null,
                ]);

            $lineItems = $services->map(function (Service $service) use ($loads, $weight): array {
                $price = $service->price_per_load ?? $service->price;
                $subtotal = match ($service->pricing_type) {
                    'per_kg' => BigDecimal::of((string) $price)
                        ->multipliedBy($weight)
                        ->toScale(2, RoundingMode::HalfUp),
                    'flat_rate' => BigDecimal::of((string) $price)->toScale(2, RoundingMode::HalfUp),
                    default => BigDecimal::of((string) $price)
                        ->multipliedBy($loads)
                        ->toScale(2, RoundingMode::HalfUp),
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

            if (isset($data['tendered_amount'])) {
                $tendered = BigDecimal::of((string) $data['tendered_amount'])->toScale(2, RoundingMode::HalfUp);
                $amountPaid = $tendered->isGreaterThan($total) ? $total : $tendered;
                $change = $tendered->isGreaterThan($total) ? $tendered->minus($total) : BigDecimal::zero();
            } else {
                $amountPaid = BigDecimal::of((string) ($data['amount_paid'] ?? '0'))->toScale(2, RoundingMode::HalfUp);
                abort_unless($amountPaid->isLessThanOrEqualTo($total), 422, 'Amount paid cannot exceed the total due.');
                $change = BigDecimal::zero();
            }

            $paymentStatus = $amountPaid->isZero()
                ? 'unpaid'
                : ($amountPaid->isEqualTo($total) ? 'paid' : 'partially_paid');

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'created_by' => $staffId,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'weight_kg' => (string) $weight,
                'number_of_loads' => $loads,
                'services' => $services->pluck('name')->implode(', '),
                'total_price' => (string) $total,
                'amount_paid' => (string) $amountPaid,
                'change' => (string) $change,
                'payment_status' => $paymentStatus,
                'order_date' => now(),
                'status' => 'received',
            ]);

            $order->orderServices()->createMany($lineItems->all());
            $order->itemDetails()->createMany($data['item_details'] ?? []);
            $order->statusHistories()->create([
                'status' => 'received',
                'changed_by' => $staffId,
                'notes' => 'Order received.',
            ]);
            $inventoryConsumption->consume($order, $services->modelKeys(), $loads, $staffId, $data['inventory_items'] ?? []);

            if ($amountPaid->isPositive()) {
                $order->payments()->create([
                    'amount' => (string) $amountPaid,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_status' => $paymentStatus === 'paid' ? 'paid' : 'partially_paid',
                    'reference_number' => $data['reference_number'] ?? null,
                    'received_by' => $staffId,
                    'paid_at' => now(),
                ]);
            }

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'TL-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
