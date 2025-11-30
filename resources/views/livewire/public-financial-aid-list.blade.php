<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                Financial Aid Programs
            </h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-400">
                Supporting education through community contribution.
            </p>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($aids as $aid)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300 border border-gray-100 dark:border-gray-700 flex flex-col" x-data="{ expanded: false }">

                    <!-- Card Header -->
                    <div class="p-6 flex-grow">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200">
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
                                <span class="text-gray-500 dark:text-gray-400">Raised: GHS {{ number_format($aid->realized_amount, 2) }}</span>
                                <span class="text-violet-600 dark:text-violet-400">{{ $aid->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-violet-600 h-2.5 rounded-full transition-all duration-1000"
                                     style="width: {{ $aid->progress_percentage }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>Goal: GHS {{ number_format($aid->goal_amount, 2) }}</span>
                                <span class="font-semibold {{ $aid->left_amount > 0 ? 'text-red-500' : 'text-green-500' }}">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="expanded"
                             x-collapse
                             class="px-6 pb-6">
                            <div class="space-y-3">
                                @forelse($aid->beneficiaries as $student)
                                    <div class="flex items-center space-x-3 p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-violet-100 dark:bg-violet-900 flex items-center justify-center text-violet-600 dark:text-violet-300 text-xs font-bold">
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

                    <!-- Donate Action (Optional Placeholder) -->
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('payments.public.lookup', ['payment_code' => $aid->code]) }}"
                           class="block w-full py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-center rounded-lg text-sm font-medium transition-colors shadow-md hover:shadow-lg">
                            Donate to {{ $aid->code }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No active programs</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check back later for new financial aid opportunities.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $aids->links() }}
        </div>
    </div>
</div>
