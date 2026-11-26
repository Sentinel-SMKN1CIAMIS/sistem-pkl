@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
        {{-- Result Range Counter --}}
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
            Menampilkan
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $paginator->firstItem() ?? 0 }}</span>
            &ndash;
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
            data
        </p>

        {{-- Pagination Controls --}}
        <div class="flex items-center justify-between sm:justify-end gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Halaman sebelumnya"
                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 text-xs font-semibold cursor-not-allowed select-none">
                    &lsaquo; <span>Prev</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                    &lsaquo; <span>Prev</span>
                </a>
            @endif

            {{-- Compact Mobile Indicator --}}
            <span class="sm:hidden px-2.5 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200">
                Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            {{-- Desktop Page Numbers --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-2 py-1.5 text-xs font-semibold text-slate-400 dark:text-slate-500 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="inline-flex items-center justify-center min-w-[32px] h-8 px-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold border border-blue-600 select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="inline-flex items-center justify-center min-w-[32px] h-8 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                    <span>Next</span> &rsaquo;
                </a>
            @else
                <span aria-disabled="true" aria-label="Halaman berikutnya"
                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 text-xs font-semibold cursor-not-allowed select-none">
                    <span>Next</span> &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@elseif ($paginator->total() > 0)
    <div class="w-full flex items-center justify-between pt-2">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
            Menampilkan semua
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
            data
        </p>
    </div>
@endif
