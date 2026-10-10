@extends('layouts_app')

@section('content')
@php
    $totalPaid = (float) $order->amount_paid;
    $balance = max(0, (float) $order->total_price - $totalPaid);
    $labels = [
        'received' => 'Received',
        'washing' => 'Washing',
        'drying' => 'Drying',
        'ironing' => 'Ironing',
        'folding' => 'Folding',
        'ready_for_pickup' => 'Ready for Pickup',
        'claimed' => 'Claimed',
        'cancelled' => 'Cancelled'
    ];
    $availableStatuses = $order->nextStatuses();
    $nextStatus = collect($availableStatuses)->first(fn ($availableStatus) => $availableStatus !== 'cancelled');
@endphp

<!-- Header & Order Identifier -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold text-[#25799B]">
            <a href="{{ route('orders.index') }}" class="hover:text-[#CB1B03] hover:underline transition">
                ← Laundry Queue Line
            </a>
            <span>·</span>
            <a href="{{ route('schedule.index') }}" class="hover:text-[#CB1B03] hover:underline transition">
                Order Registry
            </a>
        </div>
        <div class="mt-2 flex items-center gap-3">
            <h1 class="font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
                {{ $order->order_number ?? 'TL-'.$order->id }}
            </h1>
            <span class="badge border-[#182830] {{ match($order->status) {
                'washing', 'in-progress' => 'bg-[#25799B] text-white',
                'drying' => 'bg-[#CB1B03] text-white',
                'ready_for_pickup', 'ready' => 'bg-emerald-600 text-white',
                'claimed', 'delivered' => 'bg-slate-800 text-white',
                'cancelled' => 'bg-red-800 text-white',
                default => 'bg-[#A2C5D8] text-[#182830]',
            } }}">
                {{ $labels[$order->status] ?? ucfirst($order->status) }}
            </span>
        </div>
        <p class="font-mono text-xs text-[#25799B] font-medium mt-1">
            Registered customer: <strong class="text-[#182830]">{{ $order->customer?->name ?? $order->customer_name }}</strong> · Created {{ $order->created_at?->format('M j, Y h:i A') }}
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 print:hidden">
        <button type="button" onclick="window.print()" class="retro-btn-secondary text-xs flex items-center gap-1.5 cursor-pointer">
            <svg class="h-3.5 w-3.5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Print Receipt Slip</span>
        </button>
        <a href="{{ route('orders.create') }}" class="retro-btn-primary text-xs flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>＋ New Intake Register</span>
        </a>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('orders.edit', $order) }}" class="retro-btn-secondary text-xs">
                Edit
            </a>
        @endif
        <a href="{{ route('dashboard') }}" class="retro-btn-secondary text-xs">
            Dashboard ➔
        </a>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Core Order Breakdown & Next Actions -->
    <div class="space-y-6 lg:col-span-2">
        <!-- Order Specifications Panel -->
        <div class="retro-panel p-6">
            <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-3">
                <h2 class="font-recoleta text-xl font-bold text-[#182830]">Order Specifications</h2>
                <span class="font-mono text-xs font-bold text-[#25799B]">Ticket #{{ $order->order_number ?? $order->id }}</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 bg-[#F7E6CB]/40 rounded-xl p-4 border border-[#182830]/10">
                <div>
                    <p class="font-mono text-[11px] font-bold uppercase text-[#25799B]">Customer Profile</p>
                    <p class="font-recoleta text-base font-bold text-[#182830] mt-0.5">{{ $order->customer?->name ?? $order->customer_name }}</p>
                    @if($order->customer?->contact_number ?? $order->customer?->phone)
                        <p class="font-mono text-xs text-[#25799B] flex items-center gap-1 mt-0.5">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>{{ $order->customer?->contact_number ?? $order->customer?->phone }}</span>
                        </p>
                    @endif
                </div>
                <div>
                    <p class="font-mono text-[11px] font-bold uppercase text-[#25799B]">Weight & Machine Capacity</p>
                    <p class="font-mono text-base font-extrabold text-[#182830] mt-0.5">{{ $order->weight_kg }} kg</p>
                    <p class="font-mono text-xs text-[#25799B]">{{ $order->number_of_loads }} commercial load(s)</p>
                </div>
                <div>
                    <p class="font-mono text-[11px] font-bold uppercase text-[#25799B]">Order Total</p>
                    <p class="font-mono text-base font-extrabold text-[#CB1B03] mt-0.5">₱{{ number_format((float) $order->total_price, 2) }}</p>
                    <p class="font-mono text-xs text-slate-500">Based on active price rates</p>
                </div>
            </div>

            <!-- Itemized Garment List -->
            <div class="mt-6 border-t-2 border-[#182830]/10 pt-4">
                <h3 class="font-recoleta text-base font-bold text-[#182830] mb-3">Itemized Garment Count</h3>
                <div class="space-y-2">
                    @forelse($order->itemDetails as $item)
                        <div class="flex items-center justify-between rounded-lg border border-[#182830]/15 bg-[#FFFDF8] px-3.5 py-2 font-mono text-xs font-bold shadow-[1px_1px_0px_#182830]">
                            <span class="text-[#182830]">{{ $item->item_name }}</span>
                            <span class="rounded bg-[#A2C5D8] px-2 py-0.5 text-[#182830]">{{ $item->quantity }} pc(s)</span>
                        </div>
                    @empty
                        <p class="font-mono text-xs text-slate-500 italic">No garment breakdown recorded for this intake.</p>
                    @endforelse
                </div>
            </div>

            <!-- Selected Services Lines -->
            <div class="mt-6 border-t-2 border-[#182830]/10 pt-4">
                <h3 class="font-recoleta text-base font-bold text-[#182830] mb-3">Service Charges Breakdown</h3>
                <div class="space-y-2">
                    @foreach($order->orderServices as $line)
                        <div class="flex items-center justify-between rounded-lg border border-[#182830]/15 bg-[#FFFDF8] px-3.5 py-2 text-xs shadow-[1px_1px_0px_#182830]">
                            <div>
                                <strong class="font-sans text-[#182830]">{{ $line->service?->name }}</strong>
                                <span class="font-mono text-[#25799B] ml-2">({{ $line->loads }} load(s))</span>
                            </div>
                            <span class="font-mono font-extrabold text-[#182830]">₱{{ number_format((float) $line->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Advance Stage Control -->
        @if($availableStatuses)
            <div class="retro-panel p-6">
                <h2 class="font-recoleta text-xl font-bold text-[#182830] mb-1">Advance Laundry Stage</h2>
                <p class="font-mono text-xs text-[#25799B] mb-4">Move clothes through machine washer, dryer, folding station, and pickup.</p>
                
                <div class="flex flex-wrap items-end gap-3">
                    @if($nextStatus)
                        @if($nextStatus === 'claimed')
                            @if($balance > 0)
                                <button type="button" 
                                        class="open-claim-modal-btn retro-btn-primary whitespace-nowrap text-xs cursor-pointer"
                                        data-order-id="{{ $order->id }}"
                                        data-order-number="{{ $order->order_number ?? 'TL-'.$order->id }}"
                                        data-customer="{{ $order->customer?->name ?? $order->customer_name }}"
                                        data-balance="{{ $balance }}"
                                        data-total="{{ (float) $order->total_price }}"
                                        data-paid="{{ $totalPaid }}"
                                        data-redirect-to="orders.show">
                                    <span>Pay (₱{{ number_format($balance, 2) }}) ➔</span>
                                </button>
                            @else
                                <form method="POST" action="{{ route('orders.status.update', $order) }}" data-confirm="Mark Order #{{ $order->order_number }} as claimed and completed?" data-confirm-title="Confirm Order Claim" data-confirm-type="check" data-confirm-btn="Yes, Mark as Done" class="flex flex-1 flex-wrap items-end gap-3">
                                    @csrf
                                    <input type="hidden" name="status" value="claimed">
                                    <input type="hidden" name="redirect_to" value="orders.show">
                                    <div class="min-w-48 flex-1">
                                        <label for="stage-notes-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Optional Handover Note</label>
                                        <input id="stage-notes-input" name="notes" class="field text-xs" placeholder="e.g. Released clean laundry to customer...">
                                    </div>
                                    <button type="submit" class="retro-btn-primary bg-emerald-700 hover:bg-emerald-800 whitespace-nowrap text-xs cursor-pointer">
                                        <span>Done</span>
                                        <span aria-hidden="true">➔</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <form method="POST" action="{{ route('orders.status.update', $order) }}" data-confirm="Advance Order #{{ $order->order_number }} to '{{ $labels[$nextStatus] }}' stage?" data-confirm-title="Advance Order Status" data-confirm-type="primary" data-confirm-btn="Yes, Advance Order" class="flex flex-1 flex-wrap items-end gap-3">
                                @csrf
                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                <div class="min-w-48 flex-1">
                                    <label for="stage-notes-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Optional Operator Note</label>
                                    <input id="stage-notes-input" name="notes" class="field text-xs" placeholder="e.g. Moved to Washer #02, cycle duration 35 mins...">
                                </div>
                                <button type="submit" class="retro-btn-primary whitespace-nowrap text-xs">
                                    <span>Move to {{ $labels[$nextStatus] }}</span>
                                    <span aria-hidden="true">→</span>
                                </button>
                            </form>
                        @endif
                    @endif

                    @if(in_array('cancelled', $availableStatuses, true))
                        <form method="POST" action="{{ route('orders.status.update', $order) }}" data-confirm="Are you sure you want to cancel Order #{{ $order->order_number }}? This action cannot be undone." data-confirm-title="Cancel Laundry Ticket" data-confirm-type="danger" data-confirm-btn="Yes, Cancel Order">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="font-mono text-xs font-bold text-red-600 hover:underline px-2 py-2">
                                Cancel Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <!-- Audit & Stage History -->
        <div class="retro-panel p-6">
            <h2 class="font-recoleta text-xl font-bold text-[#182830] mb-4">Stage Audit Trail</h2>
            <div class="space-y-4">
                @forelse($order->statusHistories as $history)
                    <div class="flex items-start gap-3 border-l-2 border-[#25799B] pl-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-recoleta text-sm font-bold text-[#182830]">{{ $labels[$history->status] ?? ucfirst($history->status) }}</span>
                                <span class="font-mono text-[11px] text-[#25799B]">by {{ $history->changedBy->name }}</span>
                            </div>
                            <p class="font-mono text-[10px] text-slate-500">{{ $history->created_at->format('M d, Y h:i A') }}</p>
                            @if($history->notes)
                                <p class="mt-1 rounded bg-[#F7E6CB]/40 p-2 text-xs font-medium text-slate-700 border border-[#182830]/10">
                                    {{ $history->notes }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="font-mono text-xs text-slate-500">No transitions logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Cashier & Settlements -->
    <div class="space-y-6">
        <!-- Perforated Counter Ticket Receipt Slip -->
        <div id="thermal-receipt-container" class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-5 shadow-[4px_4px_0px_#182830] print:border-none print:shadow-none print:p-0">
            <div class="flex items-center justify-between border-b-2 border-[#182830]/15 pb-3 mb-4 print:hidden">
                <span class="flex items-center gap-2 font-recoleta text-lg font-bold text-[#182830]">
                    <!-- Natural Ticket Vector SVG -->
                    <svg class="h-5 w-5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"></path>
                        <path d="M13 5v2"></path>
                        <path d="M13 11v2"></path>
                        <path d="M13 17v2"></path>
                    </svg>
                    <span>Counter Ticket Slip</span>
                </span>
                <span class="font-mono text-[10px] font-bold uppercase rounded-md bg-[#A2C5D8]/40 px-2 py-0.5 text-[#182830]">
                    Official Stub
                </span>
            </div>

            <!-- Print & Quick Intake Actions on Screen -->
            <div class="flex flex-col gap-2 mb-4 print:hidden">
                <button type="button" onclick="window.print()" class="retro-btn-primary w-full text-xs flex items-center justify-center gap-2 cursor-pointer shadow-[2px_2px_0px_#182830]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Print Customer Receipt</span>
                </button>
                <a href="{{ route('orders.create') }}" class="retro-btn-secondary w-full text-center text-xs flex items-center justify-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>＋ Take Another Intake Order</span>
                </a>
            </div>

            <!-- The Perforated Physical Ticket Body (Printed & Displayed) -->
            <div id="printable-ticket-content" class="rounded-2xl border-2 border-dashed border-[#182830] bg-[#FFFDF8] p-4 text-[#182830] font-mono text-xs shadow-[2px_2px_0px_#182830]">
                <!-- Shop Header -->
                <div class="text-center border-b border-dashed border-[#182830] pb-3 mb-3">
                    <p class="font-recoleta text-lg font-black tracking-tight text-[#182830]">TROWA LAUNDRY</p>
                    <p class="text-[10px] uppercase font-bold text-[#25799B] tracking-wider">& DRY CLEANING SERVICES</p>
                    <p class="text-[10px] text-slate-600 mt-0.5">Commercial Wash · Dry · Iron · Fold</p>
                    <p class="text-[10px] font-bold text-slate-700 mt-0.5">Station Counter Register</p>
                </div>

                <!-- Ticket Meta -->
                <div class="space-y-1 text-[11px] border-b border-dashed border-[#182830] pb-2.5 mb-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500 uppercase">Ticket No:</span>
                        <strong class="font-bold text-[#182830]">{{ $order->order_number ?? 'TL-'.$order->id }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 uppercase">Intake Date:</span>
                        <span>{{ $order->created_at?->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 uppercase">Cashier:</span>
                        <span>{{ $order->creator?->name ?? 'Counter Staff' }}</span>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="space-y-1 text-[11px] border-b border-dashed border-[#182830] pb-2.5 mb-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500 uppercase">Customer:</span>
                        <strong class="text-[#182830]">{{ $order->customer?->name ?? $order->customer_name }}</strong>
                    </div>
                    @if($order->customer?->contact_number ?? $order->customer?->phone)
                        <div class="flex justify-between">
                            <span class="text-slate-500 uppercase">Contact:</span>
                            <span>{{ $order->customer?->contact_number ?? $order->customer?->phone }}</span>
                        </div>
                    @endif
                    @if($order->customer?->address)
                        <div class="flex justify-between text-[10px]">
                            <span class="text-slate-500 uppercase">Address:</span>
                            <span class="text-right truncate max-w-[180px]">{{ $order->customer?->address }}</span>
                        </div>
                    @endif
                </div>

                <!-- Scale & Load Details -->
                <div class="space-y-1 text-[11px] border-b border-dashed border-[#182830] pb-2.5 mb-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500 uppercase">Scale Weight:</span>
                        <strong class="text-[#182830]">{{ $order->weight_kg }} kg</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 uppercase">Drum Allocation:</span>
                        <span>{{ $order->number_of_loads }} commercial load(s)</span>
                    </div>
                </div>

                <!-- Itemized Service Charges -->
                <div class="border-b border-dashed border-[#182830] pb-2.5 mb-2.5">
                    <span class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Services Breakdown</span>
                    <div class="space-y-1">
                        @foreach($order->orderServices as $line)
                            <div class="flex justify-between text-[11px]">
                                <span class="pr-2">{{ $line->service?->name }} ({{ $line->loads }}x)</span>
                                <strong class="text-[#182830]">₱{{ number_format((float) $line->subtotal, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Garment Counts if present -->
                @if($order->itemDetails->isNotEmpty())
                    <div class="border-b border-dashed border-[#182830] pb-2.5 mb-2.5">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] font-bold uppercase text-slate-500">Counted Garments:</span>
                            <span class="text-[10px] font-bold text-[#25799B]">{{ $order->itemDetails->sum('quantity') }} pcs</span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[10px]">
                            @foreach($order->itemDetails as $item)
                                <div class="flex justify-between text-slate-700">
                                    <span class="truncate pr-1">• {{ $item->item_name }}:</span>
                                    <strong>{{ $item->quantity }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Financial Accounting -->
                <div class="space-y-1.5 pt-1 text-[11px]">
                    <div class="flex justify-between text-xs font-black">
                        <span>TOTAL CHARGES:</span>
                        <span class="text-[#182830]">₱{{ number_format((float) $order->total_price, 2) }}</span>
                    </div>

                    @if((float) $order->change > 0)
                        <div class="flex justify-between text-slate-600">
                            <span>Cash Tendered:</span>
                            <span>₱{{ number_format((float) $order->amount_paid + (float) $order->change, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700 font-bold">
                            <span>Change Returned:</span>
                            <span>₱{{ number_format((float) $order->change, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-slate-700">
                        <span>Amount Paid / Collected:</span>
                        <span class="font-bold text-emerald-700">₱{{ number_format($totalPaid, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-xs font-black border-t border-[#182830]/20 pt-1">
                        <span>BALANCE DUE:</span>
                        <span class="{{ $balance > 0 ? 'text-[#CB1B03]' : 'text-emerald-700' }}">
                            ₱{{ number_format($balance, 2) }}
                        </span>
                    </div>

                    <div class="mt-2 text-center rounded-lg border border-[#182830] py-1 text-[10px] font-bold uppercase {{ $balance <= 0 ? 'bg-emerald-100 text-emerald-800' : ($totalPaid > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ $balance <= 0 ? 'STATUS: PAID IN FULL' : ($totalPaid > 0 ? 'STATUS: PARTIALLY PAID' : 'STATUS: UNPAID ON CLAIM') }}
                    </div>
                </div>

                <!-- Perforated Tear-off Footer -->
                <div class="text-center border-t-2 border-dashed border-[#182830] pt-3 mt-3 text-[10px] text-slate-600 space-y-0.5">
                    <p class="font-bold text-[#182830] uppercase">Present this ticket when claiming</p>
                    <p>Current Stage: <strong class="text-[#25799B]">{{ $labels[$order->status] ?? ucfirst($order->status) }}</strong></p>
                    <p class="font-mono text-[9px] text-slate-400 pt-1">*** Thank you for your business! ***</p>
                </div>
            </div>
        </div>

        <!-- Settlement Summary -->
        <div class="retro-panel p-6">
            <h2 class="font-recoleta text-xl font-bold text-[#182830] mb-3">Settlement Ledger</h2>
            <dl class="space-y-2.5 font-mono text-xs">
                <div class="flex justify-between py-1 border-b border-[#182830]/10">
                    <dt class="text-slate-600">Total Billed:</dt>
                    <dd class="font-bold text-[#182830]">₱{{ number_format((float) $order->total_price, 2) }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-[#182830]/10">
                    <dt class="text-slate-600">Amount Collected:</dt>
                    <dd class="font-bold text-emerald-700">₱{{ number_format($totalPaid, 2) }}</dd>
                </div>
                <div class="flex justify-between py-1 font-extrabold text-sm">
                    <dt class="text-[#182830]">Outstanding Due:</dt>
                    <dd class="{{ $balance > 0 ? 'text-[#CB1B03]' : 'text-emerald-700' }}">₱{{ number_format($balance, 2) }}</dd>
                </div>
            </dl>

            <div class="mt-4">
                <span class="badge w-full justify-center text-center font-mono text-xs {{ $order->payments->contains('payment_status', 'refunded') && $totalPaid <= 0 ? 'border-red-600 bg-red-100 text-red-800' : ($balance <= 0 ? 'border-emerald-600 bg-emerald-100 text-emerald-800' : ($totalPaid > 0 ? 'border-amber-600 bg-amber-100 text-amber-800' : 'border-[#CB1B03] bg-red-100 text-[#CB1B03]')) }}">
                    {{ $balance <= 0 ? '✓ Paid in Full' : ($totalPaid > 0 ? 'Partially Paid' : 'Payment Pending') }}
                </span>
            </div>
        </div>

        <!-- Add Payment Drawer -->
        @if($balance > 0)
            <div class="retro-panel p-6 bg-[#A2C5D8]/20">
                <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-1">Tender Payment</h2>
                <p class="font-mono text-[11px] text-[#25799B] mb-3">Maximum due: ₱{{ number_format($balance, 2) }}</p>

                <form method="POST" action="{{ route('orders.payments.store', $order) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="tender-amount-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Payment Amount (₱) *</label>
                        <input id="tender-amount-input" name="amount" type="number" min="0.01" max="{{ $balance }}" step="0.01" value="{{ $balance }}" class="field font-mono font-bold text-xs" required>
                    </div>

                    <div>
                        <label for="tender-method-select" class="block font-mono text-xs font-bold text-[#182830] mb-1">Tender Channel</label>
                        <select id="tender-method-select" name="payment_method" class="field text-xs">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="tender-reference-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">Reference # (Optional)</label>
                        <input id="tender-reference-input" name="reference_number" class="field text-xs" placeholder="e.g. GCash Ref 10023">
                    </div>

                    <button type="submit" class="retro-btn-primary w-full text-xs mt-2">
                        Record Payment ➔
                    </button>
                </form>
            </div>
        @endif

        <!-- Receipts & Transaction History -->
        <div class="retro-panel p-6">
            <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3">Payment Receipts</h2>
            <div class="space-y-3">
                @forelse($order->payments as $payment)
                    <div class="rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 text-xs shadow-[1px_1px_0px_#182830]">
                        <div class="flex justify-between font-mono font-bold">
                            <span class="text-[#182830]">₱{{ number_format((float) $payment->amount, 2) }}</span>
                            <span class="uppercase text-[#25799B]">{{ $payment->payment_method }}</span>
                        </div>
                        <p class="font-mono text-[10px] text-slate-500 mt-1">
                            {{ optional($payment->paid_at)->format('M d, Y h:i A') }} · {{ $payment->receivedBy->name }}
                        </p>
                        @if($payment->reference_number)
                            <p class="font-mono text-[10px] text-slate-600 mt-0.5">Ref: {{ $payment->reference_number }}</p>
                        @endif
                    </div>
                @empty
                    <p class="font-mono text-xs text-slate-500">No payment records yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('partials.claim_payment_modal')

@push('styles')
<style>
@media print {
    /* Hide everything on page */
    body * {
        visibility: hidden !important;
    }
    /* Exclusively show the ticket */
    #printable-ticket-content, #printable-ticket-content * {
        visibility: visible !important;
    }
    #printable-ticket-content {
        position: fixed !important;
        left: 50% !important;
        top: 20px !important;
        transform: translateX(-50%) !important;
        width: 100% !important;
        max-width: 320px !important;
        margin: 0 !important;
        padding: 16px !important;
        border: 2px dashed #000 !important;
        background: #fff !important;
        color: #000 !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
@endsection
