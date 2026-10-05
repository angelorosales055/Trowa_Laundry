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
            'preset' => ['nullable', 'string', 'in:today,7d,30d,month,all'],
        ]);
        $preset = $filters['preset'] ?? null;
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        if ($preset) {
            match ($preset) {
                'today' => [$from = now()->toDateString(), $to = now()->toDateString()],
                '7d' => [$from = now()->subDays(6)->toDateString(), $to = now()->toDateString()],
                '30d' => [$from = now()->subDays(29)->toDateString(), $to = now()->toDateString()],
                'month' => [$from = now()->startOfMonth()->toDateString(), $to = now()->toDateString()],
                'all' => [$from = null, $to = null],
            };
        }

        $user = Auth::user();
        $ordersQuery = Order::query()
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest();

        if ($user->role === 'staff') {
            $ordersQuery->with([
                'customer:id,name,phone,contact_number,address',
                'creator:id,name',
                'orderServices.service',
                'payments',
                'itemDetails',
                'statusHistories',
            ]);
        }

        $orders = $ordersQuery->get();
        $graphTo = $to ?? now()->toDateString();
        $graphFrom = $from ?? ($to ? now()->parse($to)->subDays(6)->toDateString() : now()->subDays(6)->toDateString());
        $dailyTrend = collect(CarbonPeriod::create($graphFrom, $graphTo))->map(function ($date) use ($orders): array {
            $date = $date->toDateString();
            $dayOrders = $orders->filter(
                fn (Order $order): bool => $order->created_at?->toDateString() === $date
            );

            return [
                'date' => $date,
                'label' => now()->parse($date)->format('M j'),
                'count' => $dayOrders->count(),
                'revenue' => (float) $dayOrders->whereIn('status', ['claimed', 'delivered'])->sum('total_price'),
                'kg' => (float) $dayOrders->sum('weight_kg'),
            ];
        });
        $stats = [
            'pending' => $orders->whereIn('status', ['received', 'pending'])->count(),
            'in-progress' => $orders->whereIn('status', ['washing', 'in-progress'])->count(),
            'ready' => $orders->whereIn('status', ['ready_for_pickup', 'ready'])->count(),
            'washing' => $orders->whereIn('status', ['washing', 'in-progress'])->count(),
            'drying' => $orders->where('status', 'drying')->count(),
            'folding' => $orders->whereIn('status', ['folding', 'ironing'])->count(),
            'claimed' => $orders->whereIn('status', ['claimed', 'delivered'])->count(),
            'total_kg' => (float) $orders->sum('weight_kg'),
            'total_loads' => (int) $orders->sum('number_of_loads'),
            'total_paid' => (float) $orders->sum('amount_paid'),
            'unpaid_ready' => $orders->filter(fn (Order $o) => in_array($o->status, ['ready_for_pickup', 'ready'], true) && ($o->total_price > $o->amount_paid))->count(),
        ];

        if ($user->role === 'staff') {
            return view('dashboards_staff', [
                'user' => $user,
                'orders' => $orders,
                'stats' => $stats,
                'services' => Service::query()->where('is_active', true)->orderBy('id')->get(),
                'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(),
                'inventoryItems' => InventoryItem::query()->where('is_active', true)->orderBy('name')->get(),
            ]);
        }

        $revenue = $this->netRevenue($orders, $from, $to);
        $expensesTotal = (float) $this->expensesQuery($from, $to)->sum('amount');
        $netProfit = $revenue - $expensesTotal;
        $profitMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;
        $aov = $orders->count() > 0 ? round($orders->sum('total_price') / $orders->count(), 2) : 0;
        $totalKg = (float) $orders->sum('weight_kg');
        $totalLoads = (int) $orders->sum('number_of_loads');
        $daysCount = max(1, now()->parse($graphFrom)->diffInDays(now()->parse($graphTo)) + 1);
        $capacityLoads = $daysCount * 64;
        $utilizationRate = min(100, round(($totalLoads / $capacityLoads) * 100, 1));

        $topServices = $orders->flatMap(fn (Order $order): array => array_map('trim', explode(',', (string) $order->services)))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(4);

        // Hourly Rush Distribution (Morning 8-11, Midday 11-14, Afternoon 14-17, Evening 17-21)
        $hourlyDistribution = [
            'morning' => ['label' => 'Morning (8am–11am)', 'short' => '8a–11a', 'count' => 0, 'kg' => 0.0],
            'midday' => ['label' => 'Mid-Day (11am–2pm)', 'short' => '11a–2p', 'count' => 0, 'kg' => 0.0],
            'afternoon' => ['label' => 'Afternoon (2pm–5pm)', 'short' => '2p–5p', 'count' => 0, 'kg' => 0.0],
            'evening' => ['label' => 'Evening (5pm–8pm+)', 'short' => '5p–8p', 'count' => 0, 'kg' => 0.0],
        ];

        foreach ($orders as $order) {
            $hour = $order->created_at ? (int) $order->created_at->format('G') : 12;
            $kg = (float) $order->weight_kg;
            if ($hour < 11) {
                $hourlyDistribution['morning']['count']++;
                $hourlyDistribution['morning']['kg'] += $kg;
            } elseif ($hour < 14) {
                $hourlyDistribution['midday']['count']++;
                $hourlyDistribution['midday']['kg'] += $kg;
            } elseif ($hour < 17) {
                $hourlyDistribution['afternoon']['count']++;
                $hourlyDistribution['afternoon']['kg'] += $kg;
            } else {
                $hourlyDistribution['evening']['count']++;
                $hourlyDistribution['evening']['kg'] += $kg;
            }
        }
        $hourlyMax = max(1, collect($hourlyDistribution)->max('count'));
        foreach ($hourlyDistribution as $key => $data) {
            $hourlyDistribution[$key]['percent'] = $orders->count() > 0 ? round(($data['count'] / $orders->count()) * 100, 1) : 0;
            $hourlyDistribution[$key]['bar_height'] = round(($data['count'] / $hourlyMax) * 100);
        }

        // Service Share Breakdown with Palette Colors
        $serviceColors = ['#25799B', '#CB1B03', '#1E6482', '#A2C5D8', '#F7E6CB', '#182830'];
        $serviceShare = $orders->flatMap(fn (Order $order): array => array_map('trim', explode(',', (string) $order->services)))
            ->filter()
            ->countBy()
            ->sortDesc();
        $serviceShareTotal = max(1, $serviceShare->sum());
        $colorIdx = 0;
        $serviceSegments = [];
        $accumulatedAngle = 0;
        foreach ($serviceShare as $serviceName => $count) {
            $pct = ($count / $serviceShareTotal) * 100;
            $angle = ($pct / 100) * 360;
            $serviceSegments[] = [
                'name' => $serviceName,
                'count' => $count,
                'percent' => round($pct, 1),
                'color' => $serviceColors[$colorIdx % count($serviceColors)],
                'start_angle' => $accumulatedAngle,
                'angle' => $angle,
            ];
            $accumulatedAngle += $angle;
            $colorIdx++;
        }

        // 16-Unit Machine Bay Showroom Status (8 Washers + 8 Dryers)
        $washingOrders = $orders->whereIn('status', ['washing', 'in-progress'])->values();
        $dryingOrders = $orders->where('status', 'drying')->values();

        $washers = [];
        for ($i = 1; $i <= 8; $i++) {
            $activeOrder = $washingOrders->get($i - 1);
            $washers[] = [
                'id' => 'W-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'name' => "Washer #{$i}",
                'type' => 'washer',
                'is_active' => (bool) $activeOrder,
                'status' => $activeOrder ? 'In Wash Cycle' : 'Ready / Idle',
                'order_number' => $activeOrder?->order_number ?? ($activeOrder ? 'TL-' . $activeOrder->id : null),
                'customer_name' => $activeOrder?->customer_name,
                'weight_kg' => $activeOrder?->weight_kg ?? 8.0,
                'remaining_mins' => $activeOrder ? max(5, 30 - ($i * 3)) : 0,
            ];
        }

        $dryers = [];
        for ($i = 1; $i <= 8; $i++) {
            $activeOrder = $dryingOrders->get($i - 1);
            $dryers[] = [
                'id' => 'D-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'name' => "Dryer #{$i}",
                'type' => 'dryer',
                'is_active' => (bool) $activeOrder,
                'status' => $activeOrder ? 'Tumble Drying' : 'Ready / Idle',
                'order_number' => $activeOrder?->order_number ?? ($activeOrder ? 'TL-' . $activeOrder->id : null),
                'customer_name' => $activeOrder?->customer_name,
                'weight_kg' => $activeOrder?->weight_kg ?? 8.0,
                'remaining_mins' => $activeOrder ? max(5, 45 - ($i * 4)) : 0,
            ];
        }

        return view('dashboards_admin', [
            'user' => $user,
            'orders' => $orders,
            'revenue' => $revenue,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
            'aov' => $aov,
            'totalKg' => $totalKg,
            'totalLoads' => $totalLoads,
            'utilizationRate' => $utilizationRate,
            'topServices' => $topServices,
            'team' => User::query()->select('id', 'name', 'role')->orderBy('name')->get(),
            'orderStages' => $orders->countBy('status'),
            'unclaimedOrders' => $orders->whereIn('status', ['ready_for_pickup', 'ready'])->count(),
            'balancesDue' => $orders->whereNotIn('status', ['cancelled'])->sum(fn (Order $order): float => max(0, (float) $order->total_price - (float) $order->amount_paid)),
            'expensesTotal' => $expensesTotal,
            'lowStockItems' => InventoryItem::query()->where('is_active', true)->whereColumn('quantity_on_hand', '<=', 'low_stock_threshold')->get(),
            'dailyTrend' => $dailyTrend,
            'hourlyDistribution' => $hourlyDistribution,
            'serviceSegments' => $serviceSegments,
            'washers' => $washers,
            'dryers' => $dryers,
            'paymentsByMethod' => $this->paymentsByMethod($from, $to),
            'from' => $from,
            'to' => $to,
            'preset' => $preset,
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
        $expenses = $this->expensesQuery($from, $to)->get();
        $expensesTotal = (float) $expenses->sum('amount');
        $netProfit = $revenue - $expensesTotal;

        // Business Planning & Unit Economics
        $totalKg = (float) $orders->sum('weight_kg');
        $totalLoads = (int) $orders->sum('number_of_loads');
        $revenuePerKg = $totalKg > 0 ? round($revenue / $totalKg, 2) : 0;
        $costPerKg = $totalKg > 0 ? round($expensesTotal / $totalKg, 2) : 0;
        $profitPerKg = round($revenuePerKg - $costPerKg, 2);
        $revenuePerLoad = $totalLoads > 0 ? round($revenue / $totalLoads, 2) : 0;
        $costPerLoad = $totalLoads > 0 ? round($expensesTotal / $totalLoads, 2) : 0;

        // Projections & Forward Run-Rates
        $daysSample = max(1, $from && $to ? now()->parse($from)->diffInDays(now()->parse($to)) + 1 : ($orders->min('created_at') ? now()->parse($orders->min('created_at'))->diffInDays(now()) + 1 : 7));
        $dailyRevRate = $revenue / $daysSample;
        $dailyExpRate = $expensesTotal / $daysSample;
        $projections = [
            'days_sample' => $daysSample,
            'daily_revenue' => round($dailyRevRate, 2),
            'daily_expenses' => round($dailyExpRate, 2),
            'projected_30d_rev' => round($dailyRevRate * 30, 2),
            'projected_30d_exp' => round($dailyExpRate * 30, 2),
            'projected_30d_profit' => round(($dailyRevRate - $dailyExpRate) * 30, 2),
            'projected_90d_rev' => round($dailyRevRate * 90, 2),
            'projected_90d_profit' => round(($dailyRevRate - $dailyExpRate) * 90, 2),
        ];

        // Break-Even & Fleet Capacity
        $breakEvenLoadsMonthly = $revenuePerLoad > 0 ? (int) ceil(($dailyExpRate * 30) / $revenuePerLoad) : 0;
        $currentDailyLoads = round($totalLoads / $daysSample, 1);
        $capacity = [
            'break_even_loads_month' => $breakEvenLoadsMonthly,
            'break_even_daily_loads' => (int) ceil($breakEvenLoadsMonthly / 30),
            'current_daily_loads' => $currentDailyLoads,
            'max_daily_loads' => 64, // 8 washers x 8 loads/day
            'utilization_percent' => min(100, round(($currentDailyLoads / 64) * 100, 1)),
            'headroom_daily_loads' => max(0, round(64 - $currentDailyLoads, 1)),
            'max_monthly_rev_potential' => round(64 * 30 * ($revenuePerLoad > 0 ? $revenuePerLoad : 160), 2),
        ];

        $expensesByCategory = $expenses->groupBy('category')->map(fn ($group) => (float) $group->sum('amount'));

        // Settled Invoices for Billing & Invoices tab
        $settledInvoices = Order::query()
            ->with('customer:id,name')
            ->whereIn('status', ['claimed', 'delivered'])
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->get();
        $totalCollected = (float) $settledInvoices->sum('total_price');
        $billingTxnCount = $settledInvoices->count();
        $billingAvgTicket = $billingTxnCount > 0 ? round($totalCollected / $billingTxnCount, 2) : 0;

        // Strategic Business Recommendations
        $strategicDirectives = [];

        // 1. Off-peak capacity monetization
        if ($capacity['utilization_percent'] < 60) {
            $strategicDirectives[] = [
                'id' => 'capacity_headroom',
                'severity' => 'opportunity',
                'badge' => 'Growth Opportunity',
                'title' => 'Monetize Off-Peak Plant Capacity',
                'summary' => "Current plant fleet operates at {$capacity['utilization_percent']}% capacity with +{$capacity['headroom_daily_loads']} daily loads headroom.",
                'action_steps' => [
                    'Launch an early-bird morning promotion (8:00 AM - 11:00 AM) with a 10% discount on Wash & Fold.',
                    'Partner with neighborhood Airbnbs, fitness gyms, and dormitories for recurring weekday bulk laundry pickup.',
                    'Promote express same-day turnarounds at a 25% premium during low-occupancy machine hours.'
                ],
                'impact' => "+₱" . number_format($capacity['headroom_daily_loads'] * 30 * ($revenuePerLoad > 0 ? $revenuePerLoad * 0.5 : 80), 2) . "/mo potential incremental margin",
                'cta_label' => 'Review Services & Rates',
                'cta_url' => route('services.index'),
            ];
        }

        // 2. Supply chain inventory alert
        $criticalInventory = InventoryItem::query()
            ->where('quantity_on_hand', '<=', \Illuminate\Support\Facades\DB::raw('low_stock_threshold'))
            ->get();
        if ($criticalInventory->isNotEmpty()) {
            $names = $criticalInventory->pluck('name')->implode(', ');
            $strategicDirectives[] = [
                'id' => 'supply_restock',
                'severity' => 'critical',
                'badge' => 'Supply Chain Risk',
                'title' => 'Supply Replenishment Alert',
                'summary' => "{$criticalInventory->count()} critical consumable item(s) ({$names}) are at or below safety stock threshold.",
                'action_steps' => [
                    'Issue immediate purchase orders to primary chemical distributors before stock is exhausted.',
                    'Check remaining batch runway and burn rate in the Supply Inventory module.',
                    'Temporarily buffer with reserve back-stock or adjust machine dosing recipes.'
                ],
                'impact' => 'Prevents washer machine downtime and unfulfilled customer deliveries.',
                'cta_label' => 'Restock in Supply Inventory ➔',
                'cta_url' => route('inventory.index'),
            ];
        }

        // 3. Service recipes & COGS protection
        $usedServiceIds = \App\Models\ServiceInventoryUsage::query()->pluck('service_id')->unique();
        $unlinkedServicesCount = Service::query()->whereNotIn('id', $usedServiceIds)->count();
        if ($unlinkedServicesCount > 0) {
            $strategicDirectives[] = [
                'id' => 'recipes_gap',
                'severity' => 'warning',
                'badge' => 'COGS Protection',
                'title' => 'Unmetered Service Cost Leakage',
                'summary' => "{$unlinkedServicesCount} active service(s) have no automated chemical deduction recipe attached.",
                'action_steps' => [
                    'Open the Supply Inventory Recipes module.',
                    'Set exact ml or scoop dosages per washer cycle for each unlinked service.',
                    'Ensure cost per load stays under ₱' . number_format($costPerLoad > 0 ? $costPerLoad : 35, 2) . ' to preserve unit margins.'
                ],
                'impact' => 'Accurate COGS tracking and eliminates unrecorded chemical shrinkage.',
                'cta_label' => 'Configure Recipes ➔',
                'cta_url' => route('inventory.index') . '?tab=recipes',
            ];
        }

        // 4. Ticket expansion
        $strategicDirectives[] = [
            'id' => 'ticket_expansion',
            'severity' => 'info',
            'badge' => 'Revenue Expansion',
            'title' => 'Average Ticket Size Optimization',
            'summary' => 'Current average settled invoice is ₱' . number_format($billingAvgTicket, 2) . '. Industry best practice is ₱220+.',
            'action_steps' => [
                'Incentivize floor staff to recommend premium fabric softeners (+₱20) and stain treatment boosters (+₱30).',
                'Offer tiered discounts for loads over 8kg to encourage larger customer intake drop-offs.',
                'Bundle pressing and folding with wash-only tickets for an easy +₱50 add-on.'
            ],
            'impact' => 'Estimated +15% to +25% top-line revenue without equipment capital expenditure.',
            'cta_label' => 'View Customer Intelligence ➔',
            'cta_url' => route('customers.insights'),
        ];

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
            'netProfit' => $netProfit,
            'paymentsByMethod' => $this->paymentsByMethod($from, $to),
            'totalKg' => $totalKg,
            'totalLoads' => $totalLoads,
            'revenuePerKg' => $revenuePerKg,
            'costPerKg' => $costPerKg,
            'profitPerKg' => $profitPerKg,
            'revenuePerLoad' => $revenuePerLoad,
            'costPerLoad' => $costPerLoad,
            'projections' => $projections,
            'capacity' => $capacity,
            'expensesByCategory' => $expensesByCategory,
            'settledInvoices' => $settledInvoices,
            'totalCollected' => $totalCollected,
            'billingTxnCount' => $billingTxnCount,
            'billingAvgTicket' => $billingAvgTicket,
            'strategicDirectives' => $strategicDirectives,
            'activeTab' => $request->query('tab', 'plan'),
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
        $expensesTotal = (float) $expenses->sum('amount');
        $netProfit = $revenue - $expensesTotal;

        $totalKg = (float) $orders->sum('weight_kg');
        $totalLoads = (int) $orders->sum('number_of_loads');
        $revenuePerKg = $totalKg > 0 ? round($revenue / $totalKg, 2) : 0;
        $costPerKg = $totalKg > 0 ? round($expensesTotal / $totalKg, 2) : 0;
        $revenuePerLoad = $totalLoads > 0 ? round($revenue / $totalLoads, 2) : 0;

        $daysSample = max(1, $from && $to ? now()->parse($from)->diffInDays(now()->parse($to)) + 1 : 7);
        $dailyRevRate = $revenue / $daysSample;
        $dailyExpRate = $expensesTotal / $daysSample;

        $projections = [
            'projected_30d_rev' => round($dailyRevRate * 30, 2),
            'projected_30d_profit' => round(($dailyRevRate - $dailyExpRate) * 30, 2),
            'break_even_loads' => $revenuePerLoad > 0 ? (int) ceil(($dailyExpRate * 30) / $revenuePerLoad) : 0,
        ];

        return view('reports.print', [
            'orders' => $orders,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'expensesTotal' => $expensesTotal,
            'netProfit' => $netProfit,
            'paymentsByMethod' => $this->paymentsByMethod($from, $to),
            'orderStages' => $orders->countBy('status'),
            'totalKg' => $totalKg,
            'totalLoads' => $totalLoads,
            'revenuePerKg' => $revenuePerKg,
            'costPerKg' => $costPerKg,
            'revenuePerLoad' => $revenuePerLoad,
            'projections' => $projections,
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

    public function billing(Request $request): View
    {
        $request->merge(['tab' => 'billing']);

        return $this->reports($request);
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
