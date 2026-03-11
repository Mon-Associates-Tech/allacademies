<div class="school-switcher">
    @if($canSwitchSchools = $this->canSwitchSchools())
        <!-- Modern Thin Bar -->
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-sm">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-10">
                    <!-- Left Section - School Selector -->
                    <div class="flex items-center space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <div class="relative group">
                            <select wire:change="handleSchoolChange($event.target.value)"
                                    class="appearance-none bg-white/10 backdrop-blur-md border border-white/20 rounded-lg pl-3 pr-8 py-1.5 text-xs font-medium text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-200 hover:bg-white/20 cursor-pointer">
                                <option value="" @if($showAllSchools) selected @endif class="text-gray-900 bg-white">All Schools</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}"
                                            @if($currentSchool && $currentSchool->id == $school->id) selected @endif
                                            class="text-gray-900 bg-white">
                                        {{ $school->name }} {{ $school->code ? '(' . $school->code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Current Selection Badge -->
                        @if($currentSchool)
                            <div class="hidden sm:flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></div>
                                <span class="text-xs font-medium text-white">{{ Str::limit($currentSchool->name, 20) }}</span>
                            </div>
                        @else
                            <div class="hidden sm:flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-xs font-medium text-white">All Schools</span>
                            </div>
                        @endif
                    </div>

                    <!-- Center Section - Stats (Hidden on mobile) -->
                    <div class="hidden lg:flex items-center space-x-6">
                        @php $stats = $this->stats @endphp

                        @if($showAllSchools)
                            <div class="flex items-center space-x-1.5 text-white/90">
                                <div class="w-1 h-1 rounded-full bg-white/60"></div>
                                <span class="text-xs font-medium">{{ $stats['total_schools'] ?? 0 }}</span>
                                <span class="text-xs text-white/70">Schools</span>
                            </div>
                        @endif

                        <div class="flex items-center space-x-1.5 text-white/90">
                            <div class="w-1 h-1 rounded-full bg-white/60"></div>
                            <span class="text-xs font-medium">{{ $stats['total_students'] ?? 0 }}</span>
                            <span class="text-xs text-white/70">Students</span>
                        </div>

                        <div class="flex items-center space-x-1.5 text-white/90">
                            <div class="w-1 h-1 rounded-full bg-white/60"></div>
                            <span class="text-xs font-medium">{{ $stats['total_teachers'] ?? 0 }}</span>
                            <span class="text-xs text-white/70">Teachers</span>
                        </div>
                    </div>

                    <!-- Right Section - Actions -->
                    <div class="flex items-center space-x-3">
                        @if(!$showAllSchools && $currentSchool)
                            <button wire:click="showAllSchools"
                                    type="button"
                                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-xs font-medium text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <span class="hidden sm:inline">View All</span>
                            </button>
                        @endif

                        <!-- School Count Badge -->
                        <div class="hidden md:flex items-center px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                            <span class="text-xs font-medium text-white">{{ $schools->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        @if (session()->has('success'))
            <div class="fixed top-16 right-4 z-50 max-w-sm"
                 x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-green-200 dark:border-green-800 p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Success</p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="fixed top-16 right-4 z-50 max-w-sm"
                 x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-red-200 dark:border-red-800 p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Error</p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- JavaScript for handling school switching -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('school-switched', (event) => {
                console.log('School switched:', event);

                // Show a brief loading state with animation
                const switcher = document.querySelector('.school-switcher');
                if (switcher) {
                    switcher.style.transition = 'opacity 0.3s ease';
                    switcher.style.opacity = '0.7';
                }

                // Reload the page after a brief delay
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            });
        });
    </script>
</div>
