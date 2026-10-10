@php
    $modalCustomers = $customers ?? collect();
    $modalServices = $services ?? collect();
    $modalInventory = $inventoryItems ?? \App\Models\InventoryItem::where('is_active', true)->orderBy('name')->get();
@endphp

<!-- Step-by-Step Multi-Step Laundry Intake Wizard Modal -->
<div id="order-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/80 p-2 sm:p-4 backdrop-blur-xs overflow-y-auto">
    <div class="relative my-auto w-full max-w-4xl max-h-[94vh] flex flex-col rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] shadow-[8px_8px_0px_#182830] overflow-hidden">
        
        <!-- Modal Top Title Strip -->
        <div class="flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] px-5 py-3 text-[#FFFDF8]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                    <!-- Natural Laundromat Machine Vector SVG -->
                    <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" fill="#FFFDF8"/>
                        <circle cx="12" cy="13" r="5" stroke="#25799B" stroke-width="2"/>
                        <path d="M10 13c.5-.8 1.5-.8 2 0s1.5.8 2 0" stroke="#CB1B03" stroke-width="2"/>
                        <circle cx="7" cy="6.5" r="1" fill="#25799B"/>
                        <circle cx="10" cy="6.5" r="1" fill="#25799B"/>
                        <line x1="14" y1="6.5" x2="17" y2="6.5" stroke="#25799B" stroke-width="1.5"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-recoleta text-lg sm:text-xl font-black text-[#F7E6CB] leading-tight">Fast Laundry Intake Wizard</h2>
                    <p class="font-mono text-[10px] sm:text-[11px] uppercase tracking-wider text-[#A2C5D8]">Interactive Transaction Step-by-Step Terminal</p>
                </div>
            </div>

            <button type="button" data-modal-close class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-lg font-black text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer" aria-label="Close modal">
                ×
            </button>
        </div>

        <!-- Visual Progression Bar & Tactile Step Tabs Strip -->
        <div class="border-b-2 border-[#182830] bg-[#F7E6CB]/60 px-4 sm:px-6 py-3">
            <!-- Animated Progress Bar -->
            <div class="mb-3">
                <div class="flex items-center justify-between text-[11px] font-mono font-bold text-[#182830] mb-1">
                    <span id="wizard-progress-step-text" class="uppercase text-[#25799B]">Step 1 of 5: Customer Dossier</span>
                    <span id="wizard-progress-percentage" class="text-[#CB1B03]">20% Complete</span>
                </div>
                <div class="h-2.5 w-full rounded-full border-2 border-[#182830] bg-[#FFFDF8] p-0.5 shadow-[1px_1px_0px_#182830]">
                    <div id="wizard-progress-fill" class="h-full rounded-full bg-[#CB1B03] transition-all duration-300" style="width: 20%;"></div>
                </div>
            </div>

            <!-- Step Navigation Indicators / Interactive Pills -->
            <div class="grid grid-cols-5 gap-1.5 sm:gap-2">
                <button type="button" class="wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition shadow-[2px_2px_0px_#182830]" data-step="1">
                    <span class="block text-xs sm:text-sm font-black">1</span>
                    <span class="hidden sm:inline">Customer</span>
                </button>
                <button type="button" class="wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition hover:bg-amber-100" data-step="2">
                    <span class="block text-xs sm:text-sm font-black">2</span>
                    <span class="hidden sm:inline">Weight & Service</span>
                </button>
                <button type="button" class="wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition hover:bg-amber-100" data-step="3">
                    <span class="block text-xs sm:text-sm font-black">3</span>
                    <span class="hidden sm:inline">Garments</span>
                </button>
                <button type="button" class="wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition hover:bg-amber-100" data-step="4">
                    <span class="block text-xs sm:text-sm font-black">4</span>
                    <span class="hidden sm:inline">Soap & Soap Type</span>
                </button>
                <button type="button" class="wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition hover:bg-amber-100" data-step="5">
                    <span class="block text-xs sm:text-sm font-black">5</span>
                    <span class="hidden sm:inline">Review & Pay</span>
                </button>
            </div>
        </div>

        <!-- Multi-Step Form Body -->
        <form method="POST" action="{{ route('orders.add') }}" id="wizard-intake-form" class="flex-1 flex flex-col overflow-hidden bg-[#FFFDF8]">
            @csrf
            <input type="hidden" name="redirect_to" value="orders.show">
            <input type="hidden" name="amount_paid" id="wizard-hidden-amount-paid" value="0">
            <input type="hidden" name="tendered_amount" id="wizard-hidden-tendered-amount" value="0">

            <!-- Dynamic Hidden Garment Inputs Container -->
            <div id="wizard-hidden-item-rows"></div>

            <!-- Scrollable Content Viewport -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6">

                <!-- STEP 1: Customer Profile -->
                <div id="wizard-pane-1" class="wizard-pane space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b-2 border-[#182830]/15 pb-3">
                        <div>
                            <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 1 of 5</span>
                            <h3 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Customer Dossier & Identification</h3>
                            <p class="text-xs font-medium text-[#25799B]">Select an existing customer for automatic autofill, or register a quick walk-in.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="wizard-btn-walkin" class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white px-3 py-1.5 font-mono text-xs font-bold shadow-[2px_2px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer">
                                Quick Walk-in Guest
                            </button>
                            <button type="button" id="wizard-btn-clear-cust" class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] px-3 py-1.5 font-mono text-xs font-bold shadow-[2px_2px_0px_#182830] hover:bg-slate-200 transition cursor-pointer">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="wizard-customer-select" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                                Select Existing Customer (Autofill)
                            </label>
                            <select name="customer_id" id="wizard-customer-select" class="field text-xs font-medium">
                                <option value="">-- Choose Existing Customer or Enter New Below --</option>
                                @foreach($modalCustomers as $customer)
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
                                    Customer Full Name <span class="text-[#CB1B03]">*</span>
                                </label>
                                <input type="text" name="customer_name" id="wizard-customer-name" required placeholder="e.g. Maria Santos" class="field text-xs font-bold">
                                <p id="wizard-customer-name-error" class="hidden text-[11px] font-mono text-red-600 mt-1">Please specify the customer name.</p>
                            </div>
                            <div>
                                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                                    Contact Phone Number
                                </label>
                                <input type="text" name="contact_number" id="wizard-customer-contact" placeholder="09XXXXXXXXX" class="field text-xs font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">
                                Delivery Address / Customer Notes (Optional)
                            </label>
                            <input type="text" name="address" id="wizard-customer-address" placeholder="Unit #, Street, Barangay, or landmark" class="field text-xs">
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Scale Weight & Services -->
                <div id="wizard-pane-2" class="wizard-pane hidden space-y-5">
                    <div class="border-b-2 border-[#182830]/15 pb-3">
                        <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 2 of 5</span>
                        <h3 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Scale Weigh-in & Treatment Services</h3>
                        <p class="text-xs font-medium text-[#25799B]">Weigh customer items in kilograms. Drums and pricing auto-calculate live.</p>
                    </div>

                    <!-- Kilogram Weight Card -->
                    <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                            <div>
                                <label class="block font-mono text-xs font-bold uppercase text-[#182830]">Gross Laundry Scale Weight</label>
                                <span class="text-[11px] text-[#25799B]">Standard commercial drum capacity: 8 kg per load</span>
                            </div>
                            <!-- Live Drum Batching Badge -->
                            <div class="inline-flex items-center gap-2 rounded-xl border-2 border-[#182830] bg-[#A2C5D8]/30 px-3 py-1 font-mono text-xs font-extrabold text-[#182830]">
                                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="12" cy="13" r="4.5"/></svg>
                                <span>Batch: <strong id="wizard-loads-badge" class="text-base text-[#CB1B03]">1</strong> Commercial Load(s)</span>
                            </div>
                        </div>

                        <!-- Steppers & Direct Input -->
                        <div class="flex items-center gap-2 mb-3">
                            <button type="button" id="wizard-btn-w-minus" class="h-11 w-11 rounded-xl border-2 border-[#182830] bg-[#F7E6CB] font-mono text-lg font-black text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-200 transition cursor-pointer">
                                −
                            </button>
                            <div class="relative flex-1">
                                <input type="number" step="0.1" min="0.5" id="wizard-weight-kg" name="weight_kg" value="8.0" required class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xl font-black text-[#182830] text-center shadow-[2px_2px_0px_#182830] focus:border-[#25799B] focus:outline-none">
                                <span class="absolute right-3.5 top-2.5 font-mono text-xs font-bold text-slate-500">KG</span>
                            </div>
                            <button type="button" id="wizard-btn-w-plus" class="h-11 w-11 rounded-xl border-2 border-[#182830] bg-[#F7E6CB] font-mono text-lg font-black text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-200 transition cursor-pointer">
                                ＋
                            </button>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="font-mono text-[10px] font-bold uppercase text-slate-500 mr-1">Scale Presets:</span>
                            @foreach([3.0, 5.0, 8.0, 10.0, 16.0] as $preset)
                                <button type="button" class="wizard-btn-preset-weight rounded-lg border border-[#182830] bg-[#FFFDF8] px-2.5 py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-weight="{{ $preset }}">
                                    {{ $preset }} kg
                                </button>
                            @endforeach
                        </div>

                        <!-- 8 Washing Machines Fleet Limit Warning Banner -->
                        <div id="wizard-capacity-warning" class="hidden mt-3 rounded-2xl border-2 border-[#CB1B03] bg-red-50 p-3.5 text-[#182830] shadow-[3px_3px_0px_#CB1B03]">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border-2 border-[#CB1B03] bg-[#CB1B03] text-white font-mono font-black text-lg">
                                    !
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-recoleta text-sm font-bold text-[#CB1B03] leading-tight flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 22 22 22 12 2"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                        <span>Fleet Limit Warning: Maximum 8 Washing Machines</span>
                                    </h4>
                                    <p class="text-xs font-medium text-slate-700 leading-snug">
                                        Our laundromat has a total fleet of <strong>8 washing machines</strong> (1 commercial machine = 8.0 kg max; maximum shop batch is <strong>64.0 kg</strong>).
                                        Your entered weight (<span id="wizard-warning-kg-val" class="font-mono font-black text-[#CB1B03]">0</span> kg) requires 
                                        <span id="wizard-warning-loads-val" class="font-mono font-black text-[#CB1B03]">0</span> machines, which exceeds the number of washing machines in the shop!
                                    </p>
                                    <p class="font-mono text-[11px] font-extrabold text-[#CB1B03]">
                                        Please decrease the weight to ≤ 64.0 kg or split this customer order into separate batches.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 1-Click Quick Packages Strip -->
                    <div class="rounded-2xl border-2 border-[#182830] bg-[#A2C5D8]/20 p-3.5 shadow-[2px_2px_0px_#182830]">
                        <span class="block font-mono text-xs font-bold uppercase text-[#182830] mb-2">1-Click Laundry Packages:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                            <button type="button" class="wizard-btn-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer text-center" data-bundle="wash-dry-fold">
                                Wash, Dry & Fold
                            </button>
                            <button type="button" class="wizard-btn-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#25799B] hover:text-white transition cursor-pointer text-center" data-bundle="wash-dry">
                                Wash & Dry Only
                            </button>
                            <button type="button" class="wizard-btn-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#25799B] hover:text-white transition cursor-pointer text-center" data-bundle="wash-dry-iron">
                                Wash, Dry & Iron
                            </button>
                            <button type="button" class="wizard-btn-bundle rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#25799B] hover:text-white transition cursor-pointer text-center" data-bundle="wash-only">
                                Wash Only
                            </button>
                            <button type="button" class="wizard-btn-bundle rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-2 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer text-center col-span-2 sm:col-span-1" data-bundle="deluxe">
                                Full Deluxe Care
                            </button>
                        </div>
                    </div>

                    <!-- Individual Services Checkboxes Grid -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-mono text-xs font-bold uppercase text-[#182830]">
                                Select Applicable Services <span class="text-[#CB1B03]">*</span>
                            </label>
                            <span class="font-mono text-xs font-bold text-[#25799B]">
                                Estimated Subtotal: <strong id="wizard-live-subtotal" class="text-sm font-black text-[#CB1B03]">₱0.00</strong>
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto p-1">
                            @foreach($modalServices as $service)
                                @php $rate = (float)($service->price_per_load ?? $service->price); @endphp
                                <label class="wizard-service-label flex items-center justify-between rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-xs shadow-[2px_2px_0px_#182830] hover:bg-amber-50 cursor-pointer transition">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" 
                                               class="wizard-service-cb h-4 w-4 rounded border-2 border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer"
                                               data-price="{{ $rate }}"
                                               data-type="{{ $service->pricing_type }}"
                                               data-name="{{ $service->name }}">
                                        <div>
                                            <strong class="font-sans text-[#182830] block leading-tight">{{ $service->name }}</strong>
                                            <span class="font-mono text-[10px] text-slate-500 uppercase">{{ $service->pricing_type === 'per_kg' ? 'Per KG rate' : ($service->pricing_type === 'flat_rate' ? 'Flat rate' : 'Per Load rate') }}</span>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-[#25799B]">₱{{ number_format($rate, 2) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p id="wizard-services-error" class="hidden text-[11px] font-mono text-red-600 mt-1">Please check at least one laundry service.</p>
                    </div>
                </div>

                <!-- STEP 3: Garments Checkboxes & Quantity Steppers -->
                <div id="wizard-pane-3" class="wizard-pane hidden space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b-2 border-[#182830]/15 pb-3">
                        <div>
                            <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 3 of 5</span>
                            <h3 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Garment Inventory & Counts</h3>
                            <p class="text-xs font-medium text-[#25799B]">Check off items in the laundry bag and tap plus/minus for quantity counts.</p>
                        </div>
                        <span id="wizard-garments-summary-badge" class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-3 py-1 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830]">
                            0 items counted
                        </span>
                    </div>

                    <!-- Tap-to-Check & Count Grid for Garment Types -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="wizard-garments-list">
                        @php
                            $garmentCategories = [
                                ['Tops / Shirts', 'T-Shirts, Polos & Blouses', 'shirt'],
                                ['Bottoms / Pants', 'Pants, Jeans & Trousers', 'pants'],
                                ['Undergarments', 'Briefs, Panties, Bras & Socks', 'boxers'],
                                ['Towels', 'Bath Towels & Hand Towels', 'towel'],
                                ['Beddings / Linens', 'Bed Sheets, Blankets & Pillows', 'bed'],
                                ['Jackets / Hoodies', 'Jackets, Sweaters & Coats', 'jacket'],
                                ['Delicates / Silks', 'Delicates, Dresses & Fine Fabrics', 'sparkles'],
                                ['Uniforms', 'School & Office Uniforms', 'badge'],
                            ];
                        @endphp

                        @foreach($garmentCategories as [$catName, $catDesc, $catIcon])
                            <div class="wizard-garment-card flex items-center justify-between rounded-2xl border-2 border-[#182830]/30 bg-[#FFFDF8] p-3 transition" data-cat="{{ $catName }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <input type="checkbox" class="wizard-garment-check h-5 w-5 rounded-md border-2 border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer" data-cat="{{ $catName }}">
                                    <div class="min-w-0">
                                        <p class="font-recoleta text-sm font-bold text-[#182830] truncate">{{ $catName }}</p>
                                        <p class="font-mono text-[10px] text-slate-500 truncate">{{ $catDesc }}</p>
                                    </div>
                                </div>

                                <!-- Quantity Steppers (Enabled when checked) -->
                                <div class="wizard-stepper-box flex items-center gap-1.5 opacity-30 pointer-events-none transition" data-cat="{{ $catName }}">
                                    <button type="button" class="btn-g-step-dec h-8 w-8 rounded-lg border border-[#182830] bg-[#F7E6CB] font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-slate-200 transition cursor-pointer" data-cat="{{ $catName }}">
                                        −
                                    </button>
                                    <span class="g-step-val w-8 text-center font-mono text-sm font-black text-[#182830]" data-cat="{{ $catName }}">0</span>
                                    <button type="button" class="btn-g-step-inc h-8 w-8 rounded-lg border border-[#182830] bg-[#F7E6CB] font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-slate-200 transition cursor-pointer" data-cat="{{ $catName }}">
                                        ＋
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- STEP 4: Supply Inventory & Detergent/Fabcon Selection -->
                <div id="wizard-pane-4" class="wizard-pane hidden space-y-5">
                    <div class="border-b-2 border-[#182830]/15 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 4 of 5</span>
                            <h3 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Supply Inventory &amp; Wash Formulation</h3>
                            <p class="text-xs font-medium text-[#25799B]">Select detergent &amp; fabcon sachets from real shop stock. Units used will automatically deduct from inventory.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] font-mono text-[10px] font-bold flex items-center gap-1">
                                <svg class="h-3 w-3 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                <span>Auto Per Sachet Logic</span>
                            </span>
                        </div>
                    </div>

                    <!-- Customer Own Soap Quick Toggle -->
                    <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-3.5 shadow-[2px_2px_0px_#182830]">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="wizard-customer-own-supplies" class="h-4.5 w-4.5 rounded border-2 border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer">
                            <div>
                                <strong class="block font-recoleta text-sm font-bold text-[#182830]">Customer Brought Own Detergent &amp; Fabcon</strong>
                                <span class="block font-mono text-[11px] text-slate-600">Bypass shop supplies — no inventory sachets will be deducted for this ticket.</span>
                            </div>
                        </label>
                    </div>

                    <!-- Live Supply Inventory Sachets Selection -->
                    <div id="wizard-shop-supplies-section" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block font-mono text-xs font-bold uppercase text-[#182830]">
                                Select Washing Detergent Soap &amp; Shop Supplies (Tracked Per Sachet) *
                            </label>
                            <span class="font-mono text-[11px] text-[#25799B]">
                                Select sachets to deduct from stockroom
                            </span>
                        </div>

                        @if($modalInventory->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="wizard-inventory-items-list">
                                @foreach($modalInventory as $invItem)
                                    @php
                                        $onHand = (float) $invItem->quantity_on_hand;
                                        $threshold = (float) $invItem->low_stock_threshold;
                                        $isDepleted = $onHand <= 0;
                                        $isAlmostOut = !$isDepleted && $onHand <= $threshold;
                                    @endphp
                                    <div class="wizard-inv-item-card flex flex-col justify-between rounded-2xl border-2 border-[#182830] {{ $isDepleted ? 'bg-red-50/50 opacity-60' : ($isAlmostOut ? 'bg-amber-50/70 border-amber-800' : 'bg-[#FFFDF8]') }} p-3.5 shadow-[2px_2px_0px_#182830] transition" data-item-id="{{ $invItem->id }}">
                                        <div>
                                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <strong class="font-recoleta text-sm font-bold text-[#182830]">{{ $invItem->name }}</strong>
                                                    <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] text-[9px] font-mono font-bold flex items-center gap-1">
                                                        <svg class="h-2.5 w-2.5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                                        <span>{{ $invItem->unit === 'sachet' ? 'Per Sachet' : $invItem->unit }}</span>
                                                    </span>
                                                </div>

                                                <!-- Status Badge in Transaction Intake -->
                                                @if($isDepleted)
                                                    <span class="badge border border-red-800 bg-[#CB1B03] text-white text-[9px] font-mono font-black shrink-0">
                                                        Depleted (0 left)
                                                    </span>
                                                @elseif($isAlmostOut)
                                                    <span class="badge border border-amber-800 bg-amber-400 text-[#182830] text-[9px] font-mono font-black animate-pulse shrink-0">
                                                        Almost Out ({{ $onHand }} left!)
                                                    </span>
                                                @else
                                                    <span class="badge border border-emerald-600 bg-emerald-100 text-emerald-800 text-[9px] font-mono font-bold shrink-0">
                                                        Healthy Stock
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between text-xs font-mono text-slate-600 mb-3">
                                                <span>Stock on hand: <strong class="text-[#182830]">{{ $onHand }} {{ $invItem->unit }}{{ $invItem->unit === 'sachet' ? 's' : '' }}</strong></span>
                                                <span class="text-[10px] text-slate-500">Threshold: {{ $threshold }}</span>
                                            </div>
                                        </div>

                                        <!-- Quantity Stepper & Selection -->
                                        <div class="pt-2.5 border-t border-[#182830]/15 flex items-center justify-between gap-2">
                                            <label class="flex items-center gap-2 cursor-pointer text-xs font-mono font-bold text-[#182830]">
                                                <input type="checkbox" 
                                                       class="wizard-inv-checkbox h-4 w-4 rounded border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer" 
                                                       data-id="{{ $invItem->id }}"
                                                       data-name="{{ $invItem->name }}"
                                                       data-unit="{{ $invItem->unit }}"
                                                       data-max="{{ $onHand }}"
                                                       {{ $isDepleted ? 'disabled' : '' }}>
                                                <span>{{ $isDepleted ? 'Out of Stock' : 'Use for this order' }}</span>
                                            </label>

                                            <div class="wizard-inv-stepper-wrap flex items-center gap-1.5 {{ $isDepleted ? 'opacity-30 pointer-events-none' : 'opacity-40 pointer-events-none' }}" data-stepper-id="{{ $invItem->id }}">
                                                <button type="button" class="btn-inv-dec flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#F7E6CB] font-mono text-xs font-black text-[#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer" data-id="{{ $invItem->id }}">-</button>
                                                
                                                <input type="number" 
                                                       name="inventory_items[{{ $invItem->id }}]" 
                                                       id="inv-qty-input-{{ $invItem->id }}"
                                                       value="0" 
                                                       min="0" 
                                                       max="{{ $onHand }}" 
                                                       data-id="{{ $invItem->id }}"
                                                       data-name="{{ $invItem->name }}"
                                                       data-unit="{{ $invItem->unit }}"
                                                       class="wizard-inv-qty-input w-12 text-center rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-black text-[#182830]"
                                                       {{ $isDepleted ? 'disabled' : '' }}>
                                                
                                                <button type="button" class="btn-inv-inc flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#25799B] font-mono text-xs font-black text-white hover:bg-[#1E6482] transition cursor-pointer" data-id="{{ $invItem->id }}">+</button>
                                                <span class="font-mono text-[10px] text-slate-500 font-bold ml-0.5">{{ $invItem->unit }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-[#182830]/40 bg-[#FFFDF8] p-4 text-center">
                                <p class="font-mono text-xs text-slate-600">No inventory supplies recorded yet. You can add items (such as Surf powder or Downy fabcon sachets) in the Supply Inventory module.</p>
                            </div>
                        @endif

                        <!-- Optional Wash Add-ons -->
                        <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
                            <span class="block font-mono text-xs font-bold uppercase text-[#182830] mb-2.5">Wash Treatment Notes &amp; Add-ons</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <label class="flex items-center gap-2 rounded-xl border border-[#182830]/30 bg-[#FFFDF8] p-2.5 text-xs cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" id="wizard-addon-bleach" class="h-4 w-4 rounded border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer">
                                    <div>
                                        <strong class="block text-[#182830]">Color-Safe Bleach</strong>
                                        <span class="text-[10px] text-slate-500">Brightens whites &amp; tones</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 rounded-xl border border-[#182830]/30 bg-[#FFFDF8] p-2.5 text-xs cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" id="wizard-addon-sanitizer" class="h-4 w-4 rounded border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer">
                                    <div>
                                        <strong class="block text-[#182830]">Hygiene Sanitizer</strong>
                                        <span class="text-[10px] text-slate-500">Antibacterial rinse</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 rounded-xl border border-[#182830]/30 bg-[#FFFDF8] p-2.5 text-xs cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" id="wizard-addon-scent" class="h-4 w-4 rounded border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer">
                                    <div>
                                        <strong class="block text-[#182830]">Scent Booster Beads</strong>
                                        <span class="text-[10px] text-slate-500">Extra fragrance</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Final Review & Cashier Tender -->
                <div id="wizard-pane-5" class="wizard-pane hidden space-y-5">
                    <div class="border-b-2 border-[#182830]/15 pb-3">
                        <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 5 of 5</span>
                        <h3 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Order Confirmation & Cashier Tender</h3>
                        <p class="text-xs font-medium text-[#25799B]">Review ticket specifications, collect cash tender or set for claim payment, and issue order.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                        <!-- Left: Perforated Ticket Stub Preview -->
                        <div class="rounded-2xl border-2 border-dashed border-[#182830] bg-[#FFFDF8] p-4 text-[#182830] shadow-[2px_2px_0px_#182830] font-mono text-xs space-y-3">
                            <div class="text-center border-b border-dashed border-[#182830] pb-2">
                                <span class="font-recoleta text-base font-black text-[#182830]">TROWA LAUNDRY TICKET</span>
                                <span class="block text-[10px] uppercase text-[#25799B] font-bold">Counter Intake Terminal</span>
                            </div>

                            <div class="space-y-1 text-[11px] border-b border-dashed border-[#182830] pb-2">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Customer:</span>
                                    <strong id="wizard-stub-cust-name" class="text-[#182830]">Walk-in Guest</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Weight & Loads:</span>
                                    <span id="wizard-stub-weight-loads">8.0 kg (1 load)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Garments Counted:</span>
                                    <span id="wizard-stub-garment-count" class="font-bold text-[#25799B]">0 items counted</span>
                                </div>
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-slate-500">Soap & Formulation:</span>
                                    <span id="wizard-stub-soap-spec" class="text-right truncate max-w-[170px]">Commercial Liquid</span>
                                </div>
                            </div>

                            <div>
                                <span class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Services Breakdown</span>
                                <ul id="wizard-stub-services-list" class="space-y-0.5 text-[11px]">
                                    <li class="italic text-slate-400">Loading services...</li>
                                </ul>
                            </div>

                            <div class="border-t border-dashed border-[#182830] pt-2 flex justify-between items-center text-sm font-black">
                                <span>TOTAL CHARGES:</span>
                                <span id="wizard-stub-grand-total" class="font-mono text-xl text-[#CB1B03]">₱0.00</span>
                            </div>
                        </div>

                        <!-- Right: Cash Tender Controls -->
                        <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830] space-y-4">
                            <div>
                                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1.5">Payment Timing</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-2.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] cursor-pointer">
                                        <input type="radio" name="wizard_pay_timing" value="now" id="wizard-pay-now-radio" checked class="text-[#CB1B03] focus:ring-0">
                                        <span>Tender Now</span>
                                    </label>
                                    <label class="flex items-center gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-2.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] cursor-pointer">
                                        <input type="radio" name="wizard_pay_timing" value="pickup" id="wizard-pay-pickup-radio" class="text-[#CB1B03] focus:ring-0">
                                        <span>Pay on Claim</span>
                                    </label>
                                </div>
                            </div>

                            <div id="wizard-tender-controls" class="space-y-3">
                                <!-- Cash Presets -->
                                <div>
                                    <span class="block font-mono text-[10px] font-bold uppercase text-slate-500 mb-1">Quick Cash Tender:</span>
                                    <div class="grid grid-cols-4 gap-1.5">
                                        <button type="button" id="wizard-btn-exact-tender" class="rounded-lg border border-[#182830] bg-[#F7E6CB] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-slate-200 transition cursor-pointer">
                                            Exact
                                        </button>
                                        <button type="button" class="wizard-btn-cash-preset rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="100">
                                            ₱100
                                        </button>
                                        <button type="button" class="wizard-btn-cash-preset rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="200">
                                            ₱200
                                        </button>
                                        <button type="button" class="wizard-btn-cash-preset rounded-lg border border-[#182830] bg-[#FFFDF8] py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-amount="500">
                                            ₱500
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block font-mono text-[10px] font-bold uppercase text-[#182830] mb-1">Cash Tender (₱)</label>
                                        <input type="number" step="0.01" min="0" id="wizard-tender-input" placeholder="0.00" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-sm font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block font-mono text-[10px] font-bold uppercase text-[#182830] mb-1">Method</label>
                                        <select name="payment_method" id="wizard-payment-method" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
                                            <option value="cash">Cash Tender</option>
                                            <option value="gcash">GCash E-Wallet</option>
                                            <option value="other">Other / Bank</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="wizard-ref-container" class="hidden">
                                    <label class="block font-mono text-[10px] font-bold uppercase text-[#182830] mb-1">GCash / Reference #</label>
                                    <input type="text" name="reference_number" id="wizard-ref-number" placeholder="GCash Ref # or OR #" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1 font-mono text-xs text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
                                </div>

                                <!-- Change Display -->
                                <div id="wizard-change-box" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-2.5 text-center shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[9px] font-bold uppercase text-slate-500">Change Due to Customer</span>
                                    <strong id="wizard-change-due-val" class="font-mono text-lg font-black text-emerald-700">₱0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Action Footer with Dynamic Next / Back Navigation -->
            <div class="border-t-2 border-[#182830] bg-[#F7E6CB]/40 px-4 sm:px-6 py-3 flex items-center justify-between">
                <div>
                    <button type="button" id="wizard-btn-back" class="hidden rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer">
                        ← Back
                    </button>
                    <button type="button" data-modal-close id="wizard-btn-cancel" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-slate-600 shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer">
                        Cancel
                    </button>
                </div>

                <div>
                    <button type="button" id="wizard-btn-next" class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-5 py-2 font-recoleta text-sm font-bold text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer flex items-center gap-1.5">
                        <span id="wizard-btn-next-label">Next: Scale & Services</span>
                        <span>➔</span>
                    </button>

                    <button type="submit" id="wizard-btn-submit" class="hidden rounded-xl border-3 border-[#182830] bg-[#CB1B03] px-6 py-2.5 font-recoleta text-sm font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer flex items-center gap-2">
                        <span>Proceed to Start It All ➔</span>
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

<script>
(function() {
    let currentStep = 1;
    const totalSteps = 5;

    // Elements
    const progressBar = document.getElementById('wizard-progress-fill');
    const stepText = document.getElementById('wizard-progress-step-text');
    const stepPercentage = document.getElementById('wizard-progress-percentage');
    const stepTabs = document.querySelectorAll('.wizard-step-tab');
    const btnBack = document.getElementById('wizard-btn-back');
    const btnCancel = document.getElementById('wizard-btn-cancel');
    const btnNext = document.getElementById('wizard-btn-next');
    const btnNextLabel = document.getElementById('wizard-btn-next-label');
    const btnSubmit = document.getElementById('wizard-btn-submit');

    // Step 1 Customer Elements
    const customerSelect = document.getElementById('wizard-customer-select');
    const customerName = document.getElementById('wizard-customer-name');
    const customerContact = document.getElementById('wizard-customer-contact');
    const customerAddress = document.getElementById('wizard-customer-address');
    const btnWalkin = document.getElementById('wizard-btn-walkin');
    const btnClearCust = document.getElementById('wizard-btn-clear-cust');
    const custError = document.getElementById('wizard-customer-name-error');

    // Step 2 Scale & Service Elements
    const weightInput = document.getElementById('wizard-weight-kg');
    const loadsBadge = document.getElementById('wizard-loads-badge');
    const btnWMinus = document.getElementById('wizard-btn-w-minus');
    const btnWPlus = document.getElementById('wizard-btn-w-plus');
    const serviceCheckboxes = document.querySelectorAll('.wizard-service-cb');
    const liveSubtotalEl = document.getElementById('wizard-live-subtotal');
    const servicesError = document.getElementById('wizard-services-error');

    // Step 3 Garment Elements
    const garmentChecks = document.querySelectorAll('.wizard-garment-check');
    const garmentSummaryBadge = document.getElementById('wizard-garments-summary-badge');
    const hiddenItemRows = document.getElementById('wizard-hidden-item-rows');
    let garmentCounts = {};

    // Step 4 Soap Elements
    const soapRadios = document.querySelectorAll('input[name="wizard_soap_type"]');
    const fabconCheck = document.getElementById('wizard-fabcon-enable');
    const fabconScentsBox = document.getElementById('wizard-fabcon-scents-box');
    const fabconScentRadios = document.querySelectorAll('input[name="wizard_fabcon_scent"]');
    const addonBleach = document.getElementById('wizard-addon-bleach');
    const addonSanitizer = document.getElementById('wizard-addon-sanitizer');
    const addonScent = document.getElementById('wizard-addon-scent');

    // Step 5 Review & Tender Elements
    const stubCustName = document.getElementById('wizard-stub-cust-name');
    const stubWeightLoads = document.getElementById('wizard-stub-weight-loads');
    const stubGarmentCount = document.getElementById('wizard-stub-garment-count');
    const stubSoapSpec = document.getElementById('wizard-stub-soap-spec');
    const stubServicesList = document.getElementById('wizard-stub-services-list');
    const stubGrandTotal = document.getElementById('wizard-stub-grand-total');

    const payNowRadio = document.getElementById('wizard-pay-now-radio');
    const payPickupRadio = document.getElementById('wizard-pay-pickup-radio');
    const tenderControls = document.getElementById('wizard-tender-controls');
    const tenderInput = document.getElementById('wizard-tender-input');
    const hiddenAmountPaid = document.getElementById('wizard-hidden-amount-paid');
    const hiddenTenderedAmount = document.getElementById('wizard-hidden-tendered-amount');
    const changeDueVal = document.getElementById('wizard-change-due-val');
    const changeBox = document.getElementById('wizard-change-box');
    const paymentMethod = document.getElementById('wizard-payment-method');
    const refContainer = document.getElementById('wizard-ref-container');
    const btnExactTender = document.getElementById('wizard-btn-exact-tender');

    let calculatedTotal = 0;

    // STEP LABELS FOR NAVIGATION
    const stepMeta = [
        { title: 'Step 1 of 5: Customer Dossier', nextBtn: 'Next: Scale & Services' },
        { title: 'Step 2 of 5: Scale Weight & Treatments', nextBtn: 'Next: Count Garments' },
        { title: 'Step 3 of 5: Garment Counts', nextBtn: 'Next: Soap & Detergent' },
        { title: 'Step 4 of 5: Soap & Wash Supplies', nextBtn: 'Next: Review & Payment' },
        { title: 'Step 5 of 5: Review & Settle', nextBtn: 'Proceed to Start It All' }
    ];

    function showStep(step) {
        currentStep = step;

        // Hide all panes
        for (let i = 1; i <= totalSteps; i++) {
            const pane = document.getElementById(`wizard-pane-${i}`);
            if (pane) pane.classList.toggle('hidden', i !== currentStep);
        }

        // Update progress bar
        const percent = Math.round((currentStep / totalSteps) * 100);
        if (progressBar) progressBar.style.width = `${percent}%`;
        if (stepText) stepText.textContent = stepMeta[currentStep - 1].title;
        if (stepPercentage) stepPercentage.textContent = `${percent}% Complete`;

        // Update tabs styling
        stepTabs.forEach(tab => {
            const tabStep = parseInt(tab.dataset.step, 10);
            if (tabStep === currentStep) {
                tab.className = 'wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition shadow-[2px_2px_0px_#182830]';
            } else if (tabStep < currentStep) {
                tab.className = 'wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition shadow-[1px_1px_0px_#182830]';
            } else {
                tab.className = 'wizard-step-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-500 p-1.5 text-center font-mono text-[10px] sm:text-xs font-bold transition hover:bg-amber-50';
            }
        });

        // Navigation buttons
        if (currentStep === 1) {
            btnBack?.classList.add('hidden');
            btnCancel?.classList.remove('hidden');
        } else {
            btnBack?.classList.remove('hidden');
            btnCancel?.classList.add('hidden');
        }

        if (currentStep === 5) {
            btnNext?.classList.add('hidden');
            btnSubmit?.classList.remove('hidden');
            renderStep5Summary();
        } else {
            btnNext?.classList.remove('hidden');
            btnSubmit?.classList.add('hidden');
            if (btnNextLabel) btnNextLabel.textContent = stepMeta[currentStep - 1].nextBtn;
        }
    }

    // Validation before stepping forward
    function validateStep(step) {
        if (step === 1) {
            const name = customerName?.value.trim();
            if (!name) {
                custError?.classList.remove('hidden');
                customerName?.focus();
                return false;
            }
            custError?.classList.add('hidden');
            return true;
        }

        if (step === 2) {
            const weight = parseFloat(weightInput?.value) || 0;
            const checkedServices = document.querySelectorAll('.wizard-service-cb:checked');
            if (weight <= 0) {
                alert('Please enter a valid scale weight (kg).');
                weightInput?.focus();
                return false;
            }
            if (weight > 64 || Math.ceil(weight / 8) > 8) {
                const loads = Math.ceil(weight / 8);
                alert(`Fleet Limit Exceeded: Trowa Laundry has only 8 washing machines (max 64.0 kg per run). The entered weight of ${weight.toFixed(1)} kg requires ${loads} machines. Please reduce the weight or process as multiple separate orders.`);
                const warningEl = document.getElementById('wizard-capacity-warning');
                if (warningEl) warningEl.classList.remove('hidden');
                weightInput?.focus();
                return false;
            }
            if (checkedServices.length === 0) {
                servicesError?.classList.remove('hidden');
                return false;
            }
            servicesError?.classList.add('hidden');
            return true;
        }

        return true;
    }

    // Step Nav Button Listeners
    btnNext?.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }
    });

    btnBack?.addEventListener('click', () => {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    stepTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetStep = parseInt(tab.dataset.step, 10);
            if (targetStep < currentStep) {
                showStep(targetStep);
            } else if (targetStep > currentStep) {
                if (validateStep(currentStep)) {
                    showStep(targetStep);
                }
            }
        });
    });

    // STEP 1: Customer Logic
    customerSelect?.addEventListener('change', () => {
        const selected = customerSelect.options[customerSelect.selectedIndex];
        if (selected && selected.value) {
            if (customerName) customerName.value = selected.dataset.name || '';
            if (customerContact) customerContact.value = selected.dataset.contact || '';
            if (customerAddress) customerAddress.value = selected.dataset.address || '';
        } else {
            if (customerName) customerName.value = '';
            if (customerContact) customerContact.value = '';
            if (customerAddress) customerAddress.value = '';
        }
    });

    btnWalkin?.addEventListener('click', () => {
        if (customerSelect) customerSelect.value = '';
        if (customerName) customerName.value = 'Walk-in Guest';
        if (customerContact) customerContact.value = '';
        if (customerAddress) customerAddress.value = '';
        custError?.classList.add('hidden');
    });

    btnClearCust?.addEventListener('click', () => {
        if (customerSelect) customerSelect.value = '';
        if (customerName) customerName.value = '';
        if (customerContact) customerContact.value = '';
        if (customerAddress) customerAddress.value = '';
    });

    // STEP 2: Scale & Services Logic
    function recalculateServices() {
        const weight = parseFloat(weightInput?.value) || 0;
        const loads = weight > 0 ? Math.ceil(weight / 8) : 0;

        const warningEl = document.getElementById('wizard-capacity-warning');
        const warningKgVal = document.getElementById('wizard-warning-kg-val');
        const warningLoadsVal = document.getElementById('wizard-warning-loads-val');

        if (weight > 64 || loads > 8) {
            if (warningEl) warningEl.classList.remove('hidden');
            if (warningKgVal) warningKgVal.textContent = weight.toFixed(1);
            if (warningLoadsVal) warningLoadsVal.textContent = loads;
            if (loadsBadge) {
                loadsBadge.textContent = `${loads} (OVER 8 MACHINES!)`;
                loadsBadge.className = 'text-base font-black text-red-600 animate-pulse';
            }
        } else {
            if (warningEl) warningEl.classList.add('hidden');
            if (loadsBadge) {
                loadsBadge.textContent = loads;
                loadsBadge.className = 'text-base text-[#CB1B03]';
            }
        }

        let subtotal = 0;
        serviceCheckboxes.forEach(cb => {
            if (cb.checked) {
                const type = cb.dataset.type;
                const price = parseFloat(cb.dataset.price) || 0;
                if (type === 'per_kg') {
                    subtotal += price * weight;
                } else if (type === 'flat_rate') {
                    subtotal += price;
                } else {
                    subtotal += price * loads;
                }
            }
        });

        calculatedTotal = subtotal;
        if (liveSubtotalEl) liveSubtotalEl.textContent = `₱${subtotal.toFixed(2)}`;
        recalculateChange();
    }

    weightInput?.addEventListener('input', recalculateServices);

    btnWMinus?.addEventListener('click', () => {
        if (weightInput) {
            const cur = parseFloat(weightInput.value) || 0;
            weightInput.value = Math.max(0.5, (cur - 0.5)).toFixed(1);
            recalculateServices();
        }
    });

    btnWPlus?.addEventListener('click', () => {
        if (weightInput) {
            const cur = parseFloat(weightInput.value) || 0;
            weightInput.value = (cur + 0.5).toFixed(1);
            recalculateServices();
        }
    });

    document.querySelectorAll('.wizard-btn-preset-weight').forEach(btn => {
        btn.addEventListener('click', () => {
            if (weightInput) {
                weightInput.value = btn.dataset.weight;
                recalculateServices();
            }
        });
    });

    serviceCheckboxes.forEach(cb => cb.addEventListener('change', recalculateServices));

    // 1-Click Packages
    document.querySelectorAll('.wizard-btn-bundle').forEach(btn => {
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
            recalculateServices();
        });
    });

    // STEP 3: Garment Checkboxes & Steppers
    function updateGarmentInputs() {
        if (!hiddenItemRows) return;
        hiddenItemRows.innerHTML = '';
        let totalPieces = 0;
        let activeCategories = 0;
        let index = 0;

        Object.keys(garmentCounts).forEach(cat => {
            const qty = garmentCounts[cat] || 0;
            if (qty > 0) {
                totalPieces += qty;
                activeCategories++;

                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `item_details[${index}][item_name]`;
                nameInput.value = cat;

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `item_details[${index}][quantity]`;
                qtyInput.value = qty;

                hiddenItemRows.appendChild(nameInput);
                hiddenItemRows.appendChild(qtyInput);
                index++;
            }
        });

        if (garmentSummaryBadge) {
            garmentSummaryBadge.textContent = `${totalPieces} items counted across ${activeCategories} categories`;
        }
    }

    garmentChecks.forEach(cb => {
        cb.addEventListener('change', () => {
            const cat = cb.dataset.cat;
            const card = document.querySelector(`.wizard-garment-card[data-cat="${cat}"]`);
            const stepper = document.querySelector(`.wizard-stepper-box[data-cat="${cat}"]`);
            const valEl = document.querySelector(`.g-step-val[data-cat="${cat}"]`);

            if (cb.checked) {
                garmentCounts[cat] = Math.max(1, garmentCounts[cat] || 1);
                if (valEl) valEl.textContent = garmentCounts[cat];
                if (stepper) stepper.className = 'wizard-stepper-box flex items-center gap-1.5 opacity-100 pointer-events-auto transition';
                if (card) card.className = 'wizard-garment-card flex items-center justify-between rounded-2xl border-2 border-[#182830] bg-[#A2C5D8]/20 p-3 shadow-[2px_2px_0px_#182830] transition';
            } else {
                garmentCounts[cat] = 0;
                if (valEl) valEl.textContent = '0';
                if (stepper) stepper.className = 'wizard-stepper-box flex items-center gap-1.5 opacity-30 pointer-events-none transition';
                if (card) card.className = 'wizard-garment-card flex items-center justify-between rounded-2xl border-2 border-[#182830]/30 bg-[#FFFDF8] p-3 transition';
            }
            updateGarmentInputs();
        });
    });

    document.querySelectorAll('.btn-g-step-inc').forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.cat;
            const cb = document.querySelector(`.wizard-garment-check[data-cat="${cat}"]`);
            const valEl = document.querySelector(`.g-step-val[data-cat="${cat}"]`);
            if (cb && !cb.checked) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change'));
            } else {
                garmentCounts[cat] = (garmentCounts[cat] || 0) + 1;
                if (valEl) valEl.textContent = garmentCounts[cat];
                updateGarmentInputs();
            }
        });
    });

    document.querySelectorAll('.btn-g-step-dec').forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.cat;
            const cb = document.querySelector(`.wizard-garment-check[data-cat="${cat}"]`);
            const valEl = document.querySelector(`.g-step-val[data-cat="${cat}"]`);
            if ((garmentCounts[cat] || 0) > 1) {
                garmentCounts[cat]--;
                if (valEl) valEl.textContent = garmentCounts[cat];
                updateGarmentInputs();
            } else {
                if (cb) {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    // STEP 4: Live Inventory Supplies Selection Logic
    const custOwnSuppliesCheck = document.getElementById('wizard-customer-own-supplies');
    const invItemsList = document.getElementById('wizard-inventory-items-list');

    custOwnSuppliesCheck?.addEventListener('change', () => {
        const isOwn = custOwnSuppliesCheck.checked;
        document.querySelectorAll('.wizard-inv-checkbox').forEach(cb => {
            if (isOwn) {
                cb.checked = false;
                const id = cb.dataset.id;
                const input = document.getElementById('inv-qty-input-' + id);
                if (input) input.value = 0;
                const stepper = document.querySelector(`.wizard-inv-stepper-wrap[data-stepper-id="${id}"]`);
                if (stepper) {
                    stepper.classList.add('opacity-40', 'pointer-events-none');
                    stepper.classList.remove('opacity-100');
                }
            }
        });
        if (invItemsList) {
            invItemsList.classList.toggle('opacity-50', isOwn);
            invItemsList.classList.toggle('pointer-events-none', isOwn);
        }
    });

    document.querySelectorAll('.wizard-inv-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const id = cb.dataset.id;
            const input = document.getElementById('inv-qty-input-' + id);
            const stepper = document.querySelector(`.wizard-inv-stepper-wrap[data-stepper-id="${id}"]`);
            const max = parseFloat(cb.dataset.max) || 0;

            if (cb.checked) {
                if (custOwnSuppliesCheck) custOwnSuppliesCheck.checked = false;
                if (input) {
                    const weight = parseFloat(weightInput?.value) || 0;
                    const defaultLoads = Math.max(1, Math.ceil(weight / 8));
                    input.value = Math.min(max, Math.max(1, defaultLoads));
                }
                if (stepper) {
                    stepper.classList.remove('opacity-40', 'pointer-events-none');
                    stepper.classList.add('opacity-100');
                }
            } else {
                if (input) input.value = 0;
                if (stepper) {
                    stepper.classList.add('opacity-40', 'pointer-events-none');
                    stepper.classList.remove('opacity-100');
                }
            }
        });
    });

    document.querySelectorAll('.btn-inv-inc').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const input = document.getElementById('inv-qty-input-' + id);
            const cb = document.querySelector(`.wizard-inv-checkbox[data-id="${id}"]`);
            const stepper = document.querySelector(`.wizard-inv-stepper-wrap[data-stepper-id="${id}"]`);
            if (input) {
                const max = parseFloat(input.max) || 999;
                const cur = parseFloat(input.value) || 0;
                if (cur < max) {
                    input.value = cur + 1;
                    if (cb && !cb.checked) {
                        cb.checked = true;
                    }
                    if (stepper) {
                        stepper.classList.remove('opacity-40', 'pointer-events-none');
                        stepper.classList.add('opacity-100');
                    }
                    if (custOwnSuppliesCheck) custOwnSuppliesCheck.checked = false;
                }
            }
        });
    });

    document.querySelectorAll('.btn-inv-dec').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const input = document.getElementById('inv-qty-input-' + id);
            const cb = document.querySelector(`.wizard-inv-checkbox[data-id="${id}"]`);
            const stepper = document.querySelector(`.wizard-inv-stepper-wrap[data-stepper-id="${id}"]`);
            if (input) {
                const cur = parseFloat(input.value) || 0;
                if (cur > 1) {
                    input.value = cur - 1;
                } else {
                    input.value = 0;
                    if (cb) cb.checked = false;
                    if (stepper) {
                        stepper.classList.add('opacity-40', 'pointer-events-none');
                        stepper.classList.remove('opacity-100');
                    }
                }
            }
        });
    });

    document.querySelectorAll('.wizard-inv-qty-input').forEach(input => {
        input.addEventListener('change', () => {
            const id = input.dataset.id;
            const cb = document.querySelector(`.wizard-inv-checkbox[data-id="${id}"]`);
            const stepper = document.querySelector(`.wizard-inv-stepper-wrap[data-stepper-id="${id}"]`);
            const val = parseFloat(input.value) || 0;
            const max = parseFloat(input.max) || 999;
            const clamped = Math.max(0, Math.min(max, val));
            input.value = clamped;
            if (clamped > 0) {
                if (cb) cb.checked = true;
                if (stepper) {
                    stepper.classList.remove('opacity-40', 'pointer-events-none');
                    stepper.classList.add('opacity-100');
                }
                if (custOwnSuppliesCheck) custOwnSuppliesCheck.checked = false;
            } else {
                if (cb) cb.checked = false;
                if (stepper) {
                    stepper.classList.add('opacity-40', 'pointer-events-none');
                    stepper.classList.remove('opacity-100');
                }
            }
        });
    });

    // STEP 5: Render Summary & Live Stub
    function renderStep5Summary() {
        if (stubCustName) stubCustName.textContent = customerName?.value.trim() || 'Walk-in Guest';

        const weight = parseFloat(weightInput?.value) || 0;
        const loads = weight > 0 ? Math.ceil(weight / 8) : 0;
        if (stubWeightLoads) stubWeightLoads.textContent = `${weight.toFixed(1)} kg (${loads} commercial load${loads === 1 ? '' : 's'})`;

        // Garments
        let totalPcs = 0;
        Object.values(garmentCounts).forEach(q => totalPcs += (q || 0));
        if (stubGarmentCount) stubGarmentCount.textContent = `${totalPcs} item${totalPcs === 1 ? '' : 's'} counted`;

        // Soap & Supplies Spec
        let selectedSoap = '';
        if (custOwnSuppliesCheck?.checked) {
            selectedSoap = 'Customer Brought Own Soap / No Shop Supplies';
        } else {
            const usedSupplies = [];
            document.querySelectorAll('.wizard-inv-qty-input').forEach(input => {
                const qty = parseFloat(input.value) || 0;
                if (qty > 0) {
                    const name = input.dataset.name || 'Supply';
                    const unit = input.dataset.unit || 'sachet';
                    usedSupplies.push(`${name} (${qty} ${unit}${qty > 1 && unit === 'sachet' ? 's' : ''})`);
                }
            });
            if (usedSupplies.length > 0) {
                selectedSoap = usedSupplies.join(', ');
            } else {
                selectedSoap = 'Standard Machine Formula';
            }
        }
        if (document.getElementById('wizard-addon-bleach')?.checked) selectedSoap += ' + Bleach';
        if (document.getElementById('wizard-addon-sanitizer')?.checked) selectedSoap += ' + Sanitizer';
        if (document.getElementById('wizard-addon-scent')?.checked) selectedSoap += ' + Scent Beads';
        if (stubSoapSpec) stubSoapSpec.textContent = selectedSoap;

        // Services list
        if (stubServicesList) {
            const selectedServices = [];
            serviceCheckboxes.forEach(cb => {
                if (cb.checked) {
                    const type = cb.dataset.type;
                    const price = parseFloat(cb.dataset.price) || 0;
                    let lineTotal = 0;
                    if (type === 'per_kg') lineTotal = price * weight;
                    else if (type === 'flat_rate') lineTotal = price;
                    else lineTotal = price * loads;
                    selectedServices.push({ name: cb.dataset.name, subtotal: lineTotal });
                }
            });

            if (selectedServices.length === 0) {
                stubServicesList.innerHTML = '<li class="italic text-slate-400">No services selected.</li>';
            } else {
                stubServicesList.innerHTML = selectedServices.map(s => `
                    <li class="flex justify-between py-0.5 border-b border-slate-100">
                        <span class="truncate pr-1">${s.name}</span>
                        <strong class="font-bold text-[#182830]">₱${s.subtotal.toFixed(2)}</strong>
                    </li>
                `).join('');
            }
        }

        if (stubGrandTotal) stubGrandTotal.textContent = `₱${calculatedTotal.toFixed(2)}`;
        recalculateChange();
    }

    // Tender & Change Logic
    function recalculateChange() {
        if (payPickupRadio?.checked) {
            if (hiddenAmountPaid) hiddenAmountPaid.value = '0';
            if (hiddenTenderedAmount) hiddenTenderedAmount.value = '0';
            if (tenderControls) tenderControls.classList.add('hidden');
            return;
        } else {
            if (tenderControls) tenderControls.classList.remove('hidden');
        }

        const rawTenderVal = tenderInput?.value ? parseFloat(tenderInput.value) : null;
        const tender = rawTenderVal !== null && !isNaN(rawTenderVal) ? rawTenderVal : 0;
        const amountToPay = Math.min(tender, calculatedTotal);
        if (hiddenAmountPaid) hiddenAmountPaid.value = amountToPay.toFixed(2);
        if (hiddenTenderedAmount) hiddenTenderedAmount.value = tender.toFixed(2);

        const change = tender - calculatedTotal;
        if (change >= 0) {
            if (changeDueVal) {
                changeDueVal.textContent = `₱${change.toFixed(2)}`;
                changeDueVal.className = 'font-mono text-lg font-black text-emerald-700';
            }
            if (changeBox) changeBox.className = 'rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-2.5 text-center shadow-[1px_1px_0px_#182830]';
        } else {
            const short = Math.abs(change);
            if (changeDueVal) {
                changeDueVal.textContent = `Short ₱${short.toFixed(2)}`;
                changeDueVal.className = 'font-mono text-xs font-bold text-red-600';
            }
            if (changeBox) changeBox.className = 'rounded-xl border-2 border-red-500 bg-red-50 p-2.5 text-center shadow-[1px_1px_0px_#182830]';
        }
    }

    tenderInput?.addEventListener('input', recalculateChange);
    payNowRadio?.addEventListener('change', recalculateChange);
    payPickupRadio?.addEventListener('change', recalculateChange);

    btnExactTender?.addEventListener('click', () => {
        if (tenderInput) {
            tenderInput.value = calculatedTotal.toFixed(2);
            recalculateChange();
        }
    });

    document.querySelectorAll('.wizard-btn-cash-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            if (tenderInput) {
                tenderInput.value = parseFloat(btn.dataset.amount).toFixed(2);
                recalculateChange();
            }
        });
    });

    paymentMethod?.addEventListener('change', function() {
        if (this.value === 'gcash' || this.value === 'other') {
            refContainer?.classList.remove('hidden');
        } else {
            refContainer?.classList.add('hidden');
        }
    });

    // Form submission validation & auto-tender fallback
    const form = document.getElementById('wizard-intake-form');
    form?.addEventListener('submit', function(e) {
        if (!validateStep(1) || !validateStep(2)) {
            e.preventDefault();
            return false;
        }

        const currentWeight = parseFloat(weightInput?.value) || 0;
        if (currentWeight > 64 || Math.ceil(currentWeight / 8) > 8) {
            e.preventDefault();
            alert('Cannot proceed: Total weight exceeds our 8 washing machines capacity (64.0 kg).');
            return false;
        }

        // Intercept with Trowa Retro Confirmation Modal
        if (!form.hasAttribute('data-intake-confirmed')) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const customerNameVal = customerName?.value.trim() || 'Walk-in Customer';
            const loadsVal = Math.ceil(currentWeight / 8);
            const timingVal = payNowRadio?.checked ? 'Tender Now (Paid)' : 'Pay on Claim';
            const detailsHtml = `
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Customer:</span>
                    <strong>${customerNameVal}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Weight / Drums:</span>
                    <strong>${currentWeight.toFixed(1)} kg (${loadsVal} drum${loadsVal === 1 ? '' : 's'})</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Payment Timing:</span>
                    <span class="text-slate-700">${timingVal}</span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-[#182830] font-black uppercase">Total Charges:</span>
                    <strong class="font-recoleta text-base text-[#CB1B03]">₱${calculatedTotal.toFixed(2)}</strong>
                </div>
            `;

            if (window.TrowaConfirm) {
                window.TrowaConfirm({
                    title: 'Confirm Laundry Intake Order',
                    message: 'Please review and confirm order details before registering to laundry queue:',
                    badge: 'Order Finalization',
                    type: 'primary',
                    confirmText: 'Yes, Finalize & Start Wash ➔',
                    cancelText: 'Back to Review',
                    details: detailsHtml
                }, function() {
                    form.setAttribute('data-intake-confirmed', 'true');
                    const btn = document.getElementById('wizard-btn-submit');
                    if (btn) {
                        btn.click();
                    } else {
                        form.submit();
                    }
                });
                return false;
            }
        }

        // Confirmed! Clear temporary flag
        form.removeAttribute('data-intake-confirmed');

        if (payNowRadio?.checked) {
            const rawTender = parseFloat(tenderInput?.value);
            if (isNaN(rawTender) || rawTender <= 0) {
                // If cashier picked "Tender Now" but didn't type an amount, assume paid in full with exact total
                if (tenderInput) tenderInput.value = calculatedTotal.toFixed(2);
                if (hiddenAmountPaid) hiddenAmountPaid.value = calculatedTotal.toFixed(2);
                if (hiddenTenderedAmount) hiddenTenderedAmount.value = calculatedTotal.toFixed(2);
            } else {
                if (hiddenAmountPaid) hiddenAmountPaid.value = Math.min(rawTender, calculatedTotal).toFixed(2);
                if (hiddenTenderedAmount) hiddenTenderedAmount.value = rawTender.toFixed(2);
            }
        } else {
            if (hiddenAmountPaid) hiddenAmountPaid.value = '0';
            if (hiddenTenderedAmount) hiddenTenderedAmount.value = '0';
        }

        if (window.TrowaLoading) {
            window.TrowaLoading.show({
                header: '⚡ TROWA INTAKE TERMINAL ⚡',
                stage: 'WEIGHING & WASHING',
                title: 'Registering Laundry Ticket...',
                message: 'Calibrating wash load, allocating supplies, and prepping machine bay...'
            });
        }
    });

    // Initialize Default States
    if (serviceCheckboxes.length > 0) {
        serviceCheckboxes[0].checked = true;
    }
    recalculateServices();
    showStep(1);
})();
</script>
