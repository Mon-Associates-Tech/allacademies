@php use App\Models\AcademicTopic; @endphp
@php use App\Models\AcademicSubject; @endphp
<div class="space-y-6">
    <!-- Header with Breadcrumb Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                    @if($viewingTopicId)
                        <button wire:click="backToSubjects" class="hover:text-gray-700 transition-colors">Subject
                            Management
                        </button>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <button wire:click="backToTopics"
                                class="hover:text-gray-700 transition-colors">{{ optional(AcademicSubject::find($viewingSubjectId))->name }}</button>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-900 font-medium">{{ optional(AcademicTopic::find($viewingTopicId))->name }} - Subtopics</span>
                    @elseif($viewingSubjectId)
                        <button wire:click="backToSubjects" class="hover:text-gray-700 transition-colors">Subject
                            Management
                        </button>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-900 font-medium">{{ optional(AcademicSubject::find($viewingSubjectId))->name }} - Topics</span>
                    @else
                        {{--                        <span class="text-gray-900 font-medium">Subject Management</span>--}}
                    @endif
                </nav>

                <h1 class="text-3xl font-bold text-gray-900">
                    @if($viewingTopicId)
                        <div class="flex items-center">
                            <button wire:click="backToTopics"
                                    class="mr-3 p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            Subtopics Management
                        </div>
                    @elseif($viewingSubjectId)
                        <div class="flex items-center">
                            <button wire:click="backToSubjects"
                                    class="mr-3 p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            Topics Management
                        </div>
                    @else
                        Subject Management
                    @endif
                </h1>

                @if($viewingTopicId)
                    <p class="text-gray-600 mt-1">Manage subtopics
                        for {{ optional(AcademicTopic::find($viewingTopicId))->name }}</p>
                @elseif($viewingSubjectId)
                    <p class="text-gray-600 mt-1">Manage topics
                        for {{ optional(AcademicSubject::find($viewingSubjectId))->name }}</p>
                @else
                    <p class="text-gray-600 mt-1">Create and manage academic subjects, topics, and subtopics</p>
                @endif
            </div>

            <!-- Quick Stats -->
            @unless($viewingTopicId || $viewingSubjectId)
                <div class="flex justify-end">
                    <x-button.primary onclick="window.Modal.open('subject-management-form')">
                        Create New Subject
                    </x-button.primary>
                </div>
            @endunless
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($viewingTopicId)
        <!-- Subtopics Management Section -->
        <div class="space-y-6">
            <!-- Add Subtopic Button -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Subtopics</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $subtopics->count() }} subtopic(s) available</p>
                </div>
                <button wire:click="showCreateSubtopicForm"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Subtopic
                </button>
            </div>

            <!-- Subtopic Form -->
            @if($showSubtopicForm)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $isEditingSubtopic ? 'Edit Subtopic' : 'Create New Subtopic' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <form wire:submit.prevent="{{ $isEditingSubtopic ? 'updateSubtopic' : 'createSubtopic' }}"
                              class="space-y-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtopic Name *</label>
                                    <input type="text" wire:model="subtopicName"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="Enter subtopic name">
                                    @error('subtopicName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                                    <input type="text" wire:model="subtopicSlug"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 dark:text-gray-400 text-gray-500"
                                           readonly>
                                    @error('subtopicSlug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea wire:model="subtopicDescription" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                          placeholder="Enter subtopic description (optional)"></textarea>
                                @error('subtopicDescription')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="resetSubtopicForm"
                                        class="px-4 py-2 text-gray-700 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    {{ $isEditingSubtopic ? 'Update Subtopic' : 'Create Subtopic' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Subtopics List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                @if($subtopics->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No subtopics</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new subtopic.</p>
                        <div class="mt-6">
                            <button wire:click="showCreateSubtopicForm"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Subtopic
                            </button>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subtopics as $subtopic)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $subtopic->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $subtopic->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ Str::limit($subtopic->description ?? 'No description available', 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="editSubtopic({{ $subtopic->id }})"
                                                    class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 px-2 py-1 rounded transition-colors">
                                                Edit
                                            </button>
                                            <button wire:click="deleteSubtopic({{ $subtopic->id }})"
                                                    class="text-red-600 hover:text-red-900 hover:bg-red-50 px-2 py-1 rounded transition-colors"
                                                    onclick="return confirm('Are you sure you want to delete this subtopic?')">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    @elseif($viewingSubjectId)
        <!-- Topics Management Section -->
        <div class="space-y-6">
            <!-- Add Topic Button -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Topics</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $topics->count() }} topic(s) available</p>
                </div>
                <button wire:click="showCreateTopicForm"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Topic
                </button>
            </div>

            <!-- Topic Form -->
            @if($showTopicForm)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $isEditingTopic ? 'Edit Topic' : 'Create New Topic' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <form wire:submit.prevent="{{ $isEditingTopic ? 'updateTopic' : 'createTopic' }}"
                              class="space-y-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Topic Name *</label>
                                    <input type="text" wire:model="topicName"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="Enter topic name">
                                    @error('topicName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                                    <input type="text" wire:model="topicSlug"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 dark:text-gray-400 text-gray-500"
                                           readonly>
                                    @error('topicSlug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea wire:model="topicDescription" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                          placeholder="Enter topic description (optional)"></textarea>
                                @error('topicDescription')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="resetTopicForm"
                                        class="px-4 py-2 text-gray-700 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    {{ $isEditingTopic ? 'Update Topic' : 'Create Topic' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Topics List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                @if($topics->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No topics</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new topic.</p>
                        <div class="mt-6">
                            <button wire:click="showCreateTopicForm"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Topic
                            </button>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtopics
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topics as $topic)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $topic->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $topic->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ Str::limit($topic->description ?? 'No description available', 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $topic->subtopics_count }} subtopic(s)
                                            </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="viewTopicSubtopics({{ $topic->id }})"
                                                    class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 px-2 py-1 rounded transition-colors">
                                                Subtopics
                                            </button>
                                            <button wire:click="editTopic({{ $topic->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 px-2 py-1 rounded transition-colors">
                                                Edit
                                            </button>
                                            <button wire:click="deleteTopic({{ $topic->id }})"
                                                    class="text-red-600 hover:text-red-900 hover:bg-red-50 px-2 py-1 rounded transition-colors"
                                                    onclick="return confirm('Are you sure you want to delete this topic?')">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    @else

        <div class="space-y-6">

            <x-modal-component name="subject-management-form" size="2xl">
                <x-slot:header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ $isEditingSubject ? 'Edit Subject' : 'Create New Subject' }}
                    </h2>
                </x-slot:header>
                <form id="subject-management-form"
                      wire:submit.prevent="{{ $isEditingSubject ? 'updateSubject' : 'createSubject' }}"
                      class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Academic Group *</label>
                            <select wire:model="academicGroupId"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Select Academic Group</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('academicGroupId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Academic Level *</label>
                            <select wire:model="academicLevelId"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Select Academic Level</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('academicLevelId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-form.input type="text" wire:model="name" name="name" label="Subject Name" required
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                          placeholder="Enter subject name">

                            </x-form.input>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-form.input type="text" wire:model="subjectCode" name="subjectCode" label="Subject Code" required placeholder="Enter subject code"
                                          class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                          ></x-form.input>
                            @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-form.input type="text" wire:model="slug" name="slug"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 dark:text-gray-400 text-gray-500"
                                          readonly></x-form.input>
                            @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea wire:model="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                  placeholder="Enter subject description (optional)"></textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
                <x-slot:footer>
                    <div class="flex justify-end">
                        @if($isEditingSubject)
                            <x-button.white wire:click="resetSubjectForm">
                                Cancel
                            </x-button.white>
                        @else
                            <x-button.white onclick="window.Modal.close('subject-management-form')">
                                Close
                            </x-button.white>
                        @endif

                        <x-button.primary class="ml-4" form="subject-management-form" type="submit">
                            {{ $isEditingSubject ? 'Update Subject' : 'Create Subject' }}
                        </x-button.primary>
                    </div>

                </x-slot:footer>
            </x-modal-component>


            <!-- Subjects List -->
            <div class="bg-white rounded-b-lg shadow-sm border-b border-gray-200 overflow-hidden">
                <!-- Search and Filters -->
                <div class="bg-white border-t border-gray-200 rounded-t-lg shadow-sm p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                        <h2 class="text-lg font-medium text-gray-900">Subjects List</h2>
                        <div class="flex space-x-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" wire:model.debounce.300ms="searchTerm"
                                       placeholder="Search subjects..."
                                       class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
                @if($subjectsList->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($searchTerm)
                                Try adjusting your search terms.
                            @else
                                Get started by creating your first subject.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Academic Info
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Topics
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subjectsList as $subject)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex">
                                            <x-avatar  name="{{$subject->name}}" text-size="text-xs" class="h-8 w-8" />
                                            <div class="text-sm font-medium my-auto ml-2 text-gray-900">{{ $subject->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $subject->slug }}</div>
                                        </div>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm text-gray-900">{{ $subject->academicLevel->academicGroup->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $subject->academicLevel->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ Str::limit($subject->description ?? 'No description available', 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $subject->topics_count }} topic(s)
                                            </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="viewSubjectTopics({{ $subject->id }})"
                                                    class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 px-2 py-1 rounded transition-colors">
                                                Topics
                                            </button>
                                            <button wire:click="editSubject({{ $subject->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 px-2 py-1 rounded transition-colors">
                                                Edit
                                            </button>
                                            <button wire:click="deleteSubject({{ $subject->id }})"
                                                    class="text-red-600 hover:text-red-900 hover:bg-red-50 px-2 py-1 rounded transition-colors"
                                                    onclick="return confirm('Are you sure you want to delete this subject?')">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($subjectsList->hasPages())
                <div class="bg-white px-4 py-3 border border-gray-200 rounded-lg">
                    {{ $subjectsList->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
