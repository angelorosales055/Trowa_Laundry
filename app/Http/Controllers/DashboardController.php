<?php

namespace App\Http\Controllers;

use App\Actions\UpdateOrderStatusAction;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $ordersQuery = Order::query()->latest();

        if ($user->role === 'staff') {
            $ordersQuery->where('created_by', $user->id);
        }

        $orders = $ordersQuery->with(['customer:id,name', 'creator:id,name'])->get();
        $stats = [
            'pending' => $orders->whereIn('status', ['received', 'pending'])->count(),
            'in-progress' => $orders->whereIn('status', ['washing', 'in-progress'])->count(),
            'ready' => $orders->whereIn('status', ['ready_for_pickup', 'ready'])->count(),
        ];

        if ($user->role === 'staff') {
            return view('dashboards_staff', [
                'user' => $user,
                'orders' => $orders,
                'stats' => $stats,
                'services' => Service::query()->where('is_active', true)->orderBy('id')->get(),
                'customers' => Customer::query()->orderBy('name')->get(),
            ]);
        }

        return view('dashboards_admin', [
            'user' => $user,
            'orders' => $orders,
            'revenue' => $orders->whereIn('status', ['claimed', 'delivered'])->sum('total_price'),
            'team' => User::query()->select('id', 'name', 'role')->orderBy('name')->get(),
        ]);
    }

    public function reports(): View
    {
        $orders = Order::query()->with('customer:id,name')->latest()->get();

        return view('reports.index', [
            'orders' => $orders,
            'revenue' => $orders->whereIn('status', ['claimed', 'delivered'])->sum('total_price'),
            'serviceBreakdown' => $orders->flatMap(fn (Order $order): array => array_map('trim', explode(',', $order->services)))
                ->countBy()
                ->sortDesc(),
            'statusBreakdown' => $orders->countBy('status'),
            'topCustomers' => Customer::query()->withCount('orders')->orderByDesc('orders_count')->limit(5)->get(),
        ]);
    }

    public function billing(): View
    {
        $payments = Order::query()
            ->with('customer:id,name')
            ->whereIn('status', ['claimed', 'delivered'])
            ->latest()
            ->get();

        return view('billing.index', [
            'payments' => $payments,
            'totalCollected' => $payments->sum('total_price'),
        ]);
    }

    public function updateOrder(Request $request, UpdateOrderStatusAction $updateOrderStatus): RedirectResponse
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'in:pending,in-progress,ready,delivered,cancelled'],
        ]);

        $order = Order::findOrFail($data['id']);
        $user = Auth::user();

        abort_unless($user->role !== 'staff' || $order->created_by === $user->id, 403);

        $updateOrderStatus->handle($order, $data['status'], (int) $user->id);

        return back()->with('status', 'Order status updated.');
    }
}
