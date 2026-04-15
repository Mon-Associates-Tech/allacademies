<section
    class="py-16 sm:py-20 lg:py-24 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <div class="mb-8 sm:mb-12 lg:mb-0" data-aos="fade-right" data-aos-delay="100">
                <div
                    class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 font-semibold text-xs sm:text-sm mb-4 sm:mb-6" data-aos="fade-down" data-aos-delay="200">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    support
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold sm:font-extrabold text-gray-900 dark:text-white lg:text-4xl mb-4 sm:mb-6" data-aos="fade-up" data-aos-delay="300">
                    Help Educate a <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-purple-600"> Needy Child</span>
                </h2>
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 dark:text-gray-300 mb-6 sm:mb-8 leading-relaxed" data-aos="fade-up" data-aos-delay="400">
                    Give a child the opportunity to learn. Your support can help provide the education
                    their family cannot provide
                </p>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4" data-aos="zoom-in" data-aos-delay="500">
                    <a href="{{ route('public.financial-aid') }}"
                       class="inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-4 text-base sm:text-lg font-bold rounded-xl text-white bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Make a Contribution
                    </a>
                </div>

                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-start sm:items-center sm:space-x-4 space-y-2 sm:space-y-0 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mr-1.5 sm:mr-2" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Secure Transaction
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mr-1.5 sm:mr-2" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Instant Receipt
                    </div>
                </div>
            </div>

            <div class="relative" data-aos="fade-left" data-aos-delay="100">
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
</section>
