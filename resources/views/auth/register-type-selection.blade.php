<x-app>
    <section class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 dark:opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-300 dark:bg-indigo-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 dark:opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-300 dark:bg-pink-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 dark:opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <div class="h-screen flex flex-col items-center justify-center px-4 py-8 sm:px-6 lg:px-8 relative z-10 overflow-y-auto">
            <div class="w-full max-w-5xl flex-shrink-0">
                <!-- Logo and branding -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center justify-center gap-3 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl blur-lg opacity-50"></div>
                            <img class="relative h-14 w-auto" src="{{ asset('img/logo.png') }}"
                                 alt="{{ config('app.name') }} Logo">
                        </div>
                        <span class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">All Academies</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                        Join All Academies
                    </h1>
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Choose your account type to get started with our platform
                    </p>
                </div>

                <!-- Registration type cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-4">
                    <!-- Guest Account -->
                    <a href="{{ route('register.guest') }}"
                       class="group relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl dark:shadow-gray-900/50 hover:shadow-2xl dark:hover:shadow-indigo-500/20 transition-all duration-500 transform hover:-translate-y-3 overflow-visible border border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-800 md:scale-105 md:z-10">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-indigo-500/0 dark:from-indigo-500/10 dark:to-indigo-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl"></div>
                        <div class="absolute -top-2 right-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-1 rounded-full text-xs font-bold shadow-lg transform rotate-3 z-10">
                            POPULAR
                        </div>
                        <div class="relative p-8 h-full flex flex-col">
                            <div class="flex justify-center mb-6">
                                <div class="relative">
                                    <div class="absolute inset-0 bg-indigo-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-50 transition-opacity duration-500"></div>
                                    <div class="relative w-20 h-20 bg-gradient-to-br from-indigo-100 to-indigo-50 dark:from-indigo-900/30 dark:to-indigo-900/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-3">Student / Guest</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center text-sm mb-6 flex-grow">
                                Access courses, learn at your pace, and grow your knowledge
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400 mb-6">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Access 500+ courses</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>7-day free trial of premium features</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>No credit card needed</span>
                                </li>
                            </ul>
                            <button
                                class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-xl transition-all duration-300 group-hover:shadow-lg group-hover:shadow-indigo-500/50">
                                Get Started
                            </button>
                        </div>
                    </a>

                    <!-- Author Account -->
                    <a href="{{ route('register.author') }}"
                       class="group relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl dark:shadow-gray-900/50 hover:shadow-2xl dark:hover:shadow-purple-500/20 transition-all duration-500 transform hover:-translate-y-3 overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-purple-200 dark:hover:border-purple-800">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-purple-500/0 dark:from-purple-500/10 dark:to-purple-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl"></div>
                        <div class="relative p-8 h-full flex flex-col">
                            <div class="flex justify-center mb-6">
                                <div class="relative">
                                    <div class="absolute inset-0 bg-purple-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-50 transition-opacity duration-500"></div>
                                    <div class="relative w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <svg class="w-10 h-10 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-3">Author / Instructor</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center text-sm mb-6 flex-grow">
                                Create courses, share knowledge, and earn from your expertise
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400 mb-6">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Create unlimited courses</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Analytics & insights</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-purple-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Monetization tools</span>
                                </li>
                            </ul>
                            <button
                                class="w-full py-3 px-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-xl transition-all duration-300 group-hover:shadow-lg group-hover:shadow-purple-500/50">
                                Start Creating
                            </button>
                        </div>
                    </a>

                    <!-- School Account -->
                    <a href="{{ route('register.school') }}"
                       class="group relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl dark:shadow-gray-900/50 hover:shadow-2xl dark:hover:shadow-green-500/20 transition-all duration-500 transform hover:-translate-y-3 overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-green-200 dark:hover:border-green-800">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 to-green-500/0 dark:from-green-500/10 dark:to-green-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative p-8 h-full flex flex-col">
                            <div class="flex justify-center mb-6">
                                <div class="relative">
                                    <div class="absolute inset-0 bg-green-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-50 transition-opacity duration-500"></div>
                                    <div class="relative w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-3">School / Institution</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center text-sm mb-6 flex-grow">
                                Manage your institution with comprehensive tools and features
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400 mb-6">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Complete student management</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Staff administration</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span>Advanced reporting</span>
                                </li>
                            </ul>
                            <button
                                class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all duration-300 group-hover:shadow-lg group-hover:shadow-green-500/50">
                                Onboard School
                            </button>
                        </div>
                    </a>
                </div>

                <!-- Divider -->
                <div class="mt-8 mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200 dark:border-gray-700"/>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">Already have an account?</span>
                        </div>
                    </div>
                </div>

                <!-- Sign in link -->
                <div class="mt-6">
                    <a
                        href="{{ route('login') }}"
                        class="group flex items-center justify-center w-full px-6 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-all duration-300"
                    >
                        <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:-translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">Sign in to your account</span>
                        <svg class="w-5 h-5 ml-3 text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app>
