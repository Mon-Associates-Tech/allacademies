@if(Auth::check())
<x-layouts.app background="bg-white dark:bg-gray-900">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="max-w-2xl m-auto mt-16">

            <div class="text-center px-4">
                <!-- Error Icon -->
                <div class="inline-flex mb-8">
                    <svg class="w-24 h-24 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 9v2m0 4v2m-6.773-11.423L5.005 5.005a2 2 0 0 1 2.828-2.828l.988.988a2 2 0 0 1 0 2.828l-.988.988a2 2 0 0 1-2.828 0zm13.942 13.942l.988.988a2 2 0 0 1-2.828 2.828l-.988-.988a2 2 0 0 1 0-2.828l.988-.988a2 2 0 0 1 2.828 0zM5.005 18.995a2 2 0 0 1-2.828 0l-.988-.988a2 2 0 0 1 0-2.828l.988-.988a2 2 0 0 1 2.828 0l.988.988a2 2 0 0 1 0 2.828zm13.942-13.942a2 2 0 0 1 2.828 0l.988.988a2 2 0 0 1 0 2.828l-.988.988a2 2 0 0 1-2.828 0l-.988-.988a2 2 0 0 1 0-2.828z" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                </div>

                <!-- Error Code -->
                <div class="mb-6">
                    <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-2">403</h1>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">Access Forbidden</p>
                    <p class="text-gray-600 dark:text-gray-400">You don't have permission to access this resource.</p>
                </div>

                <!-- Error Description -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-8 text-left">
                    <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-100 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.487 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Why am I seeing this?
                    </h3>
                    <ul class="space-y-2 text-sm text-amber-800 dark:text-amber-200 ml-7">
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>The resource you're trying to access belongs to another user</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>You may not have the necessary permissions or subscription</span>
                        </li>
                        <li class="flex items-start">
                            <span class="inline-flex items-center justify-center flex-shrink-0 h-4 w-4 mr-2 mt-0.5">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>Your session may have expired or your account access level may have changed</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg transition-colors shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V3" />
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="javascript:history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Go Back
                    </a>
                </div>

                <!-- Contact Support Link -->
                <p class="mt-8 text-sm text-gray-600 dark:text-gray-400">
                    Still having trouble? <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Contact support</a>
                </p>
            </div>

        </div>

    </div>
</x-layouts.app>
@else
<x-layouts.guest background="bg-white dark:bg-gray-900">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="max-w-2xl m-auto mt-16">

            <div class="text-center px-4">
                <!-- Error Icon -->
                <div class="inline-flex mb-8">
                    <svg class="w-24 h-24 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 9v2m0 4v2m-6.773-11.423L5.005 5.005a2 2 0 0 1 2.828-2.828l.988.988a2 2 0 0 1 0 2.828l-.988.988a2 2 0 0 1-2.828 0zm13.942 13.942l.988.988a2 2 0 0 1-2.828 2.828l-.988-.988a2 2 0 0 1 0-2.828l.988-.988a2 2 0 0 1 2.828 0zM5.005 18.995a2 2 0 0 1-2.828 0l-.988-.988a2 2 0 0 1 0-2.828l.988-.988a2 2 0 0 1 2.828 0l.988.988a2 2 0 0 1 0 2.828zm13.942-13.942a2 2 0 0 1 2.828 0l.988.988a2 2 0 0 1 0 2.828l-.988.988a2 2 0 0 1-2.828 0l-.988-.988a2 2 0 0 1 0-2.828z" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                </div>

                <!-- Error Code -->
                <div class="mb-6">
                    <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-2">403</h1>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">Access Forbidden</p>
                    <p class="text-gray-600 dark:text-gray-400">You don't have permission to access this resource.</p>
                </div>

                <!-- Error Description -->
                <p class="text-gray-600 dark:text-gray-400 mb-8">
                    Please log in to your account or contact support for assistance.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg transition-colors shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V3" />
                        </svg>
                        Go Home
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-layouts.guest>
@endif
