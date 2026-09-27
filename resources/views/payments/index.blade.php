@extends('layouts_app')

@section('content')
<div class="mb-7">
    <p class="mb-1 text-sm font-medium text-brand-600">Payment module</p>
    <h1 class="text-3xl font-bold text-slate-900">Payments</h1>
    <p class="mt-1 text-sm text-slate-500">Review balances and record payments for laundry orders.</p>
    @if($method || $from || $to)<p class="mt-2 text-xs text-brand-700">@if($method)Filtered by {{ strtoupper($method) }}@endif @if($from || $to){{ $from ? 'from '.$from : '' }}{{ $from && $to ? ' ' : '' }}{{ $to ? 'through '.$to : '' }}@endif · <a href="{{ route('payments.index') }}" class="underline">Clear filters</a></p>@endif
</div>
<div class="panel overflow-hidden">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="font-display text-lg font-semibold">Order Payment Accounts</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Paid</th><th class="px-5 py-3">Balance</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Action</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    @php($paid = (float) $order->amount_paid)
                    @php($balance = max(0, (float) $order->total_price - $paid))
                    <tr>
                        <td class="px-5 py-3 font-semibold text-brand-600">{{ $order->order_number ?? 'TL-'.$order->id }}</td>
                        <td class="px-5 py-3">{{ $order->customer?->name ?? $order->customer_name }}</td>
                        <td class="px-5 py-3">₱{{ number_format((float) $order->total_price, 2) }}</td>
                        <td class="px-5 py-3">₱{{ number_format($paid, 2) }}</td>
                        <td class="px-5 py-3 font-semibold {{ $balance > 0 ? 'text-amber-700' : 'text-emerald-700' }}">₱{{ number_format($balance, 2) }}</td>
                        <td class="px-5 py-3"><span class="badge {{ $balance <= 0 ? 'bg-emerald-100 text-emerald-700' : ($paid > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ $balance <= 0 ? 'Paid' : ($paid > 0 ? 'Partially Paid' : 'Pending') }}</span></td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('orders.show', $order) }}" class="btn-secondary px-3 py-1 text-xs">Manage Payment</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">No orders available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 px-5 py-3">{{ $orders->links() }}</div>
</div>
@endsection
