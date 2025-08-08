<div class="space-y-6">
    <!-- Critical Alert - No Student Record -->
    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-lg shadow-sm p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <span class="text-3xl">🚨</span>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-medium text-red-800 dark:text-red-200 mb-2">
                    Student Account Setup Required
                </h3>
                <div class="text-red-700 dark:text-red-300 mb-4">
                    <p class="mb-2">
                        Your account is configured as a student, but your student profile hasn't been created yet.
                        This prevents you from accessing student features like assessments, books, and academic materials.
                    </p>
                    <p>
                        Please contact your administrator or IT support to complete your student account setup.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="window.location.reload()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:text-red-200 dark:hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh Page
                    </button>
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800 dark:border-red-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        View Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Limited Welcome Section -->
    <div class="bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 rounded-xl p-6 text-white shadow-lg">
        <div class="text-center">
            <h2 class="text-3xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-200 text-lg mb-4">We're setting up your student account</p>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 max-w-md mx-auto">
                <div class="flex items-center justify-center space-x-3 text-yellow-300">
                    <svg class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="text-lg font-medium">Account Setup in Progress</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">What you can do while waiting</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Profile Management -->
                <div class="flex items-start space-x-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Update Your Profile</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Make sure your profile information is complete and up-to-date.
                        </p>
                        <a href="{{ route('profile.edit') }}"
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700 transition-colors">
                            Go to Profile →
                        </a>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="flex items-start space-x-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.452l-3.17 3.17a.5.5 0 01-.849-.353V13.5A8.474 8.474 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Contact Support</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Need help with your account setup? Our support team is here to assist you.
                        </p>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Email:</strong> support@allacademies.com
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Phone:</strong> +1 (555) 123-4567
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your Account Information</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ auth()->user()->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(auth()->user()->role) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                    <dd class="mt-1 text-sm">
                        @if(auth()->user()->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                ✓ Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                ⏳ Pending
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Frequently Asked Questions</h3>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Why can't I access student features?</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Your account role is set to "student" but your student profile hasn't been created yet. This is typically done during the initial account setup process by an administrator.
                    </p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">How long does the setup take?</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Account setup usually takes 1-2 business days after your initial registration. If it's been longer, please contact support.
                    </p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">What information do I need to provide?</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Ensure your profile has complete information including your full name and verified email address. Additional information may be required by your school or institution.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
