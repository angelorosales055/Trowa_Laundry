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
        return view('inventory.index', [
            'items' => InventoryItem::query()->with(['movements.recordedBy', 'movements.order'])->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
            'usages' => ServiceInventoryUsage::query()->with(['service', 'inventoryItem'])->orderBy('service_id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:inventory_items,name'],
            'unit' => ['required', 'string', 'max:30'],
            'quantity_on_hand' => ['required', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $item = InventoryItem::query()->create($data);
            if ((float) $data['quantity_on_hand'] > 0) {
                $item->movements()->create([
                    'recorded_by' => $request->user()->id,
                    'movement_type' => 'opening_stock',
                    'quantity_change' => $data['quantity_on_hand'],
                    'notes' => 'Initial inventory balance.',
                ]);
            }
        });

        return back()->with('status', 'Inventory item created.');
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
