@php use Carbon\Carbon; @endphp
<x-layouts.app page-name="Subscription Details">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-gray-50 dark:bg-gradient-to-br dark:from-gray-900 dark:via-blue-900/10 dark:to-gray-900 transition-colors duration-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Navigation Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
                <a href="{{ route('subscriptions.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Subscriptions
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $subscription->reference }}</span>
            </nav>

            <!-- Main Content Flow -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">

                <!-- Hero Header Section with Status -->
                <div class="relative">
                    <!-- Hero Content -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white px-8 py-10">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl font-bold mb-1">{{ ucfirst(str_replace([':', '_'], [' ', ' '], $subscription->package)) }}</h1>
                                        <p class="text-blue-100 text-sm font-mono">{{ $subscription->reference }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-4 mt-4">
                                    <div class="flex items-center text-sm bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        {{ $subscription->beneficiaries }} {{ Str::plural('User', $subscription->beneficiaries) }}
                                    </div>
                                    <div class="flex items-center text-sm bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $subscription->duration_in_months }} {{ Str::plural('Month', $subscription->duration_in_months) }}
                                    </div>
                                    <div class="flex items-center text-sm bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        {{ $subscription->academicSubjects->count() }} {{ Str::plural('Subject', $subscription->academicSubjects->count()) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-center lg:items-end gap-3">
                                <div class="text-center lg:text-right">
                                    <p class="text-blue-100 text-sm mb-1">Total Amount</p>
                                    <p class="text-4xl font-bold">{{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}</p>
                                </div>
                                @if(\App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status)
                                    <a href="{{ route('payment.initialize', ['subscription' => $subscription->id]) }}"
                                       class="inline-flex items-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        Pay Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert for Unpaid (Seamlessly connected) -->
                @if(\App\Enums\SubscriptionStatus::UNPAID === $subscription->status)
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 px-8 py-6 border-b-2 border-yellow-300 dark:border-yellow-700">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-800 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-yellow-900 dark:text-yellow-100 mb-2">
                                    Complete Your Payment to Unlock Full Access
                                </h3>
                                <p class="text-yellow-800 dark:text-yellow-200 text-sm mb-4">
                                    Your subscription is ready! Complete payment to activate instant access to all selected subjects and learning materials.
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('payment.initialize', ['subscription' => $subscription->id]) }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        Pay GHS {{ number_format($subscription->amount, 2) }}
                                    </a>
                                    <button onclick="sharePaymentLink()"
                                            class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 border border-yellow-300 dark:border-yellow-600 text-yellow-800 dark:text-yellow-200 font-semibold rounded-lg hover:bg-yellow-50 dark:hover:bg-gray-700 transition-colors text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                        Share Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Key Details Grid (Flowing from header) -->
                <div class="px-8 py-8 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="group">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Reference</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white font-mono">{{ Str::limit($subscription->reference, 15) }}</p>
                                </div>
                            </div>
                            <button onclick="copyToClipboard('{{ $subscription->reference }}')"
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy
                            </button>
                        </div>

                        <div class="group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Created</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subscription->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $subscription->expires_at && $subscription->expires_at > now() ? 'Expires' : 'Status' }}
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        @if($subscription->expires_at)
                                            {{ $subscription->expires_at->format('M j, Y') }}
                                        @else
                                            Not Active
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace('-', ' ', $subscription->status->value)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($subscription->expires_at && $subscription->expires_at > now())
                        @php
                            $daysRemaining = Carbon::now()->diffInDays($subscription->expires_at);
                        @endphp
                        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            @if($daysRemaining == 0)
                                                ⚠️ Expires Today
                                            @elseif($daysRemaining <= 7)
                                                ⚠️ {{ intval($daysRemaining) }} {{ Str::plural('Day', $daysRemaining) }} Remaining
                                            @else
                                                {{ intval($daysRemaining) }} {{ Str::plural('Day', $daysRemaining) }} Remaining
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $subscription->expires_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all"
                                         style="width: {{ min(100, ($daysRemaining / ($subscription->duration_in_months * 30)) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Academic Subjects Section (Connected flow) -->
                <div class="px-8 py-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            Your Academic Subjects
                        </h2>
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-sm font-semibold">
                            {{ $subscription->academicSubjects->count() }} {{ Str::plural('Subject', $subscription->academicSubjects->count()) }}
                        </span>
                    </div>

                    @if($subscription->academicSubjects->count() > 0)
                        <div class="space-y-6">
                            @foreach($subscription->academicSubjects->groupBy('academicLevel.academicGroup.name') as $groupName => $subjects)
                                <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                            {{ $groupName }}
                                        </h3>
                                    </div>
                                    <div class="p-6">
                                        @foreach($subjects->groupBy('academicLevel.name') as $levelName => $levelSubjects)
                                            <div class="mb-6 last:mb-0">
                                                <div class="flex items-center gap-2 mb-4">
                                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                    </div>
                                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $levelName }}</h4>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $levelSubjects->count() }} subjects)</span>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach($levelSubjects as $subject)
                                                        <div class="group flex items-center gap-3 p-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all duration-200">
                                                            <div class="w-2 h-2 bg-green-500 rounded-full group-hover:scale-150 transition-transform"></div>
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
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Subjects Assigned</h3>
                            <p class="text-gray-500 dark:text-gray-400">This subscription doesn't include any specific academic subjects yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 dark:bg-gray-800/50 px-8 py-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-3 justify-center">
                        @if(\App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status)
                            <a href="{{ route('payment.initialize', ['subscription' => $subscription->id]) }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Complete Payment
                            </a>
                            <button onclick="sharePaymentLink()"
                                    class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Share Payment Link
                            </button>
                        @endif
                        <a href="{{ route('subscriptions.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Subscriptions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('Reference copied to clipboard!', 'success');
                }).catch(function(err) {
                    fallbackCopyToClipboard(text);
                });
            } else {
                fallbackCopyToClipboard(text);
            }
        }

        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
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

        function sharePaymentLink() {
            const paymentUrl = '{{ route("payment.initialize", ["subscription" => $subscription->id]) }}';
            const fullUrl = window.location.origin + paymentUrl;

            if (navigator.share) {
                navigator.share({
                    title: 'Complete Subscription Payment',
                    text: 'Complete payment for subscription {{ $subscription->reference }}',
                    url: fullUrl
                }).then(() => {
                    showToast('Payment link shared successfully!', 'success');
                }).catch((error) => {
                    console.log('Error sharing:', error);
                    copyToClipboard(fullUrl);
                });
            } else {
                copyToClipboard(fullUrl);
                showToast('Payment link copied to clipboard!', 'success');
            }
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
