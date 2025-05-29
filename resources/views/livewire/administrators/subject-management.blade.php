<div>
    <h1 class="text-2xl font-bold mb-6">
        @if($viewingTopicId)
            <button wire:click="backToTopics" class="mr-2 text-blue-500 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            Subtopics - {{ optional(Topic::find($viewingTopicId))->name }}
        @elseif($viewingSubjectId)
            <button wire:click="backToSubjects" class="mr-2 text-blue-500 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            Topics - {{ optional(Subject::find($viewingSubjectId))->name }}
        @else
            Subject Management
        @endif
    </h1>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($viewingTopicId)
        <!-- Subtopics Management -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Subtopics List</h2>

                <button wire:click="showCreateSubtopicForm" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Add New Subtopic
                </button>
            </div>

            @if($showSubtopicForm)
                <div class="mb-6 bg-white p-4 rounded shadow">
                    <h3 class="text-md font-semibold mb-4">{{ $isEditingSubtopic ? 'Edit Subtopic' : 'Create New Subtopic' }}</h3>

                    <form wire:submit.prevent="{{ $isEditingSubtopic ? 'updateSubtopic' : 'createSubtopic' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtopic Name</label>
                                <input type="text" wire:model="subtopicName" class="w-full p-2 border rounded">
                                @error('subtopicName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" wire:model="subtopicSlug" class="w-full p-2 border rounded bg-gray-50" readonly>
                                @error('subtopicSlug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="subtopicDescription" rows="3" class="w-full p-2 border rounded"></textarea>
                                @error('subtopicDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                {{ $isEditingSubtopic ? 'Update Subtopic' : 'Create Subtopic' }}
                            </button>

                            <button type="button" wire:click="resetSubtopicForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                @if($subtopics->isEmpty())
                    <div class="px-6 py-4 text-center text-gray-500">No subtopics found. Create your first subtopic!</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subtopics as $subtopic)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subtopic->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ $subtopic->description ?? 'No description available' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="editSubtopic({{ $subtopic->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button wire:click="deleteSubtopic({{ $subtopic->id }})" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this subtopic?')">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @elseif($viewingSubjectId)
        <!-- Topics Management -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Topics List</h2>

                <button wire:click="showCreateTopicForm" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Add New Topic
                </button>
            </div>

            @if($showTopicForm)
                <div class="mb-6 bg-white p-4 rounded shadow">
                    <h3 class="text-md font-semibold mb-4">{{ $isEditingTopic ? 'Edit Topic' : 'Create New Topic' }}</h3>

                    <form wire:submit.prevent="{{ $isEditingTopic ? 'updateTopic' : 'createTopic' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Topic Name</label>
                                <input type="text" wire:model="topicName" class="w-full p-2 border rounded">
                                @error('topicName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" wire:model="topicSlug" class="w-full p-2 border rounded bg-gray-50" readonly>
                                @error('topicSlug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="topicDescription" rows="3" class="w-full p-2 border rounded"></textarea>
                                @error('topicDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                {{ $isEditingTopic ? 'Update Topic' : 'Create Topic' }}
                            </button>

                            <button type="button" wire:click="resetTopicForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                @if($topics->isEmpty())
                    <div class="px-6 py-4 text-center text-gray-500">No topics found. Create your first topic!</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtopics</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topics as $topic)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $topic->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ $topic->description ?? 'No description available' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ $topic->subtopics_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="viewTopicSubtopics({{ $topic->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Subtopics</button>
                                        <button wire:click="editTopic({{ $topic->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button wire:click="deleteTopic({{ $topic->id }})" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this topic?')">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @else
        <!-- Main Subject Management -->
        <!-- Subject Form -->
        <div class="mb-8 bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold mb-4">{{ $isEditingSubject ? 'Edit Subject' : 'Create New Subject' }}</h2>

            <form wire:submit.prevent="{{ $isEditingSubject ? 'updateSubject' : 'createSubject' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Academic Group</label>
                        <select wire:model="academicGroupId" class="w-full p-2 border rounded">
                            @foreach($academicGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Academic Level</label>
                        <select wire:model="academicLevelId" class="w-full p-2 border rounded">
                            @foreach($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        @error('academicLevelId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name</label>
                        <input type="text" wire:model="name" class="w-full p-2 border rounded">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" wire:model="slug" class="w-full p-2 border rounded bg-gray-50" readonly>
                        @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full p-2 border rounded"></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4 flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        {{ $isEditingSubject ? 'Update Subject' : 'Create Subject' }}
                    </button>

                    @if($isEditingSubject)
                        <button type="button" wire:click="resetSubjectForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Subjects List -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Subjects List</h2>

                <div>
                    <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search subjects..."
                        class="p-2 border rounded">
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Group/Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topics</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subjectsList as $subject)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $subject->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $subject->academicLevel->academicGroup->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $subject->academicLevel->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ $subject->description ?? 'No description available' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ $subject->topics_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="viewSubjectTopics({{ $subject->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Topics</button>
                                    <button wire:click="editSubject({{ $subject->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                    <button wire:click="deleteSubject({{ $subject->id }})" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this subject?')">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subjectsList->links() }}
            </div>
        </div>
    @endif
</div>
