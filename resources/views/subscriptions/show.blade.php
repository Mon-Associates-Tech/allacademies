<x-layouts.app page-name="Subscription Details">

    <div class="min-h-screen bg-gray-50 rounded">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-32 w-80 h-80 bg-gray-200 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
            <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-gray-300 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <x-link.secondary :to="route('subscriptions.index')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Subscriptions
            </x-link.secondary>
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-900 rounded-xl shadow-lg mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-1">
                    Subscription Details
                </h1>
                <p class="text text-gray-600 max-w-2xl mx-auto">
                    Complete overview of your subscription package and included services
                </p>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Subscription Info Card -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-indigo-600 px-8 py-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-white">{{ $subscription->reference }}</h2>
                                    <p class="text-gray-300">Subscription Reference</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-white">{{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}</div>
                                    <p class="text-gray-300">Total Amount</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Status -->
                                <div class="space-y-4">
                                    <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <div class="w-2 h-2 bg-gray-900 rounded-full mr-3"></div>
                                        Status Information
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600 text-sm">Current Status</span>
                                            <span @class([
                                                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                                'bg-red-100 text-red-800' => \App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status,
                                                'bg-yellow-100 text-yellow-800' => \App\Enums\SubscriptionStatus::PART_PAID->value === $subscription->status,
                                                'bg-green-100 text-green-800' => \App\Enums\SubscriptionStatus::PAID->value === $subscription->status,
                                            ])>
                                                {{ ucfirst(str_replace('-', ' ', $subscription->status->value)) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600 text-sm">Expires On</span>
                                            <span class="font-medium text-gray-900">{{ $subscription->expires_at->format('M j, Y') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600 text-sm">Time Remaining</span>
                                            <span class="font-medium text-gray-900">
                                                @if($subscription->expires_at > now())
                                                    {{ $subscription->expires_at->diffForHumans() }}
                                                @else
                                                    <span class="text-red-600">Expired</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Details -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <div class="w-2 h-2 bg-gray-600 rounded-full mr-3"></div>
                                        Package Details
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Package Type</span>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst(str_replace([':', '_'], [' ', ' '], $subscription->package)) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Beneficiaries</span>
                                            <span class="font-medium text-gray-900">{{ $subscription->beneficiaries }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Created On</span>
                                            <span class="font-medium text-gray-900">{{ $subscription->created_at->format('M j, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subjects Grid -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-800 px-8 py-6">
                            <h2 class="text-2xl font-bold text-white">Subscribed Subjects</h2>
                            <p class="text-gray-300 mt-1">{{ $subscription->academicSubjects->count() }} {{ Str::plural('subject', $subscription->academicSubjects->count()) }} included</p>
                        </div>

                        <div class="p-8">
                            @if($subscription->academicSubjects->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($subscription->academicSubjects->groupBy('academicLevel.academicGroup.name') as $groupName => $subjects)
                                        <div class="space-y-4">
                                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                                <div class="w-3 h-3 bg-gray-700 rounded-full mr-3"></div>
                                                {{ $groupName }}
                                            </h3>

                                            @foreach($subjects->groupBy('academicLevel.name') as $levelName => $levelSubjects)
                                                <div class="ml-6 space-y-2">
                                                    <h4 class="font-medium text-gray-700 flex items-center">
                                                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                                        {{ $levelName }}
                                                    </h4>
                                                    <div class="ml-4 space-y-1">
                                                        @foreach($levelSubjects as $subject)
                                                            <div class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors duration-150">
                                                                <svg class="w-3 h-3 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                                {{ $subject->name }}
                                                                @if($subject->code)
                                                                    <span class="ml-2 text-xs text-gray-400">({{ $subject->code }})</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects assigned</h3>
                                    <p class="mt-1 text-sm text-gray-500">This subscription doesn't include any specific subjects.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Payment Info -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
                        <div class="bg-indigo-500 px-6 py-4">
                            <h3 class="text-lg font-bold text-white">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <button onclick="copyToClipboard('{{ $subscription->reference }}')"
                                    class="w-full flex items-center justify-center px-4 py-3 bg-gray-400 text-white font-medium rounded-lg hover:bg-gray-800 transition-all duration-200 transform hover:scale-105 shadow">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy Reference
                            </button>

                            @if(\App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status)
                                <button class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Pay Now
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Payment Guide
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Mobile Money -->
                            <div class="space-y-3">
                                <h4 class="font-semibold text-gray-900 flex items-center">
                                    <div class="w-2 h-2 bg-gray-600 rounded-full mr-3"></div>
                                    Mobile Money Payment
                                </h4>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">📱</span>
                                        <div>
                                            <p class="font-semibold text-gray-900">Dial: <span class="text-gray-700">*772*30#</span></p>
                                            <p class="text-sm text-gray-600">MTN Mobile Money</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">🏪</span>
                                        <div>
                                            <p class="font-semibold text-gray-900">Merchant: <span class="text-gray-700">1326001</span></p>
                                            <p class="text-sm text-gray-600">All Academies</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">📋</span>
                                        <div>
                                            <p class="font-semibold text-gray-900">Reference: <span class="text-gray-700">{{ $subscription->reference }}</span></p>
                                            <p class="text-sm text-gray-600">Use this as payment reference</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Steps -->
                            <div class="space-y-3">
                                <h4 class="font-semibold text-gray-900 flex items-center">
                                    <div class="w-2 h-2 bg-gray-500 rounded-full mr-3"></div>
                                    Payment Steps
                                </h4>
                                <div class="space-y-2">
                                    @foreach(['Dial *772*30# on your phone', 'Select option for payment', 'Enter merchant code: 1326001', 'Enter amount: ' . $subscription->currency . ' ' . number_format($subscription->amount, 2), 'Enter reference: ' . $subscription->reference, 'Confirm payment with PIN'] as $index => $step)
                                        <div class="flex items-center text-sm">
                                            <div class="flex-shrink-0 w-6 h-6 bg-gray-700 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                                {{ $index + 1 }}
                                            </div>
                                            <span class="text-gray-700">{{ $step }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Timeline -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-500 px-6 py-4">
                            <h3 class="text-lg font-bold text-white">Subscription Timeline</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Created</p>
                                        <p class="text-xs text-gray-500">{{ $subscription->created_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>

                                @if(\App\Enums\SubscriptionStatus::PAID->value === $subscription->status)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full mr-4"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Activated</p>
                                            <p class="text-xs text-gray-500">Payment confirmed</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-3 h-3 {{ $subscription->expires_at > now() ? 'bg-yellow-500' : 'bg-red-500' }} rounded-full mr-4"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $subscription->expires_at > now() ? 'Expires' : 'Expired' }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $subscription->expires_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Reference copied to clipboard!', 'success');
            }, function(err) {
                showToast('Failed to copy reference', 'error');
                console.error('Could not copy text: ', err);
            });
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
            }`;
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                toast.style.transform = 'translateX(full)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>
</x-layouts.app>
