@php use Carbon\Carbon; @endphp
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="px-4 py-8 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Virtual Session</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Set up a new live classroom session for your students
            </p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="flex items-center">
                        <div
                            class="flex items-center justify-center w-10 h-10 {{ $currentStep >= 1 ? 'bg-violet-600' : 'bg-gray-300' }} rounded-full">
                            <span class="text-sm font-medium text-white">1</span>
                        </div>
                        <span
                            class="ml-2 text-sm font-medium {{ $currentStep >= 1 ? 'text-violet-600' : 'text-gray-500' }}">Details</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $currentStep >= 2 ? 'bg-violet-600' : 'bg-gray-300' }}"></div>
                </div>

                <div class="flex items-center flex-1">
                    <div class="flex items-center">
                        <div
                            class="flex items-center justify-center w-10 h-10 {{ $currentStep >= 2 ? 'bg-violet-600' : 'bg-gray-300' }} rounded-full">
                            <span class="text-sm font-medium text-white">2</span>
                        </div>
                        <span
                            class="ml-2 text-sm font-medium {{ $currentStep >= 2 ? 'text-violet-600' : 'text-gray-500' }}">Settings</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $currentStep >= 3 ? 'bg-violet-600' : 'bg-gray-300' }}"></div>
                </div>

                <div class="flex items-center">
                    <div
                        class="flex items-center justify-center w-10 h-10 {{ $currentStep >= 3 ? 'bg-violet-600' : 'bg-gray-300' }} rounded-full">
                        <span class="text-sm font-medium text-white">3</span>
                    </div>
                    <span
                        class="ml-2 text-sm font-medium {{ $currentStep >= 3 ? 'text-violet-600' : 'text-gray-500' }}">Participants</span>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="createSession">
            <!-- Step 1: Session Details -->
            @if($currentStep === 1)
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">Session Details</h2>

                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Session Title <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="title" type="text" id="title"
                                   class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            @error('title') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea wire:model="description" id="description" rows="3"
                                      class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"></textarea>
                            @error('description') <span
                                class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date and Time -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="scheduled_date"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="scheduled_date" type="date" id="scheduled_date"
                                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                @error('scheduled_date') <span
                                    class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="scheduled_time"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Time <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="scheduled_time" type="time" id="scheduled_time"
                                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                @error('scheduled_time') <span
                                    class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Duration (minutes) <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="duration" id="duration"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                                <option value="180">3 hours</option>
                            </select>
                            @error('duration') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Recurring Section -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input wire:model.live="is_recurring" type="checkbox" id="is_recurring"
                                           class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_recurring"
                                           class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Make this a recurring session
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Automatically create multiple sessions based on a schedule
                                    </p>
                                </div>
                            </div>

                            @if($is_recurring)
                                <div
                                    class="p-4 mt-4 space-y-4 border border-violet-200 rounded-lg bg-violet-50 dark:bg-violet-900/20 dark:border-violet-800">
                                    <!-- Recurrence Pattern -->
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="recurrence_pattern"
                                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Repeat <span class="text-red-500">*</span>
                                            </label>
                                            <select wire:model.live="recurrence_pattern" id="recurrence_pattern"
                                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                            @error('recurrence_pattern') <span
                                                class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="recurrence_interval"
                                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Every <span class="text-red-500">*</span>
                                            </label>
                                            <div class="flex items-center mt-1">
                                                <input wire:model="recurrence_interval" type="number"
                                                       id="recurrence_interval" min="1" max="12"
                                                       class="block w-20 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $recurrence_pattern === 'daily' ? 'day(s)' : ($recurrence_pattern === 'weekly' ? 'week(s)' : 'month(s)') }}
                                                </span>
                                            </div>
                                            @error('recurrence_interval') <span
                                                class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Days of Week (for weekly recurrence) -->
                                    @if($recurrence_pattern === 'weekly')
                                        <div>
                                            <label
                                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Repeat on <span class="text-red-500">*</span>
                                            </label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7] as $day => $value)
                                                    <label class="inline-flex items-center px-3 py-2 border rounded-lg cursor-pointer
                                                        {{ in_array($value, $recurrence_days ?? []) ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600' }}">
                                                        <input type="checkbox" wire:model="recurrence_days"
                                                               value="{{ $value }}" class="sr-only">
                                                        <span
                                                            class="text-sm font-medium">{{ substr($day, 0, 3) }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('recurrence_days') <span
                                                class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    <!-- End Date Options -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Ends
                                        </label>
                                        <div class="space-y-2">
                                            <!-- Never -->
                                            <label class="flex items-center">
                                                <input wire:model.live="recurrence_end_type" type="radio" value="never"
                                                       class="w-4 h-4 text-violet-600 border-gray-300 focus:ring-violet-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Never</span>
                                            </label>

                                            <!-- On Date -->
                                            <label class="flex items-center">
                                                <input wire:model.live="recurrence_end_type" type="radio"
                                                       value="on_date"
                                                       class="w-4 h-4 text-violet-600 border-gray-300 focus:ring-violet-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">On</span>
                                                <input wire:model="recurrence_end_date" type="date"
                                                       {{ $recurrence_end_type !== 'on_date' ? 'disabled' : '' }}
                                                       class="ml-2 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                            </label>
                                            @error('recurrence_end_date') <span
                                                class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror

                                            <!-- After Occurrences -->
                                            <label class="flex items-center">
                                                <input wire:model.live="recurrence_end_type" type="radio"
                                                       value="after_occurrences"
                                                       class="w-4 h-4 text-violet-600 border-gray-300 focus:ring-violet-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">After</span>
                                                <input wire:model="recurrence_occurrences" type="number" min="1"
                                                       max="52"
                                                       {{ $recurrence_end_type !== 'after_occurrences' ? 'disabled' : '' }}
                                                       class="w-20 ml-2 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                <span
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300">occurrences</span>
                                            </label>
                                            @error('recurrence_occurrences') <span
                                                class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Recurrence Summary -->
                                    <div class="p-3 rounded-lg bg-violet-100 dark:bg-violet-900/30">
                                        <p class="text-sm font-medium text-violet-900 dark:text-violet-300">
                                            📅 Summary:
                                            @if($recurrence_pattern === 'daily')
                                                Every {{ $recurrence_interval > 1 ? $recurrence_interval : '' }}
                                                day{{ $recurrence_interval > 1 ? 's' : '' }}
                                            @elseif($recurrence_pattern === 'weekly')
                                                Every {{ $recurrence_interval > 1 ? $recurrence_interval : '' }}
                                                week{{ $recurrence_interval > 1 ? 's' : '' }}
                                                @if(!empty($recurrence_days))
                                                    on {{ collect($recurrence_days)->map(fn($d) => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$d-1])->implode(', ') }}
                                                @endif
                                            @else
                                                Every {{ $recurrence_interval > 1 ? $recurrence_interval : '' }}
                                                month{{ $recurrence_interval > 1 ? 's' : '' }}
                                            @endif
                                            @if($recurrence_end_type === 'on_date' && $recurrence_end_date)
                                                until {{ Carbon::parse($recurrence_end_date)->format('M d, Y') }}
                                            @elseif($recurrence_end_type === 'after_occurrences')
                                                for {{ $recurrence_occurrences }} occurrences
                                            @else
                                                (no end date)
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Academic Context -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label for="academic_level_id"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Academic Level
                                </label>
                                <select wire:model.live="academic_level_id" id="academic_level_id"
                                        class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                    <option value="">Select Level</option>
                                    @foreach($academicLevels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="academic_group_id"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Academic Group
                                </label>
                                <select wire:model.live="academic_group_id" id="academic_group_id"
                                        class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                    {{ empty($academicGroups) ? 'disabled' : '' }}>
                                    <option value="">Select Group</option>
                                    @foreach($academicGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="academic_subject_id"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Subject
                                </label>
                                <select wire:model="academic_subject_id" id="academic_subject_id"
                                        class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                    <option value="">Select Subject</option>
                                    @foreach($academicSubjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Session Settings -->
            @if($currentStep === 2)
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">Session Settings</h2>

                    <div class="space-y-6">
                        <!-- Recording -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="auto_record" type="checkbox" id="auto_record"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            </div>
                            <div class="ml-3">
                                <label for="auto_record" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Auto-record session
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Automatically record the session for later viewing
                                </p>
                            </div>
                        </div>

                        <!-- Mute on Start -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="mute_on_start" type="checkbox" id="mute_on_start"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            </div>
                            <div class="ml-3">
                                <label for="mute_on_start" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Mute participants on join
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Participants will be muted when they join the session
                                </p>
                            </div>
                        </div>

                        <!-- Webcams Only for Moderator -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="webcams_only_for_moderator" type="checkbox"
                                       id="webcams_only_for_moderator"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            </div>
                            <div class="ml-3">
                                <label for="webcams_only_for_moderator"
                                       class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Webcams only for moderator
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Only you can share webcam video
                                </p>
                            </div>
                        </div>

                        <!-- Allow Guest Login -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="allow_guest_login" type="checkbox" id="allow_guest_login"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            </div>
                            <div class="ml-3">
                                <label for="allow_guest_login"
                                       class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Allow guest login
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Allow non-registered users to join via invitation link
                                </p>
                            </div>
                        </div>

                        <!-- Guest Policy -->
                        <div>
                            <label for="guest_policy"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Guest Policy
                            </label>
                            <select wire:model="guest_policy" id="guest_policy"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                <option value="ALWAYS_ACCEPT">Always Accept</option>
                                <option value="ALWAYS_DENY">Always Deny</option>
                                <option value="ASK_MODERATOR">Ask Moderator</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                How to handle guest join requests
                            </p>
                        </div>

                        <!-- Max Participants -->
                        <div>
                            <label for="max_participants"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Maximum Participants
                            </label>
                            <input wire:model="max_participants" type="number" id="max_participants" min="1" max="500"
                                   class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 3: Participants -->
            @if($currentStep === 3)
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">Select Participants</h2>

                    <div class="space-y-6">
                        <!-- Invite All Toggle -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model.live="inviteAllStudents" type="checkbox" id="inviteAllStudents"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            </div>
                            <div class="ml-3">
                                <label for="inviteAllStudents"
                                       class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Invite all eligible students
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Automatically invite all students based on selected level/group
                                </p>
                            </div>
                        </div>

                        <!-- Manual Selection -->
                        @if(!$inviteAllStudents && !empty($students))
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Select Students
                                </label>
                                <div
                                    class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg dark:border-gray-600">
                                    @foreach($students as $student)
                                        <label class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <input wire:model="selectedStudents" type="checkbox"
                                                   value="{{ $student->id }}"
                                                   class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $student->user->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($inviteAllStudents)
                            <div class="p-4 rounded-lg bg-violet-50 dark:bg-violet-900/20">
                                <p class="text-sm text-violet-800 dark:text-violet-300">
                                    All eligible students will be invited to this session based on your academic context
                                    selection.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-6">
                @if($currentStep > 1)
                    <button type="button" wire:click="previousStep"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous
                    </button>
                @else
                    <a href="{{ route('teachers.classroom.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                @endif

                @if($currentStep < 3)
                    <button type="button" wire:click="nextStep"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                        Next
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @else
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Session
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
