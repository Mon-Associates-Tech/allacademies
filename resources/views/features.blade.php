<x-app>
    <div class="bg-white dark:bg-gray-900 transition-colors duration-300 min-h-screen">
        @include('branding.partials.header')
        
        <div class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl mb-6">
                        Everything You Need to <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-600">Excel</span>
                    </h1>
                    <p class="max-w-3xl text-xl text-gray-600 dark:text-gray-300 mx-auto leading-relaxed">
                        Our comprehensive platform combines cutting-edge technology with educational expertise to deliver an unparalleled learning experience.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Digital Library -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg mb-6">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Digital Library</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Access over 15,000 educational resources spanning multiple disciplines with AI-powered search and offline access.</p>
                    </div>

                    <!-- AI-Powered Learning -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg mb-6">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">AI-Powered Learning</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Get personalized learning experiences with AI tutoring, automated assessments, and intelligent content recommendations.</p>
                    </div>

                    <!-- Interactive Assessments -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg mb-6">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Interactive Assessments</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Create and take quizzes, examinations, and assignments with automated grading and detailed performance analytics.</p>
                    </div>

                    <!-- Virtual Classroom -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg mb-6">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Virtual Classroom</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Host live sessions, record classes, and engage with students in real-time through integrated video conferencing.</p>
                    </div>

                    <!-- Progress Tracking -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg mb-6">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Progress Tracking</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Monitor student performance with detailed analytics, reports, and insights to identify areas for improvement.</p>
                    </div>

                    <!-- Collaboration Tools -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg mb-6">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Collaboration Tools</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Share notes, participate in forums, send messages, and collaborate with peers and teachers seamlessly.</p>
                    </div>
                </div>

                <div class="mt-16 text-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        Get Started Today
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app>
