@props(['paths' => []])

@php
  $previous = count($paths) ? [array_key_last($paths) => end($paths)] : ['Dashboard' => route('dashboard')];
  $previousKey = array_key_first($previous);
@endphp

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

</style>

<div>
    <nav class="hidden pb-2 sm:block" aria-label="Breadcrumb" x-data="breadcrumbScroll">
        <div class="relative">
            <!-- Left Scroll Button -->
            <button
                x-show="canScrollLeft"
                @click="scrollLeft"
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white shadow p-1 rounded-full"
            >
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Scrollable Breadcrumb -->
            <div
                x-ref="scrollContainer"
                @scroll="checkScroll"
                class="overflow-x-auto whitespace-nowrap no-scrollbar px-6"
            >
                <ol class="inline-flex items-center space-x-4">
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Dashboard
                        </a>
                    </li>
                    @foreach ($paths as $name => $to)
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                                <a href="{{ $to }}"
                                   class="ml-4 text-xs font-medium text-gray-500 hover:text-gray-700">
                                    {{ $name }}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <!-- Right Scroll Button -->
            <button
                x-show="canScrollRight"
                @click="scrollRight"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white shadow p-1 rounded-full"
            >
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </nav>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('breadcrumbScroll', () => ({
                canScrollLeft: false,
                canScrollRight: false,

                scrollLeft() {
                    this.$refs.scrollContainer.scrollBy({ left: -150, behavior: 'smooth' });
                },

                scrollRight() {
                    this.$refs.scrollContainer.scrollBy({ left: 150, behavior: 'smooth' });
                },

                checkScroll() {
                    const el = this.$refs.scrollContainer;
                    this.canScrollLeft = el.scrollLeft > 0;
                    this.canScrollRight = el.scrollLeft + el.clientWidth < el.scrollWidth;
                },

                init() {
                    this.$nextTick(() => this.checkScroll());
                    window.addEventListener('resize', () => this.checkScroll());
                }
            }))
        });
    </script>

</div>
