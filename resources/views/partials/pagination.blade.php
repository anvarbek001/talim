@if ($paginator->hasPages())
    <nav class="pager" aria-label="Sahifalash">
        <a href="{{ $paginator->previousPageUrl() }}" class="pager-btn {{ $paginator->onFirstPage() ? 'is-disabled' : '' }}"
            @if ($paginator->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
            <i class="bi bi-chevron-left"></i>
        </a>

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pager-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pager-btn is-active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pager-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        <a href="{{ $paginator->nextPageUrl() }}" class="pager-btn {{ ! $paginator->hasMorePages() ? 'is-disabled' : '' }}"
            @if (! $paginator->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
            <i class="bi bi-chevron-right"></i>
        </a>
    </nav>

    <style>
        .pager {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .pager-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 8px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .84rem;
            font-weight: 600;
            color: var(--text, #17171D);
            border: 1px solid var(--line);
            background: var(--card);
            transition: .2s;
        }

        .pager-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pager-btn.is-active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .pager-btn.is-disabled {
            opacity: .4;
            pointer-events: none;
        }

        .pager-dots {
            padding: 0 4px;
            color: var(--muted);
            font-size: .84rem;
        }
    </style>
@endif
