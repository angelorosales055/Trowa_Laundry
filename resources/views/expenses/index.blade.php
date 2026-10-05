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
                                <p class="font-sans text-xs font-bold text-[#182830]">{{ $expense->description }}</p>
                                <small class="font-mono text-[10px] text-slate-500">By {{ $expense->recordedBy->name }}</small>
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
