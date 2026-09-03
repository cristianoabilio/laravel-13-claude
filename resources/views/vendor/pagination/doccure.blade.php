@if ($paginator->hasPages())
    <div class="pagination dashboard-pagination">
        <ul>
            @if ($paginator->onFirstPage())
                <li><span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="page-link active">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a></li>
            @else
                <li><span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </div>
@endif
