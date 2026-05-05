@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="mt-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-400">
                @if ($paginator->firstItem())
                    <span class="font-medium text-slate-200">{{ $paginator->firstItem() }}</span>
                    –
                    <span class="font-medium text-slate-200">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-medium text-slate-200">{{ $paginator->total() }}</span>
                @else
                    {{ $paginator->count() }} results
                @endif
            </p>
            <div class="inline-flex overflow-hidden rounded-xl border border-white/10 bg-white/5 shadow-lg backdrop-blur-md">
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm text-slate-600 cursor-not-allowed">&lsaquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 text-sm text-sky-300 hover:bg-white/10">&lsaquo;</a>
                @endif
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-2 text-sm text-slate-500">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-2 text-sm font-semibold bg-sky-500/25 text-sky-200">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-sm text-slate-300 hover:bg-white/10">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 text-sm text-sky-300 hover:bg-white/10">&rsaquo;</a>
                @else
                    <span class="px-3 py-2 text-sm text-slate-600 cursor-not-allowed">&rsaquo;</span>
                @endif
            </div>
        </div>
    </nav>
@endif
