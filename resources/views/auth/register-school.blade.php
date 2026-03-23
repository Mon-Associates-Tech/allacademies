<x-app>
    <section class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-green-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-emerald-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-teal-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="lg:grid lg:min-h-screen lg:grid-cols-12 relative z-10">
            <!-- Image section -->
            <aside class="relative hidden h-32 sm:block lg:order-last lg:col-span-5 lg:h-screen lg:sticky lg:top-0 xl:col-span-6">
                <img
                    alt="School building"
                    src="https://images.pexels.com/photos/256395/pexels-photo-256395.jpeg?auto=compress&cs=tinysrgb&w=1200"
                    class="absolute inset-0 h-full w-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent lg:bg-gradient-to-l"></div>
                <div class="absolute bottom-8 left-8 right-8 text-white lg:bottom-16 lg:left-16 lg:right-16">
                    <h2 class="text-2xl font-bold lg:text-4xl mb-4">Modernize Your School</h2>
                    <p class="text-lg opacity-90 mb-6">Manage students, teachers, and all school operations in one place</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Complete management</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Real-time reporting</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Dedicated support</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Custom integration</span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Form section -->
            <main class="flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:col-span-7 lg:px-16 xl:col-span-6 lg:overflow-y-auto lg:h-screen lg:py-16 dark:bg-gray-800 bg-white lg:bg-transparent lg:dark:bg-transparent">
                <div class="w-full max-w-md my-auto">
                    <!-- Logo and branding -->
                    <div class="mb-8">
                        <a href="{{ route('register') }}" class="inline-flex items-center text-sm text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 mb-4 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back to selection
                        </a>
                        <div class="flex items-center gap-3 mb-6">
                            <img class="h-10 w-auto" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo">
                            <span class="text-xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">All Academies</span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Onboard Your School
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">
                            Join thousands of schools already using our platform
                        </p>
                    </div>

                    <!-- Register form -->
                    <form method="POST" action="{{ route('register.store-school') }}" class="space-y-5">
                        @csrf

                        <!-- School Administrator Details Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">School Administrator Details</label>
                        </div>

                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">First name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="First name"
                                    value="{{ old('first_name') }}"
                                >
                            </div>
                            @error('first_name')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Last name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="Last name"
                                    value="{{ old('last_name') }}"
                                >
                            </div>
                            @error('last_name')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Other Names -->
                        <div>
                            <label for="other_names" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Other names <span class="text-gray-400 dark:text-gray-500 text-xs">(optional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input
                                    id="other_names"
                                    name="other_names"
                                    type="text"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="Middle name(s)"
                                    value="{{ old('other_names') }}"
                                >
                            </div>
                            @error('other_names')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Email address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="name@example.com"
                                    value="{{ old('email') }}"
                                >
                            </div>
                            @error('email')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- School Details Section -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">School Details</label>
                        </div>

                        <!-- School Name -->
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">School Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <input
                                    id="school_name"
                                    name="school_name"
                                    type="text"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="School name"
                                    value="{{ old('school_name') }}"
                                >
                            </div>
                            @error('school_name')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Country</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <select
                                    id="country"
                                    name="country"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                >
                                    <option value="">Select country</option>
                                </select>
                            </div>
                            @error('country')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Region -->
                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Region/State</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <select
                                    id="region"
                                    name="region"
                                    required
                                    disabled
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors disabled:bg-gray-100 dark:disabled:bg-gray-600"
                                >
                                    <option value="">Select region</option>
                                </select>
                            </div>
                            <div id="region-custom" class="mt-2 hidden">
                                <input
                                    id="region_manual"
                                    name="region_manual"
                                    type="text"
                                    placeholder="Enter your region/state"
                                    class="block w-full px-3 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400"
                                    value="{{ old('region_manual') }}"
                                >
                            </div>
                            @error('region')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">City</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <select
                                    id="city"
                                    name="city"
                                    required
                                    disabled
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors disabled:bg-gray-100 dark:disabled:bg-gray-600"
                                >
                                    <option value="">Select city</option>
                                </select>
                            </div>
                            <div id="city-custom" class="mt-2 hidden">
                                <input
                                    id="city_manual"
                                    name="city_manual"
                                    type="text"
                                    placeholder="Enter your city/town"
                                    class="block w-full px-3 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400"
                                    value="{{ old('city_manual') }}"
                                >
                            </div>
                            @error('city')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date of Birth <span class="text-gray-400 dark:text-gray-500 text-xs">(optional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input
                                    id="date_of_birth"
                                    name="date_of_birth"
                                    type="date"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    value="{{ old('date_of_birth') }}"
                                >
                            </div>
                            @error('date_of_birth')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Gender <span class="text-gray-400 dark:text-gray-500 text-xs">(optional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <select
                                    id="gender"
                                    name="gender"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                >
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            @error('gender')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Phone Number <span class="text-gray-400 dark:text-gray-500 text-xs">(optional)</span></label>
                            <div class="relative">
                                <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code') }}">
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="Enter phone number"
                                    value="{{ old('phone') }}"
                                >
                            </div>
                            @error('country_code')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                            @error('phone')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="Min. 8 characters"
                                    value="{{ old('password') }}"
                                >
                            </div>
                            @error('password')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                                    placeholder="Confirm your password"
                                    value="{{ old('password_confirmation') }}"
                                >
                            </div>
                            @error('password_confirmation')
                                <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Terms Checkbox -->
                        <div class="flex items-start mt-6">
                            <div class="flex items-center h-5">
                                <input
                                    id="terms"
                                    name="terms"
                                    type="checkbox"
                                    required
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-700 dark:text-gray-300">
                                    I agree to the
                                    <a href="{{route('branding.terms')}}" class="font-medium text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300">Terms of Service</a>
                                    and
                                    <a href="{{route('branding.privacy')}}" class="font-medium text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                        @error('terms')
                            <div class="mt-1 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 dark:from-green-600 dark:to-emerald-600 dark:hover:from-green-700 dark:hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-green-500 dark:focus:ring-green-400 transition-all duration-200"
                        >
                            <svg class="h-5 w-5 text-white group-hover:text-white opacity-75 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Continue with school onboarding
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-8 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300 dark:border-gray-600"/>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">Already have an account?</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sign in link -->
                    <div>
                        <a
                            href="{{ route('login') }}"
                            class="flex items-center justify-center w-full py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-green-300 dark:hover:border-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-green-500 dark:focus:ring-green-400 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign in to your account
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </section>

    <!-- Country/Region/City loading script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countrySelect = document.getElementById('country');
            const regionSelect = document.getElementById('region');
            const citySelect = document.getElementById('city');
            const regionCustom = document.getElementById('region-custom');
            const cityCustom = document.getElementById('city-custom');

            // Load countries on page load
            fetch('/api/countries')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(countries => {
                    console.log('Countries loaded:', countries);
                    
                    // Validate countries data
                    if (countries == null || typeof countries !== 'object') {
                        console.error('Invalid countries data:', countries);
                        countrySelect.innerHTML = '<option value="">Select country</option><option disabled>Unable to load countries</option>';
                        return;
                    }
                    
                    countrySelect.innerHTML = '<option value="">Select country</option>';
                    // countries is an object like { 'US': 'United States', 'GB': 'United Kingdom' }
                    
                    // Safely use Object.entries with validation
                    try {
                        const entries = Object.entries(countries);
                        if (entries.length === 0) {
                            console.warn('Empty countries data');
                            countrySelect.innerHTML += '<option disabled>No countries available</option>';
                            return;
                        }
                        
                        entries.forEach(([code, name]) => {
                            const option = new Option(name, code);
                            if (code === '{{ old('country') }}') option.selected = true;
                            countrySelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error processing countries:', error);
                        countrySelect.innerHTML += '<option disabled>Error loading countries</option>';
                        return;
                    }

                    // Load regions if country is pre-selected
                    if (countrySelect.value) {
                        loadRegions(countrySelect.value);
                    }
                })
                .catch(error => console.error('Error loading countries:', error));

            countrySelect.addEventListener('change', function () {
                if (this.value) {
                    loadRegions(this.value);
                } else {
                    regionSelect.innerHTML = '<option value="">Select region</option>';
                    regionSelect.disabled = true;
                    citySelect.innerHTML = '<option value="">Select city</option>';
                    citySelect.disabled = true;
                }
            });

            regionSelect.addEventListener('change', function () {
                if (this.value === 'other') {
                    regionCustom.classList.remove('hidden');
                    citySelect.disabled = true;
                } else {
                    regionCustom.classList.add('hidden');
                    if (this.value && countrySelect.value) {
                        loadCities(countrySelect.value, this.value);
                    }
                }
            });

            function loadRegions(countryCode) {
                fetch(`/api/regions?country=${countryCode}`)
                    .then(response => response.json())
                    .then(regions => {
                        regionSelect.innerHTML = '<option value="">Select region</option>';
                        regions.forEach(region => {
                            const option = new Option(region, region);
                            if (region === '{{ old('region') }}') option.selected = true;
                            regionSelect.appendChild(option);
                        });

                        const otherOption = new Option('Other', 'other');
                        regionSelect.appendChild(otherOption);
                        regionSelect.disabled = false;

                        if (regionSelect.value && regionSelect.value !== 'other') {
                            loadCities(countryCode, regionSelect.value);
                        }
                    })
                    .catch(error => console.error('Error loading regions:', error));
            }

            function loadCities(countryCode, regionCode) {
                fetch(`/api/cities?country=${countryCode}&region=${regionCode}`)
                    .then(response => response.json())
                    .then(cities => {
                        citySelect.innerHTML = '<option value="">Select city</option>';
                        cities.forEach(city => {
                            const option = new Option(city, city);
                            if (city === '{{ old('city') }}') option.selected = true;
                            citySelect.appendChild(option);
                        });

                        const otherOption = new Option('Other', 'other');
                        citySelect.appendChild(otherOption);
                        citySelect.disabled = false;

                        citySelect.addEventListener('change', function () {
                            const cityCustom = document.getElementById('city-custom');
                            if (this.value === 'other') {
                                cityCustom.classList.remove('hidden');
                            } else {
                                cityCustom.classList.add('hidden');
                            }
                        });
                    })
                    .catch(error => console.error('Error loading cities:', error));
            }
        });
    </script>
</x-app>
