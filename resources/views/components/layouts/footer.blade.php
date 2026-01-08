<footer class="relative bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 text-white overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div
            class="absolute top-0 left-0 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse animation-delay-2000"></div>
    </div>

    <!-- Newsletter CTA Section -->
    <div class="relative border-b border-white/10 bg-gradient-to-r from-blue-600/20 to-purple-600/20 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center max-w-3xl mx-auto">
                <h3 class="text-2xl md:text-3xl font-bold mb-4">
                    Stay Updated with
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">
                        All Academies
                    </span>
                </h3>
                <p class="text-lg text-gray-300 mb-8">
                    Get the latest updates on new features, educational resources, and exclusive content delivered to
                    your inbox.
                </p>

                <!-- Enhanced Newsletter Form -->

                <form id="newsletter-form">
                    <div class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                        <div class="flex-1">
                            <input type="email"
                                   name="email"
                                   id="newsletter-email"
                                   placeholder="Enter your email address"
                                   class="w-full px-6 py-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-300"
                                   required>
                        </div>
                        <button type="submit"
                                class="px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 font-semibold rounded-full hover:from-yellow-300 hover:to-orange-400 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            Subscribe
                            <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                    </div>
                </form>


                <!-- Trust Indicators -->
                <div class="flex items-center justify-center space-x-6 mt-6 text-sm text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        No spam, ever
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Unsubscribe anytime
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        5,000+ subscribers
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-12">

            <!-- Brand Section -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo" class="w-8 h-8">
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ config('app.name') }}</h2>
                        <p class="text-sm text-gray-400">Educational Excellence</p>
                    </div>
                </div>

                <p class="text-gray-300 leading-relaxed max-w-md">
                    Empowering education through innovative digital learning solutions. Join thousands of students,
                    teachers, and institutions in transforming the way we learn and teach.
                </p>

                <!-- Key Stats -->
                <div class="grid grid-cols-3 gap-4 pt-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400">5K+</div>
                        <div class="text-sm text-gray-400">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400">500+</div>
                        <div class="text-sm text-gray-400">Institutions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400">98%</div>
                        <div class="text-sm text-gray-400">Satisfaction</div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="flex space-x-4 pt-6">
                    <a href="#"
                       class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor"
                             viewBox="0 0 24 24">
                            <path
                                d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#"
                       class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor"
                             viewBox="0 0 24 24">
                            <path
                                d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#"
                       class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor"
                             viewBox="0 0 24 24">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#"
                       class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor"
                             viewBox="0 0 24 24">
                            <path
                                d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.719-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.098.119.112.223.083.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z.017-.001z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Quick Links
                </h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('branding.features') }}"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Features & Modules
                        </a></li>
                    <li><a href="{{ route('sign-up') }}"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Get Started Free
                        </a></li>
                    <li><a href="{{ route('dashboard') }}"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Dashboard
                        </a></li>
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Pricing Plans
                        </a></li>
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Success Stories
                        </a></li>

                    <li>
                        <x-link.primary type="button" class="ml-4" to="{{ route('payments.public.lookup') }}">
                            <span>Make Payment</span>
                        </x-link.primary>
                    </li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Resources
                </h3>
                <ul class="space-y-3">
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Help Center
                        </a></li>
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Documentation
                        </a></li>
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            API Reference
                        </a></li>
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Community Forum
                        </a></li>
                    <li><a href="#"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Status Page
                        </a></li>
                </ul>
            </div>

            <!-- Sponsorship -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sponsorship
                </h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('sponsorship.programs.index') }}"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Browse Programs
                        </a></li>
                    <li><a href="{{ route('sponsorship.offers.index') }}"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Sponsor Offers
                        </a></li>
                    <li><a href="{{ route('financial-aid') }}"
                           class="text-gray-300 hover:text-yellow-400 transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Financial Aid
                        </a></li>
                </ul>
            </div>

            <!-- Support & Contact -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 11-9.75 9.75A9.75 9.75 0 0112 2.25z"/>
                    </svg>
                    Support
                </h3>

                <!-- Contact Methods -->
                <div class="space-y-4">
                    <a href="mailto:{{ config('company.email') }}"
                       class="flex items-center text-gray-300 hover:text-yellow-400 transition-colors duration-200 group">
                        <div
                            class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg p-3 flex items-center justify-center mr-3 group-hover:bg-yellow-400/20 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium">Email Support</div>
                            <div class="text-sm text-gray-400">{{ config('company.email') }}</div>
                        </div>
                    </a>

                    <a href="tel:{{ config('company.phone') }}"
                       class="flex items-center text-gray-300 hover:text-yellow-400 transition-colors duration-200 group">
                        <div
                            class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-400/20 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium">Phone Support</div>
                            <div class="text-sm text-gray-400">{{ config('company.phone') }}</div>
                        </div>
                    </a>
                </div>

                <!-- Support Hours -->
                <div class="pt-4 border-t border-white/10">
                    <h4 class="font-medium text-white mb-2">Support Hours</h4>
                    <div class="text-sm text-gray-400 space-y-1">
                        <div>Mon-Fri: 9:00 AM - 6:00 PM</div>
                        <div>Weekend: 10:00 AM - 4:00 PM</div>
                        <div class="text-yellow-400">24/7 Emergency Support</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="relative border-t border-white/10 bg-black/20 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">

                <!-- Copyright -->
                <div class="text-center md:text-left">
                    <p class="text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Transforming education, one innovation at a time.
                    </p>
                </div>

                <!-- Legal Links -->
                <div class="flex flex-wrap items-center justify-center space-x-6 text-sm">
                    <a href="{{ route('branding.privacy') }}"
                       class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                        Privacy Policy
                    </a>
                    <a href="{{ route('branding.terms') }}"
                       class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                        Terms of Service
                    </a>
                    <a href="{{ route('branding.contact') }}"
                       class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                        Cookie Policy
                    </a>
                    <a href="{{ route('branding.contact') }}"
                       class="text-gray-400 hover:text-yellow-400 transition-colors duration-200">
                        Security
                    </a>
                </div>

                <!-- Back to Top -->
                <button onclick="scrollToTop()"
                        class="w-10 h-10 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 rounded-full flex items-center justify-center hover:from-yellow-300 hover:to-orange-400 transform hover:scale-110 transition-all duration-300 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</footer>

<style>
    .animation-delay-2000 {
        animation-delay: 2s;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Enhanced focus styles for accessibility */
    footer a:focus,
    footer button:focus {
        outline: 2px solid #fbbf24;
        outline-offset: 2px;
        border-radius: 4px;
    }
</style>

<script>
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Newsletter subscription functionality
    document.addEventListener('DOMContentLoaded', function () {
        const newsletterForm = document.getElementById('newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const email = this.querySelector('#newsletter-email').value;
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;

                if (email) {
                    // Disable button and show loading
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Subscribing...';

                    axios.post('{{ route('newsletter.subscribe') }}', {
                        email: email,
                        _token: '{{ csrf_token() }}'
                    })
                        .then(function (response) {
                            if (response.data.success) {
                                alert('Thank you for subscribing! You\'ll hear from us soon.');
                                newsletterForm.reset();
                            } else {
                                alert(response.data.message || 'An error occurred. Please try again.');
                            }
                        })
                        .catch(function (error) {
                            if (error.response && error.response.data && error.response.data.message) {
                                alert(error.response.data.message);
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                            console.error('Newsletter subscription error:', error);
                        })
                        .finally(function () {
                            // Re-enable button
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        });
                }
            });
        }
    });
</script>
