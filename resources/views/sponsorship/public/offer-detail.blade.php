<x-layouts.guest>
    <div class="py-12 bg-gradient-to-br from-gray-50 via-indigo-50/30 to-gray-50 dark:from-gray-900 dark:via-indigo-900/10 dark:to-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Navigation -->
            <a href="{{ route('sponsorship.offers.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mb-6 group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All Offers
            </a>

            <!-- Main Content Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <!-- Hero Header with Gradient -->
                <div class="relative h-4 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                <!-- Header Section -->
                <div class="p-8 lg:p-12 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex-1">
                            <!-- Code Badge -->
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 ring-2 ring-indigo-500/20 mb-4">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                                </svg>
                                {{ $offer->code }}
                            </div>

                            <!-- Title -->
                            <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 leading-tight">
                                {{ $offer->title }}
                            </h1>

                            <!-- Sponsor Info -->
                            <div class="flex items-center gap-4 text-gray-600 dark:text-gray-400">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-white text-sm font-bold mr-3">
                                        {{ substr($offer->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Sponsored by</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $offer->user->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex flex-col items-start lg:items-end gap-3">
                            @if($offer->status === 'open')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 ring-2 ring-green-500/20">
                                    <svg class="w-4 h-4 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Open for Applications
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                    {{ ucfirst($offer->status) }}
                                </span>
                            @endif

                            @if($offer->accepts_bids)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Accepting Applications
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Amount Showcase -->
                <div class="p-8 lg:p-12 bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/20 dark:via-emerald-900/20 dark:to-teal-900/20 border-b border-green-100 dark:border-green-800">
                    <div class="text-center max-w-2xl mx-auto">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Sponsorship Amount</p>
                        <div class="flex items-baseline justify-center mb-4">
                            <span class="text-2xl font-bold text-gray-600 dark:text-gray-400">GHS</span>
                            <span class="text-6xl lg:text-7xl font-extrabold text-green-600 dark:text-green-400 mx-3">
                                {{ number_format($offer->amount_offered, 0) }}
                            </span>
                            <span class="text-3xl font-bold text-gray-500">.{{ str_pad((int)(($offer->amount_offered - floor($offer->amount_offered)) * 100), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Available for qualified programs</p>

                        @if($offer->expires_at)
                            <div class="mt-6 inline-flex items-center px-4 py-2 bg-orange-100 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800 rounded-xl">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-left">
                                    <p class="text-xs text-orange-800 dark:text-orange-200 font-medium">Expires</p>
                                    <p class="text-sm font-bold text-orange-900 dark:text-orange-100">
                                        {{ $offer->expires_at->format('F j, Y') }}
                                        <span class="font-normal">({{ $offer->expires_at->diffForHumans() }})</span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Description Section -->
                <div class="p-8 lg:p-12 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">About This Opportunity</h2>
                    </div>
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $offer->description }}</p>
                    </div>
                </div>

                <!-- Eligibility Criteria -->
                @if($offer->criteria)
                    <div class="p-8 lg:p-12 border-b border-gray-200 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/10">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Eligibility Requirements</h2>
                        </div>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-blue-200 dark:border-blue-800 shadow-sm">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $offer->criteria }}</p>
                        </div>
                    </div>
                @endif

                <!-- Statistics Grid -->
                <div class="p-8 lg:p-12 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Offer Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-2xl border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Applications</p>
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $offer->accepts_bids ? 'Accepting' : 'Not Accepting' }}</p>
                        </div>

                        <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl border border-green-200 dark:border-green-800">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Current Status</p>
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ ucfirst($offer->status) }}</p>
                        </div>

                        <div class="p-6 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-2xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Accepted Programs</p>
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $offer->bids->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Accepted Programs -->
                @if($offer->bids->count() > 0)
                    <div class="p-8 lg:p-12 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Accepted Programs</h2>
                            </div>
                            <span class="px-4 py-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-sm font-bold">
                                {{ $offer->bids->count() }} {{ $offer->bids->count() === 1 ? 'Program' : 'Programs' }}
                            </span>
                        </div>

                        <div class="grid gap-4">
                            @foreach($offer->bids as $bid)
                                <div class="group p-6 border-2 border-gray-200 dark:border-gray-600 rounded-2xl hover:border-indigo-500 dark:hover:border-indigo-400 transition-all hover:shadow-lg bg-white dark:bg-gray-800">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $bid->sponsorshipProgram->name }}
                                                </h3>
                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full text-xs font-medium">
                                                    {{ $bid->sponsorshipProgram->code }}
                                                </span>
                                            </div>
                                            @if($bid->sponsorshipProgram->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                                    {{ $bid->sponsorshipProgram->description }}
                                                </p>
                                            @endif
                                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Goal: GHS {{ number_format($bid->sponsorshipProgram->amount_goal, 2) }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ ucfirst($bid->sponsorshipProgram->type) }}
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('sponsorship.programs.show', $bid->sponsorshipProgram) }}"
                                           class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium flex items-center">
                                            View
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- CTA Section -->
                @if($offer->accepts_bids && $offer->status === 'open')
                    <div class="p-8 lg:p-12 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
                        <div class="text-center max-w-2xl mx-auto">
                            <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-4">
                                <svg class="w-5 h-5 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                                </svg>
                                <span class="text-white font-semibold text-sm">Ready to Apply?</span>
                            </div>

                            <h3 class="text-3xl font-extrabold text-white mb-3">Join This Opportunity</h3>
                            <p class="text-indigo-100 text-lg mb-8">
                                @auth
                                    @if($userPrograms->count() > 0)
                                        Submit your verified program for consideration
                                    @else
                                        Create and verify a program to apply for this sponsorship
                                    @endif
                                @else
                                    Sign in or create an account to apply for this amazing opportunity
                                @endauth
                            </p>

                            @auth
                                @if($userPrograms->count() > 0)
                                    <button onclick="openApplicationModal()"
                                            class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-xl hover:shadow-2xl transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Apply Now
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('benefactor.programs.create') }}"
                                       class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-xl hover:shadow-2xl transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Create a Program
                                    </a>
                                @endif
                            @else
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="{{ route('sign-in') }}"
                                       class="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all shadow-xl hover:shadow-2xl transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign In
                                    </a>
                                    <a href="{{ route('sign-up') }}"
                                       class="inline-flex items-center justify-center px-8 py-4 bg-indigo-900 text-white font-bold rounded-xl hover:bg-indigo-800 transition-all shadow-xl hover:shadow-2xl transform hover:scale-105 border-2 border-white/20">
                                        Create Account
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                @else
                    <div class="p-12 bg-gray-50 dark:bg-gray-700/50 text-center border-t border-gray-200 dark:border-gray-700">
                        <div class="max-w-md mx-auto">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Applications Closed</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                This offer is currently {{ $offer->status === 'closed' ? 'closed' : 'not accepting applications' }}.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Related Offers CTA -->
            <div class="mt-12 text-center">
                <a href="{{ route('sponsorship.offers.index') }}"
                   class="inline-flex items-center px-6 py-3 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white transition-all font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explore More Opportunities
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Application Modal -->
    @auth
        @if($userPrograms->count() > 0 && $offer->accepts_bids && $offer->status === 'open')
            <div id="applicationModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Submit Application
                            </h3>
                            <button onclick="closeApplicationModal()" class="text-white hover:text-gray-200 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <form method="POST" action="{{ route('sponsorship.offers.bid', $offer) }}">
                            @csrf

                            <!-- Select Program -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Your Program <span class="text-red-500">*</span>
                                </label>
                                <select name="sponsorship_program_id" required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 py-3">
                                    <option value="">Choose a verified program...</option>
                                    @foreach($userPrograms as $program)
                                        <option value="{{ $program->id }}">
                                            {{ $program->name }} ({{ $program->code }}) - Goal: GHS {{ number_format($program->amount_goal, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Only verified programs are listed here</p>
                            </div>

                            <!-- Message -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Application Message (Optional)
                                </label>
                                <textarea name="message" rows="5"
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Tell the sponsor why your program is a perfect fit for this opportunity..."></textarea>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Explain how this funding will help achieve your program's goals</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" onclick="closeApplicationModal()"
                                        class="px-6 py-2.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition font-semibold shadow-lg flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function openApplicationModal() {
                    document.getElementById('applicationModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }

                function closeApplicationModal() {
                    document.getElementById('applicationModal').classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }

                // Close modal when clicking outside
                document.getElementById('applicationModal')?.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeApplicationModal();
                    }
                });

                // Close modal on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeApplicationModal();
                    }
                });
            </script>
        @endif
    @endauth
</x-layouts.guest>
