@php use Carbon\Carbon; @endphp
<x-layouts.app page-name="Subscription Details">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Navigation -->
            <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-8">
                <a href="{{ route('subscriptions.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    Subscriptions
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $subscription->reference }}</span>
            </nav>

            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Subscription Details
                </h1>
                <p class="text-xl hidden text-gray-600 dark:text-gray-400">
                    Complete overview of subscription {{ $subscription->reference }}
                </p>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content - Single Merged Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Overview Section -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Subscription Overview</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reference</dt>
                                        <dd class="mt-1 flex items-center justify-between">
                                            <span class="text-lg font-mono text-gray-900 dark:text-white">{{ $subscription->reference }}</span>
                                            <button onclick="copyToClipboard('{{ $subscription->reference }}')"
                                                    class="ml-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Package Type</dt>
                                        <dd class="mt-1 text-lg text-gray-900 dark:text-white">
                                            {{ ucfirst(str_replace([':', '_'], [' ', ' '], $subscription->package)) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beneficiaries</dt>
                                        <dd class="mt-1 text-lg text-gray-900 dark:text-white">{{ $subscription->beneficiaries }}</dd>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</dt>
                                        <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="mt-1">
                                            <span @class([
                                                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                                'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' => \App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status,
                                                'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' => \App\Enums\SubscriptionStatus::PART_PAID->value === $subscription->status,
                                                'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' => \App\Enums\SubscriptionStatus::PAID->value === $subscription->status,
                                            ])>
                                                {{ ucfirst(str_replace('-', ' ', $subscription->status->value)) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                                        <dd class="mt-1 text-lg text-gray-900 dark:text-white">
                                            {{ $subscription->expires_at->format('M j, Y') }}
                                            <span class="block text-sm text-gray-500 dark:text-gray-400">
                                                @if($subscription->expires_at > now())
                                                    {{ $subscription->expires_at->diffForHumans() }}
                                                @else
                                                    Expired
                                                @endif
                                            </span>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Subjects Section -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Academic Subjects</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                {{ $subscription->academicSubjects->count() }} {{ Str::plural('subject', $subscription->academicSubjects->count()) }} included in your subscription
                            </p>

                            @if($subscription->academicSubjects->count() > 0)
                                <div class="space-y-8">
                                    @foreach($subscription->academicSubjects->groupBy('academicLevel.academicGroup.name') as $groupName => $subjects)
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $groupName }}</h4>
                                            @foreach($subjects->groupBy('academicLevel.name') as $levelName => $levelSubjects)
                                                <div class="mb-6 last:mb-0">
                                                    <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $levelName }}</h5>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                        @foreach($levelSubjects as $subject)
                                                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                                <div class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full mr-3 flex-shrink-0"></div>
                                                                <div class="min-w-0 flex-1">
                                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $subject->name }}</p>
                                                                    @if($subject->code)
                                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subject->code }}</p>
                                                                    @endif
                                                                </div>
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
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <h6 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Subjects Assigned</h6>
                                    <p class="text-gray-500 dark:text-gray-400">This subscription doesn't include any specific academic subjects.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Single Merged Card with Sections -->
                <div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Actions Section -->
                        @if(\App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status)
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Required</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Complete your payment to activate this subscription.</p>
                                <button class="w-full bg-gray-900 hover:bg-gray-800 dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 font-medium py-3 px-4 rounded-lg transition-colors">
                                    Pay Now
                                </button>
                            </div>
                        @endif

                        <!-- Payment Information Section -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Mobile Money Payment</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Dial Code:</span>
                                            <code class="font-mono text-gray-900 dark:text-white">*772*30#</code>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Merchant:</span>
                                            <code class="font-mono text-gray-900 dark:text-white">1326001</code>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Reference:</span>
                                            <button onclick="copyToClipboard('{{ $subscription->reference }}')"
                                                    class="font-mono text-gray-900 dark:text-white hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                                {{ $subscription->reference }}
                                            </button>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                            <span class="font-mono text-gray-900 dark:text-white">{{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Section -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Timeline</h3>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="w-3 h-3 bg-gray-400 dark:bg-gray-500 rounded-full mr-4 mt-2 flex-shrink-0"></div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Created</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->created_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>

                                @if(\App\Enums\SubscriptionStatus::PAID->value === $subscription->status)
                                    <div class="flex items-start">
                                        <div class="w-3 h-3 bg-gray-900 dark:bg-white rounded-full mr-4 mt-2 flex-shrink-0"></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Activated</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Payment confirmed</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-start">
                                    <div class="w-3 h-3 {{ $subscription->expires_at > now() ? 'bg-gray-400 dark:bg-gray-500' : 'bg-red-500 dark:bg-red-400' }} rounded-full mr-4 mt-2 flex-shrink-0"></div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $subscription->expires_at > now() ? 'Expires' : 'Expired' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->expires_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h3>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Created on</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $subscription->created_at->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Package</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ ucfirst(str_replace([':', '_'], [' ', ' '], $subscription->package)) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Valid until</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $subscription->expires_at->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-600 dark:text-gray-400">Days remaining</dt>
                                    <dd class="text-gray-900 dark:text-white">
                                        @if($subscription->expires_at > now())
                                            @php
                                                $daysRemaining = Carbon::now()->diffInDays($subscription->expires_at);                                            @endphp
                                            @if($daysRemaining == 0)
                                                <span class="text-yellow-600 dark:text-yellow-400">Expires today</span>
                                            @else
                                                {{ intval($daysRemaining) }} {{ Str::plural('day', $daysRemaining) }} remaining
                                            @endif
                                        @else
                                            <span class="text-red-600 dark:text-red-400">Expired</span>
                                        @endif
                                    </dd>
                                </div>

                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        function copyToClipboard(text) {
            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('Reference copied to clipboard!', 'success');
                }).catch(function(err) {
                    // Fallback to older method
                    fallbackCopyToClipboard(text);
                });
            } else {
                // Use fallback method
                fallbackCopyToClipboard(text);
            }
        }

        function fallbackCopyToClipboard(text) {
            // Create a temporary textarea element
            const textArea = document.createElement('textarea');
            textArea.value = text;

            // Make the textarea out of viewport
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                showToast('Reference copied to clipboard!', 'success');
            } catch (err) {
                console.error('Failed to copy text: ', err);
                showToast('Failed to copy reference', 'error');
            }

            document.body.removeChild(textArea);
        }

        function showToast(message, type) {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const baseClasses = 'flex items-center px-4 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300';
            const typeClasses = type === 'success'
                ? 'bg-green-600 text-white'
                : 'bg-red-600 text-white';

            toast.className = `${baseClasses} ${typeClasses}`;
            toast.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success'
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
            }
                </svg>
                <span class="font-medium">${message}</span>
            `;
            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toastContainer.contains(toast)) {
                        toastContainer.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }
    </script>

</x-layouts.app>
