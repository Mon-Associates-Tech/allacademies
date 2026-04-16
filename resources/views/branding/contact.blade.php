<x-layouts.guest pageName="Contact Us">
    <div class="relative min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-950 dark:via-gray-900 dark:to-slate-900">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-30 dark:opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
                <div class="absolute top-40 right-20 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
            </div>
        </div>

    <!-- Content -->
    <div class="relative pt-20 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-20" data-aos="fade-down">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-semibold mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Us
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 dark:text-white mb-6 tracking-tight">
                    Let's Start a
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">Conversation</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Have questions about All Academies? Our team is here to help you transform education.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
                <!-- Contact Info Sidebar -->
                <div class="lg:col-span-2 space-y-6" data-aos="fade-right">
                    <!-- Main Contact Card -->
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8 hover:shadow-indigo-500/10 transition-all duration-500">
                        <div class="flex items-center space-x-3 mb-8">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Get in Touch</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Email -->
                            <a href="mailto:{{config('company.email')}}" class="group flex items-start space-x-4 p-5 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 border border-blue-200/50 dark:border-blue-800/50 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email Us</h3>
                                    <p class="text-gray-900 dark:text-white font-semibold text-lg break-all">{{config('company.email')}}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">We'll respond within 24 hours</p>
                                </div>
                            </a>

                            <!-- Phone -->
                            <a href="tel:{{config('company.phone')}}" class="group flex items-start space-x-4 p-5 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 hover:from-green-100 hover:to-emerald-100 dark:hover:from-green-900/30 dark:hover:to-emerald-900/30 border border-green-200/50 dark:border-green-800/50 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Call Us</h3>
                                    <p class="text-gray-900 dark:text-white font-semibold text-lg">{{config('company.phone')}}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mon-Fri, 9AM-6PM GMT</p>
                                </div>
                            </a>

                            <!-- Office -->
                            <div class="group flex items-start space-x-4 p-5 rounded-2xl bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 border border-orange-200/50 dark:border-orange-800/50 transition-all duration-300">
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Visit Us</h3>
                                    <p class="text-gray-900 dark:text-white font-semibold leading-relaxed">{{config('company.address')}}</p>
                                </div>
                            </div>

                            <!-- Hours -->
                            <div class="group flex items-start space-x-4 p-5 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-200/50 dark:border-purple-800/50 transition-all duration-300">
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Business Hours</h3>
                                    <p class="text-gray-900 dark:text-white font-semibold">Monday - Friday</p>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">9:00 AM - 6:00 PM GMT</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Connect With Us</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="#" class="group relative w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl flex items-center justify-center text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-110 overflow-hidden">
                                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                    <svg class="w-5 h-5 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="#" class="group relative w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 rounded-xl flex items-center justify-center text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-110 overflow-hidden">
                                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                    <svg class="w-5 h-5 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                                <a href="#" class="group relative w-12 h-12 bg-gradient-to-br from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 rounded-xl flex items-center justify-center text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-110 overflow-hidden">
                                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                    <svg class="w-5 h-5 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                                <a href="#" class="group relative w-12 h-12 bg-gradient-to-br from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 rounded-xl flex items-center justify-center text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-110 overflow-hidden">
                                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                    <svg class="w-5 h-5 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-3" data-aos="fade-left">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8 lg:p-12 hover:shadow-indigo-500/10 transition-all duration-500">
                        <form
                            action="{{ route('contact.submit') }}"
                            method="POST"
                            x-data="contactForm()"
                            @submit.prevent="submitForm()"
                            class="space-y-8"
                        >
                            @csrf

                            <!-- Form Header -->
                            <div class="text-center pb-8 border-b border-gray-200 dark:border-gray-700">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mb-3">Send us a Message</h2>
                                <p class="text-gray-600 dark:text-gray-400 text-lg">Fill out the form below and we'll get back to you within 24 hours</p>
                            </div>

                            <!-- Name Fields -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="first_name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <input
                                            type="text"
                                            id="first_name"
                                            name="first_name"
                                            x-model="form.first_name"
                                            required
                                            class="block w-full px-5 py-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/50 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500 group-hover:border-gray-300 dark:group-hover:border-gray-600"
                                            placeholder="John"
                                        >
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                            <svg x-show="form.first_name" x-transition class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="last_name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <input
                                            type="text"
                                            id="last_name"
                                            name="last_name"
                                            x-model="form.last_name"
                                            required
                                            class="block w-full px-5 py-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/50 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500 group-hover:border-gray-300 dark:group-hover:border-gray-600"
                                            placeholder="Doe"
                                        >
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                            <svg x-show="form.last_name" x-transition class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        x-model="form.email"
                                        required
                                        class="block w-full pl-12 pr-12 py-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/50 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500 group-hover:border-gray-300 dark:group-hover:border-gray-600"
                                        placeholder="john.doe@example.com"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg x-show="isValidEmail(form.email)" x-transition class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="space-y-2">
                                <label for="subject" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <select
                                        id="subject"
                                        name="subject"
                                        x-model="form.subject"
                                        required
                                        class="block w-full px-5 py-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/50 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 group-hover:border-gray-300 dark:group-hover:border-gray-600 appearance-none cursor-pointer"
                                    >
                                        <option value="">Select a subject</option>
                                        <option value="general">General Inquiry</option>
                                        <option value="support">Technical Support</option>
                                        <option value="billing">Billing Question</option>
                                        <option value="demo">Request a Demo</option>
                                        <option value="feedback">Feedback & Suggestions</option>
                                        <option value="partnership">Partnership Opportunity</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="space-y-2">
                                <label for="message" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows="6"
                                        x-model="form.message"
                                        required
                                        maxlength="500"
                                        class="block w-full px-5 py-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/50 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500 resize-none group-hover:border-gray-300 dark:group-hover:border-gray-600"
                                        placeholder="Tell us how we can help you transform education..."
                                    ></textarea>
                                    <div class="absolute bottom-4 right-4 flex items-center space-x-2">
                                        <span
                                            x-text="form.message.length + '/500'"
                                            :class="form.message.length > 450 ? 'text-orange-500' : form.message.length === 500 ? 'text-red-500' : 'text-gray-400'"
                                            class="text-sm font-medium"
                                        ></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Newsletter -->
                            <div class="flex items-start space-x-3 p-5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border-2 border-indigo-100 dark:border-indigo-900/30">
                                <input
                                    type="checkbox"
                                    id="newsletter"
                                    name="newsletter"
                                    x-model.boolean="form.newsletter"
                                    class="mt-1 w-5 h-5 text-indigo-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 focus:ring-2 cursor-pointer"
                                >
                                <label for="newsletter" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <span class="font-semibold">Subscribe to our newsletter</span>
                                    <span class="block text-gray-600 dark:text-gray-400 mt-1">Get updates about new features, educational content, and platform improvements.</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-2xl hover:shadow-indigo-500/30 hover:scale-[1.02] active:scale-[0.98]'"
                                    class="w-full bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold py-5 px-8 rounded-xl transition-all duration-300 transform focus:outline-none focus:ring-4 focus:ring-indigo-500/50 relative overflow-hidden group"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                                    <span x-show="!loading" class="flex items-center justify-center space-x-3 relative z-10">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        <span class="text-lg">Send Message</span>
                                    </span>
                                    <span x-show="loading" class="flex items-center justify-center space-x-3 relative z-10">
                                        <svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-lg">Sending...</span>
                                    </span>
                                </button>
                            </div>

                            <!-- Success/Error Messages -->
                            <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-800 rounded-2xl shadow-lg">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-green-900 dark:text-green-100 mb-1">Message Sent Successfully!</h4>
                                        <p class="text-green-700 dark:text-green-200">Thank you for reaching out. We'll get back to you within 24 hours.</p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="showError" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="p-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-2 border-red-200 dark:border-red-800 rounded-2xl shadow-lg">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-red-900 dark:text-red-100 mb-1">Oops! Something went wrong</h4>
                                        <p class="text-red-700 dark:text-red-200" x-text="errorMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('head')
        <script>
            function contactForm() {
                return {
                    form: {
                        first_name: '',
                        last_name: '',
                        email: '',
                        subject: '',
                        message: '',
                        newsletter: false
                    },
                    loading: false,
                    showSuccess: false,
                    showError: false,
                    errorMessage: '',

                    isValidEmail(email) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return email && emailRegex.test(email);
                    },

                    async submitForm() {
                        this.loading = true;
                        this.showSuccess = false;
                        this.showError = false;

                        try {
                            const formData = new FormData();
                            Object.keys(this.form).forEach(key => {
                                formData.append(key, this.form[key]);
                            });
                            formData.append('_token', document.querySelector('[name="_token"]').value);

                            const response = await window.axios.post('{{ route("contact.submit") }}', formData);

                            const data = await response.data;

                            if (response.status === 200) {
                                this.showSuccess = true;
                                this.resetForm();
                            } else {
                                this.showError = true;
                                this.errorMessage = data.message || 'An error occurred while sending your message.';
                            }
                        } catch (error) {
                            this.showError = true;
                            this.errorMessage = 'An error occurred while sending your message.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    resetForm() {
                        this.form = {
                            first_name: '',
                            last_name: '',
                            email: '',
                            subject: '',
                            message: '',
                            newsletter: false
                        };
                    }
                }
            }
        </script>
    @endpush
</x-layouts.guest>
