<?php

namespace App\Http\Controllers;

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
            'pending' => $orders->where('status', 'pending')->count(),
            'in-progress' => $orders->where('status', 'in-progress')->count(),
            'ready' => $orders->where('status', 'ready')->count(),
        ];

        if ($user->role === 'staff') {
            return view('dashboards_staff', [
                'user' => $user,
                'orders' => $orders,
                'stats' => $stats,
                'services' => Service::query()->orderBy('id')->get(),
                'customers' => Customer::query()->orderBy('name')->get(),
            ]);
        }

        if ($user->role === 'manager') {
            $staffPerf = Order::query()
                ->selectRaw('created_by, count(*) as completed')
                ->where('status', 'delivered')
                ->groupBy('created_by')
                ->with('creator:id,name')
                ->get()
                ->map(fn (Order $order): array => [
                    'name' => $order->creator?->name ?? 'Unknown',
                    'completed' => $order->completed,
                    'avgTime' => '—',
                ])
                ->all();

            return view('dashboards_manager', [
                'user' => $user,
                'recentOrders' => $orders->take(10),
                'staffPerf' => $staffPerf,
            ]);
        }

        return view('dashboards_admin', [
            'user' => $user,
            'orders' => $orders,
            'revenue' => $orders->where('status', 'delivered')->sum('total_price'),
            'team' => User::query()->select('id', 'name', 'role')->orderBy('name')->get(),
        ]);
    }

    public function reports(): View
    {
        $orders = Order::query()->with('customer:id,name')->latest()->get();

        return view('reports.index', [
            'orders' => $orders,
            'revenue' => $orders->where('status', 'delivered')->sum('total_price'),
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
            ->where('status', 'delivered')
            ->latest()
            ->get();

        return view('billing.index', [
            'payments' => $payments,
            'totalCollected' => $payments->sum('total_price'),
        ]);
    }

    public function updateOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'in:pending,in-progress,ready,delivered,cancelled'],
        ]);

        $order = Order::findOrFail($data['id']);
        $user = Auth::user();

        abort_unless($user->role !== 'staff' || $order->created_by === $user->id, 403);

        $order->update(['status' => $data['status']]);

        return back()->with('status', 'Order status updated.');
    }

    public function addOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ]);

        $services = Service::query()->whereKey($data['service_ids'])->get();
        $weight = (float) ($data['weight'] ?? 0);
        abort_unless($services->where('pricing_type', 'per_kg')->isEmpty() || $weight > 0, 422, 'Weight is required for per-kg services.');
        $total = $services->sum(fn (Service $service): float => $service->pricing_type === 'per_kg'
            ? (float) $service->price * $weight
            : (float) $service->price);

        Order::create([
            'created_by' => Auth::id(),
            'customer_id' => $data['customer_id'],
            'customer_name' => Customer::findOrFail($data['customer_id'])->name,
            'weight_kg' => $data['weight'] ?? null,
            'services' => $services->pluck('name')->implode(', '),
            'total_price' => $total,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Order added to the queue.');
    }
}
