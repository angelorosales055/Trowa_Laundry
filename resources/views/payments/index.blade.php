@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/></svg>
            <span>Cashier Register & Settlement Terminal</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Payments & Collections
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Monitor receivables, record cash and digital tenders, and issue settlement receipts.
        </p>

        @if($method || $from || $to)
            <div class="mt-3 inline-flex items-center gap-2 rounded-lg border-2 border-[#182830] bg-[#A2C5D8] px-3 py-1 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830]">
                <span>Filter Active:</span>
                @if($method)<span>Channel: {{ strtoupper($method) }}</span>@endif
                @if($from || $to)<span>{{ $from ? 'from '.$from : '' }}{{ $from && $to ? ' ' : '' }}{{ $to ? 'through '.$to : '' }}</span>@endif
                <a href="{{ route('payments.index') }}" class="ml-2 font-bold text-[#CB1B03] underline hover:no-underline">Clear filters ×</a>
            </div>
        @endif
    </div>
</div>

<div class="retro-panel overflow-hidden">
    <div class="border-b-2 border-[#182830] bg-[#A2C5D8]/30 px-5 py-4">
        <h2 class="font-recoleta text-lg font-bold text-[#182830]">Order Payment Accounts & Receivables</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/20 font-mono text-xs uppercase tracking-wider text-[#182830]">
                <tr>
                    <th class="px-5 py-3.5 font-extrabold">Order / Ticket</th>
                    <th class="px-5 py-3.5 font-extrabold">Customer Name</th>
                    <th class="px-5 py-3.5 font-extrabold">Total Billed</th>
                    <th class="px-5 py-3.5 font-extrabold">Amount Paid</th>
                    <th class="px-5 py-3.5 font-extrabold">Balance Due</th>
                    <th class="px-5 py-3.5 font-extrabold">Payment State</th>
                    <th class="px-5 py-3.5 font-extrabold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#182830]/10 bg-[#FFFDF8]">
                @forelse($orders as $order)
                    @php($paid = (float) $order->amount_paid)
                    @php($balance = max(0, (float) $order->total_price - $paid))
                    <tr class="transition hover:bg-[#F7E6CB]/30">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-extrabold text-[#CB1B03] hover:underline">
                                {{ $order->order_number ?? 'TL-'.$order->id }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-recoleta text-sm font-bold text-[#182830]">{{ $order->customer?->name ?? $order->customer_name }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs font-extrabold text-[#182830]">
                            ₱{{ number_format((float) $order->total_price, 2) }}
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs font-bold text-emerald-700">
                            ₱{{ number_format($paid, 2) }}
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs font-extrabold {{ $balance > 0 ? 'text-[#CB1B03]' : 'text-emerald-700' }}">
                            ₱{{ number_format($balance, 2) }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="badge border-[#182830] {{ $balance <= 0 ? 'bg-emerald-100 text-emerald-800' : ($paid > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-[#CB1B03]') }}">
                                {{ $balance <= 0 ? 'Paid in Full' : ($paid > 0 ? 'Partially Paid' : 'Payment Pending') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('orders.show', $order) }}" class="retro-btn-secondary px-3 py-1 text-xs">
                                Manage Payment ➔
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-500 font-mono text-xs">
                            No orders found in this payment register view.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t-2 border-[#182830] bg-[#FFFDF8] px-5 py-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
