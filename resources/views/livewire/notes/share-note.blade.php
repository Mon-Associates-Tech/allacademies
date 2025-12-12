<div x-data="{
    showPreview: false,
    shareType: @entangle('shareType'),
    recipientCount: {{ $this->recipientCount }},
}"
     x-on:note-shared.window="showPreview = false"
     class="space-y-6">

    {{-- Share Type Selection --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Select Share Type</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {{-- Individual Users --}}
            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border-2 transition-all duration-200
                {{ $shareType === 'individual' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <input type="radio"
                       wire:model.live="shareType"
                       value="individual"
                       class="sr-only">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $shareType === 'individual' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Individual Users</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Share with specific users</span>
                </div>
            </label>

            {{-- Email Address Option - NEW --}}
            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border-2 transition-all duration-200
        {{ $shareType === 'email' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <input type="radio"
                       wire:model.live="shareType"
                       value="email"
                       class="sr-only">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                {{ $shareType === 'email' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Email Address</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Share with any email</span>
                </div>
            </label>

            {{-- Academic Groups --}}
            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border-2 transition-all duration-200
                {{ $shareType === 'academic_group' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <input type="radio"
                       wire:model.live="shareType"
                       value="academic_group"
                       class="sr-only">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $shareType === 'academic_group' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Academic Groups</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Share with class groups</span>
                </div>
            </label>

            {{-- Academic Levels --}}
            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border-2 transition-all duration-200
                {{ $shareType === 'academic_level' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <input type="radio"
                       wire:model.live="shareType"
                       value="academic_level"
                       class="sr-only">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $shareType === 'academic_level' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Academic Levels</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Share by grade level</span>
                </div>
            </label>

            {{-- Student Groups --}}
            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border-2 transition-all duration-200
                {{ $shareType === 'student_group' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <input type="radio"
                       wire:model.live="shareType"
                       value="student_group"
                       class="sr-only">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $shareType === 'student_group' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Student Groups</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Share with student groups</span>
                </div>
            </label>

            {{-- School Wide --}}
            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border-2 transition-all duration-200
                {{ $shareType === 'school_wide' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <input type="radio"
                       wire:model.live="shareType"
                       value="school_wide"
                       class="sr-only">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $shareType === 'school_wide' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Entire School</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Share with everyone</span>
                </div>
            </label>
        </div>

        @error('shareType')
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    @if($shareType === 'email')
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                Email Address
                <span class="text-red-500">*</span>
            </label>

            <input type="email"
                   wire:model.live.debounce.500ms="emailInput"
                   placeholder="Enter email address..."
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300">

            @error('emailInput')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                If this email exists in your school, they'll receive the note. Otherwise, they'll get an invitation email.
            </p>
        </div>
    @endif


    @if($shareType !== 'school_wide' && $shareType !== 'email')
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                Select Recipients
                <span class="text-red-500">*</span>
            </label>

            @livewire('notes.share-note-recipients-select', [
                'shareType' => $shareType,
                'selected' => $selectedRecipients,
                'schoolId' => auth()->user()->school_id,
                'placeholder' => 'Search and select recipients...',
                'name' => 'selectedRecipients',
            ], key('recipients-select-' . $shareType))

            @error('selectedRecipients')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            @if(!empty($selectedRecipients))
                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        This will share with <strong>{{ $this->recipientCount }}</strong> {{ \Str::plural('user', $this->recipientCount) }}
                    </p>
                </div>
            @endif
        </div>
    @else
        {{-- Keep the existing school_wide warning message --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200">School-Wide Sharing</h4>
                    <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                        This note will be shared with all <strong>{{ $this->recipientCount }}</strong> users in your school. Everyone will be notified.
                    </p>
                </div>
            </div>
        </div>
    @endif


    {{-- Permissions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Permissions</h4>

        <div class="space-y-3">
            <label class="flex items-start cursor-pointer group">
                <div class="flex items-center h-5">
                    <input type="checkbox"
                           wire:model="canEdit"
                           class="h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                </div>
                <div class="ml-3">
                    <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        Allow recipients to edit this note
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Recipients will be able to modify the note content
                    </p>
                </div>
            </label>

            <label class="flex items-start cursor-pointer group">
                <div class="flex items-center h-5">
                    <input type="checkbox"
                           wire:model="notifyRecipients"
                           checked
                           class="h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                </div>
                <div class="ml-3">
                    <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        Notify recipients via email
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Recipients will receive an email notification about this shared note
                    </p>
                </div>
            </label>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center justify-between gap-4">
        <button type="button"
                x-on:click="showPreview = true"
                x-show="{{ $shareType !== 'school_wide' ? 'false' : 'true' }} || {{ !empty($selectedRecipients) ? 'true' : 'false' }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Preview
        </button>

        <button type="button"
                wire:click="shareNote"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
            <svg wire:loading class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove>Share Note</span>
            <span wire:loading>Sharing...</span>
        </button>
    </div>

    {{-- Preview Modal --}}
    <div x-show="showPreview"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-on:click="showPreview = false"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showPreview"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Share Preview
                            </h3>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Share Type</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {{ str_replace('_', ' ', $shareType) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Recipients</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $this->recipientCount }} {{ \Str::plural('user', $this->recipientCount) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Permissions</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $canEdit ? 'Can edit' : 'View only' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button"
                            x-on:click="showPreview = false"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Got it
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
