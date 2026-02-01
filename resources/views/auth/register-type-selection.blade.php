<x-app>
    <section class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0">
            <div
                class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div
                class="absolute top-1/3 right-1/4 w-64 h-64 bg-green-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div
                class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-orange-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="min-h-screen flex items-center justify-center px-4 py-12 relative z-10">
            <div class="w-full max-w-4xl">
                <!-- Logo and branding -->
                <div class="text-center mb-12">
                    <div class="flex items-center gap-3 mb-6 justify-center">
                        <img class="h-12 w-auto" src="{{ asset('img/logo.png') }}"
                             alt="{{ config('app.name') }} Logo">
                        <span
                            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">All Academies</span>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">
                        Choose Your Account Type
                    </h1>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Select the type of account that best fits your needs. You can always switch later.
                    </p>
                </div>

                <!-- Registration type cards -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <!-- Guest Account -->
                    <a href="{{ route('register.guest') }}"
                       class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="flex justify-center mb-6">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Student / Guest</h3>
                            <p class="text-gray-600 text-center text-sm mb-6">
                                Learn from courses, access resources, and grow your knowledge.
                            </p>
                            <ul class="space-y-2 text-sm text-gray-600 mb-6">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Access 500+ courses
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    7-day free trial
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    No credit card needed
                                </li>
                            </ul>
                            <button
                                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors group-hover:shadow-lg">
                                Get Started
                            </button>
                        </div>
                    </a>

                    <!-- Author Account -->
                    <a href="{{ route('register.author') }}"
                       class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden md:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute -top-1 right-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-1 rounded-full text-xs font-semibold">
                            Popular
                        </div>
                        <div class="relative p-8">
                            <div class="flex justify-center mb-6">
                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747 0-6.002-4.5-10.747-10-10.747z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Author / Instructor</h3>
                            <p class="text-gray-600 text-center text-sm mb-6">
                                Create and share your courses, build your audience, and earn.
                            </p>
                            <ul class="space-y-2 text-sm text-gray-600 mb-6">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Create courses
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Analytics & insights
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Monetization tools
                                </li>
                            </ul>
                            <button
                                class="w-full py-2 px-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-lg transition-colors group-hover:shadow-lg">
                                Start Creating
                            </button>
                        </div>
                    </a>

                    <!-- School Account -->
                    <a href="{{ route('register.school') }}"
                       class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-green-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="flex justify-center mb-6">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 text-center mb-3">School / Institution</h3>
                            <p class="text-gray-600 text-center text-sm mb-6">
                                Onboard your school and manage all students and staff.
                            </p>
                            <ul class="space-y-2 text-sm text-gray-600 mb-6">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Manage students
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    School administration
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Advanced features
                                </li>
                            </ul>
                            <button
                                class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors group-hover:shadow-lg">
                                Onboard School
                            </button>
                        </div>
                    </a>
                </div>

                <!-- Divider -->
                <div class="mt-12 mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"/>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                        </div>
                    </div>
                </div>

                <!-- Sign in link -->
                <div class="text-center">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-8 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-blue-300 transition-all duration-200"
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
