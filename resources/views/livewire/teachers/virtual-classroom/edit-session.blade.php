<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="px-4 py-8 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('teachers.classroom.show', $session) }}"
               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to session details
            </a>

            <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Edit Session</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Update your virtual classroom session details
            </p>
        </div>

        <form wire:submit.prevent="updateSession">
            <div class="space-y-6">
                <!-- Session Details Card -->
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
                            @error('description') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date and Time -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="scheduled_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="scheduled_date" type="date" id="scheduled_date"
                                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                @error('scheduled_date') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="scheduled_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Time <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="scheduled_time" type="time" id="scheduled_time"
                                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                @error('scheduled_time') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
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
                    </div>
                </div>

                <!-- Academic Context Card -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">Academic Context</h2>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label for="academic_level_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Academic Level
                            </label>
                            <select wire:model="academic_level_id" id="academic_level_id"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                <option value="">Select Level</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="academic_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Academic Group
                            </label>
                            <select wire:model="academic_group_id" id="academic_group_id"
                                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                <option value="">Select Group</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="academic_subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
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

                <!-- Session Settings Card -->
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
                                <input wire:model="webcams_only_for_moderator" type="checkbox" id="webcams_only_for_moderator"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            </div>
                            <div class="ml-3">
                                <label for="webcams_only_for_moderator" class="text-sm font-medium text-gray-700 dark:text-gray-300">
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
                                <label for="allow_guest_login" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Allow guest login
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Allow non-registered users to join via invitation link
                                </p>
                            </div>
                        </div>

                        <!-- Guest Policy -->
                        <div>
                            <label for="guest_policy" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                            <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Maximum Participants
                            </label>
                            <input wire:model="max_participants" type="number" id="max_participants" min="1" max="500"
                                   class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between">
                    <a href="{{ route('teachers.classroom.show', $session) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Session
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
