<x-layouts.app title="Change Password">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Security' => route('security'),
            'Change Password' => null
        ]" />
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Change Password</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Update your password to keep your account secure</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Requirements Info -->
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Password Requirements</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                        <ul class="list-disc list-inside space-y-1">
                            <li>At least 8 characters long</li>
                            <li>Contains at least one uppercase letter</li>
                            <li>Contains at least one lowercase letter</li>
                            <li>Contains at least one number</li>
                            <li>Contains at least one special character</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="POST" action="{{ route('password.change') }}" x-data="passwordForm()">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   required
                                   autocomplete="current-password"
                                   class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 pr-10 dark:placeholder-gray-400"
                                   placeholder="Enter your current password">
                            <button type="button"
                                    @click="togglePassword('current_password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showCurrentPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showCurrentPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m0 0l3.122 3.122M12 12l3.122-3.122m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   x-model="newPassword"
                                   @input="checkPasswordStrength"
                                   class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 pr-10 dark:placeholder-gray-400"
                                   placeholder="Enter your new password">
                            <button type="button"
                                    @click="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showNewPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showNewPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m0 0l3.122 3.122M12 12l3.122-3.122m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div x-show="newPassword.length > 0" class="mt-2">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1">
                                    <div class="bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300"
                                             :class="{
                                                 'bg-red-500 w-1/4': strength === 'weak',
                                                 'bg-yellow-500 w-2/4': strength === 'medium',
                                                 'bg-green-500 w-3/4': strength === 'strong',
                                                 'bg-green-600 w-full': strength === 'very-strong'
                                             }"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-medium"
                                      :class="{
                                          'text-red-600 dark:text-red-400': strength === 'weak',
                                          'text-yellow-600 dark:text-yellow-400': strength === 'medium',
                                          'text-green-600 dark:text-green-400': strength === 'strong' || strength === 'very-strong'
                                      }"
                                      x-text="strengthText"></span>
                            </div>
                        </div>

                        @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   x-model="confirmPassword"
                                   class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 pr-10 dark:placeholder-gray-400"
                                   placeholder="Confirm your new password">
                            <button type="button"
                                    @click="togglePassword('password_confirmation')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m0 0l3.122 3.122M12 12l3.122-3.122m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Password Match Indicator -->
                        <div x-show="confirmPassword.length > 0" class="mt-1">
                            <div x-show="newPassword === confirmPassword && confirmPassword.length > 0"
                                 class="flex items-center text-sm text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Passwords match
                            </div>
                            <div x-show="newPassword !== confirmPassword && confirmPassword.length > 0"
                                 class="flex items-center text-sm text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Passwords don't match
                            </div>
                        </div>

                        @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('security') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Cancel
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!isFormValid">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Security Tips -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Security Tips</h3>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <li>• Use a unique password that you don't use anywhere else</li>
                <li>• Consider using a password manager to generate and store strong passwords</li>
                <li>• Don't share your password with anyone</li>
                <li>• Change your password regularly, especially if you suspect it may be compromised</li>
            </ul>
        </div>
    </div>

    @push('scripts')
        <script>
            function passwordForm() {
                return {
                    newPassword: '',
                    confirmPassword: '',
                    showCurrentPassword: false,
                    showNewPassword: false,
                    showConfirmPassword: false,
                    strength: '',
                    strengthText: '',

                    togglePassword(field) {
                        const input = document.getElementById(field);
                        if (field === 'current_password') {
                            this.showCurrentPassword = !this.showCurrentPassword;
                            input.type = this.showCurrentPassword ? 'text' : 'password';
                        } else if (field === 'password') {
                            this.showNewPassword = !this.showNewPassword;
                            input.type = this.showNewPassword ? 'text' : 'password';
                        } else if (field === 'password_confirmation') {
                            this.showConfirmPassword = !this.showConfirmPassword;
                            input.type = this.showConfirmPassword ? 'text' : 'password';
                        }
                    },

                    checkPasswordStrength() {
                        let score = 0;
                        const password = this.newPassword;

                        if (password.length >= 8) score++;
                        if (/[a-z]/.test(password)) score++;
                        if (/[A-Z]/.test(password)) score++;
                        if (/[0-9]/.test(password)) score++;
                        if (/[^a-zA-Z0-9]/.test(password)) score++;

                        if (score <= 2) {
                            this.strength = 'weak';
                            this.strengthText = 'Weak';
                        } else if (score === 3) {
                            this.strength = 'medium';
                            this.strengthText = 'Medium';
                        } else if (score === 4) {
                            this.strength = 'strong';
                            this.strengthText = 'Strong';
                        } else {
                            this.strength = 'very-strong';
                            this.strengthText = 'Very Strong';
                        }
                    },

                    get isFormValid() {
                        return this.newPassword.length >= 8 &&
                            this.newPassword === this.confirmPassword &&
                            this.strength !== 'weak';
                    }
                }
            }
        </script>
    @endpush
</x-layouts.app>
