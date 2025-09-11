<div class="max-w-7xl mx-auto">
    <!-- Main Form Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-8 py-6">
            <h2 class="text-2xl font-bold text-white mb-2">Create Your Subscription</h2>
            <p class="text-blue-100">Select your preferred duration, subjects, and customize your learning experience</p>
        </div>

        <div class="p-8">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Left Column - Form Fields -->
                <div class="lg:col-span-3 space-y-8">

                    <!-- Duration Selection -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Subscription Duration
                        </h3>
                        <x-form.select wire:model.live="durationInMonths" name="duration_in_months" label="" :options="[
                            '3' => '3 Months - Perfect for short-term goals',
                            '6' => '6 Months - Most popular choice',
                            '12' => '12 Months - Best value & comprehensive learning',
                            '1' => 'One-time purchase',
                        ]" />
                    </div>

                    <input name="package" type="hidden" value="{{ $package }}">

                    <!-- Beneficiaries Section -->
                    @if('institution:full' === $package)
                        <div class="bg-amber-50 rounded-xl p-6 border border-amber-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Number of Students
                            </h3>
                            <x-form.input wire:model.live="beneficiaries" name="beneficiaries" type="number"
                                          label="How many students will use this subscription?"
                                          placeholder="Enter number of students" />
                        </div>
                    @endif

                    <!-- Subject Selection -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Select Your Subjects
                            </h3>

                            <!-- Subject Counter and Filter -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="inline-flex items-center gap-x-2 rounded-lg py-2 px-4 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 bg-white shadow-sm">
                                    <div class="flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full {{ $this->subjects_count ? 'bg-green-400' : 'bg-red-400' }} opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 {{ $this->subjects_count ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    </div>
                                    <span class="font-semibold">{{ $this->subjects_count }}</span>
                                    <span>{{ Str::plural('Subject', $this->subjects_count) }} Selected</span>
                                </div>

                                <!-- Academic Group Filter -->
                                <div class="inline-flex rounded-lg overflow-hidden shadow-sm">
                                    <span class="inline-flex items-center rounded-l-lg ring-1 ring-inset ring-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        Academic Level
                                    </span>
                                    <select wire:model.live="academicGroupId" id="academic_group"
                                            class="-ml-px border-0 rounded-r-lg ring-1 ring-inset ring-gray-300 bg-white pl-3 pr-8 py-2 focus:ring-2 focus:ring-blue-500 text-sm font-medium text-gray-600">
                                        @foreach ($academicGroups as $academicGroup)
                                            <option value="{{ $academicGroup['id'] }}">{{ $academicGroup['name'] }}</option>
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
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-base font-semibold text-gray-700">{{ $academicLevel['name'] }}</h4>
                                                <p class="text-sm text-gray-500">{{ count($academicLevel['academic_subjects']) }} subjects available</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center text-gray-400 group-hover:text-gray-600 transition-colors">
                                            <svg x-show="!open" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                            <svg x-show="open" x-cloak class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
                                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 focus:ring-offset-0">
                                                        <div class="ml-3 flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition-colors">
                                                                {{ $academicSubject['name'] }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 group-hover:text-blue-500 transition-colors">
                                                                {{ $academicSubject['code'] }}
                                                            </div>
                                                        </div>
                                                        <!-- Selection indicator -->
                                                        <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
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
                    <input type="hidden" value="{{ $this->amount }}" name="amount" />
                </div>

                <!-- Right Column - Price Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 shadow-lg">
                            <div class="text-center">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Subscription Total</h3>
                                <div class="text-4xl font-bold text-blue-600 mb-4">
                                    GHC {{ number_format($this->amount, 2) }}
                                </div>

                                <!-- Price Breakdown -->
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between py-2 border-b border-blue-200">
                                        <span class="text-gray-600">Duration:</span>
                                        <span class="font-medium">
                                            @if($durationInMonths === 1)
                                                One-time
                                            @else
                                                {{ $durationInMonths }} months
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-blue-200">
                                        <span class="text-gray-600">Subjects++:</span>
                                        <span class="font-medium">{{ $this->subjects_count }}</span>
                                    </div>
                                    @if('institution:full' === $package && $beneficiaries)
                                        <div class="flex justify-between py-2 border-b border-blue-200">
                                            <span class="text-gray-600">Students:</span>
                                            <span class="font-medium">{{ $beneficiaries }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Value Proposition -->
                                <div class="mt-6 p-4 bg-white rounded-xl border border-blue-100">
                                    <div class="flex items-center justify-center mb-2">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">What's Included</span>
                                    </div>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li>• Unlimited practice quizzes</li>
                                        <li>• Detailed progress tracking</li>
                                        <li>• Expert-curated content</li>
                                        <li>• 24/7 access to materials</li>
                                        @if($durationInMonths >= 6)
                                            <li class="text-green-600 font-medium">• Extended support included</li>
                                        @endif
                                    </ul>
                                </div>
                                <input type="hidden" name="academic_group_tag[]" wire:model="academicGroupTag" wire:key="academic_group_tag" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
