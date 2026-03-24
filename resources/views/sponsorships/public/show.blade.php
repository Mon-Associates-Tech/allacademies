<x-layouts.app>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-blue-950 dark:to-indigo-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <a href="{{ route('sponsorships.projects.index') }}"
                   class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    ← Back to Project
                </a>
            </nav>

            <div
                class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 p-8 lg:p-12">
                        <div class="mb-4">
                                <span
                                    class="inline-block px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 rounded-full uppercase tracking-wide">
                                    {{ ucfirst($project->type) }} Project
                                </span>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $project->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed mb-8">{{ $project->description }}</p>

                        <div class="grid grid-cols-2 gap-6 mb-12 pb-8 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Program Code</span>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $project->code }}</p>
                            </div>
                            @if($project->school)
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">School</span>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $project->school->name }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Beneficiaries -->
                        @if($project->beneficiaries && $project->beneficiaries->where('name', '!=', null)->where('name', '!=', '')->count() > 0)
                            <div class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Beneficiaries
                                    ({{ $project->beneficiaries->where('name', '!=', null)->where('name', '!=', '')->count() }}
                                    )</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($project->beneficiaries->where('name', '!=', null)->where('name', '!=', '') as $beneficiary)
                                        <div
                                            class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $beneficiary->name }}</p>
                                            @if($beneficiary->student_id)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Student
                                                    ID: {{ $beneficiary->student_id }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Recent Contributions -->
                        @if($project->contributions && $project->contributions->count() > 0)
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                                    Recent Contributions
                                    @if($project->contributions_count > $project->contributions->count())
                                        <span class="text-base font-normal text-gray-500 dark:text-gray-400">(Showing {{ $project->contributions->count() }} of {{ $project->contributions_count }})</span>
                                    @endif
                                </h2>
                                <div class="space-y-4">
                                    @foreach($project->contributions as $contribution)
                                        <div
                                            class="flex justify-between items-center py-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                    <span
                                                        class="text-sm font-bold text-blue-600 dark:text-blue-300">{{ substr($contribution->payer_name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $contribution->payer_name }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contribution->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <span
                                                class="text-lg font-bold text-gray-900 dark:text-white">GHS {{ number_format($contribution->amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div
                        class="bg-gray-50/50 dark:bg-gray-900/50 p-8 lg:p-12 border-l border-gray-200 dark:border-gray-700">
                        <!-- Funding Progress -->
                        <div class="mb-8">
                            <div class="mb-6">
                                <span
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Amount Raised</span>
                                <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                    GHS {{ number_format($project->amount_raised, 2) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">of
                                    GHS {{ number_format($project->amount_goal, 2) }} goal</p>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                <div
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 h-3 rounded-full transition-all duration-500"
                                    style="width: {{ min($project->progress_percentage, 100) }}%"></div>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $project->progress_percentage }}
                                % funded</p>
                        </div>

                        <div class="space-y-4 mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Contributors</span>
                                <span
                                    class="text-lg font-bold text-gray-900 dark:text-white">{{ $project->contributions_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                        @if($project->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                            </div>
                        </div>

                        <a href="{{ route('sponsorships.projects.contribute', $project) }}"
                           class="block w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center font-bold rounded-lg hover:from-blue-700 hover:to-indigo-700 transition shadow-lg mb-8">
                            Contribute to This Program
                        </a>

                        <!-- Organizer -->
                        <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">
                                Organized By</h3>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-lg font-bold text-white">{{ substr($project->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $project->user->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Program Organizer</p>
                                </div>
                            </div>
                        </div>

                        <!-- Share -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">
                                Share Program</h3>
                            <button
                                onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!')"
                                class="w-full py-3 px-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
