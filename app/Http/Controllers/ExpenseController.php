<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $expenses = Expense::query()
            ->with(['recordedBy', 'order'])
            ->when($from, fn ($query) => $query->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('expense_date', '<=', $to))
            ->latest('expense_date')
            ->paginate(20)
            ->withQueryString();

        // Operational consumption analytics (Overall Uses of Water & Supplies)
        // Standard commercial front-load washer: ~50 Liters of water per 8kg load cycle
        // Davao/National standard commercial water rate: ~₱45.00 per cubic meter (₱0.045 per Liter) -> ₱2.25 water cost / load
        // Estimated machine power consumption: ~1.2 kWh per wash/spin/dry cycle @ ₱11.50/kWh -> ₱13.80 / load
        $filteredOrdersQuery = \App\Models\Order::query()
            ->whereNotIn('status', ['cancelled'])
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to));

        $totalLoads = (int) (clone $filteredOrdersQuery)->sum('number_of_loads');
        $totalKg = (float) (clone $filteredOrdersQuery)->sum('weight_kg');
        $totalOrdersCount = (int) (clone $filteredOrdersQuery)->count();

        // Water Usage Metrics
        $waterLitersPerLoad = 50.0; // 50 Liters per commercial load
        $waterTotalLiters = $totalLoads * $waterLitersPerLoad;
        $waterCubicMeters = $waterTotalLiters / 1000.0;
        $waterCostPerLiter = 0.045; // ~₱45 per m3
        $estWaterExpense = round($waterTotalLiters * $waterCostPerLiter, 2);

        // Power Usage Metrics (~1.2 kWh / load @ ₱11.50)
        $kwhPerLoad = 1.2;
        $totalKwh = $totalLoads * $kwhPerLoad;
        $kwhRate = 11.50;
        $estPowerExpense = round($totalKwh * $kwhRate, 2);
        $estUtilitiesTotal = round($estWaterExpense + $estPowerExpense, 2);

        // Supply Usage Metrics
        $totalSuppliesUsed = (int) \App\Models\InventoryMovement::query()
            ->where('movement_type', 'usage')
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->sum(\Illuminate\Support\Facades\DB::raw('ABS(quantity_change)'));

        // Estimated detergent cost (Avg ~₱6.50 wholesale cost per sachet consumed)
        $estSuppliesExpense = round($totalSuppliesUsed * 6.50, 2);

        // Check already logged utility expenses for this date or period
        $loggedUtilityTotal = (float) Expense::query()
            ->where('category', 'utilities')
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->sum('amount');

        return view('expenses.index', [
            'expenses' => $expenses,
            'total' => Expense::query()
                ->when($from, fn ($query) => $query->whereDate('expense_date', '>=', $from))
                ->when($to, fn ($query) => $query->whereDate('expense_date', '<=', $to))
                ->sum('amount'),
            'waterStats' => [
                'total_loads' => $totalLoads,
                'total_kg' => $totalKg,
                'orders_count' => $totalOrdersCount,
                'water_liters' => $waterTotalLiters,
                'water_m3' => $waterCubicMeters,
                'est_water_cost' => $estWaterExpense,
                'total_kwh' => $totalKwh,
                'est_power_cost' => $estPowerExpense,
                'est_utilities_total' => $estUtilitiesTotal,
                'supplies_used' => $totalSuppliesUsed,
                'est_supplies_cost' => $estSuppliesExpense,
                'logged_utility_total' => $loggedUtilityTotal,
            ],
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'in:utilities,rent,supplies,maintenance,transportation,payroll,other'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'expense_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ]);
        $data['recorded_by'] = $request->user()->id;
        Expense::query()->create($data);

        return back()->with('status', 'Expense recorded successfully.');
    }

    public function autoLogUtilities(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:water,utilities,supplies'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $category = $validated['type'] === 'supplies' ? 'supplies' : 'utilities';

        Expense::create([
            'category' => $category,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['date'],
            'recorded_by' => $request->user()->id,
            'reference_number' => 'AUTO-' . strtoupper($validated['type']) . '-' . now()->format('YmdHi'),
        ]);

        return back()->with('status', "Auto-calculated {$category} expense of ₱" . number_format($validated['amount'], 2) . " has been logged to the ledger.");
    }
}
