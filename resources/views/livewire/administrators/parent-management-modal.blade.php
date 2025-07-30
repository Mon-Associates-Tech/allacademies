<!-- Create Parent Modal -->
@if($showCreateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showCreateModal') }" x-show="show">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
                 @click="$wire.closeCreateModal()"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Add New Parent</h2>
                    <button @click="$wire.closeCreateModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="createParent" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="form.name"
                                   type="text"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter full name">
                            @error('form.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Relationship -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Relationship <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="form.relationship"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="parent">Parent</option>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                                <option value="other">Other</option>
                            </select>
                            @error('form.relationship') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="form.email"
                                   type="email"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter email address">
                            @error('form.email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Phone Number
                            </label>
                            <input wire:model="form.phone"
                                   type="tel"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter phone number">
                            @error('form.phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="form.password"
                                   type="password"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter password">
                            @error('form.password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="form.password_confirmation"
                                   type="password"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Confirm password">
                            @error('form.password_confirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input wire:model="form.is_active"
                               type="checkbox"
                               id="create_is_active"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="create_is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Account is active
                        </label>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                @click="$wire.closeCreateModal()"
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Create Parent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit Parent Modal -->
@if($showEditModal && $selectedParent)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showEditModal') }" x-show="show">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
                 @click="$wire.closeEditModal()"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Parent</h2>
                    <button @click="$wire.closeEditModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateParent" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="form.name"
                                   type="text"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter full name">
                            @error('form.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Relationship -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Relationship <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="form.relationship"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="parent">Parent</option>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                                <option value="other">Other</option>
                            </select>
                            @error('form.relationship') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="form.email"
                                   type="email"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter email address">
                            @error('form.email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Phone Number
                            </label>
                            <input wire:model="form.phone"
                                   type="tel"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter phone number">
                            @error('form.phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                New Password <span class="text-gray-500">(leave blank to keep current)</span>
                            </label>
                            <input wire:model="form.password"
                                   type="password"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter new password">
                            @error('form.password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Confirm New Password
                            </label>
                            <input wire:model="form.password_confirmation"
                                   type="password"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Confirm new password">
                            @error('form.password_confirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input wire:model="form.is_active"
                               type="checkbox"
                               id="edit_is_active"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="edit_is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Account is active
                        </label>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                @click="$wire.closeEditModal()"
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Update Parent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Associate Students Modal -->
@if($showAssociateModal && $selectedParent)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showAssociateModal') }" x-show="show">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
                 @click="$wire.closeAssociateModal()"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Associate Students</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Select students to associate with {{ $selectedParent->user->name }}
                        </p>
                    </div>
                    <button @click="$wire.closeAssociateModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Search Students -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Students</label>
                    <input wire:model.live.debounce.300ms="studentSearch"
                           type="text"
                           placeholder="Search students by name..."
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Students List -->
                <div class="max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    @if($availableStudents->count() > 0)
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($availableStudents as $student)
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center">
                                        <input wire:model="selectedStudents"
                                               type="checkbox"
                                               value="{{ $student->id }}"
                                               id="student_{{ $student->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                        <label for="student_{{ $student->id }}" class="ml-3 flex-1 cursor-pointer">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs mr-3">
                                                        {{ substr($student->user->name, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $student->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            @if($student->academicLevel)
                                                                {{ $student->academicLevel->name }}
                                                            @endif
                                                            @if($student->academicLevel && $student->studentGroup)
                                                                - {{ $student->studentGroup->name }}
                                                            @elseif($student->studentGroup)
                                                                {{ $student->studentGroup->name }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $student->user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $student->user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No students found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($studentSearch)
                                    Try adjusting your search criteria.
                                @else
                                    No students are available to associate.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Selected Count -->
                @if(count($selectedStudents) > 0)
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <p class="text-sm text-blue-700 dark:text-blue-200">
                            {{ count($selectedStudents) }} {{ Str::plural('student', count($selectedStudents)) }} selected
                        </p>
                    </div>
                @endif

                <!-- Modal Footer -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button @click="$wire.closeAssociateModal()"
                            class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Cancel
                    </button>
                    <button wire:click="updateStudentAssociations"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Update Associations
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Parent Detail Modal -->
@if($showDetailModal && $selectedParent)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDetailModal') }" x-show="show">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
                 @click="$wire.closeDetailModal()"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($selectedParent->user->name, 0, 2) }}
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $selectedParent->user->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($selectedParent->relationship ?? 'Parent') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button wire:click="showEditForm({{ $selectedParent->id }})"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            Edit
                        </button>
                        <button @click="$wire.closeDetailModal()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Parent Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                <span class="text-gray-900 dark:text-white">{{ $selectedParent->user->email }}</span>
                            </div>
                            @if($selectedParent->user->phone)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    <span class="text-gray-900 dark:text-white">{{ $selectedParent->user->phone }}</span>
                                </div>
                            @endif
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 dark:text-white">Joined {{ $selectedParent->created_at->format('M j, Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedParent->user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $selectedParent->user->is_active ? 'Active Account' : 'Inactive Account' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Students Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Children ({{ $selectedParent->students->count() }})
                            </h3>
                            <button wire:click="showAssociateStudents({{ $selectedParent->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Manage
                            </button>
                        </div>
                        @if($selectedParent->students->count() > 0)
                            <div class="space-y-4">
                                @foreach($selectedParent->students as $student)
                                    <div class="bg-white dark:bg-gray-600 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                                    {{ substr($student->user->name, 0, 2) }}
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</h4>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if($student->academicLevel)
                                                            {{ $student->academicLevel->name }}
                                                        @endif
                                                        @if($student->academicLevel && $student->studentGroup)
                                                            - {{ $student->studentGroup->name }}
                                                        @elseif($student->studentGroup)
                                                            {{ $student->studentGroup->name }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $student->user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ $student->user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No students associated with this parent.</p>
                                <button wire:click="showAssociateStudents({{ $selectedParent->id }})"
                                        class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Associate Students
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex justify-end">
                    <button @click="$wire.closeDetailModal()"
                            class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
