<div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                    Financial Aid Programs
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-400">
                    Supporting education through community contribution.
                </p>
            </div>
            
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- School Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Schools
                        </label>
                        <livewire:common.searchable-multi-select 
                            :items="$schools->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray()" 
                            :selected="$selectedSchools"
                            placeholder="Select schools..." 
                            :multiple="true"
                            size="sm"
                            name="selectedSchools"
                        />
                    </div>
                    
                    <!-- Search Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search Programs
                        </label>
                        <input 
                            type="text" 
                            wire:model.live="search"
                            placeholder="Search by name, code, or description..." 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status
                        </label>
                        <select 
                            wire:model.live="status"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    
                    <!-- Sort Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sort By
                        </label>
                        <select 
                            wire:model.live="sortBy"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="latest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name">Alphabetical</option>
                            <option value="progress">Progress (Least to Most)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Clear Filters Button -->
                @if($selectedSchools || $search || $status || $sortBy !== 'latest')
                <div class="mt-4 flex justify-end">
                    <button 
                        wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500"
                    >
                        Clear Filters
                    </button>
                </div>
                @endif
            </div>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($aids as $aid)
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300 border border-gray-100 dark:border-gray-700 flex flex-col"
                    x-data="{ expanded: false }">

                    <!-- Card Header -->
                    <div class="p-6 flex-grow">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200">
                                {{ $aid->code }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $aid->school->name ?? 'All Academies' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $aid->name }}
                        </h3>

                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 line-clamp-3">
                            {{ $aid->description }}
                        </p>

                        <!-- Progress Section -->
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-sm font-medium">
                                <span
                                    class="text-gray-500 dark:text-gray-400">Raised: GHS {{ number_format($aid->realized_amount, 2) }}</span>
                                <span
                                    class="text-violet-600 dark:text-violet-400">{{ $aid->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-blue-500 to-violet-600 h-2.5 rounded-full transition-all duration-1000"
                                    style="width: {{ $aid->progress_percentage }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>Goal: GHS {{ number_format($aid->goal_amount, 2) }}</span>
                                <span
                                    class="font-semibold {{ $aid->left_amount > 0 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $aid->left_amount > 0 ? 'Needed: GHS ' . number_format($aid->left_amount, 2) : 'Goal Met!' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficiaries Section (Accordion) -->
                    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <button @click="expanded = !expanded"
                                class="w-full px-6 py-4 flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus:outline-none">
                            <span>
                                Beneficiaries ({{ $aid->beneficiaries->count() }})
                            </span>
                            <svg class="w-5 h-5 transform transition-transform duration-200"
                                 :class="{ 'rotate-180': expanded }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="expanded"
                             x-collapse
                             class="px-6 pb-6">
                            <div class="space-y-3">
                                @forelse($aid->beneficiaries as $student)
                                    <div
                                        class="flex items-center space-x-3 p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                        <div
                                            class="flex-shrink-0 h-8 w-8 rounded-full bg-violet-100 dark:bg-violet-900 flex items-center justify-center text-violet-600 dark:text-violet-300 text-xs font-bold">
                                            {{ substr($student->user->name ?? 'S', 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $student->user->name ?? 'Unknown Student' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                ID: {{ $student->student_id }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">No beneficiaries listed yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Donate Action -->
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('payments.public.lookup', ['payment_code' => $aid->code]) }}"
                           class="block w-full py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-center rounded-lg text-sm font-medium transition-colors shadow-md hover:shadow-lg">
                            Donate to {{ $aid->code }}
                        </a>
                    </div>
                </div>
            @empty
                <!-- Empty State with Call to Action -->
                <div class="col-span-full">
                    <div class="max-w-md mx-auto text-center">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                            <!-- Icon -->
                            <div
                                class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-violet-100 dark:bg-violet-900/30 mb-4">
                                <svg class="h-10 w-10 text-violet-600 dark:text-violet-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <!-- Heading -->
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                No Needy Students Found
                            </h3>

                            <!-- Description -->
                            <p class="text-gray-600 dark:text-gray-400 mb-6 text-lg">
                                @if($selectedSchools || $search || $status)
                                    No programs match your current filters. Try adjusting your search criteria.
                                @else
                                    No needy students at the moment. Check back soon! If  you are looking to
                                    make other payments, click the button below.
                                @endif
                            </p>

                            <!-- CTA Button -->
                            <div class="flex flex-col sm:flex-row justify-center gap-4">
                                <a href="{{ route('payments.public.lookup') }}"
                                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-violet-600 hover:bg-violet-700 transition-colors shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Make Payment
                                </a>
                                
                                @if($selectedSchools || $search || $status)
                                <button
                                    wire:click="clearFilters"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                                    Clear Filters
                                </button>
                                @endif
                            </div>

                            <!-- Secondary Info -->
                            <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                                Your support makes a difference in students' educational journeys
                            </p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($aids->hasPages())
            <div class="mt-12">
                {{ $aids->links() }}
            </div>
        @endif
    </div>
</div>
