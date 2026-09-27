<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Payment;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundPaymentAction
{
    public function handle(Order $order, Payment $payment, array $data, int $userId, SyncOrderPaymentSummaryAction $syncSummary): void
    {
        DB::transaction(function () use ($order, $payment, $data, $userId, $syncSummary): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            $payment = Payment::query()->lockForUpdate()->where('order_id', $order->id)->findOrFail($payment->id);
            if (! in_array($payment->payment_status, ['paid', 'partially_paid'], true)) {
                throw ValidationException::withMessages(['amount' => 'Only settled payments can be refunded.']);
            }

            $alreadyRefunded = $payment->refunds()->get(['amount'])->reduce(
                fn (BigDecimal $sum, Payment $refund): BigDecimal => $sum->plus((string) $refund->amount),
                BigDecimal::zero()
            );
            $refundable = BigDecimal::of((string) $payment->amount)->minus($alreadyRefunded);
            $amount = BigDecimal::of((string) $data['amount'])->toScale(2, RoundingMode::HalfUp);
            if ($amount->isGreaterThan($refundable)) {
                throw ValidationException::withMessages([
                    'amount' => 'Refund cannot exceed the unrefunded amount of ₱'.number_format($refundable->toFloat(), 2).'.',
                ]);
            }

            $order->payments()->create([
                'amount' => (string) $amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => 'refunded',
                'reference_number' => $data['reference_number'] ?? null,
                'received_by' => $userId,
                'refunded_by' => $userId,
                'related_payment_id' => $payment->id,
                'paid_at' => now(),
                'notes' => $data['notes'],
            ]);

            $syncSummary->handle($order);
        });
    }
}
