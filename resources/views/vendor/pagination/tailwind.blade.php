@if ($paginator->hasPages())
<nav class="flex items-center justify-between gap-4 flex-wrap" aria-label="Paginación">

    {{-- Info --}}
    <p class="text-xs text-gray-500">
        Mostrando
        <span class="font-medium text-gray-300">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-gray-300">{{ $paginator->lastItem() }}</span>
        de <span class="font-medium text-gray-300">{{ $paginator->total() }}</span> registros
    </p>

    {{-- Botones --}}
    <div class="flex items-center gap-1">

        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-800 border border-gray-700 rounded-lg cursor-not-allowed select-none">
            <i class="bi bi-chevron-left"></i>
        </span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 transition-colors">
            <i class="bi bi-chevron-left"></i>
        </a>
        @endif

        {{-- Páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
            <span class="px-2 py-1.5 text-xs text-gray-600 select-none">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 text-xs font-bold text-white rounded-lg" style="background:#D71920;">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $url }}"
                       class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 transition-colors">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-gray-600 transition-colors">
            <i class="bi bi-chevron-right"></i>
        </a>
        @else
        <span class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-800 border border-gray-700 rounded-lg cursor-not-allowed select-none">
            <i class="bi bi-chevron-right"></i>
        </span>
        @endif

    </div>
</nav>
@endif
