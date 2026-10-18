@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
        <p class="text-base text-gray-700">
            @if ($paginator->firstItem())
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }}
            @else
                {{ $paginator->count() }} of {{ $paginator->total() }}
            @endif
        </p>

        {{-- --- Phones: previous, page x of y, next --- --}}
        <div class="flex items-center gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secondary btn-sm opacity-50" aria-disabled="true">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary btn-sm">Previous</a>
            @endif

            <span class="text-base text-gray-700">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary btn-sm">Next</a>
            @else
                <span class="btn btn-secondary btn-sm opacity-50" aria-disabled="true">Next</span>
            @endif
        </div>

        {{-- --- Tablets and up: previous, page numbers, next --- --}}
        <div class="hidden items-center gap-1 sm:flex">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secondary btn-sm opacity-50" aria-disabled="true">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary btn-sm">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-gray-600" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="btn btn-primary btn-sm min-w-[2.25rem]">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn btn-secondary btn-sm min-w-[2.25rem]" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary btn-sm">Next</a>
            @else
                <span class="btn btn-secondary btn-sm opacity-50" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif
