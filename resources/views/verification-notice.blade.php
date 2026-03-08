<x-center>
    <div class="max-w-md mx-auto space-y-6">
        <x-alert.success/>

        <!-- Header with Icon -->
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-blue-100 rounded-full p-4">
                    <svg class="w-12 h-12 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4.75 7.75C4.75 6.64543 5.64543 5.75 6.75 5.75H17.25C18.3546 5.75 19.25 6.64543 19.25 7.75V16.25C19.25 17.3546 18.3546 18.25 17.25 18.25H6.75C5.64543 18.25 4.75 17.3546 4.75 16.25V7.75Z"></path>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M5.5 6.5L12 12.25L18.5 6.5"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3">Verify Your Email</h1>
            <p class="text-gray-700 mb-2">We've sent a verification link to activate your account.</p>
            @if(isset($email) && $email)
                <p class="text-base font-medium text-gray-800 py-2 px-3 bg-gray-50 rounded-lg inline-block">{{ $email }}</p>
            @endif
            <p class="text-sm text-gray-600 mt-4">Click the link in the email to complete your registration and start using All Academies.</p>
        </div>

        <!-- Timeline/Steps Section -->
        <div class="bg-blue-50 border hidden border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 text-sm mb-3">What to do next:</h3>
            <ol class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">1</span>
                    <span>Look for an email from <strong>noreply@allacademies.com</strong></span>
                </li>
                <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">2</span>
                    <span>Click the "Verify Email Address" button in the email</span>
                </li>
                <li class="flex items-start">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">3</span>
                    <span>You'll be redirected to sign in to your account</span>
                </li>
            </ol>
        </div>

        <!-- Troubleshooting Card -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-600 mt-0.5" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900 text-sm mb-2">Didn't receive the email?</p>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li>✓ Check your spam or junk folder</li>
                        <li>✓ Verify the email address is correct: <strong>{{ $email ?? 'your email' }}</strong></li>
                        <li>✓ Wait a few moments (can take 1-2 minutes)</li>
                        <li>✓ Try resending a new verification link below</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Expiration Notice -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <p class="text-xs text-amber-800"><strong>Important:</strong> This verification link expires in 60 minutes. Please verify your email soon.</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                @if(isset($email) && $email && !auth()->check())
                    <input type="hidden" name="email" value="{{ $email }}">
                @endif
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  points="22,6 12,13 2,6"/>
                    </svg>
                    Resend Verification Email
                </button>
            </form>

            <div class="text-center">
                <a class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200"
                   href="{{ auth()->check() && auth()->user()->hasVerifiedEmail() ? route('dashboard') : route('login') }}">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    @if(auth()->check() && auth()->user()->hasVerifiedEmail())
                        Go to Dashboard
                    @else
                        Back to Sign In
                    @endif
                </a>
            </div>
        </div>

        <!-- Support Section -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-red-900 mb-1">Still having issues?</h3>
                    <p class="text-xs text-red-800 mb-2">
                        If you continue to have trouble receiving the verification email after multiple attempts, our support team is here to help.
                    </p>
                    <a href="mailto:support@allacademies.com"
                       class="text-xs text-red-900 hover:text-red-950 font-semibold underline inline-flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500">
                Email verification helps protect your account from unauthorized access.
            </p>
        </div>
    </div>

    <!-- Auto-refresh notice for better UX -->
    <script>
        // Optional: Auto-check if user gets verified (useful if they verify in another tab)
        document.addEventListener('DOMContentLoaded', function () {
            let checkCount = 0;
            const maxChecks = 12; // Check for 2 minutes (12 * 10 seconds)

            const checkVerification = setInterval(function () {
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
