@props([
    'paginator',
    'align' => 'end',
    'ariaLabel' => 'Page navigation',
    'onEachSide' => 1,
    'showWhenSinglePage' => true,
])

@php
    $alignmentClass = match ($align) {
        'start', 'left' => '',
        'center' => 'justify-content-center',
        'end', 'right' => 'justify-content-end',
        default => 'justify-content-end',
    };

    $currentPage = $paginator->currentPage();
    $lastPage = method_exists($paginator, 'lastPage') ? $paginator->lastPage() : null;
    $hasPages = $paginator->hasPages();
    $pages = [];

    if ($lastPage) {
        $start = max(1, $currentPage - $onEachSide);
        $end = min($lastPage, $currentPage + $onEachSide);

        if ($start > 1) {
            $pages[] = 1;

            if ($start > 2) {
                $pages[] = '...';
            }
        }

        for ($page = $start; $page <= $end; $page++) {
            $pages[] = $page;
        }

        if ($end < $lastPage) {
            if ($end < $lastPage - 1) {
                $pages[] = '...';
            }

            $pages[] = $lastPage;
        }
    }
@endphp

@if ($hasPages || $showWhenSinglePage)
    <nav aria-label="{{ $ariaLabel }}">
        <ul {{ $attributes->class(['pagination', 'mb-0', $alignmentClass]) }}>
            {{-- First Page Link --}}
            <li class="page-item first {{ $currentPage <= 1 ? 'disabled' : '' }}">
                @if ($currentPage <= 1)
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
            @if ($lastPage)
                @foreach ($pages as $page)
                    @if ($page === '...')
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">...</span>
                        </li>
                    @elseif ($page === $currentPage)
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif

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
            @if ($lastPage)
                <li class="page-item last {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                    @if ($currentPage >= $lastPage)
                        <span class="page-link" aria-hidden="true"><i class="tf-icon bx bx-chevrons-right"></i></span>
                    @else
                        <a class="page-link" href="{{ $paginator->url($lastPage) }}" aria-label="Halaman terakhir">
                            <i class="tf-icon bx bx-chevrons-right"></i>
                        </a>
                    @endif
                </li>
            @endif
        </ul>
    </nav>
@endif
