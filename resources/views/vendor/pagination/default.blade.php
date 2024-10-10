@if ($paginator->hasPages())
<nav>
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        {{--
				<li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
					<span aria-hidden="true"><span class="material-symbols-outlined">arrow_back</span></span>
				</li>
				--}}
        @else
        <li>
            <a href="{{ $paginator->previousPageUrl() }}" data-page="{{ ($paginator->currentPage() - 1) }}"
                rel="prev" aria-label="@lang('pagination.previous')"><span class="material-symbols-outlined">arrow_back</span></a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="active" aria-current="page"><span>{{ $page }}</span></li>
        @else
        <li><a href="{{ $url }}" data-page="{{ $page }}">{{ $page }}</a></li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li>
            <a href="{{ $paginator->nextPageUrl() }}" data-page="{{ ($paginator->currentPage() + 1) }}"
                rel="next"
                aria-label="@lang('pagination.next')"><span class="material-symbols-outlined">
                    arrow_forward
                </span></a>
        </li>
        @else
        {{--
				<li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
					<span aria-hidden="true"><span class="material-symbols-outlined">arrow_forward</span></span>
				</li>
				--}}
        @endif
    </ul>
    @endif