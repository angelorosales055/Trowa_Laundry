@extends('layouts_app')

@section('content')
<!-- Staff Station Operational Deck -->
<div class="space-y-6">

    <!-- Top Station Header & Live Clock Bar -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-[#25799B]">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-[#CB1B03] animate-pulse"></span>
                <span>Station Desk · Counter A · {{ now()->format('l, F j, Y') }}</span>
            </div>
            <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
                Good day, {{ $user->name }}
            </h1>
            <p class="text-sm font-medium text-[#25799B]">
                Here is your live laundry processing line. All machines, intake queues, and customer pickups in one place.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Real-time Ticking Retro Clock -->
            <div class="flex items-center gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <div class="font-mono text-sm font-bold tracking-tight" id="live-station-clock">
                    {{ now()->format('h:i:s A') }}
                </div>
            </div>

            <!-- Quick Action: Fast Order Intake Modal Trigger -->
            <button type="button" data-modal-open="order-modal" class="retro-btn-primary flex items-center gap-2 text-sm">
                <span class="text-base font-bold">＋</span>
                <span class="font-recoleta">Fast Laundry Intake</span>
                <kbd class="hidden sm:inline-block rounded border border-white/40 bg-black/20 px-1.5 py-0.5 text-[10px] font-mono">Alt+N</kbd>
            </button>
        </div>
    </div>

    <!-- Lively Moving Marquee Information Strip (Clean Natural SVG Badges) -->
    <div class="overflow-hidden rounded-xl border-2 border-[#182830] bg-[#A2C5D8] shadow-[3px_3px_0px_#182830]">
        <div class="flex items-center">
            <div class="shrink-0 flex items-center gap-1.5 border-r-2 border-[#182830] bg-[#CB1B03] px-3.5 py-2 font-mono text-xs font-extrabold uppercase text-[#FFFDF8]">
                <span class="h-2 w-2 rounded-full bg-white animate-ping"></span>
                <span>Live Pulse</span>
            </div>
            <div class="marquee-container flex-1 py-1.5 px-3 text-xs font-bold text-[#182830]">
                <div class="marquee-content flex items-center gap-8">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10l2 11h12l2-11H4z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                        <strong>Active Queue:</strong> {{ $stats['pending'] + $stats['in-progress'] }} order(s) in progress
                    </span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="12" cy="13" r="4"/></svg>
                        <strong>Washer Bay:</strong> Machines running standard & heavy cycles
                    </span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        <strong>Ready for Pickup:</strong> {{ $stats['ready'] }} order(s) bagged and labeled
                    </span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 4c0 3 4 3 4 6s-4 3-4 6 4 3 4 6"/><path d="M16 4c0 3 4 3 4 6s-4 3-4 6 4 3 4 6"/></svg>
                        <strong>Dryer Capacity:</strong> Commercial gas tumblers operating
                    </span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="6" x2="12" y2="18"/></svg>
                        <strong>Today's Weight:</strong> {{ number_format($stats['total_kg'], 1) }} kg ({{ $stats['total_loads'] }} machine loads)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alive Laundry Machine Bay Visualizer (Sleek 2 Washers & 2 Dryers Quick Glance -> Full 16-Unit Showroom) -->
    @php
        $washingOrders = $orders->filter(fn($o) => in_array($o->status, ['washing', 'in-progress'], true))->values();
        $dryingOrders = $orders->filter(fn($o) => $o->status === 'drying')->values();

        $washerSpecs = [
            1 => ['specs' => '8 kg Speed Queen', 'cycle' => 'Cold Eco Cycle', 'time' => '~18m left'],
            2 => ['specs' => '8 kg Huebsch Pro', 'cycle' => 'Heavy Duty Wash', 'time' => '~25m left'],
        ];

        $dryerSpecs = [
            1 => ['specs' => '10 kg Commercial Gas', 'cycle' => '40m Tumbler', 'time' => '~14m left'],
            2 => ['specs' => '10 kg Huebsch Tumbler', 'cycle' => 'Medium Temp Tumbler', 'time' => '~22m left'],
        ];
    @endphp
    <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-5 shadow-[4px_4px_0px_#25799B]">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 border-b-2 border-[#182830]/15 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#25799B] text-[#FFFDF8] shadow-[1px_1px_0px_#182830]">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0-.33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </div>
                <div>
                    <h2 class="font-recoleta text-lg font-bold text-[#182830]">Live Machine Bay & Washer Rack</h2>
                    <p class="text-xs text-[#25799B] font-medium">Station preview glance · 2 active washers & 2 dryers</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full border border-[#182830] bg-[#F7E6CB] px-2.5 py-1 text-[11px] font-mono font-bold text-[#182830] shrink-0">
                    Floor: {{ $washingOrders->count() + $dryingOrders->count() }} / 16 In Use
                </span>
                <a href="{{ route('schedule.index') }}" class="retro-btn-secondary flex items-center gap-1.5 text-xs font-bold py-1 px-3 shadow-[2px_2px_0px_#182830]">
                    <span>Machine Showroom (16 Units)</span>
                    <span aria-hidden="true">➔</span>
                </a>
            </div>
        </div>

        <!-- 4-Unit Interactive Bay Quick Rack (2 Washers & 2 Dryers) -->
        <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Washer #01 -->
            @for($i = 1; $i <= 2; $i++)
                @php
                    $wOrder = $washingOrders->get($i - 1);
                    $spec = $washerSpecs[$i];
                    $numStr = sprintf('%02d', $i);
                @endphp
                <div class="group relative flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $wOrder ? 'bg-[#A2C5D8]/20 border-[#25799B]' : 'bg-[#FFFDF8]' }} p-3.5 shadow-[2px_2px_0px_#182830] transition hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-extrabold uppercase text-[#25799B]">Washer #{{ $numStr }}</span>
                        <span class="badge {{ $wOrder ? 'border-[#25799B] bg-[#25799B] text-white' : 'border-slate-300 bg-slate-100 text-slate-600' }}">
                            {{ $wOrder ? 'Spinning' : 'Available' }}
                        </span>
                    </div>

                    <div class="my-3 flex items-center gap-3">
                        <div class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] {{ $wOrder ? 'bg-[#25799B]' : 'bg-[#F7E6CB]' }} shadow-[2px_2px_0px_#182830]">
                            <div class="h-10 w-10 rounded-full border-2 border-dashed {{ $wOrder ? 'border-white animate-wash-spin' : 'border-[#182830]/40' }} flex items-center justify-center">
                                @if($wOrder)
                                    <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3a9 9 0 0 1 9 9"/><path d="M12 12a4 4 0 0 1 4 4"/><path d="M12 21a9 9 0 0 1-9-9"/>
                                    </svg>
                                @else
                                    <div class="h-4 w-4 rounded-full border-2 border-[#182830]/40"></div>
                                @endif
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            @if($wOrder)
                                <p class="truncate font-mono text-xs font-bold text-[#CB1B03]">{{ $wOrder->order_number ?? 'TL-'.$wOrder->id }}</p>
                                <p class="truncate text-xs font-bold text-[#182830]">{{ $wOrder->customer?->name ?? $wOrder->customer_name }}</p>
                                <p class="text-[11px] text-[#25799B] font-mono">{{ $wOrder->weight_kg }} kg · {{ $spec['cycle'] }}</p>
                            @else
                                <p class="text-xs font-bold text-slate-700">Drum Clean & Ready</p>
                                <p class="text-[11px] text-slate-500 font-mono">{{ $spec['specs'] }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#182830]/10 flex items-center justify-between text-xs">
                        <span class="font-mono text-[11px] text-[#25799B] font-bold">{{ $wOrder ? 'Cycle: ' . $spec['time'] : 'Idle' }}</span>
                        @if($wOrder)
                            <a href="{{ route('orders.show', $wOrder) }}" class="font-mono text-[11px] font-bold text-[#25799B] hover:underline">Inspect ➔</a>
                        @else
                            <button type="button" data-modal-open="order-modal" class="font-mono text-[11px] font-bold text-[#CB1B03] hover:underline cursor-pointer">＋ Load Order</button>
                        @endif
                    </div>
                </div>
            @endfor

            <!-- Dryer #01 & Dryer #02 -->
            @for($i = 1; $i <= 2; $i++)
                @php
                    $dOrder = $dryingOrders->get($i - 1);
                    $spec = $dryerSpecs[$i];
                    $numStr = sprintf('%02d', $i);
                @endphp
                <div class="group relative flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $dOrder ? 'bg-[#CB1B03]/10 border-[#CB1B03]' : 'bg-[#FFFDF8]' }} p-3.5 shadow-[2px_2px_0px_#182830] transition hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-extrabold uppercase text-[#CB1B03]">Dryer #{{ $numStr }}</span>
                        <span class="badge {{ $dOrder ? 'border-[#CB1B03] bg-[#CB1B03] text-white animate-pulse' : 'border-slate-300 bg-slate-100 text-slate-600' }}">
                            {{ $dOrder ? 'Tumbling (Heat)' : 'Available' }}
                        </span>
                    </div>

                    <div class="my-3 flex items-center gap-3">
                        <div class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] {{ $dOrder ? 'bg-[#CB1B03]' : 'bg-[#F7E6CB]' }} shadow-[2px_2px_0px_#182830]">
                            <div class="h-10 w-10 rounded-full border-2 border-dashed {{ $dOrder ? 'border-white animate-wash-spin-fast' : 'border-[#182830]/40' }} flex items-center justify-center">
                                @if($dOrder)
                                    <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 4c0 3 4 3 4 6s-4 3-4 6 4 3 4 6"/><path d="M16 4c0 3 4 3 4 6s-4 3-4 6 4 3 4 6"/>
                                    </svg>
                                @else
                                    <div class="h-4 w-4 rounded-full border-2 border-[#182830]/40"></div>
                                @endif
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            @if($dOrder)
                                <p class="truncate font-mono text-xs font-bold text-[#CB1B03]">{{ $dOrder->order_number ?? 'TL-'.$dOrder->id }}</p>
                                <p class="truncate text-xs font-bold text-[#182830]">{{ $dOrder->customer?->name ?? $dOrder->customer_name }}</p>
                                <p class="text-[11px] text-[#25799B] font-mono">{{ $dOrder->weight_kg }} kg · {{ $spec['cycle'] }}</p>
                            @else
                                <p class="text-xs font-bold text-slate-700">Tumbler Cool & Ready</p>
                                <p class="text-[11px] text-slate-500 font-mono">{{ $spec['specs'] }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#182830]/10 flex items-center justify-between text-xs">
                        <span class="font-mono text-[11px] text-[#CB1B03] font-bold">{{ $dOrder ? 'Tumble: ' . $spec['time'] : 'Idle' }}</span>
                        @if($dOrder)
                            <a href="{{ route('orders.show', $dOrder) }}" class="font-mono text-[11px] font-bold text-[#CB1B03] hover:underline">Inspect ➔</a>
                        @else
                            <button type="button" data-modal-open="order-modal" class="font-mono text-[11px] font-bold text-[#CB1B03] hover:underline cursor-pointer">＋ Load Dryer</button>
                        @endif
                    </div>
                </div>
            @endfor
        </div>

        <div class="mt-4 pt-3 border-t border-[#182830]/15 flex items-center justify-between">
            <p class="font-mono text-xs text-[#25799B]">
                Showing 4 bays summary. <strong>12 more machines</strong> are operating on the commercial floor.
            </p>
            <a href="{{ route('schedule.index') }}" class="font-mono text-xs font-bold text-[#CB1B03] hover:underline flex items-center gap-1">
                <span>Open Floor Showroom</span>
                <span>➔</span>
            </a>
        </div>
    </div>

    <!-- Tactile Retro Metric Summary Cards (Clean Natural SVGs) -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- 1: Total Queue / In-Process -->
        <div class="retro-panel-interactive flex items-start gap-4 p-5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">Active In Queue</p>
                <p class="font-recoleta text-3xl font-extrabold text-[#182830] leading-none my-1">
                    {{ $stats['pending'] + $stats['in-progress'] }}
                </p>
                <p class="text-xs text-[#25799B] font-medium">
                    {{ $stats['washing'] }} in wash · {{ $stats['drying'] }} in dryer
                </p>
            </div>
        </div>

        <!-- 2: Awaiting Wash -->
        <div class="retro-panel-interactive flex items-start gap-4 p-5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">Awaiting Wash</p>
                <p class="font-recoleta text-3xl font-extrabold text-[#182830] leading-none my-1">
                    {{ $stats['pending'] }}
                </p>
                <p class="text-xs text-[#25799B] font-medium">
                    Received & staged at intake counter
                </p>
            </div>
        </div>

        <!-- 3: Ready for Customer Pickup -->
        <div class="retro-panel-interactive flex items-start gap-4 p-5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">Ready for Pickup</p>
                <p class="font-recoleta text-3xl font-extrabold text-[#182830] leading-none my-1">
                    {{ $stats['ready'] }}
                </p>
                <div class="flex items-center gap-1.5 text-xs">
                    @if($stats['unpaid_ready'] > 0)
                        <span class="font-bold text-[#CB1B03]">{{ $stats['unpaid_ready'] }} unpaid balance</span>
                    @else
                        <span class="font-medium text-[#25799B]">All stubs labeled & bagged</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4: Today's Shift Weight & Collections -->
        <div class="retro-panel-interactive flex items-start gap-4 p-5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#25799B] text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">Shift Collections</p>
                <p class="font-recoleta text-3xl font-extrabold text-[#182830] leading-none my-1">
                    ₱{{ number_format($stats['total_paid'], 2) }}
                </p>
                <p class="text-xs text-[#25799B] font-medium">
                    {{ number_format($stats['total_kg'], 1) }} kg processed ({{ $stats['total_loads'] }} loads)
                </p>
            </div>
        </div>
    </div>

    <!-- Active Orders Operations Board with Stage Tabs & Instant Filter -->
    <div class="retro-panel overflow-hidden">
        <!-- Header & Action Ribbon -->
        <div class="border-b-2 border-[#182830] bg-[#A2C5D8]/20 p-5">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="font-recoleta text-xl font-bold text-[#182830]">Staff Operations Board</h2>
                    <p class="text-xs text-[#25799B] font-medium">Real-time status of orders assigned to your station. Progress each order with 1-click.</p>
                </div>

                <!-- Instant Search Input (Clean SVG) -->
                <div class="flex items-center gap-2">
                    <div class="relative w-full sm:w-72">
                        <input type="text" id="order-filter-search" placeholder="Search customer, TL ticket #..." class="field pr-9 text-xs h-10">
                        <span class="absolute right-3 top-3 text-[#25799B]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                    </div>
                    <a href="{{ route('orders.index') }}" class="retro-btn-secondary whitespace-nowrap text-xs h-10">
                        Full Order List ➔
                    </a>
                </div>
            </div>

            <!-- Dynamic Stage Filter Tabs (Clean text & badges, no emojis) -->
            <div class="mt-4 flex flex-wrap gap-2 pt-2 border-t border-[#182830]/15" id="stage-filter-buttons">
                <button type="button" data-filter="all" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#182830] px-3.5 py-1.5 font-mono text-xs font-bold text-[#FFFDF8] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    All Orders ({{ $orders->count() }})
                </button>
                <button type="button" data-filter="received,pending" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    Queued ({{ $stats['pending'] }})
                </button>
                <button type="button" data-filter="washing,in-progress" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    Washing ({{ $stats['washing'] }})
                </button>
                <button type="button" data-filter="drying" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    Drying ({{ $stats['drying'] }})
                </button>
                <button type="button" data-filter="folding,ironing" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    Folding ({{ $stats['folding'] }})
                </button>
                <button type="button" data-filter="ready_for_pickup,ready" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    Ready ({{ $stats['ready'] }})
                </button>
                <button type="button" data-filter="claimed,delivered" class="stage-tab-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-1.5 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[2px_2px_0px_#182830] cursor-pointer">
                    Claimed ({{ $stats['claimed'] }})
                </button>
            </div>
        </div>

        <!-- Orders Grid with Interactive Cards -->
        <div class="p-5">
            @php
                $statusLabels = [
                    'received' => 'Received',
                    'washing' => 'Washing',
                    'drying' => 'Drying',
                    'ironing' => 'Ironing',
                    'folding' => 'Folding',
                    'ready_for_pickup' => 'Ready for Pickup',
                    'claimed' => 'Claimed',
                    'cancelled' => 'Cancelled'
                ];
            @endphp

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" id="orders-cards-container">
                @forelse($orders as $order)
                    @php
                        $availableStatuses = $order->nextStatuses();
                        $nextStatus = collect($availableStatuses)->first(fn ($s) => $s !== 'cancelled');
                        $isPaid = $order->payment_status === 'paid' || ($order->total_price > 0 && $order->amount_paid >= $order->total_price);
                        $balance = max(0, (float)$order->total_price - (float)$order->amount_paid);
                    @endphp

                    <div class="order-interactive-card group relative flex flex-col justify-between rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#182830] transition hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_#25799B]"
                         data-status="{{ strtolower($order->status) }}"
                         data-search="{{ strtolower(($order->order_number ?? 'TL-'.$order->id) . ' ' . ($order->customer?->name ?? $order->customer_name) . ' ' . ($order->customer?->contact_number ?? $order->customer?->phone ?? '')) }}">

                        <!-- Card Header -->
                        <div>
                            <div class="flex items-start justify-between gap-2 border-b border-[#182830]/15 pb-2.5">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 font-mono text-xs font-extrabold text-[#CB1B03]">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                        <a href="{{ route('orders.show', $order) }}" class="hover:underline">
                                            {{ $order->order_number ?? 'TL-'.$order->id }}
                                        </a>
                                    </span>
                                    <p class="font-recoleta text-base font-bold text-[#182830] leading-snug mt-0.5">
                                        {{ $order->customer?->name ?? $order->customer_name }}
                                    </p>
                                    @if($order->customer?->contact_number || $order->customer?->phone)
                                        <p class="font-mono text-[11px] text-[#25799B] font-semibold flex items-center gap-1 mt-0.5">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            <span>{{ $order->customer?->contact_number ?? $order->customer?->phone }}</span>
                                        </p>
                                    @endif
                                </div>

                                <!-- Current Status Stamp -->
                                <div class="text-right">
                                    <span class="badge border-[#182830] {{ match($order->status) {
                                        'washing', 'in-progress' => 'bg-[#25799B] text-white',
                                        'drying' => 'bg-[#CB1B03] text-white',
                                        'ready_for_pickup', 'ready' => 'bg-emerald-600 text-white',
                                        'claimed', 'delivered' => 'bg-slate-800 text-white',
                                        'cancelled' => 'bg-red-800 text-white',
                                        default => 'bg-[#A2C5D8] text-[#182830]',
                                    } }}">
                                        {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                    </span>
                                    <p class="text-[10px] text-slate-500 font-mono mt-1">
                                        {{ $order->created_at?->diffForHumans() ?? 'today' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Weight, Loads, & Services -->
                            <div class="my-3 space-y-2">
                                <div class="flex items-center justify-between text-xs font-mono font-bold bg-[#F7E6CB]/50 p-2 rounded-lg border border-[#182830]/10">
                                    <span class="text-[#182830] flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 3v18"/><path d="M6 7l6-4 6 4"/><path d="M4 14l4-7 4 7H4z"/><path d="M12 14l4-7 4 7h-8z"/></svg>
                                        {{ $order->weight_kg }} kg
                                    </span>
                                    <span class="text-[#25799B]">{{ $order->number_of_loads }} Load(s)</span>
                                    <span class="text-[#182830]">₱{{ number_format((float)$order->total_price, 2) }}</span>
                                </div>

                                @php
                                    $staffSvcList = collect(explode(',', (string) $order->services))->map(fn($s) => trim($s))->filter();
                                    $staffSvcCount = $staffSvcList->count() ?: 1;
                                    $staffSvcTier = match(true) {
                                        $staffSvcCount <= 2 => 'Basic Service',
                                        $staffSvcCount <= 4 => 'Deluxe Service',
                                        default => 'Premium Service',
                                    };
                                @endphp
                                <div class="flex items-center justify-between text-[11px] font-mono">
                                    <span class="font-bold text-[#25799B] bg-[#A2C5D8]/20 px-2 py-0.5 rounded border border-[#182830]/15">{{ $staffSvcTier }}</span>
                                    <span class="text-slate-500 truncate max-w-[150px]" title="{{ $staffSvcList->implode(', ') }}">{{ $staffSvcList->implode(' · ') ?: 'Standard Wash' }}</span>
                                </div>

                                <!-- Payment Status Pill -->
                                <div class="flex items-center justify-between text-xs pt-1">
                                    <span class="font-mono text-[11px] font-bold text-slate-600">Payment:</span>
                                    @if($isPaid)
                                        <div class="flex items-center gap-1.5">
                                            <span class="line-through font-mono text-[11px] text-slate-400">₱{{ number_format((float)$order->total_price, 2) }}</span>
                                            <span class="inline-flex items-center gap-0.5 font-mono text-[11px] font-black text-emerald-700">
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                Paid
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 font-mono text-[11px] font-extrabold text-[#CB1B03] bg-red-50 px-2 py-0.5 rounded border border-red-300">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                            BAL: ₱{{ number_format($balance, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions & Status Progression -->
                        <div class="border-t border-[#182830]/15 pt-3">
                            <div class="flex items-center gap-2">
                                @if($nextStatus)
                                    @if($nextStatus === 'claimed')
                                        @if($balance > 0)
                                            <button type="button" 
                                                    class="open-claim-modal-btn flex-1 flex items-center justify-center gap-1.5 rounded-lg border-2 border-[#182830] bg-[#CB1B03] px-3 py-1.5 font-sans text-xs font-bold text-[#FFFDF8] shadow-[2px_2px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer"
                                                    data-order-id="{{ $order->id }}"
                                                    data-order-number="{{ $order->order_number ?? 'TL-'.$order->id }}"
                                                    data-customer="{{ $order->customer?->name ?? $order->customer_name }}"
                                                    data-balance="{{ $balance }}"
                                                    data-total="{{ (float) $order->total_price }}"
                                                    data-paid="{{ (float) $order->amount_paid }}"
                                                    data-redirect-to="dashboard">
                                                <span>Pay (₱{{ number_format($balance, 2) }}) ➔</span>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('orders.status.update', $order) }}" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="status" value="claimed">
                                                <input type="hidden" name="redirect_to" value="dashboard">
                                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 rounded-lg border-2 border-[#182830] bg-emerald-700 px-3 py-1.5 font-sans text-xs font-bold text-[#FFFDF8] shadow-[2px_2px_0px_#182830] hover:bg-emerald-800 hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer">
                                                    <span>Done</span>
                                                    <span aria-hidden="true">➔</span>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('orders.status.update', $order) }}" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                                            <input type="hidden" name="redirect_to" value="dashboard">
                                            <button type="submit" class="w-full flex items-center justify-center gap-1.5 rounded-lg border-2 border-[#182830] bg-[#CB1B03] px-3 py-1.5 font-sans text-xs font-bold text-[#FFFDF8] shadow-[2px_2px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer">
                                                <span>Move to {{ $statusLabels[$nextStatus] ?? ucfirst($nextStatus) }}</span>
                                                <span aria-hidden="true">→</span>
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                <a href="{{ route('orders.show', $order) }}" class="rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-2.5 py-1.5 text-xs font-bold text-[#25799B] shadow-[2px_2px_0px_#182830] hover:bg-[#A2C5D8] transition">
                                    Details
                                </a>
                            </div>

                            @if(in_array('cancelled', $availableStatuses, true))
                                <form method="POST" action="{{ route('orders.status.update', $order) }}" onsubmit="return confirm('Cancel this order?');" class="mt-2 text-right">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="text-[10px] font-bold text-red-600 hover:underline">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border-2 border-dashed border-[#182830]/30 bg-[#FFFDF8] p-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[2px_2px_0px_#182830]">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                        </div>
                        <h3 class="mt-3 font-recoleta text-lg font-bold text-[#182830]">No laundry orders recorded yet</h3>
                        <p class="text-xs text-slate-500">Tap below to take your first customer order of the day.</p>
                        <button type="button" data-modal-open="order-modal" class="retro-btn-primary mt-4 text-xs">
                            ＋ Create New Intake
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Fast Laundry Intake Modal -->
@include('partials.order_intake_modal', ['customers' => $customers, 'services' => $services])

<!-- Claim Release & Settlement Payment Window Modal -->
@include('partials.claim_payment_modal')

@push('scripts')
<script>
    // Live ticking clock for staff desk
    function updateLiveClock() {
        const el = document.getElementById('live-station-clock');
        if (el) {
            const now = new Date();
            el.textContent = now.toLocaleTimeString('en-US', { hour12: true });
        }
    }
    setInterval(updateLiveClock, 1000);

    // Instant Search and Stage Tab Filtering
    const searchInput = document.getElementById('order-filter-search');
    const stageTabs = document.querySelectorAll('.stage-tab-btn');
    const orderCards = document.querySelectorAll('.order-interactive-card');
    let activeFilter = 'all';

    function filterCards() {
        const query = searchInput?.value.toLowerCase().trim() || '';
        orderCards.forEach(card => {
            const cardStatus = card.dataset.status || '';
            const cardSearch = card.dataset.search || '';

            const matchesStatus = (activeFilter === 'all') || 
                activeFilter.split(',').some(f => cardStatus === f.trim());
            const matchesQuery = !query || cardSearch.includes(query);

            if (matchesStatus && matchesQuery) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput?.addEventListener('input', filterCards);

    stageTabs.forEach(btn => {
        btn.addEventListener('click', () => {
            stageTabs.forEach(b => {
                b.classList.remove('bg-[#182830]', 'text-[#FFFDF8]');
                b.classList.add('bg-[#FFFDF8]', 'text-[#182830]');
            });
            btn.classList.add('bg-[#182830]', 'text-[#FFFDF8]');
            btn.classList.remove('bg-[#FFFDF8]', 'text-[#182830]');

            activeFilter = btn.dataset.filter || 'all';
            filterCards();
        });
    });
</script>
@endpush
@endsection
