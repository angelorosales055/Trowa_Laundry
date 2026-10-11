@php
    $total = $paginator->total();
    $currentPage = $paginator->currentPage();
    $lastPage = max(1, $paginator->lastPage());
    $hasPages = $paginator->hasPages();
@endphp

@if ($hasPages || $total > 0)
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3.5 bg-[#FFFDF8] border-t-2 border-[#182830]">
        <!-- Page Count Summary -->
        <div class="flex items-center gap-2">
            <span class="font-mono text-xs text-[#25799B]">
                @if($total > 0)
                    Showing <strong class="text-[#182830]">{{ $paginator->firstItem() }}</strong>–<strong class="text-[#182830]">{{ $paginator->lastItem() }}</strong> of <strong class="text-[#182830]">{{ $total }}</strong> records
                    <span class="text-slate-400 mx-1">·</span>
                    <span>Page <strong class="text-[#182830]">{{ $currentPage }}</strong> of <strong class="text-[#182830]">{{ $lastPage }}</strong></span>
                @else
                    <span>0 records</span>
                @endif
            </span>
        </div>

        @if ($hasPages)
            <!-- Pagination Controls -->
            <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-wrap items-center gap-1.5">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
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

                {{-- Page Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-1.5 font-mono text-xs text-slate-400 select-none">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="rounded-xl border-2 border-[#182830] bg-[#182830] px-3 py-1.5 font-mono text-xs font-bold text-[#FFFDF8] shadow-[2px_2px_0px_#182830] select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="inline-flex items-center gap-1 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition active:translate-x-0.5 active:translate-y-0.5">
                        <span>Next</span>
                        <span>→</span>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Next page" class="inline-flex items-center gap-1 rounded-xl border-2 border-slate-300 bg-slate-100 px-3 py-1.5 font-mono text-xs font-bold text-slate-400 cursor-not-allowed select-none">
                        <span>Next</span>
                        <span>→</span>
                    </span>
                @endif
            </nav>
        @endif
    </div>
@endif
