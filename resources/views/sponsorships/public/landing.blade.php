@extends('components.layouts.guest')

@section('content')
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="text-center max-w-4xl mx-auto mb-16">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    Make a Difference Through
                    <span class="text-blue-600 dark:text-blue-400">Sponsorship</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                    Connect with meaningful causes, support educational programs, and help build a better future through
                    our transparent sponsorship platform.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('sponsorships.projects.index') }}"
                       class="px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-lg">
                        Browse Programs
                    </a>
                    <a href="{{ route('sponsorships.offers.index') }}"
                       class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-lg border border-gray-200 dark:border-gray-700">
                        View Sponsor Offers
                    </a>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                        {{ $stats['total_programs'] }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Active Programs</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                        GHS {{ number_format($stats['total_raised'], 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Funds Raised</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                        {{ $stats['total_offers'] }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Sponsor Offers</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2">
                        {{ $stats['total_beneficiaries'] }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Beneficiaries</div>
                </div>
            </div>
        </div>

        <!-- Featured Programs Section -->
        <div class="bg-white dark:bg-gray-800 py-16">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Featured Programs</h2>
                        <p class="text-gray-600 dark:text-gray-400">Support these impactful sponsorship programs</p>
                    </div>
                    <a href="{{ route('sponsorships.projects.index') }}"
                       class="px-6 py-2 text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                        View All →
                    </a>
                </div>

                @if($featuredPrograms->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredPrograms as $program)
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                                <!-- Header -->
                                <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ Str::limit($program->name, 50) }}</h3>
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ ucfirst($program->type) }}
                                    </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $program->description }}</p>
                                </div>

                                <!-- Progress -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            <span>Progress</span>
                                            <span>{{ $program->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full transition-all"
                                                 style="width: {{ $program->progress_percentage }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Stats -->
                                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Raised</p>
                                            <p class="font-bold text-gray-900 dark:text-white">
                                                GHS {{ number_format($program->amount_raised, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Goal</p>
                                            <p class="font-bold text-gray-900 dark:text-white">
                                                GHS {{ number_format($program->amount_goal, 2) }}</p>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('sponsorships.programs.show', $program) }}"
                                           class="px-3 py-2 text-center text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                            Details
                                        </a>
                                        <a href="{{ route('sponsorships.programs.contribute', $program) }}"
                                           class="px-3 py-2 text-center text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            Contribute
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No featured programs available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Featured Sponsor Offers Section -->
        <div class="bg-gray-50 dark:bg-gray-900 py-16">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Featured Sponsor Offers</h2>
                        <p class="text-gray-600 dark:text-gray-400">Opportunities from sponsors looking to make an
                            impact</p>
                    </div>
                    <a href="{{ route('sponsorships.offers.index') }}"
                       class="px-6 py-2 text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                        View All →
                    </a>
                </div>

                @if($featuredOffers->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredOffers as $offer)
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                                <!-- Header -->
                                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ Str::limit($offer->title, 50) }}</h3>
                                        @if($offer->accepts_bids)
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Open
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $offer->description }}</p>
                                </div>

                                <!-- Details -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Amount Offered</span>
                                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                            GHS {{ number_format($offer->amount_offered, 2) }}
                                        </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>By {{ $offer->user->name }}</span>
                                    </div>

                                    <a href="{{ route('sponsorships.offers.show', $offer) }}"
                                       class="block w-full px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No sponsor offers available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="bg-white dark:bg-gray-800 py-16">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">How It Works</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Our platform makes it easy to support causes you care about
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">1. Browse Programs</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Explore verified sponsorship programs and sponsor offers that align with your values.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">2. Make a Contribution</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Choose an amount and complete your contribution through our secure payment gateway.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">3. Track Impact</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Watch your contribution make a real difference and see the progress of programs you support.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Ready to Make a Difference?
                </h2>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Join our community of sponsors and benefactors working together to create positive change.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('sponsorships.projects.index') }}"
                       class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition shadow-lg">
                        Start Supporting
                    </a>
                    @auth
                        <a href="{{ route('benefactors.index') }}"
                           class="px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition">
                            Create a Program
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
