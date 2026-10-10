@extends('layouts_app')

@section('content')
@php
    $statusLabels = [
        'pending_confirmation' => 'Pending Confirmation',
        'received' => 'Received',
        'washing' => 'Washing',
        'drying' => 'Drying',
        'ironing' => 'Ironing',
        'folding' => 'Folding',
        'ready_for_pickup' => 'Ready for Pickup',
        'claimed' => 'Claimed',
        'cancelled' => 'Cancelled',
    ];
@endphp

<div class="space-y-6">

    <!-- Top Station Header & Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
                <!-- Natural Vector Conveyor / Laundry Queue SVG -->
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                </svg>
                <span>Laundry Processing & Intake Line</span>
            </div>
            <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
                Laundry Queue Management
            </h1>
            <p class="text-xs sm:text-sm font-medium text-[#25799B]">
                Live orders, stage dispatching, batch weights, and cashier settlement.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Active Counter Pill -->
            <div class="flex items-center gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#CB1B03] opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#CB1B03]"></span>
                </span>
                <span class="font-mono text-xs font-bold uppercase tracking-wide">
                    {{ $stats['total_active'] }} Active Ticket{{ $stats['total_active'] === 1 ? '' : 's' }}
                </span>
            </div>

            <!-- New Laundry Intake Trigger -->
            <button type="button" data-modal-open="order-modal" class="retro-btn-primary flex items-center gap-2 text-sm shadow-[3px_3px_0px_#182830]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span class="font-recoleta font-bold">Fast Laundry Intake</span>
                <kbd class="hidden sm:inline-block rounded border border-white/40 bg-black/20 px-1.5 py-0.5 text-[10px] font-mono">Alt+N</kbd>
            </button>
        </div>
    </div>

    <!-- Live Queue Pulse Metrics (4 Tactile Retro KPI Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Active in Queue -->
        <a href="{{ route('orders.index', ['status' => '']) }}" class="group block rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#25799B] transition hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_#182830]">
            <div class="flex items-center justify-between text-[#25799B]">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider">Queue Total</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#A2C5D8] text-[#182830]">
                    <svg class="h-4 w-4 animate-spin [animation-duration:8s]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v3"/><path d="M12 18v3"/><path d="M3 12h3"/><path d="M18 12h3"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-mono text-3xl font-black text-[#182830]">{{ $stats['total_active'] }}</span>
                <span class="font-mono text-xs font-bold text-[#25799B]">ticket(s)</span>
            </div>
            <p class="mt-1 font-mono text-[10px] text-slate-500">{{ $stats['total_loads_active'] }} load(s) processing</p>
        </a>

        <!-- Card 2: Awaiting Wash -->
        <a href="{{ route('orders.index', ['status' => 'received']) }}" class="group block rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#25799B] transition hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_#182830]">
            <div class="flex items-center justify-between text-[#25799B]">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider">Awaiting Wash</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#F7E6CB] text-[#25799B]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="12" cy="13" r="4.5"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-mono text-3xl font-black text-[#182830]">{{ $stats['awaiting_wash'] }}</span>
                <span class="font-mono text-xs font-bold text-[#25799B]">in intake</span>
            </div>
            <p class="mt-1 font-mono text-[10px] text-slate-500">In intake queue</p>
        </a>

        <!-- Card 3: Ready for Pickup -->
        <a href="{{ route('orders.index', ['status' => 'ready_for_pickup']) }}" class="group block rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#25799B] transition hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_#182830]">
            <div class="flex items-center justify-between text-[#25799B]">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider">Ready for Pickup</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-emerald-100 text-emerald-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-mono text-3xl font-black text-[#182830]">{{ $stats['ready'] }}</span>
                <span class="font-mono text-xs font-bold text-emerald-700">bagged</span>
            </div>
            <p class="mt-1 font-mono text-[10px] text-slate-500">Bagged & ready</p>
        </a>

        <!-- Card 4: Unpaid Balances -->
        <a href="{{ route('orders.index', ['payment_status' => 'unpaid']) }}" class="group block rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#CB1B03] transition hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_#182830]">
            <div class="flex items-center justify-between text-[#CB1B03]">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider">Receivables Due</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#CB1B03] text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-mono text-2xl sm:text-3xl font-black text-[#CB1B03]">₱{{ number_format($stats['unpaid_amount'], 2) }}</span>
            </div>
            <p class="mt-1 font-mono text-[10px] text-slate-500">{{ $stats['unpaid_count'] }} ticket(s) pending balance</p>
        </a>
    </div>

    <!-- Interactive Stage Filter Tabs Strip -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1 no-scrollbar">
        @php
            $stageTabs = [
                '' => ['All Tickets', $stats['total_all'] ?? $orders->total()],
                'pending_confirmation' => ['Online Requests', $stats['pending_confirmation'] ?? 0],
                'received' => ['Intake / Queued', $stats['awaiting_wash']],
                'washing' => ['Washing', $stats['washing']],
                'drying' => ['Drying', $stats['drying']],
                'ironing' => ['Ironing', $stats['ironing']],
                'folding' => ['Folding', $stats['folding']],
                'ready_for_pickup' => ['Ready for Pickup', $stats['ready']],
                'claimed' => ['Claimed', $stats['claimed']],
                'cancelled' => ['Cancelled', $stats['cancelled']],
            ];
        @endphp

        @foreach($stageTabs as $val => [$tabName, $count])
            @php
                $isCurrent = ($canonicalStatus === $val) || ($val === '' && $canonicalStatus === '');
            @endphp
            <a href="{{ route('orders.index', array_merge(request()->except('page'), ['status' => $val])) }}" 
               class="group shrink-0 flex items-center gap-2 rounded-xl px-3.5 py-2 font-mono text-xs font-bold transition border-2 border-[#182830] {{ $isCurrent ? 'bg-[#182830] text-[#FFFDF8] shadow-[2px_2px_0px_#182830]' : 'bg-[#FFFDF8] text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830]' }}">
                <span>{{ $tabName }}</span>
                <span class="rounded-md px-1.5 py-0.5 text-[10px] font-black {{ $isCurrent ? 'bg-[#CB1B03] text-white' : 'bg-[#A2C5D8] text-[#182830]' }}">
                    {{ $count }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Search & Filter Options Bar -->
    <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#182830]">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 8) }}">

            <!-- Instant Search Input -->
            <div class="sm:col-span-5 relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search by ticket #, customer name, or phone..." 
                       class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] pl-10 pr-4 py-2.5 text-xs font-medium text-[#182830] shadow-[1px_1px_0px_#182830] focus:border-[#25799B] focus:outline-none">
                <span class="absolute left-3.5 top-3 text-[#25799B]">
                    <!-- Natural Search Lens SVG -->
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
            </div>

            <!-- Payment Filter -->
            <div class="sm:col-span-3">
                <select name="payment_status" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2.5 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:border-[#25799B] focus:outline-none">
                    <option value="">All Payment Statuses</option>
                    <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                    <option value="partially_paid" {{ $paymentStatus === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                </select>
            </div>

            <!-- Date Filter (Today / Custom) -->
            <div class="sm:col-span-2">
                <input type="date" name="date" value="{{ $date }}" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-2.5 py-2 font-mono text-xs text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 rounded-xl border-2 border-[#182830] bg-[#25799B] py-2.5 font-recoleta text-xs font-bold text-white shadow-[2px_2px_0px_#182830] hover:bg-[#1E6482] transition cursor-pointer text-center">
                    Filter
                </button>
                @if($search !== '' || $status !== '' || $paymentStatus !== '' || $date !== '')
                    <a href="{{ route('orders.index') }}" title="Clear filters" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Laundry Queue Line Board (Responsive Retro Table / Board) -->
    <div id="queue-board" class="rounded-3xl border-2 border-[#182830] bg-[#FFFDF8] shadow-[4px_4px_0px_#182830] overflow-hidden">
        <!-- Top Board Status & Quick Pagination Strip -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-[#182830]/15 bg-[#FFFDF8] px-5 py-3">
            <div class="flex items-center gap-2.5">
                <span class="flex h-2.5 w-2.5 rounded-full bg-[#CB1B03] animate-pulse"></span>
                <span class="font-recoleta text-base font-bold text-[#182830]">Processing Line Board</span>
                <span class="rounded-full border border-[#182830]/25 bg-[#F7E6CB] px-2.5 py-0.5 font-mono text-[11px] font-bold text-[#182830]">
                    {{ $orders->total() }} Total Tickets
                </span>
            </div>

            <!-- Top Page Quick Switcher & Page Size -->
            <div class="flex items-center gap-3">
                <div class="font-mono text-xs text-[#25799B]">
                    Page <strong class="text-[#182830]">{{ $orders->currentPage() }}</strong> of <strong class="text-[#182830]">{{ $orders->lastPage() }}</strong>
                </div>

                <div class="flex items-center gap-1">
                    @if($orders->onFirstPage())
                        <span class="rounded-lg border border-slate-300 bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-400 cursor-not-allowed select-none">
                            ←
                        </span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" title="Previous page" class="rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-2.5 py-1 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#F7E6CB] transition">
                            ←
                        </a>
                    @endif

                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" title="Next page" class="rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-2.5 py-1 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#F7E6CB] transition">
                            →
                        </a>
                    @else
                        <span class="rounded-lg border border-slate-300 bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-400 cursor-not-allowed select-none">
                            →
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/35 font-mono text-xs uppercase tracking-wider text-[#182830]">
                    <tr>
                        <th class="px-4 py-3 font-black">Ticket</th>
                        <th class="px-4 py-3 font-black">Customer</th>
                        <th class="px-4 py-3 font-black">Weight & Loads</th>
                        <th class="px-4 py-3 font-black">Services</th>
                        <th class="px-4 py-3 font-black">Settlement</th>
                        <th class="px-4 py-3 font-black">Stage</th>
                        <th class="px-4 py-3 font-black text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182830]/10 bg-[#FFFDF8]">
                    @forelse($orders as $order)
                        @php
                            $availableStatuses = $order->nextStatuses();
                            $nextStatus = collect($availableStatuses)->first(fn ($s) => $s !== 'cancelled');
                            $balanceDue = max(0, (float) $order->total_price - (float) $order->amount_paid);
                        @endphp
                        <tr class="transition hover:bg-[#F7E6CB]/35 group">
                            
                            <!-- 1. Ticket Number & Time -->
                            <td class="px-4 py-3 align-middle">
                                <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-black text-[#CB1B03] hover:underline flex items-center gap-1">
                                    <span>{{ $order->order_number ?? 'TL-'.$order->id }}</span>
                                </a>
                                <p class="font-mono text-[11px] text-slate-500 mt-0.5">
                                    {{ $order->created_at?->format('M j · g:i A') }}
                                </p>
                            </td>

                            <!-- 2. Customer Profile -->
                            <td class="px-4 py-3 align-middle">
                                <div class="font-recoleta text-sm font-bold text-[#182830] leading-tight" title="{{ $order->customer?->address ?? '' }}">
                                    {{ $order->customer?->name ?? $order->customer_name }}
                                </div>
                                @if($order->customer?->contact_number ?? $order->customer?->phone)
                                    <p class="font-mono text-[11px] text-[#25799B] mt-0.5">
                                        {{ $order->customer?->contact_number ?? $order->customer?->phone }}
                                    </p>
                                @endif
                            </td>

                            <!-- 3. Weight & Loads -->
                            <td class="px-4 py-3 align-middle font-mono">
                                <div class="text-xs font-black text-[#182830]">
                                    {{ $order->weight_kg }} kg
                                    <span class="text-slate-400 font-normal">·</span>
                                    <span class="font-bold text-[#25799B]">{{ $order->number_of_loads }} commercial load{{ $order->number_of_loads > 1 ? 's' : '' }}</span>
                                </div>
                                @if($order->itemDetails->isNotEmpty())
                                    <span class="block text-[10px] text-slate-400 mt-0.5" title="{{ $order->itemDetails->map(fn($i)=>"{$i->item_name} ({$i->quantity})")->implode(', ') }}">
                                        {{ $order->itemDetails->count() }} garment item(s) listed
                                    </span>
                                @endif
                            </td>

                            <!-- 4. Selected Services -->
                            <td class="px-4 py-3 align-middle">
                                @php
                                    $serviceList = collect(explode(',', (string) $order->services))
                                        ->map(fn($s) => trim($s))
                                        ->filter(fn($s) => $s !== '');
                                    if ($serviceList->isEmpty() && $order->orderServices->isNotEmpty()) {
                                        $serviceList = $order->orderServices->map(fn($os) => $os->service?->name ?? 'Laundry')->filter();
                                    }
                                    $svcCount = $serviceList->count() ?: 1;
                                    $serviceNames = $serviceList->implode(', ') ?: 'Standard Wash';

                                    $tier = match(true) {
                                        $svcCount <= 2 => [
                                            'name' => 'Basic Service',
                                            'badge' => 'border-2 border-[#182830] bg-[#FFFDF8] text-[#182830] shadow-[1px_1px_0px_#182830]',
                                            'dot' => 'bg-[#25799B]',
                                        ],
                                        $svcCount <= 4 => [
                                            'name' => 'Deluxe Service',
                                            'badge' => 'border-2 border-[#182830] bg-[#25799B] text-white shadow-[1px_1px_0px_#182830]',
                                            'dot' => 'bg-[#F7E6CB]',
                                        ],
                                        default => [
                                            'name' => 'Premium Service',
                                            'badge' => 'border-2 border-[#182830] bg-[#CB1B03] text-white shadow-[1px_1px_0px_#182830]',
                                            'dot' => 'bg-amber-300',
                                        ],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-mono font-black {{ $tier['badge'] }}" title="{{ $serviceNames }}">
                                    <span class="h-2 w-2 rounded-full {{ $tier['dot'] }}"></span>
                                    <span>{{ $tier['name'] }}</span>
                                </span>
                            </td>

                            <!-- 5. Settlement / Payment -->
                            <td class="px-4 py-3 align-middle">
                                @if($order->payment_status === 'paid' || $balanceDue <= 0)
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 font-mono text-xs font-black text-emerald-700">
                                            ✓ Paid
                                        </span>
                                        <span class="line-through font-mono text-[11px] text-slate-400">
                                            ₱{{ number_format((float) $order->total_price, 2) }}
                                        </span>
                                    </div>
                                @elseif($order->payment_status === 'partially_paid')
                                    <div class="font-mono text-xs font-black text-[#CB1B03]">
                                        ₱{{ number_format($balanceDue, 2) }}
                                        <span class="line-through text-[10px] text-slate-400 font-normal">₱{{ number_format((float) $order->total_price, 2) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-[10px] font-bold text-amber-700">Partial</span>
                                        <button type="button" 
                                                class="open-quick-pay-btn font-mono text-[10px] font-bold text-[#25799B] hover:text-[#CB1B03] hover:underline cursor-pointer"
                                                data-order-id="{{ $order->id }}"
                                                data-order-number="{{ $order->order_number ?? 'TL-'.$order->id }}"
                                                data-customer="{{ $order->customer?->name ?? $order->customer_name }}"
                                                data-balance="{{ $balanceDue }}">
                                            + Pay Bal
                                        </button>
                                    </div>
                                @else
                                    <div class="font-mono text-xs font-black text-[#182830]">
                                        ₱{{ number_format((float) $order->total_price, 2) }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-[10px] font-bold text-[#CB1B03]">Unpaid</span>
                                        <button type="button" 
                                                class="open-quick-pay-btn font-mono text-[10px] font-bold text-[#25799B] hover:text-[#CB1B03] hover:underline cursor-pointer"
                                                data-order-id="{{ $order->id }}"
                                                data-order-number="{{ $order->order_number ?? 'TL-'.$order->id }}"
                                                data-customer="{{ $order->customer?->name ?? $order->customer_name }}"
                                                data-balance="{{ $balanceDue }}">
                                            + Pay
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <!-- 6. Stage Status Badge -->
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center gap-1.5 rounded-xl border-2 border-[#182830] px-2.5 py-1 font-mono text-xs font-black shadow-[1px_1px_0px_#182830] {{ match($order->status) {
                                    'pending_confirmation' => 'bg-amber-400 text-amber-950 animate-pulse',
                                    'washing', 'in-progress' => 'bg-[#25799B] text-white',
                                    'drying' => 'bg-[#CB1B03] text-white',
                                    'ironing' => 'bg-amber-400 text-[#182830]',
                                    'folding' => 'bg-[#A2C5D8] text-[#182830]',
                                    'ready_for_pickup', 'ready' => 'bg-emerald-600 text-white',
                                    'claimed', 'delivered' => 'bg-slate-800 text-white',
                                    'cancelled' => 'bg-red-800 text-white',
                                    default => 'bg-[#F7E6CB] text-[#182830]',
                                } }}">
                                    <span>{{ $order->status === 'pending_confirmation' ? 'Online Request' : str_replace('_', ' ', ucfirst($order->status)) }}</span>
                                </span>
                            </td>

                            <!-- 7. Action / Quick Stage Advancement -->
                            <td class="px-4 py-3 align-middle text-right">
                                <div class="flex flex-col items-end gap-1">
                                    @if($order->status === 'pending_confirmation')
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <form method="POST" action="{{ route('orders.confirm-online', $order) }}" data-confirm="Accept Order #{{ $order->order_number }} into the shop wash queue?" data-confirm-title="Confirm Online Laundry Order" data-confirm-type="primary" data-confirm-btn="Yes, Accept Order">
                                                @csrf
                                                <button type="submit" class="rounded-xl border-2 border-[#182830] bg-emerald-600 px-3 py-1 font-recoleta text-xs font-black text-white shadow-[2px_2px_0px_#182830] hover:bg-emerald-700 transition cursor-pointer">
                                                    ✓ Confirm
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    onclick="openStaffRejectModal({{ $order->id }}, '{{ $order->order_number }}')" 
                                                    class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-2.5 py-1 font-recoleta text-xs font-black text-white shadow-[2px_2px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer">
                                                ✕ Reject
                                            </button>
                                        </div>
                                    @elseif($nextStatus)
                                        @if($nextStatus === 'claimed')
                                            @if($balanceDue > 0)
                                                <button type="button" 
                                                        class="open-claim-modal-btn retro-btn-primary whitespace-nowrap px-3 py-1.5 text-xs font-bold shadow-[2px_2px_0px_#182830] cursor-pointer"
                                                        data-order-id="{{ $order->id }}"
                                                        data-order-number="{{ $order->order_number ?? 'TL-'.$order->id }}"
                                                        data-customer="{{ $order->customer?->name ?? $order->customer_name }}"
                                                        data-balance="{{ $balanceDue }}"
                                                        data-total="{{ (float) $order->total_price }}"
                                                        data-paid="{{ (float) $order->amount_paid }}"
                                                        data-redirect-to="orders.index">
                                                    <span>Pay (₱{{ number_format($balanceDue, 2) }}) ➔</span>
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('orders.status.update', $order) }}" data-confirm="Mark Order #{{ $order->order_number }} as claimed and completed?" data-confirm-title="Confirm Order Claim" data-confirm-type="check" data-confirm-btn="Yes, Mark as Done">
                                                    @csrf
                                                    <input type="hidden" name="status" value="claimed">
                                                    <input type="hidden" name="redirect_to" value="orders.index">
                                                    <button type="submit" class="retro-btn-primary bg-emerald-700 hover:bg-emerald-800 whitespace-nowrap px-3.5 py-1.5 text-xs font-bold shadow-[2px_2px_0px_#182830] cursor-pointer">
                                                        <span>Done</span>
                                                        <span aria-hidden="true">➔</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('orders.status.update', $order) }}" data-confirm="Advance Order #{{ $order->order_number }} to '{{ $statusLabels[$nextStatus] ?? ucfirst($nextStatus) }}' stage?" data-confirm-title="Advance Order Status" data-confirm-type="primary" data-confirm-btn="Yes, Advance Order">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                <input type="hidden" name="redirect_to" value="orders.index">
                                                <button type="submit" class="retro-btn-primary whitespace-nowrap px-3 py-1.5 text-xs font-bold shadow-[2px_2px_0px_#182830]">
                                                    <span>Move to {{ $statusLabels[$nextStatus] ?? ucfirst($nextStatus) }}</span>
                                                    <span aria-hidden="true">→</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="font-mono text-[11px] font-bold text-[#25799B] hover:text-[#CB1B03] hover:underline">
                                            Inspect ➔
                                        </a>

                                        @if(in_array('cancelled', $availableStatuses, true))
                                            <form method="POST" action="{{ route('orders.status.update', $order) }}" data-confirm="Are you sure you want to cancel Order #{{ $order->order_number }}? This will stop laundry processing and mark the ticket as cancelled." data-confirm-title="Cancel Laundry Ticket" data-confirm-type="danger" data-confirm-btn="Yes, Cancel Ticket">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <input type="hidden" name="redirect_to" value="orders.index">
                                                <button type="submit" class="font-mono text-[10px] font-bold text-red-500 hover:underline">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[3px_3px_0px_#182830]">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                                    </svg>
                                </div>
                                <h3 class="mt-3 font-recoleta text-lg font-bold text-[#182830]">No matching laundry orders found</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Try clearing your search query or selecting a different stage tab above.</p>
                                <div class="mt-4">
                                    <button type="button" data-modal-open="order-modal" class="retro-btn-primary text-xs inline-flex items-center gap-2">
                                        <span>＋</span>
                                        <span>Create New Intake Order</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Retro Pagination Bar -->
        @include('partials.retro_pagination', ['paginator' => $orders])
    </div>

</div>

<!-- Quick Pay Floating Settlement Dialog -->
<div id="quick-pay-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/75 p-4 backdrop-blur-xs">
    <div class="w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[8px_8px_0px_#182830]">
        <div class="flex items-center justify-between border-b-2 border-[#182830] pb-3 mb-4">
            <div>
                <h3 class="font-recoleta text-xl font-black text-[#182830]">Quick Settle Payment</h3>
                <p id="qp-ticket-subtitle" class="font-mono text-xs text-[#25799B]">Ticket #</p>
            </div>
            <button type="button" id="qp-modal-close" class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-lg font-bold text-[#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer">×</button>
        </div>

        <form method="POST" id="quick-pay-form" class="space-y-4">
            @csrf
            <div class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-3 font-mono text-xs">
                <div class="flex justify-between py-0.5">
                    <span class="text-slate-600">Customer:</span>
                    <strong id="qp-customer-name" class="text-[#182830]">--</strong>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="text-slate-600">Remaining Balance:</span>
                    <strong id="qp-balance-due" class="text-[#CB1B03] font-bold">₱0.00</strong>
                </div>
            </div>

            <div>
                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Amount to Tender (₱) <span class="text-[#CB1B03]">*</span>
                </label>
                <input type="number" step="0.01" min="0.01" name="amount" id="qp-amount-input" required class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-lg font-bold text-[#182830] shadow-[2px_2px_0px_#182830] focus:outline-none focus:border-[#25799B]">
            </div>

            <div>
                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Payment Method <span class="text-[#CB1B03]">*</span>
                </label>
                <select name="payment_method" id="qp-method-select" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none focus:border-[#25799B]">
                    <option value="cash">Cash Settlement</option>
                    <option value="gcash">GCash E-Wallet</option>
                    <option value="other">Other / Bank Transfer</option>
                </select>
            </div>

            <div id="qp-ref-container" class="hidden">
                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Reference / Transaction #
                </label>
                <input type="text" name="reference_number" id="qp-ref-input" placeholder="Ref #" class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-2 font-mono text-xs text-[#182830] shadow-[1px_1px_0px_#182830] focus:outline-none">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="qp-cancel-btn" class="retro-btn-secondary text-xs">Cancel</button>
                <button type="submit" class="retro-btn-primary text-xs">Record Receipt ➔</button>
            </div>
        </form>
    </div>
</div>

<!-- Fast Laundry Intake Modal Partial -->
@include('partials.order_intake_modal', ['customers' => $customers, 'services' => $services])

<!-- Claim Release & Settlement Payment Window Modal Partial -->
@include('partials.claim_payment_modal')

<!-- Staff Reject Online Request Modal -->
<div id="staff-reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/80 p-4 backdrop-blur-xs">
    <div class="relative w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] shadow-[8px_8px_0px_#182830] p-6">
        <div class="flex items-center justify-between border-b-2 border-[#182830]/15 pb-3 mb-4">
            <div>
                <h3 class="font-recoleta text-lg font-black text-[#CB1B03]">Reject Online Request</h3>
                <p class="font-mono text-xs text-slate-500">Ticket #<strong id="staff-reject-order-num" class="text-[#182830]"></strong></p>
            </div>
            <button type="button" onclick="closeStaffRejectModal()" class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-sm font-black hover:bg-[#CB1B03] hover:text-white transition">
                ✕
            </button>
        </div>

        <form method="POST" id="staff-reject-form" class="space-y-4">
            @csrf
            <div>
                <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Select Rejection Reason <span class="text-[#CB1B03]">*</span>
                </label>
                <select name="rejection_reason" class="field text-xs font-medium mb-3">
                    <option value="All 8 washing machines currently booked to maximum capacity">All 8 washing machines currently booked to maximum capacity</option>
                    <option value="Outside operating hours / Store closing">Outside operating hours / Store closing</option>
                    <option value="Garment types require dry cleaning not supported in commercial drums">Garment types require dry cleaning not supported in commercial drums</option>
                    <option value="Customer unreachable or outside service radius">Customer unreachable or outside service radius</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeStaffRejectModal()" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-slate-700 shadow-[1px_1px_0px_#182830] hover:bg-slate-100">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-4 py-2 font-recoleta text-xs font-black text-white shadow-[2px_2px_0px_#182830] hover:bg-[#B51702]">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
window.openStaffRejectModal = function(orderId, orderNum) {
    const modal = document.getElementById('staff-reject-modal');
    const numEl = document.getElementById('staff-reject-order-num');
    const form = document.getElementById('staff-reject-form');
    if (numEl) numEl.textContent = orderNum;
    if (form) form.action = `/orders/${orderId}/reject-online`;
    modal?.classList.remove('hidden');
    modal?.classList.add('flex');
};

window.closeStaffRejectModal = function() {
    const modal = document.getElementById('staff-reject-modal');
    modal?.classList.add('hidden');
    modal?.classList.remove('flex');
};

document.addEventListener('DOMContentLoaded', () => {
    // Quick Pay Modal Logic
    const qpModal = document.getElementById('quick-pay-modal');
    const qpForm = document.getElementById('quick-pay-form');
    const qpSubtitle = document.getElementById('qp-ticket-subtitle');
    const qpCustomer = document.getElementById('qp-customer-name');
    const qpBalance = document.getElementById('qp-balance-due');
    const qpAmountInput = document.getElementById('qp-amount-input');
    const qpMethodSelect = document.getElementById('qp-method-select');
    const qpRefContainer = document.getElementById('qp-ref-container');

    document.querySelectorAll('.open-quick-pay-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const orderId = btn.dataset.orderId;
            const orderNumber = btn.dataset.orderNumber;
            const customer = btn.dataset.customer;
            const balance = parseFloat(btn.dataset.balance) || 0;

            if (qpForm) qpForm.action = `/orders/${orderId}/payments`;
            if (qpSubtitle) qpSubtitle.textContent = `Order ${orderNumber}`;
            if (qpCustomer) qpCustomer.textContent = customer;
            if (qpBalance) qpBalance.textContent = `₱${balance.toFixed(2)}`;
            if (qpAmountInput) {
                qpAmountInput.value = balance.toFixed(2);
                qpAmountInput.max = balance.toFixed(2);
            }

            qpModal?.classList.replace('hidden', 'flex');
        });
    });

    const closeQp = () => qpModal?.classList.replace('flex', 'hidden');
    document.getElementById('qp-modal-close')?.addEventListener('click', closeQp);
    document.getElementById('qp-cancel-btn')?.addEventListener('click', closeQp);

    qpMethodSelect?.addEventListener('change', function() {
        if (this.value === 'gcash' || this.value === 'other') {
            qpRefContainer?.classList.remove('hidden');
        } else {
            qpRefContainer?.classList.add('hidden');
        }
    });

    // Auto-open modal if URL has open_intake=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_intake') === '1') {
        const orderModal = document.getElementById('order-modal');
        if (orderModal) {
            orderModal.classList.remove('hidden');
            orderModal.classList.add('flex');
        }
    }

    // Keyboard Shortcut Alt+N for New Intake
    document.addEventListener('keydown', (e) => {
        if (e.altKey && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            const orderModal = document.getElementById('order-modal');
            if (orderModal) {
                orderModal.classList.remove('hidden');
                orderModal.classList.add('flex');
                document.getElementById('wizard-customer-name')?.focus();
            }
        }
    });
});
</script>
@endpush

@endsection
