<x-app>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-12 w-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>

            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                Account Suspended
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Your account has been suspended by an administrator.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-lg ring-1 ring-gray-900/5 dark:ring-gray-700/50 sm:rounded-lg sm:px-10">
                <!-- Suspension Details -->
                <div class="space-y-6">
                    @if(session('suspension_reason'))
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Suspension</h3>
                            <div class="mt-2 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-sm text-red-700 dark:text-red-300">{{ session('suspension_reason') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(session('suspended_at'))
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Suspended On</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if(session('suspended_at') instanceof \Carbon\Carbon)
                                    {{ session('suspended_at')->format('F j, Y \a\t g:i A') }}
                                @else
                                    {{ session('suspended_at') }}
                                @endif
                            </p>
                        </div>
                    @endif

                    @if(session('suspended_by'))
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Suspended By</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ session('suspended_by') }}</p>
                        </div>
                    @endif

                    <!-- Information Box -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-200">
                                    If you believe this suspension was made in error, please contact the administrator or support team for assistance.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-3">
                        <a href="{{ route('home') }}"
                           class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                            Return to Home
                        </a>
                        <a href="{{ route('login') }}"
                           class="w-full flex justify-center py-2.5 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                            Try Login Again
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Need help?
                <a href="{{ route('branding.contact') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Contact Support
                </a>
            </p>
        </div>
    </div>
</x-app>
