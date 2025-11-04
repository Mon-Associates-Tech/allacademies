<x-app>
    <main class="font-sans antialiased bg-gradient-to-br from-slate-100 via-gray-100 to-slate-200 dark:from-gray-900 dark:via-slate-900 dark:to-gray-950">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Cg fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\'%3E%3Cpath d=\'M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

        <!-- Application Logo -->
        <div class="flex justify-center hidden pt-8 pb-4">
            <img src="{{ asset('/img/logo.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto">
        </div>

        <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Icon with animated rings -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-slate-200/40 dark:bg-slate-800/40 animate-ping opacity-20"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-20 h-20 rounded-full bg-slate-300/30 dark:bg-slate-700/30 animate-pulse"></div>
                    </div>
                    <div class="relative mx-auto h-16 w-16 flex items-center justify-center rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 dark:from-slate-600 dark:to-slate-800 shadow-xl shadow-slate-500/20">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <h2 class="mt-8 text-center text-3xl font-bold tracking-tight bg-gradient-to-br from-slate-800 to-slate-600 dark:from-slate-200 dark:to-slate-400 bg-clip-text text-transparent">
                        Verify Your Identity
                    </h2>
                    <p class="mt-3 text-center text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        We've sent a 6-digit verification code to your email address.
                        <br>
                        <span class="font-semibold text-slate-800 dark:text-slate-300 inline-block mt-1.5">{{ session('2fa:user:email') ?? 'your registered email' }}</span>
                    </p>
                </div>

                <!-- Verification Form with glassmorphism -->
                <div class="relative backdrop-blur-xl bg-white/80 dark:bg-slate-800/80 rounded-3xl shadow-2xl shadow-slate-400/10 dark:shadow-slate-900/50 p-8 border border-slate-200/50 dark:border-slate-700/50">
                    <!-- Decorative gradient corner -->
                    <div class="absolute -top-1 -right-1 w-32 h-32 bg-gradient-to-br from-slate-400/20 to-transparent dark:from-slate-600/20 rounded-3xl blur-2xl"></div>
                    <div class="absolute -bottom-1 -left-1 w-32 h-32 bg-gradient-to-tr from-slate-400/20 to-transparent dark:from-slate-600/20 rounded-3xl blur-2xl"></div>

                    <form method="POST" action="{{ route('2fa.verify') }}" class="relative space-y-6" id="twoFactorForm">
                        @csrf
                        <div>
                            <label for="code" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                                Verification Code
                            </label>
                            <div class="relative group">
                                <input
                                    type="text"
                                    name="code"
                                    id="code"
                                    maxlength="6"
                                    required
                                    autocomplete="one-time-code"
                                    inputmode="numeric"
                                    pattern="[0-9]{6}"
                                    class="appearance-none relative block w-full px-4 py-4 border-2 border-slate-300 dark:border-slate-600 placeholder-slate-400 dark:placeholder-slate-500 text-slate-900 dark:text-slate-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-slate-400/20 dark:focus:ring-slate-500/30 focus:border-slate-500 dark:focus:border-slate-400 bg-white dark:bg-slate-900/50 text-center tracking-[0.5em] font-mono text-xl font-bold transition-all duration-300 @error('code') border-red-400 dark:border-red-500 @enderror hover:border-slate-400 dark:hover:border-slate-500"
                                    placeholder="000000"
                                    value="{{ old('code') }}"
                                >
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none opacity-40 group-focus-within:opacity-70 transition-opacity">
                                    <svg class="h-5 w-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('code')
                            <div class="mt-3 text-sm text-red-600 dark:text-red-400 flex items-center bg-red-50 dark:bg-red-900/20 p-3 rounded-xl border border-red-200 dark:border-red-800/50">
                                <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Timer Display with progress bar -->
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700/50">
                                <svg class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span id="timer" class="text-sm text-slate-600 dark:text-slate-400">
                                Code expires in: <span id="countdown" class="font-mono font-bold text-slate-800 dark:text-slate-300 ml-1">15:00</span>
                            </span>
                            </div>
                        </div>

                        <!-- Submit Button with enhanced states -->
                        <div>
                            <button
                                type="submit"
                                id="verifyButton"
                                class="group relative w-full flex justify-center items-center py-4 px-6 border-2 border-transparent text-sm font-semibold rounded-2xl text-white bg-gradient-to-r from-slate-700 to-slate-900 dark:from-slate-600 dark:to-slate-800 hover:from-slate-800 hover:to-slate-950 dark:hover:from-slate-500 dark:hover:to-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-400/30 dark:focus:ring-slate-500/30 disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-300 shadow-lg shadow-slate-500/20 hover:shadow-xl hover:shadow-slate-600/30 hover:-translate-y-0.5 disabled:hover:translate-y-0 disabled:hover:shadow-lg"
                            >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                                <svg class="h-5 w-5 text-slate-400 group-hover:text-slate-300 transition-colors" fill="currentColor" viewBox="0 0 20 20">
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

                        <!-- Resend Code Section -->
                        <div class="pt-2">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                                </div>
                                <div class="relative flex justify-center text-xs uppercase">
                                    <span class="bg-white dark:bg-slate-800/80 px-3 text-slate-500 dark:text-slate-400 font-medium">Or</span>
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <button
                                    type="button"
                                    id="resendButton"
                                    class="inline-flex items-center text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 underline decoration-slate-300 dark:decoration-slate-600 decoration-2 underline-offset-4 hover:decoration-slate-500 dark:hover:decoration-slate-400 disabled:text-slate-400 dark:disabled:text-slate-600 disabled:no-underline disabled:cursor-not-allowed transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Resend Code
                                </button>
                                <div id="resendCooldown" class="hidden mt-2 inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700/50">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-xs text-slate-600 dark:text-slate-400">
                                    Available in <span id="resendTimer" class="font-mono font-semibold"></span>s
                                </span>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Back to Sign In -->
                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700/50 text-center">
                        <a href="{{ route('sign-in') }}" class="inline-flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors group">
                            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Sign In
                        </a>
                    </div>
                </div>

                <!-- Additional security badge -->
                <div class="flex items-center justify-center text-xs text-slate-500 dark:text-slate-500 space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Secured with two-factor authentication</span>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-focus the code input with subtle animation
                const codeInput = document.getElementById('code');
                setTimeout(() => codeInput.focus(), 100);

                const button = document.getElementById('verifyButton');
                const buttonText = document.getElementById('buttonText');
                const spinner = document.getElementById('loadingSpinner');

                // Timer countdown for code expiration (15 minutes)
                let timeLeft = 15 * 60;
                const countdownElement = document.getElementById('countdown');

                function updateTimer() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    if (timeLeft <= 0) {
                        countdownElement.textContent = 'EXPIRED';
                        countdownElement.className = 'font-mono font-bold text-red-600 dark:text-red-400 ml-1';
                        document.getElementById('verifyButton').disabled = true;
                        return;
                    }

                    // Change color when less than 5 minutes left
                    if (timeLeft <= 300) {
                        countdownElement.className = 'font-mono font-bold text-red-600 dark:text-red-400 ml-1';
                    } else if (timeLeft <= 600) {
                        countdownElement.className = 'font-mono font-bold text-amber-600 dark:text-amber-400 ml-1';
                    }

                    timeLeft--;
                }

                const timerInterval = setInterval(updateTimer, 1000);
                updateTimer();

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
                                // Show success with subtle notification
                                alert('New verification code sent to your email!');

                                resendCooldown = 60;
                                resendButton.disabled = true;
                                resendCooldownDiv.classList.remove('hidden');
                                resendInterval = setInterval(updateResendTimer, 1000);
                                updateResendTimer();

                                // Reset main timer
                                timeLeft = 15 * 60;
                                countdownElement.className = 'font-mono font-bold text-slate-800 dark:text-slate-300 ml-1';
                                document.getElementById('verifyButton').disabled = false;

                                codeInput.value = '';
                                codeInput.focus();
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

                // Auto-submit when 6 digits are entered with input animation
                codeInput.addEventListener('input', function(e) {
                    const value = e.target.value.replace(/\D/g, '');
                    e.target.value = value;

                    if (value.length === 6) {
                        setTimeout(() => {
                            document.getElementById('twoFactorForm').submit();
                            button.disabled = true;
                            buttonText.textContent = 'Wait! Verifying OTP ...';
                            spinner.classList.remove('hidden');
                        }, 500);
                    }
                });

                // Add subtle pulse effect to input when focused
                codeInput.addEventListener('focus', function() {
                    this.parentElement.classList.add('scale-[1.01]');
                });

                codeInput.addEventListener('blur', function() {
                    this.parentElement.classList.remove('scale-[1.01]');
                });
            });
        </script>
    </main>
</x-app>
