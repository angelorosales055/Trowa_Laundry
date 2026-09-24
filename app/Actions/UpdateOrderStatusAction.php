<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusAction
{
    public function handle(Order $order, string $status, int $userId, ?string $notes = null): void
    {
        DB::transaction(function () use ($order, $status, $userId, $notes): void {
            if ($order->status === $status && blank($notes)) {
                return;
            }

            $order->update(['status' => $status]);
            $order->statusHistories()->create([
                'status' => $status,
                'changed_by' => $userId,
                'notes' => $notes,
            ]);
        });
    }
}
