@php
    $total = $paginator->total();
    $currentPage = $paginator->currentPage();
    $lastPage = max(1, $paginator->lastPage());
    $hasPages = $paginator->hasPages();
    $currentPerPage = (int) request('per_page', 8);
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 bg-[#FFFDF8] border-t-2 border-[#182830]">
    <!-- Page Count Summary & Page Size Selector -->
    <div class="flex flex-wrap items-center gap-3">
        <span class="font-mono text-xs text-[#25799B]">
            @if($total > 0)
                Showing <strong class="text-[#182830]">{{ $paginator->firstItem() }}</strong>–<strong class="text-[#182830]">{{ $paginator->lastItem() }}</strong> of <strong class="text-[#182830]">{{ $total }}</strong> orders
                <span class="text-slate-400 mx-1">·</span>
                <span>Page <strong class="text-[#182830]">{{ $currentPage }}</strong> of <strong class="text-[#182830]">{{ $lastPage }}</strong></span>
            @else
                <span>0 orders found in this view</span>
            @endif
        </span>

        <!-- Per-page size picker -->
        <div class="flex items-center gap-1 text-[11px] font-mono font-bold text-slate-600">
            <span>Per page:</span>
            @foreach([5, 8, 10, 15] as $size)
                <a href="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" 
                   title="Show {{ $size }} orders per page"
                   class="px-2 py-0.5 rounded border {{ $currentPerPage === $size ? 'border-[#182830] bg-[#182830] text-white font-black' : 'border-[#182830]/30 bg-white text-[#182830] hover:bg-[#F7E6CB]' }} transition">
                    {{ $size }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Pagination Navigation Controls (Always Available) -->
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-wrap items-center gap-1.5">
        {{-- Previous Page Link --}}
        @if ($currentPage <= 1)
            <span aria-disabled="true" aria-label="Previous page" class="inline-flex items-center gap-1 rounded-xl border-2 border-slate-300 bg-slate-100 px-3 py-1.5 font-mono text-xs font-bold text-slate-400 cursor-not-allowed select-none">
                <span>←</span>
                <span>Prev</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="inline-flex items-center gap-1 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition active:translate-x-0.5 active:translate-y-0.5">
                <span>←</span>
                <span>Prev</span>
            </a>
        @endif

        {{-- Page Number Elements --}}
        @php
            $start = max(1, $currentPage - 2);
            $end = min($lastPage, $currentPage + 2);
        @endphp

        @if($start > 1)
            <a href="{{ $paginator->url(1) }}" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition">
                1
            </a>
            @if($start > 2)
                <span class="px-1 font-mono text-xs text-slate-400 select-none">…</span>
            @endif
        @endif

        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $currentPage)
                <span aria-current="page" class="rounded-xl border-2 border-[#182830] bg-[#182830] px-3 py-1.5 font-mono text-xs font-bold text-[#FFFDF8] shadow-[2px_2px_0px_#182830] select-none">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $paginator->url($page) }}" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition">
                    {{ $page }}
                </a>
            @endif
        @endfor

        @if($end < $lastPage)
            @if($end < $lastPage - 1)
                <span class="px-1 font-mono text-xs text-slate-400 select-none">…</span>
            @endif
            <a href="{{ $paginator->url($lastPage) }}" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition">
                {{ $lastPage }}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($currentPage >= $lastPage)
            <span aria-disabled="true" aria-label="Next page" class="inline-flex items-center gap-1 rounded-xl border-2 border-slate-300 bg-slate-100 px-3 py-1.5 font-mono text-xs font-bold text-slate-400 cursor-not-allowed select-none">
                <span>Next</span>
                <span>→</span>
            </span>
        @else
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="inline-flex items-center gap-1 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition active:translate-x-0.5 active:translate-y-0.5">
                <span>Next</span>
                <span>→</span>
            </a>
        @endif
    </nav>
</div>
