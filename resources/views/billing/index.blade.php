@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span>Commercial Invoices & Finished Order Billing</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Billing & Invoices
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Audit settled payments, average ticket metrics, and order invoicing receipts.
        </p>
    </div>

    <button onclick="window.print()" class="retro-btn-secondary text-xs">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        <span>Export / Print Billing</span>
    </button>
</div>

<!-- 3 Billing Metric Cards -->
<div class="mb-6 grid gap-4 md:grid-cols-3">
    @foreach([
        ['revenue', 'Total collected', '₱'.number_format($totalCollected, 2), 'All time revenue', 'bg-[#A2C5D8]'],
        ['txns', 'Transactions', $payments->count(), 'Paid orders', 'bg-[#F7E6CB]'],
        ['avg', 'Avg per order', '₱'.number_format($payments->count() ? $totalCollected / $payments->count() : 0, 2), 'Average ticket size', 'bg-[#25799B] text-white']
    ] as [$iconKey, $label, $value, $hint, $colorClass])
        <div class="retro-panel flex items-start gap-3.5 p-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] {{ $colorClass }} shadow-[2px_2px_0px_#182830]">
                @if($iconKey === 'revenue')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/></svg>
                @elseif($iconKey === 'txns')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                @elseif($iconKey === 'avg')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                @endif
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">{{ $label }}</p>
                <p class="font-recoleta text-2xl font-extrabold text-[#182830] leading-none my-1">{{ $value }}</p>
                <p class="font-mono text-[11px] text-slate-500">{{ $hint }}</p>
            </div>
        </div>
    @endforeach
</div>

<!-- Payment Records Table -->
<div class="retro-panel overflow-hidden">
    <div class="border-b-2 border-[#182830] bg-[#A2C5D8]/30 px-5 py-4">
        <h2 class="font-recoleta text-lg font-bold text-[#182830]">Settled Invoices & Receipts</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/20 font-mono text-xs uppercase tracking-wider text-[#182830]">
                <tr>
                    <th class="px-5 py-3.5 font-extrabold">Order / Ticket</th>
                    <th class="px-5 py-3.5 font-extrabold">Customer</th>
                    <th class="px-5 py-3.5 font-extrabold">Services Rendered</th>
                    <th class="px-5 py-3.5 font-extrabold">Date Settled</th>
                    <th class="px-5 py-3.5 font-extrabold text-right">Settled Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#182830]/10 bg-[#FFFDF8]">
                @forelse($payments as $payment)
                    <tr class="transition hover:bg-[#F7E6CB]/30">
                        <td class="px-5 py-3.5 font-mono text-xs font-extrabold text-[#CB1B03]">
                            TL-{{ str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-5 py-3.5 font-recoleta text-sm font-bold text-[#182830]">
                            {{ $payment->customer?->name ?? $payment->customer_name }}
                        </td>
                        <td class="px-5 py-3.5 text-xs text-[#25799B] font-medium">
                            {{ $payment->services }}
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs text-slate-600">
                            {{ $payment->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono text-xs font-black text-emerald-700">
                            ₱{{ number_format($payment->total_price, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500 font-mono text-xs">
                            No completed payment receipts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-[#182830] bg-[#F7E6CB]/50 font-mono text-xs font-black">
                    <td colspan="4" class="px-5 py-3.5 text-[#182830]">Grand Total Revenue</td>
                    <td class="px-5 py-3.5 text-right text-[#CB1B03] text-sm">₱{{ number_format($totalCollected, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
