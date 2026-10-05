@extends('layouts_app')

@section('content')
@php
    $stageLabels = [
        'received' => 'Received',
        'pending' => 'Received',
        'washing' => 'Washing',
        'in-progress' => 'Washing',
        'drying' => 'Drying',
        'ironing' => 'Ironing',
        'folding' => 'Folding',
        'ready_for_pickup' => 'Ready for Pickup',
        'ready' => 'Ready for Pickup',
        'claimed' => 'Claimed',
        'delivered' => 'Claimed',
        'cancelled' => 'Cancelled'
    ];
    $dailyCountMax = max(1, $dailyTrend->max('count') ?? 0);
    $dailyRevMax = max(1, $dailyTrend->max('revenue') ?? 0);
    $dailyKgMax = max(1, $dailyTrend->max('kg') ?? 0);

    $chartPoints = $dailyTrend->values()->map(fn ($day, $index) => [
        'x' => 50 + ($index * 95),
        'y_count' => 140 - ($day['count'] / $dailyCountMax * 95),
        'y_rev' => 140 - ($day['revenue'] / $dailyRevMax * 95),
        'y_kg' => 140 - ($day['kg'] / $dailyKgMax * 95),
        ...$day,
    ]);
    $stageFilterAliases = ['pending' => 'received', 'in-progress' => 'washing', 'ready' => 'ready_for_pickup', 'delivered' => 'claimed'];
    $dateQuery = array_filter(['from' => $from, 'to' => $to]);

    $activeWashersCount = collect($washers)->where('is_active', true)->count();
    $activeDryersCount = collect($dryers)->where('is_active', true)->count();
@endphp

<!-- CSS Animation for Live Washing and Drying Units -->
<style>
    @keyframes drum-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes tumble-heat {
        0%, 100% { opacity: 0.6; transform: scale(0.98); }
        50% { opacity: 1; transform: scale(1.02); }
    }
    .animate-spin-fast {
        animation: drum-spin 2s linear infinite;
        transform-origin: center;
    }
    .animate-spin-slow {
        animation: drum-spin 4s linear infinite;
        transform-origin: center;
    }
    .animate-tumble-heat {
        animation: tumble-heat 2s ease-in-out infinite;
    }
</style>

<!-- Admin Command Top Header -->
<div class="mb-6 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <span class="relative flex h-2.5 w-2.5">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#CB1B03] opacity-75"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#CB1B03]"></span>
            </span>
            <span id="live-header-clock">Executive Command Center · Trowa Headquarters · {{ now()->format('l, F j, Y') }}</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
            Admin Dashboard
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Real-time financial telemetry, capacity utilization, unit economics, and operational pipeline.
        </p>
    </div>

    <!-- Quick Actions Toolbar & Date Filter Form -->
    <div class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-2.5">
        <!-- Interactive Executive Actions -->
        <div class="flex items-center gap-1.5">
            <button type="button" onclick="openQuickExpenseModal()" class="retro-btn-primary h-8 px-3 text-xs flex items-center gap-1.5 cursor-pointer" title="Log expense voucher on the fly">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>+ Log Expense</span>
            </button>
            <button type="button" onclick="openPromoModal()" class="retro-btn-secondary h-8 px-2.5 text-xs flex items-center gap-1.5 cursor-pointer" title="Off-peak capacity promo generator">
                <svg class="h-3.5 w-3.5 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                <span>Promo Booster</span>
            </button>
            <button type="button" onclick="triggerTelemetryPing()" class="retro-btn-secondary h-8 px-2.5 text-xs flex items-center gap-1.5 cursor-pointer" title="Live refresh ping">
                <svg class="h-3.5 w-3.5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                <span id="ping-text">Live Ping</span>
            </button>
        </div>

        <!-- Quick Preset Pills -->
        <div class="flex items-center gap-1 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-1 shadow-[2px_2px_0px_#182830] font-mono text-xs">
            <a href="{{ route('dashboard', ['preset' => 'today']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ ($preset ?? '') === 'today' ? 'bg-[#25799B] text-white' : 'text-[#182830] hover:bg-[#F7E6CB]' }}">Today</a>
            <a href="{{ route('dashboard', ['preset' => '7d']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ ($preset ?? '') === '7d' ? 'bg-[#25799B] text-white' : 'text-[#182830] hover:bg-[#F7E6CB]' }}">7D</a>
            <a href="{{ route('dashboard', ['preset' => '30d']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ ($preset ?? '') === '30d' ? 'bg-[#25799B] text-white' : 'text-[#182830] hover:bg-[#F7E6CB]' }}">30D</a>
            <a href="{{ route('dashboard', ['preset' => 'month']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ ($preset ?? '') === 'month' ? 'bg-[#25799B] text-white' : 'text-[#182830] hover:bg-[#F7E6CB]' }}">This Month</a>
            <a href="{{ route('dashboard', ['preset' => 'all']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ empty($from) && empty($to) && ($preset ?? '') === 'all' ? 'bg-[#25799B] text-white' : 'text-[#182830] hover:bg-[#F7E6CB]' }}">All</a>
        </div>

        <!-- Custom Date Range Form -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-1.5 shadow-[2px_2px_0px_#182830]">
            <div>
                <label for="admin-date-from" class="block font-mono text-[9px] font-bold uppercase text-[#25799B] px-1">From</label>
                <input id="admin-date-from" type="date" name="from" value="{{ $from }}" class="field h-7 px-2 py-0.5 text-xs font-mono font-bold w-28">
            </div>
            <div>
                <label for="admin-date-to" class="block font-mono text-[9px] font-bold uppercase text-[#25799B] px-1">To</label>
                <input id="admin-date-to" type="date" name="to" value="{{ $to }}" class="field h-7 px-2 py-0.5 text-xs font-mono font-bold w-28">
            </div>
            <button type="submit" class="retro-btn-primary h-7 px-2.5 text-xs">Apply</button>
            @if($from || $to)
                <a href="{{ route('dashboard') }}" class="retro-btn-secondary h-7 px-2 text-xs">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="mb-4 flex flex-wrap items-center justify-between font-mono text-[11px] text-[#25799B] font-bold gap-2">
    <span>
        {{ $from || $to ? "Telemetry window: {$from} to {$to}" : 'Metrics reflect all-time totals; daily timeline displays the 7 most recent days.' }}
    </span>
    <span class="rounded bg-[#A2C5D8] px-2.5 py-1 text-[#182830] border border-[#182830]/20 flex items-center gap-1.5">
        <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
        Fleet: 16 Industrial Units ({{ $activeWashersCount }}/8 Washers · {{ $activeDryersCount }}/8 Dryers Active)
    </span>
</div>

<!-- 8 High-Impact Retro KPI Panels -->
<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @php
        $kpis = [
            ['revenue', 'Completed Revenue', '₱'.number_format((float) $revenue, 2), 'Claimed/completed orders', route('reports', $dateQuery), 'bg-[#A2C5D8]'],
            ['expenses', 'Recorded Expenses', '₱'.number_format((float) $expensesTotal, 2), 'All recorded expenses', route('expenses.index', $dateQuery), 'bg-[#F7E6CB]'],
            ['profit', 'Net Operating Profit', '₱'.number_format((float) $netProfit, 2), "Margin: {$profitMargin}%", route('reports', $dateQuery), 'bg-emerald-100 text-emerald-800'],
            ['balance', 'Balances Due', '₱'.number_format((float) $balancesDue, 2), 'Outstanding receivables', route('payments.index', $dateQuery), 'bg-red-100 text-[#CB1B03]'],
            ['unclaimed', 'Unclaimed Orders', $unclaimedOrders, 'Ready on customer racks', route('orders.index', ['status'=>'ready_for_pickup', ...$dateQuery]), 'bg-[#A2C5D8]'],
            ['orders', 'Total Orders Processed', $orders->count(), "Avg Basket: ₱".number_format($aov, 2), route('orders.index', $dateQuery), 'bg-[#F7E6CB]'],
            ['utilization', 'Fleet Load Volume', $totalLoads . ' Loads', "{$totalKg} kg total laundry", route('reports', $dateQuery), 'bg-[#25799B] text-white'],
            ['stock', 'Low-Stock Items', $lowStockItems->count(), 'At or below reorder threshold', route('inventory.index'), 'bg-[#CB1B03] text-white'],
        ];
    @endphp

    @foreach($kpis as [$iconKey, $label, $value, $hint, $url, $colorClass])
        <a href="{{ $url }}" class="retro-panel-interactive flex items-start gap-3.5 p-4 transition group cursor-pointer">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] {{ $colorClass }} shadow-[2px_2px_0px_#182830]">
                @if($iconKey === 'revenue')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                @elseif($iconKey === 'expenses')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                @elseif($iconKey === 'profit')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="9" x2="19" y2="9"/><line x1="5" y1="15" x2="19" y2="15"/></svg>
                @elseif($iconKey === 'balance')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @elseif($iconKey === 'unclaimed')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                @elseif($iconKey === 'orders')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                @elseif($iconKey === 'utilization')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="2" width="18" height="20" rx="3"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 1 3 3"/></svg>
                @elseif($iconKey === 'stock')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">{{ $label }}</span>
                <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">{{ $value }}</span>
                <span class="block font-mono text-[11px] text-slate-500 group-hover:text-[#CB1B03] transition">{{ $hint }} · Audit ➔</span>
            </div>
        </a>
    @endforeach
</div>

<!-- Low Stock Banner Warning -->
@if($lowStockItems->isNotEmpty())
    <div class="mb-6 rounded-2xl border-2 border-[#182830] bg-[#CB1B03] p-4 text-[#FFFDF8] shadow-[4px_4px_0px_#182830]">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#CB1B03] shadow-[2px_2px_0px_#182830]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </span>
                <div>
                    <h2 class="font-recoleta text-base font-bold text-[#F7E6CB]">Low-stock Alert Triggered</h2>
                    <p class="text-xs font-mono text-white/90">
                        @foreach($lowStockItems as $item)
                            <strong>{{ $item->name }}</strong> ({{ $item->quantity_on_hand }} {{ $item->unit }} left)@if(!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
            </div>
            <a href="{{ route('inventory.index') }}" class="retro-btn-secondary bg-[#FFFDF8] text-xs">
                Open Stock Advisor ➔
            </a>
        </div>
    </div>
@endif

<!-- ============================================================ -->
<!-- GRAPH SECTION 1: INTERACTIVE DAILY VELOCITY (Volume/Rev/Kg)  -->
<!-- ============================================================ -->
<div class="mb-6 retro-panel p-5">
    <div class="mb-3 flex flex-col md:flex-row md:items-center md:justify-between border-b-2 border-[#182830]/15 pb-3 gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg border border-[#182830] bg-[#25799B] text-white">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </span>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Daily Order Volume &amp; Financial Velocity</h2>
            </div>
            <p class="font-mono text-xs text-[#25799B] mt-0.5">
                {{ \Carbon\Carbon::parse($dailyTrend->first()['date'])->format('M j') }} – {{ \Carbon\Carbon::parse($dailyTrend->last()['date'])->format('M j, Y') }} (Interactive multi-series telemetry)
            </p>
        </div>

        <!-- Interactive Layer View Switcher -->
        <div class="flex items-center gap-1.5 font-mono text-xs bg-[#FFFDF8] p-1 rounded-xl border-2 border-[#182830] shadow-[2px_2px_0px_#182830]">
            <button type="button" 
                    id="chart-mode-volume" 
                    onclick="setChartMetric('count')" 
                    class="px-2.5 py-1 rounded-lg font-bold transition bg-[#25799B] text-white cursor-pointer">
                📦 Volume (Orders)
            </button>
            <button type="button" 
                    id="chart-mode-rev" 
                    onclick="setChartMetric('rev')" 
                    class="px-2.5 py-1 rounded-lg font-bold transition text-[#182830] hover:bg-[#F7E6CB] cursor-pointer">
                💰 Revenue (₱)
            </button>
            <button type="button" 
                    id="chart-mode-kg" 
                    onclick="setChartMetric('kg')" 
                    class="px-2.5 py-1 rounded-lg font-bold transition text-[#182830] hover:bg-[#F7E6CB] cursor-pointer">
                ⚖️ Weight (kg)
            </button>
        </div>
    </div>

    <!-- Interactive Dynamic SVG Chart Canvas -->
    <div class="relative overflow-x-auto pb-2 pt-2">
        <svg id="velocity-svg" viewBox="0 0 700 180" role="img" aria-labelledby="daily-orders-title daily-orders-description" class="h-52 w-full min-w-[580px]">
            <title id="daily-orders-title">Daily order volume line graph</title>
            <desc id="daily-orders-description">Clickable points show the number of orders created on each date.</desc>
            
            <!-- Gridlines -->
            <line x1="40" y1="140" x2="660" y2="140" stroke="#182830" stroke-width="1.5" stroke-dasharray="3 3"/>
            <line x1="40" y1="90" x2="660" y2="90" stroke="#A2C5D8" stroke-width="1" stroke-dasharray="2 2"/>
            <line x1="40" y1="40" x2="660" y2="40" stroke="#A2C5D8" stroke-width="1" stroke-dasharray="2 2"/>
            
            <!-- Order Volume Polyline (Required by test assertions) -->
            <polyline id="chart-polyline-volume" points="{{ $chartPoints->map(fn ($point) => $point['x'].','.$point['y_count'])->implode(' ') }}" fill="none" stroke="#25799B" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
            
            <!-- Revenue Polyline (Toggled by JS) -->
            <polyline id="chart-polyline-rev" points="{{ $chartPoints->map(fn ($point) => $point['x'].','.$point['y_rev'])->implode(' ') }}" fill="none" stroke="#CB1B03" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" class="hidden"/>

            <!-- Weight Polyline (Toggled by JS) -->
            <polyline id="chart-polyline-kg" points="{{ $chartPoints->map(fn ($point) => $point['x'].','.$point['y_kg'])->implode(' ') }}" fill="none" stroke="#1E6482" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" class="hidden"/>

            <!-- Interactive Nodes -->
            @foreach($chartPoints as $point)
                <!-- Background column highlight on hover -->
                <rect x="{{ $point['x'] - 20 }}" y="20" width="40" height="130" fill="transparent" class="hover:fill-[#F7E6CB]/40 transition cursor-pointer" onclick="window.location='{{ route('orders.index', ['date' => $point['date']]) }}'">
                    <title>{{ $point['label'] }}: {{ $point['count'] }} orders · ₱{{ number_format($point['revenue'], 2) }} · {{ $point['kg'] }} kg</title>
                </rect>

                <!-- Circle Node (Targeted by test assertions) -->
                <a href="{{ route('orders.index', ['date' => $point['date']]) }}" aria-label="View {{ $point['count'] }} orders from {{ \Carbon\Carbon::parse($point['date'])->format('F j, Y') }}" class="cursor-pointer chart-node-link">
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y_count'] }}" r="7" fill="#CB1B03" stroke="#FFFDF8" stroke-width="2.5" class="chart-circle transition-all hover:scale-125" data-y-count="{{ $point['y_count'] }}" data-y-rev="{{ $point['y_rev'] }}" data-y-kg="{{ $point['y_kg'] }}">
                        <title>{{ $point['count'] }} orders · ₱{{ number_format($point['revenue'], 2) }} revenue · {{ \Carbon\Carbon::parse($point['date'])->format('M j, Y') }}</title>
                    </circle>
                </a>

                <!-- X Axis Label -->
                <text x="{{ $point['x'] }}" y="165" text-anchor="middle" fill="#182830" font-family="'Space Grotesk', monospace" font-size="11" font-weight="700">{{ $point['label'] }}</text>
                
                <!-- Dynamic Value Label above node -->
                <text x="{{ $point['x'] }}" y="{{ $point['y_count'] - 12 }}" text-anchor="middle" fill="#182830" font-family="'Space Grotesk', monospace" font-size="12" font-weight="800" class="chart-val-label" data-count="{{ $point['count'] }}" data-rev="₱{{ number_format($point['revenue'], 0) }}" data-kg="{{ $point['kg'] }}kg" data-y-count="{{ $point['y_count'] - 12 }}" data-y-rev="{{ $point['y_rev'] - 12 }}" data-y-kg="{{ $point['y_kg'] - 12 }}">
                    {{ $point['count'] }}
                </text>
            @endforeach
        </svg>
    </div>
</div>

<!-- ============================================================ -->
<!-- GRAPH SECTION 2: HOURLY RUSH HEATMAP & SERVICES DONUT        -->
<!-- ============================================================ -->
<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <!-- Peak Rush Hours Intake Graph (Histogram Bar Chart) -->
    <section class="retro-panel p-5 flex flex-col justify-between">
        <div>
            <div class="mb-3 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
                <div>
                    <h2 class="font-recoleta text-lg font-bold text-[#182830]">Peak Rush Hours &amp; Drop-off Heatmap</h2>
                    <p class="font-mono text-xs text-[#25799B]">Operating window customer influx distribution</p>
                </div>
                <span class="badge border-[#182830] bg-amber-100 text-amber-900 font-mono text-xs font-bold flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-amber-600 animate-pulse"></span>
                    Peak Rush Tracking
                </span>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-3 items-end h-40 pt-4 pb-2 border-b border-[#182830]/15">
                @foreach($hourlyDistribution as $slotKey => $slot)
                    @php($isPeak = $slot['percent'] > 0 && $slot['bar_height'] >= 90)
                    <div class="flex flex-col items-center h-full justify-end group cursor-pointer" onclick="alert('Time slot: {{ $slot['label'] }}\nOrders: {{ $slot['count'] }}\nTotal weight: {{ $slot['kg'] }} kg')">
                        <span class="font-mono text-[11px] font-black {{ $isPeak ? 'text-[#CB1B03]' : 'text-[#182830]' }} mb-1">
                            {{ $slot['count'] }}
                        </span>
                        <div class="w-full max-w-[50px] rounded-t-lg border-2 border-[#182830] {{ $isPeak ? 'bg-[#CB1B03]' : 'bg-[#25799B]' }} transition-all group-hover:opacity-90 shadow-[2px_0px_0px_#182830]" style="height: {{ max(10, $slot['bar_height']) }}%">
                        </div>
                        <span class="mt-2 font-mono text-[10px] font-bold text-slate-700 text-center leading-tight">
                            {{ $slot['short'] }}
                        </span>
                        <span class="font-mono text-[9px] text-[#25799B] font-bold">
                            {{ $slot['percent'] }}%
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4 rounded-xl border border-[#182830]/15 bg-[#F7E6CB]/40 p-3 font-mono text-xs flex items-center justify-between">
            <span class="text-slate-700">Recommended Shift Staffing:</span>
            <strong class="text-[#CB1B03]">Double Counter Attendant at Afternoon Rush (2pm–5pm)</strong>
        </div>
    </section>

    <!-- Services Volume & Revenue Donut Chart -->
    <section class="retro-panel p-5 flex flex-col justify-between">
        <div>
            <div class="mb-3 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
                <div>
                    <h2 class="font-recoleta text-lg font-bold text-[#182830]">Services Volume &amp; Demand Breakdown</h2>
                    <p class="font-mono text-xs text-[#25799B]">Product bundle popularity &amp; service distribution</p>
                </div>
                <a href="{{ route('services.index') }}" class="font-mono text-xs font-bold text-[#25799B] hover:underline">
                    Edit Catalog ➔
                </a>
            </div>

            <!-- Donut Visual + Legend Grid -->
            <div class="grid sm:grid-cols-[160px_1fr] items-center gap-4 mt-2">
                <!-- SVG Donut Chart -->
                <div class="flex justify-center">
                    <svg viewBox="0 0 160 160" class="h-36 w-36">
                        <circle cx="80" cy="80" r="55" fill="none" stroke="#F7E6CB" stroke-width="22"/>
                        @php($circumference = 345.57)
                        @php($runningOffset = 0)
                        @foreach($serviceSegments as $seg)
                            @php($dash = ($seg['percent'] / 100) * $circumference)
                            <circle cx="80" cy="80" r="55" fill="none" stroke="{{ $seg['color'] }}" stroke-width="22"
                                    stroke-dasharray="{{ $dash }} {{ $circumference - $dash }}"
                                    stroke-dashoffset="{{ -$runningOffset }}"
                                    class="transition-all hover:stroke-width-[26] cursor-pointer"
                                    transform="rotate(-90 80 80)">
                                <title>{{ $seg['name'] }}: {{ $seg['count'] }} orders ({{ $seg['percent'] }}%)</title>
                            </circle>
                            @php($runningOffset += $dash)
                        @endforeach
                        <text x="80" y="76" text-anchor="middle" font-family="'Recoleta', serif" font-size="20" font-weight="900" fill="#182830">{{ $orders->count() }}</text>
                        <text x="80" y="93" text-anchor="middle" font-family="'Space Grotesk', monospace" font-size="9" font-weight="700" fill="#25799B" text-transform="uppercase">Orders</text>
                    </svg>
                </div>

                <!-- Legend Breakdown -->
                <div class="space-y-2">
                    @forelse($serviceSegments as $seg)
                        <div class="flex items-center justify-between rounded-lg border border-[#182830]/15 bg-[#FFFDF8] px-3 py-1.5 shadow-[1px_1px_0px_#182830]">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full border border-[#182830]" style="background-color: {{ $seg['color'] }}"></span>
                                <span class="font-recoleta text-xs font-bold text-[#182830]">{{ $seg['name'] }}</span>
                            </div>
                            <div class="font-mono text-xs font-bold text-[#25799B]">
                                {{ $seg['count'] }} <span class="text-[10px] text-slate-500">({{ $seg['percent'] }}%)</span>
                            </div>
                        </div>
                    @empty
                        <p class="font-mono text-xs text-slate-500 py-3">No orders recorded in this timeframe.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-[#182830]/15 flex items-center justify-between text-xs font-mono">
            <span class="text-slate-600">Dominant Service:</span>
            <strong class="text-[#25799B] font-bold">{{ $serviceSegments[0]['name'] ?? 'Wash & Fold' }} ({{ $serviceSegments[0]['percent'] ?? 0 }}%)</strong>
        </div>
    </section>
</div>

<!-- ============================================================ -->
<!-- 16-UNIT INDUSTRIAL MACHINE BAY SHOWROOM (8 Washers, 8 Dryers)-->
<!-- ============================================================ -->
<div class="mb-6 retro-panel p-5">
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-3 gap-2">
        <div class="flex items-center gap-3">
            <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <circle cx="12" cy="13" r="4.5"/>
                    <path d="M10 13c.5-.8 1.5-.8 2 0s1.5.8 2 0"/>
                </svg>
            </div>
            <div>
                <h2 class="font-recoleta text-xl font-bold text-[#182830]">Plant Capacity &amp; Fleet Load</h2>
                <p class="font-mono text-xs text-[#25799B]">Real-time operational cycle status of all 16 plant units (8 Washers · 8 Dryers)</p>
            </div>
        </div>

        <!-- Filter Machines by Type -->
        <div class="flex items-center gap-1.5 font-mono text-xs bg-[#FFFDF8] p-1 rounded-xl border-2 border-[#182830] shadow-[2px_2px_0px_#182830]">
            <button type="button" onclick="filterFleet('all')" id="fleet-tab-all" class="px-2.5 py-1 rounded-lg font-bold transition bg-[#25799B] text-white cursor-pointer">All (16)</button>
            <button type="button" onclick="filterFleet('washer')" id="fleet-tab-washer" class="px-2.5 py-1 rounded-lg font-bold transition text-[#182830] hover:bg-[#F7E6CB] cursor-pointer">Washers (8)</button>
            <button type="button" onclick="filterFleet('dryer')" id="fleet-tab-dryer" class="px-2.5 py-1 rounded-lg font-bold transition text-[#182830] hover:bg-[#F7E6CB] cursor-pointer">Dryers (8)</button>
            <a href="{{ route('schedule.index') }}" class="px-2.5 py-1 rounded-lg font-bold text-[#CB1B03] hover:underline" title="Open full showroom floor">Floor Map ➔</a>
        </div>
    </div>

    <!-- Capacity Utilization Progress Bars -->
    <div class="mb-4 grid gap-3 sm:grid-cols-2 rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3.5 shadow-[1px_1px_0px_#182830]">
        <div>
            <div class="flex justify-between font-mono text-xs font-bold mb-1">
                <span class="text-[#182830]">Washer Bay (8 Units · 8kg capacity each)</span>
                <span class="text-[#25799B]">{{ $totalLoads }} Wash Cycles Logged</span>
            </div>
            <div class="h-2.5 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                <div class="h-full rounded-full bg-[#25799B]" style="width: {{ max(6, $utilizationRate) }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex justify-between font-mono text-xs font-bold mb-1">
                <span class="text-[#182830]">Dryer Bay (8 Units · High-temp tumble)</span>
                <span class="text-[#CB1B03]">{{ round($totalLoads * 0.85) }} Estimated Cycles</span>
            </div>
            <div class="h-2.5 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                <div class="h-full rounded-full bg-[#CB1B03]" style="width: {{ max(5, round($utilizationRate * 0.85)) }}%"></div>
            </div>
        </div>
    </div>

    <!-- 16 Machine Units Grid -->
    <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-8" id="machine-fleet-grid">
        <!-- 8 Washers -->
        @foreach($washers as $unit)
            <div class="machine-unit-card machine-washer flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $unit['is_active'] ? 'bg-sky-50 border-[#25799B]' : 'bg-[#FFFDF8]' }} p-3 shadow-[2px_2px_0px_#182830] transition hover:-translate-y-0.5">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-mono text-[10px] font-black uppercase text-[#25799B]">{{ $unit['id'] }}</span>
                    @if($unit['is_active'])
                        <span class="inline-flex h-2 w-2 rounded-full bg-[#CB1B03] animate-pulse"></span>
                    @else
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    @endif
                </div>

                <!-- Animated Retro Washing Machine SVG Icon -->
                <div class="my-2 flex justify-center">
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] shadow-[1px_1px_0px_#182830]">
                        <svg class="h-8 w-8 text-[#25799B] {{ $unit['is_active'] ? 'animate-spin-fast' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="8"/>
                            <path d="M12 6v6l4 2"/>
                            <circle cx="12" cy="12" r="2.5" fill="{{ $unit['is_active'] ? '#CB1B03' : '#25799B' }}"/>
                        </svg>
                    </div>
                </div>

                <div class="text-center font-mono">
                    <span class="block text-xs font-bold text-[#182830]">{{ $unit['name'] }}</span>
                    @if($unit['is_active'])
                        <span class="mt-0.5 block rounded bg-[#CB1B03] px-1 py-0.5 text-[9px] font-black uppercase text-white animate-pulse">
                            {{ $unit['remaining_mins'] }}m left
                        </span>
                        <span class="mt-0.5 block truncate text-[9px] text-[#25799B] font-bold" title="{{ $unit['customer_name'] }}">
                            {{ $unit['order_number'] }}
                        </span>
                    @else
                        <span class="mt-0.5 block rounded bg-emerald-100 px-1 py-0.5 text-[9px] font-bold text-emerald-800">
                            Available
                        </span>
                        <span class="mt-0.5 block text-[9px] text-slate-400">8kg Max</span>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- 8 Dryers -->
        @foreach($dryers as $unit)
            <div class="machine-unit-card machine-dryer flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $unit['is_active'] ? 'bg-amber-50 border-amber-800' : 'bg-[#FFFDF8]' }} p-3 shadow-[2px_2px_0px_#182830] transition hover:-translate-y-0.5">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-mono text-[10px] font-black uppercase text-[#CB1B03]">{{ $unit['id'] }}</span>
                    @if($unit['is_active'])
                        <span class="inline-flex h-2 w-2 rounded-full bg-amber-600 animate-pulse"></span>
                    @else
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    @endif
                </div>

                <!-- Animated Retro Dryer SVG Icon -->
                <div class="my-2 flex justify-center">
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] shadow-[1px_1px_0px_#182830]">
                        <svg class="h-8 w-8 text-[#CB1B03] {{ $unit['is_active'] ? 'animate-spin-slow animate-tumble-heat' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="3"/>
                            <circle cx="12" cy="12" r="7"/>
                            <path d="M10 12c.8-1 1.6-1 2.4 0s1.6 1 2.4 0"/>
                        </svg>
                    </div>
                </div>

                <div class="text-center font-mono">
                    <span class="block text-xs font-bold text-[#182830]">{{ $unit['name'] }}</span>
                    @if($unit['is_active'])
                        <span class="mt-0.5 block rounded bg-amber-600 px-1 py-0.5 text-[9px] font-black uppercase text-white animate-pulse">
                            {{ $unit['remaining_mins'] }}m tumble
                        </span>
                        <span class="mt-0.5 block truncate text-[9px] text-[#CB1B03] font-bold" title="{{ $unit['customer_name'] }}">
                            {{ $unit['order_number'] }}
                        </span>
                    @else
                        <span class="mt-0.5 block rounded bg-emerald-100 px-1 py-0.5 text-[9px] font-bold text-emerald-800">
                            Available
                        </span>
                        <span class="mt-0.5 block text-[9px] text-slate-400">High Heat</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ============================================================ -->
<!-- FLOOR STAGE PIPELINE & RECENT INTAKES                         -->
<!-- ============================================================ -->
<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <!-- Orders by Stage Pipeline -->
    <section class="retro-panel p-5">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-3">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Orders by Stage</h2>
                <p class="font-mono text-xs text-[#25799B]">Click any stage bar to audit filtered orders</p>
            </div>
            <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830]">
                {{ $orders->count() }} Total Tickets
            </span>
        </div>

        <div class="space-y-3">
            @foreach($stageLabels as $status => $label)
                @if($orderStages->has($status))
                    @php($stageCount = $orderStages->get($status))
                    @php($percent = $orders->count() ? ($stageCount / $orders->count()) * 100 : 0)
                    <a href="{{ route('orders.index', ['status' => $stageFilterAliases[$status] ?? $status, ...$dateQuery]) }}" 
                       class="group flex items-center gap-3 rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-2.5 transition hover:bg-[#F7E6CB]/50 shadow-[1px_1px_0px_#182830]">
                        <span class="w-32 shrink-0 font-mono text-xs font-bold text-[#182830] group-hover:text-[#CB1B03] transition">
                            {{ $label }}
                        </span>
                        <div class="h-3 flex-1 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                            <div class="h-full rounded-full bg-[#25799B] group-hover:bg-[#CB1B03] transition-all" style="width: {{ max(4, $percent) }}%"></div>
                        </div>
                        <span class="font-mono text-xs font-black text-[#182830] w-10 text-right">
                            {{ $stageCount }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    <!-- Recent Intakes & Transactions -->
    <section class="retro-panel p-5">
        <div class="mb-3 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Recent Intakes</h2>
                <p class="font-mono text-xs text-[#25799B]">Most recent customer order drop-offs</p>
            </div>
            <a href="{{ route('orders.index') }}" class="font-mono text-xs font-bold text-[#25799B] hover:underline">
                View All Orders ➔
            </a>
        </div>
        <div class="space-y-2.5">
            @forelse($orders->take(6) as $order)
                <a href="{{ route('orders.show', $order) }}" 
                   class="flex items-center justify-between rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 transition hover:bg-[#F7E6CB]/40 shadow-[1px_1px_0px_#182830]">
                    <div>
                        <span class="font-mono text-xs font-bold text-[#CB1B03]">{{ $order->order_number ?? 'TL-'.$order->id }}</span>
                        <p class="font-recoleta text-xs font-bold text-[#182830]">{{ $order->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <strong class="font-mono text-xs text-[#182830]">₱{{ number_format((float) $order->total_price, 2) }}</strong>
                        <span class="block font-mono text-[10px] uppercase font-bold text-[#25799B]">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                </a>
            @empty
                <p class="font-mono text-xs text-slate-500 py-3">No recent orders found.</p>
            @endforelse
        </div>
    </section>
</div>

<!-- Payments by Method & Team Overview -->
<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <!-- Payments by Method -->
    <section class="retro-panel p-5">
        <div class="mb-3 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830]">Payments by Method</h2>
            <a href="{{ route('payments.index', $dateQuery) }}" class="font-mono text-xs font-bold text-[#25799B] hover:underline">
                Cashier Ledger ➔
            </a>
        </div>
        <div class="space-y-2.5">
            @forelse($paymentsByMethod as $method => $amount)
                <a href="{{ route('payments.index', ['method' => $method, ...$dateQuery]) }}" 
                   class="flex items-center justify-between rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 transition hover:bg-[#F7E6CB]/40 shadow-[1px_1px_0px_#182830]">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#A2C5D8] text-[#182830]">
                            @if($method === 'gcash')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                            @else
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                            @endif
                        </span>
                        <span class="font-mono text-xs font-bold capitalize text-[#182830]">{{ $method }}</span>
                    </div>
                    <strong class="font-mono text-sm text-[#182830]">₱{{ number_format($amount, 2) }}</strong>
                </a>
            @empty
                <p class="font-mono text-xs text-slate-500 py-3">No payments recorded in this timeframe.</p>
            @endforelse
        </div>
    </section>

    <!-- Team Overview Dock -->
    <section class="retro-panel p-5">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Team Staffing Roster</h2>
                <p class="font-mono text-xs text-[#25799B]">Active personnel on register &amp; floor shift duties</p>
            </div>
            <a href="{{ route('reports') }}" class="retro-btn-secondary text-xs">
                Staff Reports ➔
            </a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($team as $member)
                <div class="flex items-center gap-3 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 shadow-[2px_2px_0px_#182830]">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] {{ $member->role === 'admin' ? 'bg-[#CB1B03]' : 'bg-[#25799B]' }} text-white font-recoleta text-base font-bold shadow-[1px_1px_0px_#182830]">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-sans text-xs font-bold text-[#182830]">{{ $member->name }}</p>
                        <span class="badge border-[#182830] {{ $member->role === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-[#A2C5D8] text-[#182830]' }} mt-0.5">
                            {{ ucfirst($member->role) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

<!-- ============================================================ -->
<!-- INTERACTIVE QUICK EXPENSE MODAL                              -->
<!-- ============================================================ -->
<div id="quick-expense-modal" class="fixed inset-0 z-50 hidden bg-[#182830]/75 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-md rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[6px_6px_0px_#182830]">
        <button type="button" onclick="closeQuickExpenseModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <div class="flex items-center gap-2 mb-2">
            <span class="badge border-[#182830] bg-[#CB1B03] text-white text-[10px] font-mono font-bold uppercase">Cash Outflow</span>
            <span class="font-mono text-[10px] text-[#25799B] font-bold">Fast Ledger Entry</span>
        </div>

        <h3 class="font-recoleta text-xl font-extrabold text-[#182830]">Record Shop Expense</h3>
        <p class="text-xs text-slate-600 mt-1 font-sans">Instantly record operational supplies, utilities, or maintenance costs:</p>

        <form method="POST" action="{{ route('expenses.store') }}" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Expense Category *</label>
                <select name="category" class="field text-xs" required>
                    <option value="supplies">Detergents &amp; Supplies</option>
                    <option value="utilities">Electricity &amp; Water Utilities</option>
                    <option value="maintenance">Equipment Maintenance</option>
                    <option value="rent">Shop Lease / Rent</option>
                    <option value="payroll">Staff Payroll</option>
                    <option value="other">Other Operating Overhead</option>
                </select>
            </div>

            <div>
                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Description / Item *</label>
                <input type="text" name="description" class="field text-xs" placeholder="e.g. 5 Gallons Liquid Detergent" required>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Amount (₱) *</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="field text-xs font-mono font-bold" placeholder="500.00" required>
                </div>
                <div>
                    <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Date *</label>
                    <input type="date" name="expense_date" class="field text-xs font-mono" value="{{ now()->toDateString() }}" required>
                </div>
            </div>

            <div>
                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Receipt Ref # (Optional)</label>
                <input type="text" name="reference_number" class="field text-xs font-mono" placeholder="OR-98124">
            </div>

            <div class="mt-4 pt-3 border-t border-[#182830]/15 flex items-center justify-end gap-2">
                <button type="button" onclick="closeQuickExpenseModal()" class="retro-btn-secondary text-xs px-3 py-1.5">Cancel</button>
                <button type="submit" class="retro-btn-primary text-xs px-4 py-1.5 cursor-pointer">
                    Save Expense ➔
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- INTERACTIVE CAPACITY FLASH PROMO MODAL                       -->
<!-- ============================================================ -->
<div id="promo-booster-modal" class="fixed inset-0 z-50 hidden bg-[#182830]/75 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[6px_6px_0px_#182830]">
        <button type="button" onclick="closePromoModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>

        <div class="flex items-center gap-2 mb-2">
            <span class="badge border-[#182830] bg-[#25799B] text-white text-[10px] font-mono font-bold uppercase">Growth Catalyst</span>
            <span class="font-mono text-[10px] text-[#CB1B03] font-bold">Plant Utilization Optimizer</span>
        </div>

        <h3 class="font-recoleta text-xl font-extrabold text-[#182830]">Off-Peak Capacity Flash Promos</h3>
        <p class="text-xs text-slate-600 mt-1 font-sans">Ready-to-broadcast marketing campaigns to fill idle washer &amp; dryer machine slots:</p>

        <div class="mt-4 space-y-3">
            <div class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-3.5">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-recoleta text-sm font-bold text-[#182830]">Early-Bird Morning Wash (-15%)</span>
                    <span class="font-mono text-[10px] font-bold text-[#25799B]">8:00 AM – 11:00 AM</span>
                </div>
                <p class="text-xs text-slate-700 font-sans">"Drop off your dirty laundry before 11 AM today and receive an automatic 15% discount on Wash &amp; Fold! Ready for pickup by evening."</p>
                <div class="mt-2 text-right">
                    <button type="button" onclick="copyPromoText(this, 'Early-Bird Morning Wash: Drop off before 11 AM today for 15% OFF Wash & Fold at Trowa Laundry!')" class="retro-btn-secondary text-[10px] px-2.5 py-1">Copy SMS/Promo Text</button>
                </div>
            </div>

            <div class="rounded-xl border-2 border-[#182830] bg-emerald-50/70 p-3.5 border-emerald-700/30">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-recoleta text-sm font-bold text-emerald-900">Bedding &amp; Comforter Twin Deal</span>
                    <span class="font-mono text-[10px] font-bold text-emerald-700">Heavy Bulky Wash</span>
                </div>
                <p class="text-xs text-slate-700 font-sans">"Wash 2 King or Queen Comforters for only ₱350 this Tuesday &amp; Wednesday. Deep sanitizing wash + ultra-tumble dry."</p>
                <div class="mt-2 text-right">
                    <button type="button" onclick="copyPromoText(this, 'Comforter Twin Deal: 2 Large Comforters for only ₱350 this week at Trowa Laundry! Deep steam sanitize.')" class="retro-btn-secondary text-[10px] px-2.5 py-1">Copy SMS/Promo Text</button>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#182830]/15 flex items-center justify-end">
            <button type="button" onclick="closePromoModal()" class="retro-btn-primary text-xs px-4 py-1.5">Done</button>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- INTERACTIVE SCRIPTS                                          -->
<!-- ============================================================ -->
<script>
    // Live Clock updater
    function updateClock() {
        const now = new Date();
        const clockEl = document.getElementById('live-header-clock');
        if (clockEl) {
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            clockEl.textContent = `Executive Command Center · Trowa Headquarters · ${timeStr}`;
        }
    }
    setInterval(updateClock, 1000);

    // Dynamic Chart Metric Layer Switcher
    function setChartMetric(metric) {
        const polyCount = document.getElementById('chart-polyline-volume');
        const polyRev = document.getElementById('chart-polyline-rev');
        const polyKg = document.getElementById('chart-polyline-kg');

        const btnCount = document.getElementById('chart-mode-volume');
        const btnRev = document.getElementById('chart-mode-rev');
        const btnKg = document.getElementById('chart-mode-kg');

        // Reset button styles
        [btnCount, btnRev, btnKg].forEach(b => {
            b.classList.remove('bg-[#25799B]', 'bg-[#CB1B03]', 'bg-[#1E6482]', 'text-white');
            b.classList.add('text-[#182830]', 'hover:bg-[#F7E6CB]');
        });

        // Toggle polylines and node positions
        if (metric === 'rev') {
            btnRev.classList.add('bg-[#CB1B03]', 'text-white');
            btnRev.classList.remove('text-[#182830]');
            polyCount.classList.add('hidden');
            polyRev.classList.remove('hidden');
            polyKg.classList.add('hidden');

            document.querySelectorAll('.chart-circle').forEach(circle => {
                circle.setAttribute('cy', circle.getAttribute('data-y-rev'));
                circle.setAttribute('fill', '#CB1B03');
            });
            document.querySelectorAll('.chart-val-label').forEach(label => {
                label.setAttribute('y', label.getAttribute('data-y-rev'));
                label.textContent = label.getAttribute('data-rev');
            });
        } else if (metric === 'kg') {
            btnKg.classList.add('bg-[#1E6482]', 'text-white');
            btnKg.classList.remove('text-[#182830]');
            polyCount.classList.add('hidden');
            polyRev.classList.add('hidden');
            polyKg.classList.remove('hidden');

            document.querySelectorAll('.chart-circle').forEach(circle => {
                circle.setAttribute('cy', circle.getAttribute('data-y-kg'));
                circle.setAttribute('fill', '#1E6482');
            });
            document.querySelectorAll('.chart-val-label').forEach(label => {
                label.setAttribute('y', label.getAttribute('data-y-kg'));
                label.textContent = label.getAttribute('data-kg');
            });
        } else {
            btnCount.classList.add('bg-[#25799B]', 'text-white');
            btnCount.classList.remove('text-[#182830]');
            polyCount.classList.remove('hidden');
            polyRev.classList.add('hidden');
            polyKg.classList.add('hidden');

            document.querySelectorAll('.chart-circle').forEach(circle => {
                circle.setAttribute('cy', circle.getAttribute('data-y-count'));
                circle.setAttribute('fill', '#CB1B03');
            });
            document.querySelectorAll('.chart-val-label').forEach(label => {
                label.setAttribute('y', label.getAttribute('data-y-count'));
                label.textContent = label.getAttribute('data-count');
            });
        }
    }

    // Machine Fleet Filter Tabs
    function filterFleet(type) {
        ['all', 'washer', 'dryer'].forEach(t => {
            const btn = document.getElementById('fleet-tab-' + t);
            if (btn) {
                btn.classList.remove('bg-[#25799B]', 'text-white');
                btn.classList.add('text-[#182830]');
            }
        });
        const activeBtn = document.getElementById('fleet-tab-' + type);
        if (activeBtn) {
            activeBtn.classList.add('bg-[#25799B]', 'text-white');
            activeBtn.classList.remove('text-[#182830]');
        }

        document.querySelectorAll('.machine-unit-card').forEach(card => {
            if (type === 'all') {
                card.style.display = '';
            } else if (type === 'washer') {
                card.style.display = card.classList.contains('machine-washer') ? '' : 'none';
            } else if (type === 'dryer') {
                card.style.display = card.classList.contains('machine-dryer') ? '' : 'none';
            }
        });
    }

    // Quick Expense Modal
    function openQuickExpenseModal() {
        document.getElementById('quick-expense-modal').classList.remove('hidden');
    }
    function closeQuickExpenseModal() {
        document.getElementById('quick-expense-modal').classList.add('hidden');
    }

    // Promo Modal
    function openPromoModal() {
        document.getElementById('promo-booster-modal').classList.remove('hidden');
    }
    function closePromoModal() {
        document.getElementById('promo-booster-modal').classList.add('hidden');
    }

    function copyPromoText(btn, text) {
        navigator.clipboard.writeText(text);
        const originalText = btn.textContent;
        btn.textContent = 'Copied! ✓';
        btn.classList.add('bg-emerald-600', 'text-white');
        setTimeout(() => {
            btn.textContent = originalText;
            btn.classList.remove('bg-emerald-600', 'text-white');
        }, 2000);
    }

    // Telemetry Live Ping Trigger
    function triggerTelemetryPing() {
        const pingBtn = document.getElementById('ping-text');
        if (typeof SoundFx !== 'undefined') {
            SoundFx.success();
        }
        if (pingBtn) {
            pingBtn.textContent = 'Synchronized! ✓';
            setTimeout(() => { pingBtn.textContent = 'Live Ping'; }, 2000);
        }
    }
</script>
@endsection
