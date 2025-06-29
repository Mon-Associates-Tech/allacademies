<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Teacher Management</h1>
            <p class="mt-2 text-gray-600">Manage teachers, their specializations, and subject assignments</p>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Create/Edit Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $isEditing ? 'Edit Teacher' : 'Create New Teacher' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $isEditing ? 'Update the teacher information below' : 'Fill in the details to create a new teacher account' }}
                </p>
            </div>

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>

                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter teacher's full name"
                                    >
                                    @error('name')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        wire:model="email"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter email address"
                                    >
                                    @error('email')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Password
                                        @if($isEditing)
                                            <span class="text-gray-500 text-xs">(leave blank to keep current)</span>
                                        @else
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="password"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="{{ $isEditing ? 'Leave blank to keep current password' : 'Enter password (min 8 characters)' }}"
                                    >
                                    @error('password')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <!-- Specialization -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                                    <input
                                        type="text"
                                        wire:model="specialization"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="e.g., Mathematics, Science, History"
                                    >
                                    @error('specialization')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Academic Information</h3>

                            <!-- Subjects -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teaching Subjects</label>
                                <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto bg-gray-50">
                                    @if($subjects->count() > 0)
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach($subjects as $subject)
                                                <label class="flex items-center p-2 hover:bg-white rounded-md transition-colors cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="subjectIds"
                                                        value="{{ $subject->id }}"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-3"
                                                    >
                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                                                        @if($subject->description)
                                                            <div class="text-xs text-gray-500">{{ $subject->description }}</div>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-sm text-center py-4">No subjects available</p>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Select the subjects this teacher will be teaching</p>
                            </div>

                            <!-- Biography -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biography</label>
                                <textarea
                                    wire:model="biography"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Enter teacher's background, experience, and qualifications"
                                ></textarea>
                                @error('biography')
                                <p class="text-red-500 text-sm mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    @if($isEditing)
                        <button
                            type="button"
                            wire:click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Cancel
                        </button>
                    @endif
                    <button
                        type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                        {{ $isEditing ? 'Update Teacher' : 'Create Teacher' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Teachers List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Teachers List</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage existing teachers</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model.debounce.300ms="searchTerm"
                                placeholder="Search teachers..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden">
                @if($teachers->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teacher Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact & Specialization
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teaching Subjects
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Groups & Stats
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($teachers as $teacher)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ substr($teacher->user->name, 0, 2) }}
                                                </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $teacher->user->name }}</div>
                                            <div class="text-sm text-gray-500">Teacher ID: #{{ str_pad($teacher->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $teacher->user->email }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $teacher->specialization ?? 'No specialization' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($teacher->subjects as $subject)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $subject->name }}
                                                </span>
                                        @empty
                                            <span class="text-sm text-gray-500">No subjects assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center text-sm text-gray-900">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ $teacher->studentGroups->count() }} groups
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $teacher->studentsFromGroups->count() }} students
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <button
                                            wire:click="edit({{ $teacher->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium transition-colors"
                                            title="Edit Teacher"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            wire:click="showTeacherDetails({{ $teacher->id }})"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium transition-colors"
                                            title="View Details"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </button>
                                        <button
                                            wire:click="confirmDelete({{ $teacher->id }})"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium transition-colors"
                                            title="Delete Teacher"
                                        >
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">No teachers found</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($searchTerm)
                                Try adjusting your search terms.
                            @else
                                Get started by creating a new teacher.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            @if($teachers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>

        <!-- Teacher Details Modal -->
        @if($showTeacherModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Teacher Details</h3>
                                <p class="mt-1 text-sm text-gray-600">Complete information about the teacher</p>
                            </div>
                            <button
                                wire:click="closeTeacherModal"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    @if($selectedTeacher)
                        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-900">Basic Information</h4>
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Name</label>
                                            <p class="text-sm text-gray-900">{{ $selectedTeacher->user->name }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Email</label>
                                            <p class="text-sm text-gray-900">{{ $selectedTeacher->user->email }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Specialization</label>
                                            <p class="text-sm text-gray-900">{{ $selectedTeacher->specialization ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistics -->
                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-900">Statistics</h4>
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Student Groups</span>
                                            <span class="text-sm text-gray-900">{{ $selectedTeacher->studentGroups->count() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Total Students</span>
                                            <span class="text-sm text-gray-900">{{ $selectedTeacher->studentsFromGroups->count() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Teaching Subjects</span>
                                            <span class="text-sm text-gray-900">{{ $selectedTeacher->subjects->count() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Biography -->
                                @if($selectedTeacher->biography)
                                    <div class="lg:col-span-2 space-y-4">
                                        <h4 class="font-medium text-gray-900">Biography</h4>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-sm text-gray-700">{{ $selectedTeacher->biography }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Subjects -->
                                <div class="lg:col-span-2 space-y-4">
                                    <h4 class="font-medium text-gray-900">Teaching Subjects</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($selectedTeacher->subjects as $subject)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $subject->name }}
                                            </span>
                                        @empty
                                            <p class="text-sm text-gray-500">No subjects assigned</p>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Student Groups -->
                                <div class="lg:col-span-2 space-y-4">
                                    <h4 class="font-medium text-gray-900">Student Groups</h4>
                                    @if($selectedTeacher->studentGroups->count() > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($selectedTeacher->studentGroups as $group)
                                                <div class="bg-gray-50 rounded-lg p-3">
                                                    <div class="font-medium text-gray-900">{{ $group->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $group->students->count() }} students</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">No student groups assigned</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Teacher</h3>
                            <p class="text-sm text-gray-500 mb-6">
                                Are you sure you want to delete this teacher? This action cannot be undone and will also delete their user account.
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button
                                wire:click="closeDeleteModal"
                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Cancel
                            </button>
                            <button
                                wire:click="deleteTeacher"
                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
