<header
    x-data="{
                scrolled: false,
                init() {
                    this.scrolled = window.scrollY > 10;
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 10;
                    });
                }
            }"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 header-blur"
    :class="{
                'bg-white/95 dark:bg-gray-900/95 backdrop-blur-md shadow-lg border-b border-white/20 dark:border-gray-700/50': scrolled,
                '': !scrolled
            }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div x-data="{ open: false }" class="flex justify-between items-center h-16 lg:h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div
                            class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                            <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo"
                                 class="w-8 h-8">
                        </div>
                        <div
                            class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                                <span class="text-xl lg:text-2xl font-bold transition-colors duration-300"
                                      :class="scrolled ? 'text-gray-900 dark:text-white' : 'text-white'">
                                    All Academies
                                </span>
                        <div class="text-xs font-medium tracking-wider transition-colors duration-300"
                             :class="scrolled ? 'text-gray-500 dark:text-gray-400' : 'text-blue-200'">
                            Educational Excellence
                        </div>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('branding.features') }}"
                   class="font-semibold transition-colors duration-300 relative group flex items-center"
                   :class="scrolled ? 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' : 'text-white/90 hover:text-white'">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                    </svg>
                    Features
                    <span
                        class="absolute -bottom-1 left-0 w-0 h-0.5 group-hover:w-full transition-all duration-300"
                        :class="scrolled ? 'bg-blue-600' : 'bg-white'"></span>
                </a>
                <a href="{{ route('library.showcase') }}"
                   class="font-semibold transition-colors duration-300 relative group flex items-center"
                   :class="scrolled ? 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' : 'text-white/90 hover:text-white'">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                    Books
                    <span
                        class="absolute -bottom-1 left-0 w-0 h-0.5 group-hover:w-full transition-all duration-300"
                        :class="scrolled ? 'bg-blue-600' : 'bg-white'"></span>
                </a>
                <a href="{{ route('branding.pricing') }}"
                   class="font-semibold transition-colors duration-300 relative group flex items-center"
                   :class="scrolled ? 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' : 'text-white/90 hover:text-white'">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H4.5m-1.5 0H3c.621 0 1.125.504 1.125 1.125v.375m13.5 0h1.125c.621 0 1.125.504 1.125 1.125v.375a.75.75 0 0 1-1.5 0V6h-.75m-1.5-1.5H21A2.25 2.25 0 0 1 23.25 6v12a2.25 2.25 0 0 1-2.25 2.25H3A2.25 2.25 0 0 1 .75 18V6A2.25 2.25 0 0 1 3 3.75h18Z"/>
                    </svg>
                    Pricing
                    <span
                        class="absolute -bottom-1 left-0 w-0 h-0.5 group-hover:w-full transition-all duration-300"
                        :class="scrolled ? 'bg-blue-600' : 'bg-white'"></span>
                </a>
                <a href="{{ route('public.financial-aid') }}"
                   class="font-semibold transition-colors duration-300 relative group flex items-center"
                   :class="scrolled ? 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' : 'text-white/90 hover:text-white'">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                    </svg>
                    Financial Aid
                    <span
                        class="absolute -bottom-1 left-0 w-0 h-0.5 group-hover:w-full transition-all duration-300"
                        :class="scrolled ? 'bg-blue-600' : 'bg-white'"></span>
                </a>
                <a href="{{ route('branding.contact') }}"
                   class="font-semibold transition-colors duration-300 relative group flex items-center"
                   :class="scrolled ? 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' : 'text-white/90 hover:text-white'">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                    Contact
                    <span
                        class="absolute -bottom-1 left-0 w-0 h-0.5 group-hover:w-full transition-all duration-300"
                        :class="scrolled ? 'bg-blue-600' : 'bg-white'"></span>
                </a>
            </nav>

            <!-- Right side actions -->
            <div class="flex items-center space-x-3 lg:space-x-4">
                <!-- Sign In and Register Buttons -->
                <div class="hidden lg:flex items-center gap-3">
                    <!-- Register Button (Smaller, Outlined) -->
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-4 py-2 font-semibold rounded-lg transition-all duration-300 border-2"
                       :class="scrolled ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950' : 'text-white border-white/40 hover:border-white hover:bg-white/10'">
                        Register
                    </a>
                    <!-- Sign In Button -->
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-6 py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300"
                       :class="scrolled ? 'text-white bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700' : 'text-white bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white hover:text-blue-600'">
                        Sign In
                        <span aria-hidden="true" class="ml-2">&rarr;</span>
                    </a>
                    <x-link.primary variant="primary" type="button" class="ml-4 hidden"
                                    to="{{ route('payments.public.lookup') }}">
                        <span>School Fees Payment Portal</span>
                    </x-link.primary>
                </div>

                <!-- Mobile menu button -->
                <button x-on:click="open = true" type="button"
                        class="lg:hidden inline-flex items-center justify-center rounded-md p-2.5 hover:bg-white/10 transition-colors"
                        :class="scrolled ? 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' : 'text-white'">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div x-show="open"
                 x-transition:enter="duration-200 ease-out"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="duration-100 ease-in"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="lg:hidden"
                 role="dialog"
                 aria-modal="true"
                 @click.outside="open = false">
                <div class="fixed inset-0 z-50 bg-black/20 backdrop-blur-sm" x-on:click="open = false"></div>
                <div
                    class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white dark:bg-gray-900 px-6 py-6 sm:max-w-sm shadow-2xl">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('home') }}" class="-m-1.5 p-1.5">
                            <span class="sr-only">{{ config('app.name') }}</span>
                            <img class="h-8 w-auto" src="{{ asset('img/logo.png') }}"
                                 alt="{{ config('app.name') }} Logo">
                        </a>
                        <button x-on:click="open = false" type="button"
                                class="-m-2.5 rounded-md p-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10 dark:divide-gray-400/10">
                            <div class="space-y-2 py-6">
                                <a href="{{ route('branding.features') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                   x-on:click="open = false">Features</a>
                                <a href="{{ route('library.showcase') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                   x-on:click="open = false">Books</a>
                                <a href="{{ route('branding.pricing') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                   x-on:click="open = false">Pricing</a>
                                <a href="{{ route('public.financial-aid') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                   x-on:click="open = false">Financial Aid</a>
                                <a href="{{ route('branding.contact') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                   x-on:click="open = false">Contact</a>
                            </div>
                            <div class="py-6 space-y-3">
                                <a href="{{ route('register') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-center border-2 border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950 dark:border-blue-400 dark:text-blue-400 transition-all">
                                    Register
                                </a>
                                <a href="{{ route('login') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-center text-white bg-gradient-to-r from-blue-600 to-green-600 hover:shadow-lg transition-all">
                                    Sign In
                                </a>
                                <a href="{{ route('payments.public.lookup') }}"
                                   class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-center text-white bg-gradient-to-r from-blue-600 to-green-600 hover:shadow-lg transition-all">
                                    School Fees Payment Portal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</header>
