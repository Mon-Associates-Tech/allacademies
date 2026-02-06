<x-app>
    <section class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0">
            <div
                class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-200 dark:bg-blue-900/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 dark:opacity-30 animate-blob"></div>
            <div
                class="absolute top-1/3 right-1/4 w-64 h-64 bg-green-200 dark:bg-green-900/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 dark:opacity-30 animate-blob animation-delay-2000"></div>
            <div
                class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-orange-200 dark:bg-orange-900/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 dark:opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="h-screen flex flex-col items-center justify-center px-4 py-6 sm:px-6 lg:px-8 relative z-10 overflow-y-auto">
            <div class="w-full max-w-5xl flex-shrink-0">
                <!-- Logo and branding -->
                <div class="text-center mb-8 sm:mb-10">
                    <div class="flex items-center gap-3 mb-6 justify-center">
                        <img class="h-12 w-auto" src="{{ asset('img/logo.png') }}"
                             alt="{{ config('app.name') }} Logo">
                        <span
                            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">All Academies</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Choose Your Account Type
                    </h1>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Select the type of account that best fits your needs.
                    </p>
                </div>

                <!-- Registration type cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <!-- Guest Account -->
                    <a href="{{ route('register.guest') }}"
                       class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg dark:shadow-gray-900/50 hover:shadow-2xl dark:hover:shadow-gray-900/70 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden h-full">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-500/5 dark:from-blue-500/20 dark:to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-5 sm:p-6 h-full flex flex-col">
                            <div class="flex justify-center mb-4">
                                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                                    <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Student / Guest</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center text-xs sm:text-sm mb-4 flex-grow">
                                Learn from courses, access resources, and grow your knowledge.
                            </p>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Access 500+ courses
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    7-day free trial for premium features
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    No credit card needed
                                </li>
                            </ul>
                            <button
                                class="w-full py-2 px-3 text-sm sm:text-base bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors group-hover:shadow-lg">
                                Get Started
                            </button>
                        </div>
                    </a>

                    <!-- Author Account -->
                    <a href="{{ route('register.author') }}"
                       class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg dark:shadow-gray-900/50 hover:shadow-2xl dark:hover:shadow-gray-900/70 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden h-full md:scale-105 md:z-10">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-purple-500/5 dark:from-purple-500/20 dark:to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute -top-1 right-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-0.5 rounded-full text-xs font-semibold shadow-lg">
                            Popular
                        </div>
                        <div class="relative p-5 sm:p-6 h-full flex flex-col">
                            <div class="flex justify-center mb-4">
                                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747 0-6.002-4.5-10.747-10-10.747z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Author / Instructor</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center text-xs sm:text-sm mb-4 flex-grow">
                                Create and share your courses, build your audience, and earn.
                            </p>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Create courses
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Analytics & insights
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Monetization tools
                                </li>
                            </ul>
                            <button
                                class="w-full py-2 px-3 text-sm sm:text-base bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 dark:from-purple-600 dark:to-pink-600 dark:hover:from-purple-700 dark:hover:to-pink-700 text-white font-semibold rounded-lg transition-colors group-hover:shadow-lg">
                                Start Creating
                            </button>
                        </div>
                    </a>

                    <!-- School Account -->
                    <a href="{{ route('register.school') }}"
                       class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg dark:shadow-gray-900/50 hover:shadow-2xl dark:hover:shadow-gray-900/70 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden h-full">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-green-500/5 dark:from-green-500/20 dark:to-green-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-5 sm:p-6 h-full flex flex-col">
                            <div class="flex justify-center mb-4">
                                <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                                    <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">School / Institution</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center text-xs sm:text-sm mb-4 flex-grow">
                                Onboard your school and manage all students and staff.
                            </p>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Manage students
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    School administration
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Advanced features
                                </li>
                            </ul>
                            <button
                                class="w-full py-2 px-3 text-sm sm:text-base bg-green-600 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 text-white font-semibold rounded-lg transition-colors group-hover:shadow-lg">
                                Onboard School
                            </button>
                        </div>
                    </a>
                </div>

                <!-- Divider -->
                <div class="mt-6 sm:mt-7 mb-4 sm:mb-5">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-700"/>
                        </div>
                        <div class="relative flex justify-center text-xs sm:text-sm">
                            <span class="px-2 sm:px-4 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">Already have an account?</span>
                        </div>
                    </div>
                </div>

                <!-- Sign in link -->
                <div class="mt-4 sm:mt-5">
                    <a
                        href="{{ route('login') }}"
                        class="flex items-center justify-center w-full px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-800 text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-blue-400 dark:hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900 transition-all duration-200"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign in to your account
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app>
