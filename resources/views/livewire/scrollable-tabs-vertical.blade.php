<section>
    <div class="relative h-full"
         x-data="{
         showTopArrow: false,
         showBottomArrow: false,

         checkScroll() {
             const container = this.$refs.tabContainer;
             if (!container) return;

             this.showTopArrow = container.scrollTop > 0;
             this.showBottomArrow = container.scrollTop < (container.scrollHeight - container.clientHeight - 10);
         },

         scrollUp() {
             const container = this.$refs.tabContainer;
             if (!container) return;

             container.scrollBy({
                 top: -200,
                 behavior: 'smooth'
             });
         },

         scrollDown() {
             const container = this.$refs.tabContainer;
             if (!container) return;

             container.scrollBy({
                 top: 200,
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

        <!-- Top Chevron -->
        <button
            x-show="showTopArrow"
            @click="scrollUp()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute top-0 left-0 right-0 z-10 flex items-center justify-center h-12 bg-gradient-to-b from-white via-white dark:from-gray-900 dark:via-gray-900 to-transparent shadow-lg hover:shadow-xl transition-all group"
            style="pointer-events: all;"
            aria-label="Scroll up">
            <div class="bg-white dark:bg-gray-800 rounded-full p-2 shadow-md border border-gray-200 dark:border-gray-700 group-hover:bg-gray-50 dark:group-hover:bg-gray-700 group-hover:shadow-lg transition-all">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                </svg>
            </div>
        </button>

        <!-- Tabs Container -->
        <nav
            x-ref="tabContainer"
            class="flex flex-col {{ $tabContainerClass }} overflow-y-auto scrollbar-hide scroll-smooth py-12 h-full"
            style="scrollbar-width: none; -ms-overflow-style: none;"
            @scroll="checkScroll()">

            @foreach($tabs as $tab)
                <button
                    wire:click="setActiveTab('{{ $tab['key'] }}')"
                    class="{{ $tabClass }} {{ $activeTab === $tab['key'] ? $activeTabClass : $inactiveTabClass }}"
                    type="button">
                    @if(isset($tab['icon']))
                        <span class="inline-flex items-center">
                        {!! $tab['icon'] !!}
                        <span class="ml-2">{{ $tab['label'] }}</span>
                    </span>
                    @else
                        {{ $tab['label'] }}
                    @endif
                </button>
            @endforeach
        </nav>

        <!-- Bottom Chevron -->
        <button
            x-show="showBottomArrow"
            @click="scrollDown()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute bottom-0 left-0 right-0 z-10 flex items-center justify-center h-12 bg-gradient-to-t from-white via-white dark:from-gray-900 dark:via-gray-900 to-transparent shadow-lg hover:shadow-xl transition-all group"
            style="pointer-events: all;"
            aria-label="Scroll down">
            <div class="bg-white dark:bg-gray-800 rounded-full p-2 shadow-md border border-gray-200 dark:border-gray-700 group-hover:bg-gray-50 dark:group-hover:bg-gray-700 group-hover:shadow-lg transition-all">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>

</section>
