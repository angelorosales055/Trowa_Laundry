<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateOrderStatusAction
{
    public function handle(Order $order, string $status, int $userId, ?string $notes = null): void
    {
        DB::transaction(function () use ($order, $status, $userId, $notes): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($order->status === $status && blank($notes)) {
                return;
            }

            if (! in_array($status, $order->nextStatuses(), true)) {
                throw ValidationException::withMessages([
                    'status' => 'This status transition is not allowed. Select one of the available next stages.',
                ]);
            }

            $order->update(['status' => $status]);
            $order->statusHistories()->create([
                'status' => $status,
                'changed_by' => $userId,
                'notes' => $notes,
            ]);

            if (in_array($status, ['ready_for_pickup', 'claimed'], true) && ! $order->ready_notified_at) {
                app(SendOrderReadyNotificationAction::class)->handle($order);
                $order->update(['ready_notified_at' => now()]);
            }
        });
    }
}
