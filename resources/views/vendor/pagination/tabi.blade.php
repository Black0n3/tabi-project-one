@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Paginacija') }}" class="flex items-center justify-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="rounded-md border border-line px-4 py-2 text-[13px] font-semibold text-ink-faint">‹ {{ __('Prethodna') }}</span>
        @else
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" class="rounded-md border border-line-strong bg-white px-4 py-2 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">‹ {{ __('Prethodna') }}</button>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-[13px] text-ink-faint">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="flex h-10 w-10 items-center justify-center rounded-md bg-brand text-[13px] font-bold text-white">{{ $page }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="flex h-10 w-10 items-center justify-center rounded-md border border-line-strong bg-white text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand" aria-label="{{ __('Stranica :page', ['page' => $page]) }}">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" class="rounded-md border border-line-strong bg-white px-4 py-2 text-[13px] font-semibold text-ink transition hover:border-brand hover:text-brand">{{ __('Sljedeća') }} ›</button>
        @else
            <span class="rounded-md border border-line px-4 py-2 text-[13px] font-semibold text-ink-faint">{{ __('Sljedeća') }} ›</span>
        @endif
    </nav>
@endif
