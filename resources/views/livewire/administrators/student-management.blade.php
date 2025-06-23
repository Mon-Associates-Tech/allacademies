<div class="bg-white dark:bg-gray-900 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Student Management</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg relative mb-6 transition-colors duration-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Student Form -->
        <div class="mb-8 bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 transition-colors duration-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $isEditing ? 'Edit Student' : 'Create New Student' }}
                </h2>
                @if($isEditing)
                    <button type="button" wire:click="resetForm"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                @endif
            </div>



            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Enter student's full name">
                        @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="student@example.com">
                        @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password {{ $isEditing ? '(leave blank to keep current)' : '' }} <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Enter password">
                        @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student Group (Optional)
                        </label>
                        <select wire:model="studentGroupId"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">-- Select Student Group --</option>
                            @foreach($studentGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('studentGroupId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Academic Assignment</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Academic Group <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="academicGroupId"
                                    wire:key="academic-group-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                <option value="">-- Select Academic Group --</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('academicGroupId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Academic Level <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="academicLevelId"
                                    wire:key="academic-level-select-{{ $academicGroupId }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                                    @if(!$academicGroupId || $academicLevels->isEmpty()) disabled @endif>
                                <option value="">
                                    @if(!$academicGroupId)
                                        -- Select Academic Group First --
                                    @else
                                        -- Select Academic Level --
                                    @endif
                                </option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('academicLevelId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Teacher Assignment -->
                    @if(!empty($availableTeachers) && count($availableTeachers) > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Teacher Assignment
                            </label>

                            <!-- Teachers Selection -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-3 border border-gray-200 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 mb-4">
                                @foreach($availableTeachers as $teacher)
                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded transition-colors duration-200">
                                        <input type="checkbox" wire:model="selectedTeachers" value="{{ $teacher->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $teacher->user->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Primary Teacher Selection -->
                            @if(!empty($selectedTeachers))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Primary Teacher
                                    </label>
                                    <select wire:model="primaryTeacherId"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                        <option value="">-- Select Primary Teacher --</option>
                                        @foreach($availableTeachers->whereIn('id', $selectedTeachers) as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The primary teacher will be the main contact for this student.</p>
                                </div>
                            @endif

                            @error('selectedTeachers') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @error('primaryTeacherId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <!-- Subject Assignment -->
                    <!-- Subject Assignment Section (replace the existing subject assignment part) -->
                    @if(!empty($levelSubjects) && count($levelSubjects) > 0)
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Subject Access
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Students automatically have access to all subjects in their academic level. You can add additional subjects or remove specific ones.
                                    </p>
                                </div>
                                <button type="button" wire:click="$toggle('showIndividualSubjects')"
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    {{ $showIndividualSubjects ? 'Hide' : 'Show' }} Individual Assignments
                                </button>
                            </div>

                            <!-- Academic Level Subjects (Always included) -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Academic Level Subjects ({{ count($levelSubjects) }} subjects)
                                </h4>
                                <div class="p-3 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        @foreach($levelSubjects as $subject)
                                            <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                {{ $subject->name }}
                            </span>
                                                @if(in_array($subject->id, $removedSubjects))
                                                    <span class="text-xs text-red-600 dark:text-red-400">(Removed)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if($showIndividualSubjects)
                                <!-- Individual Subject Management -->
                                <div class="space-y-4">
                                    <!-- Remove Level Subjects -->
                                    @if(!empty($levelSubjects) && count($levelSubjects) > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Remove Academic Level Subjects
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                Select subjects to remove from this student's access (overrides academic level access).
                                            </p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-32 overflow-y-auto p-3 border border-gray-200 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800">
                                                @foreach($levelSubjects as $subject)
                                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded transition-colors duration-200">
                                                        <input type="checkbox" wire:model="removedSubjects" value="{{ $subject->id }}"
                                                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $subject->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('removedSubjects') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                    @endif

                                    <!-- Add Additional Subjects -->
                                    @if(!empty($availableAdditionalSubjects) && count($availableAdditionalSubjects) > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Add Additional Subjects
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                Select subjects from other academic levels to add to this student's access.
                                            </p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-3 border border-gray-200 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800">
                                                @foreach($availableAdditionalSubjects as $subject)
                                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded transition-colors duration-200">
                                                        <input type="checkbox" wire:model="additionalSubjects" value="{{ $subject->id }}"
                                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $subject->name }}</span>
                                                        <span class="text-xs text-gray-400">({{ $subject->academicLevel->name ?? 'Other Level' }})</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('additionalSubjects') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $isEditing ? 'Update Student' : 'Create Student' }}
                    </button>
                </div>
            </form>
            <!-- Teacher Management Button (add this near your form) -->
            @if($academicGroupId)
                <div class="mb-4">
                    <button type="button" wire:click="showManageTeachersModal"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Manage Teachers for Selected Group/Level
                    </button>
                </div>
            @endif

            <!-- Create Teacher Button -->
            <div class="mb-4">
                <button type="button" wire:click="showCreateTeacherModal"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create New Teacher
                </button>
            </div>

            <!-- Teacher Management Modal -->
            @if($showManageTeachers)
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Manage Teachers</h3>

                            <!-- Academic Group Teachers -->
                            @if($academicGroupId)
                                <div class="mb-6">
                                    <h4 class="text-md font-medium text-gray-700 mb-2">Academic Group Teachers</h4>

                                    <!-- Assign Teachers to Group -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teachers to Group</label>
                                        <select wire:model="selectedTeachersForGroup" multiple class="form-multiselect w-full border rounded px-3 py-2">
                                            @foreach($teachersToAssignToGroup as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->user->name }} ({{ $teacher->user->email }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="assignTeachersToGroup"
                                                class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            Assign to Group
                                        </button>
                                    </div>

                                    <!-- Current Group Teachers -->
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-600 mb-2">Current Teachers in Group</h5>
                                        <div class="space-y-2">
                                            @foreach($groupTeachers as $teacher)
                                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                                    <span>{{ $teacher->user->name }}</span>
                                                    <button type="button" wire:click="removeTeacherFromGroup({{ $teacher->id }})"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                        Remove
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Academic Level Teachers -->
                            @if($academicLevelId)
                                <div class="mb-6">
                                    <h4 class="text-md font-medium text-gray-700 mb-2">Academic Level Teachers</h4>

                                    <!-- Assign Teachers to Level -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teachers to Level</label>
                                        <select wire:model="selectedTeachersForLevel" multiple class="form-multiselect w-full border rounded px-3 py-2">
                                            @foreach($teachersToAssignToLevel as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->user->name }} ({{ $teacher->user->email }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="assignTeachersToLevel"
                                                class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            Assign to Level
                                        </button>
                                    </div>

                                    <!-- Current Level Teachers -->
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-600 mb-2">Current Teachers in Level</h5>
                                        <div class="space-y-2">
                                            @foreach($levelTeachers as $teacher)
                                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                                    <span>{{ $teacher->user->name }}</span>
                                                    <button type="button" wire:click="removeTeacherFromLevel({{ $teacher->id }})"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                        Remove
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Modal Footer -->
                            <div class="flex justify-end">
                                <button type="button" wire:click="closeManageTeachersModal"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Create Teacher Modal -->
            @if($showTeacherModal)
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Teacher</h3>

                            <form wire:submit.prevent="createTeacher">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                    <input type="text" wire:model="teacherName"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('teacherName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                    <input type="email" wire:model="teacherEmail"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('teacherEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                    <input type="password" wire:model="teacherPassword"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('teacherPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex justify-between">
                                    <button type="button" wire:click="closeTeacherModal"
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Create Teacher
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Students List -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl transition-colors duration-200">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Students List</h2>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <input type="text" wire:model.debounce.300ms="searchTerm"
                                   placeholder="Search students, groups, levels..."
                                   class="w-full sm:w-64 px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Academic Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teachers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subjects</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $student->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $student->academicGroup?->name ?? 'Not Assigned' }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ $student->academicLevel?->name ?? 'No Level' }}</div>
                                        @if($student->studentGroup)
                                            <div class="text-xs text-blue-600 dark:text-blue-400">Group: {{ $student->studentGroup->name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($student->teachers->count() > 0)
                                            @php
                                                $primaryTeacher = $student->teachers->where('pivot.is_primary', true)->first();
                                                $secondaryTeachers = $student->teachers->where('pivot.is_primary', false);
                                            @endphp

                                            @if($primaryTeacher)
                                                <div class="flex items-center mb-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 mr-2">Primary</span>
                                                    <span class="font-medium">{{ $primaryTeacher->user->name }}</span>
                                                </div>
                                            @endif

                                            @foreach($secondaryTeachers as $teacher)
                                                <div class="text-xs text-gray-600 dark:text-gray-400 ml-2">
                                                    {{ $teacher->user->name }}
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">No teachers assigned</span>
                                        @endif
                                    </div>
                                </td>
                                <!-- In the table subjects column, update to: -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @php
                                            $subjectDetails = $student->getSubjectDetails();
                                            $levelSubjectsCount = $subjectDetails['level_subjects']->count();
                                            $individualActive = $subjectDetails['individual_active']->count();
                                            $individualRemoved = $subjectDetails['individual_removed']->count();
                                            $totalAccessible = $subjectDetails['total_accessible']->count();
                                        @endphp

                                        <div class="space-y-1">
                                            <div class="flex flex-wrap gap-1">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                    {{ $totalAccessible }} Total
                </span>
                                                @if($levelSubjectsCount > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        {{ $levelSubjectsCount - $individualRemoved }} from Level
                    </span>
                                                @endif
                                                @if($individualActive > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-200">
                        +{{ $individualActive }} Individual
                    </span>
                                                @endif
                                                @if($individualRemoved > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                        -{{ $individualRemoved }} Removed
                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="edit({{ $student->id }})"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $student->id }})"
                                                onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm">No students found matching your search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
