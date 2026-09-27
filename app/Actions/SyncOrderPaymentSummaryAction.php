<?php

namespace App\Actions;

use App\Models\Order;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class SyncOrderPaymentSummaryAction
{
    public function handle(Order $order): void
    {
        $payments = $order->payments()->get(['amount', 'payment_status']);
        $paid = BigDecimal::zero();
        $refunded = BigDecimal::zero();

        foreach ($payments as $payment) {
            if (in_array($payment->payment_status, ['paid', 'partially_paid'], true)) {
                $paid = $paid->plus((string) $payment->amount);
            } elseif ($payment->payment_status === 'refunded') {
                $refunded = $refunded->plus((string) $payment->amount);
            }
        }

        $netPaid = $paid->minus($refunded)->toScale(2, RoundingMode::HalfUp);
        $total = BigDecimal::of((string) $order->total_price);
        $status = match (true) {
            $netPaid->isZero() => 'unpaid',
            $netPaid->isGreaterThanOrEqualTo($total) => 'paid',
            default => 'partially_paid',
        };

        $order->update([
            'amount_paid' => (string) $netPaid,
            'change' => '0.00',
            'payment_status' => $status,
        ]);
    }
}
