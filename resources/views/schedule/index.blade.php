@extends('layouts_app')

@section('content')
@php($module = $module ?? 'status')
<div class="mb-6 flex items-start justify-between">
    <div><p class="mb-1 text-sm text-slate-500">{{ $module === 'management' ? 'Create and manage customer laundry orders' : 'Monitor and update laundry processing stages' }}</p><h1 class="text-3xl font-bold text-slate-900">{{ $module === 'management' ? 'Laundry Order Management' : 'Order Status' }}</h1></div>
    @if($module === 'management')
        <button type="button" data-modal-open="order-modal" class="btn-primary">＋ New Order</button>
    @else
        <a href="{{ route('orders.index') }}" class="btn-secondary">Manage Orders</a>
    @endif
</div>
@if($module !== 'management')
<div class="mb-5 flex flex-wrap gap-2">
    @foreach([''=>'All','received'=>'Received','washing'=>'Washing','drying'=>'Drying','folding'=>'Folding','ready_for_pickup'=>'Ready for Pickup','claimed'=>'Claimed','cancelled'=>'Cancelled'] as $value => $label)
        <a href="{{ route('schedule.index', ['status' => $value]) }}" class="{{ $status === $value ? 'bg-brand-500 text-white' : 'bg-white text-slate-600' }} rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold">{{ $label }}</a>
    @endforeach
</div>
@endif
<form method="GET" class="mb-5">@if($module !== 'management')<input type="hidden" name="status" value="{{ $status }}">@endif<input name="search" value="{{ $search }}" class="field" placeholder="Search by customer or order number..."></form>
<div class="panel overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Weight / Loads</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Payment</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">{{ $module === 'management' ? 'Details' : 'Update' }}</th></tr></thead>
    <tbody class="divide-y divide-slate-100">@forelse($orders as $order)<tr><td class="px-5 py-3 font-semibold text-brand-600"><a href="{{ route('orders.show', $order) }}" class="hover:underline">{{ $order->order_number ?? 'TL-'.$order->id }}</a></td><td class="px-5 py-3"><p>{{ $order->customer?->name ?? $order->customer_name }}</p><p class="text-xs text-slate-400">{{ $order->customer?->contact_number ?? $order->customer?->phone }}</p><p class="text-xs text-slate-400">{{ $order->services }}</p></td><td class="px-5 py-3">{{ $order->weight_kg }} kg <span class="text-slate-400">· {{ $order->number_of_loads }} load(s)</span></td><td class="px-5 py-3 font-semibold">₱{{ number_format((float) $order->total_price, 2) }}</td><td class="px-5 py-3"><span class="badge {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($order->payment_status === 'partially_paid' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</span></td><td class="px-5 py-3"><span class="badge bg-blue-100 text-blue-700">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td><td class="px-5 py-3">@if($module === 'management')<a href="{{ route('orders.show', $order) }}" class="btn-secondary px-3 py-1 text-xs">View Details</a>@else<form method="POST" action="{{ route('orders.status.update', $order) }}">@csrf<select name="status" onchange="this.form.submit()" class="field py-1 text-xs"><option value="received" @selected($order->status === 'received')>Received</option><option value="washing" @selected($order->status === 'washing')>Washing</option><option value="drying" @selected($order->status === 'drying')>Drying</option><option value="folding" @selected($order->status === 'folding')>Folding</option><option value="ready_for_pickup" @selected($order->status === 'ready_for_pickup')>Ready for Pickup</option><option value="claimed" @selected($order->status === 'claimed')>Claimed</option><option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option></select></form>@endif</td></tr>@empty<tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">No orders found.</td></tr>@endforelse</tbody></table></div>
    <div class="border-t border-slate-100 px-5 py-3">{{ $orders->links() }}</div>
</div>

@if($module === 'management')
<div id="order-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4">
    <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-5 flex items-center justify-between"><div><h2 class="font-display text-xl font-bold">Create Laundry Order</h2><p class="text-sm text-slate-500">Loads are calculated automatically at 8 kg per machine load.</p></div><button data-modal-close class="text-2xl text-slate-400">×</button></div>
        <form method="POST" action="{{ route('orders.add') }}" id="order-form" class="space-y-5">@csrf
            <div><h3 class="mb-3 font-display font-semibold">Customer Information</h3><div class="grid gap-3 md:grid-cols-2"><label class="text-sm font-medium">Existing customer<select name="customer_id" id="customer-id" class="field mt-1"><option value="">New customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-contact="{{ $customer->contact_number ?? $customer->phone }}" data-address="{{ $customer->address }}">{{ $customer->name }}</option>@endforeach</select></label><label class="text-sm font-medium">Customer name<input name="customer_name" id="customer-name" class="field mt-1" placeholder="Full name"></label><label class="text-sm font-medium">Contact number<input name="contact_number" id="customer-contact" class="field mt-1" placeholder="09XXXXXXXXX"></label><label class="text-sm font-medium">Address<input name="address" id="customer-address" class="field mt-1" placeholder="Street, city"></label></div></div>
            <div><h3 class="mb-3 font-display font-semibold">Laundry Information</h3><div class="grid gap-3 md:grid-cols-3"><label class="text-sm font-medium">Weight (kg)<input name="weight_kg" id="weight-kg" type="number" min="0.01" step="0.01" class="field mt-1" required></label><div class="rounded-lg bg-brand-50 p-3 text-sm"><p class="text-xs text-brand-700">Machine capacity</p><strong>8 kg per load</strong></div><div class="rounded-lg bg-brand-50 p-3 text-sm"><p class="text-xs text-brand-700">Number of loads</p><strong id="load-count">0</strong><span class="ml-1 text-xs text-slate-500">(automatic)</span></div></div></div>
            <div><h3 class="mb-3 font-display font-semibold">Service Selection</h3><div class="grid gap-2 md:grid-cols-2">@foreach($services as $service)<label class="service-option flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 p-3 text-sm hover:border-brand-400"><input type="checkbox" name="service_ids[]" value="{{ $service->id }}" data-price="{{ $service->price_per_load ?? $service->price }}" class="service-check"><span class="text-lg">{{ $service->icon }}</span><span class="flex-1"><strong class="block">{{ $service->name }}</strong><small class="text-slate-400">₱{{ number_format((float) ($service->price_per_load ?? $service->price), 2) }} / load</small></span><span class="service-subtotal font-semibold text-brand-700">₱0.00</span></label>@endforeach</div></div>
            <div><div class="mb-3 flex items-center justify-between"><h3 class="font-display font-semibold">Laundry Item Details</h3><button type="button" id="add-item-detail" class="btn-secondary px-3 py-1 text-xs">＋ Add Item</button></div><p class="mb-3 text-xs text-slate-500">Record the individual item types and quantities received.</p><div id="item-details" class="space-y-2"><div class="item-detail grid gap-2 sm:grid-cols-[1fr_140px_auto]"><input name="item_details[0][item_name]" class="field" placeholder="Item name, e.g. Shirt"><input name="item_details[0][quantity]" type="number" min="1" class="field" placeholder="Quantity"><button type="button" class="remove-item text-sm text-red-500">Remove</button></div></div></div>
            <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs uppercase text-slate-500">Total due</p><strong id="total-due" class="text-xl">₱0.00</strong><p class="mt-1 text-xs text-slate-500">Record payments separately in the Payments module.</p></div>
            <div class="flex justify-end gap-3"><button type="button" data-modal-close class="btn-secondary">Cancel</button><button class="btn-primary">Create Order</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
const weightInput = document.getElementById('weight-kg'), loadCount = document.getElementById('load-count'), totalDue = document.getElementById('total-due'), customerSelect = document.getElementById('customer-id'), customerName = document.getElementById('customer-name'), customerContact = document.getElementById('customer-contact'), customerAddress = document.getElementById('customer-address'), itemDetails = document.getElementById('item-details');
function recalculateOrder() {
    const weight = Number(weightInput.value || 0), loads = weight > 0 ? Math.ceil(weight / 8) : 0;
    let total = 0;
    loadCount.textContent = loads;
    document.querySelectorAll('.service-check').forEach((input) => { const subtotal = Number(input.dataset.price) * loads; input.closest('.service-option').querySelector('.service-subtotal').textContent = `₱${subtotal.toFixed(2)}`; if (input.checked) total += subtotal; });
    totalDue.textContent = `₱${total.toFixed(2)}`;
}
weightInput?.addEventListener('input', recalculateOrder); document.querySelectorAll('.service-check').forEach((input) => input.addEventListener('change', recalculateOrder));
let itemDetailIndex = 1;
document.getElementById('add-item-detail')?.addEventListener('click', () => {
    itemDetails.insertAdjacentHTML('beforeend', `<div class="item-detail grid gap-2 sm:grid-cols-[1fr_140px_auto]"><input name="item_details[${itemDetailIndex}][item_name]" class="field" placeholder="Item name, e.g. Shirt"><input name="item_details[${itemDetailIndex}][quantity]" type="number" min="1" class="field" placeholder="Quantity"><button type="button" class="remove-item text-sm text-red-500">Remove</button></div>`);
    itemDetailIndex++;
});
itemDetails?.addEventListener('click', (event) => { if (event.target.classList.contains('remove-item') && itemDetails.children.length > 1) event.target.closest('.item-detail').remove(); });
customerSelect?.addEventListener('change', () => {
    const selected = customerSelect.options[customerSelect.selectedIndex];
    customerName.value = selected.dataset.name || '';
    customerContact.value = selected.dataset.contact || '';
    customerAddress.value = selected.dataset.address || '';
});
document.querySelectorAll('[data-modal-open]').forEach((button) => button.addEventListener('click', () => document.getElementById(button.dataset.modalOpen).classList.replace('hidden', 'flex')));
document.querySelectorAll('[data-modal-close]').forEach((button) => button.addEventListener('click', () => button.closest('[data-modal]').classList.replace('flex', 'hidden')));
</script>
@endpush
@endif
@endsection
