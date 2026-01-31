<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Academic Level Preference</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if ($this->isStudent && $isEditingOwnProfile)
                    Your academic level is set by your teacher or administrator.
                @elseif (!$isEditingOwnProfile)
                    Set the academic level for this student.
                @else
                    Set your preferred academic level to determine which grading system is used for quizzes and assessments.
                @endif
            </p>
        </div>

        <div class="px-6 py-4">
            {{-- Current Grading System Info --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                        Current Grading System: <strong>{{ $currentGradingSystem }}</strong>
                    </span>
                </div>
            </div>

            {{-- Academic Level Selection --}}
            @if ($canEdit)
                <form wire:submit.prevent="save">
                    <div class="space-y-4">
                        <div>
                            <label for="academicLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Academic Level
                            </label>
                            <select
                                id="academicLevel"
                                wire:model.live="selectedAcademicLevelId"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">-- Select Academic Level (Default: BECE) --</option>
                                @foreach ($academicGroups as $group)
                                    <optgroup label="{{ $group['name'] }} ({{ ucfirst($group['tag']) }})">
                                        @foreach ($group['levels'] as $level)
                                            <option value="{{ $level['id'] }}">
                                                {{ $level['name'] }}
                                                @if ($level['label'])
                                                    ({{ $level['label'] }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('selectedAcademicLevelId')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                This determines which grading scale is used:
                            </p>
                            <ul class="mt-1 text-sm text-gray-500 dark:text-gray-400 list-disc list-inside ml-2">
                                <li><strong>Basic</strong> (Pre-School, Primary, JHS): BECE Grading (1-9)</li>
                                <li><strong>Senior</strong> (SHS): WASSCE Grading (A1-F9)</li>
                                <li><strong>University</strong>: University Grading (A-F)</li>
                            </ul>
                        </div>

                        {{-- Grading Scale Preview --}}
                        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Grading Scale Preview
                            </h4>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                @if (str_contains($currentGradingSystem, 'BECE'))
                                    <div class="grid grid-cols-3 gap-2">
                                        <div><span class="font-semibold">1:</span> 80-100% (Excellent)</div>
                                        <div><span class="font-semibold">2:</span> 70-79% (Very Good)</div>
                                        <div><span class="font-semibold">3:</span> 60-69% (Good)</div>
                                        <div><span class="font-semibold">4:</span> 55-59% (Credit)</div>
                                        <div><span class="font-semibold">5:</span> 50-54% (Credit)</div>
                                        <div><span class="font-semibold">6:</span> 45-49% (Credit)</div>
                                        <div><span class="font-semibold">7:</span> 40-44% (Pass)</div>
                                        <div><span class="font-semibold">8:</span> 35-39% (Pass)</div>
                                        <div><span class="font-semibold">9:</span> 0-34% (Fail)</div>
                                    </div>
                                @elseif (str_contains($currentGradingSystem, 'WASSCE'))
                                    <div class="grid grid-cols-3 gap-2">
                                        <div><span class="font-semibold">A1:</span> 80-100% (Excellent)</div>
                                        <div><span class="font-semibold">B2:</span> 70-79% (Very Good)</div>
                                        <div><span class="font-semibold">B3:</span> 65-69% (Good)</div>
                                        <div><span class="font-semibold">C4:</span> 60-64% (Credit)</div>
                                        <div><span class="font-semibold">C5:</span> 55-59% (Credit)</div>
                                        <div><span class="font-semibold">C6:</span> 50-54% (Credit)</div>
                                        <div><span class="font-semibold">D7:</span> 45-49% (Pass)</div>
                                        <div><span class="font-semibold">E8:</span> 40-44% (Pass)</div>
                                        <div><span class="font-semibold">F9:</span> 0-39% (Fail)</div>
                                    </div>
                                @else
                                    <div class="grid grid-cols-3 gap-2">
                                        <div><span class="font-semibold">A:</span> 90-100% (Excellent)</div>
                                        <div><span class="font-semibold">B:</span> 80-89% (Good)</div>
                                        <div><span class="font-semibold">C:</span> 70-79% (Satisfactory)</div>
                                        <div><span class="font-semibold">D:</span> 60-69% (Needs Improvement)</div>
                                        <div><span class="font-semibold">F:</span> 0-59% (Failing)</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="flex justify-end pt-4">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="save">Save Preference</span>
                                <span wire:loading wire:target="save">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            @else
                {{-- Read-only view for students viewing their own profile --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Academic Level
                        </label>
                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md">
                            @if ($this->effectiveAcademicLevel)
                                <span class="text-gray-900 dark:text-white font-medium">
                                    {{ $this->effectiveAcademicLevel->name }}
                                </span>
                                @if ($this->effectiveAcademicLevel->academicGroup)
                                    <span class="text-gray-500 dark:text-gray-400 text-sm ml-2">
                                        ({{ $this->effectiveAcademicLevel->academicGroup->name }})
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">
                                    Not set - Using default BECE grading
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Contact your teacher or administrator to change your academic level.
                        </p>
                    </div>

                    {{-- Grading Scale Info --}}
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your Grading Scale
                        </h4>
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            @if (str_contains($currentGradingSystem, 'BECE'))
                                <div class="grid grid-cols-3 gap-2">
                                    <div><span class="font-semibold">1:</span> 80-100% (Excellent)</div>
                                    <div><span class="font-semibold">2:</span> 70-79% (Very Good)</div>
                                    <div><span class="font-semibold">3:</span> 60-69% (Good)</div>
                                    <div><span class="font-semibold">4:</span> 55-59% (Credit)</div>
                                    <div><span class="font-semibold">5:</span> 50-54% (Credit)</div>
                                    <div><span class="font-semibold">6:</span> 45-49% (Credit)</div>
                                    <div><span class="font-semibold">7:</span> 40-44% (Pass)</div>
                                    <div><span class="font-semibold">8:</span> 35-39% (Pass)</div>
                                    <div><span class="font-semibold">9:</span> 0-34% (Fail)</div>
                                </div>
                            @elseif (str_contains($currentGradingSystem, 'WASSCE'))
                                <div class="grid grid-cols-3 gap-2">
                                    <div><span class="font-semibold">A1:</span> 80-100% (Excellent)</div>
                                    <div><span class="font-semibold">B2:</span> 70-79% (Very Good)</div>
                                    <div><span class="font-semibold">B3:</span> 65-69% (Good)</div>
                                    <div><span class="font-semibold">C4:</span> 60-64% (Credit)</div>
                                    <div><span class="font-semibold">C5:</span> 55-59% (Credit)</div>
                                    <div><span class="font-semibold">C6:</span> 50-54% (Credit)</div>
                                    <div><span class="font-semibold">D7:</span> 45-49% (Pass)</div>
                                    <div><span class="font-semibold">E8:</span> 40-44% (Pass)</div>
                                    <div><span class="font-semibold">F9:</span> 0-39% (Fail)</div>
                                </div>
                            @else
                                <div class="grid grid-cols-3 gap-2">
                                    <div><span class="font-semibold">A:</span> 90-100% (Excellent)</div>
                                    <div><span class="font-semibold">B:</span> 80-89% (Good)</div>
                                    <div><span class="font-semibold">C:</span> 70-79% (Satisfactory)</div>
                                    <div><span class="font-semibold">D:</span> 60-69% (Needs Improvement)</div>
                                    <div><span class="font-semibold">F:</span> 0-59% (Failing)</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
