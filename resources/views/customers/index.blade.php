@extends('layouts_app')

@section('content')
@php
    $stats = $stats ?? [
        'total' => $customers->total(),
        'active' => $customers->where('is_active', true)->count(),
        'vip' => $customers->where('orders_count', '>=', 3)->count(),
        'total_spend' => 0,
    ];
@endphp

<div class="space-y-6">

    <!-- Top Station Header & Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Patron Registry · {{ $customers->total() }} Registered Accounts</span>
            </div>
            <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
                Customer Directory
            </h1>
            <p class="text-xs sm:text-sm font-medium text-[#25799B]">
                Complete client registry, contact stubs, loyalty tiers, lifetime spend, and active laundry tickets.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" data-modal-open="customer-modal" class="retro-btn-primary flex items-center gap-2 text-xs shadow-[3px_3px_0px_#182830]">
                <span class="text-base font-bold">＋</span>
                <span class="font-recoleta font-bold">Register New Customer</span>
            </button>
        </div>
    </div>

    <!-- Informational Patron Pulse KPI Cards (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Total Registered -->
        <div class="retro-panel p-4 flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Registered Patrons</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">{{ $stats['total'] }}</p>
                <p class="font-mono text-[11px] text-[#25799B]">Client accounts on file</p>
            </div>
        </div>

        <!-- Card 2: VIP Frequent Clients -->
        <div class="retro-panel p-4 flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">VIP & Regulars</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">{{ $stats['vip'] }}</p>
                <p class="font-mono text-[11px] text-amber-700 font-bold">3+ Completed orders</p>
            </div>
        </div>

        <!-- Card 3: Active Accounts -->
        <div class="retro-panel p-4 flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-emerald-100 text-emerald-800 shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-emerald-800">Active Status</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">{{ $stats['active'] }}</p>
                <p class="font-mono text-[11px] text-emerald-700">In good standing</p>
            </div>
        </div>

        <!-- Card 4: All-Time Patron Revenue -->
        <div class="retro-panel p-4 flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white shadow-[2px_2px_0px_#182830]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div>
                <p class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#F7E6CB]">Total Revenue</p>
                <p class="font-recoleta text-2xl font-black text-[#182830] leading-tight">₱{{ number_format($stats['total_spend'], 0) }}</p>
                <p class="font-mono text-[11px] text-[#25799B]">Lifetime customer billing</p>
            </div>
        </div>
    </div>

    <!-- Search & Quick Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="font-mono text-xs font-bold text-[#182830]">Browse:</span>
            <div class="flex items-center gap-1">
                <button type="button" class="cust-filter-btn rounded-lg border-2 border-[#182830] bg-[#182830] px-3 py-1 font-mono text-xs font-bold text-[#FFFDF8] shadow-[1px_1px_0px_#182830] cursor-pointer" data-tier="all">
                    All ({{ $customers->total() }})
                </button>
                <button type="button" class="cust-filter-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[1px_1px_0px_#182830] cursor-pointer" data-tier="vip">
                    VIPs ({{ $stats['vip'] }})
                </button>
                <button type="button" class="cust-filter-btn rounded-lg border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#F7E6CB] shadow-[1px_1px_0px_#182830] cursor-pointer" data-tier="active">
                    Active ({{ $stats['active'] }})
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('customers.index') }}" class="w-full sm:w-96">
            <div class="relative">
                <input name="search" id="customer-search-input" value="{{ $search }}" class="field pr-10 text-xs" placeholder="Search by name, phone, address, or email...">
                <span class="absolute right-3 top-2.5 text-[#25799B]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
            </div>
        </form>
    </div>

    <!-- Informational Customer Profile Directory Grid -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($customers as $customer)
            @php
                $orderCount = (int) $customer->orders_count;
                $totalSpent = (float) ($customer->orders_sum_total_price ?? 0);
                $latestOrder = $customer->orders->first();
                $isVip = $orderCount >= 3;
                
                // Tier classification
                if ($orderCount >= 10) {
                    $tierName = 'Platinum VIP';
                    $tierBadge = 'bg-[#CB1B03] text-white border-[#182830]';
                } elseif ($orderCount >= 5) {
                    $tierName = 'Gold Patron';
                    $tierBadge = 'bg-amber-400 text-[#182830] border-[#182830]';
                } elseif ($orderCount >= 2) {
                    $tierName = 'Silver Member';
                    $tierBadge = 'bg-[#A2C5D8] text-[#182830] border-[#182830]';
                } else {
                    $tierName = 'New Patron';
                    $tierBadge = 'bg-[#F7E6CB] text-slate-700 border-slate-400';
                }
            @endphp
            <div class="customer-profile-card group flex flex-col justify-between rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-5 shadow-[4px_4px_0px_#182830] transition hover:-translate-y-1 hover:shadow-[5px_5px_0px_#25799B]"
                 data-vip="{{ $isVip ? '1' : '0' }}"
                 data-active="{{ $customer->is_active ? '1' : '0' }}"
                 data-search="{{ strtolower($customer->name . ' ' . $customer->phone . ' ' . $customer->contact_number . ' ' . $customer->email . ' ' . $customer->address) }}">
                
                <div>
                    <!-- Card Header: Avatar, Name & Loyalty Tier -->
                    <div class="flex items-start justify-between gap-3 border-b border-[#182830]/15 pb-3.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Retro Monogram Avatar -->
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] {{ $isVip ? 'bg-[#CB1B03] text-[#FFFDF8]' : 'bg-[#25799B] text-[#FFFDF8]' }} font-recoleta text-lg font-bold shadow-[2px_2px_0px_#182830]">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate font-recoleta text-lg font-bold text-[#182830] group-hover:text-[#25799B] transition">
                                    {{ $customer->name }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="rounded-full border px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider {{ $tierBadge }}">
                                        {{ $tierName }}
                                    </span>
                                    <span class="rounded-full border border-[#182830]/30 px-1.5 py-0.5 font-mono text-[10px] font-bold {{ $customer->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Count Pill -->
                        <div class="text-right shrink-0">
                            <span class="font-recoleta text-xl font-black text-[#182830]">{{ $orderCount }}</span>
                            <span class="block font-mono text-[10px] font-bold text-slate-500 uppercase">Visits</span>
                        </div>
                    </div>

                    <!-- Informational Contact Stubs -->
                    <div class="py-3.5 space-y-2 text-xs">
                        <!-- Primary Phone -->
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-[#182830]/30 bg-[#A2C5D8]/30 text-[#25799B]">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            @if($customer->phone || $customer->contact_number)
                                <a href="tel:{{ $customer->phone ?? $customer->contact_number }}" class="font-mono font-bold text-[#25799B] hover:underline">
                                    {{ $customer->phone ?? $customer->contact_number }}
                                </a>
                            @else
                                <span class="font-mono text-slate-400">No phone on file</span>
                            @endif
                        </div>

                        <!-- Email Address -->
                        @if($customer->email)
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-[#182830]/30 bg-[#A2C5D8]/30 text-[#25799B]">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </span>
                                <a href="mailto:{{ $customer->email }}" class="truncate font-mono text-slate-600 hover:text-[#CB1B03] hover:underline">
                                    {{ $customer->email }}
                                </a>
                            </div>
                        @endif

                        <!-- Residential / Delivery Address -->
                        @if($customer->address)
                            <div class="flex items-start gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-[#182830]/30 bg-[#A2C5D8]/30 text-[#25799B] mt-0.5">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </span>
                                <p class="text-xs text-slate-600 leading-snug line-clamp-2">
                                    {{ $customer->address }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Patron Financial Metrics Box -->
                    <div class="rounded-xl border border-[#182830]/15 bg-[#F7E6CB]/40 p-3 grid grid-cols-2 gap-2 text-xs mb-3">
                        <div>
                            <span class="block font-mono text-[10px] font-bold text-[#25799B] uppercase">Lifetime Billing</span>
                            <span class="font-recoleta text-base font-extrabold text-[#182830]">
                                ₱{{ number_format($totalSpent, 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="block font-mono text-[10px] font-bold text-[#25799B] uppercase">Last Order</span>
                            @if($latestOrder)
                                <span class="font-mono text-xs font-bold text-[#CB1B03]">
                                    {{ $latestOrder->created_at?->format('M j, Y') ?? 'Recent' }}
                                </span>
                            @else
                                <span class="font-mono text-xs text-slate-500">First-time client</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="pt-3 border-t border-[#182830]/15 flex items-center justify-between gap-2">
                    <a href="{{ route('orders.index', ['search' => $customer->name]) }}" class="font-mono text-xs font-bold text-[#25799B] hover:text-[#CB1B03] hover:underline flex items-center gap-1">
                        <span>Orders ({{ $orderCount }})</span>
                        <span aria-hidden="true">➔</span>
                    </a>
                    
                    <button type="button" 
                            data-modal-open="order-modal" 
                            class="retro-btn-secondary text-[11px] py-1 px-2.5 font-bold"
                            onclick="if(document.getElementById('wizard-customer-name')) { document.getElementById('wizard-customer-name').value = '{{ addslashes($customer->name) }}'; document.getElementById('wizard-customer-phone').value = '{{ addslashes($customer->phone ?? $customer->contact_number ?? '') }}'; }">
                        <span>＋ New Order</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border-2 border-dashed border-[#182830]/40 p-12 text-center bg-[#FFFDF8]">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830] mb-3">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <h3 class="font-recoleta text-lg font-bold text-[#182830]">No matching customers found</h3>
                <p class="text-xs text-[#25799B] font-medium mt-1">Try adjusting your search criteria or register a new customer profile.</p>
                <button type="button" data-modal-open="customer-modal" class="retro-btn-primary text-xs mt-4">
                    ＋ Register New Customer
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
        <div class="retro-panel p-4">
            {{ $customers->links() }}
        </div>
    @endif

    <!-- ADMIN TOOLS SECTION -->
    @if(auth()->user()->role === 'admin')
    <section class="mt-8 space-y-4">
        <div class="flex items-center gap-2 border-b-2 border-[#182830]/15 pb-2">
            <span class="rounded bg-[#CB1B03] px-2 py-0.5 font-mono text-[11px] font-bold uppercase text-white">Admin Deck</span>
            <h2 class="font-recoleta text-xl font-bold text-[#182830]">Account Record Administration & Profile Merging</h2>
        </div>

        <div class="space-y-3">
            @foreach($customers as $customer)
                <details class="retro-panel p-4 group">
                    <summary class="flex cursor-pointer items-center justify-between font-recoleta text-base font-bold text-[#182830]">
                        <div class="flex items-center gap-2">
                            <span>{{ $customer->name }}</span>
                            <span class="font-mono text-xs font-semibold text-[#25799B]">({{ $customer->phone ?? 'No phone' }})</span>
                        </div>
                        <span class="font-mono text-xs font-semibold text-[#25799B]">
                            {{ $customer->orders_count }} orders · {{ $customer->is_active ? 'Active' : 'Inactive' }} ▾
                        </span>
                    </summary>

                    <div class="mt-4 border-t border-[#182830]/15 pt-4 grid gap-5 lg:grid-cols-2">
                        <form method="POST" action="{{ route('customers.update', $customer) }}" class="grid gap-3 sm:grid-cols-2 bg-[#F7E6CB]/30 p-4 rounded-xl border border-[#182830]/15">
                            @csrf @method('PUT')
                            <div>
                                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Full Name</label>
                                <input name="name" value="{{ $customer->name }}" class="field text-xs" required>
                            </div>
                            <div>
                                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Mobile Phone</label>
                                <input name="phone" value="{{ $customer->phone }}" class="field text-xs">
                            </div>
                            <div>
                                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Contact Alt</label>
                                <input name="contact_number" value="{{ $customer->contact_number }}" class="field text-xs">
                            </div>
                            <div>
                                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Email</label>
                                <input name="email" type="email" value="{{ $customer->email }}" class="field text-xs">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block font-mono text-[11px] font-bold text-[#182830] mb-1">Delivery Address</label>
                                <input name="address" value="{{ $customer->address }}" class="field text-xs">
                            </div>
                            <button class="retro-btn-secondary text-xs sm:col-span-2 mt-1">Save Customer Changes</button>
                        </form>

                        <div class="space-y-3 bg-[#FFFDF8] p-4 rounded-xl border border-[#182830]/15">
                            <h3 class="font-recoleta text-sm font-bold text-[#182830]">Merge & Status Actions</h3>
                            @if($customer->is_active && !$customer->merged_into_id)
                                <form method="POST" action="{{ route('customers.merge', $customer) }}" class="flex gap-2">
                                    @csrf
                                    <select name="target_customer_id" class="field text-xs" required>
                                        <option value="">Merge into active customer...</option>
                                        @foreach($mergeTargets->where('id', '!=', $customer->id) as $target)
                                            <option value="{{ $target->id }}">{{ $target->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="retro-btn-secondary text-xs shrink-0" onclick="return confirm('Merge this customer into the selected record? Existing order customer snapshots will be preserved.')">
                                        Merge
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('customers.toggle-active', $customer) }}">
                                    @csrf @method('PATCH')
                                    <button class="font-mono text-xs font-bold text-[#CB1B03] hover:underline">
                                        {{ $customer->is_active ? 'Deactivate Customer' : 'Reactivate Customer' }}
                                    </button>
                                </form>
                            @endif
                            @if($customer->merged_into_id)
                                <p class="font-mono text-xs text-slate-500">Merged into {{ $customer->mergedInto?->name }}.</p>
                            @endif
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </section>
    @endif
</div>

<!-- Add Customer Modal -->
<div id="customer-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/75 p-4 backdrop-blur-xs">
    <div class="w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 shadow-[8px_8px_0px_#182830]">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830] pb-3">
            <div>
                <h2 class="font-recoleta text-2xl font-bold text-[#182830]">Register Customer</h2>
                <p class="text-xs text-[#25799B] font-medium">Create client account for order intake and loyalty</p>
            </div>
            <button data-modal-close class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-lg font-bold hover:bg-[#CB1B03] hover:text-white transition cursor-pointer">×</button>
        </div>
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Full Customer Name *</label>
                <input name="name" class="field text-xs" placeholder="e.g. Maria Santos" required autofocus>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Mobile Phone</label>
                    <input name="phone" class="field text-xs" placeholder="0917XXXXXXX">
                </div>
                <div>
                    <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Contact Alt</label>
                    <input name="contact_number" class="field text-xs" placeholder="Landline/Alt">
                </div>
            </div>
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Email Address</label>
                <input name="email" type="email" class="field text-xs" placeholder="maria@example.com">
            </div>
            <div>
                <label class="block font-mono text-xs font-bold text-[#182830] mb-1">Delivery / Residence Address</label>
                <input name="address" class="field text-xs" placeholder="Unit #, Street, Barangay, City">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" data-modal-close class="retro-btn-secondary flex-1 text-xs">Cancel</button>
                <button class="retro-btn-primary flex-1 text-xs">Save Patron ➔</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Live filter pills (All / VIP / Active)
    const custFilterBtns = document.querySelectorAll('.cust-filter-btn');
    const custCards = document.querySelectorAll('.customer-profile-card');
    const searchInput = document.getElementById('customer-search-input');
    let activeTier = 'all';

    function filterCustomers() {
        const query = searchInput?.value.toLowerCase().trim() || '';
        custCards.forEach(card => {
            const isVip = card.dataset.vip === '1';
            const isActive = card.dataset.active === '1';
            const searchData = card.dataset.search || '';

            let matchTier = true;
            if (activeTier === 'vip') matchTier = isVip;
            if (activeTier === 'active') matchTier = isActive;

            const matchQuery = !query || searchData.includes(query);

            if (matchTier && matchQuery) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    custFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            custFilterBtns.forEach(b => {
                b.classList.remove('bg-[#182830]', 'text-[#FFFDF8]');
                b.classList.add('bg-[#FFFDF8]', 'text-[#182830]');
            });
            btn.classList.add('bg-[#182830]', 'text-[#FFFDF8]');
            btn.classList.remove('bg-[#FFFDF8]', 'text-[#182830]');

            activeTier = btn.dataset.tier || 'all';
            filterCustomers();
        });
    });

    searchInput?.addEventListener('input', filterCustomers);
</script>
@endpush
@endsection
