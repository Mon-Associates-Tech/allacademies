<div
    class="py-24 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <div class="mb-12 lg:mb-0">
                <div
                    class="inline-flex items-center px-4 py-2 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 font-semibold text-sm mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    support
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl mb-6">
                    Help Educate a <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-purple-600"> Needy Child</span>
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Give a child the opportunity to learn. Your support can help provide the education
                    their family cannot provide
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('public.financial-aid') }}"
                       class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Make a Contribution
                    </a>
                </div>

                <div class="mt-8 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Secure Transaction
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Instant Receipt
                    </div>
                </div>
            </div>

            <div class="relative">
                <!-- Decorative blobs -->
                <div
                    class="absolute -top-4 -right-4 w-72 h-72 bg-purple-300 dark:bg-purple-900/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div
                    class="absolute -bottom-4 -left-4 w-72 h-72 bg-violet-300 dark:bg-violet-900/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>

                <div
                    class="relative rounded-2xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700 transform rotate-2 hover:rotate-0 transition-all duration-500">
                    <img
                        src="{{ asset('images/school_children_in_class.jpg') }}?crop&w=800&q=80"
                        alt="Student paying fees"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</div>
