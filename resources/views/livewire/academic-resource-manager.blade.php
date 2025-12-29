<div class="academic-resource-manager">
    <div class="container mx-auto px-4 py-6">
        {{-- Index View - Show Academic Groups --}}
        @if($viewType === 'index')
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Academic Resources</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Browse resources by academic hierarchy</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($academicGroups as $group)
                    <a href="{{ route('academic-resources.group', $group) }}"
                       class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors duration-200">
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $group->name }}</h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">
                            {{ $group->academicLevels->count() }} levels
                        </p>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No academic groups</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No academic groups available.</p>
                    </div>
                @endforelse
            </div>
        @else
            {{-- Context View - Show Resources, Notes, Todos --}}
            @if($contextModel)
                @switch($viewType)
                    @case('group')
                        @include('academic-resources.group-view', [
                            'group' => $contextModel,
                            'resources' => $resources,
                            'notes' => $notes,
                            'todos' => $todos
                        ])
                        @break
                    @case('level')
                        @include('academic-resources.level-view', [
                            'level' => $contextModel,
                            'resources' => $resources,
                            'notes' => $notes,
                            'todos' => $todos
                        ])
                        @break
                    @case('subject')
                        @include('academic-resources.subject-view', [
                            'subject' => $contextModel,
                            'resources' => $resources,
                            'notes' => $notes,
                            'todos' => $todos
                        ])
                        @break
                    @case('topic')
                        @include('academic-resources.topic-view', [
                            'topic' => $contextModel,
                            'resources' => $resources,
                            'notes' => $notes,
                            'todos' => $todos
                        ])
                        @break
                    @case('subtopic')
                        @include('academic-resources.subtopic-view', [
                            'subtopic' => $contextModel,
                            'resources' => $resources,
                            'notes' => $notes,
                            'todos' => $todos
                        ])
                        @break
                @endswitch
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Not found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The requested resource could not be found.</p>
                    <div class="mt-6">
                        <a href="{{ route('academic-resources.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to Resources
                        </a>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Upload Resource Modal --}}
    @if($showUploadModal)
        <x-modal-component name="upload-resource" :show="true" size="lg" title="Upload Resource">
            <form wire:submit.prevent="uploadResource">
                <div class="space-y-4">
                    <div>
                        <label for="resourceTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" wire:model="resourceTitle" id="resourceTitle"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                               placeholder="Enter resource title">
                        @error('resourceTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="resourceDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea wire:model="resourceDescription" id="resourceDescription" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                  placeholder="Enter resource description (optional)"></textarea>
                        @error('resourceDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="uploadFile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File</label>
                        <input type="file" wire:model="uploadFile" id="uploadFile"
                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                      dark:file:bg-gray-700 dark:file:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Supported: PDF, Word, Excel, PowerPoint, Images, Text (Max 100MB)
                        </p>
                        @error('uploadFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" wire:model="resourceIsPublic" id="resourceIsPublic"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="resourceIsPublic" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Make this resource public
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showUploadModal', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Upload
                    </button>
                </div>
            </form>
        </x-modal-component>
    @endif

    {{-- Create Note Modal --}}
    @if($showNoteModal)
        <x-modal-component name="create-note" :show="true" size="lg" title="Create Note">
            <form wire:submit.prevent="createNote">
                <div class="space-y-4">
                    <div>
                        <label for="noteTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" wire:model="noteTitle" id="noteTitle"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                               placeholder="Enter note title">
                        @error('noteTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="noteContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                        <textarea wire:model="noteContent" id="noteContent" rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                  placeholder="Enter note content"></textarea>
                        @error('noteContent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" wire:model="noteIsPublic" id="noteIsPublic"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="noteIsPublic" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Make this note public
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showNoteModal', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create Note
                    </button>
                </div>
            </form>
        </x-modal-component>
    @endif

    {{-- Create Todo Modal --}}
    @if($showTodoModal)
        <x-modal-component name="create-todo" :show="true" size="lg" title="Create Todo">
            <form wire:submit.prevent="createTodo">
                <div class="space-y-4">
                    <div>
                        <label for="todoTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" wire:model="todoTitle" id="todoTitle"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                               placeholder="Enter todo title">
                        @error('todoTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="todoDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea wire:model="todoDescription" id="todoDescription" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                  placeholder="Enter todo description (optional)"></textarea>
                        @error('todoDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="todoPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <select wire:model="todoPriority" id="todoPriority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div>
                            <label for="todoDueDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                            <input type="date" wire:model="todoDueDate" id="todoDueDate"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" wire:model="todoIsPrivate" id="todoIsPrivate"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="todoIsPrivate" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Keep this todo private (only visible to you)
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showTodoModal', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Create Todo
                    </button>
                </div>
            </form>
        </x-modal-component>
    @endif
</div>
