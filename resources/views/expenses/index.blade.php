@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span>Operating Expenditures & Overhead Ledger</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Expenses
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Record categorized business expenses for accurate net profit reporting.
        </p>
    </div>
</div>

<!-- ========================================================================= -->
<!-- LIVE WATER, POWER & SUPPLIES USAGE AUTO-CALCULATOR PANEL                  -->
<!-- ========================================================================= -->
<div class="mb-6 rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[6px_6px_0px_#182830]">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b-2 border-[#182830]/15 pb-4 mb-5">
        <div>
            <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                <span>Automated Resource Telemetry</span>
                <span>·</span>
                <span>{{ $waterStats['orders_count'] }} Transactions / {{ $waterStats['total_loads'] }} Machine Loads</span>
            </div>
            <h2 class="font-recoleta text-2xl font-black text-[#182830] mt-1">
                Overall Water &amp; Operating Supplies Consumption
            </h2>
            <p class="font-mono text-xs text-slate-600 mt-0.5">
                Calculates real-time water volume, machine cycle kilowatt-hours, and detergent sachet consumption based on recorded customer orders.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full border-2 border-emerald-800 bg-emerald-100 px-3.5 py-1.5 font-mono text-xs font-bold text-emerald-950 shadow-[2px_2px_0px_#182830]">
                <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Automated: Water &amp; Electricity Logged Per Transaction</span>
            </span>
        </div>
    </div>

    <!-- 4 KPI Telemetry Cards: Water, Power, Supplies, Period Total -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Card 1: Water Volume -->
        <div class="rounded-2xl border-2 border-[#182830] bg-[#A2C5D8]/25 p-4 shadow-[3px_3px_0px_#182830] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[10px] font-black uppercase text-[#25799B]">Water Consumption</span>
                    <span class="badge border border-[#182830] bg-[#FFFDF8] text-[9px] font-mono font-bold">50L / Load</span>
                </div>
                <div class="mt-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-recoleta text-3xl font-black text-[#182830]">{{ number_format($waterStats['water_liters'], 0) }}</span>
                        <span class="font-mono text-xs font-bold text-slate-600">Liters</span>
                    </div>
                    <span class="font-mono text-[11px] text-slate-600 block mt-0.5">
                        {{ number_format($waterStats['water_m3'], 2) }} m³ (cubic meters)
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-[#182830]/15 flex items-center justify-between font-mono text-xs">
                <span class="text-slate-600">Calculated Water Cost:</span>
                <strong class="font-bold text-[#CB1B03]">₱{{ number_format($waterStats['est_water_cost'], 2) }}</strong>
            </div>
        </div>

        <!-- Card 2: Electricity & Power -->
        <div class="rounded-2xl border-2 border-[#182830] bg-[#F7E6CB]/50 p-4 shadow-[3px_3px_0px_#182830] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[10px] font-black uppercase text-amber-800">Machine Power (kWh)</span>
                    <span class="badge border border-[#182830] bg-[#FFFDF8] text-[9px] font-mono font-bold">1.2 kWh / Load</span>
                </div>
                <div class="mt-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-recoleta text-3xl font-black text-[#182830]">{{ number_format($waterStats['total_kwh'], 1) }}</span>
                        <span class="font-mono text-xs font-bold text-slate-600">kWh</span>
                    </div>
                    <span class="font-mono text-[11px] text-slate-600 block mt-0.5">
                        Washers &amp; dryers run time
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-[#182830]/15 flex items-center justify-between font-mono text-xs">
                <span class="text-slate-600">Calculated Power Cost:</span>
                <strong class="font-bold text-[#CB1B03]">₱{{ number_format($waterStats['est_power_cost'], 2) }}</strong>
            </div>
        </div>

        <!-- Card 3: Supply Sachets Consumed -->
        <div class="rounded-2xl border-2 border-[#182830] bg-emerald-50/70 border-emerald-900 p-4 shadow-[3px_3px_0px_#182830] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[10px] font-black uppercase text-emerald-800">Supplies Consumed</span>
                    <span class="badge border border-emerald-800 bg-emerald-100 text-[9px] font-mono font-bold text-emerald-900">Live Stock Usage</span>
                </div>
                <div class="mt-2">
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-recoleta text-3xl font-black text-emerald-950">{{ number_format($waterStats['supplies_used'], 0) }}</span>
                        <span class="font-mono text-xs font-bold text-emerald-800">Sachets / Units</span>
                    </div>
                    <span class="font-mono text-[11px] text-slate-600 block mt-0.5">
                        Auto-deducted from shop inventory
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-[#182830]/15 flex items-center justify-between font-mono text-xs">
                <span class="text-slate-600">Estimated Soap Cost:</span>
                <strong class="font-bold text-emerald-900">₱{{ number_format($waterStats['est_supplies_cost'], 2) }}</strong>
            </div>
        </div>

        <!-- Card 4: Operating Cost Rate per Load -->
        <div class="rounded-2xl border-2 border-[#182830] bg-[#182830] p-4 text-[#FFFDF8] shadow-[3px_3px_0px_#CB1B03] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[10px] font-black uppercase text-[#A2C5D8]">Resource Cost Rate</span>
                    <span class="rounded bg-[#CB1B03] px-1.5 py-0.5 text-[9px] font-mono font-black text-white">Live</span>
                </div>
                <div class="mt-2">
                    <div class="flex items-baseline gap-1">
                        <span class="font-recoleta text-3xl font-black text-[#F7E6CB]">₱{{ $waterStats['total_loads'] > 0 ? number_format(($waterStats['est_utilities_total'] + $waterStats['est_supplies_cost']) / $waterStats['total_loads'], 2) : '0.00' }}</span>
                        <span class="font-mono text-xs text-[#A2C5D8]">/ load</span>
                    </div>
                    <span class="font-mono text-[11px] text-slate-300 block mt-0.5">
                        Water + Power + Detergent
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-white/20 flex items-center justify-between font-mono text-xs">
                <span class="text-[#A2C5D8]">Total Resources:</span>
                <strong class="font-black text-[#F7E6CB]">₱{{ number_format($waterStats['est_utilities_total'] + $waterStats['est_supplies_cost'], 2) }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="mb-6 grid gap-6 lg:grid-cols-[1fr_1.5fr]">
    <!-- Log Expense Form -->
    <div class="retro-panel p-5">
        <div class="mb-4 border-b-2 border-[#182830]/15 pb-2.5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830]">Log Expense</h2>
            <p class="font-mono text-xs text-[#25799B]">Utilities, detergent supplies, rent, machine maintenance</p>
        </div>

        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-3.5">
            @csrf
            <div>
                <label for="expense-cat-select" class="block font-mono text-xs font-bold text-[#182830] mb-1">Expense Category *</label>
                <select id="expense-cat-select" name="category" class="field text-xs" required>
                    @foreach(['utilities'=>'Utilities (Water & Power)','rent'=>'Shop Rent','supplies'=>'Bulk Supplies','maintenance'=>'Machine Maintenance','transportation'=>'Transportation / Delivery','payroll'=>'Staff Payroll','other'=>'Other Overhead'] as $key=>$label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="expense-desc-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Expense Description *</label>
                <input id="expense-desc-input" name="description" class="field text-xs" placeholder="e.g. Electricity bill for September" required maxlength="255">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="expense-amt-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Amount (₱) *</label>
                    <input id="expense-amt-input" name="amount" type="number" min="0.01" step="0.01" class="field text-xs font-mono font-bold" placeholder="0.00" required>
                </div>
                <div>
                    <label for="expense-date-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Date *</label>
                    <input id="expense-date-input" name="expense_date" type="date" value="{{ now()->toDateString() }}" class="field text-xs font-mono" required>
                </div>
            </div>
            <div>
                <label for="expense-ref-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Receipt / Voucher Reference</label>
                <input id="expense-ref-input" name="reference_number" class="field text-xs" placeholder="OR # or voucher code">
            </div>
            <button type="submit" class="retro-btn-primary w-full text-xs mt-2">Record Expense ➔</button>
        </form>
    </div>

    <!-- Filter & Expense Records -->
    <div class="retro-panel p-5">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3 border-b-2 border-[#182830]/15 pb-4">
            <form method="GET" class="flex flex-wrap items-end gap-2.5">
                <div>
                    <label for="exp-filter-from" class="block font-mono text-[10px] font-bold uppercase text-[#25799B]">From</label>
                    <input id="exp-filter-from" type="date" name="from" value="{{ request('from') }}" class="field h-8 px-2 py-1 text-xs font-mono">
                </div>
                <div>
                    <label for="exp-filter-to" class="block font-mono text-[10px] font-bold uppercase text-[#25799B]">To</label>
                    <input id="exp-filter-to" type="date" name="to" value="{{ request('to') }}" class="field h-8 px-2 py-1 text-xs font-mono">
                </div>
                <button type="submit" class="retro-btn-secondary h-8 text-xs px-3">Filter</button>
                @if(request('from') || request('to'))
                    <a href="{{ route('expenses.index') }}" class="font-mono text-xs text-[#CB1B03] underline hover:no-underline self-center">Clear</a>
                @endif
            </form>

            <div class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-3.5 py-2 text-right shadow-[2px_2px_0px_#182830]">
                <p class="font-mono text-[10px] font-bold uppercase text-[#FFFDF8]">Filtered Expense Total</p>
                <strong class="font-recoleta text-2xl font-black text-[#F7E6CB]">₱{{ number_format((float) $total, 2) }}</strong>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b-2 border-[#182830] font-mono text-xs uppercase text-[#25799B]">
                    <tr>
                        <th class="py-2.5">Date / Category</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182830]/10">
                    @forelse($expenses as $expense)
                        <tr class="transition hover:bg-[#F7E6CB]/30">
                            <td class="py-2.5">
                                <span class="font-mono text-xs text-slate-700">{{ $expense->expense_date->format('Y-m-d') }}</span>
                                <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830] ml-1 text-[10px]">
                                    {{ ucfirst($expense->category) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <p class="font-sans text-xs font-bold text-[#182830]">{{ $expense->description }}</p>
                                    @if($expense->order)
                                        <a href="{{ route('orders.show', $expense->order_id) }}" class="inline-flex items-center gap-1 font-mono text-[10px] font-bold text-[#25799B] bg-[#A2C5D8]/30 px-1.5 py-0.5 rounded border border-[#25799B]/30 hover:underline">
                                            <span>#{{ $expense->order->order_number }}</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 font-mono text-[10px] text-slate-500">
                                    <span>By {{ $expense->recordedBy->name }}</span>
                                    @if($expense->reference_number)
                                        <span>· Ref: {{ $expense->reference_number }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right font-mono text-xs font-black text-[#CB1B03]">
                                ₱{{ number_format((float) $expense->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-500 font-mono text-xs">
                                No expenses recorded for this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection
