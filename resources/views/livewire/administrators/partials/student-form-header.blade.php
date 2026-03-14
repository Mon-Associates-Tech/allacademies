<div class="flex justify-between space-x-4">
    <div class="flex-shrink-0 flex my-auto">
        <div
            class="w-12 h-12 my-auto bg-indigo-50 dark:bg-white/5 backdrop-blur-sm rounded-xl flex items-center justify-center ring-2 ring-indigo-100 dark:ring-white/30">
            <svg class="w-7 h-7 text-indigo-700 dark:text-white" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div class="my-auto">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title">
                {{ $formMode === 'edit' ? 'Edit Student Profile' : 'Create New Student' }}
            </h2>
            <p class="hidden text-sm mt-1 text-gray-600 dark:text-gray-300">
                {{ $formMode === 'edit' ? 'Update student information and academic assignments' : 'Add a new student to the academic system' }}
            </p>
        </div>
    </div>

    <div class="flex items-center space-x-3">
        @if($academicGroupId)
            <button type="button"
                    onclick="window.Modal.open('teacher-manage-form')"
                    class="inline-flex items-center px-4 py-2.5 border border-indigo-500 dark:border-indigo-300 text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                <svg class="w-4 h-4 mr-2" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Manage Teachers
            </button>
        @endif

        <button
            onclick="window.Modal.open('teacher-add-form')"
            class="inline-flex items-center px-4 py-2.5 border border-indigo-300 dark:border-indigo-200 text-sm font-medium rounded-xl shadow-sm text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-300 dark:text-white dark:bg-indigo-600 dark:hover:bg-indigo-500 dark:focus:ring-indigo-400">
            <svg class="w-4 h-4 mr-2" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Teacher
        </button>

        @if($isEditing)
            <button type="button" wire:click="resetForm"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 dark:border-gray-500 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:text-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-500">
                <svg class="w-4 h-4 mr-2" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </button>
        @endif
    </div>
</div>
