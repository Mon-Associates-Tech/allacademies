<div>
    <h1 class="text-2xl font-bold mb-6">Student Group Management</h1>

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

    <!-- Group Form -->
    <div class="mb-8 bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Student Group' : 'Create New Student Group' }}</h2>

        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign Teacher</label>
                    <select wire:model="teacherId" class="w-full p-2 border rounded">
                        <option value="">-- Select Teacher --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                    @error('teacherId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ $isEditing ? 'Update Group' : 'Create Group' }}
                </button>

                @if($isEditing)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Groups List -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Student Groups</h2>

            <div>
                <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search groups..."
                    class="p-2 border rounded">
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($groups as $group)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $group->name }}</div>
                                <div class="text-sm text-gray-500">{{ $group->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $group->teacher->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $group->students->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="showStudentsInGroup({{ $group->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Manage Students</button>
                                <button wire:click="edit({{ $group->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $group->id }})" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this group?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $groups->links() }}
        </div>
    </div>

    <!-- Students in Group Modal -->
    @if($showStudents)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">
                        Manage Students in {{ StudentGroup::find($selectedGroupId)->name }}
                    </h3>
                    <button wire:click="closeStudentsModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-4 max-h-[600px] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Current Students in Group -->
                <div>
                    <h4 class="font-medium mb-2">Students in Group</h4>
                    <div class="border rounded max-h-[400px] overflow-y-auto">
                        @if($studentsInGroup->count() > 0)
                            <ul class="divide-y">
                                @foreach($studentsInGroup as $student)
                                    <li class="p-2 flex justify-between items-center">
                                        <span>{{ $student->user->name }}</span>
                                        <button wire:click="removeStudentFromGroup({{ $student->id }})"
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            Remove
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="p-4 text-gray-500">No students in this group.</p>
                        @endif
                    </div>
                </div>

                <!-- Add Students to Group -->
                <div>
                    <h4 class="font-medium mb-2">Add Students to Group</h4>
                    <div class="border rounded max-h-[400px] overflow-y-auto">
                        @if($studentsNotInGroup->count() > 0)
                            <ul class="divide-y">
                                @foreach($studentsNotInGroup as $student)
                                    <li class="p-2 flex items-center">
                                        <input type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}" class="mr-2">
                                        <span>{{ $student->user->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="p-2 border-t">
                                <button wire:click="addStudentsToGroup"
                                        class="w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Add Selected Students
                                </button>
                            </div>
                        @else
                            <p class="p-4 text-gray-500">No students available to add.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
