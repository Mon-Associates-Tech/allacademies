<div
    class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-blue-950 dark:to-indigo-950">
    <div class="relative">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\" 60\
        " height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\"
        fill-rule=\"evenodd\"%3E%3Cg fill=\"%239C92AC\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M36
        34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6
        4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <div class="relative px-4 sm:px-6 lg:px-8 py-8">
        @if(session('message'))
            <div
                class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 backdrop-blur-sm border-l-4 border-green-400 text-green-700 dark:text-green-200 rounded-lg shadow-sm">
                {{ session('message') }}
            </div>
        @endif
        @if(session('error'))
            <div
                class="mb-6 p-4 bg-red-50 dark:bg-red-900/50 backdrop-blur-sm border-l-4 border-red-400 text-red-700 dark:text-red-200 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <div
                    class="p-3 bg-gradient-to-br from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Benefactor Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Manage your sponsorship projects and track
                        contributions</p>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Projects</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_projects'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Active Projects</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['active_projects'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Raised</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            GHS {{ number_format($stats['total_raised'], 2) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stats['total_contributions'] }}
                            contributions</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pending Review</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pending_verification'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Setup Alert -->
        @php
            $hasPaymentSetup = auth()->user()->subaccount !== null;
        @endphp
        @if(!$hasPaymentSetup)
            <div
                class="bg-yellow-50/80 dark:bg-yellow-900/50 backdrop-blur-sm border-l-4 border-yellow-400 p-4 mb-6 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Payment Setup Required</h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Set up your bank account to receive contributions directly.
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('benefactors.payment-setup') }}"
                               class="text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-600 underline">
                                Set up payment →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full md:w-auto">
                <div class="relative w-full sm:w-64">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" wire:model.live="search" placeholder="Search projects..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <select wire:model.live="statusFilter"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="pending_verification">Pending Verification</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <a href="{{ route('benefactors.projects.create') }}"
               class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Project
            </a>
        </div>

        <!-- Projects List -->
        <div
            class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg overflow-hidden border border-gray-200/50 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Project
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Amount Raised
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($projects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex-1">
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $project->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                        {{ ucfirst($project->type) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
                                        'pending_verification' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                 style="width: {{ $project->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $project->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    GHS {{ number_format($project->amount_raised, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    of GHS {{ number_format($project->amount_goal, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('benefactors.projects.edit', $project) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-white hover:bg-blue-600 dark:text-blue-400 dark:hover:bg-blue-500 border border-blue-600 dark:border-blue-400 rounded transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    @if($project->status === 'draft')
                                        <button wire:click="submitForVerification({{ $project->id }})"
                                                wire:confirm="Are you sure you want to submit this project for verification?"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 hover:text-white hover:bg-green-600 dark:text-green-400 dark:hover:bg-green-500 border border-green-600 dark:border-green-400 rounded transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Submit
                                        </button>
                                    @endif
                                    @if($project->status === 'draft')
                                        <button wire:click="deleteProject({{ $project->id }})"
                                                wire:confirm="Are you sure you want to delete this project? This action cannot be undone."
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 dark:text-red-400 dark:hover:bg-red-500 border border-red-600 dark:border-red-400 rounded transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-4 text-gray-500 dark:text-gray-400 font-medium">No projects found</p>
                                <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Create your first project to
                                    get started!</p>
                                <a href="{{ route('benefactors.projects.create') }}"
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create Project
                                </a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</div>
