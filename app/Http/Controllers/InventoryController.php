<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\ServiceInventoryUsage;
use Brick\Math\BigDecimal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::query()
            ->with(['movements.recordedBy', 'movements.order'])
            ->orderBy('name')
            ->get();
        $services = Service::query()->orderBy('name')->get();
        $usages = ServiceInventoryUsage::query()->with(['service', 'inventoryItem'])->orderBy('service_id')->get();

        $thirtyDaysAgo = now()->subDays(30);
        $smartSuggestions = [];
        $lowStockCount = 0;
        $almostOutCount = 0;
        $depletedCount = 0;
        $healthyCount = 0;
        $totalItems = $items->count();

        $enrichedItems = $items->map(function ($item) use ($thirtyDaysAgo, &$smartSuggestions, &$lowStockCount, &$almostOutCount, &$depletedCount, &$healthyCount) {
            $recentOutMovements = $item->movements->filter(fn ($m) => $m->created_at >= $thirtyDaysAgo && $m->quantity_change < 0);
            $totalOut30Days = abs((float) $recentOutMovements->sum('quantity_change'));
            $dailyBurn = $totalOut30Days > 0 ? $totalOut30Days / 30 : 0;
            
            $onHand = (float) $item->quantity_on_hand;
            $threshold = (float) $item->low_stock_threshold;
            
            $daysRemaining = $dailyBurn > 0 ? (int) floor($onHand / $dailyBurn) : ($onHand > 0 ? 99 : 0);
            $isLow = $onHand <= $threshold;
            if ($isLow) {
                $lowStockCount++;
            }

            if ($onHand <= 0) {
                $depletedCount++;
                $medal = [
                    'type' => 'depleted',
                    'label' => 'Depleted Stock Medal',
                    'short_label' => 'Depleted',
                    'icon' => '🏅',
                    'color' => 'red',
                    'badge_class' => 'bg-[#CB1B03] text-white border-[#182830]',
                    'title' => 'Critical: Out of Stock',
                ];
            } elseif ($isLow) {
                $almostOutCount++;
                $medal = [
                    'type' => 'almost_out',
                    'label' => 'Almost Out Medal',
                    'short_label' => 'Almost Out',
                    'icon' => '🎖️',
                    'color' => 'amber',
                    'badge_class' => 'bg-amber-400 text-[#182830] border-[#182830] animate-pulse',
                    'title' => 'Warning: Safety Buffer Breached',
                ];
            } else {
                $healthyCount++;
                $medal = [
                    'type' => 'healthy',
                    'label' => 'Stock Healthy Medal',
                    'short_label' => 'Healthy',
                    'icon' => '🥇',
                    'color' => 'emerald',
                    'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-600',
                    'title' => 'Optimal Buffer Maintained',
                ];
            }

            $targetSafetyStock = max($threshold * 2, $dailyBurn * 30);
            $suggestedReorder = max((float) ceil($targetSafetyStock - $onHand), 5.0);

            if ($isLow || $daysRemaining <= 5) {
                $smartSuggestions[] = [
                    'item' => $item,
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'unit' => $item->unit,
                    'severity' => $onHand <= 0 ? 'critical' : ($isLow ? 'danger' : 'warning'),
                    'title' => $onHand <= 0 ? "Depleted Stock: {$item->name} (🏅 Depleted Stock Medal)" : "Stock Alert: {$item->name} (🎖️ Almost Out Medal)",
                    'message' => "Current stock ({$onHand} {$item->unit}) is " . ($onHand <= 0 ? "completely exhausted (🏅 Depleted Stock Medal)." : "at or below safety threshold ({$threshold} {$item->unit}) (🎖️ Almost Out Medal).") . " Estimated runway: {$daysRemaining} days. Recommend ordering {$suggestedReorder} {$item->unit}.",
                    'suggested_quantity' => $suggestedReorder,
                    'action_title' => "Replenish {$suggestedReorder} {$item->unit} of {$item->name}",
                    'action_steps' => [
                        "Submit purchase requisition for {$suggestedReorder} {$item->unit} to registered chemical/soap distributor.",
                        "Inspect package and sachet integrity upon arrival at laundry intake bay.",
                        "Record 'Stock In' receipt in the ledger with invoice or vendor delivery receipt."
                    ],
                    'action_type' => 'reorder',
                ];
            }

            $item->daily_burn = round($dailyBurn, 2);
            $item->days_remaining = $daysRemaining;
            $item->suggested_reorder = $suggestedReorder;
            $item->is_low = $isLow;
            $item->medal = $medal;

            return $item;
        });

        $servicesWithRecipes = $usages->pluck('service_id')->unique();
        $unlinkedServices = $services->whereNotIn('id', $servicesWithRecipes);
        if ($unlinkedServices->isNotEmpty()) {
            $smartSuggestions[] = [
                'severity' => 'info',
                'title' => 'Missing Auto-Deduction Recipes',
                'message' => "{$unlinkedServices->count()} service(s) ({$unlinkedServices->pluck('name')->implode(', ')}) do not have automatic chemical consumption recipes. Machines will process loads without tracking detergent depletion.",
                'suggested_quantity' => null,
                'action_title' => 'Link Detergent Dosage to Services',
                'action_steps' => [
                    'Navigate to the Auto-Consumption Recipes tab.',
                    'Select each unlinked service (' . $unlinkedServices->pluck('name')->implode(', ') . ').',
                    'Specify recommended dosage (e.g. 1 sachet per load) and click Save.'
                ],
                'action_type' => 'recipe_setup',
            ];
        }

        return view('inventory.index', [
            'items' => $enrichedItems,
            'services' => $services,
            'usages' => $usages,
            'smartSuggestions' => $smartSuggestions,
            'lowStockCount' => $lowStockCount,
            'almostOutCount' => $almostOutCount,
            'depletedCount' => $depletedCount,
            'healthyCount' => $healthyCount,
            'totalItems' => $totalItems,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:inventory_items,name'],
            'unit' => ['nullable', 'string', 'max:30'],
            'quantity_on_hand' => ['required', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        // Automatically default and perceive unit as 'sachet' if omitted or blank
        $data['unit'] = !empty(trim($data['unit'] ?? '')) ? trim($data['unit']) : 'sachet';

        DB::transaction(function () use ($data, $request): void {
            $item = InventoryItem::query()->create($data);
            if ((float) $data['quantity_on_hand'] > 0) {
                $item->movements()->create([
                    'recorded_by' => $request->user()->id,
                    'movement_type' => 'opening_stock',
                    'quantity_change' => $data['quantity_on_hand'],
                    'notes' => "Initial inventory balance ({$data['quantity_on_hand']} {$data['unit']}).",
                ]);
            }
        });

        return back()->with('status', "Inventory item '{$data['name']}' created (tracked per {$data['unit']}).");
    }

    public function movement(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $data = $request->validate([
            'movement_type' => ['required', 'in:stock_in,stock_out,adjustment'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $inventoryItem, $request): void {
            $item = InventoryItem::query()->lockForUpdate()->findOrFail($inventoryItem->id);
            $quantity = BigDecimal::of((string) $data['quantity']);
            $delta = match ($data['movement_type']) {
                'stock_in' => $quantity,
                'stock_out' => $quantity->negated(),
                default => $quantity->minus((string) $item->quantity_on_hand),
            };
            $newQuantity = BigDecimal::of((string) $item->quantity_on_hand)->plus($delta);
            if ($newQuantity->isNegative()) {
                throw ValidationException::withMessages(['quantity' => 'The stock movement cannot reduce stock below zero.']);
            }

            $item->update(['quantity_on_hand' => (string) $newQuantity]);
            $item->movements()->create([
                'recorded_by' => $request->user()->id,
                'movement_type' => $data['movement_type'],
                'quantity_change' => (string) $delta,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return back()->with('status', 'Stock movement recorded.');
    }

    public function configureUsage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'quantity_per_load' => ['required', 'numeric', 'gt:0'],
        ]);

        ServiceInventoryUsage::query()->updateOrCreate(
            ['service_id' => $data['service_id'], 'inventory_item_id' => $data['inventory_item_id']],
            ['quantity_per_load' => $data['quantity_per_load']]
        );

        return back()->with('status', 'Automatic service consumption configured.');
    }

    public function deleteUsage(ServiceInventoryUsage $usage): RedirectResponse
    {
        $usage->delete();

        return back()->with('status', 'Automatic service consumption removed.');
    }
}
