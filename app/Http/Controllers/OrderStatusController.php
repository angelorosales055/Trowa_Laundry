<?php

namespace App\Http\Controllers;

use App\Actions\UpdateOrderStatusAction;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function update(Request $request, Order $order, UpdateOrderStatusAction $updateOrderStatus): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:received,washing,drying,folding,ready_for_pickup,claimed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        abort_unless($user->role === 'admin' || $order->created_by === $user->id, 403);
        $updateOrderStatus->handle($order, $data['status'], (int) $user->id, $data['notes'] ?? null);

        return back()->with('status', 'Order status updated.');
    }
}
