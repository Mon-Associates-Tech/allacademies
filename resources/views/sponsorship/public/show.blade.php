<x-layouts.guest>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('sponsorship.programs.index') }}" class="hover:text-blue-600">Programs</a></li>
                    <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li class="text-gray-900 dark:text-white">{{ $program->name }}</li>
                </ol>
            </nav>

            <!-- Main Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden mb-6">
                <!-- Header -->
                <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $program->name }}</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Code: {{ $program->code }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ ucfirst($program->type) }}
                    </span>
                    </div>

                    <!-- Benefactor Info -->
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Created by <strong class="text-gray-900 dark:text-white">{{ $program->user->name }}</strong></span>
                        @if($program->school)
                            <span class="mx-2">•</span>
                            <span>{{ $program->school->name }}</span>
                        @endif
                    </div>
                </div>

                <!-- Progress Section -->
                <div class="p-8 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <div class="mb-4">
                        <div class="flex justify-between items-baseline mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Funding Progress</span>
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $program->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4">
                            <div class="bg-green-600 h-4 rounded-full transition-all" style="width: {{ $program->progress_percentage }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Amount Raised</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($program->amount_raised, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Funding Goal</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($program->amount_goal, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Amount Needed</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">GHS {{ number_format($program->amount_left, 2) }}</p>
                        </div>
                    </div>

                    @if($program->deadline)
                        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-yellow-800 dark:text-yellow-200">
                                <strong>Deadline:</strong> {{ $program->deadline->format('F j, Y') }} ({{ $program->deadline->diffForHumans() }})
                            </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="p-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">About This Program</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-600 dark:text-gray-400">{{ $program->description }}</p>
                    </div>

                    @if($program->affected_individuals)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">Impact</h3>
                            <p class="text-sm text-blue-800 dark:text-blue-300">{{ $program->affected_individuals }}</p>
                        </div>
                    @endif
                </div>

                <!-- Beneficiaries -->
                @if($program->beneficiaries->count() > 0)
                    <div class="p-8 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Beneficiaries ({{ $program->beneficiaries->count() }})</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($program->beneficiaries as $beneficiary)
                                <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $beneficiary->beneficiary_name }}</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ ucfirst($beneficiary->beneficiary_type) }}</p>
                                            @if($beneficiary->beneficiary_description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $beneficiary->beneficiary_description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Contributions -->
                @if($program->contributions->count() > 0)
                    <div class="p-8 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Contributions</h2>
                        <div class="space-y-3">
                            @foreach($program->contributions as $contribution)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $contribution->payer_name ?? 'Anonymous' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contribution->paid_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-green-600 dark:text-green-400">GHS {{ number_format($contribution->amount, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- CTA -->
                <div class="p-8 bg-blue-50 dark:bg-blue-900 border-t border-blue-100 dark:border-blue-800">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Support This Program</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Your contribution can make a real difference</p>
                        <a href="{{ route('sponsorship.programs.contribute', $program) }}"
                           class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Contribute Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.guest>
