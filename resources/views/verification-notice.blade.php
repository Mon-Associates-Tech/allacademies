<x-center>
    <div class="max-w-md mx-auto space-y-6">
        <x-alert.success />

        <!-- Header with Icon -->
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-primary-100 rounded-full p-4">
                    <svg class="w-12 h-12 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 7.75C4.75 6.64543 5.64543 5.75 6.75 5.75H17.25C18.3546 5.75 19.25 6.64543 19.25 7.75V16.25C19.25 17.3546 18.3546 18.25 17.25 18.25H6.75C5.64543 18.25 4.75 17.3546 4.75 16.25V7.75Z"></path>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.5 6.5L12 12.25L18.5 6.5"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Check your email</h1>
            <p class="text-gray-600 mb-1">We've sent a verification link to your email address.</p>
            @if(isset($email) && $email)
                <p class="text-sm font-medium text-gray-700 mb-2">{{ $email }}</p>
            @endif
            <p class="text-sm text-gray-500">Click the link in the email to verify your account and start using All Academies.</p>
        </div>

        <!-- Email Status Card -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 text-sm">
                    <p class="font-medium text-gray-900">Didn't receive the email?</p>
                    <ul class="mt-2 text-gray-600 space-y-1">
                        <li>• Check your spam or junk folder</li>
                        <li>• Make sure the email address is correct</li>
                        <li>• Wait a few minutes and try again</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                @if(isset($email) && $email && !auth()->check())
                    <input type="hidden" name="email" value="{{ $email }}">
                @endif
                <x-button.primary class="w-full justify-center" type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="22,6 12,13 2,6"/>
                    </svg>
                    Resend verification email
                </x-button.primary>
            </form>

            <div class="text-center">
                <a class="inline-flex items-center text-primary-600 text-sm hover:text-primary-800 transition-colors duration-200"
                   href="{{ auth()->check() && auth()->user()->hasVerifiedEmail() ? route('dashboard') : route('sign-in') }}">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @if(auth()->check() && auth()->user()->hasVerifiedEmail())
                        Go to Dashboard
                    @else
                        Already verified? Sign in
                    @endif
                </a>
            </div>
        </div>

        <!-- Additional Help -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">Still having trouble?</h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        If you continue to have issues receiving the verification email, please contact our support team for assistance.
                    </p>
                    <div class="mt-2">
                        <a href="mailto:support@allacademies.com" class="text-sm text-yellow-800 hover:text-yellow-900 font-medium underline">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500">
                This verification helps keep your account secure.
            </p>
        </div>
    </div>

    <!-- Auto-refresh notice for better UX -->
    <script>
        // Optional: Auto-check if user gets verified (useful if they verify in another tab)
        document.addEventListener('DOMContentLoaded', function() {
            let checkCount = 0;
            const maxChecks = 12; // Check for 2 minutes (12 * 10 seconds)

            const checkVerification = setInterval(function() {
                checkCount++;

                // Stop checking after maxChecks
                if (checkCount >= maxChecks) {
                    clearInterval(checkVerification);
                    return;
                }

                // Check if user is verified by making a simple request
                fetch('/ping', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                }).then(response => {
                    // If the request succeeds and user is authenticated and verified,
                    // we could redirect them. For now, we'll just continue.
                }).catch(() => {
                    // Ignore errors, user is likely not verified yet
                });
            }, 10000); // Check every 10 seconds
        });
    </script>
</x-center>
