@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden gap-2">
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
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div class="text-sm text-emerald-100/70">
                {!! __('Menampilkan') !!}
                <span class="font-medium text-white">{{ $paginator->firstItem() }}</span>
                {!! __('sampai') !!}
                <span class="font-medium text-white">{{ $paginator->lastItem() }}</span>
                {!! __('dari') !!}
                <span class="font-medium text-white">{{ $paginator->total() }}</span>
                {!! __('data') !!}
            </div>

            <div>
                <span class="inline-flex items-center gap-2">
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/5 border border-white/10 text-emerald-100/60 opacity-50 cursor-not-allowed" aria-hidden="true">
                            <span aria-hidden="true">&lsaquo;</span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/5 border border-white/10 text-emerald-100/90 hover:bg-white/10 transition" aria-label="{{ __('pagination.previous') }}">
                            <span aria-hidden="true">&lsaquo;</span>
                        </a>
                    @endif

                    <span class="inline-flex items-center gap-1">
                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span class="inline-flex items-center justify-center h-10 px-3 rounded-xl bg-white/5 border border-white/10 text-emerald-100/60">
                                    {{ $element }}
                                </span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page" class="inline-flex items-center justify-center h-10 min-w-10 px-3 rounded-xl bg-emerald-600 text-white font-medium">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="inline-flex items-center justify-center h-10 min-w-10 px-3 rounded-xl bg-white/5 border border-white/10 text-emerald-100/90 hover:bg-white/10 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </span>

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/5 border border-white/10 text-emerald-100/90 hover:bg-white/10 transition" aria-label="{{ __('pagination.next') }}">
                            <span aria-hidden="true">&rsaquo;</span>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/5 border border-white/10 text-emerald-100/60 opacity-50 cursor-not-allowed" aria-hidden="true">
                            <span aria-hidden="true">&rsaquo;</span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
