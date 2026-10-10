<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#25799B">
    <title>{{ $title ?? 'Trowa Laundry — Staff Station' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Young+Serif&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F7E6CB] text-[#182830] font-sans antialiased selection:bg-[#A2C5D8] selection:text-[#182830]">
<!-- Top Retro Laundromat Navigation Loading Bar -->
<div id="trowa-top-loader" aria-hidden="true">
    <div id="trowa-top-loader-inner"></div>
</div>

<!-- Global Trowa Laundromat Transaction Loading Modal -->
<div id="global-wash-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/80 backdrop-blur-xs p-4 transition-opacity duration-200" role="status" aria-live="polite">
    <div class="relative w-full max-w-sm rounded-3xl border-4 border-[#182830] bg-[#FFFDF8] shadow-[10px_10px_0px_#182830] overflow-hidden animate-machine-vibrate">
        
        <!-- Top Canopy -->
        <div class="border-b-3 border-[#182830] bg-[#1E6482] px-4 py-2.5 text-[#FFFDF8] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-[#CB1B03] shadow-[0_0_6px_#CB1B03] animate-pulse"></span>
                <span class="font-recoleta text-xs sm:text-sm font-black tracking-wide text-[#F7E6CB]" id="global-wash-header">
                    ⚡ TROWA LAUNDRY SYSTEM ⚡
                </span>
            </div>
            <span class="rounded bg-[#14232B] px-2 py-0.5 font-mono text-[9px] font-bold text-[#BAE6FD] border border-[#182830]" id="global-wash-stage">
                PROCESSING
            </span>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col items-center text-center space-y-3.5">
            <!-- Compact Washing Machine Porthole -->
            <div class="relative flex h-28 w-28 items-center justify-center rounded-full border-6 border-[#182830] bg-gradient-to-tr from-[#94A3B8] via-[#E2E8F0] to-[#FFFFFF] p-2 shadow-[4px_4px_0px_#182830]">
                <!-- Outer Rivets -->
                <div class="absolute top-1 left-1/2 -translate-x-1/2 h-1.5 w-1.5 rounded-full bg-[#182830]"></div>
                <div class="absolute bottom-1 left-1/2 -translate-x-1/2 h-1.5 w-1.5 rounded-full bg-[#182830]"></div>
                <div class="absolute left-1 top-1/2 -translate-y-1/2 h-1.5 w-1.5 rounded-full bg-[#182830]"></div>
                <div class="absolute right-1 top-1/2 -translate-y-1/2 h-1.5 w-1.5 rounded-full bg-[#182830]"></div>

                <!-- Drum Window -->
                <div class="relative h-full w-full rounded-full border-3 border-[#182830] bg-radial from-[#1A4559] via-[#0E232F] to-[#071319] overflow-hidden flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-2 border-dashed border-[#A2C5D8]/30 animate-wash-spin"></div>
                    <!-- Sloshing water -->
                    <div class="absolute bottom-0 inset-x-0 h-14 bg-gradient-to-t from-[#0284C7]/80 via-[#0EA5E9]/60 to-[#38BDF8]/20 animate-suds-wave"></div>
                    
                    <!-- Tumbling mini garments -->
                    <div class="absolute z-10 animate-laundry-1">
                        <svg class="h-8 w-8 text-[#CB1B03]" viewBox="0 0 64 64" fill="none">
                            <path d="M20 12 L26 18 C28 20 36 20 38 18 L44 12 L56 20 L50 28 L44 24 L44 52 L20 52 L20 24 L14 28 L8 20 Z" fill="#CB1B03" stroke="#182830" stroke-width="3" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="absolute z-10 animate-laundry-2">
                        <svg class="h-7 w-7 text-[#F59E0B]" viewBox="0 0 64 64" fill="none">
                            <path d="M24 10 L38 10 L38 34 C38 42 46 44 48 48 C50 52 46 56 40 56 C32 56 22 50 20 40 L24 10 Z" fill="#F59E0B" stroke="#182830" stroke-width="3" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <!-- Glass Reflection -->
                    <div class="pointer-events-none absolute -top-4 -left-4 h-16 w-16 rounded-full bg-white/30 blur-xs"></div>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <div class="space-y-1">
                <h4 id="global-wash-title" class="font-recoleta text-lg font-black text-[#182830]">
                    Calibrating Wash Batch...
                </h4>
                <p id="global-wash-msg" class="font-mono text-xs text-[#25799B] font-semibold">
                    Updating laundromat records in real-time...
                </p>
            </div>

            <!-- Chunky Hazard Bar -->
            <div class="w-full h-4 overflow-hidden rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-0.5 shadow-[2px_2px_0px_#182830]">
                <div class="h-full w-full rounded-lg retro-hazard-bar"></div>
            </div>
        </div>

        <div class="border-t-2 border-[#182830] bg-[#F7E6CB] px-4 py-2 font-mono text-[10px] text-slate-700 font-bold flex justify-between">
            <span>STATION: TERMINAL #01</span>
            <span class="text-[#CB1B03] flex items-center gap-1">
                <span class="h-1.5 w-1.5 rounded-full bg-[#CB1B03] animate-ping"></span>
                ACTIVE VORTEX
            </span>
        </div>
    </div>
</div>

<!-- Global Trowa Retro Laundromat Confirmation Modal -->
<div id="retro-confirm-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-[#182830]/80 backdrop-blur-xs p-4 transition-all duration-200" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-md rounded-3xl border-4 border-[#182830] bg-[#FFFDF8] shadow-[8px_8px_0px_#182830] overflow-hidden text-[#182830] animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header -->
        <div class="border-b-3 border-[#182830] bg-[#1E6482] px-5 py-3 text-[#FFFDF8] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#FFFDF8] text-[#CB1B03] shadow-[1px_1px_0px_#182830]">
                    <!-- Ticket / Stamp Vector Icon -->
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </span>
                <div>
                    <h3 class="font-recoleta text-base font-black text-[#F7E6CB] leading-tight" id="retro-confirm-title">
                        Confirm Action
                    </h3>
                    <p class="font-mono text-[9px] uppercase tracking-wider text-[#A2C5D8]" id="retro-confirm-badge">
                        Trowa Laundromat
                    </p>
                </div>
            </div>
            <button type="button" id="retro-confirm-btn-close" class="flex h-7 w-7 items-center justify-center rounded-lg border border-[#182830] bg-[#FFFDF8] text-sm font-black text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer" aria-label="Close">
                ×
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 space-y-4 font-sans bg-[#FFFDF8]">
            <div class="flex items-start gap-3.5">
                <div id="retro-confirm-icon-wrap" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#CB1B03] text-white shadow-[2px_2px_0px_#182830]">
                    <svg id="retro-confirm-icon-danger" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <svg id="retro-confirm-icon-primary" class="h-6 w-6 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <svg id="retro-confirm-icon-check" class="h-6 w-6 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="flex-1 space-y-1">
                    <p id="retro-confirm-message" class="text-sm font-bold text-[#182830] leading-snug">
                        Are you sure you want to proceed with this action?
                    </p>
                    <p id="retro-confirm-hint" class="text-xs text-slate-600 font-medium leading-relaxed">
                        Please review the details below before confirming.
                    </p>
                </div>
            </div>

            <!-- Optional Details Summary Box -->
            <div id="retro-confirm-details" class="hidden rounded-2xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-3.5 font-mono text-xs text-[#182830] space-y-1.5 shadow-[2px_2px_0px_#182830]">
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t-2 border-[#182830]/15">
                <button type="button" id="retro-confirm-btn-cancel" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-slate-700 shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="retro-confirm-btn-proceed" class="rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-5 py-2 font-recoleta text-sm font-bold text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer flex items-center gap-1.5">
                    <span id="retro-confirm-btn-proceed-label">Yes, Proceed</span>
                    <span id="retro-confirm-btn-proceed-arrow">➔</span>
                </button>
            </div>
        </div>

    </div>
</div>

@auth
    <!-- Mobile Sticky Top Bar -->
    <header class="lg:hidden sticky top-0 z-40 flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] px-4 py-3 text-[#FFFDF8] shadow-[0_3px_0_#182830] print:hidden">
        <div class="flex items-center gap-3">
            <button type="button" id="mobile-menu-toggle" aria-label="Toggle Navigation Menu" class="flex h-10 w-10 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#182830] shadow-[2px_2px_0px_#182830] active:translate-x-0.5 active:translate-y-0.5">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <circle cx="12" cy="13" r="4.5"/>
                        <path d="M10 13c.5-.8 1.5-.8 2 0s1.5.8 2 0"/>
                        <circle cx="7" cy="6.5" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <span class="font-recoleta text-lg font-black tracking-tight text-[#FFFDF8]">Trowa Laundry</span>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-[#A2C5D8]">Station Counter</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="flex items-center gap-1.5 rounded-full border border-[#182830] bg-[#FFFDF8] px-2.5 py-1 text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830]">
                <span class="h-2 w-2 rounded-full bg-[#CB1B03] animate-pulse"></span>
                <span class="font-mono text-[10px] font-bold">ONLINE</span>
            </span>
            <button type="button" onclick="confirmStaffLogout()" title="Sign out station" class="flex h-9 w-9 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#CB1B03] shadow-[1px_1px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
        </div>
    </header>

    <div class="min-h-screen lg:flex">
        <!-- Backdrop for mobile drawer -->
        <div id="mobile-menu-backdrop" class="fixed inset-0 z-40 hidden bg-[#182830]/70 backdrop-blur-xs transition-opacity lg:hidden print:hidden"></div>

        <!-- Generous, Well-Proportioned Retro Sidebar (w-72 on lg, w-80 on xl for laptop breathing room) -->
        <aside id="sidebar-drawer" class="fixed inset-y-0 left-0 z-50 flex w-72 sm:w-80 lg:w-72 xl:w-80 shrink-0 -translate-x-full flex-col border-r-2 border-[#182830] bg-[#25799B] text-[#FFFDF8] shadow-[4px_0px_0px_rgba(24,40,48,0.15)] transition-transform duration-200 ease-in-out lg:translate-x-0 print:hidden">
            <!-- Sidebar Header & Laundromat Emblem -->
            <div class="border-b-2 border-[#182830] bg-[#1E6482] p-5 lg:p-6">
                <div class="flex items-center justify-between">
                    <a href="{{ auth()->check() && auth()->user()->role === 'customer' ? route('customer.portal') : route('dashboard') }}" class="group flex items-center gap-3.5">
                        <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[3px_3px_0px_#182830] transition-transform group-hover:rotate-6">
                            <!-- Natural Retro Washing Machine Vector Icon -->
                            <svg class="h-7 w-7 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="3" fill="#FFFDF8"/>
                                <circle cx="12" cy="13" r="5" stroke="#25799B" stroke-width="2"/>
                                <path d="M10 13c.5-.8 1.5-.8 2 0s1.5.8 2 0" stroke="#CB1B03" stroke-width="2"/>
                                <circle cx="7" cy="6.5" r="1" fill="#25799B"/>
                                <circle cx="10" cy="6.5" r="1" fill="#25799B"/>
                                <line x1="14" y1="6.5" x2="17" y2="6.5" stroke="#25799B" stroke-width="1.5"/>
                            </svg>
                            <span class="absolute -top-1 -right-1 h-3.5 w-3.5 rounded-full border border-[#182830] bg-[#CB1B03]"></span>
                        </div>
                        <div class="leading-tight">
                            <h1 class="font-recoleta text-2xl font-black tracking-tight text-[#F7E6CB] group-hover:text-white transition">Trowa Laundry</h1>
                            <p class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#A2C5D8] mt-0.5">Est. 2024 · Clean & Fresh</p>
                        </div>
                    </a>
                    <button id="mobile-menu-close" class="text-2xl text-[#F7E6CB] hover:text-white lg:hidden">×</button>
                </div>

                <!-- Live Counter Status Ribbon -->
                <div class="mt-4 flex items-center justify-between rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#CB1B03] opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#CB1B03]"></span>
                        </span>
                        <span class="font-mono text-xs font-bold uppercase tracking-wide">Terminal Active</span>
                    </div>
                    <span class="rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[11px] font-black uppercase text-[#182830]">Station #1</span>
                </div>
            </div>

            <!-- Quick Action: Fast Order Launch Button -->
            @if(auth()->user()->role === 'customer')
                <div class="p-4 lg:px-5 lg:pt-4 lg:pb-2">
                    <button type="button" onclick="document.getElementById('btn-open-intake-wizard')?.click()" class="w-full flex items-center justify-center gap-2.5 rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-4 py-3 font-recoleta text-base font-bold text-[#FFFDF8] shadow-[3px_3px_0px_#182830] transition hover:bg-[#B51702] cursor-pointer">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Laundry Request</span>
                    </button>
                </div>
            @elseif(auth()->user()->role === 'staff')
                <div class="p-4 lg:px-5 lg:pt-4 lg:pb-2">
                    <button type="button" data-modal-open="order-modal" class="w-full flex items-center justify-center gap-2.5 rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-4 py-3 font-recoleta text-base font-bold text-[#FFFDF8] shadow-[3px_3px_0px_#182830] transition hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 cursor-pointer">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Laundry Intake</span>
                    </button>
                </div>
            @endif

            <!-- Spacious Nav Links -->
            <nav class="flex-1 space-y-1.5 overflow-y-auto p-4 lg:p-5 pt-2">
                @if(auth()->user()->role === 'customer')
                    <p class="px-3 pt-2 pb-1 font-mono text-[11px] font-bold uppercase tracking-widest text-[#A2C5D8]">Customer Portal</p>
                    @php
                        $nav = [
                            ['customer.portal', 'My Dashboard', 'dashboard', 'Mascot jokes & personal expense analytics', ['tab' => 'dashboard']],
                            ['customer.portal', 'Live Cycle Tracker', 'machines', 'Real-time wash progress & garments status', ['tab' => 'active']],
                            ['customer.portal', 'Transaction History', 'billing', 'Past laundry tickets, reviews & receipts', ['tab' => 'history']],
                        ];
                    @endphp
                @elseif(auth()->user()->role === 'staff')
                    <p class="px-3 pt-2 pb-1 font-mono text-[11px] font-bold uppercase tracking-widest text-[#A2C5D8]">Staff Operations</p>
                    @php
                        $nav = [
                            ['dashboard', 'Staff Station', 'dashboard', 'Live operations deck & machine glance'],
                            ['orders.index', 'Laundry Queue', 'queue', 'Orders intake, weights & settlement'],
                            ['schedule.index', 'Machine Bay Showroom', 'machines', 'Live 16-unit floor showroom & stages'],
                            ['customers.index', 'Customer Directory', 'customers', 'Client accounts & contact stubs'],
                        ];
                    @endphp
                @else
                    <p class="px-3 pt-2 pb-1 font-mono text-[11px] font-bold uppercase tracking-widest text-[#A2C5D8]">Executive Management</p>
                    @php
                        $nav = [
                            ['dashboard', 'Executive Dashboard', 'dashboard', 'Financial metrics, volume & fleet glance'],
                            ['admin.staff.index', 'Staff & Team', 'customers', 'Staff CRUD, operator logins & permissions'],
                            ['customers.insights', 'Customer Insights', 'insights', 'LTV, repeat rates & VIP intelligence'],
                            ['inventory.index', 'Supply Inventory & Helper', 'inventory', 'Stockroom, burn rate & smart helper'],
                            ['reports', 'Business Reports & Plans', 'reports', 'Financial audits, billing ledger & strategy'],
                            ['expenses.index', 'Shop Expenses', 'expenses', 'Operating costs & vendor audits'],
                            ['services.index', 'Service Catalog', 'services', 'Pricing configuration & laundry services'],
                        ];
                    @endphp
                @endif

                @foreach($nav as $item)
                    @php
                        $route = $item[0];
                        $label = $item[1];
                        $iconKey = $item[2];
                        $hint = $item[3];
                        $params = $item[4] ?? [];
                        if (auth()->user()->role === 'customer') {
                            $targetTab = $params['tab'] ?? 'dashboard';
                            $currentTab = request('tab', 'dashboard');
                            $isActive = request()->routeIs($route) && ($currentTab === $targetTab);
                        } else {
                            $isActive = request()->routeIs($route);
                        }
                        $url = !empty($params) ? route($route, $params) : route($route);
                    @endphp
                    <a href="{{ $url }}" 
                       title="{{ $hint }}"
                       class="group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition-all {{ $isActive ? 'border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[3px_3px_0px_#182830]' : 'border-2 border-transparent text-[#F7E6CB] hover:border-[#182830]/40 hover:bg-[#1E6482] hover:text-[#FFFDF8]' }}">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $isActive ? 'border-2 border-[#182830] bg-[#A2C5D8] text-[#182830]' : 'bg-[#1E6482] text-[#A2C5D8] group-hover:bg-[#25799B] group-hover:text-white' }} transition">
                                @if($iconKey === 'dashboard')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                @elseif($iconKey === 'intake')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                @elseif($iconKey === 'queue')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l2 11h12l2-11H4z"/><path d="M4 10h16"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
                                @elseif($iconKey === 'machines' || $iconKey === 'status')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="2" width="18" height="20" rx="3"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 1 3 3"/><circle cx="7" cy="6" r="1" fill="currentColor"/><circle cx="10" cy="6" r="1" fill="currentColor"/></svg>
                                @elseif($iconKey === 'cashier')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="11" y1="15" x2="13" y2="15"/></svg>
                                @elseif($iconKey === 'customers')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                @elseif($iconKey === 'insights')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21H3V3"/><path d="M7 14l4-4 4 4 6-6"/><circle cx="11" cy="10" r="1.5"/><circle cx="15" cy="14" r="1.5"/><circle cx="21" cy="8" r="1.5"/></svg>
                                @elseif($iconKey === 'reports')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                @elseif($iconKey === 'billing')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                @elseif($iconKey === 'inventory')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                @elseif($iconKey === 'expenses')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                @elseif($iconKey === 'services')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block font-sans font-bold text-sm tracking-tight truncate leading-tight">{{ $label }}</span>
                                <span class="block text-[11px] font-medium leading-none {{ $isActive ? 'text-[#182830]/75' : 'text-[#A2C5D8]' }} truncate mt-0.5">{{ $hint }}</span>
                            </div>
                        </div>
                        @if($isActive)
                            <span class="h-2 w-2 rounded-full bg-[#CB1B03] shrink-0 ml-2"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- Retro Sound & Haptics Toggle Bar -->
            <div class="border-t border-[#1E6482] px-5 py-2.5 flex items-center justify-between text-xs text-[#A2C5D8]">
                <span class="font-mono text-[11px] uppercase font-bold tracking-wide">Sound Synthesizer</span>
                <button type="button" id="sound-toggle-btn" class="flex items-center gap-2 rounded-lg border border-[#182830] bg-[#1E6482] px-2.5 py-1 text-xs font-bold text-[#F7E6CB] hover:bg-[#A2C5D8] hover:text-[#182830] transition cursor-pointer">
                    <span id="sound-icon-container" class="inline-flex">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    </span>
                    <span id="sound-label" class="font-mono text-[10px]">Audio ON</span>
                </button>
            </div>

            <!-- Staff Identity Card & Logout -->
            <div class="border-t-2 border-[#182830] bg-[#1E6482] p-4 lg:p-5">
                <div class="flex items-center gap-3 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-[#182830] shadow-[2px_2px_0px_#182830]">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#CB1B03] font-recoleta text-base font-bold text-white shadow-[1px_1px_0px_#182830]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold leading-tight">{{ auth()->user()->name }}</p>
                        <p class="font-mono text-[10px] font-semibold uppercase text-[#25799B]">{{ auth()->user()->role === 'staff' ? 'Counter Staff' : 'Store Admin' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" id="staff-logout-form">
                        @csrf
                        <button type="button" onclick="confirmStaffLogout()" title="Sign out station" class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#F7E6CB] text-[#CB1B03] shadow-[1px_1px_0px_#182830] transition hover:bg-[#CB1B03] hover:text-white active:translate-x-0.5 active:translate-y-0.5 cursor-pointer">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Screen Canvas (#F7E6CB) with directly proportional margin for desktop/laptop -->
        <main class="min-w-0 flex-1 lg:ml-72 xl:ml-80 print:ml-0 print:p-0">
            <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8 print:p-0 print:max-w-none">
                <!-- Session Flash Alerts in Retro Style -->
                @if(session('status'))
                    <div class="mb-6 flex items-center justify-between rounded-xl border-2 border-[#182830] bg-[#A2C5D8] px-4 py-3 text-sm font-bold text-[#182830] shadow-[3px_3px_0px_#182830] print:hidden">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#FFFDF8] text-base text-[#182830]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span>{{ session('status') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-xl font-bold hover:text-[#CB1B03] cursor-pointer">×</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 flex items-center justify-between rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-4 py-3 text-sm font-bold text-[#FFFDF8] shadow-[3px_3px_0px_#182830] print:hidden">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#FFFDF8] text-base text-[#CB1B03]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </span>
                            <span>{{ $errors->first() }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-xl font-bold hover:text-[#F7E6CB] cursor-pointer">×</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
@else
    @yield('content')
@endauth

<!-- Web Audio Sound Engine & Mobile Drawer Script -->
<script>
    // Web Audio Synthesizer for tactile laundromat feedback
    const SoundFx = (function() {
        let audioCtx = null;
        let isEnabled = localStorage.getItem('trowa_audio') !== 'false';

        function initContext() {
            if (!audioCtx && (window.AudioContext || window.webkitAudioContext)) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
        }

        function playTone(freq, type, duration, gainVal) {
            if (!isEnabled) return;
            try {
                initContext();
                if (!audioCtx) return;
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = type || 'sine';
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                gain.gain.setValueAtTime(gainVal || 0.08, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + duration);
            } catch (e) {}
        }

        return {
            click: function() { playTone(540, 'triangle', 0.06, 0.05); },
            success: function() { 
                playTone(523, 'sine', 0.1, 0.06); 
                setTimeout(() => playTone(659, 'sine', 0.15, 0.07), 80);
                setTimeout(() => playTone(784, 'sine', 0.22, 0.08), 160);
            },
            toggle: function() {
                isEnabled = !isEnabled;
                localStorage.setItem('trowa_audio', isEnabled);
                return isEnabled;
            },
            getState: function() { return isEnabled; }
        };
    })();

    // Initialize sound toggle button UI
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('sound-toggle-btn');
        const iconContainer = document.getElementById('sound-icon-container');
        const label = document.getElementById('sound-label');

        function updateSoundUI() {
            const active = SoundFx.getState();
            if (iconContainer) {
                if (active) {
                    iconContainer.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>';
                } else {
                    iconContainer.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>';
                }
            }
            if (label) label.textContent = active ? 'Audio ON' : 'Audio OFF';
        }
        updateSoundUI();

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const state = SoundFx.toggle();
                updateSoundUI();
                if (state) SoundFx.success();
            });
        }

        // Tactile button sound on click
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('button, .btn-primary, .btn-secondary, .retro-btn-primary, .retro-btn-secondary, a.rounded-xl');
            if (btn && !btn.id?.includes('sound-toggle')) {
                SoundFx.click();
            }
        });

        // Mobile drawer handlers
        const mobileToggle = document.getElementById('mobile-menu-toggle');
        const mobileClose = document.getElementById('mobile-menu-close');
        const backdrop = document.getElementById('mobile-menu-backdrop');
        const drawer = document.getElementById('sidebar-drawer');

        function openDrawer() {
            drawer?.classList.remove('-translate-x-full');
            backdrop?.classList.remove('hidden');
        }
        function closeDrawer() {
            drawer?.classList.add('-translate-x-full');
            backdrop?.classList.add('hidden');
        }

        mobileToggle?.addEventListener('click', openDrawer);
        mobileClose?.addEventListener('click', closeDrawer);
        backdrop?.addEventListener('click', closeDrawer);

        // Global Modal Open & Close Triggers
        document.querySelectorAll('[data-modal-open]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalId = btn.getAttribute('data-modal-open');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.getElementById('wizard-customer-name')?.focus();
                } else if (modalId === 'order-modal') {
                    window.location.href = "{{ route('orders.index') }}?open_intake=1";
                }
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('[data-modal]') || btn.closest('.fixed.inset-0');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        });

        document.querySelectorAll('[data-modal]').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-modal]:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            }
        });

        // ======================================================================
        // Global Trowa Retro Laundromat Loading System
        // ======================================================================
        window.TrowaLoading = {
            show: function(opts) {
                const modal = document.getElementById('global-wash-modal');
                if (!modal) return;
                if (opts?.header) {
                    const h = document.getElementById('global-wash-header');
                    if (h) h.textContent = opts.header;
                }
                if (opts?.stage) {
                    const s = document.getElementById('global-wash-stage');
                    if (s) s.textContent = opts.stage;
                }
                if (opts?.title) {
                    const t = document.getElementById('global-wash-title');
                    if (t) t.textContent = opts.title;
                }
                if (opts?.message) {
                    const m = document.getElementById('global-wash-msg');
                    if (m) m.textContent = opts.message;
                }
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                if (window.SoundFx) SoundFx.click();
            },
            hide: function() {
                const modal = document.getElementById('global-wash-modal');
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            },
            startTopLoader: function() {
                const inner = document.getElementById('trowa-top-loader-inner');
                if (!inner) return;
                inner.style.opacity = '1';
                inner.style.transition = 'width 0.4s ease-out';
                inner.style.width = '75%';
            },
            finishTopLoader: function() {
                const inner = document.getElementById('trowa-top-loader-inner');
                if (!inner) return;
                inner.style.width = '100%';
                setTimeout(() => {
                    inner.style.opacity = '0';
                    setTimeout(() => {
                        inner.style.width = '0%';
                        inner.style.opacity = '1';
                    }, 300);
                }, 200);
            }
        };

        // ======================================================================
        // Global Trowa Retro Confirmation Modal System
        // ======================================================================
        window.TrowaConfirm = function(options, onConfirm, onCancel) {
            const modal = document.getElementById('retro-confirm-modal');
            if (!modal) {
                if (window.confirm(options.message || 'Are you sure?')) {
                    if (typeof onConfirm === 'function') onConfirm();
                } else {
                    if (typeof onCancel === 'function') onCancel();
                }
                return;
            }

            const titleEl = document.getElementById('retro-confirm-title');
            const msgEl = document.getElementById('retro-confirm-message');
            const hintEl = document.getElementById('retro-confirm-hint');
            const badgeEl = document.getElementById('retro-confirm-badge');
            const detailsEl = document.getElementById('retro-confirm-details');
            const proceedBtn = document.getElementById('retro-confirm-btn-proceed');
            const proceedLabel = document.getElementById('retro-confirm-btn-proceed-label');
            const cancelBtn = document.getElementById('retro-confirm-btn-cancel');
            const closeBtn = document.getElementById('retro-confirm-btn-close');
            const iconWrap = document.getElementById('retro-confirm-icon-wrap');
            const iconDanger = document.getElementById('retro-confirm-icon-danger');
            const iconPrimary = document.getElementById('retro-confirm-icon-primary');
            const iconCheck = document.getElementById('retro-confirm-icon-check');

            const type = options.type || 'primary'; // 'danger', 'primary', 'check', 'warning'
            
            if (titleEl) titleEl.textContent = options.title || 'Please Confirm';
            if (msgEl) msgEl.textContent = options.message || 'Are you sure you want to proceed?';
            if (hintEl) hintEl.textContent = options.hint || 'Please review the details below before confirming.';
            if (badgeEl) badgeEl.textContent = options.badge || (type === 'danger' ? 'Action Warning' : 'Confirmation');

            if (detailsEl) {
                if (options.details) {
                    detailsEl.innerHTML = options.details;
                    detailsEl.classList.remove('hidden');
                } else {
                    detailsEl.innerHTML = '';
                    detailsEl.classList.add('hidden');
                }
            }

            // Type styling
            if (iconWrap && proceedBtn) {
                if (type === 'danger') {
                    iconWrap.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#CB1B03] text-white shadow-[2px_2px_0px_#182830]';
                    proceedBtn.className = 'rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-5 py-2 font-recoleta text-sm font-bold text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer flex items-center gap-1.5';
                    if (iconDanger) iconDanger.classList.remove('hidden');
                    if (iconPrimary) iconPrimary.classList.add('hidden');
                    if (iconCheck) iconCheck.classList.add('hidden');
                } else if (type === 'check') {
                    iconWrap.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-emerald-700 text-white shadow-[2px_2px_0px_#182830]';
                    proceedBtn.className = 'rounded-xl border-2 border-[#182830] bg-emerald-700 px-5 py-2 font-recoleta text-sm font-bold text-white shadow-[3px_3px_0px_#182830] hover:bg-emerald-800 transition cursor-pointer flex items-center gap-1.5';
                    if (iconDanger) iconDanger.classList.add('hidden');
                    if (iconPrimary) iconPrimary.classList.add('hidden');
                    if (iconCheck) iconCheck.classList.remove('hidden');
                } else if (type === 'warning') {
                    iconWrap.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-amber-500 text-[#182830] shadow-[2px_2px_0px_#182830]';
                    proceedBtn.className = 'rounded-xl border-2 border-[#182830] bg-[#182830] px-5 py-2 font-recoleta text-sm font-bold text-white shadow-[3px_3px_0px_#182830] hover:bg-slate-800 transition cursor-pointer flex items-center gap-1.5';
                    if (iconDanger) iconDanger.classList.remove('hidden');
                    if (iconPrimary) iconPrimary.classList.add('hidden');
                    if (iconCheck) iconCheck.classList.add('hidden');
                } else {
                    iconWrap.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#25799B] text-white shadow-[2px_2px_0px_#182830]';
                    proceedBtn.className = 'rounded-xl border-2 border-[#182830] bg-[#25799B] px-5 py-2 font-recoleta text-sm font-bold text-white shadow-[3px_3px_0px_#182830] hover:bg-[#1E6482] transition cursor-pointer flex items-center gap-1.5';
                    if (iconDanger) iconDanger.classList.add('hidden');
                    if (iconPrimary) iconPrimary.classList.remove('hidden');
                    if (iconCheck) iconCheck.classList.add('hidden');
                }
            }

            if (proceedLabel) proceedLabel.textContent = options.confirmText || 'Yes, Proceed';
            if (cancelBtn) cancelBtn.textContent = options.cancelText || 'Cancel';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (window.SoundFx) SoundFx.click();

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                cleanup();
            }

            function onProceed() {
                closeModal();
                if (typeof onConfirm === 'function') onConfirm();
            }

            function onCancel() {
                closeModal();
                if (typeof onCancel === 'function') onCancel();
            }

            function cleanup() {
                proceedBtn?.removeEventListener('click', onProceed);
                cancelBtn?.removeEventListener('click', onCancel);
                closeBtn?.removeEventListener('click', onCancel);
                modal?.removeEventListener('click', onBackdrop);
                document.removeEventListener('keydown', onKey);
            }

            function onBackdrop(e) {
                if (e.target === modal) onCancel();
            }

            function onKey(e) {
                if (e.key === 'Escape') onCancel();
            }

            proceedBtn?.addEventListener('click', onProceed);
            cancelBtn?.addEventListener('click', onCancel);
            closeBtn?.addEventListener('click', onCancel);
            modal?.addEventListener('click', onBackdrop);
            document.addEventListener('keydown', onKey);
        };

        // Declarative [data-confirm] Interceptor for Forms (Capturing Phase)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-confirm-confirmed')) {
                form.removeAttribute('data-confirm-confirmed');
                return;
            }

            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const msg = form.getAttribute('data-confirm');
                const title = form.getAttribute('data-confirm-title') || 'Confirm Action';
                const type = form.getAttribute('data-confirm-type') || 'primary';
                const confirmText = form.getAttribute('data-confirm-btn') || 'Yes, Proceed';
                const details = form.getAttribute('data-confirm-details') || '';

                window.TrowaConfirm({
                    title: title,
                    message: msg,
                    type: type,
                    confirmText: confirmText,
                    details: details
                }, function() {
                    form.setAttribute('data-confirm-confirmed', 'true');
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        form.submit();
                    }
                });
            }
        }, true);

        // Declarative [data-confirm] Interceptor for Buttons & Links (Capturing Phase)
        document.addEventListener('click', function(e) {
            const el = e.target.closest('[data-confirm]:not(form)');
            if (!el) return;
            if (el.hasAttribute('data-confirm-confirmed')) {
                el.removeAttribute('data-confirm-confirmed');
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();

            const msg = el.getAttribute('data-confirm');
            const title = el.getAttribute('data-confirm-title') || 'Confirm Action';
            const type = el.getAttribute('data-confirm-type') || 'primary';
            const confirmText = el.getAttribute('data-confirm-btn') || 'Yes, Proceed';
            const details = el.getAttribute('data-confirm-details') || '';

            window.TrowaConfirm({
                title: title,
                message: msg,
                type: type,
                confirmText: confirmText,
                details: details
            }, function() {
                el.setAttribute('data-confirm-confirmed', 'true');
                el.click();
            });
        }, true);

        window.confirmStaffLogout = function() {
            if (window.TrowaConfirm) {
                window.TrowaConfirm({
                    title: 'Confirm Sign Out',
                    message: 'Are you sure you want to end your current staff session and sign out from Trowa Laundry station?',
                    hint: 'Make sure any pending customer intake or cash drawer transactions are saved.',
                    badge: 'Station Security',
                    type: 'warning',
                    confirmText: 'Yes, Sign Out Station',
                    cancelText: 'Stay Signed In'
                }, function() {
                    document.getElementById('staff-logout-form')?.submit();
                });
            } else if (confirm('Are you sure you want to sign out from Trowa Laundry station?')) {
                document.getElementById('staff-logout-form')?.submit();
            }
        };

        // Form Submit Loading Interceptor
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-no-loading')) return;
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;

            const submitBtn = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled])');
            if (submitBtn) {
                const originalHtml = submitBtn.innerHTML;
                submitBtn.setAttribute('data-original-html', originalHtml);
                submitBtn.classList.add('btn-wash-loading');
                setTimeout(() => {
                    submitBtn.disabled = true;
                }, 20);

                const txt = submitBtn.textContent.trim().toLowerCase();
                let loadingLabel = 'Processing...';

                if (form.id === 'wizard-intake-form') {
                    loadingLabel = 'Weighing & Registering...';
                    window.TrowaLoading.show({
                        header: '⚡ TROWA INTAKE TERMINAL ⚡',
                        stage: 'WEIGHING & WASHING',
                        title: 'Registering Laundry Ticket...',
                        message: 'Calibrating wash load, allocating supplies, and prepping machine bay...'
                    });
                } else if (form.id === 'claim-payment-form') {
                    loadingLabel = 'Settling & Releasing...';
                    window.TrowaLoading.show({
                        header: '⚡ CASHIER & CLAIM TERMINAL ⚡',
                        stage: 'SETTLEMENT',
                        title: 'Settling Laundry Ticket...',
                        message: 'Recording cash register receipt and releasing fresh clean garments...'
                    });
                } else if (txt.includes('sign in') || txt.includes('login')) {
                    loadingLabel = 'Spinning Up Station...';
                } else if (txt.includes('move to')) {
                    loadingLabel = 'Transitioning Drum...';
                } else if (txt.includes('done') || txt.includes('claim')) {
                    loadingLabel = 'Releasing Order...';
                } else if (txt.includes('save') || txt.includes('add') || txt.includes('update') || txt.includes('create')) {
                    loadingLabel = 'Saving Records...';
                } else if (txt.includes('filter')) {
                    loadingLabel = 'Filtering Queue...';
                }

                // Retro washing machine spinning SVG
                const washerSpinnerSvg = `<svg class="h-4 w-4 animate-wash-spin inline-block mr-1.5 text-current shrink-0 align-middle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="4"/><circle cx="12" cy="13" r="5" stroke-dasharray="24" stroke-dashoffset="6"/><circle cx="12" cy="13" r="2" fill="currentColor"/><circle cx="6" cy="6" r="1" fill="currentColor"/><circle cx="9" cy="6" r="1" fill="currentColor"/></svg>`;
                submitBtn.innerHTML = `${washerSpinnerSvg}<span>${loadingLabel}</span>`;
            }

            window.TrowaLoading.startTopLoader();
        });

        // Top Navigation Loading Bar on Page Links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.getAttribute('target') === '_blank' || link.hasAttribute('download')) {
                return;
            }
            window.TrowaLoading.startTopLoader();
        });

        // Bfcache & History restoration
        window.addEventListener('pageshow', function() {
            window.TrowaLoading.hide();
            window.TrowaLoading.finishTopLoader();
            document.querySelectorAll('.btn-wash-loading').forEach(btn => {
                btn.classList.remove('btn-wash-loading');
                btn.removeAttribute('disabled');
                const orig = btn.getAttribute('data-original-html');
                if (orig) btn.innerHTML = orig;
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
