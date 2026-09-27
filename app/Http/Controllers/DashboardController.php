<?php

namespace App\Http\Controllers;

use App\Actions\UpdateOrderStatusAction;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $user = Auth::user();
        $ordersQuery = Order::query()
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest();

        if ($user->role === 'staff') {
            $ordersQuery->where('created_by', $user->id);
        }

        $orders = $ordersQuery->with(['customer:id,name', 'creator:id,name'])->get();
        $graphTo = $to ?? now()->toDateString();
        $graphFrom = $from ?? ($to ? now()->parse($to)->subDays(6)->toDateString() : now()->subDays(6)->toDateString());
        $dailyTrend = collect(CarbonPeriod::create($graphFrom, $graphTo))->map(function ($date) use ($orders): array {
            $date = $date->toDateString();

            return [
                'date' => $date,
                'label' => now()->parse($date)->format('M j'),
                'count' => $orders->filter(
                    fn (Order $order): bool => $order->created_at?->toDateString() === $date
                )->count(),
            ];
        });
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
                'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(),
            ]);
        }

        return view('dashboards_admin', [
            'user' => $user,
            'orders' => $orders,
            'revenue' => $this->netRevenue($orders, $from, $to),
            'team' => User::query()->select('id', 'name', 'role')->orderBy('name')->get(),
            'orderStages' => $orders->countBy('status'),
            'unclaimedOrders' => $orders->whereIn('status', ['ready_for_pickup', 'ready'])->count(),
            'balancesDue' => $orders->whereNotIn('status', ['cancelled'])->sum(fn (Order $order): float => max(0, (float) $order->total_price - (float) $order->amount_paid)),
            'expensesTotal' => $this->expensesQuery($from, $to)->sum('amount'),
            'lowStockItems' => InventoryItem::query()->where('is_active', true)->whereColumn('quantity_on_hand', '<=', 'low_stock_threshold')->get(),
            'dailyTrend' => $dailyTrend,
            'paymentsByMethod' => $this->paymentsByMethod($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function reports(Request $request): View
    {
        [$from, $to] = $this->dateFilters($request);
        $orders = Order::query()
            ->with('customer:id,name')
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->get();
        $revenue = $this->netRevenue($orders, $from, $to);
        $expensesTotal = $this->expensesQuery($from, $to)->sum('amount');

        return view('reports.index', [
            'orders' => $orders,
            'revenue' => $revenue,
            'serviceBreakdown' => $orders->flatMap(fn (Order $order): array => array_map('trim', explode(',', $order->services)))
                ->countBy()
                ->sortDesc(),
            'statusBreakdown' => $orders->countBy('status'),
            'topCustomers' => Customer::query()
                ->withCount(['orders' => fn ($query) => $query
                    ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
                    ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))])
                ->orderByDesc('orders_count')
                ->limit(5)
                ->get(),
            'expensesTotal' => $expensesTotal,
            'netProfit' => $revenue - $expensesTotal,
            'paymentsByMethod' => $this->paymentsByMethod($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function printReports(Request $request): View
    {
        [$from, $to] = $this->dateFilters($request);
        $orders = Order::query()
            ->with('customer:id,name')
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->get();
        $revenue = $this->netRevenue($orders, $from, $to);
        $expenses = $this->expensesQuery($from, $to)->with('recordedBy')->latest('expense_date')->get();
        $expensesTotal = $expenses->sum('amount');

        return view('reports.print', [
            'orders' => $orders,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'expensesTotal' => $expensesTotal,
            'netProfit' => $revenue - $expensesTotal,
            'paymentsByMethod' => $this->paymentsByMethod($from, $to),
            'orderStages' => $orders->countBy('status'),
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * @return array<string, float>
     */
    private function paymentsByMethod(?string $from = null, ?string $to = null): array
    {
        $payments = Payment::query()
            ->whereIn('payment_status', ['paid', 'partially_paid', 'refunded'])
            ->when($from, fn ($query) => $query->whereDate('paid_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('paid_at', '<=', $to))
            ->get();
        $totals = [];

        foreach ($payments as $payment) {
            $amount = (float) $payment->amount * ($payment->payment_status === 'refunded' ? -1 : 1);
            $totals[$payment->payment_method] = ($totals[$payment->payment_method] ?? 0) + $amount;
        }

        return $totals;
    }

    private function netRevenue(Collection $orders, ?string $from = null, ?string $to = null): float
    {
        $completedOrders = $orders->whereIn('status', ['claimed', 'delivered']);
        $grossRevenue = (float) $completedOrders->sum('total_price');
        $refunds = Payment::query()
            ->whereIn('order_id', $completedOrders->modelKeys())
            ->where('payment_status', 'refunded')
            ->when($from, fn ($query) => $query->whereDate('paid_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('paid_at', '<=', $to))
            ->sum('amount');

        return $grossRevenue - (float) $refunds;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function dateFilters(Request $request): array
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return [$filters['from'] ?? null, $filters['to'] ?? null];
    }

    private function expensesQuery(?string $from, ?string $to): Builder
    {
        return Expense::query()
            ->when($from, fn ($query) => $query->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('expense_date', '<=', $to));
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
        $order = Order::findOrFail($request->input('id'));
        $user = Auth::user();
        abort_unless($user->role !== 'staff' || $order->created_by === $user->id, 403);

        $data = $request->validate([
            'id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'in:received,washing,drying,ironing,folding,ready_for_pickup,claimed,cancelled,pending,in-progress,ready,delivered'],
        ]);

        $canonicalStatus = [
            'pending' => 'received',
            'in-progress' => 'washing',
            'ready' => 'ready_for_pickup',
            'delivered' => 'claimed',
        ][$data['status']] ?? $data['status'];

        $updateOrderStatus->handle($order, $canonicalStatus, (int) $user->id);

        return back()->with('status', 'Order status updated.');
    }
}
