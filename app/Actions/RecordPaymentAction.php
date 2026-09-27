<?php

namespace App\Actions;

use App\Models\Order;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordPaymentAction
{
    public function handle(Order $order, array $data, int $userId, SyncOrderPaymentSummaryAction $syncSummary): void
    {
        DB::transaction(function () use ($order, $data, $userId, $syncSummary): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            $total = BigDecimal::of((string) $order->total_price);
            $paid = $order->payments()->whereIn('payment_status', ['paid', 'partially_paid'])->get(['amount'])
                ->reduce(fn (BigDecimal $sum, $payment): BigDecimal => $sum->plus((string) $payment->amount), BigDecimal::zero());
            $refunded = $order->payments()->where('payment_status', 'refunded')->get(['amount'])
                ->reduce(fn (BigDecimal $sum, $payment): BigDecimal => $sum->plus((string) $payment->amount), BigDecimal::zero());
            $paid = $paid->minus($refunded);
            $remaining = $total->minus((string) $paid);
            $amount = BigDecimal::of((string) $data['amount'])->toScale(2, RoundingMode::HalfUp);

            if ($amount->isGreaterThan($remaining)) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot exceed the remaining balance of ₱'.number_format($remaining->toFloat(), 2).'.',
                ]);
            }

            $order->payments()->create([
                'amount' => (string) $amount,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
                'reference_number' => $data['reference_number'] ?? null,
                'received_by' => $userId,
                'paid_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            $syncSummary->handle($order);
        });
    }
}
