<div class="max-w-7xl mx-auto">
    <!-- Main Form Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 lg:px-8 py-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4 sm:ml-5">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1">Create Your Subscription</h1>
                        <p class="text-gray-600 text-sm">Select your preferred duration, subjects, and customize your
                            learning experience</p>
                    </div>
                </div>

                <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                    <!-- Pricing Information -->
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 text-gray-800 border border-gray-200 min-w-[200px] shadow-sm">
                        <div class="text-center">
                            <div class="text-sm font-medium opacity-90">Total Amount</div>
                            <div class="text-2xl font-bold mt-1 text-gray-900">
                                GHC {{ number_format($this->amount, 2) }}</div>
                            <div class="text-xs opacity-80 mt-1">
                                @if($durationInMonths === 1)
                                    One-time purchase
                                @else
                                    {{ $durationInMonths }} months
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="rounded-md bg-blue-50 p-4 mb-6 mx-auto justify-center place-items-center">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1 md:flex  md:justify-between">
                    <p class="text-sm text-blue-700">This subscription will apply to
                        <strong>{{ $this->currentTeam->name }}</strong>. You can change the team if this not your
                        intended
                        team.</p>
                    <p class="my-auto text-sm md:ml-6">
                        <a href="{{ route('teams.index') }}"
                           class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600">
                            Change Team
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if(auth()->user()->role === \App\Enums\UserRole::GUEST)
                <div class="rounded-md bg-amber-50 p-4 mb-6 mx-auto">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-800">
                                As a guest, you have access to select academic groups only. Contact support if you need
                                access to additional groups.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid lg:grid-cols-5 gap-4">
                <!-- Left Column - Form Fields -->
                <div class="lg:col-span-3 space-y-8">

                    <!-- Duration Selection -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Subscription Duration
                        </h3>
                        <x-form.select wire:model.live="durationInMonths" name="duration_in_months" label="" :options="[
                            '3' => '3 Months - Perfect for short-term goals',
                            '6' => '6 Months - Most popular choice',
                            '12' => '12 Months - Best value & comprehensive learning',
                            '1' => 'One-time purchase',
                        ]"/>
                    </div>

                    <input name="package" type="hidden" value="{{ $package }}">

                    <!-- Beneficiaries Section -->
                    @if('institution:full' === $package)
                        <div class="bg-amber-50 rounded-xl p-6 border border-amber-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Number of Students
                            </h3>
                            <x-form.input wire:model.live="beneficiaries" name="beneficiaries" type="number"
                                          label="How many students will use this subscription?"
                                          placeholder="Enter number of students"/>
                        </div>
                    @endif

                    <!-- Subject Selection -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Select Your Subjects
                            </h3>

                            <!-- Subject Counter and Filter -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div
                                    class="inline-flex items-center gap-x-2 rounded-lg py-2 px-4 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 bg-white shadow-sm">
                                    <div class="flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-2 w-2 rounded-full {{ $this->subjects_count ? 'bg-green-400' : 'bg-red-400' }} opacity-75"></span>
                                        <span
                                            class="relative inline-flex rounded-full h-2 w-2 {{ $this->subjects_count ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    </div>
                                    <span class="font-semibold">{{ $this->subjects_count }}</span>
                                    <span>{{ Str::plural('Subject', $this->subjects_count) }} Selected</span>
                                </div>

                                <!-- Academic Group Filter -->
                                <div class="inline-flex rounded-lg overflow-hidden shadow-sm">
                                    <span
                                        class="inline-flex items-center rounded-l-lg ring-1 ring-inset ring-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        Academic Group
                                    </span>
                                    <select wire:model.live="academicGroupId" id="academic_group"
                                            class="-ml-px border-0 rounded-r-lg ring-1 ring-inset ring-gray-300 bg-white pl-3 pr-8 py-2 focus:ring-2 focus:ring-blue-500 text-sm font-medium text-gray-600">
                                        @foreach ($academicGroups as $academicGroup)
                                            <option
                                                value="{{ $academicGroup['id'] }}">{{ $academicGroup['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Levels List -->
                        <div class="divide-y divide-gray-200">
                            @foreach ($academicLevels as $levelIndex => $academicLevel)
                                <div x-data="{ open: false }" wire:key="academic_level_{{ $academicLevel['id'] }}"
                                     class="transition-all duration-200 hover:bg-gray-50">

                                    <!-- Level Header -->
                                    <div x-on:click="open = !open"
                                         class="flex items-center justify-between cursor-pointer p-6 group">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-base font-semibold text-gray-700">{{ $academicLevel['name'] }}</h4>
                                                <p class="text-sm text-gray-500">{{ count($academicLevel['academic_subjects']) }}
                                                    subjects available</p>
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-center text-gray-400 group-hover:text-gray-600 transition-colors">
                                            <svg x-show="!open" class="w-5 h-5 transition-transform duration-200"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                            <svg x-show="open" x-cloak class="w-5 h-5 transition-transform duration-200"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Subjects List -->
                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="px-6 pb-6">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="grid sm:grid-cols-2 gap-3">
                                                @foreach ($academicLevel['academic_subjects'] as $academicSubject)
                                                    <label wire:key="academic_subject_{{ $academicSubject['id'] }}"
                                                           class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 hover:shadow-sm transition-all duration-200 group">
                                                        <input value="{{ $academicSubject['id'] }}"
                                                               wire:model.live="academicSubjects"
                                                               name="academic_subject_ids[]"
                                                               type="checkbox"
                                                               class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 dark:text-blue-500 focus:ring-blue-600 focus:ring-offset-0">
                                                        <div class="ml-3 flex-1 min-w-0">
                                                            <div
                                                                class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition-colors">
                                                                {{ $academicSubject['name'] }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-gray-500 group-hover:text-blue-500 transition-colors">
                                                                {{ $academicSubject['code'] }}
                                                            </div>
                                                        </div>
                                                        <!-- Selection indicator -->
                                                        <div
                                                            class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <svg class="w-4 h-4 text-blue-500" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Amount Input (Hidden but keeping for form submission) -->
                    <input type="hidden" value="{{ $this->amount }}" name="amount"/>
                    <input type="hidden" name="academic_group_tag" value="{{ $academicGroupTag }}"/>
                </div>

                <!-- Right Column - Selected Subjects Summary -->
                <div class="lg:col-span-2">
                    <div class="sticky top-4">
                        <div
                            class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200 shadow-lg">
                            <div class="text-center">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Your Selection</h3>

                                <!-- Selected Subjects Summary -->
                                @if($academicSubjects && count($academicSubjects) > 0)
                                    <div class="space-y-4 text-sm">
                                        @php
                                            // Group selected subjects by academic level for display
                                            $selectedSubjectsByLevel = [];
                                            foreach($academicLevels as $level) {
                                                foreach($level['academic_subjects'] as $subject) {
                                                    if(in_array($subject['id'], $academicSubjects)) {
                                                        if(!isset($selectedSubjectsByLevel[$level['name']])) {
                                                            $selectedSubjectsByLevel[$level['name']] = [];
                                                        }
                                                        $selectedSubjectsByLevel[$level['name']][] = $subject;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @foreach($selectedSubjectsByLevel as $levelName => $subjects)
                                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                                <h4 class="font-semibold text-gray-800 mb-2 text-left">{{ $levelName }}</h4>
                                                <ul class="space-y-1 text-left">
                                                    @foreach($subjects as $subject)
                                                        <li class="flex items-center text-gray-600">
                                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <span>{{ $subject['name'] }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach

                                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mt-4">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700 font-medium">Total Subjects:</span>
                                                <span
                                                    class="text-blue-600 font-bold">{{ count($academicSubjects) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-white rounded-xl p-6 border border-gray-200 mt-4">
                                        <div class="text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <p class="mt-2">No subjects selected yet</p>
                                            <p class="text-sm mt-1">Select subjects from the list to see them here</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Price Summary -->
                                <div class="mt-6 bg-white rounded-xl p-4 border border-gray-200">
                                    <h4 class="font-semibold text-gray-700 mb-3">Subscription Summary</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between py-1">
                                            <span class="text-gray-600">Duration:</span>
                                            <span class="font-medium">
                                @if($durationInMonths === 1)
                                                    One-time
                                                @else
                                                    {{ $durationInMonths }} months
                                                @endif
                            </span>
                                        </div>
                                        <div class="flex justify-between py-1">
                                            <span class="text-gray-600">Subjects:</span>
                                            <span class="font-medium">{{ $this->subjects_count }}</span>
                                        </div>
                                        @if('institution:full' === $package && $beneficiaries)
                                            <div class="flex justify-between py-1">
                                                <span class="text-gray-600">Students:</span>
                                                <span class="font-medium">{{ $beneficiaries }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between pt-3 mt-2 border-t border-gray-200">
                                            <span class="text-gray-700 font-semibold">Total:</span>
                                            <span
                                                class="text-blue-600 font-bold text-lg">GHC {{ number_format($this->amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Value Proposition -->
                                <div class="mt-6 p-4 bg-white rounded-xl border border-gray-200">
                                    <div class="flex items-center justify-start mb-2">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-lg font-medium text-gray-700">What's Included</span>
                                    </div>
                                    <ul class="text-sm text-start text-gray-600 space-y-1">
                                        <li>• Unlimited practice quizzes</li>
                                        <li>• Detailed progress tracking</li>
                                        <li>• Expert-curated content</li>
                                        <li>• 24/7 access to materials</li>
                                        @if($durationInMonths >= 6)
                                            <li class="text-green-600 font-medium">• Extended support included</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                     x-data="{ hasSubjects: @js($academicGroups) }"
                     x-init="$wire.on('subjectsUpdated', (count) => { hasSubjects = count > 0 })">

                    <!-- Validation Message -->
                    <div class="flex items-center text-sm"
                         :class="hasSubjects ? 'text-green-700' : 'text-amber-700'">
                        <svg x-show="!hasSubjects" class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <svg x-show="hasSubjects" x-cloak class="w-5 h-5 mr-2 text-green-500" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="!hasSubjects">Please select at least one subject to continue</span>
                        <span x-show="hasSubjects" x-cloak>Ready to create your subscription</span>
                    </div>


                    <!-- Submit Button -->
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createSubscription"
                            @disabled($this->subjects_count == 0)
                            class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed
        {{ $this->subjects_count == 0 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-violet-600 hover:bg-violet-700 text-white' }}">

                            {{-- Icon + Text --}}
                            @if($this->subjects_count == 0)
                                {{-- Disabled State --}}
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Select Subjects First</span>
                            @else
                                {{-- Enabled State --}}
                                <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Create Subscription</span>
                            @endif
                        </button>


                    </div>
                </div>
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your
                                    submission:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Additional Info -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">
                        By creating this subscription, you agree to our
                        <a href="{{route('branding.terms')}}" class="text-blue-600 hover:text-blue-800 underline">Terms
                            of Service</a>
                        and
                        <a href="{{route('branding.privacy')}}" class="text-blue-600 hover:text-blue-800 underline">Privacy
                            Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
