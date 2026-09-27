<?php

namespace App\Http\Controllers;

use App\Actions\UpdateOrderStatusAction;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $user = $request->user();
        $status = strtolower($request->string('status')->toString());
        $statusAliases = [
            'pending' => 'received',
            'in-progress' => 'washing',
            'ready' => 'ready_for_pickup',
            'delivered' => 'claimed',
        ];
        $status = $statusAliases[$status] ?? $status;
        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->with(['customer:id,name,phone,contact_number', 'orderServices.service'])
            ->when($user->role === 'staff', fn ($query) => $query->where('created_by', $user->id))
            ->when($status !== '', function ($query) use ($status): void {
                $legacyStatuses = match ($status) {
                    'received' => ['received', 'pending'],
                    'washing' => ['washing', 'in-progress'],
                    'ready_for_pickup' => ['ready_for_pickup', 'ready'],
                    'claimed' => ['claimed', 'delivered'],
                    default => [$status],
                };
                $query->whereIn(DB::raw('LOWER(status)'), $legacyStatuses);
            })
            ->when(isset($filters['date']), fn ($query) => $query->whereDate('created_at', $filters['date']))
            ->when(! isset($filters['date']) && isset($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when(! isset($filters['date']) && isset($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('schedule.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'module' => 'status',
            'customers' => collect(),
            'services' => collect(),
        ]);
    }

    public function update(Request $request, Order $order, UpdateOrderStatusAction $updateOrderStatus): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:received,washing,drying,ironing,folding,ready_for_pickup,claimed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        abort_unless($user->role === 'admin' || $order->created_by === $user->id, 403);
        $updateOrderStatus->handle($order, $data['status'], (int) $user->id, $data['notes'] ?? null);

        return redirect()
            ->route('schedule.index', ['status' => $data['status']])
            ->with('status', 'Order status updated.');
    }
}
