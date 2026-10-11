@extends('layouts_app')

@section('title', 'Customer Feedbacks & Ratings · Trowa Laundry')

@section('content')
<div class="space-y-6">

    <!-- Header Section with Breadcrumbs & Print/Export Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-mono font-bold text-[#25799B] mb-1">
                <span>OPERATIONS &amp; QUALITY</span>
                <span>/</span>
                <span>VERIFIED REVIEWS</span>
            </div>
            <h1 class="font-recoleta text-3xl font-black text-[#182830]">
                Customer Feedbacks &amp; Ratings
            </h1>
            <p class="font-mono text-xs text-[#25799B] mt-0.5">
                Real customer satisfaction logs, quality ratings, and service experience reviews.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="window.print()" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-slate-100 transition cursor-pointer flex items-center gap-1.5">
                <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Print / Save as PDF</span>
            </button>
            <a href="{{ route('reports', ['tab' => 'quality']) }}" class="rounded-xl border-2 border-[#182830] bg-[#F7E6CB] px-4 py-2 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-amber-200 transition cursor-pointer flex items-center gap-1.5">
                <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Executive Reports &amp; Charts ➔</span>
            </a>
        </div>
    </div>

    <!-- Rating Scorecard Stats Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <!-- Average Satisfaction -->
        <div class="col-span-2 sm:col-span-1 rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 shadow-[4px_4px_0px_#182830] flex flex-col justify-between">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Store Satisfaction Score</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-recoleta text-4xl font-black text-[#CB1B03]">{{ number_format($averageRating, 1) }}</span>
                <span class="font-mono text-xs font-bold text-slate-500">/ 5.0</span>
            </div>
            <div class="flex items-center gap-1 mt-2 text-amber-500">
                @for($s = 1; $s <= 5; $s++)
                    <svg class="h-4 w-4 {{ $s <= round($averageRating) ? 'fill-amber-400 text-amber-500' : 'text-slate-300' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                @endfor
            </div>
        </div>

        <!-- Total Reviews -->
        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 shadow-[4px_4px_0px_#182830] flex flex-col justify-between">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Total Rated Tickets</span>
            <div class="mt-2">
                <span class="font-recoleta text-3xl font-black text-[#182830]">{{ $totalRatingsCount }}</span>
                <span class="block font-mono text-[11px] text-slate-500 mt-0.5">Verified patron transactions</span>
            </div>
        </div>

        <!-- 5-Star Reviews -->
        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 shadow-[4px_4px_0px_#182830] flex flex-col justify-between">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">5-Star Top Reviews</span>
            <div class="mt-2">
                <span class="font-recoleta text-3xl font-black text-emerald-700">{{ $fiveStarCount }}</span>
                <span class="block font-mono text-[11px] text-slate-500 mt-0.5">
                    {{ $totalRatingsCount > 0 ? round(($fiveStarCount / $totalRatingsCount) * 100) : 0 }}% of total volume
                </span>
            </div>
        </div>

        <!-- Low Rating Alerts (1-2 Stars) -->
        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 shadow-[4px_4px_0px_#182830] flex flex-col justify-between">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Needs Follow-Up (1-2 Stars)</span>
            <div class="mt-2">
                <span class="font-recoleta text-3xl font-black text-[#CB1B03]">{{ $lowRatingCount }}</span>
                <span class="block font-mono text-[11px] text-slate-500 mt-0.5">
                    {{ $lowRatingCount > 0 ? 'Requires attention' : 'Zero complaints logged' }}
                </span>
            </div>
        </div>

        <!-- Written Comments -->
        <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 shadow-[4px_4px_0px_#182830] flex flex-col justify-between">
            <span class="font-mono text-[10px] font-bold uppercase text-[#25799B]">Patron Written Notes</span>
            <div class="mt-2">
                <span class="font-recoleta text-3xl font-black text-[#25799B]">{{ $withCommentsCount }}</span>
                <span class="block font-mono text-[11px] text-slate-500 mt-0.5">Custom comments left</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-4 shadow-[4px_4px_0px_#182830]">
        <form method="GET" action="{{ route('customers.feedbacks') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Star Rating Tabs/Filters -->
            <div class="flex flex-wrap items-center gap-1.5 font-mono text-xs">
                <a href="{{ route('customers.feedbacks', array_filter(['search' => $search])) }}" 
                   class="rounded-xl border-2 px-3 py-1.5 font-bold transition {{ !$currentFilter ? 'border-[#182830] bg-[#182830] text-white shadow-[2px_2px_0px_#CB1B03]' : 'border-[#182830]/20 bg-white text-[#182830] hover:bg-slate-100' }}">
                    All Ratings ({{ $totalRatingsCount }})
                </a>
                <a href="{{ route('customers.feedbacks', array_filter(['rating' => '5', 'search' => $search])) }}" 
                   class="rounded-xl border-2 px-3 py-1.5 font-bold transition {{ $currentFilter === '5' ? 'border-[#182830] bg-amber-400 text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-[#182830]/20 bg-white text-[#182830] hover:bg-slate-100' }}">
                    5 Stars ({{ $fiveStarCount }})
                </a>
                <a href="{{ route('customers.feedbacks', array_filter(['rating' => '4', 'search' => $search])) }}" 
                   class="rounded-xl border-2 px-3 py-1.5 font-bold transition {{ $currentFilter === '4' ? 'border-[#182830] bg-amber-300 text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-[#182830]/20 bg-white text-[#182830] hover:bg-slate-100' }}">
                    4 Stars ({{ $fourStarCount }})
                </a>
                <a href="{{ route('customers.feedbacks', array_filter(['rating' => '3', 'search' => $search])) }}" 
                   class="rounded-xl border-2 px-3 py-1.5 font-bold transition {{ $currentFilter === '3' ? 'border-[#182830] bg-amber-200 text-[#182830] shadow-[2px_2px_0px_#182830]' : 'border-[#182830]/20 bg-white text-[#182830] hover:bg-slate-100' }}">
                    3 Stars ({{ $threeStarCount }})
                </a>
                <a href="{{ route('customers.feedbacks', array_filter(['rating' => 'low', 'search' => $search])) }}" 
                   class="rounded-xl border-2 px-3 py-1.5 font-bold transition {{ $currentFilter === 'low' ? 'border-[#182830] bg-[#CB1B03] text-white shadow-[2px_2px_0px_#182830]' : 'border-[#182830]/20 bg-white text-[#CB1B03] hover:bg-red-50' }}">
                    1-2 Stars ({{ $lowRatingCount }})
                </a>
            </div>

            <!-- Search input -->
            <div class="flex items-center gap-2">
                @if($currentFilter)
                    <input type="hidden" name="rating" value="{{ $currentFilter }}">
                @endif
                <div class="relative flex-1 sm:w-64">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, note, or ticket..." 
                           class="w-full rounded-xl border-2 border-[#182830] bg-white px-3 py-1.5 pl-8 font-mono text-xs text-[#182830] placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-[#25799B]">
                    <svg class="absolute left-2.5 top-2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <button type="submit" class="rounded-xl border-2 border-[#182830] bg-[#25799B] px-3 py-1.5 font-mono text-xs font-bold text-white shadow-[2px_2px_0px_#182830] hover:bg-[#1E6482] cursor-pointer">
                    Filter
                </button>
                @if($search || $currentFilter)
                    <a href="{{ route('customers.feedbacks') }}" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-slate-600 shadow-[1px_1px_0px_#182830] hover:bg-slate-100">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Feedbacks Grid / List -->
    @if($feedbacks->isEmpty())
        <div class="rounded-3xl border-3 border-dashed border-[#182830]/30 bg-[#FFFDF8] p-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-[#182830] bg-[#F7E6CB] text-[#25799B] shadow-[2px_2px_0px_#182830] mb-3">
                <svg class="h-7 w-7 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h3 class="font-recoleta text-xl font-bold text-[#182830]">No Feedback Records Found</h3>
            <p class="font-mono text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                {{ $search || $currentFilter ? 'No reviews match your selected filter or search keywords. Try clearing filters.' : 'Completed orders will show customer ratings and reviews once patrons submit them.' }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($feedbacks as $fb)
                @php
                    $isLow = $fb->rating <= 2;
                    $isFive = $fb->rating === 5;
                @endphp
                <div class="flex flex-col justify-between rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] p-5 shadow-[4px_4px_0px_#182830] transition hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_#182830]">
                    <div>
                        <!-- Header with Stars & Status Badge -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex items-center gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg class="h-4 w-4 {{ $s <= $fb->rating ? 'fill-amber-400 text-amber-500' : 'text-slate-200' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @endfor
                                <span class="ml-1 font-mono text-xs font-black {{ $isLow ? 'text-[#CB1B03]' : ($isFive ? 'text-amber-700' : 'text-slate-700') }}">
                                    {{ $fb->rating }}/5
                                </span>
                            </div>

                            @if($isLow)
                                <span class="rounded-md border border-[#182830] bg-[#CB1B03] px-2 py-0.5 font-mono text-[9px] font-bold uppercase text-white shadow-[1px_1px_0px_#182830]">
                                    Attention Needed
                                </span>
                            @elseif($isFive)
                                <span class="rounded-md border border-[#182830] bg-[#F7E6CB] px-2 py-0.5 font-mono text-[9px] font-bold uppercase text-[#182830] shadow-[1px_1px_0px_#182830]">
                                    Top Rated
                                </span>
                            @endif
                        </div>

                        <!-- Customer Note / Comment -->
                        <div class="rounded-xl border-2 border-[#182830]/15 bg-slate-50 p-3 mb-4 min-h-[64px]">
                            @if(!empty(trim($fb->rating_comment ?? '')))
                                <p class="text-xs font-medium text-[#182830] leading-relaxed italic">
                                    &ldquo;{{ $fb->rating_comment }}&rdquo;
                                </p>
                            @else
                                <p class="text-xs text-slate-400 italic">
                                    No written note provided (rating only)
                                </p>
                            @endif
                        </div>

                        <!-- Order & Patron Details -->
                        <div class="space-y-1.5 text-xs font-mono text-slate-600 border-t border-[#182830]/15 pt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-bold">Patron:</span>
                                <strong class="text-[#182830]">{{ $fb->customer?->name ?? ($fb->customer_name ?? 'Guest Patron') }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-bold">Ticket:</span>
                                <a href="{{ route('orders.show', $fb) }}" class="text-[#25799B] font-bold hover:underline flex items-center gap-1">
                                    <span>#{{ $fb->order_number }}</span>
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                </a>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-bold">Services:</span>
                                <span class="text-slate-800 truncate max-w-[160px]">{{ $fb->services }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with timestamp -->
                    <div class="mt-4 pt-2.5 border-t border-[#182830]/10 flex items-center justify-between text-[11px] font-mono text-slate-500">
                        <span>Rated: {{ $fb->rated_at ? $fb->rated_at->format('M d, Y · h:i A') : $fb->updated_at->format('M d, Y · h:i A') }}</span>
                        <a href="{{ route('orders.show', $fb) }}" class="text-[#CB1B03] font-bold hover:underline">
                            View Order ➔
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $feedbacks->links() }}
        </div>
    @endif

</div>
@endsection
