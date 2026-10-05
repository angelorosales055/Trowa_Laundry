@extends('layouts_app')

@section('content')
<div class="space-y-6">

    <!-- Top Station Header & Breadcrumb -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
                <a href="{{ route('orders.index') }}" class="flex items-center gap-1 hover:text-[#CB1B03] hover:underline transition">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                    </svg>
                    <span>Laundry Queue Line</span>
                </a>
                <span>·</span>
                <span>Counter Intake Terminal</span>
            </div>
            <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
                New Laundry Intake Register
            </h1>
            <p class="text-xs sm:text-sm font-medium text-[#25799B]">
                Weigh customer garments, allocate commercial drums, select treatments, and issue immediate tickets.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830]">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Counter Register Ready</span>
            </span>
            <a href="{{ route('orders.index') }}" class="retro-btn-secondary text-xs">
                View Live Queue ➔
            </a>
        </div>
    </div>

    <!-- Intake Form Grid (8 Cols Formulation / 4 Cols Live Stub & Cashier) -->
    <form method="POST" action="{{ route('orders.add') }}" id="intake-counter-form" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf
        <input type="hidden" name="redirect_to" value="orders.show">

        <!-- LEFT COLUMN: Customer, Weight, Services, Garments (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- 1. 1-Click Popular Treatment Packages Strip -->
            <div class="rounded-3xl border-2 border-[#182830] bg-[#A2C5D8]/20 p-5 shadow-[3px_3px_0px_#182830]">
                <div class="flex items-center justify-between mb-3 border-b border-[#182830]/15 pb-2">
                    <span class="flex items-center gap-2 font-recoleta text-base font-bold text-[#182830]">
                        <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        1-Click Quick Laundry Packages:
                    </span>
                    <span class="font-mono text-[10px] text-slate-500 font-semibold uppercase">Single-tap selection</span>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    <button type="button" class="btn-create-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer text-center" data-bundle="wash-dry-fold">
                        Wash, Dry & Fold
                    </button>
                    <button type="button" class="btn-create-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#25799B] hover:text-white transition cursor-pointer text-center" data-bundle="wash-dry">
                        Wash & Dry Only
                    </button>
                    <button type="button" class="btn-create-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#25799B] hover:text-white transition cursor-pointer text-center" data-bundle="wash-dry-iron">
                        Wash, Dry & Iron
                    </button>
                    <button type="button" class="btn-create-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#25799B] hover:text-white transition cursor-pointer text-center" data-bundle="wash-only">
                        Wash Only
                    </button>
                    <button type="button" class="btn-create-bundle rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-3 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer text-center col-span-2 sm:col-span-1" data-bundle="deluxe">
                        Full Deluxe Care
                    </button>
                </div>
            </div>

            <!-- 2. Customer Identification Card -->
            <div class="rounded-3xl border-2 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[3px_3px_0px_#182830]">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4 border-b-2 border-[#182830]/10 pb-3">
                    <span class="flex items-center gap-2 font-recoleta text-lg font-bold text-[#182830]">
                        <svg class="h-5 w-5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        1. Customer Dossier & Registry
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btn-create-walkin" class="rounded-lg border-2 border-[#182830] bg-[#CB1B03] text-white px-3 py-1 font-mono text-xs font-bold shadow-[1px_1px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer">
                            Quick Walk-in
                        </button>
                        <button type="button" id="btn-create-clear-cust" class="rounded-lg border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] px-3 py-1 font-mono text-xs font-bold shadow-[1px_1px_0px_#182830] hover:bg-slate-200 transition cursor-pointer">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="create-customer-select" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                            Select Existing Customer (Auto-fill)
                        </label>
                        <select name="customer_id" id="create-customer-select" class="field text-xs font-medium">
                            <option value="">-- New Walk-in Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                        data-name="{{ $customer->name }}" 
                                        data-contact="{{ $customer->contact_number ?? $customer->phone }}" 
                                        data-address="{{ $customer->address }}">
                                    {{ $customer->name }} ({{ $customer->contact_number ?? $customer->phone ?? 'No contact' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                                Customer Name <span class="text-[#CB1B03]">*</span>
                            </label>
                            <input type="text" name="customer_name" id="create-customer-name" required placeholder="e.g. Maria Santos" class="field text-xs font-bold">
                        </div>
                        <div>
                            <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                                Contact Phone Number
                            </label>
                            <input type="text" name="contact_number" id="create-customer-contact" placeholder="09XXXXXXXXX" class="field text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                            Delivery Address / Special Instructions
                        </label>
                        <input type="text" name="address" id="create-customer-address" placeholder="Unit #, Street, Barangay, or landmark" class="field text-xs">
                    </div>
                </div>
            </div>

            <!-- 3. Scale Weigh-in & Drum Batching Card -->
            <div class="rounded-3xl border-2 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[3px_3px_0px_#182830]">
                <div class="flex items-center justify-between mb-4 border-b-2 border-[#182830]/10 pb-3">
                    <span class="flex items-center gap-2 font-recoleta text-lg font-bold text-[#182830]">
                        <svg class="h-5 w-5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20"/><path d="M6 6l6-4 6 4"/><path d="M6 18l6 4 6-4"/><path d="M3 10h18"/><path d="M3 14h18"/>
                        </svg>
                        2. Scale Weigh-in & Machine Drum Batching
                    </span>
                    <span class="font-mono text-xs uppercase font-extrabold text-[#182830] bg-[#A2C5D8] px-2.5 py-1 rounded-lg border-2 border-[#182830] shadow-[1px_1px_0px_#182830]">
                        8.0 kg / drum max
                    </span>
                </div>

                <!-- Scale Presets Ribbon -->
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="font-mono text-xs font-bold text-slate-600 uppercase mr-1">Scale Presets:</span>
                    @foreach([3, 5, 8, 10, 16] as $presetKg)
                        <button type="button" class="btn-create-weight rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#F7E6CB] transition cursor-pointer" data-weight="{{ $presetKg }}">
                            {{ $presetKg }} kg {{ $presetKg === 8 ? '(1 Drum)' : ($presetKg === 16 ? '(2 Drums)' : '') }}
                        </button>
                    @endforeach
                    <button type="button" id="btn-create-w-minus" class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-3 py-1.5 font-mono text-xs font-black text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#A2C5D8] cursor-pointer" title="Minus 0.5 kg">
                        -0.5
                    </button>
                    <button type="button" id="btn-create-w-plus" class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-3 py-1.5 font-mono text-xs font-black text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#A2C5D8] cursor-pointer" title="Plus 0.5 kg">
                        +0.5
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                    <div class="sm:col-span-6">
                        <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                            Gross Laundry Scale Weight (KG) <span class="text-[#CB1B03]">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0.01" name="weight_kg" id="create-weight-kg" required placeholder="0.00" class="w-full rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-3 font-mono text-2xl font-black text-[#182830] shadow-[2px_2px_0px_#182830] focus:border-[#CB1B03] focus:outline-none">
                            <span class="absolute right-4 top-3.5 font-mono text-sm font-black uppercase text-[#25799B]">KG</span>
                        </div>
                    </div>

                    <div class="sm:col-span-6 flex gap-3">
                        <div class="flex-1 rounded-2xl border-2 border-[#182830] bg-[#A2C5D8] p-3 text-center shadow-[2px_2px_0px_#182830]">
                            <span class="block font-mono text-[10px] font-black uppercase text-[#182830]">Machine Loads</span>
                            <strong id="create-loads-badge" class="font-mono text-3xl font-black text-[#182830] leading-none block my-1">0</strong>
                            <span class="font-mono text-[10px] uppercase font-bold text-[#182830]/80">Commercial Drums</span>
                        </div>
                        <div class="flex-1 rounded-2xl border-2 border-[#182830] bg-[#25799B] p-3 text-center text-white shadow-[2px_2px_0px_#182830]">
                            <span class="block font-mono text-[10px] font-bold uppercase text-[#A2C5D8]">Cycle Capacity</span>
                            <span id="create-capacity-meter" class="font-mono text-xl font-bold text-[#F7E6CB] block my-1">0% Full</span>
                            <span class="font-mono text-[10px] text-[#A2C5D8] block">Standard Cycle</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Individual Service Treatments Selection -->
            <div class="rounded-3xl border-2 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[3px_3px_0px_#182830]">
                <div class="flex items-center justify-between mb-4 border-b-2 border-[#182830]/10 pb-3">
                    <span class="flex items-center gap-2 font-recoleta text-lg font-bold text-[#182830]">
                        <svg class="h-5 w-5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        3. Service Treatments & Pricing <span class="text-[#CB1B03]">*</span>
                    </span>
                    <span class="font-mono text-xs text-slate-500 font-medium">Select any combination</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1">
                    @foreach($services as $service)
                        @php
                            $price = (float) ($service->price_per_load ?? $service->price);
                            $pricingType = $service->pricing_type ?? 'per_load';
                            $nameLower = strtolower($service->name);
                        @endphp
                        <label class="create-service-card group flex items-center justify-between rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-3 shadow-[2px_2px_0px_#182830] cursor-pointer transition hover:bg-[#F7E6CB]/50 has-checked:border-[#25799B] has-checked:bg-[#A2C5D8]/30">
                            <div class="flex items-center gap-3 min-w-0">
                                <input type="checkbox" 
                                       name="service_ids[]" 
                                       value="{{ $service->id }}" 
                                       data-name="{{ $service->name }}"
                                       data-price="{{ $price }}"
                                       data-pricing="{{ $pricingType }}"
                                       class="create-service-checkbox h-4.5 w-4.5 rounded border-2 border-[#182830] text-[#CB1B03] focus:ring-0">
                                
                                <!-- Natural SVG Vector Icons (No Emojis) -->
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-[#182830] bg-[#F7E6CB] text-[#25799B] group-hover:bg-[#FFFDF8]">
                                    @if(str_contains($nameLower, 'wash') || str_contains($nameLower, 'power'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M8 12c1-1.5 2-1.5 3 0s2 1.5 3 0 2-1.5 3 0"/></svg>
                                    @elseif(str_contains($nameLower, 'dry') && !str_contains($nameLower, 'spin'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 4c0 3 4 3 4 6s-4 3-4 6 4 3 4 6"/><path d="M16 4c0 3 4 3 4 6s-4 3-4 6 4 3 4 6"/></svg>
                                    @elseif(str_contains($nameLower, 'iron'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16h18a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2H9L3 13v3z"/><line x1="3" y1="16" x2="21" y2="16"/></svg>
                                    @elseif(str_contains($nameLower, 'fold'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                                    @elseif(str_contains($nameLower, 'detergent') || str_contains($nameLower, 'soap'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6v3H9z"/><path d="M6 6h12l-1 15H7z"/></svg>
                                    @elseif(str_contains($nameLower, 'fabcon') || str_contains($nameLower, 'softener'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v8"/><path d="M12 10a4 4 0 1 0 4 4"/><circle cx="12" cy="18" r="3"/></svg>
                                    @elseif(str_contains($nameLower, 'bleach'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v6l-4 8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l-4-8V2"/><line x1="8.5" y1="2" x2="15.5" y2="2"/></svg>
                                    @elseif(str_contains($nameLower, 'spin'))
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 9 9"/><path d="M12 21a9 9 0 0 1-9-9"/></svg>
                                    @else
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                                    @endif
                                </div>

                                <div class="truncate">
                                    <strong class="block text-xs font-bold text-[#182830] leading-tight truncate">{{ $service->name }}</strong>
                                    <span class="font-mono text-[11px] text-[#25799B]">
                                        ₱{{ number_format($price, 2) }} / {{ $pricingType === 'per_kg' ? 'kg' : ($pricingType === 'flat_rate' ? 'flat' : 'load') }}
                                    </span>
                                </div>
                            </div>

                            <span class="create-service-subtotal font-mono text-sm font-black text-[#CB1B03] shrink-0 ml-2">
                                ₱0.00
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 5. Interactive Garment Counter (Tap-to-Count Steppers) -->
            <div class="rounded-3xl border-2 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[3px_3px_0px_#182830]">
                <div class="flex items-center justify-between mb-4 border-b-2 border-[#182830]/10 pb-3">
                    <span class="flex items-center gap-2 font-recoleta text-lg font-bold text-[#182830]">
                        <svg class="h-5 w-5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/>
                        </svg>
                        4. Tap-to-Count Garment Breakdown
                    </span>
                    <span id="create-garment-counter-badge" class="font-mono text-xs font-bold text-[#25799B] bg-[#A2C5D8]/40 px-3 py-1 rounded-xl border-2 border-[#182830]">
                        0 pcs counted
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach([
                        'Shirts & Polos',
                        'Pants & Jeans',
                        'Bath Towels',
                        'Bed Sheets',
                        'Blankets / Comforter',
                        'Delicates / Underwear'
                    ] as $cat)
                        <div class="create-stepper-card flex items-center justify-between rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                            <span class="font-mono text-xs font-bold text-[#182830] truncate pr-1">{{ $cat }}</span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" class="btn-create-step-dec flex h-7 w-7 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#F7E6CB] font-mono text-xs font-black text-[#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer" data-cat="{{ $cat }}">-</button>
                                <span class="create-step-val font-mono text-xs font-black text-[#182830] w-6 text-center" data-cat="{{ $cat }}">0</span>
                                <button type="button" class="btn-create-step-inc flex h-7 w-7 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#25799B] font-mono text-xs font-black text-white hover:bg-[#1E6482] transition cursor-pointer" data-cat="{{ $cat }}">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Hidden inputs for form submission -->
                <div id="create-item-rows" class="hidden"></div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Sticky Official Receipt Stub & Counter Tender (4 cols) -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">

            <!-- Tactile Laundromat Receipt Stub Preview -->
            <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-5 shadow-[4px_4px_0px_#182830] relative overflow-hidden">
                <!-- Perforated top receipt border -->
                <div class="border-b-2 border-dashed border-[#182830] pb-3 mb-4 text-center">
                    <span class="font-mono text-[11px] font-black tracking-widest uppercase text-[#25799B]">TROWA LAUNDRY SYSTEM</span>
                    <h3 class="font-recoleta text-xl font-black text-[#182830] mt-0.5">Official Intake Slip</h3>
                    <div class="flex items-center justify-between font-mono text-[11px] text-slate-500 mt-2 px-1">
                        <span>DATE: {{ now()->format('Y-m-d') }}</span>
                        <span>REG: DRAFT TICKET</span>
                    </div>
                </div>

                <!-- Live Summary Ticket Lines -->
                <div class="space-y-2.5 text-xs font-mono">
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-600">Client:</span>
                        <strong id="create-stub-customer" class="font-bold text-[#182830] truncate max-w-[170px]">Walk-in Guest</strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-600">Weight & Loads:</span>
                        <strong id="create-stub-weight-loads" class="font-bold text-[#182830]">0.0 kg (0 Loads)</strong>
                    </div>
                    
                    <div>
                        <span class="block text-[11px] font-bold text-[#25799B] mb-1">Services Breakdown:</span>
                        <ul id="create-stub-services-list" class="space-y-1 text-xs text-slate-700 max-h-32 overflow-y-auto pr-1">
                            <li class="italic text-slate-400">No services selected yet.</li>
                        </ul>
                    </div>

                    <div class="flex justify-between border-t border-slate-200 pt-1.5 text-xs">
                        <span class="text-slate-600">Garment Pieces:</span>
                        <span id="create-stub-garment-count" class="font-bold text-[#182830]">0 items listed</span>
                    </div>

                    <!-- Total Price Display -->
                    <div class="mt-4 pt-3 border-t-2 border-dashed border-[#182830] flex items-center justify-between">
                        <span class="font-recoleta text-base font-bold text-[#182830]">Grand Total:</span>
                        <strong id="create-grand-total" class="font-recoleta text-3xl font-black text-[#CB1B03]">₱0.00</strong>
                    </div>
                </div>
            </div>

            <!-- Counter Register Tender Card -->
            <div class="rounded-3xl border-3 border-[#182830] bg-[#F7E6CB] p-5 shadow-[4px_4px_0px_#182830]">
                <div class="flex items-center justify-between mb-3 border-b-2 border-[#182830]/15 pb-2">
                    <span class="flex items-center gap-2 font-recoleta text-base font-bold text-[#182830]">
                        <svg class="h-5 w-5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/>
                        </svg>
                        Intake Settlement Tender
                    </span>
                    <span class="font-mono text-[10px] font-extrabold text-[#CB1B03] uppercase">Counter Register</span>
                </div>

                <!-- Tender Timing Radio -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <label class="flex-1 flex items-center justify-center gap-1.5 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] py-2 px-3 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] cursor-pointer has-checked:bg-[#25799B] has-checked:text-white transition">
                            <input type="radio" name="payment_timing" value="now" id="create-pay-now-radio" checked class="hidden">
                            <span>Tender Now</span>
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-1.5 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] py-2 px-3 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] cursor-pointer has-checked:bg-[#182830] has-checked:text-white transition">
                            <input type="radio" name="payment_timing" value="pickup" id="create-pay-pickup-radio" class="hidden">
                            <span>Pay on Claim</span>
                        </label>
                    </div>

                    <div id="create-tender-inputs-box" class="space-y-3">
                        <!-- Quick Cash Tender Buttons -->
                        <div>
                            <span class="block font-mono text-[10px] font-bold uppercase text-slate-700 mb-1">Quick Cash Presets</span>
                            <div class="grid grid-cols-4 gap-1.5">
                                <button type="button" id="btn-create-tender-exact" class="rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer">
                                    Exact
                                </button>
                                <button type="button" class="btn-create-tender-preset rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="100">
                                    ₱100
                                </button>
                                <button type="button" class="btn-create-tender-preset rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="200">
                                    ₱200
                                </button>
                                <button type="button" class="btn-create-tender-preset rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="500">
                                    ₱500
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-mono text-[11px] font-bold uppercase text-[#182830] mb-1">Cash Tender (₱)</label>
                                <input type="number" step="0.01" min="0" id="create-tender-input" placeholder="0.00" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-base font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none focus:border-[#25799B]">
                                <input type="hidden" name="amount_paid" id="create-hidden-amount-paid" value="0">
                                <input type="hidden" name="tendered_amount" id="create-hidden-tendered-amount" value="0">
                            </div>
                            <div>
                                <label class="block font-mono text-[11px] font-bold uppercase text-[#182830] mb-1">Method</label>
                                <select name="payment_method" id="create-payment-method" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-2 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none focus:border-[#25799B]">
                                    <option value="cash">Cash Tender</option>
                                    <option value="gcash">GCash E-Wallet</option>
                                    <option value="other">Other / Bank</option>
                                </select>
                            </div>
                        </div>

                        <!-- Reference Input for E-Wallets -->
                        <div id="create-ref-container" class="hidden">
                            <label class="block font-mono text-[10px] font-bold uppercase text-[#182830] mb-1">Transaction Ref #</label>
                            <input type="text" name="reference_number" id="create-ref-number" placeholder="GCash Ref # or OR #" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
                        </div>

                        <!-- Dynamic Change Feedback -->
                        <div id="create-change-feedback-box" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-center shadow-[1px_1px_0px_#182830]">
                            <span class="block font-mono text-[10px] font-bold uppercase text-slate-500">Change Due to Customer</span>
                            <strong id="create-change-due-val" class="font-mono text-xl font-black text-emerald-700">₱0.00</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Submission Buttons -->
            <div class="space-y-2 pt-1">
                <button type="submit" id="btn-create-submit" class="w-full rounded-2xl border-3 border-[#182830] bg-[#CB1B03] py-3.5 px-5 font-recoleta text-base font-black text-white shadow-[4px_4px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer flex items-center justify-center gap-2">
                    <span>Queue & Print Laundry Ticket ➔</span>
                </button>
                <a href="{{ route('orders.index') }}" class="block text-center rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] py-2.5 font-recoleta text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition">
                    Cancel & Return to Queue
                </a>
            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const weightInput = document.getElementById('create-weight-kg');
    const loadsBadge = document.getElementById('create-loads-badge');
    const capacityMeter = document.getElementById('create-capacity-meter');
    const customerSelect = document.getElementById('create-customer-select');
    const customerName = document.getElementById('create-customer-name');
    const customerContact = document.getElementById('create-customer-contact');
    const customerAddress = document.getElementById('create-customer-address');
    const stubCustomer = document.getElementById('create-stub-customer');
    const stubWeightLoads = document.getElementById('create-stub-weight-loads');
    const stubServicesList = document.getElementById('create-stub-services-list');
    const stubGarmentCount = document.getElementById('create-stub-garment-count');
    const grandTotalEl = document.getElementById('create-grand-total');
    const tenderInput = document.getElementById('create-tender-input');
    const hiddenAmountPaid = document.getElementById('create-hidden-amount-paid');
    const hiddenTenderedAmount = document.getElementById('create-hidden-tendered-amount');
    const changeDueVal = document.getElementById('create-change-due-val');
    const changeBox = document.getElementById('create-change-feedback-box');
    const methodSelect = document.getElementById('create-payment-method');
    const refContainer = document.getElementById('create-ref-container');
    const payNowRadio = document.getElementById('create-pay-now-radio');
    const payPickupRadio = document.getElementById('create-pay-pickup-radio');
    const tenderBox = document.getElementById('create-tender-inputs-box');
    const serviceCheckboxes = document.querySelectorAll('.create-service-checkbox');
    const btnWalkin = document.getElementById('btn-create-walkin');
    const btnClearCust = document.getElementById('btn-create-clear-cust');
    const btnWMinus = document.getElementById('btn-create-w-minus');
    const btnWPlus = document.getElementById('btn-create-w-plus');
    const btnTenderExact = document.getElementById('btn-create-tender-exact');
    const itemRowsContainer = document.getElementById('create-item-rows');

    let garmentCounts = {};
    let calculatedGrandTotal = 0;

    function recalculateIntake() {
        const weight = parseFloat(weightInput?.value) || 0;
        const loads = weight > 0 ? Math.ceil(weight / 8) : 0;

        if (loadsBadge) loadsBadge.textContent = loads;
        if (capacityMeter) {
            if (loads === 0) {
                capacityMeter.textContent = '0% Full';
            } else {
                const remainder = weight % 8;
                const fillPct = remainder === 0 ? 100 : Math.round((remainder / 8) * 100);
                capacityMeter.textContent = `${fillPct}% Full`;
            }
        }

        if (stubWeightLoads) {
            stubWeightLoads.textContent = `${weight.toFixed(1)} kg (${loads} Load${loads === 1 ? '' : 's'})`;
        }

        let total = 0;
        const selectedServices = [];

        serviceCheckboxes.forEach(cb => {
            const card = cb.closest('.create-service-card');
            const subtotalEl = card?.querySelector('.create-service-subtotal');
            const price = parseFloat(cb.dataset.price) || 0;
            const pricing = cb.dataset.pricing || 'per_load';
            const name = cb.dataset.name || 'Service';

            let lineSubtotal = 0;
            if (pricing === 'per_load') {
                lineSubtotal = loads * price;
            } else if (pricing === 'per_kg') {
                lineSubtotal = weight * price;
            } else {
                lineSubtotal = price;
            }

            if (subtotalEl) {
                subtotalEl.textContent = `₱${lineSubtotal.toFixed(2)}`;
            }

            if (cb.checked) {
                total += lineSubtotal;
                selectedServices.push({ name, subtotal: lineSubtotal });
            }
        });

        calculatedGrandTotal = total;
        if (grandTotalEl) grandTotalEl.textContent = `₱${total.toFixed(2)}`;

        // Render Live Ticket Stub
        if (stubServicesList) {
            if (selectedServices.length === 0) {
                stubServicesList.innerHTML = '<li class="italic text-slate-400">No services selected yet.</li>';
            } else {
                stubServicesList.innerHTML = selectedServices.map(s => `
                    <li class="flex justify-between py-0.5 border-b border-slate-100">
                        <span class="truncate pr-1">${s.name}</span>
                        <strong class="font-bold text-[#182830]">₱${s.subtotal.toFixed(2)}</strong>
                    </li>
                `).join('');
            }
        }

        recalculateChange();
    }

    function recalculateChange() {
        if (payPickupRadio?.checked) {
            if (hiddenAmountPaid) hiddenAmountPaid.value = '0';
            if (hiddenTenderedAmount) hiddenTenderedAmount.value = '0';
            if (tenderBox) tenderBox.classList.add('hidden');
            return;
        } else {
            if (tenderBox) tenderBox.classList.remove('hidden');
        }

        const rawTenderVal = tenderInput?.value ? parseFloat(tenderInput.value) : null;
        const tender = rawTenderVal !== null && !isNaN(rawTenderVal) ? rawTenderVal : 0;
        const amountToPay = Math.min(tender, calculatedGrandTotal);
        if (hiddenAmountPaid) hiddenAmountPaid.value = amountToPay.toFixed(2);
        if (hiddenTenderedAmount) hiddenTenderedAmount.value = tender.toFixed(2);

        const change = tender - calculatedGrandTotal;
        if (change >= 0) {
            if (changeDueVal) {
                changeDueVal.textContent = `₱${change.toFixed(2)}`;
                changeDueVal.className = 'font-mono text-xl font-black text-emerald-700';
            }
            if (changeBox) changeBox.className = 'rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-center shadow-[1px_1px_0px_#182830]';
        } else {
            const short = Math.abs(change);
            if (changeDueVal) {
                changeDueVal.textContent = `Short ₱${short.toFixed(2)}`;
                changeDueVal.className = 'font-mono text-xs font-bold text-red-600';
            }
            if (changeBox) changeBox.className = 'rounded-xl border-2 border-red-500 bg-red-50 p-3 text-center shadow-[1px_1px_0px_#182830]';
        }
    }

    // Weight Listeners & Steppers
    weightInput?.addEventListener('input', recalculateIntake);
    document.querySelectorAll('.btn-create-weight').forEach(btn => {
        btn.addEventListener('click', () => {
            if (weightInput) {
                weightInput.value = btn.dataset.weight;
                recalculateIntake();
            }
        });
    });

    btnWMinus?.addEventListener('click', () => {
        if (weightInput) {
            const cur = parseFloat(weightInput.value) || 0;
            weightInput.value = Math.max(0.5, (cur - 0.5)).toFixed(1);
            recalculateIntake();
        }
    });

    btnWPlus?.addEventListener('click', () => {
        if (weightInput) {
            const cur = parseFloat(weightInput.value) || 0;
            weightInput.value = (cur + 0.5).toFixed(1);
            recalculateIntake();
        }
    });

    // Customer Select & Walk-in
    customerSelect?.addEventListener('change', () => {
        const selected = customerSelect.options[customerSelect.selectedIndex];
        if (selected && selected.value) {
            if (customerName) customerName.value = selected.dataset.name || '';
            if (customerContact) customerContact.value = selected.dataset.contact || '';
            if (customerAddress) customerAddress.value = selected.dataset.address || '';
            if (stubCustomer) stubCustomer.textContent = selected.dataset.name || 'Walk-in Guest';
        } else {
            if (customerName) customerName.value = '';
            if (customerContact) customerContact.value = '';
            if (customerAddress) customerAddress.value = '';
            if (stubCustomer) stubCustomer.textContent = 'Walk-in Guest';
        }
    });

    customerName?.addEventListener('input', () => {
        if (stubCustomer) stubCustomer.textContent = customerName.value.trim() || 'Walk-in Guest';
    });

    btnWalkin?.addEventListener('click', () => {
        if (customerSelect) customerSelect.value = '';
        if (customerName) customerName.value = 'Walk-in Guest';
        if (customerContact) customerContact.value = '';
        if (customerAddress) customerAddress.value = '';
        if (stubCustomer) stubCustomer.textContent = 'Walk-in Guest';
        weightInput?.focus();
    });

    btnClearCust?.addEventListener('click', () => {
        if (customerSelect) customerSelect.value = '';
        if (customerName) customerName.value = '';
        if (customerContact) customerContact.value = '';
        if (customerAddress) customerAddress.value = '';
        if (stubCustomer) stubCustomer.textContent = 'Walk-in Guest';
    });

    // Service Checkboxes
    serviceCheckboxes.forEach(cb => cb.addEventListener('change', recalculateIntake));

    // 1-Click Quick Treatment Packages
    document.querySelectorAll('.btn-create-bundle').forEach(btn => {
        btn.addEventListener('click', () => {
            const bundle = btn.dataset.bundle;
            serviceCheckboxes.forEach(cb => {
                const name = (cb.dataset.name || '').toLowerCase();
                if (bundle === 'wash-dry-fold') {
                    cb.checked = name.includes('wash') || name.includes('dry') || name.includes('fold');
                } else if (bundle === 'wash-dry') {
                    cb.checked = (name.includes('wash') || name.includes('dry')) && !name.includes('iron') && !name.includes('fold');
                } else if (bundle === 'wash-dry-iron') {
                    cb.checked = name.includes('wash') || name.includes('dry') || name.includes('iron');
                } else if (bundle === 'wash-only') {
                    cb.checked = name.includes('wash') && !name.includes('dry') && !name.includes('iron') && !name.includes('fold');
                } else if (bundle === 'deluxe') {
                    cb.checked = true;
                }
            });
            recalculateIntake();
        });
    });

    // Garment Counters
    function updateGarmentInputs() {
        if (!itemRowsContainer) return;
        itemRowsContainer.innerHTML = '';
        let totalPieces = 0;
        let index = 0;

        Object.keys(garmentCounts).forEach(cat => {
            const qty = garmentCounts[cat] || 0;
            if (qty > 0) {
                totalPieces += qty;
                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `item_details[${index}][item_name]`;
                nameInput.value = cat;

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `item_details[${index}][quantity]`;
                qtyInput.value = qty;

                itemRowsContainer.appendChild(nameInput);
                itemRowsContainer.appendChild(qtyInput);
                index++;
            }
        });

        const badge = document.getElementById('create-garment-counter-badge');
        if (badge) badge.textContent = `${totalPieces} pcs counted`;
        if (stubGarmentCount) stubGarmentCount.textContent = `${totalPieces} items listed`;
    }

    document.querySelectorAll('.btn-create-step-inc').forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.cat;
            garmentCounts[cat] = (garmentCounts[cat] || 0) + 1;
            const valEl = document.querySelector(`.create-step-val[data-cat="${cat}"]`);
            if (valEl) valEl.textContent = garmentCounts[cat];
            updateGarmentInputs();
        });
    });

    document.querySelectorAll('.btn-create-step-dec').forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.cat;
            if ((garmentCounts[cat] || 0) > 0) {
                garmentCounts[cat]--;
                const valEl = document.querySelector(`.create-step-val[data-cat="${cat}"]`);
                if (valEl) valEl.textContent = garmentCounts[cat];
                updateGarmentInputs();
            }
        });
    });

    // Tender Listeners
    tenderInput?.addEventListener('input', recalculateChange);
    payNowRadio?.addEventListener('change', recalculateChange);
    payPickupRadio?.addEventListener('change', recalculateChange);

    btnTenderExact?.addEventListener('click', () => {
        if (tenderInput) {
            tenderInput.value = calculatedGrandTotal.toFixed(2);
            recalculateChange();
        }
    });

    document.querySelectorAll('.btn-create-tender-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            if (tenderInput) {
                tenderInput.value = parseFloat(btn.dataset.amount).toFixed(2);
                recalculateChange();
            }
        });
    });

    methodSelect?.addEventListener('change', function() {
        if (this.value === 'gcash' || this.value === 'other') {
            refContainer?.classList.remove('hidden');
        } else {
            refContainer?.classList.add('hidden');
        }
    });

    // Form submit validation & auto-tender fallback
    const intakeForm = document.getElementById('intake-counter-form');
    intakeForm?.addEventListener('submit', function(e) {
        const checkedServices = document.querySelectorAll('.create-service-checkbox:checked');
        if (checkedServices.length === 0) {
            e.preventDefault();
            alert('Please select at least one laundry service treatment (or tap a Quick Package).');
            return false;
        }

        if (payNowRadio?.checked) {
            const rawTender = parseFloat(tenderInput?.value);
            if (isNaN(rawTender) || rawTender <= 0) {
                // If cashier picked "Tender Now" but didn't type an amount, assume paid in full with exact total
                if (tenderInput) tenderInput.value = calculatedGrandTotal.toFixed(2);
                if (hiddenAmountPaid) hiddenAmountPaid.value = calculatedGrandTotal.toFixed(2);
                if (hiddenTenderedAmount) hiddenTenderedAmount.value = calculatedGrandTotal.toFixed(2);
            } else {
                if (hiddenAmountPaid) hiddenAmountPaid.value = Math.min(rawTender, calculatedGrandTotal).toFixed(2);
                if (hiddenTenderedAmount) hiddenTenderedAmount.value = rawTender.toFixed(2);
            }
        } else {
            if (hiddenAmountPaid) hiddenAmountPaid.value = '0';
            if (hiddenTenderedAmount) hiddenTenderedAmount.value = '0';
        }
    });

    // Default: auto-select first service (e.g. Wash) or bundle
    if (serviceCheckboxes.length > 0) {
        serviceCheckboxes[0].checked = true;
    }
    recalculateIntake();
});
</script>
@endpush
@endsection
