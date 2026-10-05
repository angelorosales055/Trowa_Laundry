@extends('layouts_app')

@section('content')
<div class="mb-6">
    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-[#25799B] hover:text-[#CB1B03] transition">
        <span>←</span>
        <span>Return to Order Details</span>
    </a>
    <h1 class="mt-2 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
        Correct Order Details
    </h1>
    <p class="text-xs text-[#25799B] font-medium mt-0.5">
        Corrections are logged in the audit trail. Previous customer payments remain preserved.
    </p>
</div>

<form method="POST" action="{{ route('orders.update-details', $order) }}" class="retro-panel space-y-6 p-6">
    @csrf @method('PUT')

    <!-- Customer & Weight -->
    <div>
        <h2 class="mb-3 font-recoleta text-base font-bold text-[#182830] border-b-2 border-[#182830]/15 pb-1.5">
            1. Customer Profile & Measured Weight
        </h2>
        <div class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Customer *</label>
                <select name="customer_id" class="field text-xs" required>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected($customer->id === $order->customer_id)>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Weight in kilograms *</label>
                <input name="weight_kg" type="number" min="0.01" step="0.01" value="{{ $order->weight_kg }}" class="field text-xs font-mono font-bold" required>
            </div>
            <div class="rounded-xl border-2 border-[#182830] bg-[#A2C5D8] p-3 shadow-[1px_1px_0px_#182830]">
                <p class="font-mono text-[10px] font-extrabold uppercase text-[#182830]">Automatic Load Rule</p>
                <strong class="font-mono text-sm text-[#182830]">CEILING(weight ÷ 8 kg)</strong>
            </div>
        </div>
    </div>

    <!-- Services Selection -->
    <div>
        <h2 class="mb-3 font-recoleta text-base font-bold text-[#182830] border-b-2 border-[#182830]/15 pb-1.5">
            2. Assigned Laundry Services
        </h2>
        <div class="grid gap-2 sm:grid-cols-2">
            @foreach($services as $service)
                <label class="flex cursor-pointer items-center justify-between rounded-xl border-2 border-[#182830] p-2.5 text-xs hover:bg-[#F7E6CB]/40 shadow-[1px_1px_0px_#182830]">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" @checked($order->orderServices->contains('service_id', $service->id)) class="h-4 w-4 rounded border-2 border-[#182830] text-[#CB1B03] focus:ring-0">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md border border-[#182830] bg-[#A2C5D8] text-[#182830] shrink-0">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        </span>
                        <span class="font-bold text-[#182830]">{{ $service->name }}</span>
                    </div>
                    <span class="font-mono text-xs text-[#25799B] font-bold">₱{{ number_format((float) ($service->price_per_load ?? $service->price), 2) }}/load</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Garment Breakdown -->
    <div>
        <h2 class="mb-3 font-recoleta text-base font-bold text-[#182830] border-b-2 border-[#182830]/15 pb-1.5">
            3. Garment Item Quantities
        </h2>
        <div id="edit-items" class="space-y-2">
            @forelse($order->itemDetails as $index => $item)
                <div class="grid gap-2 sm:grid-cols-2">
                    <input name="item_details[{{ $index }}][item_name]" value="{{ $item->item_name }}" class="field text-xs" placeholder="Item name">
                    <input name="item_details[{{ $index }}][quantity]" value="{{ $item->quantity }}" type="number" min="1" class="field text-xs font-mono" placeholder="Quantity">
                </div>
            @empty
                <div class="grid gap-2 sm:grid-cols-2">
                    <input name="item_details[0][item_name]" class="field text-xs" placeholder="Item name">
                    <input name="item_details[0][quantity]" type="number" min="1" class="field text-xs font-mono" placeholder="Quantity">
                </div>
            @endforelse
        </div>
    </div>

    <!-- Audit Reason -->
    <div>
        <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Official Reason for Order Correction *</label>
        <textarea name="reason" class="field text-xs" rows="3" required placeholder="Explain why this order is being corrected (e.g. scale re-calibrated, added steam press request)..."></textarea>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('orders.show', $order) }}" class="retro-btn-secondary text-xs">Cancel</a>
        <button type="submit" class="retro-btn-primary text-xs">Save Correction ➔</button>
    </div>
</form>
@endsection
