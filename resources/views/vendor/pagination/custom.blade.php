@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-end mb-0">
            {{-- First Page Link --}}
            <li class="page-item first {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevrons-left"></i></span>
                @else
                    <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Halaman pertama">
                        <i class="tf-icon bx bx-chevrons-left"></i>
                    </a>
                @endif
            </li>

            {{-- Previous Page Link --}}
            <li class="page-item prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevron-left"></i></span>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">
                        <i class="tf-icon bx bx-chevron-left"></i>
                    </a>
                @endif
            </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            <li class="page-item next {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if ($paginator->hasMorePages())
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">
                        <i class="tf-icon bx bx-chevron-right"></i>
                    </a>
                @else
                    <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevron-right"></i></span>
                @endif
            </li>

            {{-- Last Page Link --}}
            <li class="page-item last {{ $paginator->currentPage() >= $paginator->lastPage() ? 'disabled' : '' }}">
                @if ($paginator->currentPage() >= $paginator->lastPage())
                    <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevrons-right"></i></span>
                @else
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Halaman terakhir">
                        <i class="tf-icon bx bx-chevrons-right"></i>
                    </a>
                @endif
            </li>
        </ul>
    </nav>
@endif
