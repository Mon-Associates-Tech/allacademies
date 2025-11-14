<section>
    <div class="relative"
         x-data="{
         showLeftArrow: false,
         showRightArrow: false,

         checkScroll() {
             const container = this.$refs.tabContainer;
             if (!container) return;

             this.showLeftArrow = container.scrollLeft > 0;
             this.showRightArrow = container.scrollLeft < (container.scrollWidth - container.clientWidth - 10);
         },

         scrollLeft() {
             const container = this.$refs.tabContainer;
             if (!container) return;

             container.scrollBy({
                 left: -200,
                 behavior: 'smooth'
             });
         },

         scrollRight() {
             const container = this.$refs.tabContainer;
             if (!container) return;

             container.scrollBy({
                 left: 200,
                 behavior: 'smooth'
             });
         },

         init() {
             this.$nextTick(() => {
                 this.checkScroll();

                 // Listen to scroll events
                 this.$refs.tabContainer?.addEventListener('scroll', () => {
                     this.checkScroll();
                 });

                 // Listen to window resize
                 window.addEventListener('resize', () => {
                     this.checkScroll();
                 });
             });
         }
     }"
         x-init="init()">

        <!-- Left Chevron -->
        <button
            x-show="showLeftArrow"
            @click="scrollLeft()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute left-0 top-0 bottom-0 z-10 flex items-center justify-center w-10 bg-gradient-to-r from-white dark:from-gray-900 to-transparent hover:from-gray-50 dark:hover:from-gray-800 transition-all"
            aria-label="Scroll left">
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Tabs Container -->
        <nav
            x-ref="tabContainer"
            class="flex {{ $tabContainerClass }} overflow-x-auto scrollbar-hide scroll-smooth"
            style="scrollbar-width: none; -ms-overflow-style: none;"
            @scroll="checkScroll()">

            @foreach($tabs as $tab)
                <button
                    wire:click="setActiveTab('{{ $tab['key'] }}')"
                    class="{{ $tabClass }} {{ $activeTab === $tab['key'] ? $activeTabClass : $inactiveTabClass }}"
                    type="button">
                    @if(isset($tab['icon']))
                        <span class="inline-flex items-center gap-2">
                        {!! $tab['icon'] !!}
                        <span>{{ $tab['label'] }}</span>
                    </span>
                    @else
                        {{ $tab['label'] }}
                    @endif
                </button>
            @endforeach
        </nav>

        <!-- Right Chevron -->
        <button
            x-show="showRightArrow"
            @click="scrollRight()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute right-0 top-0 bottom-0 z-10 flex items-center justify-center w-10 bg-gradient-to-l from-white dark:from-gray-900 to-transparent hover:from-gray-50 dark:hover:from-gray-800 transition-all"
            aria-label="Scroll right">
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</section>
