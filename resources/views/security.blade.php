<x-layouts.app title="Security Settings">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Security Settings</h1>
                        <p class="text-sm text-gray-500">Manage your account security and privacy settings</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Password Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Password</p>
                        <p class="text-xs text-gray-500">Last changed: {{ auth()->user()->updated_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Email Verification Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Email Status</p>
                        @if(auth()->user()->hasVerifiedEmail())
                            <p class="text-xs text-green-600">Verified</p>
                        @else
                            <p class="text-xs text-orange-600">Unverified</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 {{ auth()->user()->hasVerifiedEmail() ? 'bg-green-400' : 'bg-orange-400' }} rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Account Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Last Activity</p>
                        <p class="text-xs text-gray-500">{{ now()->diffForHumans() }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Password Management -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Password Management</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                Keep your account secure by regularly updating your password. Use a strong, unique password that you don't use anywhere else.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Use at least 8 characters</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Include numbers and special characters</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Avoid common words or patterns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Last changed: {{ auth()->user()->updated_at->format('M j, Y') }}
                        </div>
                        <a href="{{ route('password.change') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Change Password
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Verification -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Email Verification</h3>
                            @if(auth()->user()->hasVerifiedEmail())
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-green-700">Email Verified</span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    Your email address <strong>{{ auth()->user()->email }}</strong> has been verified and is secure.
                                </p>
                            @else
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-orange-700">Email Not Verified</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-4">
                                    Please verify your email address <strong>{{ auth()->user()->email }}</strong> to secure your account.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    @if(auth()->user()->hasVerifiedEmail())
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Verified on {{ auth()->user()->email_verified_at->format('M j, Y') }}
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Email: {{ auth()->user()->email }}
                            </div>
                            <a href="{{ route('verification.notice') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                                Verify Email
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                <p class="text-sm text-gray-500">Basic details about your account and security status</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Account Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Account Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->created_at->format('F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Profile Update</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->updated_at->format('F j, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Security Level</dt>
                                <dd class="mt-1">
                                    @php
                                        $securityScore = 0;
                                        if(auth()->user()->hasVerifiedEmail()) $securityScore += 50;
                                        $securityScore += 50; // For having a password

                                        $level = $securityScore >= 100 ? 'High' : ($securityScore >= 75 ? 'Medium' : 'Low');
                                        $color = $securityScore >= 100 ? 'green' : ($securityScore >= 75 ? 'yellow' : 'red');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $level }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ now()->format('F j, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Recommendations -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">Security Recommendations</h3>
                    <div class="text-sm text-blue-800 space-y-2">
                        <div class="flex items-start space-x-2">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong>Use a unique password:</strong> Never reuse your account password on other websites or services.
                            </div>
                        </div>
                        <div class="flex items-start space-x-2">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong>Enable email notifications:</strong> Stay informed about important account activities and security events.
                            </div>
                        </div>
                        <div class="flex items-start space-x-2">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong>Keep your email secure:</strong> Your email account is the key to your account recovery - protect it well.
                            </div>
                        </div>
                        <div class="flex items-start space-x-2">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong>Review regularly:</strong> Check your security settings periodically and update them as needed.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Edit Profile</p>
                        <p class="text-xs text-gray-500">Update your personal information</p>
                    </div>
                </a>

                <a href="{{ route('password.change') }}"
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Change Password</p>
                        <p class="text-xs text-gray-500">Update your account password</p>
                    </div>
                </a>

                @if(!auth()->user()->hasVerifiedEmail())
                    <a href="{{ route('verification.notice') }}"
                       class="flex items-center p-4 border border-orange-200 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-orange-900">Verify Email</p>
                            <p class="text-xs text-orange-700">Secure your account now</p>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
