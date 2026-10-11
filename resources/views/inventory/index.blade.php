@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            <span>Supply Stockroom &amp; Smart Assistant</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Supply Inventory
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Real-time stock velocity, burn-rate projections, automated recipes, and proactive replenishment guidance.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
        <button type="button" onclick="openAutoRestockModal()" class="rounded-xl border-3 border-[#182830] bg-[#CB1B03] px-4 py-2 font-recoleta text-xs font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer flex items-center gap-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span>1-Click Auto-Restock Deliveries</span>
        </button>
        <span class="badge border-[#182830] bg-[#FFFDF8] px-3 py-1 font-mono text-xs font-bold text-[#182830]">
            {{ $totalItems }} Tracked SKUs
        </span>
        <span class="badge border-[#182830] bg-amber-400 text-[#182830] px-3 py-1 font-mono text-xs font-black shadow-[1px_1px_0px_#182830] {{ $almostOutCount > 0 ? 'animate-pulse' : '' }}">
            <svg class="h-3.5 w-3.5 inline-block mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>{{ $almostOutCount }} Almost Out Medals
        </span>
        <span class="badge border-[#182830] bg-[#CB1B03] text-white px-3 py-1 font-mono text-xs font-black shadow-[1px_1px_0px_#182830]">
            <svg class="h-3.5 w-3.5 inline-block mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>{{ $depletedCount }} Depleted Medals
        </span>
        <span class="badge border-[#182830] bg-emerald-100 text-emerald-800 px-3 py-1 font-mono text-xs font-bold">
            <svg class="h-3.5 w-3.5 inline-block mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>{{ $healthyCount }} Healthy Stock Medals
        </span>
    </div>
</div>

<!-- 4 High-Impact Inventory KPI Cards -->
<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#A2C5D8]/20">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Active Inventory SKUs</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">{{ $totalItems }}</span>
            <span class="block font-mono text-[11px] text-slate-500">Tracked per sachet &amp; bulk</span>
        </div>
    </div>

    <div class="retro-panel flex items-start gap-3.5 p-4 {{ $lowStockCount > 0 ? 'bg-red-50/70 border-red-700/30' : 'bg-emerald-50/70 border-emerald-700/30' }}">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] {{ $lowStockCount > 0 ? 'bg-red-100 text-[#CB1B03]' : 'bg-emerald-100 text-emerald-800' }} shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 22 22 22 12 2"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Buffer Warning Status</span>
            <span class="block font-recoleta text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-[#CB1B03]' : 'text-emerald-700' }} leading-tight my-0.5">{{ $lowStockCount }} Alert SKUs</span>
            <span class="block font-mono text-[11px] text-slate-500">{{ $almostOutCount }} Low Buffer · {{ $depletedCount }} Depleted</span>
        </div>
    </div>

    <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#F7E6CB]/40">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Auto Recipes Active</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">{{ $usages->count() }} Recipes</span>
            <span class="block font-mono text-[11px] text-slate-500">Per-load automatic deductions</span>
        </div>
    </div>

    <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#25799B] text-white">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-white text-[#25799B] shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#A2C5D8]">Direct Suppliers</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#FFFDF8] leading-tight my-0.5">{{ $suppliers->count() }} Linked</span>
            <span class="block font-mono text-[11px] text-[#A2C5D8]">1-Click Restock Ready</span>
        </div>
    </div>
</div>

<!-- Interactive Navigation Toggles (Eliminates long vertical scrolling) -->
<div class="mb-6 flex flex-wrap items-center gap-2 border-b-2 border-[#182830]/20 pb-2">
    <button type="button" 
            onclick="switchInventoryTab('advisor')" 
            id="inv-tab-btn-advisor"
            class="inv-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        <span>Smart Advisor &amp; Action Alerts</span>
        @if(count($smartSuggestions) > 0)
            <span class="rounded-full bg-[#CB1B03] px-2 py-0.2 font-mono text-[10px] font-bold text-white">{{ count($smartSuggestions) }}</span>
        @endif
    </button>

    <button type="button" 
            onclick="switchInventoryTab('stock')" 
            id="inv-tab-btn-stock"
            class="inv-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        <span>Stock on Hand &amp; Movement History</span>
        <span class="rounded-full bg-[#25799B] px-2 py-0.2 font-mono text-[10px] font-bold text-white">{{ $items->count() }}</span>
    </button>

    <button type="button" 
            onclick="switchInventoryTab('recipes')" 
            id="inv-tab-btn-recipes"
            class="inv-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span>Auto-Consumption Recipes &amp; Config</span>
        <span class="rounded-full bg-[#182830] px-2 py-0.2 font-mono text-[10px] font-bold text-white">{{ $usages->count() }}</span>
    </button>

    <button type="button" 
            onclick="switchInventoryTab('suppliers')" 
            id="inv-tab-btn-suppliers"
            class="inv-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Direct Suppliers &amp; Auto-Restock</span>
        <span class="rounded-full bg-[#CB1B03] px-2 py-0.2 font-mono text-[10px] font-bold text-white">{{ $suppliers->count() }}</span>
    </button>
</div>

<!-- ========================================== -->
<!-- TAB 1: SMART ADVISOR & PROACTIVE ACTIONS   -->
<!-- ========================================== -->
<div id="inv-view-advisor" class="inv-view-pane space-y-6">
    <div class="retro-panel p-5">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-2.5 gap-2">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#CB1B03] opacity-75"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-[#CB1B03]"></span>
                </span>
                <div>
                    <h2 class="font-recoleta text-lg font-bold text-[#182830]">Trowa Smart Inventory Advisor</h2>
                    <p class="font-mono text-xs text-[#25799B]">Predictive burn rate analytics, stock-out forecasts &amp; replenishment directives</p>
                </div>
            </div>
            <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830] font-mono text-xs font-bold">
                Live AI Telemetry
            </span>
        </div>

        @if(count($smartSuggestions) > 0)
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($smartSuggestions as $tip)
                    <div class="flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $tip['severity'] === 'critical' ? 'bg-red-50 border-[#CB1B03]' : ($tip['severity'] === 'danger' ? 'bg-amber-50/80 border-amber-800' : 'bg-[#F7E6CB]/40') }} p-4.5 shadow-[3px_3px_0px_#182830]">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="badge border-[#182830] {{ $tip['severity'] === 'critical' ? 'bg-[#CB1B03] text-white' : ($tip['severity'] === 'danger' ? 'bg-amber-600 text-white' : 'bg-[#25799B] text-white') }} text-[10px] font-mono font-bold uppercase">
                                    {{ ucfirst($tip['severity']) }}
                                </span>
                                @if(isset($tip['item']))
                                    <span class="font-mono text-[10px] text-slate-600 font-bold">
                                        Burn: {{ $tip['item']->daily_burn }} {{ $tip['item']->unit }}/day
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-recoleta text-base font-bold text-[#182830]">{{ $tip['title'] }}</h3>
                            <p class="mt-1 text-xs text-slate-700 leading-relaxed font-sans">{{ $tip['message'] }}</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#182830]/15 flex items-center justify-between gap-2">
                            @if(isset($tip['item']) && $tip['suggested_quantity'])
                                <span class="font-mono text-xs font-bold text-[#CB1B03]">Order +{{ $tip['suggested_quantity'] }} {{ $tip['item']->unit }}</span>
                            @else
                                <span class="font-mono text-xs text-slate-600">Action Recommended</span>
                            @endif

                            <button type="button" 
                                    onclick="openInventoryActionModal(
                                        '{{ addslashes($tip['title']) }}', 
                                        '{{ $tip['severity'] }}', 
                                        '{{ addslashes($tip['message']) }}', 
                                        '{{ addslashes($tip['action_title'] ?? '') }}', 
                                        '{{ addslashes(json_encode($tip['action_steps'] ?? [])) }}',
                                        '{{ $tip['action_type'] ?? 'reorder' }}',
                                        '{{ $tip['item_id'] ?? '' }}',
                                        '{{ addslashes($tip['item_name'] ?? '') }}',
                                        '{{ $tip['unit'] ?? '' }}',
                                        '{{ $tip['suggested_quantity'] ?? '' }}'
                                    )"
                                    class="retro-btn-primary text-xs px-3 py-1.5 flex items-center gap-1.5 cursor-pointer">
                                <span>Take Action</span>
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-emerald-700/30 bg-emerald-50 p-4 text-emerald-900 font-mono text-xs flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div>
                    <strong>Stock Health is Exemplary!</strong>
                    <p class="text-emerald-800 text-[11px] mt-0.5">All consumables have more than 14 days of estimated runway based on recent washer load turnover. All active services have automated deduction recipes attached.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 2: STOCK ON HAND & MOVEMENT HISTORY    -->
<!-- ========================================== -->
<div id="inv-view-stock" class="inv-view-pane hidden space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#CB1B03]">Live Stockroom</span>
            <h2 class="font-recoleta text-xl font-bold text-[#182830]">Stock on Hand &amp; Velocity Ledger</h2>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" 
                    onclick="switchInventoryTab('recipes')"
                    class="retro-btn-secondary text-xs px-3 py-1.5 flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Add Item / Recipe</span>
            </button>
        </div>
    </div>

    <!-- Visual Stockroom Showcase: What We Have Inside Our Inventory & Warning Medals -->
    <div class="retro-panel p-5 bg-[#FFFDF8]">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-2.5 gap-2">
            <div>
                <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] font-mono text-[10px] font-bold uppercase">
                    Live Stock Count &amp; Warning Medals
                </span>
                <h3 class="font-recoleta text-lg font-bold text-[#182830] mt-0.5">What Is Inside Our Inventory</h3>
                <p class="font-mono text-xs text-[#25799B]">Real-time count of all shop supplies (automatic per sachet tracking) with active warning medals.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-slate-600">Total: {{ $items->count() }} supplies registered</span>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($items as $item)
                @php
                    $onHand = (float) $item->quantity_on_hand;
                    $threshold = (float) $item->low_stock_threshold;
                    $isDepleted = $onHand <= 0;
                    $isAlmostOut = !$isDepleted && $onHand <= $threshold;
                @endphp
                <div class="flex flex-col justify-between rounded-2xl border-2 border-[#182830] {{ $isDepleted ? 'bg-red-50/60 border-red-800' : ($isAlmostOut ? 'bg-amber-50/80 border-amber-800' : 'bg-[#FFFDF8]') }} p-3.5 shadow-[2px_2px_0px_#182830]">
                    <div>
                        <!-- Top item name & unit perception tag -->
                        <div class="flex items-start justify-between gap-1.5 mb-2">
                            <span class="font-recoleta text-base font-bold text-[#182830] leading-tight">
                                {{ $item->name }}
                            </span>
                            <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] text-[9px] font-mono font-bold shrink-0">
                                {{ $item->unit === 'sachet' ? 'Per Sachet' : $item->unit }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div class="mb-3">
                            @if($isDepleted)
                                <div class="inline-flex items-center gap-1.5 rounded-full border-2 border-[#182830] bg-[#CB1B03] text-white px-2.5 py-0.5 font-mono text-[11px] font-black shadow-[1px_1px_0px_#182830]">
                                    <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                    <span>Depleted Stock Medal</span>
                                </div>
                                <span class="block font-mono text-[10px] text-red-700 font-bold mt-1">Critical Empty! 0 {{ $item->unit }}s left</span>
                            @elseif($isAlmostOut)
                                <div class="inline-flex items-center gap-1.5 rounded-full border-2 border-[#182830] bg-amber-400 text-[#182830] px-2.5 py-0.5 font-mono text-[11px] font-black shadow-[1px_1px_0px_#182830] animate-pulse">
                                    <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span>Almost Out Medal</span>
                                </div>
                                <span class="block font-mono text-[10px] text-amber-900 font-bold mt-1">Warning: Below safety threshold!</span>
                            @else
                                <div class="inline-flex items-center gap-1.5 rounded-full border border-emerald-600 bg-emerald-100 text-emerald-800 px-2.5 py-0.5 font-mono text-[11px] font-bold">
                                    <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span>Stock Healthy Medal</span>
                                </div>
                                <span class="block font-mono text-[10px] text-emerald-800 mt-1">Optimal runway maintained</span>
                            @endif
                        </div>

                        <!-- Exact Quantities Display -->
                        <div class="rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-2.5 space-y-1 font-mono text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">How much we have:</span>
                                <strong class="text-base font-black {{ $isDepleted ? 'text-[#CB1B03]' : ($isAlmostOut ? 'text-amber-800' : 'text-[#182830]') }}">
                                    {{ $onHand }} {{ $item->unit }}{{ $item->unit === 'sachet' ? 's' : '' }}
                                </strong>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-600">
                                <span>Safety buffer:</span>
                                <span>{{ $threshold }} {{ $item->unit }}{{ $item->unit === 'sachet' ? 's' : '' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-600">
                                <span>Daily consumption:</span>
                                <span>{{ $item->daily_burn }} {{ $item->unit }}/day</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-[#182830]/15 flex items-center justify-between">
                        <span class="font-mono text-[10px] {{ $item->days_remaining <= 5 ? 'text-[#CB1B03] font-bold' : 'text-slate-500' }}">
                            {{ $item->days_remaining }} days runway
                        </span>
                        <a href="#movement-item-{{ $item->id }}" class="font-mono text-[11px] font-bold text-[#25799B] hover:text-[#CB1B03] underline">
                            Quick Restock ➔
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-slate-500 font-mono text-xs">
                    No supplies recorded in inventory yet. Click "Add Item" to record Surf, Downy, or other detergents.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Master Stock In & Stock Out Activity Ledger (Audit Date & Time History) -->
    <div class="retro-panel p-5 bg-[#FFFDF8]">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#182830]/15 pb-2.5">
            <div>
                <span class="badge border-[#182830] bg-[#25799B] text-white font-mono text-[10px] font-bold uppercase">
                    Audit Date &amp; Time Log
                </span>
                <h3 class="font-recoleta text-lg font-bold text-[#182830] mt-0.5">
                    Recent Stock In &amp; Stock Out Log
                </h3>
                <p class="font-mono text-xs text-slate-600">Chronological timestamp of all supply deliveries, manual usage deductions, and customer batch dispensings.</p>
            </div>
            <span class="font-mono text-xs text-slate-500">
                Latest {{ $recentMovements->count() }} movements recorded
            </span>
        </div>

        @if($recentMovements->isEmpty())
            <div class="py-6 text-center font-mono text-xs text-slate-500">
                No inventory stock movements recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead class="border-b-2 border-[#182830]/20 text-[#25799B]">
                        <tr>
                            <th class="py-2.5 px-2">Date &amp; Time of Stock In / Out</th>
                            <th class="px-2">Movement Type</th>
                            <th class="px-2">Supply Item</th>
                            <th class="px-2">Quantity Change</th>
                            <th class="px-2">Order / PO Ref</th>
                            <th class="px-2">Recorded By</th>
                            <th class="px-2">Audit Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#182830]/10">
                        @foreach($recentMovements as $mv)
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="py-2.5 px-2 text-[#182830]">
                                    <strong class="block font-bold">{{ $mv->created_at->format('M d, Y') }}</strong>
                                    <span class="text-[10px] text-slate-500">{{ $mv->created_at->format('h:i:s A') }} ({{ $mv->created_at->diffForHumans() }})</span>
                                </td>
                                <td class="px-2">
                                    @if($mv->movement_type === 'stock_in')
                                        <span class="badge border-2 border-emerald-700 bg-emerald-100 text-emerald-900 font-black text-[10px] inline-flex items-center gap-1">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            <span>Stock In (+)</span>
                                        </span>
                                    @elseif(in_array($mv->movement_type, ['usage', 'stock_out']))
                                        <span class="badge border-2 border-red-700 bg-red-100 text-[#CB1B03] font-black text-[10px] inline-flex items-center gap-1">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 14 12 9 7 14"/><line x1="12" y1="9" x2="12" y2="21"/></svg>
                                            <span>Stock Out (-)</span>
                                        </span>
                                    @else
                                        <span class="badge border border-cyan-700 bg-cyan-100 text-cyan-900 font-bold text-[10px]">
                                            Adjustment
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 font-bold text-[#182830]">
                                    {{ $mv->item?->name ?? 'Supply' }}
                                </td>
                                <td class="px-2 font-bold {{ $mv->quantity_change > 0 ? 'text-emerald-700' : 'text-[#CB1B03]' }}">
                                    {{ $mv->quantity_change > 0 ? '+' : '' }}{{ $mv->quantity_change }} {{ $mv->item?->unit ?? '' }}
                                </td>
                                <td class="px-2 text-[#25799B] font-bold">
                                    {{ $mv->order?->order_number ?? '—' }}
                                </td>
                                <td class="px-2 text-slate-700">
                                    {{ $mv->recordedBy?->name ?? 'System' }}
                                </td>
                                <td class="px-2 text-slate-600 max-w-xs truncate" title="{{ $mv->notes }}">
                                    {{ $mv->notes ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recentMovements->hasPages())
                <div class="border-t-2 border-[#182830] bg-[#FFFDF8]">
                    {{ $recentMovements->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Inventory Items Accordion Cards -->
    <div class="space-y-4">
        @foreach($items as $item)
            @php
                $onHand = (float) $item->quantity_on_hand;
                $threshold = (float) $item->low_stock_threshold;
                $isDepleted = $onHand <= 0;
                $isAlmostOut = !$isDepleted && $onHand <= $threshold;
            @endphp
            <details id="movement-item-{{ $item->id }}" class="retro-panel overflow-hidden group" {{ $item->is_low ? 'open' : '' }}>
                <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 border-b-2 border-[#182830]/15 bg-[#A2C5D8]/20 p-4 select-none">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="font-recoleta text-lg font-bold text-[#182830]">
                            {{ $item->name }}
                        </span>
                        <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] text-[10px] font-mono font-bold">
                            {{ $item->unit === 'sachet' ? 'Per Sachet' : $item->unit }}
                        </span>

                        @if($isDepleted)
                            <span class="badge border-2 border-[#182830] bg-[#CB1B03] text-white text-[10px] font-mono font-black flex items-center gap-1">
                                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                <span>Depleted Stock Medal</span>
                            </span>
                        @elseif($isAlmostOut)
                            <span class="badge border-2 border-[#182830] bg-amber-400 text-[#182830] animate-pulse text-[10px] font-mono font-black flex items-center gap-1">
                                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span>Almost Out Medal · Low Stock Alert</span>
                            </span>
                        @else
                            <span class="badge border border-emerald-600 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold flex items-center gap-1">
                                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span>Stock Healthy Medal</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="text-right">
                            <span class="block font-mono text-xs text-slate-600">Burn: <strong>{{ $item->daily_burn }} {{ $item->unit }}/day</strong></span>
                            <span class="block font-mono text-[10px] {{ $item->days_remaining <= 5 ? 'text-[#CB1B03] font-bold' : 'text-slate-500' }}">
                                Runway: {{ $item->days_remaining }} days left
                            </span>
                        </div>

                        <span class="badge border-[#182830] {{ $isDepleted ? 'bg-[#CB1B03] text-white font-black' : ($isAlmostOut ? 'bg-amber-400 text-[#182830] font-black' : 'bg-[#FFFDF8] text-[#182830] font-black') }} text-sm px-3 py-1 shadow-[1px_1px_0px_#182830]">
                            {{ $item->quantity_on_hand }} {{ $item->unit }} Available
                        </span>
                        <span class="font-mono text-xs text-[#25799B]">Threshold: {{ $item->low_stock_threshold }}</span>
                    </div>
                </summary>

                <div class="p-4 bg-[#FFFDF8]">
                    <!-- Quick Movement Form -->
                    <form method="POST" action="{{ route('inventory.movement', $item) }}" class="mb-4 grid gap-2 sm:grid-cols-[160px_140px_1fr_auto] bg-[#F7E6CB]/30 p-3 rounded-xl border border-[#182830]/15">
                        @csrf
                        <div>
                            <label class="block font-mono text-[10px] font-bold text-[#182830] mb-1">Movement Type</label>
                            <select name="movement_type" class="field text-xs">
                                <option value="stock_in">Stock received (＋)</option>
                                <option value="stock_out">Manual usage (−)</option>
                                <option value="adjustment">Count adjustment (=)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-mono text-[10px] font-bold text-[#182830] mb-1">Quantity</label>
                            <input id="qty-input-{{ $item->id }}" name="quantity" type="number" min="0.001" step="0.001" class="field text-xs font-mono" placeholder="Amount" required>
                        </div>
                        <div>
                            <label class="block font-mono text-[10px] font-bold text-[#182830] mb-1">Audit Notes</label>
                            <input name="notes" class="field text-xs" placeholder="Supplier invoice, replenishment, wastage reason...">
                        </div>
                        <div class="flex items-end">
                            <button class="retro-btn-secondary text-xs h-10 w-full sm:w-auto">Record Movement</button>
                        </div>
                    </form>

                    <!-- History Table (Contained, Clean) -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-mono text-xs">
                            <thead class="border-b border-[#182830]/15 text-[#25799B]">
                                <tr>
                                    <th class="py-2.5">Date of Stock In / Out</th>
                                    <th>Movement Type</th>
                                    <th>Quantity Change</th>
                                    <th>Order / PO Ref</th>
                                    <th>Recorded by</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#182830]/10">
                                @forelse($item->movements->take(12) as $movement)
                                    <tr>
                                        <td class="py-2.5 text-slate-800">
                                            <strong class="block font-bold">{{ $movement->created_at->format('M d, Y') }}</strong>
                                            <span class="text-[10px] text-slate-500">{{ $movement->created_at->format('h:i A') }}</span>
                                        </td>
                                        <td>
                                            @if($movement->movement_type === 'stock_in')
                                                <span class="badge border-2 border-emerald-700 bg-emerald-100 text-emerald-900 font-black text-[10px] inline-flex items-center gap-1">
                                                    <span>Stock In (Arrival)</span>
                                                </span>
                                            @elseif(in_array($movement->movement_type, ['usage', 'stock_out']))
                                                <span class="badge border-2 border-red-700 bg-red-100 text-[#CB1B03] font-black text-[10px] inline-flex items-center gap-1">
                                                    <span>Stock Out (Usage)</span>
                                                </span>
                                            @else
                                                <span class="badge border border-cyan-700 bg-cyan-100 text-cyan-900 font-bold text-[10px]">
                                                    Adjustment
                                                </span>
                                            @endif
                                        </td>
                                        <td class="font-bold {{ $movement->quantity_change > 0 ? 'text-emerald-700' : 'text-[#CB1B03]' }}">
                                            {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }} {{ $item->unit }}
                                        </td>
                                        <td class="text-[#25799B] font-bold">{{ $movement->order?->order_number ?? '—' }}</td>
                                        <td>{{ $movement->recordedBy->name }}</td>
                                        <td class="text-slate-600">{{ $movement->notes ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-slate-500">No stock movements recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </details>
        @endforeach
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 3: AUTO CONSUMPTION RECIPES & CONFIG   -->
<!-- ========================================== -->
<div id="inv-view-recipes" class="inv-view-pane hidden space-y-6">
    <div class="grid gap-6 xl:grid-cols-[1fr_1.5fr]">
        <!-- Add Consumable Item Form -->
        <div class="retro-panel p-5">
            <div class="mb-4 border-b-2 border-[#182830]/15 pb-2.5">
                <div class="flex items-center justify-between">
                    <h2 class="font-recoleta text-lg font-bold text-[#182830]">Add Supply / Consumable Stock</h2>
                    <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] font-mono text-[9px] font-bold">
                        Auto: Per Sachet
                    </span>
                </div>
                <p class="font-mono text-xs text-[#25799B]">Register new soap, detergent or fabcon (tracked per sachet by default)</p>
            </div>

            <form method="POST" action="{{ route('inventory.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Item Name *</label>
                    <input name="name" class="field text-xs" placeholder="e.g. Surf Fabcon, Downy Sunrise Fresh, Ariel Detergent" required>
                    <p class="text-[11px] font-mono text-[#25799B] mt-1">
                        Note: All laundry supplies (e.g. Surf, Downy, Ariel) are automatically tagged and perceived per sachet.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Unit (Auto)</label>
                        <input name="unit" class="field text-xs font-mono" value="sachet" placeholder="sachet">
                    </div>
                    <div>
                        <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Opening Count</label>
                        <input name="quantity_on_hand" type="number" min="0" step="0.001" class="field text-xs font-mono" value="20" required>
                    </div>
                    <div>
                        <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Warning Buffer</label>
                        <input name="low_stock_threshold" type="number" min="0" step="0.001" class="field text-xs font-mono" value="5" required>
                    </div>
                </div>
                <button class="retro-btn-primary w-full text-xs mt-2">Add Supply to Inventory ➔</button>
            </form>
        </div>

        <!-- Automatic Service Consumption Configuration -->
        <div class="retro-panel p-5">
            <div class="mb-3 border-b-2 border-[#182830]/15 pb-2.5">
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Automatic Service Consumption</h2>
                <p class="font-mono text-xs text-[#25799B]">Configure consumable quantity deducted automatically per machine load.</p>
            </div>

            <form method="POST" action="{{ route('inventory.usage.store') }}" id="recipe-setup-form" class="grid gap-2.5 sm:grid-cols-3 bg-[#F7E6CB]/30 p-3 rounded-xl border border-[#182830]/15">
                @csrf
                <div>
                    <label class="block font-mono text-[10px] font-bold text-[#182830] mb-1">Service</label>
                    <select name="service_id" id="recipe-service-select" class="field text-xs" required>
                        <option value="">-- Choose Service --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-mono text-[10px] font-bold text-[#182830] mb-1">Consumable</label>
                    <select name="inventory_item_id" class="field text-xs" required>
                        <option value="">-- Consumable --</option>
                        @foreach($items->where('is_active', true) as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-mono text-[10px] font-bold text-[#182830] mb-1">Qty / Machine Load</label>
                    <div class="flex gap-2">
                        <input name="quantity_per_load" type="number" min="0.001" step="0.001" class="field text-xs font-mono" placeholder="0.200" required>
                        <button class="retro-btn-primary text-xs px-3">Save</button>
                    </div>
                </div>
            </form>

            <div class="mt-4 space-y-2">
                @forelse($usages as $usage)
                    <div class="flex items-center justify-between rounded-lg border border-[#182830]/15 bg-[#FFFDF8] px-3.5 py-2 font-mono text-xs shadow-[1px_1px_0px_#182830]">
                        <span class="text-[#182830] font-bold">
                            {{ $usage->service->name }} uses {{ $usage->quantity_per_load }} {{ $usage->inventoryItem->unit }} {{ $usage->inventoryItem->name }} / load
                        </span>
                        <form method="POST" action="{{ route('inventory.usage.destroy', $usage) }}" data-confirm="Are you sure you want to remove recipe usage of {{ $usage->inventoryItem->name }} for {{ $usage->service->name }}?" data-confirm-title="Remove Service Recipe" data-confirm-type="danger" data-confirm-btn="Yes, Remove">
                            @csrf @method('DELETE')
                            <button type="submit" class="font-mono text-xs font-bold text-[#CB1B03] hover:underline cursor-pointer">Remove ×</button>
                        </form>
                    </div>
                @empty
                    <p class="font-mono text-xs text-slate-500 py-2">No service consumption recipes configured.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 4: DIRECT SUPPLIERS & AUTO-RESTOCK    -->
<!-- ========================================== -->
<div id="inv-view-suppliers" class="inv-view-pane hidden space-y-6">
    <!-- Header Banner & 1-Click Restock Callout -->
    <div class="retro-panel p-5 bg-[#FFFDF8]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b-2 border-[#182830]/15 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="badge border-[#182830] bg-[#CB1B03] text-white font-mono text-[10px] font-black uppercase">
                        Direct Distributor Integration
                    </span>
                    <span class="font-mono text-xs text-[#25799B] font-bold">1-Click Automated Intake</span>
                </div>
                <h2 class="font-recoleta text-2xl font-black text-[#182830] mt-1">
                    Direct Suppliers &amp; Automated Restock
                </h2>
                <p class="font-mono text-xs text-slate-600 mt-0.5 max-w-xl">
                    Eliminate manual inventory counting. Receive supplies from direct distributors with 1 click: stock balances automatically update, purchase orders are logged, and shop expenses are recorded.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openSupplierModal()" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Register Supplier</span>
                </button>
                <button type="button" onclick="openAutoRestockModal()" class="rounded-xl border-3 border-[#182830] bg-[#CB1B03] px-4 py-2 font-recoleta text-xs font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    <span>1-Click Auto-Restock Deliveries</span>
                </button>
            </div>
        </div>

        <!-- Supplier Directory Cards Grid -->
        <div class="mt-5">
            <h3 class="font-mono text-xs font-bold uppercase text-[#182830] mb-3 flex items-center gap-2">
                <span>Registered Direct Suppliers ({{ $suppliers->count() }})</span>
            </h3>

            @if($suppliers->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-[#182830]/30 bg-slate-50 p-6 text-center font-mono text-xs text-slate-500">
                    No suppliers registered yet. Click "Register Supplier" above to add your direct chemical/soap distributor.
                </div>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($suppliers as $supplier)
                        <div class="flex flex-col justify-between rounded-xl border-2 border-[#182830] bg-white p-4 shadow-[3px_3px_0px_#182830] transition hover:translate-y-[-1px]">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="font-recoleta text-base font-bold text-[#182830] leading-tight">
                                        {{ $supplier->name }}
                                    </h4>
                                    <span class="badge border-[#182830] {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }} text-[9px] font-mono font-bold shrink-0">
                                        {{ $supplier->is_active ? 'Active Partner' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="space-y-1 font-mono text-xs text-slate-600 mb-3">
                                    @if($supplier->contact_person)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-400">Contact:</span>
                                            <strong class="text-slate-800">{{ $supplier->contact_person }}</strong>
                                        </div>
                                    @endif
                                    @if($supplier->phone)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-400">Phone:</span>
                                            <span class="text-slate-800">{{ $supplier->phone }}</span>
                                        </div>
                                    @endif
                                    @if($supplier->email)
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="text-slate-400">Email:</span>
                                            <span class="text-slate-800 truncate">{{ $supplier->email }}</span>
                                        </div>
                                    @endif
                                    @if($supplier->payment_terms)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-400">Terms:</span>
                                            <span class="text-[#25799B] font-bold">{{ $supplier->payment_terms }}</span>
                                        </div>
                                    @endif
                                    @if($supplier->notes)
                                        <p class="text-[11px] text-slate-500 italic mt-1 border-t border-slate-100 pt-1">
                                            &ldquo;{{ $supplier->notes }}&rdquo;
                                        </p>
                                    @endif

                                    <!-- Direct Contact Quick Action Buttons -->
                                    <div class="mt-2.5 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                        @if($supplier->email)
                                            <a href="mailto:{{ $supplier->email }}?subject=Supply%20Inquiry%20-%20Trowa%20Laundry" 
                                               class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-[#182830] bg-[#FFFDF8] px-2.5 py-1.5 font-mono text-[10px] font-bold text-[#182830] hover:bg-[#F7E6CB] transition shadow-[1px_1px_0px_#182830]"
                                               title="Email {{ $supplier->name }} ({{ $supplier->email }})">
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                                <span>Email Supplier</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-[#182830]/15 flex items-center justify-between font-mono text-xs">
                                <span class="text-slate-500 text-[11px]">
                                    {{ $supplier->orders_count }} orders logged
                                </span>
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            onclick='editSupplier(@json($supplier))'
                                            class="text-[#25799B] font-bold hover:underline cursor-pointer">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('inventory.suppliers.destroy', $supplier) }}" data-confirm="Are you sure you want to remove supplier '{{ $supplier->name }}'?" data-confirm-title="Remove Supplier" data-confirm-type="danger" data-confirm-btn="Yes, Remove">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[#CB1B03] font-bold hover:underline cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Delivery Intake / Purchase Orders History Table -->
    <div class="retro-panel p-5 bg-[#FFFDF8]">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#182830]/15 pb-2.5">
            <div>
                <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] font-mono text-[10px] font-bold uppercase">
                    Delivery Audit Ledger
                </span>
                <h3 class="font-recoleta text-lg font-bold text-[#182830] mt-0.5">
                    Automated Purchase Orders &amp; Restock History
                </h3>
            </div>
            <span class="font-mono text-xs text-slate-500">
                Latest {{ $recentSupplierOrders->count() }} batches
            </span>
        </div>

        @if($recentSupplierOrders->isEmpty())
            <div class="py-8 text-center font-mono text-xs text-slate-500">
                No automated restock orders logged yet. Use "1-Click Auto-Restock Deliveries" above to auto-log your first delivery.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead class="border-b-2 border-[#182830]/20 text-[#25799B]">
                        <tr>
                            <th class="py-2.5 px-2">PO &amp; Invoice Ref</th>
                            <th class="px-2">Received Date &amp; Time</th>
                            <th class="px-2">Supplier Details</th>
                            <th class="px-2">Items Received Breakdown</th>
                            <th class="px-2 text-right">Invoice Cost</th>
                            <th class="px-2">Contact &amp; Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#182830]/10">
                        @foreach($recentSupplierOrders as $po)
                            @php
                                $poData = [
                                    'order_number' => $po->order_number,
                                    'date_formatted' => $po->received_at ? $po->received_at->format('M d, Y · h:i A') : $po->created_at->format('M d, Y · h:i A'),
                                    'supplier_name' => $po->supplier?->name ?? 'Direct Distributor',
                                    'contact_person' => $po->supplier?->contact_person ?? 'Wholesale Representative',
                                    'phone' => $po->supplier?->phone ?? '',
                                    'email' => $po->supplier?->email ?? '',
                                    'address' => $po->supplier?->address ?? 'Central Warehouse Depot',
                                    'payment_terms' => $po->supplier?->payment_terms ?? 'Standard Wholesale Terms',
                                    'total_cost' => (float)$po->total_cost,
                                    'expense_id' => $po->expense_id,
                                    'expense_ref' => $po->expense?->reference_number ?? $po->order_number,
                                    'notes' => $po->notes,
                                    'recorded_by' => $po->recordedBy?->name ?? 'Admin',
                                    'items' => is_array($po->items_payload) ? $po->items_payload : [],
                                ];
                            @endphp
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="py-3 px-2">
                                    <span class="font-black text-[#CB1B03] block text-xs">{{ $po->order_number }}</span>
                                    <span class="badge border border-[#182830]/30 bg-emerald-50 text-emerald-800 text-[9px] font-mono font-bold uppercase mt-0.5">
                                        Received &amp; Stocked
                                    </span>
                                </td>
                                <td class="px-2 text-slate-700 text-[11px]">
                                    <strong class="block text-xs text-[#182830]">{{ $po->received_at ? $po->received_at->format('M d, Y') : $po->created_at->format('M d, Y') }}</strong>
                                    <span class="text-[10px] text-slate-500">{{ $po->received_at ? $po->received_at->format('h:i A') : $po->created_at->format('h:i A') }} ({{ $po->received_at ? $po->received_at->diffForHumans() : $po->created_at->diffForHumans() }})</span>
                                </td>
                                <td class="px-2 font-mono text-xs">
                                    <strong class="text-[#182830] block text-xs">{{ $po->supplier?->name ?? 'Direct Distributor' }}</strong>
                                    @if($po->supplier?->contact_person)
                                        <span class="block text-[11px] text-slate-600">Rep: {{ $po->supplier->contact_person }}</span>
                                    @endif
                                    @if($po->supplier?->phone)
                                        <span class="block text-[10px] text-slate-500">Tel: {{ $po->supplier->phone }}</span>
                                    @endif
                                </td>
                                <td class="px-2 max-w-xs">
                                    <div class="flex flex-wrap gap-1">
                                        @if(is_array($po->items_payload))
                                            @foreach($po->items_payload as $itm)
                                                <span class="inline-block rounded bg-[#F7E6CB] px-1.5 py-0.5 text-[10px] font-bold text-[#182830] border border-[#182830]/20">
                                                    {{ $itm['item_name'] ?? 'Supply' }} +{{ $itm['quantity_added'] ?? 0 }} {{ $itm['unit'] ?? '' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-slate-500 text-[11px]">{{ $po->notes }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 text-right font-black text-[#182830]">
                                    <span class="block text-sm">₱{{ number_format((float) $po->total_cost, 2) }}</span>
                                    @if($po->expense_id)
                                        <span class="block text-[9px] text-emerald-700 font-bold uppercase">Expense Auto-Logged</span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($po->supplier?->email)
                                            <a href="mailto:{{ $po->supplier->email }}?subject=Invoice%20Inquiry%20-%20{{ $po->order_number }}" 
                                               class="p-1 rounded-lg border border-[#182830] bg-white text-[#182830] hover:bg-[#F7E6CB] transition shadow-[1px_1px_0px_#182830]"
                                               title="Email {{ $po->supplier->name }} ({{ $po->supplier->email }})">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                            </a>
                                        @endif
                                        <button type="button" 
                                                onclick='openInvoiceVoucherModal(@json($poData))'
                                                class="rounded-lg border border-[#182830] bg-[#FFFDF8] px-2 py-1 font-mono text-[10px] font-bold text-[#25799B] hover:bg-[#F7E6CB] transition shadow-[1px_1px_0px_#182830] cursor-pointer">
                                            View Invoice ➔
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recentSupplierOrders->hasPages())
                <div class="border-t-2 border-[#182830] bg-[#FFFDF8] mt-2">
                    {{ $recentSupplierOrders->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- INTERACTIVE TAKE ACTION INVENTORY MODAL    -->
<!-- ========================================== -->
<div id="inv-action-modal" class="fixed inset-0 z-[500] hidden bg-[#182830]/75 backdrop-blur-xs flex items-center justify-center p-4" style="z-index: 500;">
    <div class="relative w-full max-w-lg rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[6px_6px_0px_#182830]">
        <button type="button" onclick="closeInventoryActionModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <div class="flex items-center gap-2 mb-2">
            <span id="inv-modal-badge" class="badge border-[#182830] bg-[#CB1B03] text-white text-[10px] font-mono font-bold uppercase"></span>
            <span class="font-mono text-[10px] text-[#25799B] font-bold">Inventory Replenishment Directive</span>
        </div>

        <h3 id="inv-modal-title" class="font-recoleta text-xl font-extrabold text-[#182830]"></h3>
        <p id="inv-modal-message" class="mt-2 text-xs text-slate-700 leading-relaxed font-sans"></p>

        <!-- What To Do / Steps -->
        <div class="mt-4 rounded-xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-4">
            <h4 class="font-mono text-xs font-bold uppercase text-[#182830] mb-2 flex items-center gap-1.5">
                <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Standard Operational Guidance (What To Do):
            </h4>
            <ol id="inv-modal-steps" class="space-y-1.5 font-mono text-xs text-slate-800 list-decimal list-inside"></ol>
        </div>

        <!-- Embedded Quick Restock Form (For Reorders) -->
        <div id="inv-modal-reorder-container" class="mt-4 rounded-xl border-2 border-[#182830] bg-white p-4 shadow-[2px_2px_0px_#182830]">
            <h4 class="font-recoleta text-sm font-bold text-[#182830] mb-2 flex items-center gap-1.5">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Record Immediate Stock-In Arrival</span>
            </h4>
            <p class="text-[11px] text-slate-600 mb-3 font-sans">
                Once supplies have been delivered to the station, confirm stock-in below to update the inventory ledger and clear this alert:
            </p>

            <form method="POST" id="inv-modal-form" action="" class="space-y-2.5">
                @csrf
                <input type="hidden" name="movement_type" value="stock_in">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-mono text-[10px] font-bold text-[#182830] mb-0.5">Quantity Received</label>
                        <div class="flex items-center">
                            <input type="number" step="0.001" min="0.001" name="quantity" id="inv-modal-qty" class="field text-xs font-mono font-bold" required>
                            <span id="inv-modal-unit" class="ml-1.5 font-mono text-xs text-slate-600 font-bold"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block font-mono text-[10px] font-bold text-[#182830] mb-0.5">Receipt / Note</label>
                        <input type="text" name="notes" id="inv-modal-notes" class="field text-xs" value="Replenishment from Smart Advisor recommendation">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeInventoryActionModal()" class="retro-btn-secondary text-xs px-3 py-1.5">Cancel</button>
                    <button type="submit" class="retro-btn-primary text-xs px-4 py-1.5 flex items-center gap-1.5 cursor-pointer">
                        <span>Confirm Stock-In ➔</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Alternative CTA (For Recipe Setup) -->
        <div id="inv-modal-recipe-container" class="mt-4 hidden pt-2 flex items-center justify-end gap-2">
            <button type="button" onclick="closeInventoryActionModal()" class="retro-btn-secondary text-xs px-3 py-1.5">Close</button>
            <button type="button" onclick="closeInventoryActionModal(); switchInventoryTab('recipes');" class="retro-btn-primary text-xs px-4 py-1.5 flex items-center gap-1.5">
                <span>Configure Service Recipes ➔</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: 1-CLICK AUTO-RESTOCK FROM SUPPLIER                                 -->
<!-- ========================================================================= -->
<div id="auto-restock-modal" class="fixed inset-0 z-[500] hidden bg-[#182830]/80 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" style="z-index: 500;">
    <div class="relative w-full max-w-lg rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[8px_8px_0px_#182830] my-8">
        <button type="button" onclick="closeAutoRestockModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <div class="flex items-center gap-2 mb-2">
            <span class="rounded-md border border-[#182830] bg-[#CB1B03] px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-white shadow-[1px_1px_0px_#182830]">
                1-Click Automation
            </span>
            <span class="font-mono text-xs text-[#25799B] font-bold">Distributor Restock Intake</span>
        </div>

        <h3 class="font-recoleta text-2xl font-black text-[#182830]">
            1-Click Auto-Restock Deliveries
        </h3>
        <p class="text-xs text-slate-600 mt-1 font-sans">
            Instantly log arriving supplies from your direct supplier without typing item by item. Stock balances, supplier PO logs, and store expenses will be created automatically.
        </p>

        <form method="POST" action="{{ route('inventory.auto-restock') }}" data-confirm="Auto-log and update stock inventory from this supplier delivery?" data-confirm-title="Confirm 1-Click Auto-Restock" data-confirm-type="check" data-confirm-btn="Yes, Auto-Restock Now" class="mt-5 space-y-4">
            @csrf

            <!-- Mode Selection Radios -->
            <div>
                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-2">
                    Select Restock Mode <span class="text-[#CB1B03]">*</span>
                </label>
                <div class="grid grid-cols-1 gap-2.5">
                    <label class="flex items-start gap-3 rounded-xl border-2 border-[#182830] bg-amber-50/70 p-3 cursor-pointer hover:bg-amber-100 transition shadow-[2px_2px_0px_#182830]">
                        <input type="radio" name="mode" value="low_depleted" checked class="mt-0.5 h-4 w-4 text-[#CB1B03] focus:ring-0">
                        <div class="flex-1">
                            <strong class="font-recoleta text-sm font-bold text-[#182830] block">
                                Restock Depleted &amp; Low Stock SKUs (Recommended)
                            </strong>
                            <span class="font-mono text-[11px] text-slate-600 block mt-0.5">
                                Automatically scans all items at or below buffer threshold and restocks recommended commercial quantities ({{ $lowStockCount }} items currently flagged).
                            </span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 cursor-pointer hover:bg-slate-100 transition shadow-[2px_2px_0px_#182830]">
                        <input type="radio" name="mode" value="commercial_pack" class="mt-0.5 h-4 w-4 text-[#CB1B03] focus:ring-0">
                        <div class="flex-1">
                            <strong class="font-recoleta text-sm font-bold text-[#182830] block">
                                Standard Commercial Pack (+50 Sachets / +10L Liquids)
                            </strong>
                            <span class="font-mono text-[11px] text-slate-600 block mt-0.5">
                                Adds a bulk distributor delivery pack of 50 sachets for each dry/sachet SKU and 10 liters for liquids.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Supplier Selection -->
            <div>
                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Select Direct Supplier
                </label>
                <select name="supplier_id" class="field text-xs font-mono">
                    @forelse($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->contact_person ?? 'Direct' }})</option>
                    @empty
                        <option value="">No suppliers registered (will record as General Distributor)</option>
                    @endforelse
                </select>
            </div>

            <!-- Cost & Expense Accounting -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-mono text-xs font-bold text-[#182830] mb-1">
                        Total Delivery Cost (₱)
                    </label>
                    <input type="number" step="0.01" min="0" name="cost_estimate" placeholder="e.g. 1500.00" class="field text-xs font-mono font-bold" value="0.00">
                </div>
                <div>
                    <label class="block font-mono text-xs font-bold text-[#182830] mb-1">
                        Delivery Notes / Invoice #
                    </label>
                    <input type="text" name="notes" placeholder="e.g. Direct supplier weekly delivery" class="field text-xs">
                </div>
            </div>

            <!-- Auto-Expense Checkbox -->
            <div class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB]/50 p-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="log_expense" value="1" checked class="h-4 w-4 rounded border-2 border-[#182830] text-[#CB1B03] focus:ring-0">
                    <span class="font-mono text-xs font-bold text-[#182830]">
                        Auto-record as expense in Store Expenses ledger (Category: Supplies)
                    </span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t-2 border-[#182830]/15 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeAutoRestockModal()" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl border-3 border-[#182830] bg-[#CB1B03] px-5 py-2.5 font-recoleta text-sm font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer flex items-center gap-2">
                    <span>Confirm &amp; Auto-Log Supplies ➔</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: REGISTER / EDIT SUPPLIER                                           -->
<!-- ========================================================================= -->
<div id="supplier-modal" class="fixed inset-0 z-[500] hidden bg-[#182830]/80 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" style="z-index: 500;">
    <div class="relative w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[8px_8px_0px_#182830] my-8">
        <button type="button" onclick="closeSupplierModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <div class="flex items-center gap-2 mb-2">
            <span class="rounded-md border border-[#182830] bg-[#25799B] px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-white shadow-[1px_1px_0px_#182830]">
                Vendor Directory
            </span>
        </div>

        <h3 id="supplier-modal-title" class="font-recoleta text-2xl font-black text-[#182830]">
            Register Direct Supplier
        </h3>
        <p class="text-xs text-slate-600 mt-1 font-sans">
            Add chemical distributors or detergent providers to enable 1-click automated restocking.
        </p>

        <form method="POST" id="supplier-modal-form" action="{{ route('inventory.suppliers.store') }}" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="_method" id="supplier-modal-method" value="POST">

            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Company / Supplier Name *</label>
                <input type="text" name="name" id="supplier-name" required placeholder="e.g. CleanWash Supplies Inc." class="field text-xs font-bold">
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="supplier-contact" placeholder="e.g. Maria Santos" class="field text-xs">
                </div>
                <div>
                    <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Phone Number</label>
                    <input type="text" name="phone" id="supplier-phone" placeholder="e.g. 0917-123-4567" class="field text-xs font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Email Address</label>
                    <input type="email" name="email" id="supplier-email" placeholder="sales@distributor.ph" class="field text-xs">
                </div>
                <div>
                    <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Payment Terms</label>
                    <input type="text" name="payment_terms" id="supplier-terms" placeholder="e.g. Cash on Delivery, Net 15" class="field text-xs">
                </div>
            </div>

            <div>
                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Warehouse / Office Address</label>
                <input type="text" name="address" id="supplier-address" placeholder="e.g. Warehouse 4, Quezon City" class="field text-xs">
            </div>

            <div>
                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Notes / SKUs Supplied</label>
                <input type="text" name="notes" id="supplier-notes" placeholder="e.g. Primary distributor for Surf, Ariel & Downy packs" class="field text-xs">
            </div>

            <div id="supplier-active-wrap" class="hidden">
                <label class="flex items-center gap-2 cursor-pointer pt-1 font-mono text-xs font-bold text-[#182830]">
                    <input type="checkbox" name="is_active" value="1" id="supplier-active" class="h-4 w-4 rounded border-2 border-[#182830] text-[#25799B] focus:ring-0">
                    <span>Active Supplier Partner</span>
                </label>
            </div>

            <div class="pt-3 border-t-2 border-[#182830]/15 flex items-center justify-end gap-2">
                <button type="button" onclick="closeSupplierModal()" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="supplier-modal-submit-btn" class="rounded-xl border-3 border-[#182830] bg-[#25799B] px-5 py-2.5 font-recoleta text-xs font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#1E6482] transition cursor-pointer">
                    Save Supplier ➔
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: PURCHASE ORDER & SUPPLIER INVOICE VOUCHER                          -->
<!-- ========================================================================= -->
<div id="invoice-voucher-modal" class="fixed inset-0 z-[500] hidden bg-[#182830]/80 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" style="z-index: 500;">
    <div class="relative w-full max-w-xl rounded-3xl border-4 border-[#182830] bg-[#FFFDF8] p-6 shadow-[8px_8px_0px_#182830] my-8 text-[#182830]">
        <button type="button" onclick="closeInvoiceVoucherModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <!-- Header -->
        <div class="border-b-2 border-[#182830]/15 pb-3 mb-4">
            <div class="flex items-center gap-2">
                <span class="badge border-[#182830] bg-[#25799B] text-white font-mono text-[10px] font-bold uppercase">
                    Delivery Invoice Voucher
                </span>
                <span id="inv-voucher-date" class="font-mono text-xs text-slate-500 font-bold"></span>
            </div>
            <h3 id="inv-voucher-title" class="font-recoleta text-2xl font-black text-[#182830] mt-1">
                Purchase Order &amp; Delivery Invoice
            </h3>
            <p class="font-mono text-xs text-[#25799B]">
                Official supplier delivery intake record and payment audit voucher
            </p>
        </div>

        <!-- Supplier & Invoice Details Split -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <!-- Supplier Card -->
            <div class="rounded-2xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-3.5 space-y-1.5 font-mono text-xs">
                <span class="block text-[10px] uppercase font-bold text-[#25799B]">Direct Supplier Profile</span>
                <h4 id="inv-voucher-supplier-name" class="font-recoleta text-base font-black text-[#182830]"></h4>
                <div class="space-y-0.5 text-slate-700 text-[11px]">
                    <div id="inv-voucher-contact-wrap">Rep: <strong id="inv-voucher-contact"></strong></div>
                    <div id="inv-voucher-phone-wrap">Tel: <strong id="inv-voucher-phone"></strong></div>
                    <div id="inv-voucher-email-wrap">Email: <span id="inv-voucher-email" class="underline"></span></div>
                    <div id="inv-voucher-terms-wrap">Terms: <strong id="inv-voucher-terms" class="text-[#25799B]"></strong></div>
                    <div id="inv-voucher-address-wrap" class="text-slate-500 text-[10px] pt-1">Address: <span id="inv-voucher-address"></span></div>
                </div>

                <!-- Direct Contact Action Buttons inside Voucher -->
                <div class="pt-2 flex items-center gap-2 border-t border-[#182830]/15 mt-2">
                    <a id="inv-voucher-btn-email" href="#" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border-2 border-[#182830] bg-white py-1.5 text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span>Email Supplier</span>
                    </a>
                </div>
            </div>

            <!-- Invoice Metadata & Accounting Voucher -->
            <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-3.5 space-y-1.5 font-mono text-xs shadow-[2px_2px_0px_#182830]">
                <span class="block text-[10px] uppercase font-bold text-[#CB1B03]">Invoice Accounting &amp; Ledger</span>
                <div class="space-y-1 text-slate-700 text-[11px] pt-1">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Invoice Status:</span>
                        <strong class="text-emerald-700 uppercase">Received &amp; Stocked</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Recorded By:</span>
                        <span id="inv-voucher-recorded-by" class="font-bold"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Expense Tracking:</span>
                        <span id="inv-voucher-expense" class="font-bold text-emerald-800"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-100">
                        <span class="text-[#182830] font-black uppercase">Invoice Total:</span>
                        <strong id="inv-voucher-total" class="font-recoleta text-lg font-black text-[#CB1B03]"></strong>
                    </div>
                </div>
                <div class="pt-2 text-[10px] text-slate-500 italic border-t border-slate-100" id="inv-voucher-notes"></div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="rounded-2xl border-2 border-[#182830] bg-white overflow-hidden mb-4">
            <div class="bg-[#25799B] text-white px-3 py-1.5 font-mono text-xs font-bold flex justify-between">
                <span>Delivered Supplies &amp; Dosage</span>
                <span>Replenishment Details</span>
            </div>
            <div class="p-3">
                <table class="w-full text-left font-mono text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 text-[10px] uppercase">
                            <th class="pb-1">Supply Item</th>
                            <th class="pb-1 text-center">Unit</th>
                            <th class="pb-1 text-right">Quantity Received</th>
                            <th class="pb-1 text-right">New Stock Balance</th>
                        </tr>
                    </thead>
                    <tbody id="inv-voucher-items-body" class="divide-y divide-slate-100">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-3 border-t-2 border-[#182830]/15">
            <button type="button" onclick="window.print()" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 cursor-pointer flex items-center gap-1.5">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Print Invoice Voucher</span>
            </button>
            <button type="button" onclick="closeInvoiceVoucherModal()" class="rounded-xl border-3 border-[#182830] bg-[#182830] px-5 py-2 font-recoleta text-xs font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#25799B] transition cursor-pointer">
                Close Voucher
            </button>
        </div>
    </div>
</div>

<script>
    function switchInventoryTab(tab) {
        document.querySelectorAll('.inv-tab-btn').forEach(btn => {
            btn.classList.remove('border-2', 'border-[#182830]', 'bg-[#F7E6CB]', 'text-[#182830]', 'shadow-[2px_2px_0px_#182830]');
            btn.classList.add('border-2', 'border-transparent', 'bg-white/60', 'text-[#25799B]');
        });

        const activeBtn = document.getElementById('inv-tab-btn-' + tab);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'bg-white/60', 'text-[#25799B]');
            activeBtn.classList.add('border-2', 'border-[#182830]', 'bg-[#F7E6CB]', 'text-[#182830]', 'shadow-[2px_2px_0px_#182830]');
        }

        document.querySelectorAll('.inv-view-pane').forEach(pane => pane.classList.add('hidden'));
        const activePane = document.getElementById('inv-view-' + tab);
        if (activePane) activePane.classList.remove('hidden');

        if (history.pushState) {
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tab);
            history.pushState(null, '', newUrl.toString());
        }
    }

    function openInventoryActionModal(title, severity, message, actionTitle, stepsJson, actionType, itemId, itemName, unit, suggestedQty) {
        document.getElementById('inv-modal-title').textContent = title;
        document.getElementById('inv-modal-badge').textContent = severity.toUpperCase();
        document.getElementById('inv-modal-message').textContent = message;

        const stepsContainer = document.getElementById('inv-modal-steps');
        stepsContainer.innerHTML = '';
        try {
            const steps = JSON.parse(stepsJson);
            steps.forEach(step => {
                const li = document.createElement('li');
                li.className = 'py-0.5 leading-snug';
                li.textContent = step;
                stepsContainer.appendChild(li);
            });
        } catch(e) {
            const li = document.createElement('li');
            li.textContent = 'Follow standard protocol outlined above.';
            stepsContainer.appendChild(li);
        }

        const reorderBox = document.getElementById('inv-modal-reorder-container');
        const recipeBox = document.getElementById('inv-modal-recipe-container');

        if (actionType === 'reorder' && itemId) {
            reorderBox.classList.remove('hidden');
            recipeBox.classList.add('hidden');

            const form = document.getElementById('inv-modal-form');
            form.action = '/inventory/' + itemId + '/movements';
            document.getElementById('inv-modal-qty').value = suggestedQty || '5';
            document.getElementById('inv-modal-unit').textContent = unit || '';
        } else {
            reorderBox.classList.add('hidden');
            recipeBox.classList.remove('hidden');
        }

        document.getElementById('inv-action-modal').classList.remove('hidden');
    }

    function closeInventoryActionModal() {
        document.getElementById('inv-action-modal').classList.add('hidden');
    }

    function openAutoRestockModal() {
        document.getElementById('auto-restock-modal').classList.remove('hidden');
    }

    function closeAutoRestockModal() {
        document.getElementById('auto-restock-modal').classList.add('hidden');
    }

    function openSupplierModal() {
        document.getElementById('supplier-modal-title').textContent = 'Register Direct Supplier';
        document.getElementById('supplier-modal-submit-btn').textContent = 'Save Supplier ➔';
        const form = document.getElementById('supplier-modal-form');
        form.action = "{{ route('inventory.suppliers.store') }}";
        document.getElementById('supplier-modal-method').value = 'POST';
        form.reset();
        document.getElementById('supplier-active-wrap').classList.add('hidden');
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    function closeSupplierModal() {
        document.getElementById('supplier-modal').classList.add('hidden');
    }

    function editSupplier(supplier) {
        document.getElementById('supplier-modal-title').textContent = 'Edit Supplier: ' + supplier.name;
        document.getElementById('supplier-modal-submit-btn').textContent = 'Update Supplier ➔';
        const form = document.getElementById('supplier-modal-form');
        form.action = '/inventory/suppliers/' + supplier.id;
        document.getElementById('supplier-modal-method').value = 'PUT';

        document.getElementById('supplier-name').value = supplier.name || '';
        document.getElementById('supplier-contact').value = supplier.contact_person || '';
        document.getElementById('supplier-phone').value = supplier.phone || '';
        document.getElementById('supplier-email').value = supplier.email || '';
        document.getElementById('supplier-terms').value = supplier.payment_terms || '';
        document.getElementById('supplier-address').value = supplier.address || '';
        document.getElementById('supplier-notes').value = supplier.notes || '';
        
        const activeWrap = document.getElementById('supplier-active-wrap');
        activeWrap.classList.remove('hidden');
        document.getElementById('supplier-active').checked = !!supplier.is_active;

        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    function openInvoiceVoucherModal(po) {
        document.getElementById('inv-voucher-date').textContent = po.date_formatted || '';
        document.getElementById('inv-voucher-title').textContent = 'PO #' + po.order_number + ' Delivery Invoice';
        document.getElementById('inv-voucher-supplier-name').textContent = po.supplier_name || 'Direct Distributor';
        document.getElementById('inv-voucher-contact').textContent = po.contact_person || 'Representative';
        document.getElementById('inv-voucher-phone').textContent = po.phone || 'None on record';
        document.getElementById('inv-voucher-email').textContent = po.email || 'None on record';
        document.getElementById('inv-voucher-terms').textContent = po.payment_terms || 'Standard Wholesale';
        document.getElementById('inv-voucher-address').textContent = po.address || 'Central Supply Depot';

        const emailBtn = document.getElementById('inv-voucher-btn-email');
        if (po.email) {
            emailBtn.href = 'mailto:' + po.email + '?subject=' + encodeURIComponent('Inquiry regarding PO #' + po.order_number);
            emailBtn.classList.remove('hidden');
        } else {
            emailBtn.classList.add('hidden');
        }

        document.getElementById('inv-voucher-recorded-by').textContent = po.recorded_by || 'Admin';
        document.getElementById('inv-voucher-expense').textContent = po.expense_id ? 'Voucher #' + po.expense_id + ' (Recorded)' : 'Not synced to expenses';
        document.getElementById('inv-voucher-total').textContent = '₱' + parseFloat(po.total_cost || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('inv-voucher-notes').textContent = po.notes ? '“' + po.notes + '”' : 'No extra delivery notes attached.';

        const tbody = document.getElementById('inv-voucher-items-body');
        tbody.innerHTML = '';
        if (Array.isArray(po.items) && po.items.length > 0) {
            po.items.forEach(itm => {
                const tr = document.createElement('tr');
                tr.className = 'py-1';
                tr.innerHTML = `
                    <td class="py-1 font-bold text-[#182830]">${itm.item_name || 'Supply Item'}</td>
                    <td class="py-1 text-center">${itm.unit || ''}</td>
                    <td class="py-1 text-right font-black text-emerald-700">+${itm.quantity_added || 0}</td>
                    <td class="py-1 text-right font-bold text-slate-700">${itm.new_balance !== undefined ? itm.new_balance : '—'}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="4" class="py-2 text-center text-slate-500">General batch delivery. Detailed line items not itemized.</td>`;
            tbody.appendChild(tr);
        }

        document.getElementById('invoice-voucher-modal').classList.remove('hidden');
    }

    function closeInvoiceVoucherModal() {
        document.getElementById('invoice-voucher-modal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || 'advisor';
        if (tabParam) {
            switchInventoryTab(tabParam);
        }
    });
</script>
@endsection
