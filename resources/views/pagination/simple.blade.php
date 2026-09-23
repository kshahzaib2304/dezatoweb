@if ($paginator->hasPages())
    <nav class="pager" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="pager__btn is-disabled">Previous</span>
        @else
            <a class="pager__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif

        <span class="pager__status">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="pager__btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="pager__btn is-disabled">Next</span>
        @endif
    </nav>
@endif
