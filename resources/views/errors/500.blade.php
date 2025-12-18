@if(Auth::check())
<x-layouts.app background="bg-white dark:bg-gray-900">
    <div class="px-2 sm:px-4 lg:px-6 py-2 w-full  max-w-4xl mx-auto">
        <div class="max-w-xl m-auto">
            <div class="text-center px-2">
                <!-- Error Icon/Illustration -->
                <div class="inline-flex mb-2">
                    <svg class="w-20 h-20 text-red-500 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                    </svg>
                </div>

                <!-- Error Code -->
                <div class="mb-2">
                    <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-1">500</h1>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1">Oops! Server Error</p>
                </div>

                <!-- Error Description -->
                <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm leading-tight">
                    Something went wrong on our end. Our team has been notified and we're working to fix it.
                </p>

                <!-- Helpful Tips -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-4 mb-4 text-left">
                    <h3 class="text-xs font-semibold text-blue-900 dark:text-blue-100 mb-2">What you can try:</h3>
                    <ul class="space-y-1 text-xs text-blue-800 dark:text-blue-200">
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Refresh the page and try again
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Clear your browser cache
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Try again in a few minutes
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 justify-center">
                    <!-- Go Back Button -->
                    <button onclick="history.back()" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded font-medium text-xs text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Go Back
                    </button>

                    <!-- Dashboard Button -->
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded font-medium transition-colors duration-200 shadow hover:shadow-md">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4" />
                        </svg>
                        Dashboard
                    </a>
                </div>

                <!-- Support Note -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Still having issues? 
                        <a href="#" class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

@elseif(!Auth::check())
<x-layouts.guest background="bg-white dark:bg-gray-900">
    <div class="px-2 sm:px-4 lg:px-6 py-2 w-full max-w-4xl mx-auto">
        <div class="max-w-xl m-auto mt-8">
            <div class="text-center px-2">
                <!-- Error Icon/Illustration -->
                <div class="inline-flex mb-2">
                    <svg class="w-24 h-24 text-red-500 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                    </svg>
                </div>

                <!-- Error Code -->
                <div class="mb-2">
                    <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-1">500</h1>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1">Oops! Server Error</p>
                </div>

                <!-- Error Description -->
                <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm leading-relaxed">
                    Something went wrong on our end. Our team has been notified and we're working to fix it.
                </p>

                <!-- Helpful Tips -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-4 mb-4 text-left">
                    <h3 class="text-xs font-semibold text-blue-900 dark:text-blue-100 mb-2">What you can try:</h3>
                    <ul class="space-y-1 text-xs text-blue-800 dark:text-blue-200">
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Refresh the page and try again
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Clear your browser cache
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Try again in a few minutes
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 justify-center">
                    <!-- Go Back Button -->
                    <button onclick="history.back()" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded font-medium text-xs text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Go Back
                    </button>

                    <!-- Home Button -->
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded font-medium transition-colors duration-200 shadow hover:shadow-md">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4" />
                        </svg>
                        Home
                    </a>
                </div>

                <!-- Support Note -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Still having issues? 
                        <a href="#" class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
@endif
