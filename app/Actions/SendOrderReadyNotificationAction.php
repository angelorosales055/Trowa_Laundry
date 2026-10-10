<?php

namespace App\Actions;

use App\Mail\OrderReadyForClaimMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderReadyNotificationAction
{
    public function handle(Order $order): bool
    {
        $order->loadMissing('customer.user');

        $email = $order->customer?->email
            ?? $order->customer?->user?->email
            ?? User::where('customer_id', $order->customer_id)->value('email')
            ?? User::where('id', $order->created_by)->where('role', 'customer')->value('email');

        if (! $email) {
            Log::info("No email address found for customer on Order #{$order->order_number}. Notification skipped.");
            return false;
        }

        try {
            Mail::to($email)->send(new OrderReadyForClaimMail($order));
            Log::info("Ready for claim notification successfully dispatched to {$email} for Order #{$order->order_number}.");
            return true;
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch ready notification to {$email} for Order #{$order->order_number}: " . $e->getMessage());
            return false;
        }
    }
}
