<x-layouts.app>
    <section name="header">
        <div class="flex items-center justify-between py-4">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                {{ __('My Sponsorships') }}
            </h2>
            <a href="{{ route('sponsorship.programs.index') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-sm hover:shadow-indigo-500/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('New Contribution') }}
            </a>
        </div>
    </section>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Contributed') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        <span class="text-indigo-600 dark:text-indigo-400 text-xl font-semibold">GHS</span>
                        {{ number_format($contributions->where('status', 'completed')->sum('amount'), 2) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Active Sponsorships') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $contributions->where('status', 'completed')->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Impact') }}</p>
                    <p class="mt-2 text-lg font-medium text-gray-600 dark:text-gray-300">{{ __('Supporting educational growth across the region.') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 px-1">{{ __('Recent Activity') }}</h3>

                @if($contributions->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('No contributions yet') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-6">
                            {{ __('Your history of giving will appear here once you make your first contribution to a program or offer.') }}
                        </p>
                        <a href="{{ route('sponsorship.programs.index') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                            {{ __('Explore active programs') }} &rarr;
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($contributions as $contribution)
                            @php
                                $statusColors = match($contribution->status) {
                                    'completed' => ['bg' => 'bg-green-50 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'dot' => 'bg-green-500'],
                                    'failed' => ['bg' => 'bg-red-50 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500'],
                                    default => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/30', 'text' => 'text-yellow-700 dark:text-yellow-400', 'dot' => 'bg-yellow-500'],
                                };
                            @endphp

                            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="hidden sm:flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            @if($contribution->sponsorshipProgram)
                                                {{ $contribution->sponsorshipProgram->name }}
                                            @elseif($contribution->sponsorOffer)
                                                {{ $contribution->sponsorOffer->title }}
                                            @else
                                                {{ __('Philanthropic Contribution') }}
                                            @endif
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $contribution->created_at->format('M d, Y') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16"/></svg>
                                                {{ $contribution->payment_reference }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-0 pt-3 md:pt-0">
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}
                                        </div>
                                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors['bg'] }} {{ $statusColors['text'] }} mt-1">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusColors['dot'] }} mr-1.5"></span>
                                            {{ ucfirst($contribution->status) }}
                                        </div>
                                    </div>

                                    @if($contribution->status === 'completed')
                                        <a href="{{ route('sponsorship.contributions.receipt', $contribution) }}"
                                           class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                           title="{{ __('Download Receipt') }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
