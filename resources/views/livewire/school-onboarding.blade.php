<section>
    <div x-data="schoolOnboardingForm()" x-init="init()"
         class="min-h-screen ">
        
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden dark:bg-gray-800 dark:shadow-2xl">
                
                {{-- Progress Section (Part of Form) --}}
                <div class="page-header-purple py-6 px-4">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">School Registration</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Complete your school onboarding</p>
                        </div>
                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-4 py-2 rounded-full">
                            Step <span x-text="currentStep"></span> of {{ $totalSteps }}
                        </span>
                    </div>
                    
                    {{-- Progress Timeline (Numbers and Bar on One Line) --}}
                    <div class="relative">
                        {{-- Step Numbers and Connectors --}}
                        <div class="flex justify-between items-center">
                            <template x-for="step in {{ $totalSteps }}" :key="step">
                                <div class="flex flex-col items-center relative z-10">
                                    <div :class="`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all ${step === currentStep ? 'bg-blue-600 text-white dark:bg-blue-500 shadow-lg scale-110' : step < currentStep ? 'bg-green-500 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300'}`"
                                         x-text="step"></div>
                                    <span :class="`text-xs font-medium mt-3 text-center transition-colors ${step <= currentStep ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-400 dark:text-gray-600'}`">
                                        <template x-if="step === 1">School Info</template>
                                        <template x-if="step === 2">Academics</template>
                                        <template x-if="step === 3">Banking</template>
                                        <template x-if="step === 4">Review</template>
                                    </span>
                                </div>
                            </template>
                        </div>
                        
                        {{-- Progress Bar (Behind Numbers) --}}
                        <div class="absolute top-5 left-5 right-5 h-1 bg-gray-300 dark:bg-gray-700 rounded-full -z-0">
                            <div class="h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600 rounded-full transition-all duration-500 ease-out dark:from-blue-400 dark:via-purple-400 dark:to-indigo-500"
                                 :style="`width: ${((currentStep - 1) / ({{ $totalSteps }} - 1)) * 100}%`"></div>
                        </div>
                    </div>
                </div>

                <form enctype="multipart/form-data" id="schoolForm">
                    
                    {{-- STEP 1: School Details --}}
                    <div x-show="currentStep === 1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-10"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         class="px-6 sm:px-8 py-8">
                        
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4H9m0 4a2 2 0 110-4m0 4v2m0-6V9a2 2 0 010-4h0a2 2 0 010 4m0 6v2m0 0h.581"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">School Information</h2>
                            <p class="text-gray-600 dark:text-gray-400">Let's start with the basic details about your school</p>
                        </div>

                        <div class="space-y-6">
                            {{-- Basic Info Grid --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- School Name --}}
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Name <span class="text-red-500 font-bold">*</span>
                                    </label>
                                    <input type="text" id="name" wire:model.defer="name"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('name') border-red-500 dark:border-red-500 @enderror"
                                           placeholder="Enter your school name" x-ref="firstInput">
                                    @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Email <span class="text-red-500 font-bold">*</span>
                                    </label>
                                    <input type="email" id="email" wire:model.defer="email"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('email') border-red-500 dark:border-red-500 @enderror"
                                           placeholder="school@example.com">
                                    @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" wire:model.defer="phone"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                           placeholder="+233 XX XXX XXXX"
                                           x-on:input="formatPhoneNumber($event)">
                                </div>

                                {{-- Type --}}
                                <div>
                                    <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Type <span class="text-red-500 font-bold">*</span>
                                    </label>
                                    <select id="type" wire:model.defer="type"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('type') border-red-500 dark:border-red-500 @enderror">
                                        <option value="">Select school type</option>
                                        @foreach($this->schoolTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Ownership --}}
                                <div>
                                    <label for="ownership" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Ownership Type <span class="text-red-500 font-bold">*</span>
                                    </label>
                                    <select id="ownership" wire:model.defer="ownership"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('ownership') border-red-500 dark:border-red-500 @enderror">
                                        <option value="">Select ownership type</option>
                                        @foreach($this->ownershipTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('ownership')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Website --}}
                                <div>
                                    <label for="website" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Website
                                    </label>
                                    <input type="url" id="website" wire:model.defer="website"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                           placeholder="https://www.yourschool.edu.gh">
                                </div>

                                {{-- Established Date --}}
                                <div>
                                    <label for="established_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Established Date
                                    </label>
                                    <input type="date" id="established_date" wire:model.defer="established_date"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                {{-- Student Capacity --}}
                                <div>
                                    <label for="student_capacity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Student Capacity <span class="text-red-500 font-bold">*</span>
                                    </label>
                                    <input type="number" id="student_capacity" wire:model.defer="student_capacity" min="1"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('student_capacity') border-red-500 dark:border-red-500 @enderror"
                                           placeholder="e.g., 500">
                                    @error('student_capacity')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Description --}}
                                <div class="lg:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Description
                                    </label>
                                    <textarea id="description" wire:model.defer="description" rows="4"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 resize-none"
                                              placeholder="Brief description of your school..."></textarea>
                                </div>

                                {{-- Logo Upload --}}
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Logo
                                    </label>
                                    <div class="flex items-center space-x-6">
                                        <div class="flex-shrink-0">
                                            <div class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-700">
                                                <template x-if="logoPreview">
                                                    <img :src="logoPreview" class="w-full h-full object-cover rounded-lg">
                                                </template>
                                                <template x-if="!logoPreview">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-400 dark:text-gray-500 h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" id="logo" wire:model="logo" accept="image/*"
                                                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/20 dark:file:text-blue-400 hover:file:bg-blue-100"
                                                   x-on:change="handleFileUpload($event)">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">PNG, JPG, GIF up to 2MB</p>
                                            <div wire:loading wire:target="logo" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                                Uploading...
                                            </div>
                                        </div>
                                    </div>
                                    @error('logo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Address Section --}}
                            <div class="border-t dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address Information</h3>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    {{-- Address --}}
                                    <div class="lg:col-span-2">
                                        <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Street Address <span class="text-red-500 font-bold">*</span>
                                        </label>
                                        <textarea id="address" wire:model.defer="address" rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 resize-none @error('address') border-red-500 dark:border-red-500 @enderror"
                                                  placeholder="Enter complete street address"></textarea>
                                        @error('address')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- City --}}
                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            City <span class="text-red-500 font-bold">*</span>
                                        </label>
                                        <input type="text" id="city" wire:model.defer="city"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('city') border-red-500 dark:border-red-500 @enderror"
                                               placeholder="e.g., Accra">
                                        @error('city')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Region/State --}}
                                    <div>
                                        <label for="state" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Region/State <span class="text-red-500 font-bold">*</span>
                                        </label>
                                        <select id="state" wire:model.defer="state"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('state') border-red-500 dark:border-red-500 @enderror">
                                            <option value="">Select region</option>
                                            @foreach($this->ghanaRegions as $region)
                                                <option value="{{ $region }}">{{ $region }}</option>
                                            @endforeach
                                        </select>
                                        @error('state')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Country --}}
                                    <div>
                                        <label for="country" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Country
                                        </label>
                                        <input type="text" id="country" wire:model.defer="country"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 cursor-not-allowed"
                                               readonly>
                                    </div>

                                    {{-- Postal Code --}}
                                    <div>
                                        <label for="postal_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Postal Code
                                        </label>
                                        <input type="text" id="postal_code" wire:model.defer="postal_code"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                               placeholder="e.g., GA-123-4567">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Academic Structure --}}
                    <div x-show="currentStep === 2" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-10"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         class="px-6 sm:px-8 py-8">
                        
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Academic Structure</h2>
                            <p class="text-gray-600 dark:text-gray-400">Configure your school's academic organization</p>
                        </div>

                        <div class="space-y-8">
                            {{-- Academic Groups --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Academic Groups <span class="text-red-500 font-bold">*</span></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select the academic divisions your school offers</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->availableAcademicGroups as $group)
                                        <label :class="`group relative flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 border-2 ${$wire.selectedAcademicGroups.includes('{{ $group->id }}') ? 'bg-green-50 dark:bg-green-900/20 border-green-500 dark:border-green-400 shadow-md' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 hover:border-green-400 dark:hover:border-green-500 hover:shadow-md'}`">
                                            <input type="checkbox" wire:model.live="selectedAcademicGroups"
                                                   value="{{ $group->id }}" class="sr-only">
                                            <div :class="`flex-shrink-0 w-5 h-5 mr-3 border-2 rounded transition-all duration-200 flex items-center justify-center ${$wire.selectedAcademicGroups.includes('{{ $group->id }}') ? 'bg-green-500 border-green-500 dark:bg-green-400 dark:border-green-400' : 'border-gray-300 dark:border-gray-500 group-hover:border-green-500 dark:group-hover:border-green-400'}`">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3 transition-opacity duration-200" :class="{'opacity-100': $wire.selectedAcademicGroups.includes('{{ $group->id }}'), 'opacity-0': !$wire.selectedAcademicGroups.includes('{{ $group->id }}')}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <span :class="`text-sm font-medium transition-colors ${$wire.selectedAcademicGroups.includes('{{ $group->id }}') ? 'text-green-700 dark:text-green-300' : 'text-gray-900 dark:text-gray-200 group-hover:text-green-600 dark:group-hover:text-green-400'}`">{{ $group->name }}</span>
                                                @if($group->description)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $group->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedAcademicGroups')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Academic Levels --}}
                            <div x-show="$wire.selectedAcademicGroups.length > 0" x-transition>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Academic Levels <span class="text-red-500 font-bold">*</span></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select specific levels within your chosen groups</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->availableAcademicLevels as $level)
                                        <label :class="`group relative flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 border-2 ${$wire.selectedAcademicLevels.includes('{{ $level->id }}') ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-500 dark:border-blue-400 shadow-md' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md'}`">
                                            <input type="checkbox" wire:model.live="selectedAcademicLevels"
                                                   value="{{ $level->id }}" class="sr-only">
                                            <div :class="`flex-shrink-0 w-5 h-5 mr-3 border-2 rounded transition-all duration-200 flex items-center justify-center ${$wire.selectedAcademicLevels.includes('{{ $level->id }}') ? 'bg-blue-500 border-blue-500 dark:bg-blue-400 dark:border-blue-400' : 'border-gray-300 dark:border-gray-500 group-hover:border-blue-500 dark:group-hover:border-blue-400'}`">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3 transition-opacity duration-200" :class="{'opacity-100': $wire.selectedAcademicLevels.includes('{{ $level->id }}'), 'opacity-0': !$wire.selectedAcademicLevels.includes('{{ $level->id }}')}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <span :class="`text-sm font-medium transition-colors ${$wire.selectedAcademicLevels.includes('{{ $level->id }}') ? 'text-blue-700 dark:text-blue-300' : 'text-gray-900 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400'}`">{{ $level->name }}</span>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $level->academicGroup->name }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedAcademicLevels')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Settings Section --}}
                            <div class="border-t dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Academic Settings</h3>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label for="timezone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Timezone
                                        </label>
                                        <select id="timezone" wire:model.defer="timezone"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            @foreach($this->timezones as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="currency" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Currency
                                        </label>
                                        <select id="currency" wire:model.defer="currency"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            @foreach($this->currencies as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="academic_year_start" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Academic Year Start
                                        </label>
                                        <input type="date" id="academic_year_start" wire:model.defer="academic_year_start"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('academic_year_start') border-red-500 dark:border-red-500 @enderror">
                                        @error('academic_year_start')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="academic_year_end" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Academic Year End
                                        </label>
                                        <input type="date" id="academic_year_end" wire:model.defer="academic_year_end"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('academic_year_end') border-red-500 dark:border-red-500 @enderror">
                                        @error('academic_year_end')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: Bank Information --}}
                    <div x-show="currentStep === 3" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-10"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         class="px-6 sm:px-8 py-8">
                        
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10m4 0a2 2 0 11-4 0m4 0a2 2 0 014 0M7 15a2 2 0 11-4 0m4 0a2 2 0 01-4 0m6 0a2 2 0 11-4 0m4 0a2 2 0 01-4 0"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Bank Information</h2>
                            <p class="text-gray-600 dark:text-gray-400">Set up payment processing for your school (optional)</p>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Bank information enables payment processing. You can skip this and configure it later in your school settings.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="bank_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Select Bank
                                </label>
                                <select id="bank_code" wire:model.defer="bank_code"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- Select Bank --</option>
                                    @foreach(App\Constants\GhanaBanks::all() as $key => $bank)
                                        <option value="{{ $key }}">{{ $bank }}</option>
                                    @endforeach
                                </select>
                                @error('bank_code')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="account_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Account Number
                                </label>
                                <input type="text" id="account_number" wire:model.defer="account_number"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('account_number') border-red-500 dark:border-red-500 @enderror"
                                       placeholder="Enter your account number">
                                @error('account_number')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="hidden" wire:model.defer="settlement_bank">
                        </div>
                    </div>

                    {{-- STEP 4: Review & Confirmation --}}
                    <div x-show="currentStep === 4" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-10"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         class="px-6 sm:px-8 py-8">
                        
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Review Your Information</h2>
                            <p class="text-gray-600 dark:text-gray-400">Please verify all details before completing setup</p>
                        </div>

                        <div x-show="!showSuccessMessage" class="space-y-6">
                            {{-- School Information Summary --}}
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4H9m0 4a2 2 0 110-4m0 4v2m0-6V9a2 2 0 010-4h0a2 2 0 010 4m0 6v2m0 0h.581"/>
                                    </svg>
                                    School Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">School Name</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.name"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">Email</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.email"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">Phone</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.phone || 'Not provided'"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">Type</p><p class="font-semibold text-gray-900 dark:text-white capitalize" x-text="$wire.type"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">Ownership</p><p class="font-semibold text-gray-900 dark:text-white capitalize" x-text="$wire.ownership"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">Student Capacity</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.student_capacity"></p></div>
                                </div>
                                <button type="button" x-on:click="currentStep = 1" class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    ← Edit School Information
                                </button>
                            </div>

                            {{-- Address Summary --}}
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    Address
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2"><p class="text-xs text-gray-600 dark:text-gray-400">Address</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.address"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">City</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.city"></p></div>
                                    <div><p class="text-xs text-gray-600 dark:text-gray-400">Region</p><p class="font-semibold text-gray-900 dark:text-white" x-text="$wire.state"></p></div>
                                </div>
                                <button type="button" x-on:click="currentStep = 1" class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    ← Edit Address
                                </button>
                            </div>

                            {{-- Academic Structure Summary --}}
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                    Academic Structure
                                </h3>
                                <div x-show="$wire.selectedAcademicLevels.length > 0">
                                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="`${$wire.selectedAcademicLevels.length} academic level(s) selected`"></p>
                                </div>
                                <button type="button" x-on:click="currentStep = 2" class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    ← Edit Academic Structure
                                </button>
                            </div>
                        </div>

                        {{-- Success Message --}}
                        <div x-show="showSuccessMessage" x-transition class="text-center py-12">
                            <div class="mb-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-full flex items-center justify-center mx-auto shadow-lg animate-bounce">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">School Created Successfully!</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">Your school has been registered and is ready to use.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mb-8">Redirecting to school settings in <span x-text="redirectCountdown"></span> seconds...</p>
                            
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <button type="button" @click="goToSchoolSettings()"
                                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 dark:from-green-500 dark:to-green-600 text-white rounded-lg font-semibold hover:from-green-700 hover:to-green-800 dark:hover:from-green-600 dark:hover:to-green-700 transition-all duration-200 flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Go to School Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Navigation Buttons --}}
                <div class="px-6 sm:px-8 py-6 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-600 flex justify-between items-center">
                    {{-- Previous Button --}}
                    <template x-if="currentStep > 1">
                        <button type="button" wire:click="previousStep"
                                class="px-6 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </button>
                    </template>
                    <div></div>

                    {{-- Next Button --}}
                    <template x-if="currentStep < 4">
                        <button type="button" @click="nextStep()"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 transition-all duration-200 flex items-center shadow-lg">
                            Next
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </template>

                    {{-- Submit Button --}}
                    <template x-if="currentStep === 4 && !showSuccessMessage">
                        <button type="button" wire:click="createSchool" :disabled="loading"
                                :class="`px-8 py-2.5 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-lg ${loading ? 'bg-yellow-500 dark:bg-yellow-600 text-white hover:bg-yellow-600 dark:hover:bg-yellow-700' : 'bg-gradient-to-r from-green-600 to-green-700 dark:from-green-500 dark:to-green-600 text-white hover:from-green-700 hover:to-green-800 dark:hover:from-green-600 dark:hover:to-green-700'}`">
                            <template x-if="!loading">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Complete Setup
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center">
                                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Quick Tips --}}
            <div x-show="currentStep === 1" x-transition
                 class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-amber-200 dark:border-amber-900/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-amber-600 dark:text-amber-400 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    Quick Setup Tips
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        </svg>
                        <div>
                            <p class="font-medium text-blue-900 dark:text-blue-200">Accurate Information</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Ensure all school details are accurate as they'll appear in official documents</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-green-600 dark:text-green-400 mt-0.5 mr-3 flex-shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        </svg>
                        <div>
                            <p class="font-medium text-green-900 dark:text-green-200">Contact Information</p>
                            <p class="text-sm text-green-700 dark:text-green-300">Use a monitored email and phone that parents can reach you at</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-purple-600 dark:text-purple-400 mt-0.5 mr-3 flex-shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        </svg>
                        <div>
                            <p class="font-medium text-purple-900 dark:text-purple-200">Multiple Steps</p>
                            <p class="text-sm text-purple-700 dark:text-purple-300">You can navigate back and edit previous information anytime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function schoolOnboardingForm() {
            return {
                currentStep: @entangle('currentStep'),
                loading: @entangle('loading'),
                logoPreview: null,
                showSuccessMessage: false,
                redirectCountdown: 5,

                init() {
                    this.$nextTick(() => {
                        if (this.$refs.firstInput) {
                            this.$refs.firstInput.focus();
                        }
                        const bankSelect = document.querySelector('#bank_code');
                        if (bankSelect) {
                            bankSelect.addEventListener('change', (e) => {
                                const selectedText = e.target.options[e.target.selectedIndex].text;
                                this.$wire.set('settlement_bank', selectedText);
                            });
                        }
                    });

                    Livewire.on('validationError', () => {
                        this.scrollToFirstError();
                    });

                    Livewire.on('schoolCreated', () => {
                        this.handleSuccess();
                    });

                    this.$watch('$wire.logoPreview', (value) => {
                        this.logoPreview = value;
                    });
                },

                handleSuccess() {
                    this.showSuccessMessage = true;
                    this.scrollToTop();
                    
                    // Start countdown
                    const interval = setInterval(() => {
                        this.redirectCountdown--;
                        if (this.redirectCountdown <= 0) {
                            clearInterval(interval);
                            this.goToSchoolSettings();
                        }
                    }, 1000);
                },

                goToSchoolSettings() {
                    window.location.href = '/school-settings';
                },

                nextStep() {
                    this.$wire.nextStep().then(() => {
                        this.scrollToTop();
                    }).catch(() => {
                        this.scrollToFirstError();
                    });
                },

                formatPhoneNumber(event) {
                    let value = event.target.value.replace(/\D/g, '');
                    if (value.length >= 10) {
                        value = value.substring(0, 12);
                        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '+233 $1 $2 $3');
                    }
                    event.target.value = value;
                    this.$wire.set('phone', value);
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.logoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                scrollToTop() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                scrollToFirstError() {
                    this.$nextTick(() => {
                        const firstError = document.querySelector('.border-red-500');
                        if (firstError) {
                            firstError.scrollIntoView({ block: 'center', behavior: 'smooth' });
                        }
                    });
                },
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</section>
