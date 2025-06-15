<x-app>
    <div class="bg-white">
        <!-- Navigation -->
        <div x-data="{ open: false }" class="absolute inset-x-0 top-12 w-11/12 mx-auto z-50">
            <nav class="flex items-center justify-between bg-white/95 backdrop-blur-sm shadow-lg rounded-3xl p-4 lg:px-8 border border-gray-100" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="#" class="-m-1.5 p-1.5 flex items-center gap-4 group">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <img class="h-8 w-auto group-hover:scale-110 transition-transform duration-300" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">
                        <span class="text-lg font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">All Academies</span>
                    </a>
                </div>
                <div class="flex lg:hidden">
                    <button x-on:click="open = true" type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 hover:bg-gray-100 transition-colors">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
                <div class="hidden lg:flex lg:gap-x-8">
                    <a href="#home" class="text-lg font-semibold leading-6 text-gray-900 hover:text-blue-600 transition-colors duration-300 relative group">
                        Home
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#about" class="text-lg font-semibold leading-6 text-gray-900 hover:text-blue-600 transition-colors duration-300 relative group">
                        About
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#features" class="text-lg font-semibold leading-6 text-gray-900 hover:text-blue-600 transition-colors duration-300 relative group">
                        Features
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#pricing" class="text-lg font-semibold leading-6 text-gray-900 hover:text-blue-600 transition-colors duration-300 relative group">
                        Pricing
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#faq" class="text-lg font-semibold leading-6 text-gray-900 hover:text-blue-600 transition-colors duration-300 relative group">
                        FAQ
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </div>
                <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                    <a href="{{ route('sign-in') }}" class="text-sm font-semibold leading-6 text-white px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 shadow-lg hover:shadow-xl hover:scale-105 rounded-xl transition-all duration-300">
                        Sign In <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </nav>

            <!-- Mobile menu -->
            <div x-show="open" x-transition:enter="duration-200 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="duration-100 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="lg:hidden" role="dialog" aria-modal="true">
                <div class="fixed inset-0 z-50 bg-black/20 backdrop-blur-sm" x-on:click="open = false"></div>
                <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10 shadow-2xl">
                    <div class="flex items-center justify-between">
                        <a href="#" class="-m-1.5 p-1.5">
                            <span class="sr-only">{{ config('app.name') }}</span>
                            <img class="h-8 w-auto" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">
                        </a>
                        <button x-on:click="open = false" type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700 hover:bg-gray-100 transition-colors">
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10">
                            <div class="space-y-2 py-6">
                                <a href="#home" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-blue-50 transition-colors">Home</a>
                                <a href="#about" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-blue-50 transition-colors">About</a>
                                <a href="#features" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-blue-50 transition-colors">Features</a>
                                <a href="#pricing" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-blue-50 transition-colors">Pricing</a>
                                <a href="#faq" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-blue-50 transition-colors">FAQ</a>
                            </div>
                            <div class="py-6">
                                <a href="{{ route('sign-in') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-white bg-gradient-to-r from-blue-600 to-green-600 hover:shadow-lg transition-all">Sign In</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div id="home" class="relative min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute inset-0">
                <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-green-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-orange-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-32 sm:px-6 lg:px-8 lg:py-40">
                <div class="text-center fade-in-text">
                    <!-- Announcement badge -->
                    <div class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-800 mb-8 hover:bg-blue-200 transition-colors cursor-pointer">
                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Announcing our new interactive quiz platform
                    </div>

                    <!-- Main heading -->
                    <h1 class="text-5xl md:text-7xl font-bold tracking-tight bg-gradient-to-r from-blue-600 via-purple-600 to-green-600 bg-clip-text text-transparent mb-8">
                        Study and Assess
                        <span class="block">Yourself Online!</span>
                    </h1>

                    <!-- Subheading -->
                    <p class="mx-auto max-w-3xl text-xl md:text-2xl leading-relaxed text-gray-600 mb-12">
                        Study and move at your own pace without any pressure. Easily assess yourself to enhance your understanding.
                        <span class="font-semibold text-blue-600">Learning made easier and flexible.</span>
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                        <a href="{{ route('sign-up') }}" class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                            <span class="relative z-10">Get Started Free</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-green-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        <a href="#about" class="px-8 py-4 bg-white text-gray-800 font-semibold rounded-xl border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-300 text-lg">
                            Learn More →
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                        <div class="text-center article">
                            <div class="text-3xl font-bold text-blue-600 mb-2">10,000+</div>
                            <div class="text-gray-600">Students Learning</div>
                        </div>
                        <div class="text-center article">
                            <div class="text-3xl font-bold text-green-600 mb-2">500+</div>
                            <div class="text-gray-600">Courses Available</div>
                        </div>
                        <div class="text-center article">
                            <div class="text-3xl font-bold text-purple-600 mb-2">99%</div>
                            <div class="text-gray-600">Success Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <a href="#about" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- About Section -->
        <div id="about" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-text">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Why Choose <span class="text-blue-600">All Academies?</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        We revolutionize online learning with cutting-edge technology and personalized experiences
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="article-container">
                        <div class="article space-y-8">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Comprehensive Learning</h3>
                                    <p class="text-gray-600">Access thousands of courses across multiple subjects with expert-curated content</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Smart Analytics</h3>
                                    <p class="text-gray-600">Track your progress with detailed analytics and personalized recommendations</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Community Learning</h3>
                                    <p class="text-gray-600">Connect with peers and instructors in our interactive learning community</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="article">
                        <div class="bg-gradient-to-r from-blue-600 to-green-600 rounded-2xl p-8 text-white">
                            <h3 class="text-2xl font-bold mb-6">Start Learning Today</h3>
                            <div class="space-y-4 mb-8">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Free trial for 7 days</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Cancel anytime</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>24/7 support</span>
                                </div>
                            </div>
                            <a href="{{ route('sign-up') }}" class="inline-block bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                                Join Now →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-text">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Powerful <span class="text-green-600">Features</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Everything you need for an exceptional online learning experience
                    </p>
                </div>

                <div class="feature-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="feature-item left bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Interactive Quizzes</h3>
                        <p class="text-gray-600">Engage with dynamic quizzes that adapt to your learning pace and provide instant feedback</p>
                    </div>

                    <div class="feature-item right bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Flexible Scheduling</h3>
                        <p class="text-gray-600">Learn at your own pace with 24/7 access to courses and materials from anywhere</p>
                    </div>

                    <div class="feature-item left bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Certified Courses</h3>
                        <p class="text-gray-600">Earn industry-recognized certificates upon completion of courses</p>
                    </div>

                    <div class="feature-item right bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Progress Tracking</h3>
                        <p class="text-gray-600">Monitor your learning journey with detailed progress reports and analytics</p>
                    </div>

                    <div class="feature-item left bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Expert Support</h3>
                        <p class="text-gray-600">Get help from experienced instructors and teaching assistants whenever you need it</p>
                    </div>

                    <div class="feature-item right bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Mobile Learning</h3>
                        <p class="text-gray-600">Access your courses on any device with our responsive mobile-friendly platform</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="pricing" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-text">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Simple <span class="text-blue-600">Pricing</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Choose the plan that works best for you
                    </p>
                </div>

                <div class="price-card-container grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Basic Plan -->
                    <div class="price-cards bg-slate-100 border-2 border-gray-200 rounded-2xl p-8 hover:border-blue-300 hover:shadow-xl transition-all duration-300 relative">
                        <div class="text-center">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic</h3>
                            <p class="text-gray-600 mb-6">Perfect for prolific learners and anyone needing just a short time. </p>
                            <div class="text-4xl font-bold text-gray-900 mb-6">
                                GHC 15 <span class="text-lg font-normal text-gray-600">/Subject</span>
                            </div>
                            <ul class="space-y-4 mb-8 text-left">
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Access to 100+ courses
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Basic quiz features
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Email support
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Mobile app access
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Progress tracking
                                </li>
                            </ul>
                            <a href="{{ route('sign-up') }}" class="block w-full bg-gray-900 text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                                Get Started
                            </a>
                        </div>
                    </div>

                    <!-- Standard Plan (Featured) -->
                    <div class="price-cards bg-gradient-to-br from-blue-600 to-green-600 rounded-2xl p-8 text-white transform scale-105 shadow-2xl relative">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <div class="bg-orange-400 text-white text-sm font-bold py-2 px-6 rounded-full shadow-lg">
                                Most Popular
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-2xl font-bold mb-2">Standard</h3>
                            <p class="text-blue-100 mb-6">Best for serious learners</p>
                            <div class="text-4xl font-bold mb-6">
                                GHC 20 <span class="text-lg font-normal opacity-80">/Subject</span>
                            </div>
                            <ul class="space-y-4 mb-8 text-left">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Access to all courses
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Advanced analytics & insights
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Certificates included
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Priority support
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Live instructor sessions
                                </li>
                            </ul>
                            <a href="{{ route('sign-up') }}" class="block w-full bg-white text-blue-600 text-center py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                Start Free Trial
                            </a>
                        </div>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="price-cards bg-slate-50 border-2 border-gray-200 rounded-2xl p-8 hover:border-blue-300 hover:shadow-xl transition-all duration-300 relative">
                        <div class="text-center">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                            <p class="text-gray-600 mb-6">For teams & organizations</p>
                            <div class="text-4xl font-bold text-gray-900 mb-6">
                                GHC 30 <span class="text-lg font-normal text-gray-600">/Subject</span>
                            </div>
                            <ul class="space-y-4 mb-8 text-left">
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Unlimited access for teams
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Custom branding & LMS
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Dedicated account manager
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Advanced integrations
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    24/7 premium support
                                </li>
                            </ul>
                            <a href="{{ route('sign-up') }}" class="block w-full bg-gray-900 text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                                Contact Sales
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pricing Features Comparison -->
                <div class="mt-16 text-center">
                    <p class="text-gray-600 mb-4">All plans include:</p>
                    <div class="flex flex-wrap justify-center gap-8 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            No setup fees
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Cancel anytime
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            7-day free trial
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            SSL security
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div id="faq" class="py-24 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-text">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Frequently Asked <span class="text-green-600">Questions</span>
                    </h2>
                    <p class="text-xl text-gray-600">
                        Everything you need to know about All Academies
                    </p>
                </div>

                <div x-data="{ openFaq: null }" class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900">How do I get started?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openFaq === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="px-6 pb-4">
                            <div class="space-y-4">

                                <!-- Quick Start Guide -->
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h4 class="font-semibold text-blue-900">Quick Guide: Creating Your Account</h4>
                                    </div>
                                    <p class="text-sm text-blue-800">
                                        Follow these simple steps to join All Academies and start your learning journey today!
                                    </p>
                                </div>

                                <!-- Step 1: Find Sign Up -->
                                <div class="flex items-start space-x-3 p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        1
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-green-900 mb-2">Find the Sign Up Option</h4>
                                        <div class="text-sm text-green-800 space-y-2">
                                            <p>On this login page, look for the section below the login form.</p>
                                            <div class="bg-white p-3 rounded border border-green-200">
                                                <div class="flex items-center justify-center">
                                                    <span class="text-xs text-gray-500 mr-2">Look for:</span>
                                                    <div class="bg-gray-100 px-3 py-1 rounded text-sm font-medium">
                                                        "New to All Academies?"
                                                    </div>
                                                </div>
                                                <div class="text-center mt-2">
                                                    <div class="inline-flex items-center text-blue-600 text-sm font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                                        </svg>
                                                        Create new account
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Fill Form Fields -->
                                <div class="flex items-start space-x-3 p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                                    <div class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        2
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-purple-900 mb-3">Complete the Registration Form</h4>
                                        <div class="text-sm text-purple-800 space-y-3">
                                            <p class="mb-3">Fill out all required fields in the registration form:</p>

                                            <!-- Form Field Examples -->
                                            <div class="grid grid-cols-1 gap-3">
                                                <!-- Full Name -->
                                                <div class="bg-white p-3 rounded border border-purple-200">
                                                    <div class="flex items-center mb-2">
                                                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        <span class="font-medium text-purple-900">Full Name</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 ml-6">Enter your complete name (e.g., "John Smith")</p>
                                                </div>

                                                <!-- Email -->
                                                <div class="bg-white p-3 rounded border border-purple-200">
                                                    <div class="flex items-center mb-2">
                                                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                                        </svg>
                                                        <span class="font-medium text-purple-900">Email Address</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 ml-6">Use a valid email you can access (e.g., "john@email.com")</p>
                                                </div>

                                                <!-- Password -->
                                                <div class="bg-white p-3 rounded border border-purple-200">
                                                    <div class="flex items-center mb-2">
                                                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                        <span class="font-medium text-purple-900">Create Password</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 ml-6">Choose a strong password (minimum 8 characters)</p>
                                                </div>

                                                <!-- Confirm Password -->
                                                <div class="bg-white p-3 rounded border border-purple-200">
                                                    <div class="flex items-center mb-2">
                                                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="font-medium text-purple-900">Confirm Password</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 ml-6">Type the same password again to confirm</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Submit -->
                                <div class="flex items-start space-x-3 p-4 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                                    <div class="flex-shrink-0 w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        3
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-orange-900 mb-2">Complete Registration</h4>
                                        <div class="text-sm text-orange-800 space-y-3">
                                            <div class="bg-white p-3 rounded border border-orange-200">
                                                <p class="mb-2">After filling all fields, look for the sign up button:</p>
                                                <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white px-4 py-2 rounded-xl text-center font-medium text-sm">
                                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                                    </svg>
                                                    Create your free account
                                                </div>
                                                <p class="text-xs text-gray-600 mt-2">Click this button to complete your registration</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success Message -->
                                <div class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-teal-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h4 class="font-semibold text-teal-900 mb-1">What Happens Next?</h4>
                                            <ul class="text-sm text-teal-800 space-y-1">
                                                <li>• You'll receive a welcome email to verify your account</li>
                                                <li>• Your account will be created and ready to use</li>
                                                <li>• You can start exploring courses immediately</li>
                                                <li>• Your free trial begins automatically</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Tips -->
                                <div class="bg-gray-50 p-4 rounded-lg border">
                                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                        Helpful Tips
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-gray-700">
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-2">•</span>
                                            <span>Use a strong password with letters, numbers, and symbols</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-2">•</span>
                                            <span>Make sure your email is correct for account verification</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-2">•</span>
                                            <span>Keep your login details safe and secure</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-2">•</span>
                                            <span>Check your spam folder if you don't receive the welcome email</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900">How do I subscribe?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openFaq === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="px-6 pb-4">

                            <!-- Step-by-step subscription guide -->
                            <div class="space-y-4">

                                <!-- Step 1: Access Subscriptions -->
                                <div class="flex items-start space-x-3 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        1
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-blue-900 mb-2">Access Your Subscriptions</h4>
                                        <div class="text-sm text-blue-800 space-y-1">
                                            <p>• Look for your <strong>initials</strong> at the top right corner of your home page</p>
                                            <p>• Click on the dropdown menu</p>
                                            <p>• Select <strong>"Subscriptions"</strong> from the menu</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Create New Subscription -->
                                <div class="flex items-start space-x-3 p-4 bg-green-50 rounded-lg border-l-4 border-green-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        2
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-green-900 mb-2">Start New Subscription</h4>
                                        <div class="text-sm text-green-800">
                                            <p>On your subscription page, click the <strong>"New Subscription"</strong> button</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Choose Package Type -->
                                <div class="flex items-start space-x-3 p-4 bg-purple-50 rounded-lg border-l-4 border-purple-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        3
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-purple-900 mb-2">Select Package Type</h4>
                                        <div class="text-sm text-purple-800">
                                            <p class="mb-2">Under <strong>"Package"</strong>, choose your subscription type:</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <div class="bg-white p-3 rounded border border-purple-200">
                                                    <div class="flex items-center">
                                                        <div class="w-2 h-2 bg-purple-400 rounded-full mr-2"></div>
                                                        <span class="font-medium">Individual Subscription</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 mt-1">For personal use</p>
                                                </div>
                                                <div class="bg-white p-3 rounded border border-purple-200">
                                                    <div class="flex items-center">
                                                        <div class="w-2 h-2 bg-purple-400 rounded-full mr-2"></div>
                                                        <span class="font-medium">Institutional Subscription</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 mt-1">For schools/organizations</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4: Choose Duration -->
                                <div class="flex items-start space-x-3 p-4 bg-orange-50 rounded-lg border-l-4 border-orange-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        4
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-orange-900 mb-2">Select Duration</h4>
                                        <div class="text-sm text-orange-800">
                                            <p class="mb-2">Under <strong>"Duration"</strong>, choose your subscription period:</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                                <div class="bg-white p-3 rounded border border-orange-200 text-center">
                                                    <div class="font-semibold text-orange-600">3 Months</div>
                                                    <div class="text-xs text-gray-600">Short term</div>
                                                </div>
                                                <div class="bg-white p-3 rounded border border-orange-200 text-center">
                                                    <div class="font-semibold text-orange-600">6 Months</div>
                                                    <div class="text-xs text-gray-600">Medium term</div>
                                                </div>
                                                <div class="bg-white p-3 rounded border border-orange-200 text-center">
                                                    <div class="font-semibold text-orange-600">12 Months</div>
                                                    <div class="text-xs text-gray-600">Best value</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 5: Select School Category & Level -->
                                <div class="flex items-start space-x-3 p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        5
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-indigo-900 mb-2">Choose School Category & Level</h4>
                                        <div class="text-sm text-indigo-800 space-y-2">
                                            <div class="bg-white p-3 rounded border border-indigo-200">
                                                <p class="font-medium mb-1">1. Select School Category</p>
                                                <p class="text-xs">Example: <span class="font-mono bg-gray-100 px-1 rounded">Primary/Basic School</span></p>
                                            </div>
                                            <div class="bg-white p-3 rounded border border-indigo-200">
                                                <p class="font-medium mb-1">2. Choose Your Level</p>
                                                <p class="text-xs">Example: <span class="font-mono bg-gray-100 px-1 rounded">Basic Three</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 6: Select Subjects -->
                                <div class="flex items-start space-x-3 p-4 bg-teal-50 rounded-lg border-l-4 border-teal-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-teal-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        6
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-teal-900 mb-2">Select Your Subjects</h4>
                                        <div class="text-sm text-teal-800">
                                            <div class="bg-white p-3 rounded border border-teal-200">
                                                <p class="mb-2">All available subjects for your level will be displayed.</p>
                                                <div class="flex items-center text-xs">
                                                    <svg class="w-4 h-4 mr-1 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <strong>Tip:</strong> Click the checkboxes on the left of each subject you want to subscribe to
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 7: Review & Submit -->
                                <div class="flex items-start space-x-3 p-4 bg-red-50 rounded-lg border-l-4 border-red-400">
                                    <div class="flex-shrink-0 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        7
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-red-900 mb-2">Review & Create Subscription</h4>
                                        <div class="text-sm text-red-800 space-y-2">
                                            <div class="bg-white p-3 rounded border border-red-200">
                                                <p class="mb-2">• Check the <strong>"Amount"</strong> section at the bottom to see your total subscription fee</p>
                                                <p class="mb-2">• Review your selections carefully</p>
                                                <p>• Click the green <strong>"Create Subscription"</strong> button to submit</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Quick Summary -->
                            <div class="mt-6 bg-gray-50 p-4 rounded-lg border">
                                <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Quick Summary
                                </h4>
                                <p class="text-sm text-gray-700">
                                    <strong>Path:</strong> Initials Dropdown → Subscriptions → New Subscription → Package Type → Duration → School Category → Level → Select Subjects → Review Amount → Create Subscription
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900">How do I pay for my subscription?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openFaq === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="px-6 pb-4">
                            <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg">
                                <h4 class="font-semibold text-green-800 dark:text-green-200 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    Complete Your Payment
                                </h4>

                                <!-- Step-by-step payment guide -->
                                <div class="space-y-4">
                                    <!-- Step 1 -->
                                    <div class="flex items-start space-x-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-700">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            1
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-1">Dial USSD Code</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                On your mobile phone, dial the following code:
                                            </p>
                                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                                <code class="text-lg font-mono text-green-600 dark:text-green-400 font-bold">*772*30#</code>
                                                <button class="ml-2 text-xs text-blue-600 hover:text-blue-500 underline" onclick="navigator.clipboard.writeText('*772*30#')">
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="flex items-start space-x-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-700">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            2
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-1">Enter Merchant Code</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                Press send, then enter the merchant code:
                                            </p>
                                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                                <code class="text-lg font-mono text-green-600 dark:text-green-400 font-bold">1326001</code>
                                                <button class="ml-2 text-xs text-blue-600 hover:text-blue-500 underline" onclick="navigator.clipboard.writeText('1326001')">
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="flex items-start space-x-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-700">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            3
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-1">Choose Payment Method</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                Select your preferred payment option:
                                            </p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-lg border">
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">1</span>
                                                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Mobile Money</span>
                                                    </div>
                                                </div>
                                                <div class="bg-orange-50 dark:bg-orange-900 p-3 rounded-lg border">
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">2</span>
                                                        <span class="text-sm font-medium text-orange-800 dark:text-orange-200">Prudential Bank</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 4 -->
                                    <div class="flex items-start space-x-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-700">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            4
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-1">Enter Reference Number</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                When prompted, enter your subscription reference number exactly as shown:
                                            </p>
                                            <div class="bg-yellow-50 dark:bg-yellow-900 p-3 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                                <div class="flex items-center justify-between">
                                                    <code class="text-sm font-mono text-yellow-800 dark:text-yellow-200 break-all">
                                                        {{ $subscriptionData['reference'] ?? 'N/A' }}
                                                    </code>
                                                    <button class="ml-2 text-xs text-blue-600 hover:text-blue-500 underline flex-shrink-0" onclick="navigator.clipboard.writeText('{{ $subscriptionData['reference'] ?? '' }}')">
                                                        Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-2 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Important: Enter this number exactly as shown
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Step 5 -->
                                    <div class="flex items-start space-x-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-700">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            5
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-1">Confirm Payment</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                Review the payment details and confirm by pressing:
                                            </p>
                                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                                <code class="text-lg font-mono text-green-600 dark:text-green-400 font-bold">1</code>
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">to confirm and submit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success notice -->
                                <div class="mt-6 bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h5 class="font-medium text-blue-800 dark:text-blue-200 mb-1">After Payment</h5>
                                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                                Your subscription status will automatically change from "unpaid" to "paid" and you'll have immediate access to the content.
                                                Please check your subscription page to confirm the payment was processed successfully.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Help section -->
                                <div class="mt-4 text-center">
                                    <p class="text-xs text-green-700 dark:text-green-300">
                                        Need help? Contact our support team for assistance with your payment.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900">Is there mobile access?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openFaq === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="px-6 pb-4">
                            <p class="text-gray-600">Absolutely! Our platform is fully responsive and works seamlessly on all devices including smartphones, tablets, and desktop computers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24 bg-gradient-to-r from-blue-600 to-green-600">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 fade-in-text">
                    Ready to Start Learning?
                </h2>
                <p class="text-xl text-blue-100 mb-8 fade-in-text">
                    Join thousands of students already learning with All Academies
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center fade-in-text">
                    <a href="{{ route('sign-up') }}" class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:bg-gray-100 transition-all text-lg">
                        Start Free Trial
                    </a>
                    <a href="{{ route('sign-in') }}" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-blue-600 transition-all text-lg">
                        Sign In
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <img class="h-8 w-auto" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">
                            <span class="text-xl font-bold">All Academies</span>
                        </div>
                        <p class="text-gray-300 mb-6">
                            Empowering students worldwide with flexible, accessible, and high-quality online education.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Platform</h3>
                        <ul class="space-y-2">
                            <li><a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a></li>
                            <li><a href="#pricing" class="text-gray-300 hover:text-white transition-colors">Pricing</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">API</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Integrations</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <ul class="space-y-2">
                            <li><a href="#faq" class="text-gray-300 hover:text-white transition-colors">FAQ</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Help Center</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Status</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} All Academies. All rights reserved.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</x-app>
