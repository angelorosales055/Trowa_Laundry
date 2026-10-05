@extends('layouts_app')

@section('content')
@php
    $module = $module ?? 'status';
    $statusLabels = [
        'received' => 'Received',
        'washing' => 'Washing',
        'drying' => 'Drying',
        'ironing' => 'Ironing',
        'folding' => 'Folding',
        'ready_for_pickup' => 'Ready for Pickup',
        'claimed' => 'Claimed',
        'cancelled' => 'Cancelled',
    ];

    // Commercial equipment specs for the 16 showroom units
    $washerSpecs = [
        1 => ['specs' => '8 kg Speed Queen Pro', 'cycle' => 'Cold Eco Cycle', 'time' => '~18m left', 'water' => 'Cold', 'rpm' => '800 RPM'],
        2 => ['specs' => '8 kg Huebsch Pro', 'cycle' => 'Heavy Duty Wash', 'time' => '~25m left', 'water' => 'Warm', 'rpm' => '1000 RPM'],
        3 => ['specs' => '10 kg Primus Drum', 'cycle' => 'Warm Cotton Wash', 'time' => '~12m left', 'water' => 'Warm', 'rpm' => '900 RPM'],
        4 => ['specs' => '8 kg Speed Queen Pro', 'cycle' => 'Delicate Spin', 'time' => '~30m left', 'water' => 'Cold', 'rpm' => '600 RPM'],
        5 => ['specs' => '10 kg Industrial Max', 'cycle' => 'Express Wash 20m', 'time' => '~8m left', 'water' => 'Hot', 'rpm' => '1200 RPM'],
        6 => ['specs' => '8 kg Huebsch Pro', 'cycle' => 'Deep Sanitizing Wash', 'time' => '~22m left', 'water' => 'Hot 60°C', 'rpm' => '1000 RPM'],
        7 => ['specs' => '10 kg Primus Drum', 'cycle' => 'Allergen 60°C Cycle', 'time' => '~35m left', 'water' => 'Hot 60°C', 'rpm' => '1100 RPM'],
        8 => ['specs' => '8 kg Commercial Drum', 'cycle' => 'Active Gentle Spin', 'time' => '~15m left', 'water' => 'Cold', 'rpm' => '750 RPM'],
    ];

    $dryerSpecs = [
        1 => ['specs' => '10 kg Commercial Gas', 'cycle' => '40m Tumbler', 'time' => '~14m left', 'temp' => 'Medium', 'type' => 'Gas Flow'],
        2 => ['specs' => '10 kg Huebsch Tumbler', 'cycle' => 'Medium Temp Tumbler', 'time' => '~22m left', 'temp' => 'Medium', 'type' => 'Gas Flow'],
        3 => ['specs' => '12 kg Speed Queen Gas', 'cycle' => 'High Heat Fast Dry', 'time' => '~10m left', 'temp' => 'High', 'type' => 'Dual Burner'],
        4 => ['specs' => '10 kg Primus Gas', 'cycle' => 'Air Fluff Soft Tumbler', 'time' => '~18m left', 'temp' => 'Low Air', 'type' => 'Gentle Flow'],
        5 => ['specs' => '10 kg Commercial Gas', 'cycle' => 'Low Temp Delicate Dry', 'time' => '~28m left', 'temp' => 'Low', 'type' => 'Gas Flow'],
        6 => ['specs' => '12 kg Speed Queen Gas', 'cycle' => 'Heavy Blanket Tumbler', 'time' => '~16m left', 'temp' => 'High', 'type' => 'Dual Burner'],
        7 => ['specs' => '10 kg Huebsch Tumbler', 'cycle' => 'Full Bedding Cycle', 'time' => '~32m left', 'temp' => 'Medium', 'type' => 'Gas Flow'],
        8 => ['specs' => '10 kg Commercial Gas', 'cycle' => 'Express Quick Tumbler', 'time' => '~8m left', 'temp' => 'High', 'type' => 'Fast Heat'],
    ];

    $washingList = isset($activeWashingOrders) ? $activeWashingOrders : $orders->filter(fn($o) => in_array($o->status, ['washing', 'in-progress'], true))->values();
    $dryingList = isset($activeDryingOrders) ? $activeDryingOrders : $orders->filter(fn($o) => $o->status === 'drying')->values();
@endphp

<div class="space-y-6">

    <!-- Top Station Header & Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="2" width="18" height="20" rx="3"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 1 3 3"/><circle cx="7" cy="6" r="1" fill="currentColor"/><circle cx="10" cy="6" r="1" fill="currentColor"/>
                </svg>
                <span>Live Hardware Bay · 16 Commercial Units Floor Showroom</span>
            </div>
            <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
                Machine Bay & Floor Showroom
            </h1>
            <p class="text-xs sm:text-sm font-medium text-[#25799B]">
                Real-time motor spin monitors, active basket allocation, temperature tumblers, and load staging.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('orders.index') }}" class="retro-btn-secondary flex items-center gap-2 text-xs">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                <span>Open Laundry Queue ➔</span>
            </a>
            <button type="button" data-modal-open="order-modal" class="retro-btn-primary flex items-center gap-2 text-xs shadow-[3px_3px_0px_#182830]">
                <span class="text-base font-bold">＋</span>
                <span class="font-recoleta font-bold">New Laundry Intake</span>
            </button>
        </div>
    </div>

    <!-- Commercial Floor Health & Utilization Pulse -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1: Active Machine Load -->
        <div class="retro-panel p-4 flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#25799B] text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5 animate-spin" style="animation-duration: 4s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Washing Drums</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">{{ $washingList->count() }} / 8</p>
                <p class="font-mono text-[11px] text-[#25799B]">Commercial water spinning</p>
            </div>
        </div>

        <!-- 2: Gas Tumblers Active -->
        <div class="retro-panel p-4 flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#CB1B03]">Gas Tumblers</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">{{ $dryingList->count() }} / 8</p>
                <p class="font-mono text-[11px] text-[#CB1B03]">Thermal heat airflow</p>
            </div>
        </div>

        <!-- 3: Floor Utilization -->
        <div class="retro-panel p-4 flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Floor Utilization</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">
                    {{ round((($washingList->count() + $dryingList->count()) / 16) * 100) }}%
                </p>
                <p class="font-mono text-[11px] text-[#25799B]">{{ $washingList->count() + $dryingList->count() }} of 16 running</p>
            </div>
        </div>

        <!-- 4: Machine Fleet Capacity -->
        <div class="retro-panel p-4 flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#182830]">Shop Machine Fleet</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">16 Units</p>
                <p class="font-mono text-[11px] text-slate-600">8 Washers + 8 Dryers</p>
            </div>
        </div>
    </div>

    <!-- THE 16-UNIT LIVE COMMERCIAL SHOWROOM FLOOR -->
    <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[5px_5px_0px_#25799B]">
        <!-- Showroom Rack Controls Bar -->
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b-2 border-[#182830]/15 pb-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#25799B] text-[#FFFDF8] shadow-[1px_1px_0px_#182830]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="2" width="18" height="20" rx="3"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 1 3 3"/></svg>
                </div>
                <div>
                    <h2 class="font-recoleta text-xl font-bold text-[#182830]">Live Commercial Floor Showroom</h2>
                    <p class="text-xs text-[#25799B] font-medium">Real-time status of 8 washing drums and 8 commercial gas tumblers</p>
                </div>
            </div>

            <!-- View Switcher Pills -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-1 shadow-[2px_2px_0px_#182830]">
                    <button type="button" class="bay-filter-tab rounded-lg px-3 py-1 font-mono text-xs font-bold transition cursor-pointer bg-[#182830] text-[#FFFDF8]" data-target="all">
                        All Units (16)
                    </button>
                    <button type="button" class="bay-filter-tab rounded-lg px-3 py-1 font-mono text-xs font-bold transition cursor-pointer text-[#182830] hover:bg-[#F7E6CB]" data-target="washers">
                        8 Washers ({{ $washingList->count() }} Spinning)
                    </button>
                    <button type="button" class="bay-filter-tab rounded-lg px-3 py-1 font-mono text-xs font-bold transition cursor-pointer text-[#182830] hover:bg-[#F7E6CB]" data-target="dryers">
                        8 Dryers ({{ $dryingList->count() }} Tumbling)
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. WASHING STATION RACK (8 Washers) -->
        <div id="washers-bay-rack" class="bay-rack space-y-3 mb-6">
            <div class="flex items-center justify-between border-b border-[#25799B]/30 pb-2">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-[#25799B]"></span>
                    <h3 class="font-recoleta text-base font-bold text-[#182830]">Commercial Washing Bay (8 Heavy-Duty Washers)</h3>
                </div>
                <span class="font-mono text-xs font-bold text-[#25799B] bg-[#A2C5D8]/20 px-2.5 py-0.5 rounded-md border border-[#25799B]/30">
                    {{ $washingList->count() }} of 8 active drums
                </span>
            </div>

            <div class="grid gap-3.5 sm:grid-cols-2 xl:grid-cols-4">
                @for($i = 1; $i <= 8; $i++)
                    @php
                        $wOrder = $washingList->get($i - 1);
                        $spec = $washerSpecs[$i];
                        $numStr = sprintf('%02d', $i);
                    @endphp
                    <div class="group relative flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $wOrder ? 'bg-[#A2C5D8]/20 border-[#25799B]' : 'bg-[#FFFDF8]' }} p-3.5 shadow-[2px_2px_0px_#182830] transition hover:-translate-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-extrabold uppercase text-[#25799B]">Washer #{{ $numStr }}</span>
                            @if($wOrder)
                                <span class="inline-flex items-center gap-1 rounded-full border border-[#25799B] bg-[#25799B] px-2 py-0.5 font-mono text-[10px] font-bold text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-ping"></span>
                                    Washing
                                </span>
                            @else
                                <span class="rounded-full border border-slate-300 bg-slate-100 px-2 py-0.5 font-mono text-[10px] font-bold text-slate-500">
                                    Ready
                                </span>
                            @endif
                        </div>

                        <!-- Drum graphic & live details -->
                        <div class="my-3 flex items-center gap-3">
                            <div class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] {{ $wOrder ? 'bg-[#25799B]' : 'bg-[#A2C5D8]/30' }} shadow-[1px_1px_0px_#182830]">
                                <div class="relative flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#182830] {{ $wOrder ? 'bg-[#FFFDF8] animate-wash-spin' : 'bg-white' }}">
                                    @if($wOrder)
                                        <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M8 12c1.5 2 4 2 5.5 0s4-2 5.5 0"/>
                                            <circle cx="12" cy="12" r="2" fill="#25799B"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="8"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                @if($wOrder)
                                    <p class="truncate font-mono text-xs font-bold text-[#CB1B03]">{{ $wOrder->order_number ?? 'TL-'.$wOrder->id }}</p>
                                    <p class="truncate text-xs font-bold text-[#182830]">{{ $wOrder->customer?->name ?? $wOrder->customer_name }}</p>
                                    <p class="text-[11px] text-[#25799B] font-mono">{{ $wOrder->weight_kg }} kg · {{ $spec['water'] }} · {{ $spec['rpm'] }}</p>
                                @else
                                    <p class="text-xs font-bold text-slate-700">Drum Ready & Sanitized</p>
                                    <p class="text-[11px] text-slate-500 font-mono">{{ $spec['specs'] }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2 border-t border-[#182830]/10 flex items-center justify-between text-xs">
                            <span class="font-mono text-[11px] text-[#25799B] font-bold">{{ $wOrder ? 'Cycle: ' . $spec['time'] : 'Idle' }}</span>
                            @if($wOrder)
                                <a href="{{ route('orders.show', $wOrder) }}" class="font-mono text-[11px] font-bold text-[#25799B] hover:underline">Inspect ➔</a>
                            @else
                                <button type="button" data-modal-open="order-modal" class="font-mono text-[11px] font-bold text-[#25799B] hover:underline cursor-pointer">＋ Load Washer</button>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- 2. DRYING STATION RACK (8 Dryers) -->
        <div id="dryers-bay-rack" class="bay-rack space-y-3">
            <div class="flex items-center justify-between border-b border-[#CB1B03]/30 pb-2">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-[#CB1B03]"></span>
                    <h3 class="font-recoleta text-base font-bold text-[#182830]">Commercial Gas Drying Tumblers (8 Units)</h3>
                </div>
                <span class="font-mono text-xs font-bold text-[#CB1B03] bg-[#CB1B03]/10 px-2.5 py-0.5 rounded-md border border-[#CB1B03]/30">
                    {{ $dryingList->count() }} of 8 active tumblers
                </span>
            </div>

            <div class="grid gap-3.5 sm:grid-cols-2 xl:grid-cols-4">
                @for($i = 1; $i <= 8; $i++)
                    @php
                        $dOrder = $dryingList->get($i - 1);
                        $spec = $dryerSpecs[$i];
                        $numStr = sprintf('%02d', $i);
                    @endphp
                    <div class="group relative flex flex-col justify-between rounded-xl border-2 border-[#182830] {{ $dOrder ? 'bg-[#CB1B03]/10 border-[#CB1B03]' : 'bg-[#FFFDF8]' }} p-3.5 shadow-[2px_2px_0px_#182830] transition hover:-translate-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-extrabold uppercase text-[#CB1B03]">Dryer #{{ $numStr }}</span>
                            @if($dOrder)
                                <span class="inline-flex items-center gap-1 rounded-full border border-[#CB1B03] bg-[#CB1B03] px-2 py-0.5 font-mono text-[10px] font-bold text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-ping"></span>
                                    Tumbling
                                </span>
                            @else
                                <span class="rounded-full border border-slate-300 bg-slate-100 px-2 py-0.5 font-mono text-[10px] font-bold text-slate-500">
                                    Ready
                                </span>
                            @endif
                        </div>

                        <!-- Tumbler graphic & live details -->
                        <div class="my-3 flex items-center gap-3">
                            <div class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] {{ $dOrder ? 'bg-[#CB1B03]' : 'bg-red-50' }} shadow-[1px_1px_0px_#182830]">
                                <div class="relative flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#182830] {{ $dOrder ? 'bg-[#FFFDF8] animate-wash-spin-fast' : 'bg-white' }}">
                                    @if($dOrder)
                                        <svg class="h-6 w-6 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 7v10M7 12h10"/>
                                            <circle cx="12" cy="12" r="3" fill="#CB1B03"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="8"/>
                                            <line x1="8" y1="12" x2="16" y2="12"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                @if($dOrder)
                                    <p class="truncate font-mono text-xs font-bold text-[#CB1B03]">{{ $dOrder->order_number ?? 'TL-'.$dOrder->id }}</p>
                                    <p class="truncate text-xs font-bold text-[#182830]">{{ $dOrder->customer?->name ?? $dOrder->customer_name }}</p>
                                    <p class="text-[11px] text-[#25799B] font-mono">{{ $dOrder->weight_kg }} kg · {{ $spec['temp'] }} Heat</p>
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
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Machine Bay Filter Tabs (All / 8 Washers / 8 Dryers)
    const bayFilterTabs = document.querySelectorAll('.bay-filter-tab');
    const washersBayRack = document.getElementById('washers-bay-rack');
    const dryersBayRack = document.getElementById('dryers-bay-rack');

    bayFilterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            bayFilterTabs.forEach(t => {
                t.classList.remove('bg-[#182830]', 'text-[#FFFDF8]');
                t.classList.add('text-[#182830]');
            });
            tab.classList.add('bg-[#182830]', 'text-[#FFFDF8]');
            tab.classList.remove('text-[#182830]');

            const target = tab.dataset.target;
            if (target === 'all') {
                if (washersBayRack) washersBayRack.style.display = '';
                if (dryersBayRack) dryersBayRack.style.display = '';
            } else if (target === 'washers') {
                if (washersBayRack) washersBayRack.style.display = '';
                if (dryersBayRack) dryersBayRack.style.display = 'none';
            } else if (target === 'dryers') {
                if (washersBayRack) washersBayRack.style.display = 'none';
                if (dryersBayRack) dryersBayRack.style.display = '';
            }
        });
    });
</script>
@endpush
@endsection
