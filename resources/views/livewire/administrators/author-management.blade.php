<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Stats -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_authors'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Authors</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active_authors'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Active</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['authors_with_books'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">With Books</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['total_books'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Books</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Author Management</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage authors and their profiles</p>
                </div>

                @if(!$showForm)
                    <button
                        wire:click="showCreateForm"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                               transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Author</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Create/Edit Form -->
        @if($showForm)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $isEditing ? 'Edit Author' : 'Create New Author' }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $isEditing ? 'Update the author information below' : 'Fill in the details to create a new author account' }}
                            </p>
                        </div>

                        <button
                            wire:click="hideForm"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}" class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column: Personal Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>

                                <div class="space-y-6">
                                    <x-form.update.input
                                        field-id="author_name"
                                        label="Full Name"
                                        model="name"
                                        placeholder="Enter author's full name"
                                        :required="true"
                                    />

                                    <x-form.update.input
                                        field-id="author_email"
                                        label="Email Address"
                                        model="email"
                                        type="email"
                                        placeholder="author@example.com"
                                        :required="true"
                                    />

                                    @if(!$isEditing)
                                        <x-form.update.password-field
                                            label="Create Password"
                                            model="password"
                                            :show-strength="true"
                                            placeholder="Create a secure password"
                                        />

                                        <x-form.update.password-field
                                            label="Confirm Password"
                                            model="password_confirmation"
                                            :show-strength="false"
                                            placeholder="Re-enter the password"
                                        />
                                    @else
                                        <x-form.update.password-field
                                            label="New Password (leave blank to keep current)"
                                            model="password"
                                            :show-strength="true"
                                            placeholder="Enter new password"
                                            :required="false"
                                        />

                                        <x-form.update.password-field
                                            label="Confirm New Password"
                                            model="password_confirmation"
                                            :show-strength="false"
                                            placeholder="Confirm new password"
                                            :required="false"
                                        />
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Professional Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Professional Information</h3>

                                <div class="space-y-6">
                                    <x-form.update.input
                                        field-id="specialization"
                                        label="Specialization"
                                        model="specialization"
                                        placeholder="e.g., Fiction, Academic Writing, Poetry"
                                    />

                                    <x-form.update.input
                                        field-id="website"
                                        label="Website"
                                        model="website"
                                        type="url"
                                        placeholder="https://authorwebsite.com"
                                    />

                                    <x-form.update.file-upload
                                        label="Profile Picture"
                                        model="profileImage"
                                        accept="image/*"
                                        :preview="true"
                                        help-text="Maximum file size: 2MB. Supported formats: JPG, PNG, GIF"
                                    />

                                    <!-- Status Toggle -->
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input
                                                type="checkbox"
                                                wire:model="isActive"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Author</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full Width: Biography -->
                        <div class="lg:col-span-2">
                            <x-form.update.input
                                field-id="biography"
                                label="Biography"
                                model="biography"
                                type="textarea"
                                placeholder="Tell us about the author's background, achievements, and writing style..."
                                help-text="Maximum 2000 characters"
                            />
                        </div>

                        <!-- Social Media Links -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Social Media Links</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <x-form.update.input
                                    field-id="twitter"
                                    label="Twitter"
                                    model="socialMedia.twitter"
                                    placeholder="@username or full URL"
                                />

                                <x-form.update.input
                                    field-id="linkedin"
                                    label="LinkedIn"
                                    model="socialMedia.linkedin"
                                    placeholder="LinkedIn profile URL"
                                />

                                <x-form.update.input
                                    field-id="facebook"
                                    label="Facebook"
                                    model="socialMedia.facebook"
                                    placeholder="Facebook page URL"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <x-form.update.form-buttons
                        :is-editing="$isEditing"
                        submit-text="{{ $isEditing ? 'Update Author' : 'Create Author' }}"
                        loading-text="{{ $isEditing ? 'Updating Author...' : 'Creating Author...' }}"
                        submit-target="{{ $isEditing ? 'update' : 'create' }}"
                        cancel-action="hideForm"
                        reset-action="resetForm"
                    />
                </form>
            </div>
        @endif

        <!-- Authors List -->
        @if(!$showForm)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <!-- Search and Filter Bar -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                        <div class="flex-1 max-w-lg">
                            <div class="relative">
                                <input
                                    type="text"
                                    wire:model.live="searchTerm"
                                    placeholder="Search authors..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                           dark:bg-gray-700 dark:text-white"
                                >
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <select
                                wire:model.live="filterStatus"
                                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>

                            @if(count($selectedAuthors) > 0)
                                <button
                                    wire:click="bulkDelete"
                                    wire:confirm="Are you sure you want to delete {{ count($selectedAuthors) }} selected authors?"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    Delete Selected ({{ count($selectedAuthors) }})
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Authors Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input
                                    type="checkbox"
                                    wire:model="selectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-200">
                                    <span>Author</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </button>
                            </th>
                            <th class="px-6 py-3 hidden text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Specialization
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Books
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($authors as $author)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedAuthors"
                                        value="{{ $author->id }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if($author->profile_image)
                                                <img class="h-12 w-12 rounded-full object-cover"
                                                     src="{{ Storage::url($author->profile_image) }}"
                                                     alt="{{ $author->user->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $author->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $author->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 hidden py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $author->specialization ?: 'Not specified' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($author->user?->last_login)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200">
                                                Active
                                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200">
                                                Inactive
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $author->books->count() ?? 0 }} books
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $author->created_at?->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button
                                        wire:click="edit({{ $author->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        wire:click="confirmDelete({{ $author->id }})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No authors found</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $searchTerm ? 'Try adjusting your search terms.' : 'Get started by creating your first author.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($authors->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $authors->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal && $authorToDelete)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Delete Author
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to delete <strong>{{ $authorToDelete->user->name }}</strong>?
                                        This action cannot be undone and will also delete their user account.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button
                                wire:click="delete"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Delete
                            </button>
                            <button
                                wire:click="$set('showDeleteModal', false)"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
