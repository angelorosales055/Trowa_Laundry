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
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3.5">
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

            <!-- Quick Action: Fast Order Launch Button (Floor Staff Only) -->
            @if(auth()->user()->role === 'staff')
                <div class="p-4 lg:px-5 lg:pt-4 lg:pb-2">
                    <button type="button" data-modal-open="order-modal" class="w-full flex items-center justify-center gap-2.5 rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-4 py-3 font-recoleta text-base font-bold text-[#FFFDF8] shadow-[3px_3px_0px_#182830] transition hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 cursor-pointer">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Laundry Intake</span>
                    </button>
                </div>
            @endif

            <!-- Spacious Nav Links -->
            <nav class="flex-1 space-y-1.5 overflow-y-auto p-4 lg:p-5 pt-2">
                @if(auth()->user()->role === 'staff')
                    <p class="px-3 pt-2 pb-1 font-mono text-[11px] font-bold uppercase tracking-widest text-[#A2C5D8]">Staff Operations</p>
                    @php($nav = [
                        ['dashboard', 'Staff Station', 'dashboard', 'Live operations deck & machine glance'],
                        ['orders.index', 'Laundry Queue', 'queue', 'Orders intake, weights & settlement'],
                        ['schedule.index', 'Machine Bay Showroom', 'machines', 'Live 16-unit floor showroom & stages'],
                        ['customers.index', 'Customer Directory', 'customers', 'Client accounts & contact stubs'],
                    ])
                @else
                    <p class="px-3 pt-2 pb-1 font-mono text-[11px] font-bold uppercase tracking-widest text-[#A2C5D8]">Executive Management</p>
                    @php($nav = [
                        ['dashboard', 'Executive Dashboard', 'dashboard', 'Financial metrics, volume & fleet glance'],
                        ['customers.insights', 'Customer Insights', 'insights', 'LTV, repeat rates & VIP intelligence'],
                        ['inventory.index', 'Supply Inventory & Helper', 'inventory', 'Stockroom, burn rate & smart helper'],
                        ['reports', 'Business Reports & Plans', 'reports', 'Financial audits, billing ledger & strategy'],
                        ['expenses.index', 'Shop Expenses', 'expenses', 'Operating costs & vendor audits'],
                        ['services.index', 'Service Catalog', 'services', 'Pricing configuration & laundry services'],
                    ])
                @endif

                @foreach($nav as [$route, $label, $iconKey, $hint])
                    @php($isActive = request()->routeIs($route))
                    <a href="{{ route($route) }}" 
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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Sign out" class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#F7E6CB] text-[#CB1B03] shadow-[1px_1px_0px_#182830] transition hover:bg-[#CB1B03] hover:text-white active:translate-x-0.5 active:translate-y-0.5 cursor-pointer">
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
    });
</script>
@stack('scripts')
</body>
</html>
