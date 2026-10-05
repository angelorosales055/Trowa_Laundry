@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span>Executive Business Intelligence &amp; Commercial Planning</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Reports &amp; Business Plan
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Strategic forecasts, settled billing invoices, unit economics, and operational action plans.
        </p>
    </div>

    <div class="flex items-center gap-2.5">
        <a href="{{ route('reports.print', array_filter(['from' => $from, 'to' => $to])) }}" target="_blank" class="retro-btn-primary text-xs flex items-center gap-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            <span>Print Report / PDF</span>
        </a>
    </div>
</div>

<!-- Compact Date Filter Toolbar -->
<form method="GET" action="{{ route('reports') }}" class="retro-panel mb-6 flex flex-wrap items-center justify-between gap-3 p-3.5">
    <div class="flex flex-wrap items-center gap-2.5">
        <span class="font-mono text-xs font-bold uppercase text-[#25799B] flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Audit Window:
        </span>
        <div class="flex items-center gap-1.5">
            <label for="rep-filter-from" class="sr-only">From date</label>
            <input id="rep-filter-from" type="date" name="from" value="{{ $from }}" class="field h-8 px-2 py-1 text-xs font-mono">
            <span class="text-slate-400 font-mono text-xs">to</span>
            <label for="rep-filter-to" class="sr-only">To date</label>
            <input id="rep-filter-to" type="date" name="to" value="{{ $to }}" class="field h-8 px-2 py-1 text-xs font-mono">
            <input type="hidden" name="tab" id="filter-current-tab" value="{{ $activeTab ?? 'plan' }}">
            <button type="submit" class="retro-btn-primary h-8 px-3 text-xs">Apply</button>
            @if($from || $to)
                <a href="{{ route('reports', ['tab' => $activeTab ?? 'plan']) }}" class="retro-btn-secondary h-8 px-2.5 text-xs">Reset</a>
            @endif
        </div>
    </div>
    <div class="font-mono text-[11px] text-[#25799B]">
        {{ $from || $to ? "Filtering: {$from} to {$to} ({$projections['days_sample']} days)" : "All time sample ({$projections['days_sample']} days)" }}
    </div>
</form>

<!-- Interactive Module Tabs (Toggles instead of endless scrolling) -->
<div class="mb-6 flex flex-wrap items-center gap-2 border-b-2 border-[#182830]/20 pb-2">
    <button type="button" 
            onclick="switchReportTab('plan')" 
            id="tab-btn-plan"
            class="report-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer {{ ($activeTab ?? 'plan') === 'plan' ? 'border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span>Strategic Plan &amp; Recommendations</span>
        <span class="rounded-full bg-[#CB1B03] px-2 py-0.2 font-mono text-[10px] font-bold text-white">{{ count($strategicDirectives) }}</span>
    </button>

    <button type="button" 
            onclick="switchReportTab('billing')" 
            id="tab-btn-billing"
            class="report-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer {{ ($activeTab ?? 'plan') === 'billing' ? 'border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span>Billing &amp; Invoices Ledger</span>
        <span class="rounded-full bg-[#25799B] px-2 py-0.2 font-mono text-[10px] font-bold text-white">{{ $billingTxnCount }}</span>
    </button>

    <button type="button" 
            onclick="switchReportTab('financials')" 
            id="tab-btn-financials"
            class="report-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer {{ ($activeTab ?? 'plan') === 'financials' ? 'border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        <span>Financials &amp; Unit Economics</span>
    </button>

    <button type="button" 
            onclick="switchReportTab('performance')" 
            id="tab-btn-performance"
            class="report-tab-btn flex items-center gap-2 rounded-xl px-4 py-2.5 font-recoleta text-sm font-bold transition cursor-pointer {{ ($activeTab ?? 'plan') === 'performance' ? 'border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-2 border-transparent bg-white/60 text-[#25799B] hover:bg-white hover:text-[#182830]' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 21H3V3"/><path d="M7 14l4-4 4 4 6-6"/></svg>
        <span>Services &amp; Top Customers</span>
    </button>
</div>

<!-- ========================================== -->
<!-- TAB 1: STRATEGIC BUSINESS PLAN & ACTIONS   -->
<!-- ========================================== -->
<div id="report-view-plan" class="report-view-pane {{ ($activeTab ?? 'plan') === 'plan' ? '' : 'hidden' }} space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-2.5 gap-2">
        <div>
            <h2 class="font-recoleta text-xl font-bold text-[#182830]">Strategic Business Plan &amp; Forward Projections</h2>
            <p class="font-mono text-xs text-[#25799B]">Forecasted run-rates, unit economics &amp; plant expansion headroom</p>
        </div>
        <span class="badge border-[#182830] bg-amber-100 text-amber-900 font-mono text-xs font-bold">
            Executive Planning Model
        </span>
    </div>

    <!-- 3 Projection Pillars -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="retro-panel p-4.5 bg-[#F7E6CB]/40">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Next 30 Days Forecast</span>
            <div class="mt-2">
                <p class="font-recoleta text-2xl font-extrabold text-[#182830]">₱{{ number_format($projections['projected_30d_rev'], 2) }}</p>
                <p class="font-mono text-xs text-emerald-700 font-bold mt-0.5">Est. Net Profit: ₱{{ number_format($projections['projected_30d_profit'], 2) }}</p>
                <p class="font-mono text-[10px] text-slate-600 mt-1">Daily revenue rate: ₱{{ number_format($projections['daily_revenue'], 2) }}/day.</p>
            </div>
        </div>

        <div class="retro-panel p-4.5 bg-[#A2C5D8]/20">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Quarterly (90-Day) Outlook</span>
            <div class="mt-2">
                <p class="font-recoleta text-2xl font-extrabold text-[#182830]">₱{{ number_format($projections['projected_90d_rev'], 2) }}</p>
                <p class="font-mono text-xs text-emerald-700 font-bold mt-0.5">Est. Net Profit: ₱{{ number_format($projections['projected_90d_profit'], 2) }}</p>
                <p class="font-mono text-[10px] text-slate-600 mt-1">Assumes current customer visit retention.</p>
            </div>
        </div>

        <div class="retro-panel p-4.5 bg-emerald-50/70 border-emerald-700/40">
            <span class="font-mono text-[10px] font-bold uppercase text-emerald-800">Monthly Break-Even Target</span>
            <div class="mt-2">
                <p class="font-recoleta text-2xl font-extrabold text-emerald-900">{{ $capacity['break_even_loads_month'] }} Loads</p>
                <p class="font-mono text-xs text-slate-700 font-bold mt-0.5">{{ $capacity['break_even_daily_loads'] }} loads/day to break even</p>
                <p class="font-mono text-[10px] text-slate-600 mt-1">Required volume to absorb all fixed store expenses.</p>
            </div>
        </div>
    </div>

    <!-- Unit Economics Breakdown -->
    <div class="retro-panel p-5">
        <h3 class="font-recoleta text-base font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
            Unit Economics Breakdown
        </h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 font-mono text-xs">
            <div class="rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                <span class="text-slate-600 block text-[11px]">Average Revenue per Kilogram:</span>
                <strong class="text-[#182830] text-sm block mt-1">₱{{ number_format($revenuePerKg, 2) }} / kg</strong>
            </div>
            <div class="rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                <span class="text-slate-600 block text-[11px]">Operating Cost per Kilogram:</span>
                <strong class="text-[#CB1B03] text-sm block mt-1">₱{{ number_format($costPerKg, 2) }} / kg</strong>
            </div>
            <div class="rounded-xl border border-[#182830]/15 bg-emerald-50/80 p-3 shadow-[1px_1px_0px_#182830]">
                <span class="text-emerald-800 block text-[11px]">Net Profit Margin per Kilogram:</span>
                <strong class="text-emerald-700 font-bold text-sm block mt-1">₱{{ number_format($profitPerKg, 2) }} / kg</strong>
            </div>
        </div>
    </div>

    <!-- Fleet Capacity Expansion Blueprint -->
    <div class="retro-panel p-5">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-2.5 gap-2">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Fleet Capacity &amp; Expansion Blueprint</h2>
                <p class="font-mono text-xs text-[#25799B]">Real-time washer cycle capacity and maximum monthly revenue potential</p>
            </div>
            <span class="badge border-[#182830] bg-[#FFFDF8] font-mono text-xs font-bold text-[#182830]">
                8 Washers × 8 Cycles = 64 Max Daily
            </span>
        </div>

        <div class="grid gap-6 md:grid-cols-2 items-center">
            <div>
                <div class="flex justify-between mb-1.5 font-mono text-xs font-bold">
                    <span class="text-slate-700">Fleet Utilization Rate:</span>
                    <span class="text-[#182830]">{{ $capacity['utilization_percent'] }}%</span>
                </div>
                <div class="h-3.5 overflow-hidden rounded-full border-2 border-[#182830] bg-[#F7E6CB] p-0.5 shadow-[1px_1px_0px_#182830]">
                    <div class="h-full rounded-full bg-[#25799B] transition-all duration-500" style="width: {{ max(4, $capacity['utilization_percent']) }}%"></div>
                </div>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed font-sans">
                    Current operational pace processes <strong class="text-[#182830]">{{ $capacity['current_daily_loads'] }} loads/day</strong>. You have headroom for <strong class="text-emerald-700 font-bold">+{{ $capacity['headroom_daily_loads'] }} more loads/day</strong> without investing in any new washing machines or dryers.
                </p>
            </div>

            <div class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-4 font-mono text-xs space-y-2.5 shadow-[2px_2px_0px_#182830]">
                <div class="flex justify-between border-b border-[#182830]/10 pb-1.5">
                    <span class="text-slate-600">Daily Fleet Max Loads:</span>
                    <strong class="text-[#182830]">{{ $capacity['max_daily_loads'] }} loads/day</strong>
                </div>
                <div class="flex justify-between border-b border-[#182830]/10 pb-1.5">
                    <span class="text-slate-600">Unused Growth Headroom:</span>
                    <strong class="text-emerald-700 font-bold">+{{ $capacity['headroom_daily_loads'] }} loads/day</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Max Monthly Revenue Ceiling:</span>
                    <strong class="text-[#CB1B03] font-black text-sm">₱{{ number_format($capacity['max_monthly_rev_potential'], 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic Directives & Recommendations with Take Action Buttons -->
    <div class="retro-panel p-5">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-2.5 gap-2">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white shadow-[1px_1px_0px_#182830]">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </span>
                <div>
                    <h2 class="font-recoleta text-lg font-bold text-[#182830]">Actionable Business Directives &amp; Growth Initiatives</h2>
                    <p class="font-mono text-xs text-[#25799B]">Data-driven recommendations to boost profitability and maintain supply safety</p>
                </div>
            </div>
            <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830] font-mono text-xs font-bold">
                {{ count($strategicDirectives) }} Directives Active
            </span>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach($strategicDirectives as $index => $directive)
                <div class="flex flex-col justify-between rounded-xl border-2 border-[#182830] p-4.5 shadow-[3px_3px_0px_#182830] {{ $directive['severity'] === 'critical' ? 'bg-red-50/80 border-[#CB1B03]' : ($directive['severity'] === 'warning' ? 'bg-amber-50/80' : ($directive['severity'] === 'opportunity' ? 'bg-[#F7E6CB]/50' : 'bg-sky-50/70')) }}">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="badge border-[#182830] text-[10px] font-mono font-bold uppercase {{ $directive['severity'] === 'critical' ? 'bg-[#CB1B03] text-white' : ($directive['severity'] === 'warning' ? 'bg-amber-600 text-white' : ($directive['severity'] === 'opportunity' ? 'bg-[#25799B] text-white' : 'bg-emerald-600 text-white')) }}">
                                {{ $directive['badge'] }}
                            </span>
                            <span class="font-mono text-[10px] text-slate-500 font-bold">
                                #{{ $index + 1 }} Priority
                            </span>
                        </div>
                        <h3 class="font-recoleta text-base font-bold text-[#182830]">{{ $directive['title'] }}</h3>
                        <p class="mt-1 text-xs text-slate-700 leading-relaxed font-sans">{{ $directive['summary'] }}</p>
                        
                        <div class="mt-2.5 rounded-lg border border-[#182830]/15 bg-white/70 px-3 py-1.5 font-mono text-[11px] text-[#25799B]">
                            <strong>Projected Impact:</strong> {{ $directive['impact'] }}
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#182830]/15 flex items-center justify-between gap-3">
                        <span class="font-mono text-[11px] text-slate-600">Action Plan Ready</span>
                        <button type="button" 
                                onclick="openActionModal('{{ $directive['id'] }}', '{{ addslashes($directive['title']) }}', '{{ addslashes($directive['badge']) }}', '{{ addslashes($directive['summary']) }}', '{{ addslashes(json_encode($directive['action_steps'])) }}', '{{ addslashes($directive['impact']) }}', '{{ addslashes($directive['cta_label']) }}', '{{ addslashes($directive['cta_url']) }}')"
                                class="retro-btn-primary text-xs px-3.5 py-1.5 flex items-center gap-1.5 cursor-pointer">
                            <span>Take Action</span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 2: COMBINED BILLING & INVOICES LEDGER  -->
<!-- ========================================== -->
<div id="report-view-billing" class="report-view-pane {{ ($activeTab ?? 'plan') === 'billing' ? '' : 'hidden' }} space-y-6">
    <!-- 3 Billing KPI Cards -->
    <div class="grid gap-4 md:grid-cols-3">
        <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#A2C5D8]/20">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Total Collected</p>
                <p class="font-recoleta text-2xl font-extrabold text-[#182830] leading-none my-1">₱{{ number_format($totalCollected, 2) }}</p>
                <p class="font-mono text-[11px] text-slate-500">From claimed customer orders</p>
            </div>
        </div>

        <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#F7E6CB]/30">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Settled Transactions</p>
                <p class="font-recoleta text-2xl font-extrabold text-[#182830] leading-none my-1">{{ $billingTxnCount }}</p>
                <p class="font-mono text-[11px] text-slate-500">Fully paid &amp; released receipts</p>
            </div>
        </div>

        <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#25799B]/10">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#25799B] text-white shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Average Ticket Size</p>
                <p class="font-recoleta text-2xl font-extrabold text-[#182830] leading-none my-1">₱{{ number_format($billingAvgTicket, 2) }}</p>
                <p class="font-mono text-[11px] text-slate-500">Gross revenue per transaction</p>
            </div>
        </div>
    </div>

    <!-- Settled Invoices Table with Client-Side Search and Pagination -->
    <div class="retro-panel overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830] bg-[#A2C5D8]/30 px-5 py-3.5 gap-3">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Settled Commercial Invoices &amp; Receipts Ledger</h2>
                <p class="font-mono text-xs text-[#25799B]">Audit completed order settlements, payment statuses, and issued tickets</p>
            </div>

            <!-- Instant Search Input -->
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" 
                           id="billing-search-input" 
                           placeholder="Filter ticket or customer..." 
                           class="field h-8 w-52 px-2.5 text-xs font-mono" 
                           oninput="filterBillingTable()">
                    <button type="button" 
                            onclick="document.getElementById('billing-search-input').value=''; filterBillingTable();"
                            class="absolute right-2 top-1.5 text-slate-400 hover:text-slate-600 text-xs">×</button>
                </div>
                <button type="button" onclick="window.print()" class="retro-btn-secondary h-8 px-2.5 text-xs flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    <span>Print Ledger</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="billing-table">
                <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/20 font-mono text-xs uppercase tracking-wider text-[#182830]">
                    <tr>
                        <th class="px-5 py-3 font-extrabold">Order / Ticket</th>
                        <th class="px-5 py-3 font-extrabold">Customer</th>
                        <th class="px-5 py-3 font-extrabold">Services Rendered</th>
                        <th class="px-5 py-3 font-extrabold">Settlement Date</th>
                        <th class="px-5 py-3 font-extrabold text-right">Settled Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182830]/10 bg-[#FFFDF8]" id="billing-table-body">
                    @forelse($settledInvoices as $payment)
                        <tr class="billing-row transition hover:bg-[#F7E6CB]/30" data-search="{{ strtolower(($payment->order_number ?? 'TL-'.str_pad($payment->id, 3, '0', STR_PAD_LEFT)).' '.($payment->customer?->name ?? $payment->customer_name).' '.$payment->services) }}">
                            <td class="px-5 py-3 font-mono text-xs font-extrabold text-[#CB1B03]">
                                {{ $payment->order_number ?? 'TL-'.str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-3 font-recoleta text-sm font-bold text-[#182830]">
                                {{ $payment->customer?->name ?? $payment->customer_name }}
                            </td>
                            <td class="px-5 py-3 text-xs text-[#25799B] font-medium">
                                {{ $payment->services }}
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-600">
                                {{ $payment->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono text-xs font-black text-emerald-700">
                                ₱{{ number_format($payment->total_price, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr id="billing-empty-row">
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500 font-mono text-xs">
                                No completed payment receipts found in this audit window.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-[#182830] bg-[#F7E6CB]/50 font-mono text-xs font-black">
                        <td colspan="4" class="px-5 py-3 text-[#182830]">Grand Total Settled Revenue</td>
                        <td class="px-5 py-3 text-right text-[#CB1B03] text-sm">₱{{ number_format($totalCollected, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Interactive Pagination Toolbar (Avoids long scrolling!) -->
        <div class="flex items-center justify-between border-t-2 border-[#182830]/15 bg-[#FFFDF8] px-5 py-3 text-xs font-mono" id="billing-pagination-bar">
            <span class="text-slate-600" id="billing-page-info">
                Showing page 1
            </span>
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="billing-prev-btn" 
                        onclick="changeBillingPage(-1)" 
                        class="retro-btn-secondary text-[11px] px-2.5 py-1 cursor-pointer">
                    ◀ Previous
                </button>
                <button type="button" 
                        id="billing-next-btn" 
                        onclick="changeBillingPage(1)" 
                        class="retro-btn-secondary text-[11px] px-2.5 py-1 cursor-pointer">
                    Next ▶
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 3: FINANCIAL AUDIT & UNIT ECONOMICS    -->
<!-- ========================================== -->
<div id="report-view-financials" class="report-view-pane {{ ($activeTab ?? 'plan') === 'financials' ? '' : 'hidden' }} space-y-6">
    <!-- 4 High Level Financial Summary Cards -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#A2C5D8]/20">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Completed Revenue</p>
                <p class="font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">₱{{ number_format($revenue, 2) }}</p>
                <p class="font-mono text-[11px] text-slate-500">Claimed orders</p>
            </div>
        </div>

        <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#F7E6CB]/40">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Operating Expenses</p>
                <p class="font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">₱{{ number_format($expensesTotal, 2) }}</p>
                <p class="font-mono text-[11px] text-slate-500">Supplies &amp; overhead</p>
            </div>
        </div>

        <div class="retro-panel flex items-start gap-3.5 p-4 bg-emerald-50 text-emerald-800 border-emerald-700/30">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-emerald-100 text-emerald-800 shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="9" x2="19" y2="9"/><line x1="5" y1="15" x2="19" y2="15"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-emerald-800">Net profit</p>
                <p class="font-recoleta text-2xl font-extrabold text-emerald-900 leading-tight my-0.5">₱{{ number_format($netProfit, 2) }}</p>
                <p class="font-mono text-[11px] text-emerald-800">Margin: {{ $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0 }}%</p>
            </div>
        </div>

        <div class="retro-panel flex items-start gap-3.5 p-4 bg-[#25799B] text-white">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-white text-[#25799B] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#A2C5D8]">Total Orders</p>
                <p class="font-recoleta text-2xl font-extrabold text-white leading-tight my-0.5">{{ $orders->count() }}</p>
                <p class="font-mono text-[11px] text-[#A2C5D8]">{{ $from || $to ? 'Orders in selected range' : 'All recorded dates' }}</p>
            </div>
        </div>
    </div>

    <!-- Unit Economics Breakdown -->
    <div class="grid gap-6 md:grid-cols-2">
        <div class="retro-panel p-5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
                Unit Economics Metrics
            </h2>
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1 border-b border-[#182830]/10">
                    <span class="text-slate-600">Revenue per Kilogram:</span>
                    <strong class="text-[#182830]">₱{{ number_format($revenuePerKg, 2) }} / kg</strong>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-[#182830]/10">
                    <span class="text-slate-600">Operating Cost per Kilogram:</span>
                    <strong class="text-[#CB1B03]">₱{{ number_format($costPerKg, 2) }} / kg</strong>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-[#182830]/10">
                    <span class="text-slate-600">Net Profit Margin per Kilogram:</span>
                    <strong class="text-emerald-700 font-bold">₱{{ number_format($profitPerKg, 2) }} / kg</strong>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-[#182830]/10">
                    <span class="text-slate-600">Revenue per Washer Load:</span>
                    <strong class="text-[#182830]">₱{{ number_format($revenuePerLoad, 2) }} / load</strong>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-600">Operating Cost per Washer Load:</span>
                    <strong class="text-[#CB1B03]">₱{{ number_format($costPerLoad, 2) }} / load</strong>
                </div>
            </div>
        </div>

        <div class="retro-panel p-5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
                Settlement Channels &amp; Cash Flow
            </h2>
            <div class="space-y-2.5">
                @forelse($paymentsByMethod as $method => $amount)
                    <div class="flex items-center justify-between rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                        <span class="font-mono text-xs font-bold capitalize text-[#182830]">{{ $method }}</span>
                        <strong class="font-mono text-sm text-[#182830]">₱{{ number_format($amount, 2) }}</strong>
                    </div>
                @empty
                    <p class="font-mono text-xs text-slate-500 py-2">No payment channels recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Expenses by Category -->
    <div class="retro-panel p-5">
        <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
            Expense Allocation by Category
        </h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($expensesByCategory as $cat => $amount)
                @php($pct = $expensesTotal > 0 ? round(($amount / $expensesTotal) * 100, 1) : 0)
                <div class="rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3.5 shadow-[1px_1px_0px_#182830]">
                    <div class="mb-1 flex justify-between font-mono text-xs font-bold">
                        <span class="capitalize text-[#182830]">{{ $cat }}</span>
                        <span class="text-[#CB1B03]">₱{{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                        <div class="h-full rounded-full bg-[#CB1B03]" style="width: {{ max(4, $pct) }}%"></div>
                    </div>
                    <p class="mt-1 font-mono text-[10px] text-slate-500">{{ $pct }}% of all recorded operating overhead</p>
                </div>
            @empty
                <p class="font-mono text-xs text-slate-500 py-2">No categorized expenses in this audit window.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 4: SERVICES & CUSTOMER PERFORMANCE     -->
<!-- ========================================== -->
<div id="report-view-performance" class="report-view-pane {{ ($activeTab ?? 'plan') === 'performance' ? '' : 'hidden' }} space-y-6">
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Services Breakdown -->
        <section class="retro-panel p-5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
                Services Volume Share
            </h2>
            <div class="space-y-3.5">
                @forelse($serviceBreakdown as $service => $count)
                    <div>
                        <div class="mb-1 flex justify-between font-mono text-xs font-bold">
                            <span class="text-[#182830]">{{ $service }}</span>
                            <span class="text-[#25799B]">{{ $count }} orders</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                            <div class="h-full rounded-full bg-[#25799B]" style="width: {{ $orders->count() ? max(8, ($count / $orders->count()) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="font-mono text-xs text-slate-500 py-2">No order services recorded yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Top Customers -->
        <section class="retro-panel p-5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
                Key Accounts by Order Volume
            </h2>
            <div class="space-y-2.5">
                @forelse($topCustomers as $customer)
                    <div class="flex items-center justify-between rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#CB1B03] font-recoleta text-xs font-bold text-white shadow-[1px_1px_0px_#182830]">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </span>
                            <span class="font-recoleta text-sm font-bold text-[#182830]">{{ $customer->name }}</span>
                        </div>
                        <span class="font-mono text-xs font-bold text-[#25799B] bg-[#F7E6CB] px-2.5 py-1 rounded border border-[#182830]/20">
                            {{ $customer->orders_count }} orders
                        </span>
                    </div>
                @empty
                    <p class="font-mono text-xs text-slate-500 py-2">No customer accounts recorded yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>

<!-- ========================================== -->
<!-- INTERACTIVE STRATEGIC ACTION MODAL         -->
<!-- ========================================== -->
<div id="action-modal" class="fixed inset-0 z-50 hidden bg-[#182830]/75 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[6px_6px_0px_#182830]">
        <button type="button" onclick="closeActionModal()" class="absolute top-4 right-4 text-2xl font-black text-[#182830] hover:text-[#CB1B03] cursor-pointer">×</button>
        
        <div class="flex items-center gap-2 mb-2">
            <span id="modal-badge" class="badge border-[#182830] bg-[#CB1B03] text-white text-[10px] font-mono font-bold uppercase"></span>
            <span class="font-mono text-[10px] text-[#25799B] font-bold">Standard Operational Procedure</span>
        </div>

        <h3 id="modal-title" class="font-recoleta text-xl font-extrabold text-[#182830]"></h3>
        <p id="modal-summary" class="mt-2 text-xs text-slate-700 leading-relaxed font-sans"></p>

        <div class="mt-4 rounded-xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-4">
            <h4 class="font-mono text-xs font-bold uppercase text-[#182830] mb-2 flex items-center gap-1.5">
                <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Actionable Execution Steps:
            </h4>
            <ol id="modal-steps" class="space-y-2 font-mono text-xs text-slate-800 list-decimal list-inside"></ol>
        </div>

        <div class="mt-3 rounded-lg border border-[#182830]/20 bg-[#A2C5D8]/20 px-3.5 py-2 font-mono text-xs text-[#182830]">
            <strong>Financial Target:</strong> <span id="modal-impact"></span>
        </div>

        <div class="mt-5 flex items-center justify-end gap-3 pt-3 border-t border-[#182830]/15">
            <button type="button" onclick="closeActionModal()" class="retro-btn-secondary text-xs px-3.5 py-2">Close</button>
            <a id="modal-cta-btn" href="#" class="retro-btn-primary text-xs px-4 py-2 flex items-center gap-1.5">
                <span id="modal-cta-label">Execute Directive</span>
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>
</div>

<script>
    // Tab switching state
    function switchReportTab(tab) {
        document.querySelectorAll('.report-tab-btn').forEach(btn => {
            btn.classList.remove('border-2', 'border-[#182830]', 'bg-[#F7E6CB]', 'text-[#182830]', 'shadow-[2px_2px_0px_#182830]');
            btn.classList.add('border-2', 'border-transparent', 'bg-white/60', 'text-[#25799B]');
        });

        const activeBtn = document.getElementById('tab-btn-' + tab);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'bg-white/60', 'text-[#25799B]');
            activeBtn.classList.add('border-2', 'border-[#182830]', 'bg-[#F7E6CB]', 'text-[#182830]', 'shadow-[2px_2px_0px_#182830]');
        }

        document.querySelectorAll('.report-view-pane').forEach(pane => pane.classList.add('hidden'));
        const activePane = document.getElementById('report-view-' + tab);
        if (activePane) activePane.classList.remove('hidden');

        const tabInput = document.getElementById('filter-current-tab');
        if (tabInput) tabInput.value = tab;

        // Update URL hash without page reload
        if (history.pushState) {
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tab);
            history.pushState(null, '', newUrl.toString());
        }
    }

    // Modal logic for Strategic Action
    function openActionModal(id, title, badge, summary, stepsJson, impact, ctaLabel, ctaUrl) {
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-badge').textContent = badge;
        document.getElementById('modal-summary').textContent = summary;
        document.getElementById('modal-impact').textContent = impact;
        
        const stepsContainer = document.getElementById('modal-steps');
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

        const ctaBtn = document.getElementById('modal-cta-btn');
        ctaBtn.href = ctaUrl;
        document.getElementById('modal-cta-label').textContent = ctaLabel;

        document.getElementById('action-modal').classList.remove('hidden');
    }

    function closeActionModal() {
        document.getElementById('action-modal').classList.add('hidden');
    }

    // Billing Invoices Client-Side Pagination & Search
    let billingCurrentPage = 1;
    const billingPageSize = 8;

    function getFilteredBillingRows() {
        const query = (document.getElementById('billing-search-input')?.value || '').toLowerCase().trim();
        const rows = Array.from(document.querySelectorAll('.billing-row'));
        if (!query) return rows;
        return rows.filter(row => (row.getAttribute('data-search') || '').includes(query));
    }

    function renderBillingPage() {
        const filtered = getFilteredBillingRows();
        const totalRows = filtered.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / billingPageSize));

        if (billingCurrentPage > totalPages) billingCurrentPage = totalPages;
        if (billingCurrentPage < 1) billingCurrentPage = 1;

        // Hide all rows first
        document.querySelectorAll('.billing-row').forEach(r => r.style.display = 'none');

        // Show slice for current page
        const start = (billingCurrentPage - 1) * billingPageSize;
        const end = start + billingPageSize;
        filtered.slice(start, end).forEach(r => r.style.display = '');

        const info = document.getElementById('billing-page-info');
        if (info) {
            info.textContent = totalRows === 0 
                ? 'No matching invoices' 
                : `Showing ${start + 1}–${Math.min(end, totalRows)} of ${totalRows} receipts (Page ${billingCurrentPage} of ${totalPages})`;
        }

        const prevBtn = document.getElementById('billing-prev-btn');
        const nextBtn = document.getElementById('billing-next-btn');
        if (prevBtn) prevBtn.disabled = (billingCurrentPage <= 1);
        if (nextBtn) nextBtn.disabled = (billingCurrentPage >= totalPages);
    }

    function changeBillingPage(delta) {
        billingCurrentPage += delta;
        renderBillingPage();
    }

    function filterBillingTable() {
        billingCurrentPage = 1;
        renderBillingPage();
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Read URL param or hash on initial load
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || '{{ $activeTab ?? "plan" }}';
        if (tabParam) {
            switchReportTab(tabParam);
        }
        renderBillingPage();
    });
</script>
@endsection
