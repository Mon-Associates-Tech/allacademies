<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Preferences</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage your global preferences and settings
            </p>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <!-- Theme Settings -->
            <div class="px-6 py-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Appearance</h4>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Theme
                        </label>
                        <select
                            wire:model="preferences.theme"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
                        >
                            @foreach($themes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Select your preferred theme for the application
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Font Family
                        </label>
                        <select
                            wire:model="preferences.font"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
                        >
                            @foreach($fonts as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Choose the font family for better readability
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="px-6 py-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Notifications</h4>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Newsletter
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Receive our periodic newsletter with updates
                            </p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none">
                            <input
                                type="checkbox"
                                wire:model="preferences.newsletter"
                                value="true"
                                class="sr-only"
                                id="newsletter-toggle"
                            >
                            <label
                                for="newsletter-toggle"
                                class="block h-6 w-12 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out {{ $preferences['newsletter'] === 'true' ? 'bg-indigo-600 dark:bg-indigo-700' : '' }}"
                            >
                                <span
                                    class="absolute left-1 top-1 bg-white dark:bg-gray-200 border border-gray-300 dark:border-gray-500 rounded-full h-4 w-4 transform transition-transform duration-200 ease-in-out {{ $preferences['newsletter'] === 'true' ? 'translate-x-6' : '' }}"
                                ></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Assignment Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified when new assignments are posted
                            </p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none">
                            <input
                                type="checkbox"
                                wire:model="preferences.assignment_notifications"
                                value="true"
                                class="sr-only"
                                id="assignment-toggle"
                            >
                            <label
                                for="assignment-toggle"
                                class="block h-6 w-12 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out {{ $preferences['assignment_notifications'] === 'true' ? 'bg-indigo-600 dark:bg-indigo-700' : '' }}"
                            >
                                <span
                                    class="absolute left-1 top-1 bg-white dark:bg-gray-200 border border-gray-300 dark:border-gray-500 rounded-full h-4 w-4 transform transition-transform duration-200 ease-in-out {{ $preferences['assignment_notifications'] === 'true' ? 'translate-x-6' : '' }}"
                                ></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Quiz Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified about upcoming quizzes
                            </p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none">
                            <input
                                type="checkbox"
                                wire:model="preferences.quiz_notifications"
                                value="true"
                                class="sr-only"
                                id="quiz-toggle"
                            >
                            <label
                                for="quiz-toggle"
                                class="block h-6 w-12 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out {{ $preferences['quiz_notifications'] === 'true' ? 'bg-indigo-600 dark:bg-indigo-700' : '' }}"
                            >
                                <span
                                    class="absolute left-1 top-1 bg-white dark:bg-gray-200 border border-gray-300 dark:border-gray-500 rounded-full h-4 w-4 transform transition-transform duration-200 ease-in-out {{ $preferences['quiz_notifications'] === 'true' ? 'translate-x-6' : '' }}"
                                ></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Exam Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified about upcoming exams
                            </p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none">
                            <input
                                type="checkbox"
                                wire:model="preferences.exam_notifications"
                                value="true"
                                class="sr-only"
                                id="exam-toggle"
                            >
                            <label
                                for="exam-toggle"
                                class="block h-6 w-12 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out {{ $preferences['exam_notifications'] === 'true' ? 'bg-indigo-600 dark:bg-indigo-700' : '' }}"
                            >
                                <span
                                    class="absolute left-1 top-1 bg-white dark:bg-gray-200 border border-gray-300 dark:border-gray-500 rounded-full h-4 w-4 transform transition-transform duration-200 ease-in-out {{ $preferences['exam_notifications'] === 'true' ? 'translate-x-6' : '' }}"
                                ></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Grade Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified when grades are published
                            </p>
                        </div>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none">
                            <input
                                type="checkbox"
                                wire:model="preferences.grade_notifications"
                                value="true"
                                class="sr-only"
                                id="grade-toggle"
                            >
                            <label
                                for="grade-toggle"
                                class="block h-6 w-12 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out {{ $preferences['grade_notifications'] === 'true' ? 'bg-indigo-600 dark:bg-indigo-700' : '' }}"
                            >
                                <span
                                    class="absolute left-1 top-1 bg-white dark:bg-gray-200 border border-gray-300 dark:border-gray-500 rounded-full h-4 w-4 transform transition-transform duration-200 ease-in-out {{ $preferences['grade_notifications'] === 'true' ? 'translate-x-6' : '' }}"
                                ></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Settings -->
            <div class="px-6 py-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Academic Settings</h4>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Grade Display Format
                        </label>
                        <select
                            wire:model="preferences.grade_display"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
                        >
                            <option value="percentage">Percentage (e.g., 85%)</option>
                            <option value="letter">Letter Grade (e.g., B+)</option>
                            <option value="both">Both (e.g., 85% (B+))</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            How would you like grades to be displayed?
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Timezone
                        </label>
                        <select
                            wire:model="preferences.timezone"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
                        >
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time (ET)</option>
                            <option value="America/Chicago">Central Time (CT)</option>
                            <option value="America/Denver">Mountain Time (MT)</option>
                            <option value="America/Los_Angeles">Pacific Time (PT)</option>
                            <option value="Europe/London">London</option>
                            <option value="Europe/Paris">Paris</option>
                            <option value="Asia/Tokyo">Tokyo</option>
                            <option value="Asia/Shanghai">Shanghai</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Select your timezone for displaying dates and times
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <x-button.primary wire:click="save" size="md">
                Save Preferences
            </x-button.primary>
        </div>
    </div>
</div>

