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
        <span class="badge border-[#182830] bg-[#FFFDF8] px-3 py-1 font-mono text-xs font-bold text-[#182830]">
            {{ $totalItems }} Tracked SKUs
        </span>
        <span class="badge border-[#182830] bg-amber-400 text-[#182830] px-3 py-1 font-mono text-xs font-black shadow-[1px_1px_0px_#182830] {{ $almostOutCount > 0 ? 'animate-pulse' : '' }}">
            🎖️ {{ $almostOutCount }} Almost Out Medals
        </span>
        <span class="badge border-[#182830] bg-[#CB1B03] text-white px-3 py-1 font-mono text-xs font-black shadow-[1px_1px_0px_#182830]">
            🏅 {{ $depletedCount }} Depleted Medals
        </span>
        <span class="badge border-[#182830] bg-emerald-100 text-emerald-800 px-3 py-1 font-mono text-xs font-bold">
            🥇 {{ $healthyCount }} Healthy Stock Medals
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
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Medal Warning Status</span>
            <span class="block font-recoleta text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-[#CB1B03]' : 'text-emerald-700' }} leading-tight my-0.5">{{ $lowStockCount }} Alert Medals</span>
            <span class="block font-mono text-[11px] text-slate-500">{{ $almostOutCount }} Almost Out 🎖️ · {{ $depletedCount }} Depleted 🏅</span>
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
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#A2C5D8]">Smart Advisor</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#FFFDF8] leading-tight my-0.5">Active</span>
            <span class="block font-mono text-[11px] text-[#A2C5D8]">{{ count($smartSuggestions) }} Alerts Live</span>
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
                                🏷️ {{ $item->unit === 'sachet' ? 'Per Sachet' : $item->unit }}
                            </span>
                        </div>

                        <!-- Warning Medal -->
                        <div class="mb-3">
                            @if($isDepleted)
                                <div class="inline-flex items-center gap-1.5 rounded-full border-2 border-[#182830] bg-[#CB1B03] text-white px-2.5 py-0.5 font-mono text-[11px] font-black shadow-[1px_1px_0px_#182830]">
                                    <span>🏅 Depleted Stock Medal</span>
                                </div>
                                <span class="block font-mono text-[10px] text-red-700 font-bold mt-1">Critical Empty! 0 {{ $item->unit }}s left</span>
                            @elseif($isAlmostOut)
                                <div class="inline-flex items-center gap-1.5 rounded-full border-2 border-[#182830] bg-amber-400 text-[#182830] px-2.5 py-0.5 font-mono text-[11px] font-black shadow-[1px_1px_0px_#182830] animate-pulse">
                                    <span>🎖️ Almost Out Medal</span>
                                </div>
                                <span class="block font-mono text-[10px] text-amber-900 font-bold mt-1">Warning: Below safety threshold!</span>
                            @else
                                <div class="inline-flex items-center gap-1.5 rounded-full border border-emerald-600 bg-emerald-100 text-emerald-800 px-2.5 py-0.5 font-mono text-[11px] font-bold">
                                    <span>🥇 Stock Healthy Medal</span>
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
                            🏷️ {{ $item->unit === 'sachet' ? 'Per Sachet' : $item->unit }}
                        </span>

                        @if($isDepleted)
                            <span class="badge border-2 border-[#182830] bg-[#CB1B03] text-white text-[10px] font-mono font-black">
                                🏅 Depleted Stock Medal
                            </span>
                        @elseif($isAlmostOut)
                            <span class="badge border-2 border-[#182830] bg-amber-400 text-[#182830] animate-pulse text-[10px] font-mono font-black">
                                🎖️ Almost Out Medal · Low Stock Alert
                            </span>
                        @else
                            <span class="badge border border-emerald-600 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold">
                                🥇 Stock Healthy Medal
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
                                    <th class="py-2.5">Date</th>
                                    <th>Movement</th>
                                    <th>Change</th>
                                    <th>Order Ref</th>
                                    <th>Recorded by</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#182830]/10">
                                @forelse($item->movements->take(8) as $movement)
                                    <tr>
                                        <td class="py-2 text-slate-600">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge border-[#182830]/30 {{ $movement->quantity_change > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-[#CB1B03]' }}">
                                                {{ str_replace('_', ' ', ucfirst($movement->movement_type)) }}
                                            </span>
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
                        💡 All laundry supplies (e.g. Surf, Downy, Ariel) are automatically tagged and perceived per sachet.
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
<!-- INTERACTIVE TAKE ACTION INVENTORY MODAL    -->
<!-- ========================================== -->
<div id="inv-action-modal" class="fixed inset-0 z-50 hidden bg-[#182830]/75 backdrop-blur-xs flex items-center justify-center p-4">
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

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || 'advisor';
        if (tabParam) {
            switchInventoryTab(tabParam);
        }
    });
</script>
@endsection
