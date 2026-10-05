@extends('layouts_app')

@section('content')
<style>
    /* Machine body vigorous vibration/shake */
    @keyframes robotVibrateShake {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        8% { transform: translate(-5px, 2px) rotate(-4deg); }
        16% { transform: translate(5px, -2px) rotate(4deg); }
        24% { transform: translate(-4px, -3px) rotate(-3deg); }
        32% { transform: translate(4px, 3px) rotate(3deg); }
        40% { transform: translate(-5px, 1px) rotate(-4deg); }
        48% { transform: translate(5px, -1px) rotate(4deg); }
        56% { transform: translate(-3px, -2px) rotate(-2deg); }
        64% { transform: translate(3px, 2px) rotate(2deg); }
        72% { transform: translate(-2px, 1px) rotate(-1.5deg); }
        80% { transform: translate(2px, -1px) rotate(1.5deg); }
        88% { transform: translate(-1px, 0px) rotate(-0.5deg); }
    }
    .robot-shaking {
        animation: robotVibrateShake 1.25s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }

    /* Drum character rapid 1440deg (4 full loops) face spin */
    @keyframes robotSpinFace {
        0% { transform: rotate(0deg); }
        20% { transform: rotate(360deg); }
        45% { transform: rotate(720deg); }
        70% { transform: rotate(1080deg); }
        90% { transform: rotate(1380deg); }
        100% { transform: rotate(1440deg); }
    }
    .robot-face-spinning {
        transform-origin: 140px 136px;
        animation: robotSpinFace 1.25s cubic-bezier(0.25, 0.1, 0.25, 1) both;
    }

    .robot-blinking {
        transform-origin: 140px 128px;
        transform: scaleY(0.08);
        transition: transform 0.08s ease-in-out;
    }
    .trowa-robot-shadow {
        filter: drop-shadow(0 6px 12px rgba(24, 40, 48, 0.25));
    }

    /* Soapy Iridescent Floating Bubbles */
    .soap-bubble {
        position: absolute;
        border-radius: 9999px;
        background: radial-gradient(circle at 35% 35%, rgba(255, 255, 255, 0.95) 0%, rgba(186, 230, 253, 0.65) 45%, rgba(244, 114, 182, 0.45) 75%, rgba(125, 211, 252, 0.7) 100%);
        box-shadow: inset -2px -2px 5px rgba(56, 189, 248, 0.5), 0 0 10px rgba(186, 230, 253, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.85);
        pointer-events: none;
        transform: translate(-50%, -50%) scale(0);
        z-index: 35;
    }
    .soap-bubble::after {
        content: '';
        position: absolute;
        top: 18%;
        left: 22%;
        width: 25%;
        height: 25%;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.9);
    }
    @keyframes bubbleFloatPop {
        0% {
            transform: translate(-50%, -50%) scale(0) translate(0, 0);
            opacity: 0.95;
        }
        20% {
            transform: translate(-50%, -50%) scale(1) translate(calc(var(--bx) * 0.35), calc(var(--by) * 0.35));
            opacity: 1;
        }
        75% {
            transform: translate(-50%, -50%) scale(1.15) translate(var(--bx), var(--by));
            opacity: 0.9;
        }
        92% {
            transform: translate(-50%, -50%) scale(1.3) translate(var(--bx), calc(var(--by) - 12px));
            opacity: 0.75;
        }
        100% {
            transform: translate(-50%, -50%) scale(1.65) translate(var(--bx), calc(var(--by) - 18px));
            opacity: 0;
        }
    }

    /* --- 3D Retro Washing Machine Portal & Scrollytelling Styles --- */
    .perspective-stage {
        perspective: 1600px;
        transform-style: preserve-3d;
    }
    .door-3d-hinge {
        transform-origin: 0% 50%;
        transform-style: preserve-3d;
        will-change: transform;
        transition: transform 0.05s ease-out;
    }
    .drum-chamber-panel {
        will-change: opacity, transform;
        transition: opacity 0.2s ease-out, transform 0.2s ease-out;
    }
    .drum-concentric-bg {
        background: radial-gradient(circle at 50% 50%, #16384A 0%, #0E232F 50%, #061117 100%);
    }

    @media (prefers-reduced-motion: reduce) {
        .door-3d-hinge {
            transform: none !important;
        }
        .drum-chamber-panel {
            opacity: 1 !important;
            transform: none !important;
        }
    }
</style>

<div class="relative w-full">
    <!-- Top Retro Laundromat Illuminated Canopy / Marquee -->
    <header class="relative w-full border-b-4 border-[#182830] bg-[#1E6482] py-2.5 px-4 text-[#FFFDF8] shadow-[0_4px_0_#182830] select-none">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 text-xs font-mono">
            <!-- Left: Vintage Bulb Trim + Brand Marquee -->
            <div class="flex items-center gap-2.5">
                <span class="flex items-center gap-1">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#CB1B03] shadow-[0_0_8px_#CB1B03] animate-pulse"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-[#F59E0B] shadow-[0_0_8px_#F59E0B]"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-[#10B981] shadow-[0_0_8px_#10B981] animate-pulse"></span>
                </span>
                <span class="font-recoleta text-sm sm:text-base font-black tracking-wide text-[#F7E6CB]">
                    ⚡ TROWA COMMERCIAL VINTAGE LAUNDROMAT · COUNTER TERMINAL #01 ⚡
                </span>
            </div>

            <!-- Right: Real-time Shift & Machine Bay Status Ticker -->
            <div class="flex items-center gap-4 text-[11px] text-[#A2C5D8]">
                <span class="hidden md:inline-flex items-center gap-1.5 font-bold text-[#FFFDF8]">
                    <span class="inline-block h-2 w-2 rounded-full bg-[#10B981]"></span>
                    SHIFT: MORNING (07:00 – 15:00)
                </span>
                <span class="hidden sm:inline-flex items-center gap-1.5 rounded-md border border-[#182830] bg-[#14232B] px-2 py-0.5 text-[#F7E6CB]">
                    <span>FLEET:</span>
                    <span class="font-bold text-[#BAE6FD]">10 UNITS ACTIVE</span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-md border border-[#182830] bg-[#FFFDF8] px-2.5 py-0.5 text-[#182830] font-bold shadow-[2px_2px_0px_#182830]">
                    <span class="h-2 w-2 rounded-full bg-[#10B981] animate-ping"></span>
                    COUNTER OPEN
                </span>
            </div>
        </div>
    </header>

    <!-- Top Section: Station Sign-In & Mascot Companion -->
    <div id="login-station" class="relative flex min-h-[calc(100vh-140px)] flex-col items-center justify-center p-3 sm:p-6 lg:p-8">
        <div class="relative grid w-full max-w-7xl overflow-hidden rounded-3xl border-4 border-[#182830] bg-[#FFFDF8] shadow-[12px_12px_0px_#182830] lg:grid-cols-12">
        
        <!-- Left Side: Interactive Robot Companion & Laundromat Showcase -->
        <section class="relative flex flex-col justify-between overflow-hidden border-b-4 border-[#182830] bg-[#25799B] p-6 text-[#FFFDF8] sm:p-8 lg:col-span-5 lg:border-b-0 lg:border-r-4 lg:p-8">
            <!-- Retro Laundromat Dot Grid Pattern -->
            <div class="pointer-events-none absolute inset-0 opacity-10" style="background-image: radial-gradient(#FFFDF8 1.5px, transparent 1.5px); background-size: 20px 20px;"></div>

            <!-- Top Header & Brand -->
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                            <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="3" fill="#FFFDF8"/>
                                <circle cx="12" cy="13" r="5" stroke="#25799B" stroke-width="2"/>
                                <path d="M10 13c.5-.8 1.5-.8 2 0s1.5.8 2 0" stroke="#CB1B03" stroke-width="2"/>
                                <circle cx="7" cy="6.5" r="1" fill="#25799B"/>
                                <circle cx="10" cy="6.5" r="1" fill="#25799B"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-recoleta text-xl font-black tracking-tight text-[#F7E6CB]">Trowa Laundry</h1>
                            <p class="font-mono text-[10px] uppercase tracking-wider text-[#A2C5D8]">Staff & Counter Portal</p>
                        </div>
                    </div>

                    <div class="rounded-lg border-2 border-[#182830] bg-[#CB1B03] px-2.5 py-1 font-mono text-[10px] font-bold uppercase tracking-widest text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                        Retro Care · Est. 2024
                    </div>
                </div>

                <h2 class="mt-2.5 font-recoleta text-xl sm:text-2xl font-black leading-tight text-[#FFFDF8]">
                    Cleaner clothes, <span class="text-[#F7E6CB]">happier shifts.</span>
                </h2>
            </div>

            <!-- Center Stage: Interactive Mini Washing Machine Robot -->
            <div class="relative z-10 my-auto flex flex-col items-center justify-center py-4">
                
                <!-- Interactive Speech Bubble -->
                <div id="robot-speech-wrapper" class="relative mb-2 transition-transform duration-300">
                    <div id="robot-speech" class="relative max-w-[270px] rounded-2xl border-2 border-[#182830] bg-[#FFFDF8] px-3.5 py-2 text-center font-mono text-xs font-bold text-[#182830] shadow-[3px_3px_0px_#182830] transition-colors duration-300">
                        🫧 <span id="robot-speech-text">Hi! I'm Trowa-Bot! Ready to wash?</span>
                    </div>
                    <!-- Speech Triangle Pointer -->
                    <div class="mx-auto h-0 w-0 border-x-6 border-x-transparent border-t-8 border-t-[#182830]"></div>
                </div>

                <!-- Robot SVG Container -->
                <div id="robot-interactive-wrapper" class="relative flex items-center justify-center cursor-pointer select-none" title="Click me for a quick spin cycle!">
                    <!-- Floating Bubbles Layer -->
                    <div id="robot-bubbles-layer" class="pointer-events-none absolute inset-0 overflow-visible z-30"></div>

                    <svg id="trowa-robot-svg" class="trowa-robot-shadow w-52 sm:w-56 h-auto transition-transform duration-300 ease-out" viewBox="0 0 280 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <!-- Body Clip for Two-Tone Cel-Shading -->
                            <clipPath id="robot-body-clip">
                                <rect x="40" y="16" width="200" height="236" rx="38" />
                            </clipPath>
                            <!-- Drum Porthole Clip -->
                            <clipPath id="robot-drum-clip">
                                <circle cx="140" cy="136" r="43" />
                            </clipPath>
                        </defs>

                        <!-- Ground Cast Shadow -->
                        <ellipse cx="140" cy="275" rx="94" ry="9" fill="#143644" opacity="0.45" />

                        <!-- Bottom Feet -->
                        <rect x="70" y="248" width="34" height="18" rx="6" fill="#182830" />
                        <rect x="176" y="248" width="34" height="18" rx="6" fill="#182830" />

                        <!-- Main Machine Body (Split Cel-Shading) -->
                        <g clip-path="url(#robot-body-clip)">
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
                        <g id="robot-vents">
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
                        <g id="robot-screen">
                            <rect x="122" y="32" width="38" height="18" rx="4" fill="#182830" />
                            <rect x="125" y="35" width="32" height="12" rx="2" fill="#0284C7" />
                            <text x="141" y="44" font-family="monospace" font-size="7.5" font-weight="900" fill="#BAE6FD" text-anchor="middle" letter-spacing="1">LIVE</text>
                        </g>

                        <!-- Right Rotary Dial / Knob -->
                        <g id="robot-dial">
                            <circle cx="188" cy="41" r="15" fill="#182830" />
                            <circle cx="188" cy="41" r="11" fill="#4B5660" />
                            <rect x="186.5" y="32" width="3" height="5" rx="1.5" fill="#FFFDF8" />
                        </g>

                        <!-- Porthole Outer Door Frame -->
                        <g id="robot-door-assembly">
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
                        <g clip-path="url(#robot-drum-clip)">
                            <!-- Dark Drum Interior -->
                            <circle cx="140" cy="136" r="43" fill="#182830" />

                            <!-- Spinning Character Face Group -->
                            <g id="robot-spinning-face" style="transform-origin: 140px 136px;">
                                <!-- Coral Round Face Character -->
                                <circle id="robot-face-circle" cx="140" cy="136" r="39" fill="#EF6D65" />
                                <!-- Face Bottom Shadow -->
                                <path d="M 101 136 A 39 39 0 0 0 179 136 Q 140 168 101 136 Z" fill="#DC544B" opacity="0.35" />

                                <!-- Cheeks (Blush) -->
                                <rect id="robot-left-cheek" x="114" y="141" width="8" height="6" rx="2" fill="#FF8D85" />
                                <rect id="robot-right-cheek" x="158" y="141" width="8" height="6" rx="2" fill="#FF8D85" />

                                <!-- Smile Mouth -->
                                <path id="robot-mouth" d="M 134 139 Q 140 147 146 139" fill="none" stroke="#182830" stroke-width="4" stroke-linecap="round" />
                                <!-- Dizzy/Excited Mouth (Shown during spin) -->
                                <ellipse id="robot-mouth-dizzy" cx="140" cy="144" rx="4.5" ry="6" fill="#182830" style="display: none;" />

                                <!-- Eyes: OPEN STATE (Pupils track mouse) -->
                                <g id="robot-eyes-open">
                                    <!-- Left Eye Base Socket & Pupil -->
                                    <g id="robot-left-eye-open">
                                        <circle cx="116" cy="128" r="11" fill="#182830" />
                                        <g id="robot-left-pupil" style="transform-origin: 116px 128px; transition: transform 0.04s ease-out;">
                                            <circle cx="116" cy="128" r="10.5" fill="#182830" />
                                            <circle cx="112.5" cy="124.5" r="3.8" fill="#FFFFFF" />
                                            <circle cx="119.5" cy="131" r="1.5" fill="#FFFFFF" />
                                        </g>
                                    </g>

                                    <!-- Right Eye Base Socket & Pupil (Peeks toward password field) -->
                                    <g id="robot-right-eye-open">
                                        <circle cx="164" cy="128" r="11" fill="#182830" />
                                        <g id="robot-right-pupil" style="transform-origin: 164px 128px; transition: transform 0.04s ease-out;">
                                            <circle cx="164" cy="128" r="10.5" fill="#182830" />
                                            <circle cx="160.5" cy="124.5" r="3.8" fill="#FFFFFF" />
                                            <circle cx="167.5" cy="131" r="1.5" fill="#FFFFFF" />
                                        </g>
                                    </g>
                                </g>

                                <!-- Eyes: CLOSED STATE (Revealed when password toggle is clicked) -->
                                <g id="robot-eyes-closed" style="display: none;">
                                    <!-- Left Closed Eye (Happy shy curve) -->
                                    <path id="robot-left-eye-closed" d="M 106 130 Q 116 118 126 130" fill="none" stroke="#182830" stroke-width="4.2" stroke-linecap="round" />
                                    <!-- Right Closed Eye -->
                                    <path id="robot-right-eye-closed" d="M 154 130 Q 164 118 174 130" fill="none" stroke="#182830" stroke-width="4.2" stroke-linecap="round" />
                                    <!-- Deep Shy Blush -->
                                    <ellipse id="robot-shy-blush-left" cx="115" cy="142" rx="7.5" ry="5.5" fill="#FF4757" opacity="0.9" />
                                    <ellipse id="robot-shy-blush-right" cx="165" cy="142" rx="7.5" ry="5.5" fill="#FF4757" opacity="0.9" />
                                </g>
                            </g>

                            <!-- Robot Hands / Paws covering eyes when password is shown -->
                            <g id="robot-paws" style="transform: translateY(55px); opacity: 0; transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease;">
                                <!-- Left Paw / Mitt -->
                                <g id="robot-left-paw" style="transform-origin: 114px 138px; transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);">
                                    <path d="M 100 138 C 100 120, 126 120, 128 134 C 130 144, 114 148, 100 138 Z" fill="#FFFFFF" stroke="#182830" stroke-width="3.5" stroke-linejoin="round" />
                                    <line x1="110" y1="125" x2="112" y2="135" stroke="#182830" stroke-width="2" stroke-linecap="round" />
                                    <line x1="118" y1="125" x2="119" y2="135" stroke="#182830" stroke-width="2" stroke-linecap="round" />
                                </g>

                                <!-- Right Paw / Mitt (Peeks open and snaps shut) -->
                                <g id="robot-right-paw" style="transform-origin: 166px 138px; transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);">
                                    <path d="M 180 138 C 180 120, 154 120, 152 134 C 150 144, 166 148, 180 138 Z" fill="#FFFFFF" stroke="#182830" stroke-width="3.5" stroke-linejoin="round" />
                                    <line x1="170" y1="125" x2="168" y2="135" stroke="#182830" stroke-width="2" stroke-linecap="round" />
                                    <line x1="162" y1="125" x2="161" y2="135" stroke="#182830" stroke-width="2" stroke-linecap="round" />
                                </g>
                            </g>
                        </g>

                        <!-- Bottom Filter Hatch Door (Right) -->
                        <g id="robot-filter-door">
                            <rect x="178" y="212" width="26" height="22" rx="5" fill="#8C9CA8" stroke="#182830" stroke-width="4" />
                            <rect x="183" y="217" width="16" height="12" rx="2" fill="#182830" opacity="0.3" />
                        </g>
                    </svg>
                </div>

                <!-- Mascot Info Tag -->
                <p class="mt-3 flex items-center gap-1.5 font-mono text-[10px] text-[#A2C5D8]">
                    <span class="inline-block h-2 w-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span>Interactive Sentinel · Eye tracking & secret safeguard</span>
                </p>
            </div>

            <!-- Live Machine Bay Analog Gauges -->
            <div class="relative z-10 my-3 grid grid-cols-3 gap-2 font-mono text-center">
                <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                    <span class="block text-[9px] uppercase tracking-wider text-[#A2C5D8]">Steam Core</span>
                    <strong class="text-xs font-bold text-[#10B981]">60°C Active</strong>
                </div>
                <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                    <span class="block text-[9px] uppercase tracking-wider text-[#A2C5D8]">Vortex Spin</span>
                    <strong class="text-xs font-bold text-[#BAE6FD]">1400 RPM</strong>
                </div>
                <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                    <span class="block text-[9px] uppercase tracking-wider text-[#A2C5D8]">Sachet Lab</span>
                    <strong class="text-xs font-bold text-[#F7E6CB]">100% Ready</strong>
                </div>
            </div>

            <!-- Bottom: 3 Mini Steps -->
            <div class="relative z-10 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="rounded-xl border-2 border-[#182830] bg-[#1E6482] p-2 shadow-[2px_2px_0px_#182830]">
                    <strong class="block font-mono text-sm text-[#F7E6CB]">01</strong>
                    <span class="text-[10px] text-[#A2C5D8]">Drop-off</span>
                </div>
                <div class="rounded-xl border-2 border-[#182830] bg-[#1E6482] p-2 shadow-[2px_2px_0px_#182830]">
                    <strong class="block font-mono text-sm text-[#F7E6CB]">02</strong>
                    <span class="text-[10px] text-[#A2C5D8]">Wash & Dry</span>
                </div>
                <div class="rounded-xl border-2 border-[#182830] bg-[#1E6482] p-2 shadow-[2px_2px_0px_#182830]">
                    <strong class="block font-mono text-sm text-[#F7E6CB]">03</strong>
                    <span class="text-[10px] text-[#A2C5D8]">Pick-up</span>
                </div>
            </div>
        </section>

        <!-- Right Side: Tactical Sign-in Form & Live Laundromat Operations Board -->
        <section class="flex flex-col justify-between bg-[#FFFDF8] p-6 sm:p-8 lg:col-span-7 lg:p-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
                <!-- Left Sub-column: The Sign-in Form -->
                <div class="md:col-span-7 flex flex-col justify-between">
                    <div>
                        <div class="mb-5">
                            <span class="inline-block rounded-md border border-[#182830] bg-[#CB1B03] px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-[#FFFDF8] shadow-[1px_1px_0px_#182830]">
                                Terminal #01 · Shift Auth
                            </span>
                            <h2 class="font-recoleta text-2xl sm:text-3xl font-black text-[#182830] mt-1.5 leading-tight">
                                Ready for your shift?
                            </h2>
                            <p class="text-xs text-[#25799B] font-semibold mt-1">
                                Enter your credentials to access the counter terminal.
                            </p>
                        </div>

                        @if($errors->any())
                            <div class="mb-5 flex items-center gap-2.5 rounded-xl border-2 border-[#182830] bg-[#CB1B03] p-3 text-xs font-bold text-white shadow-[2px_2px_0px_#182830]">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="username-input" class="block font-mono text-xs font-bold text-[#182830] mb-1">
                                    Staff Username
                                </label>
                                <input id="username-input" name="username" value="{{ old('username', 'staff123') }}" type="text" autocomplete="username" class="field text-sm" placeholder="e.g. staff123" required autofocus>
                            </div>

                            <div>
                                <label for="login-password" class="block font-mono text-xs font-bold text-[#182830] mb-1">
                                    Password
                                </label>
                                <div class="relative">
                                    <input id="login-password" name="password" type="password" value="pass123" autocomplete="current-password" class="field pr-16 text-sm" placeholder="Enter password" required>
                                    <button type="button" id="toggle-password" class="absolute inset-y-0 right-2 my-auto h-7 px-2 text-[11px] font-mono font-bold text-[#25799B] hover:text-[#CB1B03] cursor-pointer">
                                        Show
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="retro-btn-primary w-full py-3 text-sm mt-2">
                                Sign In to Station ➔
                            </button>
                        </form>

                        <!-- Quick Fill Demo Buttons for Local Prototype -->
                        <div class="mt-5 rounded-2xl border-2 border-dashed border-[#182830]/30 bg-[#F7E6CB]/40 p-3">
                            <p class="text-center font-mono text-[10px] font-bold uppercase text-[#25799B] mb-2">Quick Fill Demo Access</p>
                            <div class="flex gap-2">
                                <button type="button" onclick="fillCreds('staff123', 'pass123')" class="flex-1 rounded-lg border border-[#182830] bg-[#FFFDF8] py-1.5 font-mono text-[11px] font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#A2C5D8] transition cursor-pointer">
                                    Staff User
                                </button>
                                <button type="button" onclick="fillCreds('admin123', 'pass123')" class="flex-1 rounded-lg border border-[#182830] bg-[#FFFDF8] py-1.5 font-mono text-[11px] font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#A2C5D8] transition cursor-pointer">
                                    Admin User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sub-column: Live Operating Board & Laundromat Amenities -->
                <div class="md:col-span-5 flex flex-col justify-between space-y-4 border-t-2 md:border-t-0 md:border-l-2 border-[#182830]/20 pt-4 md:pt-0 md:pl-5 font-mono">
                    <!-- Operating Hours & Shift Lead -->
                    <div class="rounded-2xl border-2 border-[#182830] bg-[#14232B] p-3 text-[#FFFDF8] shadow-[3px_3px_0px_#182830]">
                        <div class="flex items-center justify-between text-[10px] text-[#A2C5D8]">
                            <span>STORE HOURS</span>
                            <span class="text-[#10B981] font-bold">● OPEN NOW</span>
                        </div>
                        <p class="mt-1 font-recoleta text-lg font-bold text-[#F7E6CB]">7:00 AM – 10:00 PM</p>
                        <p class="text-[10px] text-[#BAE6FD]">Shift Lead: Carla Mendoza</p>
                    </div>

                    <!-- 4 Guarantees & Features -->
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-2 rounded-xl border border-[#182830] bg-[#FFFDF8] p-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                            <span class="text-base">🧼</span>
                            <div class="text-[11px] leading-tight">
                                <strong class="block text-[#CB1B03]">Per-Sachet Calibrated</strong>
                                <span class="text-[10px] text-slate-600">Zero residue Surf & Downy.</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 rounded-xl border border-[#182830] bg-[#FFFDF8] p-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                            <span class="text-base">🔒</span>
                            <div class="text-[11px] leading-tight">
                                <strong class="block text-[#25799B]">100% Dedicated Drum</strong>
                                <span class="text-[10px] text-slate-600">Garments never mingle.</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 rounded-xl border border-[#182830] bg-[#FFFDF8] p-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                            <span class="text-base">⚡</span>
                            <div class="text-[11px] leading-tight">
                                <strong class="block text-[#10B981]">45-Min Express</strong>
                                <span class="text-[10px] text-slate-600">Rapid moisture extraction.</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 rounded-xl border border-[#182830] bg-[#FFFDF8] p-2 text-[#182830] shadow-[2px_2px_0px_#182830]">
                            <span class="text-base">📦</span>
                            <div class="text-[11px] leading-tight">
                                <strong class="block text-[#8B5CF6]">Kraft Seal Packaging</strong>
                                <span class="text-[10px] text-slate-600">Freshness locked in kraft.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Pricing Strip -->
                    <div class="rounded-xl border border-[#182830] bg-[#E0F2FE] p-2.5 text-[#182830] text-[10px]">
                        <span class="block font-bold text-[#0369A1] uppercase tracking-wider mb-1">Standard Rates:</span>
                        <div class="flex justify-between font-bold">
                            <span>Wash: ₱60</span>
                            <span>Dry: ₱50</span>
                            <span>Full: ₱130</span>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center font-mono text-[11px] text-slate-500 border-t border-[#182830]/15 pt-4">
                Trowa Laundry System © {{ date('Y') }} · All rights reserved.
            </p>
        </section>
    </div>

    <!-- Scroll Down Prompt to Unlock the Drum (Wide Illuminated Vintage Sign) -->
    <div class="mt-8 text-center px-4">
        <a href="#trowa-drum-portal" id="scroll-prompt-btn" class="group inline-flex flex-col items-center gap-2 rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] px-8 py-4 font-mono shadow-[6px_6px_0px_#182830] transition hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[3px_3px_0px_#182830] hover:bg-[#F7E6CB] max-w-2xl w-full">
            <span class="flex items-center gap-2 text-xs sm:text-sm font-black uppercase tracking-wider text-[#CB1B03]">
                <span class="inline-block h-2.5 w-2.5 rounded-full bg-[#CB1B03] animate-ping"></span>
                Scroll Down to Unlock the Commercial Wash Drum
            </span>
            <span class="text-[11px] sm:text-xs font-semibold text-[#25799B]">
                Step inside our vintage 1974 industrial washing machine & explore the inner vortex ▾
            </span>
            <div class="mt-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-[#182830] bg-[#25799B] text-[#FFFDF8] shadow-[2px_2px_0px_#182830] animate-bounce">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </a>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Section 2: Scrollytelling Portal: The Giant Retro Washing Machine Drum   -->
<!-- Full-Bleed 100vw × 100vh Viewport Universe: The Entire Page Is Ours!     -->
<!-- Each scroll incrementally unlatches the lid, swings open the 3D door,   -->
<!-- reveals the world inside, and reversibly closes when scrolled back up!   -->
<!-- ========================================================================= -->
<section id="trowa-drum-portal" class="relative w-full min-h-[380vh]">
    <!-- Sticky Viewport Stage (Locks in view while user scrolls through the 380vh runway) -->
    <div id="portal-machine-frame" class="sticky top-0 flex h-screen w-full flex-col overflow-hidden bg-[#071319] text-[#FFFDF8]">
        
        <!-- Top Full-Bleed Machine Control Console Bar -->
        <div class="relative z-40 flex w-full flex-wrap items-center justify-between border-b-4 border-[#182830] bg-[#1E6482] px-4 py-3 sm:px-8 shadow-[0_4px_0_#182830]">
            <!-- Brand Badge -->
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <circle cx="12" cy="13" r="4.5"/>
                        <path d="M10 13c.5-.8 1.5-.8 2 0s1.5.8 2 0"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-recoleta text-lg sm:text-xl font-black tracking-tight text-[#F7E6CB]">TROWA ARCHITECTURAL VORTEX</h3>
                    <p class="font-mono text-[9px] sm:text-[10px] uppercase tracking-widest text-[#A2C5D8]">SER. NO. 1974-TL · FULL-BAY DUAL-PHASE CYLINDER</p>
                </div>
            </div>

            <!-- Retro Analog Gauges & Indicators -->
            <div class="flex items-center gap-2 sm:gap-4 font-mono text-[10px] sm:text-xs">
                <div class="hidden sm:flex items-center gap-1.5 rounded-lg border-2 border-[#182830] bg-[#14232B] px-3 py-1 text-[#F7E6CB]">
                    <span class="text-[#A2C5D8]">STEAM:</span>
                    <span class="font-bold text-[#10B981]">60°C THERMAL</span>
                </div>
                <div class="hidden md:flex items-center gap-1.5 rounded-lg border-2 border-[#182830] bg-[#14232B] px-3 py-1 text-[#F7E6CB]">
                    <span class="text-[#A2C5D8]">SPIN:</span>
                    <span class="font-bold text-[#BAE6FD]">1400 RPM</span>
                </div>
                <div class="hidden lg:flex items-center gap-1.5 rounded-lg border-2 border-[#182830] bg-[#14232B] px-3 py-1 text-[#F7E6CB]">
                    <span class="text-[#A2C5D8]">PURITY:</span>
                    <span class="font-bold text-[#F59E0B]">99.8% OZONE</span>
                </div>
                <div class="flex items-center gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 text-[#182830] font-bold shadow-[2px_2px_0px_#182830]">
                    <span id="portal-pilot-light" class="h-2.5 w-2.5 rounded-full bg-[#CB1B03] animate-pulse"></span>
                    <span id="portal-status-text" class="tracking-wider">DRUM LOCKED</span>
                </div>
            </div>
        </div>

        <!-- Center Full-Bleed Panoramic Stage: The Entire Viewport Is The Machine Universe! -->
        <div class="relative flex-1 w-full h-full overflow-hidden perspective-stage">
            
            <!-- Radiant Wash Glow Flare (Fades in across entire screen as door swings open) -->
            <div id="portal-drum-glow" class="pointer-events-none absolute inset-0 z-10 opacity-0 transition-opacity duration-300" style="background: radial-gradient(circle at 50% 50%, rgba(0, 229, 255, 0.45) 0%, rgba(245, 158, 11, 0.22) 50%, rgba(7, 19, 25, 0.85) 90%); filter: blur(30px);"></div>

            <!-- Floating Soap Bubbles across the full viewport -->
            <div class="pointer-events-none absolute inset-0 z-20 overflow-hidden">
                <div class="soap-bubble" style="left: 15%; top: 70%; width: 28px; height: 28px; --bx: -35px; --by: -140px; animation: bubbleFloatPop 3.2s infinite 0.1s;"></div>
                <div class="soap-bubble" style="left: 35%; top: 80%; width: 22px; height: 22px; --bx: 25px; --by: -110px; animation: bubbleFloatPop 2.6s infinite 0.6s;"></div>
                <div class="soap-bubble" style="left: 55%; top: 75%; width: 34px; height: 34px; --bx: -20px; --by: -160px; animation: bubbleFloatPop 3.5s infinite 1.2s;"></div>
                <div class="soap-bubble" style="left: 75%; top: 85%; width: 24px; height: 24px; --bx: 30px; --by: -120px; animation: bubbleFloatPop 2.9s infinite 0.4s;"></div>
                <div class="soap-bubble" style="left: 88%; top: 65%; width: 30px; height: 30px; --bx: -25px; --by: -130px; animation: bubbleFloatPop 3.1s infinite 1.5s;"></div>
            </div>

            <!-- Full-Screen Stainless Steel Drum Interior Cavity (Houses the Panoramic Chambers) -->
            <div id="portal-drum-interior" class="relative z-10 flex h-full w-full items-center justify-center overflow-hidden drum-concentric-bg p-4 sm:p-8 lg:p-12 text-center" style="background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px); background-size: 20px 20px;">
                
                <!-- Water ripple wave flare at bottom -->
                <div class="pointer-events-none absolute bottom-0 inset-x-0 h-40 bg-gradient-to-t from-[#0284C7]/20 via-[#0EA5E9]/10 to-transparent"></div>

                <!-- ============================================== -->
                <!-- Chamber 1: The Artisan Alchemy (Scroll 20%-48%) -->
                <!-- ============================================== -->
                <div id="portal-chamber-1" class="drum-chamber-panel absolute inset-x-4 sm:inset-x-8 lg:inset-x-16 max-w-6xl mx-auto flex flex-col items-center justify-center p-4 sm:p-6 text-center opacity-0 translate-y-8 pointer-events-none">
                    <span class="inline-block rounded-xl border-2 border-[#182830] bg-[#CB1B03] px-3.5 py-1 font-mono text-xs font-bold uppercase tracking-widest text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                        Chamber 01 · The Artisan Alchemy & Wash Laboratory
                    </span>
                    <h4 class="mt-3 font-recoleta text-2xl sm:text-4xl lg:text-5xl font-black text-[#F7E6CB] leading-tight drop-shadow-md">
                        Where Every Thread is Treated with Respect.
                    </h4>
                    <p class="mt-2 text-xs sm:text-sm lg:text-base text-[#BAE6FD] max-w-3xl font-medium leading-relaxed">
                        Inside our commercial drums, clothes aren't tossed around violently. Our balanced dampeners preserve delicate fibers while 60°C thermal steam lifts deep dirt.
                    </p>

                    <!-- 3 Wide Blueprint Cards in Panoramic Grid -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-left font-mono w-full max-w-5xl">
                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 text-[#182830] shadow-[5px_5px_0px_#182830] transition hover:-translate-y-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#CB1B03] text-white text-sm">🧼</span>
                                <strong class="text-sm font-black text-[#CB1B03]">Per-Sachet Chemistry</strong>
                            </div>
                            <p class="text-xs text-slate-700 leading-snug">
                                Accurately dosed Surf & Downy sachets calibrated per batch. Zero detergent film, pure softness, and hypoallergenic freshness.
                            </p>
                            <span class="mt-2.5 inline-block text-[10px] font-bold text-[#25799B] bg-[#E0F2FE] px-2 py-0.5 rounded border border-[#BAE6FD]">
                                100% PRE-PORTIONED
                            </span>
                        </div>

                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 text-[#182830] shadow-[5px_5px_0px_#182830] transition hover:-translate-y-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#0284C7] text-white text-sm">🌀</span>
                                <strong class="text-sm font-black text-[#0284C7]">1400 RPM Harmonic Spin</strong>
                            </div>
                            <p class="text-xs text-slate-700 leading-snug">
                                Direct-drive centrifugal extraction pulls 85% of moisture in minutes, cutting dryer tumble stress and extending garment lifespan.
                            </p>
                            <span class="mt-2.5 inline-block text-[10px] font-bold text-[#0284C7] bg-[#E0F2FE] px-2 py-0.5 rounded border border-[#BAE6FD]">
                                ZERO FIBER FATIGUE
                            </span>
                        </div>

                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 text-[#182830] shadow-[5px_5px_0px_#182830] transition hover:-translate-y-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#10B981] text-white text-sm">🔒</span>
                                <strong class="text-sm font-black text-[#10B981]">100% Dedicated Drum</strong>
                            </div>
                            <p class="text-xs text-slate-700 leading-snug">
                                Your laundry never mixes with stranger garments. One customer basket = one hermetically sealed drum cycle guaranteed.
                            </p>
                            <span class="mt-2.5 inline-block text-[10px] font-bold text-[#10B981] bg-[#D1FAE5] px-2 py-0.5 rounded border border-[#A7F3D0]">
                                ZERO CROSS-CONTAMINATION
                            </span>
                        </div>
                    </div>

                    <!-- Technical Engineering Telemetry Strip -->
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3 sm:gap-6 font-mono text-[11px] text-[#A2C5D8] border-t border-[#FFFDF8]/15 pt-4">
                        <span><strong class="text-[#F7E6CB]">Thermal Steam:</strong> 60°C Sanitized</span>
                        <span>·</span>
                        <span><strong class="text-[#F7E6CB]">Water pH:</strong> 6.8 Balanced</span>
                        <span>·</span>
                        <span><strong class="text-[#F7E6CB]">Drum Volume:</strong> 85 Liters Stainless</span>
                        <span>·</span>
                        <span><strong class="text-[#F7E6CB]">Agitation:</strong> Wave Pulsar</span>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- Chamber 2: The 3 Shift Protocols (Scroll 48%-74%) -->
                <!-- ============================================== -->
                <div id="portal-chamber-2" class="drum-chamber-panel absolute inset-x-4 sm:inset-x-8 lg:inset-x-16 max-w-6xl mx-auto flex flex-col items-center justify-center p-4 sm:p-6 text-center opacity-0 translate-y-8 pointer-events-none">
                    <span class="inline-block rounded-xl border-2 border-[#182830] bg-[#25799B] px-3.5 py-1 font-mono text-xs font-bold uppercase tracking-widest text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
                        Chamber 02 · Certified Shift Execution Protocols
                    </span>
                    <h4 class="mt-3 font-recoleta text-2xl sm:text-4xl lg:text-5xl font-black text-[#F7E6CB] leading-tight drop-shadow-md">
                        Three Golden Rules of Clean Clothes.
                    </h4>
                    <p class="mt-2 text-xs sm:text-sm lg:text-base text-[#BAE6FD] max-w-3xl font-medium leading-relaxed">
                        Every single drop-off passes through our strict shift execution standards before it ever leaves our counter.
                    </p>

                    <!-- 3 Wide Timeline Protocol Cards -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-left font-mono w-full max-w-5xl">
                        <div class="relative rounded-2xl border-3 border-[#182830] bg-[#1E6482] p-4 text-[#FFFDF8] shadow-[5px_5px_0px_#182830]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-recoleta text-2xl font-black text-[#F7E6CB]">01</span>
                                <span class="rounded bg-[#14232B] px-2 py-0.5 text-[10px] text-[#A2C5D8] font-bold">INTAKE & SCALE</span>
                            </div>
                            <strong class="block text-sm font-bold text-[#FFFDF8] mb-1">Weigh-in & Thermal Barcoding</strong>
                            <p class="text-xs text-[#BAE6FD] leading-snug">
                                Digital scale gross weight verification, automated thermal barcode print, and instant SMS intake receipt pinged to customer.
                            </p>
                        </div>

                        <div class="relative rounded-2xl border-3 border-[#182830] bg-[#1E6482] p-4 text-[#FFFDF8] shadow-[5px_5px_0px_#182830]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-recoleta text-2xl font-black text-[#F7E6CB]">02</span>
                                <span class="rounded bg-[#14232B] px-2 py-0.5 text-[10px] text-[#A2C5D8] font-bold">WASH & CENTRIFUGE</span>
                            </div>
                            <strong class="block text-sm font-bold text-[#FFFDF8] mb-1">Thermal Sanitize + 1400 RPM Spin</strong>
                            <p class="text-xs text-[#BAE6FD] leading-snug">
                                60°C thermal steam wash eradicates 99.9% of bacteria, followed by quadruple freshwater rinses and harmonic moisture extraction.
                            </p>
                        </div>

                        <div class="relative rounded-2xl border-3 border-[#182830] bg-[#1E6482] p-4 text-[#FFFDF8] shadow-[5px_5px_0px_#182830]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-recoleta text-2xl font-black text-[#F7E6CB]">03</span>
                                <span class="rounded bg-[#14232B] px-2 py-0.5 text-[10px] text-[#A2C5D8] font-bold">FINISH & SEAL</span>
                            </div>
                            <strong class="block text-sm font-bold text-[#FFFDF8] mb-1">Artisan Crisp Fold & Kraft Seal</strong>
                            <p class="text-xs text-[#BAE6FD] leading-snug">
                                Garments folded along crisp military lines, wrapped in breathable eco-kraft paper, and sealed with our signature wax guarantee stamp.
                            </p>
                        </div>
                    </div>

                    <!-- Quality Assurance Checklist Bar -->
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs font-mono text-[#F7E6CB] bg-[#14232B]/80 rounded-xl px-5 py-2.5 border-2 border-[#182830]">
                        <span class="flex items-center gap-1.5"><span class="text-[#10B981]">✓</span> Zero Color Bleed Audit</span>
                        <span class="text-slate-500">·</span>
                        <span class="flex items-center gap-1.5"><span class="text-[#10B981]">✓</span> Pocket Foreign Object Sweep</span>
                        <span class="text-slate-500">·</span>
                        <span class="flex items-center gap-1.5"><span class="text-[#10B981]">✓</span> Button & Zipper Protection Guarantee</span>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- Chamber 3: The Trowa Promise & Return (Scroll 74%-100%) -->
                <!-- ============================================== -->
                <div id="portal-chamber-3" class="drum-chamber-panel absolute inset-x-4 sm:inset-x-8 lg:inset-x-16 max-w-6xl mx-auto flex flex-col items-center justify-center p-4 sm:p-6 text-center opacity-0 translate-y-8 pointer-events-none">
                    <span class="inline-block rounded-xl border-2 border-[#182830] bg-[#10B981] px-3.5 py-1 font-mono text-xs font-bold uppercase tracking-widest text-[#182830] shadow-[2px_2px_0px_#182830]">
                        Chamber 03 · The Trowa Promise & Live Bay Telemetry
                    </span>
                    <h4 class="mt-3 font-recoleta text-2xl sm:text-4xl lg:text-5xl font-black text-[#F7E6CB] leading-tight drop-shadow-md">
                        Built for Shifts, Loved by Neighbors.
                    </h4>

                    <!-- 4 Large Statistic Plaques in Grid -->
                    <div class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 font-mono w-full max-w-5xl">
                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-3 sm:p-4 text-[#182830] shadow-[5px_5px_0px_#182830]">
                            <span class="block text-2xl sm:text-3xl font-black text-[#CB1B03]">12,850+</span>
                            <span class="text-[10px] sm:text-xs text-slate-600 font-bold uppercase tracking-wider">Baskets Washed</span>
                        </div>
                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-3 sm:p-4 text-[#182830] shadow-[5px_5px_0px_#182830]">
                            <span class="block text-2xl sm:text-3xl font-black text-[#25799B]">45-MIN</span>
                            <span class="text-[10px] sm:text-xs text-slate-600 font-bold uppercase tracking-wider">Express Ready</span>
                        </div>
                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-3 sm:p-4 text-[#182830] shadow-[5px_5px_0px_#182830]">
                            <span class="block text-2xl sm:text-3xl font-black text-[#10B981]">0 LOST</span>
                            <span class="text-[10px] sm:text-xs text-slate-600 font-bold uppercase tracking-wider">Socks Guarantee</span>
                        </div>
                        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-3 sm:p-4 text-[#182830] shadow-[5px_5px_0px_#182830]">
                            <span class="block text-2xl sm:text-3xl font-black text-[#8B5CF6]">100%</span>
                            <span class="text-[10px] sm:text-xs text-slate-600 font-bold uppercase tracking-wider">Sachet Precision</span>
                        </div>
                    </div>

                    <!-- Live Machine Bay Fleet Telemetry Grid -->
                    <div class="mt-5 hidden sm:grid grid-cols-5 gap-2.5 font-mono text-[11px] w-full max-w-5xl">
                        <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-left">
                            <span class="text-[#A2C5D8] block text-[9px] uppercase font-bold">Washer #01</span>
                            <span class="text-[#10B981] font-bold">Spinning · 1400 RPM</span>
                            <span class="text-[#F7E6CB] block text-[9px]">18 min remaining</span>
                        </div>
                        <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-left">
                            <span class="text-[#A2C5D8] block text-[9px] uppercase font-bold">Washer #02</span>
                            <span class="text-[#BAE6FD] font-bold">Sanitize · 60°C Steam</span>
                            <span class="text-[#F7E6CB] block text-[9px]">32 min remaining</span>
                        </div>
                        <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-left">
                            <span class="text-[#A2C5D8] block text-[9px] uppercase font-bold">Washer #03</span>
                            <span class="text-[#10B981] font-bold">Ready / Standby</span>
                            <span class="text-[#A2C5D8] block text-[9px]">Next batch ready</span>
                        </div>
                        <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-left">
                            <span class="text-[#A2C5D8] block text-[9px] uppercase font-bold">Dryer #01</span>
                            <span class="text-[#F59E0B] font-bold">Air Fluff · 42°C</span>
                            <span class="text-[#F7E6CB] block text-[9px]">12 min remaining</span>
                        </div>
                        <div class="rounded-xl border-2 border-[#182830] bg-[#14232B] p-2 text-left">
                            <span class="text-[#A2C5D8] block text-[9px] uppercase font-bold">Dryer #02</span>
                            <span class="text-[#F59E0B] font-bold">Tumble · Eco Heat</span>
                            <span class="text-[#F7E6CB] block text-[9px]">24 min remaining</span>
                        </div>
                    </div>

                    <!-- Back to Station Sign-In Button -->
                    <button type="button" id="return-to-station-btn" class="mt-6 rounded-2xl border-3 border-[#182830] bg-[#CB1B03] px-6 py-3 font-mono text-sm font-extrabold text-[#FFFDF8] shadow-[5px_5px_0px_#182830] transition hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[2px_2px_0px_#182830] cursor-pointer">
                        ⚡ Ready for Shift? Return to Station ➔
                    </button>
                </div>

            </div>

            <!-- ============================================== -->
            <!-- Massive 3D Hinged Industrial Machine Door / Hatch -->
            <!-- Opens on scroll down, closes on scroll up!     -->
            <!-- Anchored on left wall, covers full viewport!   -->
            <!-- ============================================== -->
            <div id="portal-drum-door" class="door-3d-hinge absolute inset-0 z-30 flex items-center justify-center w-full h-full bg-[#182830]/90 backdrop-blur-sm cursor-pointer select-none">
                
                <!-- Huge Brushed Steel Porthole Ring -->
                <div class="relative flex h-[340px] w-[340px] sm:h-[480px] sm:w-[480px] lg:h-[600px] lg:w-[600px] items-center justify-center rounded-full border-12 border-[#182830] bg-gradient-to-tr from-[#64748B] via-[#CBD5E1] to-[#FFFFFF] p-4 sm:p-6 shadow-[25px_0px_60px_rgba(0,0,0,0.85)]">
                    
                    <!-- Inner Glass Window -->
                    <div class="relative flex h-full w-full items-center justify-center rounded-full border-6 border-[#182830] bg-[#1D738E]/30 backdrop-blur-xs overflow-hidden shadow-inner">
                        <!-- Glass Convex Glare Sweep -->
                        <div class="absolute -top-24 -left-24 h-80 w-80 rounded-full bg-white/35 blur-sm"></div>
                        
                        <div class="text-center font-mono z-10 px-4">
                            <span class="inline-block rounded-xl border-3 border-[#182830] bg-[#FFFDF8] px-4 py-2 text-xs sm:text-sm font-black text-[#182830] shadow-[3px_3px_0px_#182830]">
                                🔒 SCROLL DOWN TO UNLATCH & EXPLORE
                            </span>
                            <h5 class="mt-3 text-sm sm:text-base font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)]">
                                COMMERCIAL MODEL TL-1974 · FULL-CHAMBER VORTEX
                            </h5>
                            <p class="mt-1 text-[10px] sm:text-xs text-[#BAE6FD] font-semibold drop-shadow-[0_1px_2px_rgba(0,0,0,0.9)]">
                                Click door or scroll down to swing open
                            </p>
                        </div>
                    </div>

                    <!-- Heavy Chrome Latch Handle on Right Rim -->
                    <div id="portal-door-latch" class="absolute -right-8 top-1/2 -translate-y-1/2 flex items-center transition-transform origin-center">
                        <div class="h-24 w-12 rounded-xl border-4 border-[#182830] bg-[#94A3B8] shadow-[4px_4px_0px_#182830] flex items-center justify-center">
                            <div class="h-14 w-3.5 rounded bg-[#182830]"></div>
                        </div>
                    </div>

                    <!-- Heavy Industrial Hinges Anchored to Left Machine Wall -->
                    <div class="absolute -left-10 top-1/4 h-14 w-10 rounded-l-lg border-3 border-[#182830] bg-[#334155] shadow-[2px_2px_0px_#182830]"></div>
                    <div class="absolute -left-10 bottom-1/4 h-14 w-10 rounded-l-lg border-3 border-[#182830] bg-[#334155] shadow-[2px_2px_0px_#182830]"></div>
                </div>

            </div>

        </div>

        <!-- Bottom Full-Bleed Machine Status Bar -->
        <div class="relative z-40 flex w-full flex-wrap items-center justify-between border-t-4 border-[#182830] bg-[#1E6482] px-4 py-3 sm:px-8 font-mono text-[11px] sm:text-xs shadow-[0_-2px_0_#182830]">
            <div class="flex items-center gap-2">
                <span class="inline-block h-3 w-3 rounded-full bg-[#10B981] animate-pulse"></span>
                <span class="font-bold text-[#F7E6CB]">RETRO CYCLING SYSTEM ACTIVE · BIDIRECTIONAL SCROLL SYNCHRONIZED</span>
            </div>
            <div class="text-[10px] sm:text-xs text-[#A2C5D8]">
                <span>▾ Scroll down = Swing hatch open</span> · <span>▴ Scroll up = Revert & seal</span>
            </div>
        </div>
    </div>
</section>
</div>

@push('scripts')
<script>
    // --- Trowa Robot State & Interactions ---
    let isEyesCovered = false;
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let pupilRafId = null;

    // Password Toggle Reaction
    const toggleBtn = document.getElementById('toggle-password');
    const passInput = document.getElementById('login-password');

    toggleBtn?.addEventListener('click', function () {
        const isCurrentlyText = passInput.type === 'text';
        passInput.type = isCurrentlyText ? 'password' : 'text';
        this.textContent = isCurrentlyText ? 'Show' : 'Hide';
        
        // Trigger robot eye coverage reaction
        setRobotEyesCovered(!isCurrentlyText);
    });

    let peekTimer = null;
    let peekCloseTimer = null;
    let isPeeking = false;

    function setRobotEyesCovered(cover) {
        isEyesCovered = cover;
        clearTimeout(peekTimer);
        clearTimeout(peekCloseTimer);
        isPeeking = false;

        const openEyes = document.getElementById('robot-eyes-open');
        const closedEyes = document.getElementById('robot-eyes-closed');
        const leftEyeOpen = document.getElementById('robot-left-eye-open');
        const rightEyeOpen = document.getElementById('robot-right-eye-open');
        const leftEyeClosed = document.getElementById('robot-left-eye-closed');
        const rightEyeClosed = document.getElementById('robot-right-eye-closed');
        const rightPaw = document.getElementById('robot-right-paw');
        const leftPaw = document.getElementById('robot-left-paw');
        const paws = document.getElementById('robot-paws');
        const speech = document.getElementById('robot-speech');
        const speechText = document.getElementById('robot-speech-text');
        const robotSvg = document.getElementById('trowa-robot-svg');

        if (cover) {
            // Password Revealed -> Shy / Eyes Closed Mode
            if (openEyes) openEyes.style.display = 'none';
            if (closedEyes) closedEyes.style.display = 'block';
            if (leftEyeClosed) leftEyeClosed.style.display = 'block';
            if (rightEyeClosed) rightEyeClosed.style.display = 'block';
            if (leftEyeOpen) leftEyeOpen.style.display = 'block';
            if (rightEyeOpen) rightEyeOpen.style.display = 'block';

            if (paws) {
                paws.style.transform = 'translateY(0px)';
                paws.style.opacity = '1';
            }
            if (rightPaw) rightPaw.style.transform = 'translate(0px, 0px) rotate(0deg)';
            if (leftPaw) leftPaw.style.transform = 'translate(0px, 0px) rotate(0deg)';

            if (robotSvg) {
                robotSvg.style.transform = 'rotate(-4deg) scale(0.97)';
            }
            if (speech && speechText) {
                speechText.textContent = "Eeep! No peeking! Secret password! 🙈";
                speech.classList.add('bg-[#FEE2E2]', 'text-[#991B1B]', 'border-[#991B1B]');
                speech.classList.remove('bg-[#FFFDF8]', 'text-[#182830]', 'border-[#182830]');
            }
            if (window.SoundFx) SoundFx.click();

            // Start Sneak Peek cycle!
            schedulePasswordPeek();
        } else {
            // Password Hidden -> Eyes Open & Tracking Mode
            if (openEyes) openEyes.style.display = 'block';
            if (closedEyes) closedEyes.style.display = 'none';
            if (leftEyeOpen) leftEyeOpen.style.display = 'block';
            if (rightEyeOpen) rightEyeOpen.style.display = 'block';
            if (leftEyeClosed) leftEyeClosed.style.display = 'none';
            if (rightEyeClosed) rightEyeClosed.style.display = 'none';

            if (rightPaw) rightPaw.style.transform = 'translate(0px, 0px) rotate(0deg)';
            if (leftPaw) leftPaw.style.transform = 'translate(0px, 0px) rotate(0deg)';
            if (paws) {
                paws.style.transform = 'translateY(55px)';
                paws.style.opacity = '0';
            }
            if (robotSvg) {
                robotSvg.style.transform = 'rotate(0deg) scale(1.04)';
                setTimeout(() => {
                    if (robotSvg) robotSvg.style.transform = 'rotate(0deg) scale(1)';
                }, 200);
            }
            if (speech && speechText) {
                speechText.textContent = "Safe & hidden! Shift locked in! ✨";
                speech.classList.remove('bg-[#FEE2E2]', 'text-[#991B1B]', 'border-[#991B1B]');
                speech.classList.add('bg-[#FFFDF8]', 'text-[#182830]', 'border-[#182830]');
            }
            if (window.SoundFx) SoundFx.click();

            // Re-sync pupils immediately
            requestAnimationFrame(updatePupils);
        }
    }

    // --- Sneak Peek Animation Cycle ---
    function schedulePasswordPeek() {
        clearTimeout(peekTimer);
        clearTimeout(peekCloseTimer);
        if (!isEyesCovered) return;

        peekTimer = setTimeout(() => {
            if (!isEyesCovered || isSpinningFace) return;
            triggerSneakPeek();
        }, 1800);
    }

    function triggerSneakPeek() {
        if (!isEyesCovered || isSpinningFace) return;
        isPeeking = true;

        const openEyes = document.getElementById('robot-eyes-open');
        const rightPaw = document.getElementById('robot-right-paw');
        const rightEyeOpen = document.getElementById('robot-right-eye-open');
        const rightEyeClosed = document.getElementById('robot-right-eye-closed');
        const rightPupil = document.getElementById('robot-right-pupil');
        const robotSvg = document.getElementById('trowa-robot-svg');
        const speechText = document.getElementById('robot-speech-text');

        // 1. Lower right paw slightly to reveal right eye
        if (rightPaw) {
            rightPaw.style.transform = 'translate(6px, 16px) rotate(-22deg)';
        }

        // 2. Hide right closed eye, reveal right open eye looking sideways towards password!
        if (openEyes) openEyes.style.display = 'block';
        if (rightEyeClosed) rightEyeClosed.style.display = 'none';
        if (rightEyeOpen) {
            rightEyeOpen.style.display = 'block';
            if (rightPupil) {
                rightPupil.style.transform = 'translate(5px, 0.5px)';
            }
        }

        // 3. Curious little head tilt towards the input
        if (robotSvg) {
            robotSvg.style.transform = 'rotate(-1deg) scale(0.99)';
        }

        // 4. Mischievous speech bubble
        if (speechText) {
            speechText.textContent = "Psst... just one tiny peek! 👀🤫";
        }

        // 5. Play curious sneaky audio chirp
        playSneakPeekAudio();

        // 6. Hold sneak peek for 1000ms, then GASP & SNAP SHUT!
        peekCloseTimer = setTimeout(() => {
            if (!isEyesCovered) return;

            // Snap right paw back up over eye!
            if (rightPaw) {
                rightPaw.style.transform = 'translate(0px, 0px) rotate(0deg)';
            }

            // Close right eye again
            if (rightEyeOpen) rightEyeOpen.style.display = 'none';
            if (rightEyeClosed) rightEyeClosed.style.display = 'block';

            // Shy gasp tilt
            if (robotSvg) {
                robotSvg.style.transform = 'rotate(-5deg) scale(0.97)';
            }

            if (speechText) {
                speechText.textContent = "Eeep! Didn't see anything! Covered! 🙈";
            }

            isPeeking = false;

            // Schedule next sneak peek in 3.5s!
            schedulePasswordPeek();
        }, 1000);
    }

    function playSneakPeekAudio() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            if (ctx.state === 'suspended') ctx.resume();

            const now = ctx.currentTime;
            [880, 1175].forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                const time = now + (idx * 0.1);

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, time);

                gain.gain.setValueAtTime(0.001, time);
                gain.gain.linearRampToValueAtTime(0.06, time + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, time + 0.09);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(time);
                osc.stop(time + 0.095);
            });
        } catch (e) {}
    }

    // --- Mouse Eye Tracking Logic ---
    window.addEventListener('mousemove', function (e) {
        mouseX = e.clientX;
        mouseY = e.clientY;
        if (!pupilRafId && !isEyesCovered) {
            pupilRafId = requestAnimationFrame(updatePupils);
        }
    });

    function updatePupils() {
        pupilRafId = null;
        if (isEyesCovered && !isPeeking) return;
        if (isEyesCovered && isPeeking) {
            const rightPupil = document.getElementById('robot-right-pupil');
            if (rightPupil) rightPupil.style.transform = 'translate(5px, 0.5px)';
            return;
        }

        const leftPupil = document.getElementById('robot-left-pupil');
        const rightPupil = document.getElementById('robot-right-pupil');
        if (!leftPupil || !rightPupil) return;

        const leftRect = leftPupil.getBoundingClientRect();
        const rightRect = rightPupil.getBoundingClientRect();

        const lx = leftRect.left + leftRect.width / 2;
        const ly = leftRect.top + leftRect.height / 2;
        const ldx = mouseX - lx;
        const ldy = mouseY - ly;
        const lAngle = Math.atan2(ldy, ldx);
        const lDist = Math.hypot(ldx, ldy);
        const lMove = Math.min(5, lDist / 28);

        const rx = rightRect.left + rightRect.width / 2;
        const ry = rightRect.top + rightRect.height / 2;
        const rdx = mouseX - rx;
        const rdy = mouseY - ry;
        const rAngle = Math.atan2(rdy, rdx);
        const rDist = Math.hypot(rdx, rdy);
        const rMove = Math.min(5, rDist / 28);

        const lOffset = `${Math.cos(lAngle) * lMove}px, ${Math.sin(lAngle) * lMove}px`;
        const rOffset = `${Math.cos(rAngle) * rMove}px, ${Math.sin(rAngle) * rMove}px`;

        leftPupil.style.transform = `translate(${lOffset})`;
        rightPupil.style.transform = `translate(${rOffset})`;
    }

    // --- Natural Cartoon Blinking ---
    setInterval(() => {
        if (isEyesCovered) return;
        const openEyes = document.getElementById('robot-eyes-open');
        if (!openEyes) return;
        openEyes.classList.add('robot-blinking');
        setTimeout(() => {
            openEyes.classList.remove('robot-blinking');
        }, 150);
    }, 4200);

    // --- Click Robot for Shaking & Spinning Face with Audio & Bubbles ---
    const robotWrapper = document.getElementById('robot-interactive-wrapper');
    let isSpinningFace = false;

    robotWrapper?.addEventListener('click', function () {
        if (isSpinningFace) return;
        isSpinningFace = true;

        const svg = document.getElementById('trowa-robot-svg');
        const spinningFace = document.getElementById('robot-spinning-face');
        const normalMouth = document.getElementById('robot-mouth');
        const dizzyMouth = document.getElementById('robot-mouth-dizzy');
        const speech = document.getElementById('robot-speech');
        const speechText = document.getElementById('robot-speech-text');

        // 1. Machine Shaking Animation
        if (svg) {
            svg.classList.remove('robot-shaking');
            void svg.offsetWidth; // Force reflow
            svg.classList.add('robot-shaking');
        }

        // 2. Face Spinning Animation (inside porthole)
        if (spinningFace) {
            spinningFace.classList.remove('robot-face-spinning');
            void spinningFace.offsetWidth;
            spinningFace.classList.add('robot-face-spinning');
        }

        // 3. Switch to Dizzy Open "O" Mouth
        if (normalMouth) normalMouth.style.display = 'none';
        if (dizzyMouth) dizzyMouth.style.display = 'block';

        // 4. Update Speech Bubble with Dizzy Spin Status
        if (speech && speechText) {
            speechText.textContent = "WHIRRRR! 🌀 Spin cycle 1200 RPM! Whoa dizzy! 💫";
            speech.classList.add('animate-bounce');
        }

        // 5. Play Dynamic Web Audio Spin Cycle Sound Effect
        playSpinCycleAudio();

        // 6. Spin Finishes (1.25s) -> Release Soap Bubbles Effect!
        setTimeout(() => {
            // Stop shake & spin classes
            if (svg) svg.classList.remove('robot-shaking');
            if (spinningFace) spinningFace.classList.remove('robot-face-spinning');

            // Restore smile mouth
            if (normalMouth) normalMouth.style.display = 'block';
            if (dizzyMouth) dizzyMouth.style.display = 'none';
            if (speech) speech.classList.remove('animate-bounce');

            // Playful bounce on finish
            if (svg) {
                svg.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    if (svg) svg.style.transform = 'scale(1)';
                }, 180);
            }

            // Spawn Soapy Bubbles Erupting from Porthole!
            spawnSoapBubbles();

            if (speechText) {
                speechText.textContent = "🫧 Pop! Squeaky clean & freshly spun! ✨";
            }

            // 7. Auto-Reload to Clean Default Form after bubbles float away (total 2.6s)
            setTimeout(() => {
                // Clear bubbles layer
                const bubbleLayer = document.getElementById('robot-bubbles-layer');
                if (bubbleLayer) bubbleLayer.innerHTML = '';

                // Reset speech bubble to pristine default state
                if (speech) {
                    speech.classList.remove('animate-bounce');
                    speech.classList.add('bg-[#FFFDF8]', 'text-[#182830]', 'border-[#182830]');
                    speech.classList.remove('bg-[#FEE2E2]', 'text-[#991B1B]', 'border-[#991B1B]');
                }
                if (speechText && !isEyesCovered) {
                    speechText.textContent = "Hi! I'm Trowa-Bot! Ready to wash?";
                }

                // Cleanly reset inline styles
                if (svg) svg.style.transform = '';
                if (spinningFace) spinningFace.style.transform = '';

                // Re-sync pupils to current cursor location
                requestAnimationFrame(updatePupils);

                // Reload ready state: Clickable again! (vice versa)
                isSpinningFace = false;
            }, 1400);

        }, 1250);
    });

    // --- Spawn Colorful Soapy Floating Bubbles ---
    function spawnSoapBubbles() {
        const layer = document.getElementById('robot-bubbles-layer');
        if (!layer) return;
        layer.innerHTML = '';

        // Play gentle bubble pop audio sequence
        playBubblePopAudio();

        // Spawn 14 bubbles scattered around the porthole
        const count = 14;
        for (let i = 0; i < count; i++) {
            const b = document.createElement('div');
            b.className = 'soap-bubble';

            // Bubble diameter between 16px and 40px
            const size = Math.floor(Math.random() * 24) + 16;
            b.style.width = `${size}px`;
            b.style.height = `${size}px`;

            // Start near drum porthole center
            b.style.left = '50%';
            b.style.top = '45%';

            // Scatter parameters
            const bx = (Math.random() - 0.5) * 190;
            const by = -(Math.random() * 110 + 40);
            b.style.setProperty('--bx', `${bx}px`);
            b.style.setProperty('--by', `${by}px`);

            // Stagger animation timing
            const dur = (Math.random() * 0.4 + 1.1).toFixed(2);
            const delay = (Math.random() * 0.28).toFixed(2);
            b.style.animation = `bubbleFloatPop ${dur}s cubic-bezier(0.22, 1, 0.36, 1) ${delay}s forwards`;

            layer.appendChild(b);
        }
    }

    // --- Web Audio Pop Sound Effect for Soap Bubbles ---
    function playBubblePopAudio() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            if (ctx.state === 'suspended') ctx.resume();

            const now = ctx.currentTime;
            const bubbleFreqs = [820, 980, 1150, 900, 1320, 1480, 1050, 1260, 1420, 1100];
            bubbleFreqs.forEach((freq, idx) => {
                const delay = 0.07 * idx + (Math.random() * 0.04);
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now + delay);
                osc.frequency.exponentialRampToValueAtTime(freq * 1.5, now + delay + 0.015);
                osc.frequency.exponentialRampToValueAtTime(freq * 0.45, now + delay + 0.05);

                gain.gain.setValueAtTime(0.001, now + delay);
                gain.gain.linearRampToValueAtTime(0.07, now + delay + 0.01);
                gain.gain.exponentialRampToValueAtTime(0.001, now + delay + 0.055);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + delay);
                osc.stop(now + delay + 0.06);
            });
        } catch (e) {}
    }

    // --- Web Audio Synthesizer for Washing Machine Spin & Whirl ---
    function playSpinCycleAudio() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) {
                if (window.SoundFx) SoundFx.click();
                return;
            }
            const ctx = new AudioCtx();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const now = ctx.currentTime;

            // Motor acceleration and deceleration whoosh (filtered sawtooth)
            const motorOsc = ctx.createOscillator();
            const motorGain = ctx.createGain();
            const motorFilter = ctx.createBiquadFilter();

            motorOsc.type = 'sawtooth';
            motorFilter.type = 'lowpass';
            motorFilter.frequency.setValueAtTime(320, now);
            motorFilter.frequency.exponentialRampToValueAtTime(1100, now + 0.6);
            motorFilter.frequency.exponentialRampToValueAtTime(280, now + 1.2);

            motorOsc.frequency.setValueAtTime(120, now);
            motorOsc.frequency.exponentialRampToValueAtTime(420, now + 0.6);
            motorOsc.frequency.exponentialRampToValueAtTime(100, now + 1.2);

            motorGain.gain.setValueAtTime(0.01, now);
            motorGain.gain.linearRampToValueAtTime(0.14, now + 0.15);
            motorGain.gain.linearRampToValueAtTime(0.14, now + 0.75);
            motorGain.gain.exponentialRampToValueAtTime(0.001, now + 1.25);

            motorOsc.connect(motorFilter);
            motorFilter.connect(motorGain);
            motorGain.connect(ctx.destination);

            motorOsc.start(now);
            motorOsc.stop(now + 1.25);

            // Cartoon playful chimes in ascending & descending scale
            const notes = [440, 554, 659, 880, 1108, 1318, 1108, 880, 659, 554, 440];
            const step = 0.09;
            notes.forEach((freq, idx) => {
                const toneOsc = ctx.createOscillator();
                const toneGain = ctx.createGain();

                toneOsc.type = 'sine';
                toneOsc.frequency.setValueAtTime(freq, now + (idx * step));

                toneGain.gain.setValueAtTime(0, now + (idx * step));
                toneGain.gain.linearRampToValueAtTime(0.08, now + (idx * step) + 0.02);
                toneGain.gain.exponentialRampToValueAtTime(0.001, now + (idx * step) + step);

                toneOsc.connect(toneGain);
                toneGain.connect(ctx.destination);

                toneOsc.start(now + (idx * step));
                toneOsc.stop(now + (idx * step) + step);
            });

            // Finishing cheerful bell Ding!
            setTimeout(() => {
                try {
                    const dingOsc = ctx.createOscillator();
                    const dingGain = ctx.createGain();
                    const dingTime = ctx.currentTime;
                    dingOsc.type = 'sine';
                    dingOsc.frequency.setValueAtTime(1046.5, dingTime);

                    dingGain.gain.setValueAtTime(0.12, dingTime);
                    dingGain.gain.exponentialRampToValueAtTime(0.001, dingTime + 0.6);

                    dingOsc.connect(dingGain);
                    dingGain.connect(ctx.destination);

                    dingOsc.start(dingTime);
                    dingOsc.stop(dingTime + 0.6);
                } catch (e) {}
            }, 1150);

        } catch (e) {
            if (window.SoundFx) SoundFx.click();
        }
    }

    // --- Input Focus Context Messages ---
    const usernameInput = document.getElementById('username-input');
    usernameInput?.addEventListener('focus', function () {
        if (!isEyesCovered) {
            const speechText = document.getElementById('robot-speech-text');
            if (speechText) speechText.textContent = "Who's reporting for duty today? 👋";
        }
    });

    passInput?.addEventListener('focus', function () {
        if (!isEyesCovered) {
            const speechText = document.getElementById('robot-speech-text');
            if (speechText) speechText.textContent = "Type safe! Shift code stays confidential 🤫";
        }
    });

    function fillCreds(user, pass) {
        const u = document.getElementById('username-input');
        const p = document.getElementById('login-password');
        if (u) u.value = user;
        if (p) p.value = pass;
        if (window.SoundFx) SoundFx.click();
    }

    // --- Scrollytelling 3D Washing Machine Portal Logic ---
    let portalScrollRaf = null;

    window.addEventListener('scroll', function () {
        if (!portalScrollRaf) {
            portalScrollRaf = requestAnimationFrame(updateDrumPortal);
        }
    }, { passive: true });

    // Initial check on load
    document.addEventListener('DOMContentLoaded', updateDrumPortal);

    function updateDrumPortal() {
        portalScrollRaf = null;
        const portal = document.getElementById('trowa-drum-portal');
        if (!portal) return;

        const rect = portal.getBoundingClientRect();
        const windowH = window.innerHeight;
        const totalScrollable = rect.height - windowH;
        if (totalScrollable <= 0) return;

        const currentScroll = -rect.top;
        // Progress bounded 0.0 to 1.0
        const progress = Math.max(0, Math.min(1, currentScroll / totalScrollable));

        const door = document.getElementById('portal-drum-door');
        const latch = document.getElementById('portal-door-latch');
        const glow = document.getElementById('portal-drum-glow');
        const statusText = document.getElementById('portal-status-text');
        const pilotLight = document.getElementById('portal-pilot-light');

        const c1 = document.getElementById('portal-chamber-1');
        const c2 = document.getElementById('portal-chamber-2');
        const c3 = document.getElementById('portal-chamber-3');

        // 1. Door Hinge Swing: 0% -> 26% of scroll range
        const doorProgress = Math.max(0, Math.min(1, (progress - 0.03) / 0.22));
        const doorAngle = doorProgress * -115;
        const latchAngle = Math.max(0, Math.min(1, progress / 0.08)) * -35;

        if (door) {
            door.style.transform = `perspective(1600px) rotateY(${doorAngle}deg)`;
            door.style.pointerEvents = doorProgress > 0.75 ? 'none' : 'auto';
        }
        if (latch) {
            latch.style.transform = `rotate(${latchAngle}deg)`;
        }
        if (glow) {
            glow.style.opacity = (doorProgress * 0.95).toFixed(2);
        }

        // Status update
        if (statusText && pilotLight) {
            if (doorProgress > 0.1) {
                statusText.textContent = "DRUM OPEN · INSIDE VORTEX";
                pilotLight.classList.remove('bg-[#CB1B03]');
                pilotLight.classList.add('bg-[#10B981]');
            } else {
                statusText.textContent = "DRUM LOCKED";
                pilotLight.classList.remove('bg-[#10B981]');
                pilotLight.classList.add('bg-[#CB1B03]');
            }
        }

        // 2. Chamber 1 (The Alchemy): Active between progress 0.20 and 0.48
        if (c1) {
            if (progress < 0.18) {
                c1.style.opacity = '0';
                c1.style.transform = 'translateY(24px) scale(0.95)';
                c1.style.pointerEvents = 'none';
            } else if (progress >= 0.18 && progress < 0.48) {
                const pIn = Math.min(1, (progress - 0.18) / 0.08);
                c1.style.opacity = pIn.toFixed(2);
                c1.style.transform = `translateY(${(1 - pIn) * 24}px) scale(${0.95 + pIn * 0.05})`;
                c1.style.pointerEvents = 'auto';
            } else {
                const pOut = Math.min(1, (progress - 0.48) / 0.06);
                c1.style.opacity = (1 - pOut).toFixed(2);
                c1.style.transform = `translateY(${-pOut * 24}px) scale(${1 - pOut * 0.05})`;
                c1.style.pointerEvents = 'none';
            }
        }

        // 3. Chamber 2 (Shift Protocols): Active between progress 0.48 and 0.74
        if (c2) {
            if (progress < 0.48) {
                c2.style.opacity = '0';
                c2.style.transform = 'translateY(24px) scale(0.95)';
                c2.style.pointerEvents = 'none';
            } else if (progress >= 0.48 && progress < 0.74) {
                const pIn = Math.min(1, (progress - 0.48) / 0.08);
                c2.style.opacity = pIn.toFixed(2);
                c2.style.transform = `translateY(${(1 - pIn) * 24}px) scale(${0.95 + pIn * 0.05})`;
                c2.style.pointerEvents = 'auto';
            } else {
                const pOut = Math.min(1, (progress - 0.74) / 0.06);
                c2.style.opacity = (1 - pOut).toFixed(2);
                c2.style.transform = `translateY(${-pOut * 24}px) scale(${1 - pOut * 0.05})`;
                c2.style.pointerEvents = 'none';
            }
        }

        // 4. Chamber 3 (The Guarantee): Active between progress 0.74 and 1.0
        if (c3) {
            if (progress < 0.74) {
                c3.style.opacity = '0';
                c3.style.transform = 'translateY(24px) scale(0.95)';
                c3.style.pointerEvents = 'none';
            } else {
                const pIn = Math.min(1, (progress - 0.74) / 0.08);
                c3.style.opacity = pIn.toFixed(2);
                c3.style.transform = `translateY(${(1 - pIn) * 24}px) scale(${0.95 + pIn * 0.05})`;
                c3.style.pointerEvents = 'auto';
            }
        }
    }

    // --- Return to Station Button & Prompt Links ---
    document.getElementById('return-to-station-btn')?.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (window.SoundFx) SoundFx.success();
    });

    document.getElementById('scroll-prompt-btn')?.addEventListener('click', function (e) {
        e.preventDefault();
        const portal = document.getElementById('trowa-drum-portal');
        if (portal) {
            const top = portal.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({ top: top + (window.innerHeight * 0.4), behavior: 'smooth' });
            if (window.SoundFx) SoundFx.click();
        }
    });

    // Clicking the 3D drum door toggles opening
    document.getElementById('portal-drum-door')?.addEventListener('click', function () {
        const portal = document.getElementById('trowa-drum-portal');
        if (portal) {
            const top = portal.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({ top: top + (window.innerHeight * 0.5), behavior: 'smooth' });
            if (window.SoundFx) SoundFx.click();
        }
    });
</script>
@endpush
@endsection

