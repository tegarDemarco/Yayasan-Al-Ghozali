@if ($paginator->hasPages())
<nav class="pagination-links" role="navigation" aria-label="Pagination Navigation">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="page-link disabled" aria-disabled="true">← Sebelumnya</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">← Sebelumnya</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <span class="page-link disabled" aria-disabled="true">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-link active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">Selanjutnya →</a>
    @else
        <span class="page-link disabled" aria-disabled="true">Selanjutnya →</span>
    @endif
</nav>
@endif

