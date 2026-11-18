@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-muted bg-surface-2 border border-border cursor-not-allowed leading-5 rounded-lg opacity-60">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-content bg-surface border border-border leading-5 rounded-lg hover:bg-surface-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-content bg-surface border border-border leading-5 rounded-lg hover:bg-surface-2 focus:outline-none focus:ring-2 focus:ring-primary/20 transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-muted bg-surface-2 border border-border cursor-not-allowed leading-5 rounded-lg opacity-60">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif
