@props(['active' => ''])

<nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div x-data="{
            showLeft: false,
            showRight: true,
            activeItem: '{{ $active }}',
            checkScroll() {
                this.showLeft = this.$refs.navContainer.scrollLeft > 2;
                this.showRight = this.$refs.navContainer.scrollLeft < (this.$refs.navContainer.scrollWidth - this.$refs.navContainer.clientWidth - 2);
            },
            scrollToActive() {
                if (!this.$refs.activeNavItem) return;
                const item = this.$refs.activeNavItem;
                const container = this.$refs.navContainer;
                const itemLeft = item.offsetLeft;
                const itemRight = itemLeft + item.offsetWidth;
                const containerLeft = container.scrollLeft;
                const containerRight = containerLeft + container.clientWidth;
                if (itemLeft < containerLeft + 16) {
                    container.scrollTo({ left: itemLeft - 16, behavior: 'smooth' });
                } else if (itemRight > containerRight - 16) {
                    container.scrollTo({ left: itemRight - container.clientWidth + 16, behavior: 'smooth' });
                }
            }
        }"
             x-init="checkScroll(); window.addEventListener('resize', () => checkScroll()); $nextTick(() => { setTimeout(() => scrollToActive(), 100) })"
             class="flex items-center h-16 relative">

            {{-- Left chevron to scroll nav items left --}}
            <button type="button"
                    x-show="showLeft"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-on:click="$refs.navContainer.scrollBy({ left: -200, behavior: 'smooth' })"
                    class="absolute left-0 z-10 flex items-center justify-center w-8 h-8 bg-white dark:bg-gray-800 rounded-full shadow-md border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            {{-- Scrollable nav items container --}}
            <div x-ref="navContainer"
                 x-on:scroll.debounce.200ms="checkScroll()"
                 class="flex items-center gap-1 overflow-x-auto flex-1 px-8"
                 style="scrollbar-width: none; -ms-overflow-style: none;">

                <a href="{{ route('examination-hub.dashboard') }}"
                   x-ref="{{ $active === 'dashboard' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'dashboard' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </div>
                </a>

                <a href="{{ route('examination-hub.create') }}"
                   x-ref="{{ $active === 'create' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'create' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add
                    </div>
                </a>

                <a href="{{ route('examination-hub.manage') }}"
                   x-ref="{{ $active === 'manage' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'manage' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        List
                    </div>
                </a>

                <a href="{{ route('examination-hub.live-monitoring.all-exams') }}"
                   x-ref="{{ $active === 'live-monitoring' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'live-monitoring' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Live Monitoring
                    </div>
                </a>

                <a href="{{ route('examination-hub.performance.index') }}"
                   x-ref="{{ $active === 'performance' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'performance' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Performance
                    </div>
                </a>

                <a href="{{ route('examination-hub.reports.index') }}"
                   x-ref="{{ $active === 'reports' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'reports' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Reports
                    </div>
                </a>

                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                    <a href="{{ route('examination-hub.grading-system.index') }}"
                       x-ref="{{ $active === 'grading-system' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'grading-system' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Grading System
                        </div>
                    </a>
                @endif

                <a href="{{ route('examination-hub.participant-groups.index') }}"
                   x-ref="{{ $active === 'participant-groups' ? 'activeNavItem' : '' }}"
                   class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'participant-groups' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Candidates
                    </div>
                </a>

                {{-- ═══════════════════════════════════════════════════════════
                     Multi-Center Sync Items (Central Admin Only)
                ═══════════════════════════════════════════════════════════ --}}
                @if(config('center.role') === 'central')
                    <a href="{{ route('center-sync.dashboard') }}"
                       x-ref="{{ $active === 'center-dashboard' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'center-dashboard' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                            Sync Dashboard
                        </div>
                    </a>

                    <a href="{{ route('center-sync.centers.index') }}"
                       x-ref="{{ $active === 'center-centers' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'center-centers' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Centers
                        </div>
                    </a>

                    <a href="{{ route('center-sync.exams.index') }}"
                       x-ref="{{ $active === 'center-exams' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'center-exams' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Published Exams
                        </div>
                    </a>

                    <a href="{{ route('center-sync.sync.index') }}"
                       x-ref="{{ $active === 'center-sync' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'center-sync' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Sync Status
                        </div>
                    </a>

                    <a href="{{ route('center-sync.bank.index') }}"
                       x-ref="{{ $active === 'center-bank' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'center-bank' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            Bank Backup
                        </div>
                    </a>
                @endif

                {{-- ═══════════════════════════════════════════════════════════
                     Sync Status (Center Instance Only)
                ═══════════════════════════════════════════════════════════ --}}
                @if(config('center.role') === 'center')
                    <a href="{{ route('center-sync.sync.index') }}"
                       x-ref="{{ $active === 'center-sync' ? 'activeNavItem' : '' }}"
                       class="shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $active === 'center-sync' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Sync Status
                        </div>
                    </a>
                @endif

            </div>

            {{-- Right chevron to scroll nav items right --}}
            <button type="button"
                    x-show="showRight"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-on:click="$refs.navContainer.scrollBy({ left: 200, behavior: 'smooth' })"
                    class="absolute right-0 z-10 flex items-center justify-center w-8 h-8 bg-white dark:bg-gray-800 rounded-full shadow-md border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</nav>
