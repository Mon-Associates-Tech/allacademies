<x-app>
    <!-- Dark mode toggle button -->
    <div class="fixed top-4 right-4 z-50">
        <button
            id="theme-toggle"
            class="p-2 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
            onclick="toggleTheme()"
        >
            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-gray-800" fill="currentColor"
                 viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-yellow-500" fill="currentColor"
                 viewBox="0 0 20 20">
                <path
                    d="M10 2L13.09 8.26L20 9L14 14.74L15.18 21.02L10 17.77L4.82 21.02L6 14.74L0 9L6.91 8.26L10 2Z"></path>
            </svg>
        </button>
    </div>

    <section
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden transition-colors duration-300">
        <!-- Animated background elements -->
        <div class="absolute inset-0">
            <div
                class="absolute top-1/4 left-1/4 w-32 h-32 md:w-64 md:h-64 bg-blue-200 dark:bg-blue-900/30 rounded-full mix-blend-multiply dark:mix-blend-normal filter blur-xl opacity-70 animate-blob"></div>
            <div
                class="absolute top-1/3 right-1/4 w-32 h-32 md:w-64 md:h-64 bg-green-200 dark:bg-green-900/30 rounded-full mix-blend-multiply dark:mix-blend-normal filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div
                class="absolute bottom-1/4 left-1/3 w-32 h-32 md:w-64 md:h-64 bg-orange-200 dark:bg-orange-900/30 rounded-full mix-blend-multiply dark:mix-blend-normal filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="lg:grid lg:min-h-screen lg:grid-cols-12 relative z-10">
            <!-- Image section -->
            <aside class="relative block h-32 sm:h-48 lg:order-last lg:col-span-5 lg:h-full xl:col-span-6">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-green-600/20 dark:from-blue-800/40 dark:to-green-800/40 backdrop-blur-sm"></div>
                <img
                    alt="Students learning online"
                    src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80"
                    class="absolute inset-0 h-full w-full object-cover"
                />
                <!-- Overlay content -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent lg:bg-gradient-to-l"></div>
                <div
                    class="absolute bottom-4 left-4 right-4 sm:bottom-8 sm:left-8 sm:right-8 text-white lg:bottom-16 lg:left-16 lg:right-16">
                    <h2 class="text-xl sm:text-2xl font-bold lg:text-4xl mb-2 lg:mb-4">Join thousands of students</h2>
                    <p class="text-sm sm:text-lg opacity-90 mb-4 lg:mb-6">Learning and growing with All Academies every
                        day</p>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-2 sm:space-y-0 text-xs sm:text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>10,000+ Active Students</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>500+ Courses</span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Form section -->
            <main
                class="flex items-center justify-center px-4 sm:px-8 py-8 sm:py-12 lg:col-span-7 lg:px-16 xl:col-span-6">
                <div class="w-full max-w-md">
                    <!-- Logo and branding -->
                    <div class="mb-6 sm:mb-8">
                        <div class="flex items-center gap-3 mb-4 sm:mb-6">
                            <img class="h-8 sm:h-10 w-auto" src="{{ asset('img/logo.png') }}"
                                 alt="{{ config('app.name') }} Logo">
                            <span
                                class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">All Academies</span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">
                            Welcome back!
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
                            Sign in to continue your learning journey
                        </p>
                    </div>

                    <!-- login form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Login field -->
                        <div>
                            <label for="login"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 transition-colors duration-300">Email or Username / Student ID</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 transition-colors duration-300"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input
                                    id="login"
                                    name="login"
                                    type="text"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-300"
                                    placeholder="Email or Username / Student ID"
                                    value="{{ old('login') }}"
                                >
                            </div>
                            @error('login')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password field -->
                        <div>
                            <label for="password"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 transition-colors duration-300">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 transition-colors duration-300"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-300"
                                    placeholder="Enter your password"
                                >
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePasswordVisibility()"
                                >
                                    <svg id="password-eye-closed"
                                         class="h-5 w-5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="password-eye-open"
                                         class="hidden h-5 w-5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l.914-.914M4.464 4.464l15.072 15.072"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember me and forgot password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 transition-colors duration-300"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label for="remember"
                                       class="ml-2 block text-sm text-gray-700 dark:text-gray-200 transition-colors duration-300">
                                    Remember me
                                </label>
                            </div>
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors duration-300">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Sign in button -->
                        <button
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 dark:from-blue-500 dark:to-green-500 dark:hover:from-blue-600 dark:hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg hover:shadow-xl"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                              <svg class="h-5 w-5 text-white/75 group-hover:text-white transition-colors duration-200"
                                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                              </svg>
                            </span>
                            <span class="relative">Sign in to your account</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-6 sm:mt-8 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div
                                    class="w-full border-t border-gray-300 dark:border-gray-600 transition-colors duration-300"/>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span
                                    class="px-4 py-2 bg-white dark:bg-gray-800 text-nowrap text-gray-500 dark:text-gray-400 rounded-lg transition-colors duration-300">New to All Academies?</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sign up link -->
                    <div class="text-center">
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center justify-center w-full py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-blue-300 dark:hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Create new account
                        </a>
                    </div>

                    <!-- Social proof -->
                    <div class="mt-6 sm:mt-8 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 transition-colors duration-300">Trusted
                            by students worldwide</p>
                        <div class="flex flex-wrap justify-center items-center gap-4 text-gray-400 dark:text-gray-500">
                            <div class="flex items-center text-xs">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Secure & Safe</span>
                            </div>
                            <div class="flex items-center text-xs">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>24/7 Support</span>
                            </div>
                            <div class="flex items-center text-xs">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Free Trial</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </section>

    <script>
        // Theme management
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');

            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }

            updateThemeIcons();

            // Dispatch event to match existing ThemeController behavior
            window.dispatchEvent(new CustomEvent('dark-mode-toggled', {
                detail: {darkMode: !isDark}
            }));
        }

        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');

            if (isDark) {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            } else {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            }
        }

        // Password visibility toggle
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeClosed = document.getElementById('password-eye-closed');
            const eyeOpen = document.getElementById('password-eye-open');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeClosed.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
            }
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }

            updateThemeIcons();
        });

        // System theme change detection
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                updateThemeIcons();
            }
        });
    </script>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</x-app>
