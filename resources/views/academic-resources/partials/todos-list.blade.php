@props(['todos' => [], 'canCreate' => false])

<div class="todos-list">
    {{-- Header with Actions --}}
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            @if(count($todos) > 0)
                {{-- Export Button --}}
                <button type="button"
                        wire:click="exportTodos('csv')"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export as CSV
                </button>

                {{-- Filter Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <a href="#" wire:click.prevent="filterTodos('all')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All</a>
                            <a href="#" wire:click.prevent="filterTodos('pending')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Pending</a>
                            <a href="#" wire:click.prevent="filterTodos('in_progress')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">In Progress</a>
                            <a href="#" wire:click.prevent="filterTodos('completed')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Completed</a>
                            <a href="#" wire:click.prevent="filterTodos('overdue')" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">Overdue</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($canCreate)
            <button type="button"
                    @click="$dispatch('open-modal', { name: 'create-todo' })"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Todo
            </button>
        @endif
    </div>

    {{-- Todos List --}}
    @if(count($todos) > 0)
        <div class="space-y-3">
            @foreach($todos as $todo)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 {{ $todo->is_overdue ? 'border-l-4 border-l-red-500' : '' }}">
                    <div class="p-4">
                        <div class="flex items-start">
                            {{-- Checkbox --}}
                            <div class="flex-shrink-0 mr-3">
                                <button wire:click="toggleTodoStatus({{ $todo->id }})"
                                        class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors duration-200 {{ $todo->is_completed ? 'bg-green-500 border-green-500' : 'border-gray-300 dark:border-gray-600 hover:border-green-500' }}">
                                    @if($todo->is_completed)
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </button>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium {{ $todo->is_completed ? 'text-gray-500 line-through' : 'text-gray-900 dark:text-white' }}">
                                        {{ $todo->title }}
                                    </h4>

                                    {{-- Priority Badge --}}
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $todo->priority === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                        {{ $todo->priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $todo->priority === 'low' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}">
                                        {{ ucfirst($todo->priority) }}
                                    </span>
                                </div>

                                @if($todo->description)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $todo->description }}</p>
                                @endif

                                {{-- Meta Info --}}
                                <div class="mt-2 flex items-center flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    {{-- Status --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded
                                        {{ $todo->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $todo->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $todo->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $todo->status === 'cancelled' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $todo->status)) }}
                                    </span>

                                    {{-- Due Date --}}
                                    @if($todo->due_date)
                                        <span class="inline-flex items-center {{ $todo->is_overdue ? 'text-red-600 dark:text-red-400' : '' }}">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $todo->due_date->format('M d, Y') }}
                                            @if($todo->is_overdue)
                                                <span class="ml-1 text-red-600 dark:text-red-400">(Overdue)</span>
                                            @endif
                                        </span>
                                    @endif

                                    {{-- Creator --}}
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $todo->user->name ?? 'Unknown' }}
                                    </span>

                                    {{-- Privacy --}}
                                    @if($todo->is_private)
                                        <span class="inline-flex items-center text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Private
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                            </svg>
                                            Shared
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div x-data="{ open: false }" class="relative ml-3">
                                <button @click="open = !open" class="p-1 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <a href="#" @click.prevent="$dispatch('open-modal', { name: 'view-todo', todoId: {{ $todo->id }} })" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            View Details
                                        </a>
                                        @can('update', $todo)
                                            <a href="#" @click.prevent="$dispatch('open-modal', { name: 'edit-todo', todoId: {{ $todo->id }} })" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Edit
                                            </a>
                                            <a href="#" @click.prevent="$dispatch('open-modal', { name: 'share-todo', todoId: {{ $todo->id }} })" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Share
                                            </a>
                                        @endcan
                                        @can('delete', $todo)
                                            <a href="#" wire:click.prevent="deleteTodo({{ $todo->id }})" wire:confirm="Are you sure you want to delete this todo?" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Delete
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No todos</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No todos have been created yet.</p>
            @if($canCreate)
                <div class="mt-6">
                    <button type="button"
                            @click="$dispatch('open-modal', { name: 'create-todo' })"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Todo
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
