@extends('layouts_app')

@section('content')
<div class="space-y-8 pb-12">

    <!-- Top Customer Welcome & Profile Bar -->
    <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[5px_5px_0px_#182830]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-3 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[2px_2px_0px_#182830]">
                    <!-- Retro Wash Tub SVG -->
                    <svg class="h-8 w-8 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18l-2 13H5L3 6z"/>
                        <circle cx="12" cy="13" r="3.5"/>
                        <path d="M8 2h8"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block rounded-md border border-[#182830] bg-[#25799B] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-white shadow-[1px_1px_0px_#182830]">
                            Verified Customer Member
                        </span>
                        <span class="font-mono text-xs text-[#25799B]">· Member #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h1 class="font-recoleta text-2xl sm:text-3xl font-black text-[#182830] mt-0.5 leading-tight">
                        Hello, {{ $user->name }}!
                    </h1>
                    <p class="font-mono text-xs text-slate-600 mt-0.5">
                        {{ $customer->address ?? $user->address ?? 'Registered Residence' }} · {{ $customer->contact_number ?? $user->phone ?? 'No phone' }}
                    </p>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="btn-open-intake-wizard" class="rounded-2xl border-3 border-[#182830] bg-[#CB1B03] px-4 py-2.5 font-recoleta text-sm font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 transition cursor-pointer flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>New Laundry Intake Request</span>
                </button>

                <form method="POST" action="{{ route('customer.logout') }}" id="customer-logout-form" class="inline">
                    @csrf
                    <button type="button" onclick="confirmCustomerLogout()" class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 font-mono text-xs font-bold text-slate-700 shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>



    <!-- PANE 1: CUSTOMER DASHBOARD (Mascot & Filterable Expense Analytics) -->
    <div id="cust-pane-dashboard" class="cust-tab-content {{ ($currentTab ?? 'dashboard') === 'dashboard' ? '' : 'hidden' }} space-y-6">
        
        <!-- Interactive Mascot & Laundry Expense Section Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- TALKING LAUNDRO-BOT MASCOT WIDGET (4 cols) -->
            <div class="lg:col-span-5 retro-panel p-5 bg-[#F7E6CB]/50 space-y-4 relative overflow-hidden">
                <!-- Floating Decorative Background Bubbles -->
                <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-[#A2C5D8]/20 pointer-events-none animate-pulse"></div>
                <div class="absolute right-14 top-2 w-10 h-10 rounded-full bg-white/40 pointer-events-none"></div>

                <div class="flex items-center justify-between border-b-2 border-[#182830]/15 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h3 class="font-recoleta text-base font-bold text-[#182830]">Trowa-Bot</h3>
                    </div>
                    <span id="mascot-badge" class="badge border-[#182830] bg-[#CB1B03] text-white text-[10px] font-mono font-bold">
                        {{ $mascotJokes[0]['badge'] }}
                    </span>
                </div>

                <!-- Mascot Avatar & Animated Interactive Speech Bubble -->
                <div class="flex items-start gap-4">
                    <!-- Mascot Animated Character -->
                    <div class="flex flex-col items-center shrink-0">
                        <div class="relative cursor-pointer select-none transition-transform duration-200 hover:scale-105 active:scale-95 group" id="mascot-avatar" title="Click me for another joke & a quick spin cycle!">
                            <!-- Authentic Trowa-Bot Mascot SVG from Login Page -->
                            <svg id="dash-trowa-robot-svg" class="w-24 sm:w-28 h-auto drop-shadow-[0_4px_6px_rgba(20,54,68,0.25)] transition-transform duration-300 ease-out" viewBox="0 0 280 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <!-- Body Clip for Two-Tone Cel-Shading -->
                                    <clipPath id="dash-robot-body-clip">
                                        <rect x="40" y="16" width="200" height="236" rx="38" />
                                    </clipPath>
                                    <!-- Drum Porthole Clip -->
                                    <clipPath id="dash-robot-drum-clip">
                                        <circle cx="140" cy="136" r="43" />
                                    </clipPath>
                                </defs>

                                <!-- Ground Cast Shadow -->
                                <ellipse cx="140" cy="275" rx="94" ry="9" fill="#143644" opacity="0.45" />

                                <!-- Bottom Feet -->
                                <rect x="70" y="248" width="34" height="18" rx="6" fill="#182830" />
                                <rect x="176" y="248" width="34" height="18" rx="6" fill="#182830" />

                                <!-- Main Machine Body (Split Cel-Shading) -->
                                <g clip-path="url(#dash-robot-body-clip)">
                                    <!-- Left half base (Crisp White) -->
                                    <rect x="40" y="16" width="200" height="236" fill="#FFFFFF" />
                                    <!-- Right half base (Soft Shadow Grey/Cyan) -->
                                    <rect x="140" y="16" width="100" height="236" fill="#E5ECEF" />
                                    <!-- Top Seam Line -->
                                    <line x1="40" y1="68" x2="240" y2="68" stroke="#182830" stroke-width="4" />
                                    <!-- Bottom Seam Line -->
                                    <line x1="40" y1="204" x2="240" y2="204" stroke="#182830" stroke-width="4" />
                                </g>

                                <!-- Machine Outer Border -->
                                <rect x="40" y="16" width="200" height="236" rx="38" fill="none" stroke="#182830" stroke-width="7" />

                                <!-- Top Section: Ventilation Slats (Left) -->
                                <g id="dash-robot-vents">
                                    <rect x="54" y="27" width="38" height="3.2" rx="1.6" fill="#182830" />
                                    <rect x="54" y="34" width="38" height="3.2" rx="1.6" fill="#182830" />
                                    <rect x="54" y="41" width="38" height="3.2" rx="1.6" fill="#182830" />
                                    <rect x="54" y="48" width="38" height="3.2" rx="1.6" fill="#182830" />
                                    <rect x="54" y="55" width="38" height="3.2" rx="1.6" fill="#182830" />
                                </g>

                                <!-- Left Indicator Status Dots (Below Top Seam) -->
                                <circle cx="58" cy="85" r="4" fill="#182830" />
                                <circle cx="58" cy="99" r="4" fill="#182830" />

                                <!-- Center Digital Display Screen -->
                                <g id="dash-robot-screen">
                                    <rect x="122" y="32" width="38" height="18" rx="4" fill="#182830" />
                                    <rect x="125" y="35" width="32" height="12" rx="2" fill="#0284C7" />
                                    <text x="141" y="44" font-family="monospace" font-size="7.5" font-weight="900" fill="#BAE6FD" text-anchor="middle" letter-spacing="1">LIVE</text>
                                </g>

                                <!-- Right Rotary Dial / Knob -->
                                <g id="dash-robot-dial">
                                    <circle cx="188" cy="41" r="15" fill="#182830" />
                                    <circle cx="188" cy="41" r="11" fill="#4B5660" />
                                    <rect x="186.5" y="32" width="3" height="5" rx="1.5" fill="#FFFDF8" />
                                </g>

                                <!-- Porthole Outer Door Frame -->
                                <g id="dash-robot-door-assembly">
                                    <!-- Top Arched Door Visor / Handle -->
                                    <path d="M 106 94 C 106 80, 174 80, 174 94" fill="#FFFFFF" stroke="#182830" stroke-width="7" stroke-linejoin="round" />
                                    
                                    <!-- Outer Circular Door Ring -->
                                    <circle cx="140" cy="136" r="50" fill="#FFFFFF" stroke="#182830" stroke-width="7" />
                                    <!-- Right Door Rim Shading -->
                                    <path d="M 140 86 A 50 50 0 0 1 190 136 A 50 50 0 0 1 140 186 Z" fill="#E5ECEF" />
                                    
                                    <!-- Door Latch Block (Right Side) -->
                                    <rect x="182" y="118" width="20" height="36" rx="6" fill="#8C9CA8" stroke="#182830" stroke-width="4.5" />
                                    <rect x="188" y="126" width="7" height="20" rx="2" fill="#182830" opacity="0.3" />

                                    <!-- Bottom Hinge Notches -->
                                    <rect x="148" y="184" width="5" height="5" rx="1" fill="#182830" />
                                    <rect x="164" y="182" width="5" height="5" rx="1" fill="#182830" />
                                </g>

                                <!-- Inside Drum Porthole -->
                                <g clip-path="url(#dash-robot-drum-clip)">
                                    <!-- Dark Drum Interior -->
                                    <circle cx="140" cy="136" r="43" fill="#182830" />

                                    <!-- Spinning Character Face Group -->
                                    <g id="dash-robot-spinning-face" style="transform-origin: 140px 136px; transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);">
                                        <!-- Coral Round Face Character -->
                                        <circle cx="140" cy="136" r="39" fill="#EF6D65" />
                                        <!-- Face Bottom Shadow -->
                                        <path d="M 101 136 A 39 39 0 0 0 179 136 Q 140 168 101 136 Z" fill="#DC544B" opacity="0.35" />

                                        <!-- Cheeks (Blush) -->
                                        <rect x="114" y="141" width="8" height="6" rx="2" fill="#FF8D85" />
                                        <rect x="158" y="141" width="8" height="6" rx="2" fill="#FF8D85" />

                                        <!-- Smile Mouth -->
                                        <path d="M 134 139 Q 140 147 146 139" fill="none" stroke="#182830" stroke-width="4" stroke-linecap="round" />

                                        <!-- Eyes: OPEN STATE -->
                                        <g id="dash-robot-eyes">
                                            <!-- Left Eye -->
                                            <g>
                                                <circle cx="116" cy="128" r="11" fill="#182830" />
                                                <circle cx="112.5" cy="124.5" r="3.8" fill="#FFFFFF" />
                                                <circle cx="119.5" cy="131" r="1.5" fill="#FFFFFF" />
                                            </g>
                                            <!-- Right Eye -->
                                            <g>
                                                <circle cx="164" cy="128" r="11" fill="#182830" />
                                                <circle cx="160.5" cy="124.5" r="3.8" fill="#FFFFFF" />
                                                <circle cx="167.5" cy="131" r="1.5" fill="#FFFFFF" />
                                            </g>
                                        </g>
                                    </g>
                                </g>

                                <!-- Bottom Filter Hatch Door (Right) -->
                                <g id="dash-robot-filter-door">
                                    <rect x="178" y="212" width="26" height="22" rx="5" fill="#8C9CA8" stroke="#182830" stroke-width="4" />
                                    <rect x="183" y="217" width="16" height="12" rx="2" fill="#182830" opacity="0.3" />
                                </g>
                            </svg>
                            <!-- Animated wink star badge -->
                            <span class="absolute -top-1 right-2 flex h-4 w-4 items-center justify-center rounded-full bg-[#CB1B03] text-white text-[9px] font-mono font-black ring-2 ring-[#FFFDF8]">!</span>
                        </div>
                        <span class="font-mono text-[9px] text-[#25799B] font-bold mt-1">Tap me for jokes!</span>
                    </div>

                    <!-- Comic Speech Bubble with No Sound -->
                    <div class="flex-1 relative rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-3.5 shadow-[3px_3px_0px_#182830]">
                        <!-- Bubble triangular pointer -->
                        <div class="absolute -left-2 top-5 h-4 w-4 rotate-45 border-l-2 border-b-2 border-[#182830] bg-[#FFFDF8]"></div>
                        
                        <p id="mascot-speech" class="font-sans text-xs font-semibold text-[#182830] leading-relaxed">
                            {{ $mascotJokes[0]['speech'] }}
                        </p>

                        <div class="mt-2.5 pt-2 border-t border-[#182830]/10 flex items-center justify-between">
                            <span class="font-mono text-[10px] text-slate-500">No sounds · Pure laundromat humor</span>
                            <button type="button" id="btn-next-joke" class="rounded-lg border border-[#182830] bg-[#F7E6CB] px-2 py-0.5 font-mono text-[10px] font-bold text-[#182830] hover:bg-slate-200 transition cursor-pointer">
                                Next Joke ➔
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mascot Quick Action Pill -->
                <div class="rounded-xl border border-[#182830]/20 bg-[#FFFDF8] p-3 flex items-center justify-between gap-3 text-xs font-mono">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Patron Lifetime Stats:</span>
                        <strong class="text-[#182830]">{{ $customerAnalytics['all_time_orders'] }} visits · {{ $customerAnalytics['all_time_kg'] }} kg washed</strong>
                    </div>
                    <button type="button" onclick="document.getElementById('btn-open-intake-wizard')?.click()" class="rounded-lg border border-[#182830] bg-[#25799B] text-white px-2.5 py-1 text-[11px] font-bold hover:bg-[#1E6482] transition cursor-pointer">
                        Wash Now
                    </button>
                </div>
            </div>

            <!-- CUSTOMER EXPENSES & SPENDING ANALYTICS (7 cols) -->
            <div class="lg:col-span-7 retro-panel p-5 bg-[#FFFDF8] space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-2.5 gap-2">
                    <div>
                        <h3 class="font-recoleta text-base font-bold text-[#182830]">My Laundry Spending &amp; Expenses</h3>
                        <p class="font-mono text-xs text-[#25799B]">Track your personal laundry costs across weeks and months</p>
                    </div>

                    <!-- Expense Timeframe Filter Controls -->
                    <div class="flex items-center gap-1 font-mono text-xs">
                        <a href="{{ route('customer.portal', ['tab' => 'dashboard', 'expense_filter' => 'week']) }}" 
                           class="rounded-lg px-2.5 py-1 font-bold border {{ $customerAnalytics['filter'] === 'week' ? 'border-[#182830] bg-[#CB1B03] text-white shadow-[1px_1px_0px_#182830]' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                            This Week
                        </a>
                        <a href="{{ route('customer.portal', ['tab' => 'dashboard', 'expense_filter' => 'month']) }}" 
                           class="rounded-lg px-2.5 py-1 font-bold border {{ $customerAnalytics['filter'] === 'month' ? 'border-[#182830] bg-[#CB1B03] text-white shadow-[1px_1px_0px_#182830]' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                            This Month
                        </a>
                        <a href="{{ route('customer.portal', ['tab' => 'dashboard', 'expense_filter' => 'year']) }}" 
                           class="rounded-lg px-2.5 py-1 font-bold border {{ $customerAnalytics['filter'] === 'year' ? 'border-[#182830] bg-[#CB1B03] text-white shadow-[1px_1px_0px_#182830]' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                            Year
                        </a>
                        <a href="{{ route('customer.portal', ['tab' => 'dashboard', 'expense_filter' => 'all']) }}" 
                           class="rounded-lg px-2.5 py-1 font-bold border {{ $customerAnalytics['filter'] === 'all' ? 'border-[#182830] bg-[#CB1B03] text-white shadow-[1px_1px_0px_#182830]' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                            All-Time
                        </a>
                    </div>
                </div>

                <!-- 3 Spending Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-2xl border-2 border-[#182830] bg-[#F7E6CB]/40 p-3.5 shadow-[2px_2px_0px_#182830]">
                        <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Period Spend</span>
                        <p class="font-recoleta text-2xl font-black text-[#CB1B03] mt-1 leading-tight">
                            ₱{{ number_format($customerAnalytics['total_spent_filtered'], 2) }}
                        </p>
                        <span class="font-mono text-[10px] text-slate-600 block mt-0.5">
                            Filter: {{ ucfirst($customerAnalytics['filter']) }}
                        </span>
                    </div>

                    <div class="rounded-2xl border-2 border-[#182830] bg-[#A2C5D8]/20 p-3.5 shadow-[2px_2px_0px_#182830]">
                        <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Laundry Volume</span>
                        <p class="font-recoleta text-2xl font-black text-[#182830] mt-1 leading-tight">
                            {{ $customerAnalytics['total_kg_filtered'] }} kg
                        </p>
                        <span class="font-mono text-[10px] text-slate-600 block mt-0.5">
                            Across {{ $customerAnalytics['total_orders_filtered'] }} visit{{ $customerAnalytics['total_orders_filtered'] === 1 ? '' : 's' }}
                        </span>
                    </div>

                    <div class="rounded-2xl border-2 border-[#182830] bg-emerald-50/70 border-emerald-800 p-3.5 shadow-[2px_2px_0px_#182830]">
                        <span class="font-mono text-[10px] font-bold uppercase text-emerald-800">Avg Cost / Visit</span>
                        <p class="font-recoleta text-2xl font-black text-emerald-950 mt-1 leading-tight">
                            ₱{{ number_format($customerAnalytics['avg_order_value'], 2) }}
                        </p>
                        <span class="font-mono text-[10px] text-emerald-700 block mt-0.5">
                            Based on your history
                        </span>
                    </div>
                </div>

                <!-- Budget & Savings Advice Banner -->
                <div class="rounded-xl border border-[#182830]/20 bg-[#F7E6CB]/20 p-3 flex items-start gap-3">
                    <svg class="h-5 w-5 text-[#25799B] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <div class="text-xs">
                        <strong class="font-sans font-bold text-[#182830] block">Save with Trowa Bulk Loads:</strong>
                        <p class="text-slate-600 mt-0.5">
                            Each commercial washing machine accommodates up to <strong>8.0 kg</strong>. Combining garments into full 8 kg batches minimizes your cost per kilogram!
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Dashboard Quick Navigation Hub Cards to Separated Modules -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Active Cycle Hub Card -->
            <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#182830] flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] text-[#182830] shadow-[1px_1px_0px_#182830]">
                        <svg class="h-6 w-6 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Active Wash Fleet</span>
                        <h4 class="font-recoleta text-base sm:text-lg font-bold text-[#182830]">
                            {{ $activeOrders->count() }} Order{{ $activeOrders->count() === 1 ? '' : 's' }} in Progress
                        </h4>
                        <p class="font-mono text-xs text-slate-500">Live cycle stage &amp; washer tracking</p>
                    </div>
                </div>
                <a href="{{ route('customer.portal', ['tab' => 'active']) }}" class="retro-btn-secondary text-xs px-3 py-1.5 shrink-0 inline-flex items-center">
                    Track Live ➔
                </a>
            </div>

            <!-- Past History Hub Card -->
            <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[3px_3px_0px_#182830] flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[1px_1px_0px_#182830]">
                        <svg class="h-6 w-6 text-[#182830]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Past Laundry History</span>
                        <h4 class="font-recoleta text-base sm:text-lg font-bold text-[#182830]">
                            {{ $pastOrders->total() }} Past Visit{{ $pastOrders->total() === 1 ? '' : 's' }} Recorded
                        </h4>
                        <p class="font-mono text-xs text-slate-500">Receipts, item breakdowns &amp; reviews</p>
                    </div>
                </div>
                <a href="{{ route('customer.portal', ['tab' => 'history']) }}" class="retro-btn-secondary text-xs px-3 py-1.5 shrink-0 inline-flex items-center">
                    View History ➔
                </a>
            </div>
        </div>

    </div>

    <!-- PANE 2: ACTIVE ORDERS & TRACKER (Filtered by Tab) -->
    <div id="cust-pane-active" class="cust-tab-content {{ ($currentTab ?? 'dashboard') === 'active' ? '' : 'hidden' }} space-y-4">
        <div class="flex items-center justify-between border-b-2 border-[#182830]/15 pb-2">
            <div class="flex items-center gap-2">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <h2 class="font-recoleta text-xl sm:text-2xl font-black text-[#182830]">
                    Active Laundry Orders &amp; Live Cycle Tracker
                </h2>
            </div>
            <span class="font-mono text-xs font-bold text-[#25799B]">
                {{ $activeOrders->count() }} active order{{ $activeOrders->count() === 1 ? '' : 's' }}
            </span>
        </div>

        @if($activeOrders->isEmpty())
            <div class="rounded-3xl border-3 border-dashed border-[#182830]/30 bg-[#FFFDF8] p-8 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#F7E6CB] text-[#182830] shadow-[2px_2px_0px_#182830] mb-3">
                    <svg class="h-8 w-8 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                </div>
                <h3 class="font-recoleta text-lg font-bold text-[#182830]">No Active Laundry Right Now</h3>
                <p class="text-xs text-[#25799B] font-medium max-w-sm mx-auto mt-1">
                    Ready for a fresh wash? Tap the button below to submit your garments, select your favorite soap, and let our 8-machine fleet take care of it!
                </p>
                <button type="button" onclick="document.getElementById('btn-open-intake-wizard')?.click()" class="mt-4 inline-flex items-center gap-2 rounded-2xl border-2 border-[#182830] bg-[#CB1B03] px-5 py-2.5 font-recoleta text-xs font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer">
                    <span>Submit New Laundry Intake Now ➔</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($activeOrders as $order)
                    @php
                        $isPendingConfirm = ($order->status === 'pending_confirmation');
                        $isReceived = ($order->status === 'received');
                        $isWashing = in_array($order->status, ['washing', 'in-progress']);
                        $isDrying = ($order->status === 'drying');
                        $isIroning = ($order->status === 'ironing');
                        $isFolding = ($order->status === 'folding');
                        $isReady = in_array($order->status, ['ready_for_pickup', 'ready']);

                        $stagePercent = match($order->status) {
                            'pending_confirmation' => 10,
                            'received' => 25,
                            'washing', 'in-progress' => 50,
                            'drying' => 70,
                            'ironing' => 85,
                            'folding' => 90,
                            'ready_for_pickup', 'ready' => 100,
                            default => 20
                        };
                    @endphp
                    <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-5 sm:p-6 shadow-[6px_6px_0px_#182830]">
                        
                        <!-- Order Top Metadata Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-[#182830]/15 pb-4 mb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-lg font-black text-[#CB1B03]">#{{ $order->order_number }}</span>
                                    <span class="font-mono text-xs text-slate-500">· Placed {{ $order->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="text-xs font-bold text-[#182830] mt-0.5">
                                    {{ $order->services }} · <span class="font-mono text-[#25799B]">{{ $order->weight_kg }} kg ({{ $order->number_of_loads }} drum loads)</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($isPendingConfirm)
                                    <span class="inline-flex items-center gap-1.5 rounded-xl border-2 border-amber-600 bg-amber-50 px-3 py-1 font-mono text-xs font-black text-amber-900 shadow-[1px_1px_0px_#182830]">
                                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
                                        <span>Awaiting Staff Confirmation</span>
                                    </span>
                                @elseif($isReceived)
                                    <span class="inline-flex items-center gap-1.5 rounded-xl border-2 border-[#25799B] bg-[#A2C5D8]/40 px-3 py-1 font-mono text-xs font-black text-[#182830] shadow-[1px_1px_0px_#182830]">
                                        <span class="h-2 w-2 rounded-full bg-[#25799B] animate-pulse"></span>
                                        <span>Confirmed: Ready for Drop-off</span>
                                    </span>
                                @elseif($isReady)
                                    <span class="inline-flex items-center gap-1.5 rounded-xl border-2 border-emerald-600 bg-emerald-100 px-3 py-1 font-mono text-xs font-black text-emerald-950 shadow-[1px_1px_0px_#182830] animate-bounce">
                                        <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        <span>Ready for Counter Claim!</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-xl border-2 border-[#25799B] bg-[#25799B] text-white px-3 py-1 font-mono text-xs font-black shadow-[1px_1px_0px_#182830]">
                                        <span class="h-2 w-2 rounded-full bg-white animate-spin"></span>
                                        <span class="uppercase">{{ str_replace('_', ' ', $order->status) }} CYCLE IN PROGRESS</span>
                                    </span>
                                @endif

                                <span class="font-mono text-sm font-black text-[#182830] bg-[#F7E6CB] px-3 py-1 rounded-xl border-2 border-[#182830] shadow-[1px_1px_0px_#182830]">
                                    ₱{{ number_format((float)$order->total_price, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Alert Callout -->
                        @if($isPendingConfirm)
                            <div class="mb-5 rounded-2xl border-2 border-amber-500 bg-amber-50 p-4 text-xs text-amber-950">
                                <div class="flex items-start gap-2.5">
                                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                                    <div>
                                        <strong class="font-sans font-bold block text-sm text-amber-900">Your Laundry Request was Recorded & Queued!</strong>
                                        <p class="mt-0.5 text-amber-800">
                                             Our counter staff is reviewing washing machine capacity. Once confirmed, your portal will show the green drop-off signal so you can bring your garments to the branch and pay!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($isReceived)
                            <div class="mb-5 rounded-2xl border-2 border-emerald-600 bg-emerald-50 p-4 text-xs text-emerald-950 shadow-[2px_2px_0px_#182830]">
                                <div class="flex items-start gap-2.5">
                                    <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <div>
                                        <strong class="font-sans font-bold block text-sm text-emerald-900">Confirmed by Staff! You can now visit the branch!</strong>
                                        <p class="mt-0.5 text-emerald-800">
                                            Please bring your laundry to Trowa Laundry counter and settle the payment of <strong>₱{{ number_format((float)$order->total_price, 2) }}</strong> (Cash, GCash, or Pay on Claim). A drum has been reserved for your wash!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($isReady)
                            <div class="mb-5 rounded-2xl border-2 border-[#25799B] bg-[#A2C5D8]/30 p-4 text-xs text-[#182830] shadow-[2px_2px_0px_#182830]">
                                <div class="flex items-start gap-2.5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[1px_1px_0px_#182830]">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                    <div>
                                        <strong class="font-sans font-bold block text-sm text-[#182830]">Your Fresh Laundry is Ready for Claim!</strong>
                                        <p class="mt-0.5 text-slate-700">
                                            We sent an email alert to your registered address. Please visit our branch to claim your clean, folded garments. Present ticket #<strong>{{ $order->order_number }}</strong> to the cashier.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Visual Stage Stepper Timeline -->
                        <div class="relative py-2">
                            <!-- Progress Background Bar -->
                            <div class="h-3 w-full rounded-full border-2 border-[#182830] bg-[#FFFDF8] p-0.5 shadow-[1px_1px_0px_#182830] mb-4">
                                <div class="h-full rounded-full bg-[#CB1B03] transition-all duration-500" style="width: {{ $stagePercent }}%;"></div>
                            </div>

                            <!-- Stepper Grid Nodes -->
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 text-center">
                                <!-- Node 1: Request -->
                                <div class="rounded-xl border-2 border-[#182830] {{ $stagePercent >= 10 ? 'bg-[#CB1B03] text-white' : 'bg-slate-100 text-slate-400' }} p-2 shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[10px] font-bold uppercase">1. Request</span>
                                    <span class="text-[11px] font-bold">Queued</span>
                                </div>

                                <!-- Node 2: Confirmed / Received -->
                                <div class="rounded-xl border-2 border-[#182830] {{ $stagePercent >= 25 ? 'bg-[#25799B] text-white' : 'bg-slate-100 text-slate-400' }} p-2 shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[10px] font-bold uppercase">2. Drop-off</span>
                                    <span class="text-[11px] font-bold">Confirmed</span>
                                </div>

                                <!-- Node 3: Washing -->
                                <div class="rounded-xl border-2 border-[#182830] {{ $stagePercent >= 50 ? 'bg-[#25799B] text-white' : 'bg-slate-100 text-slate-400' }} p-2 shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[10px] font-bold uppercase">3. Wash</span>
                                    <span class="text-[11px] font-bold">In Drum</span>
                                </div>

                                <!-- Node 4: Drying -->
                                <div class="rounded-xl border-2 border-[#182830] {{ $stagePercent >= 70 ? 'bg-[#25799B] text-white' : 'bg-slate-100 text-slate-400' }} p-2 shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[10px] font-bold uppercase">4. Dry</span>
                                    <span class="text-[11px] font-bold">Tumbler</span>
                                </div>

                                <!-- Node 5: Fold / Iron -->
                                <div class="hidden sm:block rounded-xl border-2 border-[#182830] {{ $stagePercent >= 90 ? 'bg-[#25799B] text-white' : 'bg-slate-100 text-slate-400' }} p-2 shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[10px] font-bold uppercase">5. Fold & Pack</span>
                                    <span class="text-[11px] font-bold">Finishing</span>
                                </div>

                                <!-- Node 6: Ready -->
                                <div class="rounded-xl border-2 border-[#182830] {{ $stagePercent >= 100 ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' }} p-2 shadow-[1px_1px_0px_#182830]">
                                    <span class="block font-mono text-[10px] font-bold uppercase">6. Claim</span>
                                    <span class="text-[11px] font-bold">Ready</span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Details Strip -->
                        <div class="mt-4 pt-3 border-t-2 border-dashed border-[#182830]/15 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Soap Formulation</span>
                                <strong class="text-[#182830] font-bold">{{ $order->soap_preference ?? 'Standard Powder' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Garments Counted</span>
                                <strong class="text-[#182830] font-bold">{{ $order->itemDetails->sum('quantity') ?: 'Bulk batch' }} items</strong>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Payment Status</span>
                                <strong class="{{ $order->payment_status === 'paid' ? 'text-emerald-700' : 'text-[#CB1B03]' }} font-black uppercase">
                                    {{ $order->payment_status }}
                                </strong>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Customer Notes</span>
                                <span class="text-slate-700 truncate block">{{ $order->customer_notes ?: 'None specified' }}</span>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- PANE 3: TRANSACTION HISTORY & PAST RECORDS -->
    <div id="cust-pane-history" class="cust-tab-content {{ ($currentTab ?? 'dashboard') === 'history' ? '' : 'hidden' }} space-y-4">
        <div class="flex items-center justify-between border-b-2 border-[#182830]/15 pb-2">
            <div>
                <h2 class="font-recoleta text-xl sm:text-2xl font-black text-[#182830]">
                    Past Transaction Records &amp; Order History
                </h2>
                <p class="font-mono text-xs text-[#25799B]">Completed, claimed and archived laundry tickets</p>
            </div>
            <span class="font-mono text-xs font-bold text-slate-600">
                {{ $pastOrders->total() }} total visit{{ $pastOrders->total() === 1 ? '' : 's' }}
            </span>
        </div>

        @if($pastOrders->isEmpty())
            <div class="rounded-2xl border-2 border-[#182830]/20 bg-[#FFFDF8] p-8 text-center text-xs font-mono text-slate-500">
                No past transactions recorded yet. Once your laundry orders are claimed, they will be archived here.
            </div>
        @else
            <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] shadow-[5px_5px_0px_#182830] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b-2 border-[#182830] bg-[#F7E6CB] font-mono uppercase text-[#182830]">
                            <tr>
                                <th class="p-3.5 font-bold">Ticket #</th>
                                <th class="p-3.5 font-bold">Date</th>
                                <th class="p-3.5 font-bold">Weight / Drums</th>
                                <th class="p-3.5 font-bold">Services</th>
                                <th class="p-3.5 font-bold">Total Paid</th>
                                <th class="p-3.5 font-bold">Status</th>
                                <th class="p-3.5 font-bold">Your Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#182830]/10 font-mono">
                            @foreach($pastOrders as $past)
                                <tr class="hover:bg-amber-50/50 transition">
                                    <td class="p-3.5 font-black text-[#CB1B03]">#{{ $past->order_number }}</td>
                                    <td class="p-3.5 text-slate-600">{{ $past->created_at->format('M d, Y') }}</td>
                                    <td class="p-3.5">{{ $past->weight_kg }} kg ({{ $past->number_of_loads }} loads)</td>
                                    <td class="p-3.5 font-sans font-medium text-slate-800">{{ $past->services }}</td>
                                    <td class="p-3.5 font-bold text-[#182830]">₱{{ number_format((float)$past->total_price, 2) }}</td>
                                    <td class="p-3.5">
                                        @if($past->status === 'claimed')
                                            <span class="rounded bg-emerald-100 text-emerald-800 px-2 py-0.5 text-[10px] font-bold uppercase border border-emerald-300">Claimed & Done</span>
                                        @elseif($past->status === 'cancelled')
                                            <span class="rounded bg-red-100 text-red-800 px-2 py-0.5 text-[10px] font-bold uppercase border border-red-300">Cancelled</span>
                                        @else
                                            <span class="rounded bg-slate-100 text-slate-800 px-2 py-0.5 text-[10px] font-bold uppercase">{{ $past->status }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        @if($past->rating)
                                            <div class="flex items-center gap-1 text-amber-500 font-bold">
                                                <div class="flex items-center gap-0.5">
                                                    @for($starIdx = 0; $starIdx < $past->rating; $starIdx++)
                                                        <svg class="h-3.5 w-3.5 fill-amber-400 text-amber-500 inline" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-[10px] text-slate-500 font-normal font-mono">({{ $past->rating }}/5)</span>
                                            </div>
                                        @else
                                            <button type="button" onclick="openRatingModal({{ $past->id }}, '{{ $past->order_number }}')" class="rounded-lg border border-[#182830] bg-[#FFFDF8] px-2 py-1 text-[10px] font-bold text-[#25799B] hover:bg-[#F7E6CB] transition cursor-pointer flex items-center gap-1">
                                                <span>Rate Service</span>
                                                <svg class="h-3 w-3 fill-amber-400 text-amber-500 inline" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($pastOrders->hasPages())
                    <div class="p-3 border-t-2 border-[#182830] bg-[#F7E6CB]/40">
                        {{ $pastOrders->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: Customer Self-Service New Laundry Intake Wizard                  -->
<!-- ========================================================================= -->
<div id="customer-intake-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#182830]/80 p-3 sm:p-5 backdrop-blur-xs overflow-y-auto">
    <div class="relative my-auto w-full max-w-3xl rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] shadow-[8px_8px_0px_#182830] overflow-hidden flex flex-col max-h-[92vh]">
        
        <!-- Header Strip -->
        <div class="flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] px-5 py-3.5 text-[#FFFDF8]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                    <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="12" cy="13" r="5"/><circle cx="7" cy="6.5" r="1"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-recoleta text-lg font-black text-[#F7E6CB] leading-tight">Customer Self-Service Laundry Intake</h3>
                    <p class="font-mono text-[10px] uppercase tracking-wider text-[#A2C5D8]">Fleet Allocation & Preference Request</p>
                </div>
            </div>
            <button type="button" id="btn-close-intake-modal" class="flex h-8 w-8 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-lg font-black text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#CB1B03] hover:text-white transition cursor-pointer">
                ×
            </button>
        </div>

        <!-- Wizard Progression Tabs Strip -->
        <div class="border-b-2 border-[#182830] bg-[#F7E6CB]/60 px-5 py-2.5">
            <div class="grid grid-cols-4 gap-2">
                <div class="cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white p-1 text-center font-mono text-xs font-bold shadow-[1px_1px_0px_#182830]" data-step="1">
                    <span class="block text-xs font-black">1. Weight & Services</span>
                </div>
                <div class="cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1 text-center font-mono text-xs font-bold" data-step="2">
                    <span class="block text-xs font-black">2. Garments Breakdown</span>
                </div>
                <div class="cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1 text-center font-mono text-xs font-bold" data-step="3">
                    <span class="block text-xs font-black">3. Detergent Choice</span>
                </div>
                <div class="cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1 text-center font-mono text-xs font-bold" data-step="4">
                    <span class="block text-xs font-black">4. Summary Review</span>
                </div>
            </div>
        </div>

        <!-- Intake Form Body -->
        <form method="POST" action="{{ route('customer.orders.submit') }}" id="customer-intake-form" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">
            @csrf

            <!-- STEP 1: Scale Weight (checked against 8 washing machines limit) & Services -->
            <div id="cust-pane-1" class="cust-pane space-y-5">
                <div>
                    <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 1 of 4</span>
                    <h4 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Scale Weight & Machine Capacity</h4>
                    <p class="text-xs text-[#25799B] font-medium">Specify your estimated laundry weight in kilograms. Each washing machine takes up to 8.0 kg.</p>
                </div>

                <!-- Kilogram Input Card -->
                <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
                    <div class="flex items-center justify-between mb-3">
                        <label class="font-mono text-xs font-bold uppercase text-[#182830]">Estimated Total Weight</label>
                        <span class="font-mono text-xs font-extrabold text-[#25799B]">
                            Calculated Drums: <strong id="cust-loads-calc" class="text-base text-[#CB1B03]">1</strong> Load(s)
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mb-3">
                        <button type="button" id="btn-cust-w-minus" class="h-11 w-11 rounded-xl border-2 border-[#182830] bg-[#F7E6CB] font-mono text-lg font-black text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-200 cursor-pointer">
                            −
                        </button>
                        <div class="relative flex-1">
                            <input type="number" step="0.1" min="0.5" id="cust-weight-input" name="weight_kg" value="8.0" required class="w-full rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-2xl font-black text-[#182830] text-center shadow-[2px_2px_0px_#182830] focus:outline-none">
                            <span class="absolute right-4 top-3 font-mono text-xs font-black text-slate-500">KG</span>
                        </div>
                        <button type="button" id="btn-cust-w-plus" class="h-11 w-11 rounded-xl border-2 border-[#182830] bg-[#F7E6CB] font-mono text-lg font-black text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-200 cursor-pointer">
                            ＋
                        </button>
                    </div>

                    <!-- Weight Presets -->
                    <div class="flex flex-wrap items-center gap-1.5 mb-2">
                        <span class="font-mono text-[10px] font-bold uppercase text-slate-500 mr-1">Quick Select:</span>
                        @foreach([3.0, 5.0, 8.0, 16.0, 24.0] as $presetKg)
                            <button type="button" class="btn-cust-preset rounded-lg border border-[#182830] bg-[#FFFDF8] px-2.5 py-1 font-mono text-xs font-bold text-[#182830] hover:bg-[#A2C5D8] transition cursor-pointer" data-weight="{{ $presetKg }}">
                                {{ $presetKg }} kg
                            </button>
                        @endforeach
                    </div>

                    <!-- 8 WASHING MACHINE CAPACITY LIMIT WARNING BANNER -->
                    <div id="cust-capacity-warning" class="hidden mt-3 rounded-2xl border-2 border-[#CB1B03] bg-red-50 p-4 text-[#182830] shadow-[3px_3px_0px_#CB1B03]">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border-2 border-[#CB1B03] bg-[#CB1B03] text-white font-mono font-black text-lg">
                                !
                            </div>
                            <div class="space-y-1">
                                <h5 class="font-recoleta text-sm font-bold text-[#CB1B03] leading-tight flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 22 22 22 12 2"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    <span>Washing Machine Fleet Limit: Maximum 8 Machines (64.0 KG Max)</span>
                                </h5>
                                <p class="text-xs font-medium text-slate-700 leading-snug">
                                    Our laundromat is equipped with only <strong>8 commercial washing machines</strong> (maximum shop batch is <strong>64.0 kg</strong>).
                                    Your entered weight (<span id="cust-warning-kg-val" class="font-mono font-black text-[#CB1B03]">0</span> kg) requires 
                                    <span id="cust-warning-loads-val" class="font-mono font-black text-[#CB1B03]">0</span> machines, which exceeds the total washing machines available!
                                </p>
                                <p class="font-mono text-[11px] font-extrabold text-[#CB1B03]">
                                    Please reduce your laundry weight to ≤ 64.0 kg or split your items into multiple orders.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Select Treatments -->
                <div>
                    <label class="block font-mono text-xs font-bold uppercase text-[#182830] mb-2">
                        Select Services Needed <span class="text-[#CB1B03]">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($services as $service)
                            @php $rate = (float)($service->price_per_load ?? $service->price); @endphp
                            <label class="flex items-center justify-between rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-xs shadow-[2px_2px_0px_#182830] hover:bg-amber-50 cursor-pointer transition">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" 
                                           class="cust-service-cb h-4 w-4 rounded border-2 border-[#182830] text-[#CB1B03] focus:ring-0"
                                           data-price="{{ $rate }}"
                                           data-type="{{ $service->pricing_type }}"
                                           data-name="{{ $service->name }}">
                                    <div>
                                        <strong class="font-sans text-[#182830] block leading-tight">{{ $service->name }}</strong>
                                        <span class="font-mono text-[10px] text-slate-500 uppercase">{{ $service->pricing_type === 'per_kg' ? 'Per KG rate' : 'Per Load rate' }}</span>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-[#25799B]">₱{{ number_format($rate, 2) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- STEP 2: Garment Items Breakdown (Tap-to-Check Checklist like Staff) -->
            <div id="cust-pane-2" class="cust-pane hidden space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b-2 border-[#182830]/15 pb-3">
                    <div>
                        <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 2 of 4</span>
                        <h4 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Garment Quantity Checklist</h4>
                        <p class="text-xs text-[#25799B] font-medium">Check off each garment type in your load. Quantities will automatically activate.</p>
                    </div>
                    <span id="cust-garments-summary-badge" class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-3 py-1 font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] self-start sm:self-auto">
                        0 items counted
                    </span>
                </div>

                <!-- Hidden inputs container for dynamically checked garments (Only quantity >= 1 will be submitted) -->
                <div id="cust-hidden-garment-rows"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="cust-garments-list">
                    @php
                        $garmentCategories = [
                            ['Tops / Shirts', 'T-Shirts, Polos, Blouses & Tops', 'shirt'],
                            ['Bottoms / Pants', 'Pants, Jeans, Shorts & Trousers', 'pants'],
                            ['Undergarments', 'Briefs, Panties, Bras & Socks', 'boxers'],
                            ['Towels', 'Bath Towels & Hand Towels', 'towel'],
                            ['Beddings / Linens', 'Bed Sheets, Blankets & Pillows', 'bed'],
                            ['Jackets / Hoodies', 'Jackets, Sweaters & Coats', 'jacket'],
                            ['Delicates / Silks', 'Delicates, Dresses & Fine Fabrics', 'sparkles'],
                            ['Uniforms & Other', 'Uniforms & Miscellaneous Garments', 'badge'],
                        ];
                    @endphp

                    @foreach($garmentCategories as [$catName, $catDesc, $catIconKey])
                        <div class="cust-garment-card flex items-center justify-between rounded-2xl border-2 border-[#182830]/30 bg-[#FFFDF8] p-3 transition" data-cat="{{ $catName }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <input type="checkbox" class="cust-garment-check h-5 w-5 rounded-md border-2 border-[#182830] text-[#CB1B03] focus:ring-0 cursor-pointer" data-cat="{{ $catName }}">
                                <div class="min-w-0">
                                    <p class="font-recoleta text-sm font-bold text-[#182830] truncate flex items-center gap-1.5">
                                        @if($catIconKey === 'shirt')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
                                        @elseif($catIconKey === 'pants')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12v4l-2 14h-3l-1-9-1 9H8L6 7V3z"/></svg>
                                        @elseif($catIconKey === 'boxers')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="12" rx="2"/><line x1="12" y1="4" x2="12" y2="16"/></svg>
                                        @elseif($catIconKey === 'towel')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                                        @elseif($catIconKey === 'bed')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9"/></svg>
                                        @elseif($catIconKey === 'jacket')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4l4 2v14H4zM20 4l-4 2v14h4zM8 6h8v14H8z"/></svg>
                                        @elseif($catIconKey === 'sparkles')
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 9 22 12 15 15 12 22 9 15 2 12 9 9 12 2"/></svg>
                                        @else
                                            <svg class="h-4 w-4 text-[#25799B] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="12" cy="10" r="3"/><line x1="8" y1="17" x2="16" y2="17"/></svg>
                                        @endif
                                        <span>{{ $catName }}</span>
                                    </p>
                                    <p class="font-mono text-[10px] text-slate-500 truncate">{{ $catDesc }}</p>
                                </div>
                            </div>

                            <!-- Quantity Stepper (Enabled when checked) -->
                            <div class="cust-stepper-box flex items-center gap-1.5 opacity-30 pointer-events-none transition" data-cat="{{ $catName }}">
                                <button type="button" class="btn-cust-g-dec h-8 w-8 rounded-lg border border-[#182830] bg-[#F7E6CB] font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-slate-200 transition cursor-pointer" data-cat="{{ $catName }}">
                                    −
                                </button>
                                <span class="cust-g-step-val w-8 text-center font-mono text-sm font-black text-[#182830]" data-cat="{{ $catName }}">0</span>
                                <button type="button" class="btn-cust-g-inc h-8 w-8 rounded-lg border border-[#182830] bg-[#F7E6CB] font-mono text-xs font-bold text-[#182830] shadow-[1px_1px_0px_#182830] hover:bg-slate-200 transition cursor-pointer" data-cat="{{ $catName }}">
                                    ＋
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- STEP 3: Detergent & Soap Formulation + Live Inventory Integration -->
            <div id="cust-pane-3" class="cust-pane hidden space-y-5">
                <div>
                    <span class="inline-block rounded bg-[#A2C5D8] px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-[#182830]">Step 3 of 4</span>
                    <h4 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Detergent & Special Instructions</h4>
                    <p class="text-xs text-[#25799B] font-medium">Select from available shop inventory or choose to bring your personal supplies.</p>
                </div>

                <!-- Customer Own Soap Option -->
                <div class="rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio" name="soap_preference" value="own" id="cust-soap-own-radio" class="mt-0.5 h-4 w-4 text-[#CB1B03]">
                        <div class="flex-1">
                            <strong class="font-recoleta text-sm font-bold text-[#182830] flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2h4M12 2v4M7 8h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2z"/></svg>
                                <span>I will bring my own soap &amp; fabric conditioner</span>
                            </strong>
                            <span class="block font-mono text-[11px] text-slate-600 mt-0.5">Bring your preferred personal brands to counter (e.g. Ariel pods, Surf powder, Downy bottle).</span>
                            <div id="cust-own-soap-input-wrap" class="mt-2.5 hidden">
                                <input type="text" name="own_soap_custom" id="cust-own-soap-input" class="field text-xs font-medium" placeholder="Specify your soap brand (e.g., Tide Pods, Surf Blossom, Downy Mystic)">
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Live Shop Stockroom Sachets Section -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block font-mono text-xs font-bold uppercase text-[#182830]">
                            Available In Shop Inventory Stock
                        </label>
                        <span class="font-mono text-[11px] text-[#25799B]">Real-time stock on hand</span>
                    </div>

                    @if(isset($inventoryItems) && $inventoryItems->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="cust-inventory-list">
                            @foreach($inventoryItems as $itemIdx => $invItem)
                                @php
                                    $onHand = (float) $invItem->quantity_on_hand;
                                    $isFirst = $itemIdx === 0;
                                @endphp
                                <label class="flex items-center justify-between rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-3 text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] cursor-pointer hover:bg-amber-50/50 has-checked:bg-[#A2C5D8]/40 has-checked:border-[#25799B] transition">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="soap_preference" value="{{ $invItem->id }}" data-item-name="{{ $invItem->name }}" {{ $isFirst ? 'checked' : '' }} class="h-4 w-4 text-[#CB1B03] cust-stock-soap-radio">
                                        <div>
                                            <span class="block font-recoleta text-sm font-bold text-[#182830]">{{ $invItem->name }}</span>
                                            <span class="font-mono text-[10px] text-slate-500 font-normal flex items-center gap-1 mt-0.5">
                                                <svg class="h-3 w-3 text-slate-500 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                                <span>Tracked per {{ $invItem->unit }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="badge border border-emerald-600 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold">
                                        {{ (int) $onHand }} in stock
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-[#182830]/40 bg-[#FFFDF8] p-4 text-center">
                            <p class="font-mono text-xs text-slate-600">No detergent supplies currently recorded in shop inventory stock. Please bring your personal detergent &amp; softener!</p>
                        </div>
                    @endif
                </div>

                <div>
                    <label for="cust-notes" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        Special Instructions / Fabric Warnings (Optional)
                    </label>
                    <textarea name="customer_notes" id="cust-notes" rows="2" placeholder="e.g. Separate white shirts, low heat dryer for red jacket, delicate wool" class="field text-xs font-medium"></textarea>
                </div>
            </div>

            <!-- STEP 4: Summary & Confirmation Review -->
            <div id="cust-pane-4" class="cust-pane hidden space-y-5">
                <div>
                    <span class="inline-block rounded bg-emerald-600 px-2 py-0.5 font-mono text-[10px] font-bold uppercase text-white">Step 4 of 4</span>
                    <h4 class="font-recoleta text-xl font-bold text-[#182830] mt-0.5">Order Intake Confirmation Summary</h4>
                    <p class="text-xs text-[#25799B] font-medium">Please review your entries. You can make adjustments or confirm to send to staff!</p>
                </div>

                <!-- Summary Receipt Card -->
                <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-5 shadow-[4px_4px_0px_#182830] space-y-4 font-mono text-xs">
                    <div class="flex items-center justify-between border-b-2 border-dashed border-[#182830]/20 pb-3">
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Customer Record</span>
                            <strong class="text-sm font-sans font-bold text-[#182830]">{{ $user->name }}</strong>
                            <span class="block text-[11px] text-slate-600">{{ $customer->address ?? $user->address }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Capacity Check</span>
                            <span id="summary-capacity-status" class="inline-flex items-center gap-1 rounded bg-emerald-100 text-emerald-900 border border-emerald-400 px-2 py-0.5 text-[10px] font-black uppercase">
                                <svg class="h-3 w-3 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Fleet OK (8 Drums)</span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 border-b-2 border-dashed border-[#182830]/20 pb-3">
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Estimated Weight</span>
                            <strong id="summary-weight" class="text-sm font-black text-[#CB1B03]">8.0 kg</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Required Drums</span>
                            <strong id="summary-loads" class="text-sm font-black text-[#182830]">1 Load</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Soap Preference</span>
                            <strong id="summary-soap" class="text-xs font-bold text-[#25799B]">Ariel Pro</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Total Items</span>
                            <strong id="summary-garment-count" class="text-xs font-bold text-[#182830]">0 items</strong>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-bold block mb-1">Selected Services</span>
                        <ul id="summary-services-list" class="space-y-1 font-sans text-xs">
                            <!-- Populated via JS -->
                        </ul>
                    </div>

                    <div class="pt-3 border-t-2 border-[#182830] flex items-center justify-between">
                        <span class="font-recoleta text-base font-bold text-[#182830]">Estimated Total Cost:</span>
                        <strong id="summary-total-cost" class="font-recoleta text-2xl font-black text-[#CB1B03]">₱0.00</strong>
                    </div>
                </div>

                <div class="rounded-xl border-2 border-[#25799B] bg-[#A2C5D8]/20 p-3 text-[11px] text-[#182830] flex items-start gap-2">
                    <svg class="h-4 w-4 text-[#25799B] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <div>
                        <strong>What happens next?</strong> Once you submit, our staff in the Laundry Queue will immediately see your request and confirm drum availability. You will see a notification to bring your garments and pay at your convenience.
                    </div>
                </div>
            </div>

            <!-- Modal Nav Actions -->
            <div class="flex items-center justify-between pt-4 border-t-2 border-[#182830]/15">
                <button type="button" id="btn-cust-back" class="hidden rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 cursor-pointer">
                    ← Back / Edit
                </button>
                <div class="ml-auto flex items-center gap-2">
                    <button type="button" id="btn-cust-next" class="rounded-xl border-2 border-[#182830] bg-[#25799B] px-5 py-2.5 font-recoleta text-xs font-black text-white shadow-[2px_2px_0px_#182830] hover:bg-[#1E6482] cursor-pointer">
                        Next Step ➔
                    </button>
                    <button type="submit" id="btn-cust-submit" class="hidden rounded-xl border-3 border-[#182830] bg-[#CB1B03] px-6 py-2.5 font-recoleta text-sm font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] cursor-pointer">
                        Confirm & Start Wash Transaction ➔
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: Customer Rating & Feedback Pop-up Pane with Confetti & Bubbles   -->
<!-- ========================================================================= -->
<div id="customer-rating-modal" class="fixed inset-0 z-50 {{ $unratedOrder ? 'flex' : 'hidden' }} items-center justify-center bg-[#182830]/85 p-4 backdrop-blur-xs overflow-hidden">
    <!-- Interactive Canvas for Party Confetti Particles -->
    <canvas id="confetti-canvas" class="absolute inset-0 pointer-events-none z-10 w-full h-full"></canvas>

    <div class="relative w-full max-w-md rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] shadow-[8px_8px_0px_#182830] p-6 text-center animate-bounce-short z-20 overflow-hidden">
        
        <!-- Animated Laundromat Foam Bubbles in Modal Background -->
        <div class="absolute -top-6 -right-6 h-24 w-24 rounded-full bg-[#A2C5D8]/30 animate-pulse pointer-events-none"></div>
        <div class="absolute -bottom-8 -left-8 h-28 w-28 rounded-full bg-[#F7E6CB]/60 pointer-events-none"></div>

        <!-- Cheerful Star Mascot & Pop Ribbon -->
        <div class="relative mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border-3 border-[#182830] bg-amber-300 text-amber-900 shadow-[3px_3px_0px_#182830] mb-3 hover:scale-110 transition cursor-pointer" id="btn-trigger-confetti" title="Click me for more celebratory bubbles & confetti!">
            <svg class="h-9 w-9 text-amber-900 fill-amber-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            <span class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full bg-[#CB1B03] border border-[#182830] animate-ping"></span>
        </div>

        <span class="inline-block rounded-md border border-[#182830] bg-[#CB1B03] px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-white shadow-[1px_1px_0px_#182830] mb-2">
            Order Complete · Fresh & Clean!
        </span>

        <h3 class="font-recoleta text-2xl font-black text-[#182830]">
            How was your laundry service?
        </h3>
        
        <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto">
            Your laundry ticket #<strong id="modal-rating-order-num" class="text-[#CB1B03]">{{ $unratedOrder->order_number ?? '' }}</strong> is clean and folded! Tap the stars to celebrate and share your feedback.
        </p>

        <form method="POST" action="{{ $unratedOrder ? route('customer.orders.rate', $unratedOrder) : '#' }}" id="rating-form" data-confirm="Submit your review and rating for this laundry order?" data-confirm-title="Confirm Review Submission" data-confirm-type="check" data-confirm-btn="Yes, Submit Rating" class="mt-5 space-y-4">
            @csrf

            <!-- Interactive 5-Star Selection with SVG Stars -->
            <div>
                <div class="flex items-center justify-center gap-2 cursor-pointer" id="star-rating-box">
                    @for($s = 1; $s <= 5; $s++)
                        <button type="button" class="star-rating-btn hover:scale-125 transition text-amber-400 select-none p-1 cursor-pointer bg-transparent border-0" data-val="{{ $s }}">
                            <svg class="h-8 w-8 star-svg fill-amber-400 text-amber-500" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-value-input" value="5">
                <span id="star-rating-label" class="block font-mono text-xs font-bold text-amber-700 mt-1">Excellent! 5 Stars</span>
            </div>

            <!-- Customer Comment Textarea -->
            <div>
                <textarea name="rating_comment" rows="2" placeholder="Tell us what you loved, or how we can improve (optional)" class="field text-xs font-medium"></textarea>
            </div>

            <div class="space-y-2 pt-2">
                <button type="submit" class="w-full rounded-2xl border-3 border-[#182830] bg-[#CB1B03] py-3 px-4 font-recoleta text-sm font-black text-white shadow-[3px_3px_0px_#182830] hover:bg-[#B51702] transition cursor-pointer">
                    Submit My Rating & Feedback ➔
                </button>
                <button type="button" id="btn-close-rating-modal" class="w-full font-mono text-xs text-slate-500 hover:text-[#182830] font-semibold py-1">
                    Rate Later
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ---------------------------------------------------------
    // 1. Intake Wizard Modal Opening & Navigation
    // ---------------------------------------------------------
    const intakeModal = document.getElementById('customer-intake-modal');
    const btnOpenIntake = document.getElementById('btn-open-intake-wizard');
    const btnCloseIntake = document.getElementById('btn-close-intake-modal');
    const btnNext = document.getElementById('btn-cust-next');
    const btnBack = document.getElementById('btn-cust-back');
    const btnSubmit = document.getElementById('btn-cust-submit');
    const wizardTabs = document.querySelectorAll('.cust-wizard-tab');

    let currentStep = 1;
    const totalSteps = 4;

    btnOpenIntake?.addEventListener('click', () => {
        intakeModal?.classList.remove('hidden');
        intakeModal?.classList.add('flex');
        showStep(1);
    });

    btnCloseIntake?.addEventListener('click', () => {
        intakeModal?.classList.add('hidden');
        intakeModal?.classList.remove('flex');
    });

    function showStep(step) {
        currentStep = step;
        for (let i = 1; i <= totalSteps; i++) {
            const pane = document.getElementById(`cust-pane-${i}`);
            if (pane) pane.classList.toggle('hidden', i !== step);
        }

        wizardTabs.forEach(tab => {
            const tabStep = parseInt(tab.dataset.step, 10);
            if (tabStep === step) {
                tab.className = 'cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#CB1B03] text-white p-1 text-center font-mono text-xs font-bold shadow-[1px_1px_0px_#182830]';
            } else if (tabStep < step) {
                tab.className = 'cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#25799B] text-white p-1 text-center font-mono text-xs font-bold';
            } else {
                tab.className = 'cust-wizard-tab rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-slate-600 p-1 text-center font-mono text-xs font-bold';
            }
        });

        if (btnBack) btnBack.classList.toggle('hidden', step === 1);
        if (btnNext) btnNext.classList.toggle('hidden', step === totalSteps);
        if (btnSubmit) btnSubmit.classList.toggle('hidden', step !== totalSteps);

        if (step === 4) {
            populateSummary();
        }
    }

    btnNext?.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }
    });

    btnBack?.addEventListener('click', () => {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    // ---------------------------------------------------------
    // 2. Step 1: Weight & 8-Machine Fleet Limit Logic
    // ---------------------------------------------------------
    const weightInput = document.getElementById('cust-weight-input');
    const loadsCalcEl = document.getElementById('cust-loads-calc');
    const capacityWarningEl = document.getElementById('cust-capacity-warning');
    const warningKgVal = document.getElementById('cust-warning-kg-val');
    const warningLoadsVal = document.getElementById('cust-warning-loads-val');
    const btnWMinus = document.getElementById('btn-cust-w-minus');
    const btnWPlus = document.getElementById('btn-cust-w-plus');

    function checkWeightFleetLimit() {
        const weight = parseFloat(weightInput?.value) || 0;
        const loads = weight > 0 ? Math.ceil(weight / 8) : 0;
        if (loadsCalcEl) loadsCalcEl.textContent = loads;

        if (weight > 64 || loads > 8) {
            if (capacityWarningEl) capacityWarningEl.classList.remove('hidden');
            if (warningKgVal) warningKgVal.textContent = weight.toFixed(1);
            if (warningLoadsVal) warningLoadsVal.textContent = loads;
            if (loadsCalcEl) {
                loadsCalcEl.textContent = `${loads} (OVER 8 MACHINES!)`;
                loadsCalcEl.className = 'text-base font-black text-red-600 animate-pulse';
            }
            return false;
        } else {
            if (capacityWarningEl) capacityWarningEl.classList.add('hidden');
            if (loadsCalcEl) {
                loadsCalcEl.textContent = loads;
                loadsCalcEl.className = 'text-base text-[#CB1B03]';
            }
            return true;
        }
    }

    weightInput?.addEventListener('input', checkWeightFleetLimit);

    btnWMinus?.addEventListener('click', () => {
        if (weightInput) {
            const cur = parseFloat(weightInput.value) || 0;
            weightInput.value = Math.max(0.5, cur - 0.5).toFixed(1);
            checkWeightFleetLimit();
        }
    });

    btnWPlus?.addEventListener('click', () => {
        if (weightInput) {
            const cur = parseFloat(weightInput.value) || 0;
            weightInput.value = (cur + 0.5).toFixed(1);
            checkWeightFleetLimit();
        }
    });

    document.querySelectorAll('.btn-cust-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            if (weightInput) {
                weightInput.value = btn.dataset.weight;
                checkWeightFleetLimit();
            }
        });
    });

    // Auto-select first service treatment by default
    const serviceCbs = document.querySelectorAll('.cust-service-cb');
    if (serviceCbs.length > 0 && !document.querySelector('.cust-service-cb:checked')) {
        serviceCbs[0].checked = true;
    }

    // Garment Tap-to-Check Checklist (Identical to Staff Portal)
    const custGarmentCounts = {};
    const custGarmentChecks = document.querySelectorAll('.cust-garment-check');
    const custHiddenGarmentRows = document.getElementById('cust-hidden-garment-rows');
    const custGarmentSummaryBadge = document.getElementById('cust-garments-summary-badge');

    function syncCustGarmentHiddenInputs() {
        if (!custHiddenGarmentRows) return;
        custHiddenGarmentRows.innerHTML = '';
        let totalPieces = 0;
        let activeCategories = 0;
        let index = 0;

        Object.keys(custGarmentCounts).forEach(cat => {
            const qty = custGarmentCounts[cat] || 0;
            if (qty > 0) {
                totalPieces += qty;
                activeCategories++;

                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `item_details[${index}][item_name]`;
                nameInput.value = cat;

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `item_details[${index}][quantity]`;
                qtyInput.value = qty;

                custHiddenGarmentRows.appendChild(nameInput);
                custHiddenGarmentRows.appendChild(qtyInput);
                index++;
            }
        });

        if (custGarmentSummaryBadge) {
            custGarmentSummaryBadge.textContent = `${totalPieces} items counted across ${activeCategories} categories`;
        }
    }

    custGarmentChecks.forEach(cb => {
        cb.addEventListener('change', () => {
            const cat = cb.dataset.cat;
            const card = cb.closest('.cust-garment-card');
            const stepperBox = card?.querySelector('.cust-stepper-box');
            const valSpan = card?.querySelector('.cust-g-step-val');

            if (cb.checked) {
                custGarmentCounts[cat] = (custGarmentCounts[cat] && custGarmentCounts[cat] > 0) ? custGarmentCounts[cat] : 1;
                if (valSpan) valSpan.textContent = custGarmentCounts[cat];
                if (stepperBox) {
                    stepperBox.classList.remove('opacity-30', 'pointer-events-none');
                    stepperBox.classList.add('opacity-100', 'pointer-events-auto');
                }
                card?.classList.add('border-[#25799B]', 'bg-[#A2C5D8]/20');
            } else {
                custGarmentCounts[cat] = 0;
                if (valSpan) valSpan.textContent = '0';
                if (stepperBox) {
                    stepperBox.classList.add('opacity-30', 'pointer-events-none');
                    stepperBox.classList.remove('opacity-100', 'pointer-events-auto');
                }
                card?.classList.remove('border-[#25799B]', 'bg-[#A2C5D8]/20');
            }
            syncCustGarmentHiddenInputs();
        });
    });

    document.querySelectorAll('.btn-cust-g-inc').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const cat = btn.dataset.cat;
            const card = btn.closest('.cust-garment-card');
            const cb = card?.querySelector('.cust-garment-check');
            const valSpan = card?.querySelector('.cust-g-step-val');

            custGarmentCounts[cat] = (custGarmentCounts[cat] || 0) + 1;
            if (valSpan) valSpan.textContent = custGarmentCounts[cat];

            if (cb && !cb.checked) {
                cb.checked = true;
                const stepperBox = card?.querySelector('.cust-stepper-box');
                if (stepperBox) {
                    stepperBox.classList.remove('opacity-30', 'pointer-events-none');
                    stepperBox.classList.add('opacity-100', 'pointer-events-auto');
                }
                card?.classList.add('border-[#25799B]', 'bg-[#A2C5D8]/20');
            }
            syncCustGarmentHiddenInputs();
        });
    });

    document.querySelectorAll('.btn-cust-g-dec').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const cat = btn.dataset.cat;
            const card = btn.closest('.cust-garment-card');
            const cb = card?.querySelector('.cust-garment-check');
            const stepperBox = card?.querySelector('.cust-stepper-box');
            const valSpan = card?.querySelector('.cust-g-step-val');

            const current = custGarmentCounts[cat] || 0;
            if (current > 1) {
                custGarmentCounts[cat] = current - 1;
                if (valSpan) valSpan.textContent = custGarmentCounts[cat];
            } else {
                custGarmentCounts[cat] = 0;
                if (valSpan) valSpan.textContent = '0';
                if (cb) cb.checked = false;
                if (stepperBox) {
                    stepperBox.classList.add('opacity-30', 'pointer-events-none');
                    stepperBox.classList.remove('opacity-100', 'pointer-events-auto');
                }
                card?.classList.remove('border-[#25799B]', 'bg-[#A2C5D8]/20');
            }
            syncCustGarmentHiddenInputs();
        });
    });

    // Soap Toggle (Own Supplies vs Shop Stock)
    const ownSoapRadio = document.getElementById('cust-soap-own-radio');
    const ownSoapWrap = document.getElementById('cust-own-soap-input-wrap');
    const ownSoapInput = document.getElementById('cust-own-soap-input');
    const allSoapRadios = document.querySelectorAll('input[name="soap_preference"]');

    allSoapRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (ownSoapRadio && ownSoapRadio.checked) {
                ownSoapWrap?.classList.remove('hidden');
                ownSoapInput?.focus();
            } else {
                ownSoapWrap?.classList.add('hidden');
            }
        });
    });

    // Validation per step
    function validateStep(step) {
        if (step === 1) {
            const weight = parseFloat(weightInput?.value) || 0;
            const loads = Math.ceil(weight / 8);
            if (weight <= 0) {
                alert('Please enter a valid scale weight in kilograms.');
                weightInput?.focus();
                return false;
            }
            if (weight > 64 || loads > 8) {
                alert(`Fleet Limit Warning: Trowa Laundry has only 8 washing machines (maximum 64.0 kg per run). The entered weight of ${weight.toFixed(1)} kg requires ${loads} machines. Please reduce the weight or process as multiple separate orders.`);
                if (capacityWarningEl) capacityWarningEl.classList.remove('hidden');
                weightInput?.focus();
                return false;
            }
            const checkedServices = document.querySelectorAll('.cust-service-cb:checked');
            if (checkedServices.length === 0) {
                alert('Please check at least one laundry service treatment.');
                return false;
            }
            return true;
        }
        return true;
    }

    // Populate Step 4 Summary
    function populateSummary() {
        const weight = parseFloat(weightInput?.value) || 0;
        const loads = Math.ceil(weight / 8);

        const summaryWeight = document.getElementById('summary-weight');
        const summaryLoads = document.getElementById('summary-loads');
        const summarySoap = document.getElementById('summary-soap');
        const summaryCount = document.getElementById('summary-garment-count');
        const summaryServicesList = document.getElementById('summary-services-list');
        const summaryTotalCost = document.getElementById('summary-total-cost');
        const summaryCapacityStatus = document.getElementById('summary-capacity-status');

        if (summaryWeight) summaryWeight.textContent = `${weight.toFixed(1)} kg`;
        if (summaryLoads) summaryLoads.textContent = `${loads} Drum${loads === 1 ? '' : 's'}`;

        if (summaryCapacityStatus) {
            if (weight <= 64 && loads <= 8) {
                summaryCapacityStatus.innerHTML = `<svg class="h-3 w-3 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg><span>Fleet OK (${loads}/8 Drums)</span>`;
                summaryCapacityStatus.className = 'inline-flex items-center gap-1 rounded bg-emerald-100 text-emerald-900 border border-emerald-400 px-2 py-0.5 text-[10px] font-black uppercase';
            } else {
                summaryCapacityStatus.innerHTML = `<svg class="h-3 w-3 text-red-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>EXCEEDS 8 DRUMS!</span>`;
                summaryCapacityStatus.className = 'inline-flex items-center gap-1 rounded bg-red-100 text-red-900 border border-red-400 px-2 py-0.5 text-[10px] font-black uppercase animate-pulse';
            }
        }

        const selectedSoap = document.querySelector('input[name="soap_preference"]:checked');
        if (summarySoap) {
            if (selectedSoap && selectedSoap.value === 'own') {
                const customVal = ownSoapInput?.value.trim();
                summarySoap.textContent = customVal ? `Own Supplies (${customVal})` : 'Customer Brought Own Supplies';
            } else if (selectedSoap && selectedSoap.dataset.itemName) {
                summarySoap.textContent = `Shop Stock: ${selectedSoap.dataset.itemName}`;
            } else {
                summarySoap.textContent = 'Standard Detergent Formulation';
            }
        }

        let totalGarments = 0;
        Object.keys(custGarmentCounts).forEach(cat => {
            totalGarments += custGarmentCounts[cat] || 0;
        });
        if (summaryCount) summaryCount.textContent = `${totalGarments} items counted`;

        let subtotal = 0;
        const selectedServices = [];
        document.querySelectorAll('.cust-service-cb:checked').forEach(cb => {
            const price = parseFloat(cb.dataset.price) || 0;
            const type = cb.dataset.type;
            const name = cb.dataset.name;
            let lineCost = (type === 'per_kg') ? (price * weight) : (price * loads);
            subtotal += lineCost;
            selectedServices.push({ name, cost: lineCost });
        });

        if (summaryServicesList) {
            summaryServicesList.innerHTML = selectedServices.map(s => `
                <li class="flex items-center justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-800">${s.name}</span>
                    <strong class="font-mono text-[#25799B]">₱${s.cost.toFixed(2)}</strong>
                </li>
            `).join('');
        }

        if (summaryTotalCost) summaryTotalCost.textContent = `₱${subtotal.toFixed(2)}`;
    }

    // Form Submission Animation & Retro Confirmation Modal
    const intakeForm = document.getElementById('customer-intake-form');
    intakeForm?.addEventListener('submit', (e) => {
        const weight = parseFloat(weightInput?.value) || 0;
        if (weight > 64) {
            e.preventDefault();
            alert('Cannot proceed: Total weight exceeds 8 washing machines capacity (64.0 kg).');
            return false;
        }

        if (!intakeForm.hasAttribute('data-cust-confirmed')) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const loads = Math.ceil(weight / 8);
            const totalText = document.getElementById('summary-total-cost')?.textContent || '₱0.00';
            const soapText = document.getElementById('summary-soap')?.textContent || 'Standard Detergent';

            const detailsHtml = `
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Scale Weight:</span>
                    <strong>${weight.toFixed(1)} kg (${loads} drum${loads === 1 ? '' : 's'})</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#182830]/10">
                    <span class="text-slate-600 font-bold">Detergent Choice:</span>
                    <span class="text-slate-800">${soapText}</span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-[#182830] font-black uppercase">Estimated Total:</span>
                    <strong class="font-recoleta text-base text-[#CB1B03]">${totalText}</strong>
                </div>
            `;

            if (window.TrowaConfirm) {
                window.TrowaConfirm({
                    title: 'Confirm Laundry Request',
                    message: 'Are you ready to send this laundry booking to Trowa Counter staff?',
                    badge: 'Patron Booking',
                    type: 'primary',
                    confirmText: 'Yes, Send Laundry Request ➔',
                    cancelText: 'Back to Edit',
                    details: detailsHtml
                }, function() {
                    intakeForm.setAttribute('data-cust-confirmed', 'true');
                    const submitBtn = intakeForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        intakeForm.submit();
                    }
                });
                return false;
            }
        }

        intakeForm.removeAttribute('data-cust-confirmed');

        if (window.TrowaLoading) {
            window.TrowaLoading.show({
                header: '⚡ TROWA PATRON TERMINAL ⚡',
                stage: 'SENDING INTAKE',
                title: 'Transmitting Laundry Booking...',
                message: 'Allocating commercial drums, generating intake request, and notifying store staff'
            });
        }
    });

    // ---------------------------------------------------------
    // 3. Customer Navigation Tabs Switching
    // ---------------------------------------------------------
    window.switchCustomerTab = function(tabName) {
        document.querySelectorAll('.cust-portal-tab-btn').forEach(btn => {
            btn.classList.remove('border-2', 'border-[#182830]', 'bg-[#F7E6CB]', 'text-[#182830]', 'shadow-[2px_2px_0px_#182830]');
            btn.classList.add('border-2', 'border-transparent', 'bg-white/60', 'text-[#25799B]');
        });

        const activeBtn = document.getElementById('cust-tab-btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'bg-white/60', 'text-[#25799B]');
            activeBtn.classList.add('border-2', 'border-[#182830]', 'bg-[#F7E6CB]', 'text-[#182830]', 'shadow-[2px_2px_0px_#182830]');
        }

        document.querySelectorAll('.cust-tab-content').forEach(pane => pane.classList.add('hidden'));
        const activePane = document.getElementById('cust-pane-' + tabName);
        if (activePane) activePane.classList.remove('hidden');

        if (history.pushState) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            history.pushState(null, '', url.toString());
        }
    };

    window.addEventListener('popstate', () => {
        const urlTab = new URLSearchParams(window.location.search).get('tab') || 'dashboard';
        window.switchCustomerTab(urlTab);
    });

    // ---------------------------------------------------------
    // 4. Mascot Interactions & Silent Jokes Cycler
    // ---------------------------------------------------------
    const mascotJokesPool = @json($mascotJokes);
    let mascotJokeIdx = 0;
    const mascotSpeechEl = document.getElementById('mascot-speech');
    const mascotBadgeEl = document.getElementById('mascot-badge');
    const btnNextJoke = document.getElementById('btn-next-joke');
    const mascotAvatar = document.getElementById('mascot-avatar');

    let faceRotation = 0;
    function cycleMascotJoke() {
        if (!mascotJokesPool || mascotJokesPool.length === 0) return;
        mascotJokeIdx = (mascotJokeIdx + 1) % mascotJokesPool.length;
        const currentJoke = mascotJokesPool[mascotJokeIdx];
        if (mascotSpeechEl) {
            mascotSpeechEl.style.opacity = '0';
            setTimeout(() => {
                mascotSpeechEl.textContent = currentJoke.speech;
                mascotSpeechEl.style.opacity = '1';
            }, 150);
        }
        if (mascotBadgeEl) mascotBadgeEl.textContent = currentJoke.badge;

        // Playful spin cycle for Trowa-Bot's face inside the drum
        const spinFace = document.getElementById('dash-robot-spinning-face');
        if (spinFace) {
            faceRotation += 360;
            spinFace.style.transform = `rotate(${faceRotation}deg)`;
        }
    }

    btnNextJoke?.addEventListener('click', cycleMascotJoke);
    mascotAvatar?.addEventListener('click', () => {
        cycleMascotJoke();
        // Little bounce animation
        mascotAvatar.classList.add('scale-110');
        setTimeout(() => mascotAvatar.classList.remove('scale-110'), 200);
    });

    // ---------------------------------------------------------
    // 5. Confetti & Celebratory Bubbles Engine
    // ---------------------------------------------------------
    const confettiCanvas = document.getElementById('confetti-canvas');
    let confettiCtx = confettiCanvas ? confettiCanvas.getContext('2d') : null;
    let confettiParticles = [];
    let confettiAnimationId = null;

    function resizeConfettiCanvas() {
        if (!confettiCanvas) return;
        confettiCanvas.width = window.innerWidth;
        confettiCanvas.height = window.innerHeight;
    }

    window.addEventListener('resize', resizeConfettiCanvas);
    resizeConfettiCanvas();

    function fireCelebrationConfetti() {
        if (!confettiCanvas || !confettiCtx) return;
        resizeConfettiCanvas();

        const colors = ['#CB1B03', '#25799B', '#F7E6CB', '#F59E0B', '#10B981', '#6366F1'];
        confettiParticles = [];

        // Generate 120 vibrant confetti ribbons & bubbles
        for (let i = 0; i < 120; i++) {
            confettiParticles.push({
                x: confettiCanvas.width / 2 + (Math.random() - 0.5) * 200,
                y: confettiCanvas.height / 2 - 50,
                size: Math.random() * 8 + 4,
                color: colors[Math.floor(Math.random() * colors.length)],
                speedX: (Math.random() - 0.5) * 16,
                speedY: Math.random() * -14 - 4,
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 10,
                isBubble: Math.random() > 0.65,
                alpha: 1,
            });
        }

        if (confettiAnimationId) cancelAnimationFrame(confettiAnimationId);
        animateConfetti();
    }

    function animateConfetti() {
        if (!confettiCtx) return;
        confettiCtx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);

        let activeCount = 0;
        confettiParticles.forEach(p => {
            p.x += p.speedX;
            p.y += p.speedY;
            p.speedY += 0.35; // gravity
            p.rotation += p.rotationSpeed;
            p.alpha -= 0.007;

            if (p.alpha > 0) {
                activeCount++;
                confettiCtx.save();
                confettiCtx.globalAlpha = Math.max(0, p.alpha);
                confettiCtx.translate(p.x, p.y);
                confettiCtx.rotate((p.rotation * Math.PI) / 180);

                if (p.isBubble) {
                    // Render cute laundromat bubble circle
                    confettiCtx.beginPath();
                    confettiCtx.arc(0, 0, p.size, 0, Math.PI * 2);
                    confettiCtx.fillStyle = '#A2C5D8';
                    confettiCtx.fill();
                    confettiCtx.strokeStyle = '#182830';
                    confettiCtx.lineWidth = 1.5;
                    confettiCtx.stroke();
                } else {
                    // Render retro confetti strip
                    confettiCtx.fillStyle = p.color;
                    confettiCtx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 1.5);
                    confettiCtx.strokeStyle = '#182830';
                    confettiCtx.lineWidth = 1;
                    confettiCtx.strokeRect(-p.size / 2, -p.size / 2, p.size, p.size * 1.5);
                }
                confettiCtx.restore();
            }
        });

        if (activeCount > 0) {
            confettiAnimationId = requestAnimationFrame(animateConfetti);
        } else {
            confettiCtx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
        }
    }

    // Trigger confetti on mascot click in rating modal
    document.getElementById('btn-trigger-confetti')?.addEventListener('click', fireCelebrationConfetti);

    // ---------------------------------------------------------
    // 6. Customer 5-Star Rating Modal Interaction
    // ---------------------------------------------------------
    const ratingModal = document.getElementById('customer-rating-modal');
    const closeRatingBtn = document.getElementById('btn-close-rating-modal');
    const starBtns = document.querySelectorAll('.star-rating-btn');
    const ratingInput = document.getElementById('rating-value-input');
    const ratingLabel = document.getElementById('star-rating-label');

    const ratingDescriptions = {
        1: 'Needs Improvement (1 Star)',
        2: 'Fair (2 Stars)',
        3: 'Good Service (3 Stars)',
        4: 'Great Job! (4 Stars)',
        5: 'Excellent! (5 Stars)'
    };

    function updateStars(val) {
        if (ratingInput) ratingInput.value = val;
        if (ratingLabel) ratingLabel.textContent = ratingDescriptions[val] || `${val} Stars`;

        starBtns.forEach(btn => {
            const starVal = parseInt(btn.dataset.val, 10);
            const svg = btn.querySelector('.star-svg');
            if (starVal <= val) {
                if (svg) {
                    svg.setAttribute('class', 'h-8 w-8 star-svg fill-amber-400 text-amber-500');
                }
            } else {
                if (svg) {
                    svg.setAttribute('class', 'h-8 w-8 star-svg fill-transparent text-slate-300');
                }
            }
        });
    }

    updateStars(5);

    starBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = parseInt(btn.dataset.val, 10);
            updateStars(val);
            // Fire playful confetti on 4 or 5 stars!
            if (val >= 4) {
                fireCelebrationConfetti();
            }
        });
    });

    closeRatingBtn?.addEventListener('click', () => {
        ratingModal?.classList.add('hidden');
        ratingModal?.classList.remove('flex');
    });

    window.openRatingModal = function(orderId, orderNum) {
        const numEl = document.getElementById('modal-rating-order-num');
        const form = document.getElementById('rating-form');
        if (numEl) numEl.textContent = orderNum;
        if (form) form.action = `/customer/orders/${orderId}/rate`;
        ratingModal?.classList.remove('hidden');
        ratingModal?.classList.add('flex');
        // Initial joyful burst upon popup open
        setTimeout(fireCelebrationConfetti, 250);
    };

    // Auto-fire confetti if rating modal is shown on initial load
    if (ratingModal && !ratingModal.classList.contains('hidden')) {
        setTimeout(fireCelebrationConfetti, 400);
    }

    window.confirmCustomerLogout = function() {
        if (window.TrowaConfirm) {
            window.TrowaConfirm({
                title: 'Confirm Sign Out',
                message: 'Are you sure you want to sign out from your laundry patron portal?',
                hint: 'You can log back in anytime using your phone or email to track active laundry batches.',
                badge: 'Patron Security',
                type: 'warning',
                confirmText: 'Yes, Sign Out',
                cancelText: 'Stay Signed In'
            }, function() {
                document.getElementById('customer-logout-form')?.submit();
            });
        } else if (confirm('Are you sure you want to sign out?')) {
            document.getElementById('customer-logout-form')?.submit();
        }
    };
});
</script>
@endpush
@endsection
