@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-[#CB1B03] animate-pulse"></span>
            <span>Manager Desk · {{ now()->format('l, F j, Y') }}</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Manager Dashboard
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Shift tracking, team performance, and real-time intake monitoring.
        </p>
    </div>
    <span class="rounded-xl border-2 border-[#182830] bg-[#A2C5D8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830]">
        Manager Access
    </span>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach([
        ['orders', "Today's Orders", $recentOrders->count(), 'Scheduled today', 'bg-[#A2C5D8]'],
        ['pending', 'Pending', $recentOrders->where('status', 'pending')->count(), 'Need attention', 'bg-[#F7E6CB]'],
        ['staff', 'Active Staff', count($staffPerf), 'Total team on duty', 'bg-[#25799B] text-white'],
        ['revenue', 'Total Revenue', '₱'.number_format($recentOrders->where('status', 'delivered')->sum('total_price'), 2), 'From completed orders', 'bg-emerald-100 text-emerald-800']
    ] as [$iconKey, $label, $value, $hint, $colorClass])
        <div class="retro-panel flex items-start gap-3.5 p-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] {{ $colorClass }} shadow-[2px_2px_0px_#182830]">
                @if($iconKey === 'orders')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                @elseif($iconKey === 'pending')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/></svg>
                @elseif($iconKey === 'staff')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                @elseif($iconKey === 'revenue')
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/></svg>
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

<div class="grid gap-6 lg:grid-cols-2">
    <div class="retro-panel p-5">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830]">Recent Intake Orders</h2>
            <a href="{{ route('orders.index') }}" class="font-mono text-xs font-bold text-[#25799B] hover:underline">All Orders ➔</a>
        </div>
        <div class="space-y-2.5">
            @forelse($recentOrders as $order)
                <div class="flex items-center justify-between rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                    <div>
                        <p class="font-recoleta text-sm font-bold text-[#182830]">{{ $order->customer_name }}</p>
                        <p class="font-mono text-[11px] text-[#25799B]">TL-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }} · {{ $order->services }}</p>
                    </div>
                    <span class="badge border-[#182830] {{ $order->status === 'ready' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'pending' ? 'bg-[#F7E6CB] text-[#182830]' : 'bg-[#A2C5D8] text-[#182830]') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            @empty
                <p class="font-mono text-xs text-slate-500 py-3">No orders recorded yet today.</p>
            @endforelse
        </div>
    </div>

    <div class="retro-panel p-5">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <h2 class="font-recoleta text-lg font-bold text-[#182830]">Staff Shift Throughput</h2>
            <span class="font-mono text-xs font-bold text-[#25799B]">Active Counters</span>
        </div>
        <div class="space-y-3">
            @forelse($staffPerf as $staff)
                <div class="flex items-center justify-between rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                    <span class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#CB1B03] font-recoleta text-sm font-bold text-white shadow-[1px_1px_0px_#182830]">
                            {{ strtoupper(substr($staff['name'], 0, 1)) }}
                        </span>
                        <span class="font-sans text-xs font-bold text-[#182830]">{{ $staff['name'] }}</span>
                    </span>
                    <span class="font-mono text-xs font-bold text-[#25799B] bg-[#F7E6CB] px-2.5 py-1 rounded-md border border-[#182830]/20">
                        {{ $staff['completed'] }} completed
                    </span>
                </div>
            @empty
                <p class="font-mono text-xs text-slate-500 py-3">No active staff records today.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
