@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-2">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white/5 border border-white/10 text-emerald-100/60 opacity-50 cursor-not-allowed">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white/5 border border-white/10 text-emerald-100/90 hover:bg-white/10 transition">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white/5 border border-white/10 text-emerald-100/90 hover:bg-white/10 transition">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white/5 border border-white/10 text-emerald-100/60 opacity-50 cursor-not-allowed">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
