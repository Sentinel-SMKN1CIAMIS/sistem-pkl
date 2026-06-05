@if ($paginator->hasPages())
    <style>
        .custom-pagination {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            gap: 1rem !important;
            font-size: 0.875rem !important;
            padding: 0.5rem 0 !important;
        }
        .custom-pagination-pages {
            display: flex !important;
            gap: 0.35rem !important;
            align-items: center !important;
        }
        .custom-pagination-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.75rem !important;
            border: 1px solid rgba(226, 232, 240, 0.5) !important;
            background-color: white !important;
            color: #475569 !important;
            font-weight: 600 !important;
            transition: all 0.2s !important;
            text-decoration: none !important;
        }
        .dark .custom-pagination-btn {
            background-color: rgb(30, 41, 59) !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
            color: #94a3b8 !important;
        }
        .custom-pagination-btn:hover:not(.disabled) {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            border-color: #cbd5e1 !important;
        }
        .dark .custom-pagination-btn:hover:not(.disabled) {
            background-color: rgb(51, 65, 85) !important;
            color: white !important;
            border-color: rgb(71, 85, 105) !important;
        }
        .custom-pagination-btn.active {
            background-color: #2563eb !important;
            color: white !important;
            border-color: #2563eb !important;
        }
        .custom-pagination-btn.disabled {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
        .custom-pagination-text {
            color: #64748b !important;
        }
        .dark .custom-pagination-text {
            color: #94a3b8 !important;
        }
        
        @media (max-width: 640px) {
            .custom-pagination-pages-desktop {
                display: none !important;
            }
        }
    </style>

    <div class="custom-pagination">
        <!-- Previous Page Link -->
        @if ($paginator->onFirstPage())
            <span class="custom-pagination-btn disabled">« Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="custom-pagination-btn">« Prev</a>
        @endif

        <!-- Page Numbers -->
        <div class="custom-pagination-pages custom-pagination-pages-desktop">
            @foreach ($elements as $element)
                <!-- "Three Dots" Separator -->
                @if (is_string($element))
                    <span class="custom-pagination-text px-2">{{ $element }}</span>
                @endif

                <!-- Array Of Links -->
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="custom-pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="custom-pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <!-- Next Page Link -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="custom-pagination-btn">Next »</a>
        @else
            <span class="custom-pagination-btn disabled">Next »</span>
        @endif
    </div>
@endif
