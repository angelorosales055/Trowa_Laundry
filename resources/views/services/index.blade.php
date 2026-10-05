@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>Price Matrix & Shop Configuration</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Settings
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Configure laundry service tariffs, machine pricing per load, and store identity details.
        </p>
    </div>

    <button type="button" data-modal-open="service-modal" class="retro-btn-primary text-xs">
        <span>＋</span>
        <span>Add Service</span>
    </button>
</div>

<!-- Shop Information Panel -->
<div class="retro-panel mb-6 p-5">
    <div class="mb-4 border-b-2 border-[#182830]/15 pb-2.5">
        <h2 class="font-recoleta text-lg font-bold text-[#182830]">Storefront Identity</h2>
        <p class="font-mono text-xs text-[#25799B]">Information printed on receipts and claim stubs</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Shop Name</label>
            <input class="field text-xs" value="Trowa Laundry House">
        </div>
        <div>
            <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Counter Contact Phone</label>
            <input class="field text-xs" value="09171234567">
        </div>
        <div>
            <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Physical Address</label>
            <input class="field text-xs" value="123 Wash St, Laundryville">
        </div>
        <div>
            <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Operating Hours</label>
            <input class="field text-xs" value="07:00 AM – 09:00 PM">
        </div>
    </div>
    <div class="mt-4 flex justify-end">
        <button class="retro-btn-secondary text-xs">Save Shop Info</button>
    </div>
</div>

<!-- Price Management Table -->
<div class="retro-panel overflow-hidden">
    <div class="flex flex-wrap items-center justify-between border-b-2 border-[#182830] bg-[#A2C5D8]/30 px-5 py-4 gap-2">
        <div>
            <h2 class="font-recoleta text-lg font-bold text-[#182830]">Service Price Management</h2>
            <p class="font-mono text-xs text-[#25799B]">Rates apply dynamically to new intakes based on machine load allocations.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/20 font-mono text-xs uppercase tracking-wider text-[#182830]">
                <tr>
                    <th class="px-5 py-3.5 font-extrabold">Service Name</th>
                    <th class="px-5 py-3.5 font-extrabold">Pricing Structure</th>
                    <th class="px-5 py-3.5 font-extrabold">Rate (₱ / Load)</th>
                    <th class="px-5 py-3.5 font-extrabold">Status</th>
                    <th class="px-5 py-3.5 font-extrabold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#182830]/10 bg-[#FFFDF8]">
                @foreach($services as $service)
                    <tr class="transition hover:bg-[#F7E6CB]/30">
                        <form method="POST" action="{{ route('services.update', $service) }}">
                            @csrf @method('PUT')
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#A2C5D8] text-[#182830] shrink-0">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                    </span>
                                    <input name="name" value="{{ $service->name }}" class="field text-xs max-w-xs font-bold font-sans" required>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <select name="pricing_type" class="field text-xs max-w-xs font-mono">
                                    <option value="per_load" @selected($service->pricing_type === 'per_load')>Per load (8kg)</option>
                                    <option value="per_kg" @selected($service->pricing_type === 'per_kg')>Per kg</option>
                                    <option value="flat_rate" @selected($service->pricing_type === 'flat_rate')>Flat rate</option>
                                </select>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="relative max-w-xs">
                                    <span class="absolute left-2.5 top-2 font-mono text-xs font-bold text-slate-500">₱</span>
                                    <input name="price_per_load" type="number" step="0.01" min="0" value="{{ $service->price_per_load ?? $service->price }}" class="field text-xs pl-6 font-mono font-bold" required>
                                </div>
                                <input type="hidden" name="price" value="{{ $service->price }}">
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="badge border-[#182830] {{ $service->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <input type="hidden" name="icon" value="{{ $service->icon }}">
                                <button class="retro-btn-secondary px-3 py-1 text-xs">Save</button>
                        </form>
                                <form method="POST" action="{{ route('services.toggle-active', $service) }}" class="ml-2 inline">
                                    @csrf @method('PATCH')
                                    <button class="font-mono text-xs font-bold text-[#25799B] hover:underline">
                                        {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('services.destroy', $service) }}" class="ml-2 inline" onsubmit="return confirm('Delete this service?');">
                                    @csrf @method('DELETE')
                                    <button class="font-mono text-sm font-bold text-red-500 hover:text-red-700 px-1">×</button>
                                </form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Service Modal -->
<div id="service-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/75 p-4 backdrop-blur-xs">
    <div class="w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[8px_8px_0px_#182830]">
        <div class="mb-4 flex justify-between border-b-2 border-[#182830] pb-3">
            <h2 class="font-recoleta text-2xl font-bold text-[#182830]">Add Laundry Service</h2>
            <button data-modal-close class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-lg font-bold hover:bg-[#CB1B03] hover:text-white transition cursor-pointer">×</button>
        </div>
        <form method="POST" action="{{ route('services.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="icon" value="service">
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Service Name *</label>
                <input name="name" class="field text-xs" placeholder="e.g. Wash & Steam Iron" required>
            </div>
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Pricing Structure</label>
                <select name="pricing_type" class="field text-xs font-mono">
                    <option value="per_load">Per load (8 kg default)</option>
                    <option value="per_kg">Per kg</option>
                    <option value="flat_rate">Flat rate</option>
                </select>
            </div>
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Price per Machine Load (₱) *</label>
                <input name="price_per_load" type="number" min="0" step="0.01" class="field text-xs font-mono font-bold" placeholder="60.00" required>
            </div>
            <input type="hidden" name="price" value="0">
            <div class="flex gap-3 pt-2">
                <button type="button" data-modal-close class="retro-btn-secondary flex-1 text-xs">Cancel</button>
                <button class="retro-btn-primary flex-1 text-xs">Add Service ➔</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-modal-open]').forEach(b=>b.addEventListener('click',()=>{
    const m = document.getElementById(b.dataset.modalOpen);
    if (m) m.classList.replace('hidden','flex');
}));
document.querySelectorAll('[data-modal-close]').forEach(b=>b.addEventListener('click',()=>b.closest('[data-modal]').classList.replace('flex','hidden')));
</script>
@endpush
@endsection
