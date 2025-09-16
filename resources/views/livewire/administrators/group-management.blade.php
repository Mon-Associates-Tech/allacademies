<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between">
            <div class="">
                <h1 class="text-3xl font-bold text-gray-900">Student Group Management</h1>
                <p class="mt-2 text-gray-600">Organize students into groups and assign teachers</p>
            </div>
            <div class="inline my-auto">
                <x-button.primary onclick="window.Modal.open('student-group-management-form')">Add New Group
                </x-button.primary>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <x-modal-component name="student-group-management-form">
            <x-slot:header>
                <div class="">
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ $isEditing ? 'Edit Student Group' : 'Create New Student Group' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $isEditing ? 'Update the group information below' : 'Fill in the details to create a new student group' }}
                    </p>
                </div>
            </x-slot:header>
            <form id="student-group-management-form" wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}"
                  class="">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Group Name -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Group Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter group name"
                        >
                        @error('name')
                        <p class="text-red-500 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Slug (Auto-generated) -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <input
                            type="text"
                            wire:model="slug"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            readonly disabled
                            placeholder="Auto-generated from name"
                        >
                        <p class="text-xs text-gray-500">Automatically generated from the group name</p>
                    </div>

                    <!-- Description -->
                    <div class="lg:col-span-2 space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter a brief description of the group (optional)"
                        ></textarea>
                        @error('description')
                        <p class="text-red-500 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Assign Teacher -->
                    <div class="space-y-2 lg:col-span-2">

                        @livewire('common.searchable-multi-select',
                                       [
                                       'selected' => $teacherId ? [$teacherId] : [],
                                        'multiple' => false,
                                         'items' => $teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->user->name])->toArray(),
                                          'labelKey' => 'name',
                                          'placeholder' => 'Choose or search teacher',
                                           'valueKey' => 'id',
                                            'name' => 'teacherId',
                                            'class' => 'py-3',
                                            'parentModel' => 'teacherId',

                                       ]
                                       )

                        @error('teacherId')
                        <p class="text-red-500 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </form>
            <x-slot:footer>
                <div class="flex items-center justify-end space-x-3">
                    @if($isEditing)
                        <x-button.white
                            type="button"
                            wire:click="resetForm"
                            class=""
                        >
                            Cancel
                        </x-button.white>
                    @endif
                    <x-button.primary
                        type="submit" form="student-group-management-form"
                        class=""
                    >
                        {{ $isEditing ? 'Update Group' : 'Create Group' }}
                    </x-button.primary>
                </div>
            </x-slot:footer>
        </x-modal-component>

        <!-- Groups List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Student Groups</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage existing student groups</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model.debounce.300ms="searchTerm"
                                placeholder="Search groups..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden">
                @if($groups->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Group Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teacher
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Students
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($groups as $group)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $group->name }}</div>
                                        @if($group->description)
                                            <div class="text-sm text-gray-500 mt-1">{{ $group->description }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400 mt-1">{{ $group->slug }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-blue-600">
                                                    {{ substr($group->teacher->user->name, 0, 2) }}
                                                </span>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $group->teacher->user->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $group->students->count() }} students
                                            </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <button
                                            wire:click="showStudentsInGroup({{ $group->id }})"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium transition-colors"
                                            title="Manage Students"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Manage
                                        </button>
                                        <button
                                            wire:click="edit({{ $group->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium transition-colors"
                                            title="Edit Group"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            wire:click="delete({{ $group->id }})"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium transition-colors"
                                            onclick="return confirm('Are you sure you want to delete this group? This action cannot be undone.')"
                                            title="Delete Group"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">No student groups found</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($searchTerm)
                                Try adjusting your search terms.
                            @else
                                Get started by creating a new student group.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            @if($groups->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>

        <!-- Student Management Modal -->
        @if($showStudents)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Manage Students
                                    in {{ $groups->where('id', $selectedGroupId)->first()->name ?? 'Group' }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">Add or remove students from this group</p>
                            </div>
                            <button
                                wire:click="closeStudentsModal"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Students in Group -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-900">Students in Group</h4>
                                    <span class="text-sm text-gray-500">{{ $studentsInGroup->count() }} students</span>
                                </div>
                                <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto">
                                    @if($studentsInGroup->count() > 0)
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($studentsInGroup as $student)
                                                <li class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                            <span class="text-xs font-medium text-blue-600">
                                                                {{ substr($student->user->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div
                                                                class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                                            <div
                                                                class="text-xs text-gray-500">{{ $student->user->email }}</div>
                                                        </div>
                                                    </div>
                                                    <button
                                                        wire:click="removeStudentFromGroup({{ $student->id }})"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors"
                                                        title="Remove from group"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="p-8 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <p class="mt-4 text-sm text-gray-500">No students in this group yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Available Students -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-900">Available Students</h4>
                                    <span
                                        class="text-sm text-gray-500">{{ $studentsNotInGroup->count() }} available</span>
                                </div>
                                <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto">
                                    @if($studentsNotInGroup->count() > 0)
                                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                                            <button
                                                wire:click="addStudentsToGroup"
                                                class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                @if(empty($selectedStudents)) disabled @endif
                                            >
                                                Add Selected Students ({{ count($selectedStudents) }})
                                            </button>
                                        </div>
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($studentsNotInGroup as $student)
                                                <li class="p-4 flex items-center hover:bg-gray-50 transition-colors">
                                                    <input
                                                        type="checkbox"
                                                        wire:model.live="selectedStudents"
                                                        value="{{ $student->id }}"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-3"
                                                    >
                                                    <div class="flex items-center flex-1">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                                            <span class="text-xs font-medium text-gray-600">
                                                                {{ substr($student->user->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div
                                                                class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                                            <div
                                                                class="text-xs text-gray-500">{{ $student->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="p-8 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <p class="mt-4 text-sm text-gray-500">No students available to add</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('selection-changed', (event) => {
                if (event[0].name === 'teacherId') {
                    @this.
                    set('teacherId', event.selected[0] || null);
                }
            });
        });
    </script>
</div>
