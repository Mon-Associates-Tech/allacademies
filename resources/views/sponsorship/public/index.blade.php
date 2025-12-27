@extends('components.layouts.guest')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Sponsorship Programs</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Browse active programs and make a difference today</p>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
            <form method="GET" action="{{ route('sponsorship.programs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search programs..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">All Types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filter Programs
                    </button>
                </div>
            </form>
        </div>

        <!-- Programs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($programs as $program)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $program->name }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ ucfirst($program->type) }}
                        </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">{{ $program->description }}</p>
                    </div>

                    <!-- Progress -->
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span>Progress</span>
                                <span>{{ $program->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $program->progress_percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Raised</p>
                                <p class="font-bold text-gray-900 dark:text-white">GHS {{ number_format($program->amount_raised, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Goal</p>
                                <p class="font-bold text-gray-900 dark:text-white">GHS {{ number_format($program->amount_goal, 2) }}</p>
                            </div>
                        </div>

                        @if($program->deadline)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Deadline: {{ $program->deadline->format('M d, Y') }}</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('sponsorship.programs.show', $program) }}"
                               class="px-4 py-2 text-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
                                View Details
                            </a>
                            <a href="{{ route('sponsorship.programs.contribute', $program) }}"
                               class="px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                Contribute
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Programs Found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Check back later for new sponsorship opportunities!</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($programs->hasPages())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
@endsection
