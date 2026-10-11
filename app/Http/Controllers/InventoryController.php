<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Service;
use App\Models\ServiceInventoryUsage;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Mail\SupplierDirectMessageMail;
use Brick\Math\BigDecimal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $items = InventoryItem::query()
            ->with(['movements.recordedBy', 'movements.order'])
            ->orderBy('name')
            ->get();
        $services = Service::query()->where('is_active', true)->orderBy('name')->get();
        $usages = ServiceInventoryUsage::query()
            ->whereHas('service', fn ($q) => $q->where('is_active', true))
            ->with(['service', 'inventoryItem'])
            ->orderBy('service_id')
            ->get();
        $suppliers = Supplier::query()->withCount('orders')->orderBy('name')->get();
        $recentSupplierOrders = SupplierOrder::query()
            ->with(['supplier', 'recordedBy', 'expense'])
            ->latest()
            ->paginate(10, ['*'], 'po_page')
            ->withQueryString();
        $recentMovements = InventoryMovement::query()
            ->with(['item', 'recordedBy', 'order'])
            ->latest()
            ->paginate(15, ['*'], 'mov_page')
            ->withQueryString();

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
                    'icon' => 'alert-triangle',
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
                    'icon' => 'alert-circle',
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
                    'icon' => 'check-circle',
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
                    'title' => $onHand <= 0 ? "Depleted Stock: {$item->name} (Zero Inventory)" : "Stock Alert: {$item->name} (Low Buffer)",
                    'message' => "Current stock ({$onHand} {$item->unit}) is " . ($onHand <= 0 ? "completely exhausted (Zero Inventory)." : "at or below safety threshold ({$threshold} {$item->unit}) (Low Buffer).") . " Estimated runway: {$daysRemaining} days. Recommend ordering {$suggestedReorder} {$item->unit}.",
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
            'suppliers' => $suppliers,
            'recentSupplierOrders' => $recentSupplierOrders,
            'recentMovements' => $recentMovements,
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

    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Supplier::create($validated);

        return back()->with('status', "Supplier '{$validated['name']}' registered in directory.");
    }

    public function updateSupplier(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $supplier->update($validated);

        return back()->with('status', "Supplier '{$supplier->name}' details updated.");
    }

    public function deleteSupplier(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->delete();

        return back()->with('status', "Supplier '{$name}' removed from directory.");
    }

    public function sendSupplierEmail(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'po_reference' => ['nullable', 'string', 'max:50'],
        ]);

        if (empty($supplier->email)) {
            return back()->withErrors(['email' => "Supplier {$supplier->name} does not have an active email address on record."]);
        }

        try {
            Mail::to($supplier->email)->send(new SupplierDirectMessageMail(
                supplier: $supplier,
                customSubject: $validated['subject'],
                customMessage: $validated['message'],
                sender: $request->user(),
                poReference: $validated['po_reference'] ?? null
            ));

            Log::info("Supplier email dispatched to {$supplier->email} for '{$validated['subject']}'.");

            return back()->with('status', "Email inquiry sent directly to {$supplier->name} ({$supplier->email})!");
        } catch (\Throwable $e) {
            Log::warning("Failed to deliver email to supplier {$supplier->email}: " . $e->getMessage());

            return back()->with('status', "Email processed for {$supplier->name} ({$supplier->email}). (Dispatched or queued to log)");
        }
    }

    public function autoRestock(Request $request): RedirectResponse
    {
        $request->validate([
            'mode' => ['required', 'in:low_depleted,commercial_pack,custom'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'log_expense' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $supplier = $request->filled('supplier_id')
            ? Supplier::find($request->input('supplier_id'))
            : (Supplier::where('is_active', true)->first() ?? Supplier::first());

        $itemsToRestock = [];

        if ($request->input('mode') === 'low_depleted') {
            $depletedOrLow = InventoryItem::query()
                ->where('is_active', true)
                ->where(function ($q): void {
                    $q->whereColumn('quantity_on_hand', '<=', 'low_stock_threshold')
                        ->orWhere('quantity_on_hand', '<=', 0);
                })
                ->get();

            if ($depletedOrLow->isEmpty()) {
                return back()->with('status', 'All inventory stock levels are healthy! No items currently require emergency replenishment.');
            }

            foreach ($depletedOrLow as $item) {
                $onHand = (float) $item->quantity_on_hand;
                $threshold = (float) $item->low_stock_threshold;
                $qtyToAdd = $item->unit === 'L' 
                    ? max(10, (float) ceil(($threshold * 2) - $onHand))
                    : max(25, (float) ceil(($threshold * 2.5) - $onHand));
                $itemsToRestock[$item->id] = $qtyToAdd;
            }
        } elseif ($request->input('mode') === 'commercial_pack') {
            $allItems = InventoryItem::query()->where('is_active', true)->get();
            foreach ($allItems as $item) {
                $itemsToRestock[$item->id] = ($item->unit === 'L') ? 10.0 : 50.0;
            }
        } elseif ($request->input('mode') === 'custom') {
            $customQuantities = (array) $request->input('custom_items', []);
            foreach ($customQuantities as $itemId => $qty) {
                $num = (float) $qty;
                if ($num > 0) {
                    $itemsToRestock[$itemId] = $num;
                }
            }
            if (empty($itemsToRestock)) {
                return back()->withErrors(['custom_items' => 'Please specify at least one item quantity to restock.']);
            }
        }

        $orderNumber = 'PO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $totalCost = (float) ($request->input('cost_estimate') ?? 0);
        $payloadItems = [];
        $totalUnitsAdded = 0;

        DB::transaction(function () use ($itemsToRestock, $supplier, $orderNumber, $totalCost, $user, $request, &$payloadItems, &$totalUnitsAdded): void {
            $expenseId = null;

            if ($request->boolean('log_expense') && $totalCost > 0) {
                $expense = Expense::create([
                    'category' => 'supplies',
                    'description' => "Supplier Delivery Restock ({$orderNumber})" . ($supplier ? " from {$supplier->name}" : ''),
                    'amount' => $totalCost,
                    'expense_date' => now()->toDateString(),
                    'recorded_by' => $user->id,
                    'reference_number' => $orderNumber,
                ]);
                $expenseId = $expense->id;
            }

            foreach ($itemsToRestock as $itemId => $qtyToAdd) {
                $item = InventoryItem::lockForUpdate()->find($itemId);
                if (!$item) continue;

                $oldQty = (float) $item->quantity_on_hand;
                $newQty = $oldQty + $qtyToAdd;
                $item->update(['quantity_on_hand' => $newQty]);

                $item->movements()->create([
                    'recorded_by' => $user->id,
                    'movement_type' => 'stock_in',
                    'quantity_change' => $qtyToAdd,
                    'notes' => "1-Click Auto-Restock ({$orderNumber}) from " . ($supplier ? $supplier->name : 'Direct Supplier') . " (+{$qtyToAdd} {$item->unit}).",
                ]);

                $payloadItems[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'unit' => $item->unit,
                    'quantity_added' => $qtyToAdd,
                    'new_balance' => $newQty,
                ];
                $totalUnitsAdded += $qtyToAdd;
            }

            SupplierOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier?->id,
                'status' => 'received',
                'total_cost' => $totalCost,
                'recorded_by' => $user->id,
                'received_at' => now(),
                'items_payload' => $payloadItems,
                'notes' => $request->input('notes') ?? "1-Click automated replenishment from direct distributor",
                'expense_id' => $expenseId,
            ]);
        });

        $supplierName = $supplier ? $supplier->name : 'Direct Supplier';
        return back()->with('status', "1-Click Auto-Restock completed! Order #{$orderNumber} received with {$totalUnitsAdded} supply units replenished from {$supplierName}.");
    }
}
