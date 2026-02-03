@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed leading-5 rounded-md">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    Sebelumnya
                </a>
            @endif

            <div class="text-sm text-gray-700">
                <span class="font-medium">{{ $paginator->currentPage() }}</span>
                <span>dari</span>
                <span class="font-medium">{{ $paginator->lastPage() }}</span>
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    Selanjutnya
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed leading-5 rounded-md">
                    Selanjutnya
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    Menampilkan
                    <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm rtl:flex-row-reverse gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed rounded-l-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $onEachSide = 1; // Show 1 page on each side of current page
                        
                        $start = max($currentPage - $onEachSide, 2);
                        $end = min($currentPage + $onEachSide, $lastPage - 1);
                    @endphp

                    {{-- First Page --}}
                    @if ($currentPage === 1)
                        <span aria-current="page">
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-myunila border border-myunila cursor-default leading-5">1</span>
                        </span>
                    @else
                        <a href="{{ $paginator->url(1) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page 1">
                            1
                        </a>
                    @endif

                    {{-- First Ellipsis --}}
                    @if ($start > 2)
                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">...</span>
                    @endif

                    {{-- Middle Pages --}}
                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span aria-current="page">
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-myunila border border-myunila cursor-default leading-5">{{ $page }}</span>
                            </span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    {{-- Last Ellipsis --}}
                    @if ($end < $lastPage - 1)
                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">...</span>
                    @endif

                    {{-- Last Page --}}
                    @if ($lastPage > 1)
                        @if ($currentPage === $lastPage)
                            <span aria-current="page">
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-myunila border border-myunila cursor-default leading-5">{{ $lastPage }}</span>
                            </span>
                        @else
                            <a href="{{ $paginator->url($lastPage) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $lastPage }}">
                                {{ $lastPage }}
                            </a>
                        @endif
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-myunila focus:border-myunila-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Next">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next">
                            <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-not-allowed rounded-r-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
