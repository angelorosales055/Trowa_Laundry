<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $customers = Customer::query()
            ->withCount('orders')
            ->withSum('orders', 'total_price')
            ->with(['orders' => fn ($q) => $q->latest('updated_at')->limit(1)])
            ->when($request->user()->role === 'staff', fn ($query) => $query->where('is_active', true))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('is_active', true)->count(),
            'vip' => Customer::has('orders', '>=', 3)->count(),
            'total_spend' => (float) \App\Models\Order::sum('total_price'),
        ];

        return view('customers.index', [
            'customers' => $customers,
            'search' => $search,
            'stats' => $stats,
            'mergeTargets' => $request->user()->role === 'admin'
                ? Customer::query()->where('is_active', true)->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Customer::create($data);

        return back()->with('status', 'Customer added.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
        $data['phone'] = $data['phone'] ?? $data['contact_number'];
        $data['contact_number'] = $data['contact_number'] ?? $data['phone'];
        $customer->update($data);

        return back()->with('status', 'Customer details updated.');
    }

    public function merge(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'target_customer_id' => ['required', 'integer', 'exists:customers,id'],
        ]);
        $target = Customer::query()->where('is_active', true)->findOrFail($data['target_customer_id']);
        abort_if($customer->id === $target->id || $customer->merged_into_id !== null, 422, 'Choose a different active customer record.');

        DB::transaction(function () use ($customer, $target): void {
            $customer->orders()->update(['customer_id' => $target->id]);
            $customer->update(['is_active' => false, 'merged_into_id' => $target->id]);
        });

        return back()->with('status', "Customer records merged into {$target->name}. Order snapshots were preserved.");
    }

    public function toggleActive(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_if($customer->merged_into_id !== null, 422, 'Merged customer records cannot be reactivated.');
        $customer->update(['is_active' => ! $customer->is_active]);

        return back()->with('status', $customer->is_active ? 'Customer activated.' : 'Customer deactivated.');
    }

    public function insights(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        $customers = Customer::query()
            ->where('is_active', true)
            ->withCount(['orders' => fn ($q) => $q
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ])
            ->withSum(['orders' => fn ($q) => $q
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ], 'total_price')
            ->withSum(['orders' => fn ($q) => $q
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ], 'weight_kg')
            ->with(['orders' => fn ($q) => $q
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
                ->latest('created_at')
                ->limit(1)
            ])
            ->get();

        $totalCustomers = $customers->count();
        $totalOrdersCount = (int) $customers->sum('orders_count');
        $totalSpend = (float) $customers->sum('orders_sum_total_price');
        $totalKg = (float) $customers->sum('orders_sum_weight_kg');

        $repeatCustomers = $customers->filter(fn ($c) => $c->orders_count >= 2);
        $repeatRate = $totalCustomers > 0 ? round(($repeatCustomers->count() / $totalCustomers) * 100, 1) : 0;
        $averageLtv = $totalCustomers > 0 ? round($totalSpend / $totalCustomers, 2) : 0;
        $averageOrderValue = $totalOrdersCount > 0 ? round($totalSpend / $totalOrdersCount, 2) : 0;
        $averageKgPerOrder = $totalOrdersCount > 0 ? round($totalKg / $totalOrdersCount, 2) : 0;

        $vipCustomers = $customers->filter(fn ($c) => $c->orders_count >= 5 || (float) $c->orders_sum_total_price >= 2000);
        $regularCustomers = $customers->filter(fn ($c) => $c->orders_count >= 2 && $c->orders_count < 5 && (float) $c->orders_sum_total_price < 2000);
        $newCustomers = $customers->filter(fn ($c) => $c->orders_count === 1);
        $dormantCustomers = $customers->filter(function ($c) {
            $latest = $c->orders->first();
            if (! $latest) return true;
            return $latest->created_at?->diffInDays(now()) > 45;
        });

        // Top Spenders Leaderboard
        $topSpenders = $customers->sortByDesc('orders_sum_total_price')->take(10)->values();

        // Frequency distribution buckets
        $frequencyDistribution = [
            '1 Order' => $customers->where('orders_count', 1)->count(),
            '2-3 Orders' => $customers->whereBetween('orders_count', [2, 3])->count(),
            '4-6 Orders' => $customers->whereBetween('orders_count', [4, 6])->count(),
            '7+ Orders' => $customers->filter(fn ($c) => $c->orders_count >= 7)->count(),
        ];

        // Orders in selected timeframe
        $ordersInPeriod = \App\Models\Order::query()
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->get();

        $dayOfWeekStats = collect([
            'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0,
            'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0
        ]);
        foreach ($ordersInPeriod as $o) {
            if ($o->created_at) {
                $dayName = $o->created_at->format('l');
                $dayOfWeekStats[$dayName] = ($dayOfWeekStats[$dayName] ?? 0) + 1;
            }
        }

        $servicePreferences = $ordersInPeriod->flatMap(fn ($o) => array_map('trim', explode(',', (string) $o->services)))
            ->filter()
            ->countBy()
            ->sortDesc();

        return view('customers.insights', [
            'totalCustomers' => $totalCustomers,
            'totalOrdersCount' => $totalOrdersCount,
            'totalSpend' => $totalSpend,
            'repeatRate' => $repeatRate,
            'repeatCount' => $repeatCustomers->count(),
            'averageLtv' => $averageLtv,
            'averageOrderValue' => $averageOrderValue,
            'averageKgPerOrder' => $averageKgPerOrder,
            'vipCount' => $vipCustomers->count(),
            'regularCount' => $regularCustomers->count(),
            'newCount' => $newCustomers->count(),
            'dormantCount' => $dormantCustomers->count(),
            'topSpenders' => $topSpenders,
            'frequencyDistribution' => $frequencyDistribution,
            'dayOfWeekStats' => $dayOfWeekStats,
            'servicePreferences' => $servicePreferences,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
