<x-app>
<main class="font-sans antialiased bg-gray-50">
<!-- Application Logo -->
<div class="flex justify-center hidden pt-8 pb-4">
    <img src="{{ asset('/img/logo.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto">
</div>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Icon -->
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Your Identity
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                We've sent a 6-digit verification code to your email address.
                <br>
                <span class="font-medium text-gray-900">{{ session('2fa:user:email') ?? 'your registered email' }}</span>
            </p>
        </div>

        <!-- Verification Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-6" id="twoFactorForm">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Verification Code
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="code"
                            id="code"
                            maxlength="6"
                            required
                            autocomplete="one-time-code"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center tracking-widest font-mono text-lg @error('code') border-red-500 @enderror"
                            placeholder="000000"
                            value="{{ old('code') }}"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    @error('code')
                    <div class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Timer Display -->
                <div class="text-center">
                    <div id="timer" class="text-sm text-gray-600">
                        Code expires in: <span id="countdown" class="font-mono font-semibold text-blue-600">15:00</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        id="verifyButton"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        <span id="buttonText">Verify Code</span>
                        <span id="loadingSpinner" class="hidden ml-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                    </button>
                </div>

                <!-- Resend Code Button -->
                <div class="text-center">
                    <button
                        type="button"
                        id="resendButton"
                        class="text-sm text-blue-600 hover:text-blue-800 underline disabled:text-gray-400 disabled:no-underline disabled:cursor-not-allowed"
                    >
                        Resend Code
                    </button>
                    <div id="resendCooldown" class="hidden text-sm text-gray-500 mt-1">
                        Resend available in: <span id="resendTimer" class="font-mono"></span>s
                    </div>
                </div>
            </form>

            <!-- Back to Sign In -->
            <div class="mt-6 text-center">
                <a href="{{ route('sign-in') }}" class="text-sm text-gray-600 hover:text-gray-800">
                    ← Back to Sign In
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus the code input
        document.getElementById('code').focus();

        const button = document.getElementById('verifyButton');
        const buttonText = document.getElementById('buttonText');
        const spinner = document.getElementById('loadingSpinner');

        // Timer countdown for code expiration (15 minutes)
        let timeLeft = 15 * 60; // 15 minutes in seconds
        const countdownElement = document.getElementById('countdown');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                countdownElement.textContent = 'EXPIRED';
                countdownElement.className = 'font-mono font-semibold text-red-600';
                document.getElementById('verifyButton').disabled = true;
                return;
            }

            // Change color when less than 5 minutes left
            if (timeLeft <= 300) {
                countdownElement.className = 'font-mono font-semibold text-red-600';
            } else if (timeLeft <= 600) {
                countdownElement.className = 'font-mono font-semibold text-yellow-600';
            }

            timeLeft--;
        }

        // Update timer every second
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Initial call

        // Form submission handling
        document.getElementById('twoFactorForm').addEventListener('submit', function() {

            button.disabled = true;
            buttonText.textContent = 'Verifying...';
            spinner.classList.remove('hidden');
        });

        // Resend code functionality
        let resendCooldown = 0;
        const resendButton = document.getElementById('resendButton');
        const resendCooldownDiv = document.getElementById('resendCooldown');
        const resendTimerSpan = document.getElementById('resendTimer');

        function updateResendTimer() {
            if (resendCooldown > 0) {
                resendTimerSpan.textContent = resendCooldown;
                resendCooldown--;
            } else {
                resendButton.disabled = false;
                resendCooldownDiv.classList.add('hidden');
                clearInterval(resendInterval);
            }
        }

        let resendInterval;

        resendButton.addEventListener('click', function() {
            console.log('Resend code button clicked');
            fetch('{{ route("2fa.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert('New verification code sent to your email!');

                        // Start cooldown
                        resendCooldown = 60;
                        resendButton.disabled = true;
                        resendCooldownDiv.classList.remove('hidden');
                        resendInterval = setInterval(updateResendTimer, 1000);
                        updateResendTimer();

                        // Reset main timer
                        timeLeft = 15 * 60;
                        countdownElement.className = 'font-mono font-semibold text-blue-600';
                        document.getElementById('verifyButton').disabled = false;

                        // Clear and focus input
                        document.getElementById('code').value = '';
                        document.getElementById('code').focus();
                    } else {
                        alert(data.message || 'Failed to resend code. Please try again.');
                        if (data.remaining_seconds) {
                            resendCooldown = data.remaining_seconds;
                            resendButton.disabled = true;
                            resendCooldownDiv.classList.remove('hidden');
                            resendInterval = setInterval(updateResendTimer, 1000);
                            updateResendTimer();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to resend code. Please try again.');
                });
        });

        // Auto-submit when 6 digits are entered
        document.getElementById('code').addEventListener('input', function(e) {
            const value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            e.target.value = value;

            if (value.length === 6) {
                // Small delay to allow user to see the complete code
                setTimeout(() => {
                    document.getElementById('twoFactorForm').submit();
                    button.disabled = true;
                    buttonText.textContent = 'Wait! Verifying OTP ...'
                }, 500);
            }
        });
    });
</script>
</main>
</x-app>
