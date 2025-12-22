<section>
    <div x-data="schoolOnboardingForm()" x-init="init()"
         class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
        
        {{-- Progress Bar Header --}}
        <div class="sticky top-0 z-40 bg-white shadow-md border-b dark:bg-gray-800 dark:border-gray-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">School Registration</h1>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                        Step <span x-text="currentStep"></span> of {{ $totalSteps }}
                    </span>
                </div>
                
                {{-- Progress Bar --}}
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out dark:from-blue-400 dark:via-purple-400 dark:to-indigo-500"
                         :style="`width: ${(currentStep / {{ $totalSteps }}) * 100}%`"></div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden dark:bg-gray-800 dark:shadow-2xl">
                <form enctype="multipart/form-data" id="schoolForm" wire:submit.prevent="createSchool">
                    
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
                            {{-- School Name --}}
                            <div class="lg:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    School Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="name" wire:model.defer="name" x-ref="firstInput"
                                           class="w-full px-4 py-3 pl-10 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                           placeholder="Enter your school name">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3.5 text-gray-400 dark:text-gray-500 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4H9m0 4a2 2 0 110-4m0 4v2m0-6V9a2 2 0 010-4h0a2 2 0 010 4m0 6v2m0 0h.581"/>
                                    </svg>
                                </div>
                                @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Email and Phone --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Email <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" wire:model.defer="email"
                                               class="w-full px-4 py-3 pl-10 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('email') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                               placeholder="school@example.com">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3.5 text-gray-400 dark:text-gray-500 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <input type="tel" id="phone" wire:model.defer="phone"
                                               class="w-full px-4 py-3 pl-10 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('phone') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                               placeholder="+233 XX XXX XXXX"
                                               x-on:input="formatPhoneNumber($event)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3.5 text-gray-400 dark:text-gray-500 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    @error('phone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Type and Ownership --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        School Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="type" wire:model.defer="type"
                                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('type') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                        <option value="">Select school type</option>
                                        @foreach($this->schoolTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ownership" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Ownership Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="ownership" wire:model.defer="ownership"
                                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('ownership') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                        <option value="">Select ownership type</option>
                                        @foreach($this->ownershipTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('ownership')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Website and Student Capacity --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="website" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Website
                                    </label>
                                    <div class="relative">
                                        <input type="url" id="website" wire:model.defer="website"
                                               class="w-full px-4 py-3 pl-10 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('website') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                               placeholder="https://www.yourschool.edu.gh">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3.5 text-gray-400 dark:text-gray-500 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    @error('website')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="student_capacity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Student Capacity <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="student_capacity" wire:model.defer="student_capacity" min="1"
                                               class="w-full px-4 py-3 pl-10 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('student_capacity') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                               placeholder="e.g., 500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3.5 text-gray-400 dark:text-gray-500 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    @error('student_capacity')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Established Date --}}
                            <div>
                                <label for="established_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Established Date
                                </label>
                                <div class="relative">
                                    <input type="date" id="established_date" wire:model.defer="established_date"
                                           class="w-full px-4 py-3 pl-10 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('established_date') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3.5 text-gray-400 dark:text-gray-500 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @error('established_date')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    School Description
                                </label>
                                <textarea id="description" wire:model.defer="description" rows="4"
                                          class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                          placeholder="Brief description of your school, its mission, and values..."></textarea>
                                @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Address Section --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 dark:text-blue-400 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Address Information
                                </h3>

                                <div class="space-y-6">
                                    <div>
                                        <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Street Address <span class="text-red-500">*</span>
                                        </label>
                                        <textarea id="address" wire:model.defer="address" rows="3"
                                                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 resize-none @error('address') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                  placeholder="Enter complete street address"></textarea>
                                        @error('address')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="city" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                City <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="city" wire:model.defer="city"
                                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('city') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                   placeholder="e.g., Accra">
                                            @error('city')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="state" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Region/State <span class="text-red-500">*</span>
                                            </label>
                                            <select id="state" wire:model.defer="state"
                                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('state') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                                <option value="">Select region</option>
                                                @foreach($this->ghanaRegions as $region)
                                                    <option value="{{ $region }}">{{ $region }}</option>
                                                @endforeach
                                            </select>
                                            @error('state')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="country" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Country <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="country" wire:model.defer="country"
                                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('country') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                   readonly>
                                            @error('country')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="postal_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Postal Code
                                            </label>
                                            <input type="text" id="postal_code" wire:model.defer="postal_code"
                                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('postal_code') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                   placeholder="e.g., GA-123-4567">
                                            @error('postal_code')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Logo Upload --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            School Logo
                                        </label>
                                        <div class="flex items-center space-x-6">
                                            <div class="flex-shrink-0">
                                                <div class="w-24 h-24 rounded-lg border-4 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-700">
                                                    <template x-if="logoPreview">
                                                        <img :src="logoPreview" class="w-full h-full object-cover rounded-lg" alt="Logo preview">
                                                    </template>
                                                    <template x-if="!logoPreview">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-400 dark:text-gray-500 h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="relative">
                                                    <input type="file" id="logo" wire:model="logo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-on:change="handleFileUpload($event)">
                                                    <div class="border-4 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700/50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-400 dark:text-gray-500 h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                        </svg>
                                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Click to upload or drag and drop</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                                                    </div>
                                                </div>
                                                @error('logo')
                                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror
                                                <div wire:loading wire:target="logo" class="mt-2 text-sm text-blue-600 dark:text-blue-400 flex items-center">
                                                    <svg class="animate-spin mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Uploading logo...
                                                </div>
                                            </div>
                                        </div>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m0 0l3-3m-3 3l-3-3"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Academic Structure</h2>
                            <p class="text-gray-600 dark:text-gray-400">Configure your school's academic organization</p>
                        </div>

                        <div class="space-y-8">
                            {{-- Academic Groups Selection --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Academic Groups</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select the academic divisions your school offers</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->availableAcademicGroups as $group)
                                        <label class="group relative flex items-center p-4 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-green-400 dark:hover:border-green-500 cursor-pointer transition-all duration-200 hover:shadow-md">
                                            <input type="checkbox" wire:model.live="selectedAcademicGroups" value="{{ $group->id }}" class="sr-only">
                                            <div class="flex items-center w-full">
                                                <div class="flex-shrink-0 w-5 h-5 mr-3 border-2 border-gray-400 dark:border-gray-500 rounded group-hover:border-green-500 dark:group-hover:border-green-400 transition-colors duration-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-200">{{ $group->name }}</span>
                                                    @if($group->description)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $group->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Academic Levels Selection --}}
                            <div x-show="$wire.selectedAcademicGroups.length > 0" x-transition>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Academic Levels</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select specific levels within your chosen academic groups</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->availableAcademicLevels as $level)
                                        <label class="group relative flex items-center p-4 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-400 dark:hover:border-blue-500 cursor-pointer transition-all duration-200 hover:shadow-md">
                                            <input type="checkbox" wire:model.live="selectedAcademicLevels" value="{{ $level->id }}" class="sr-only">
                                            <div class="flex items-center w-full">
                                                <div class="flex-shrink-0 w-5 h-5 mr-3 border-2 border-gray-400 dark:border-gray-500 rounded group-hover:border-blue-500 dark:group-hover:border-blue-400 transition-colors duration-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">{{ $level->name }}</span>
                                                    @if($level->academicGroup)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $level->academicGroup->name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
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
                            <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/30 dark:to-amber-800/30 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M7 20h10M9 5h6M6 9h12a3 3 0 013 3v7a3 3 0 01-3 3H6a3 3 0 01-3-3v-7a3 3 0 013-3z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Bank Information</h2>
                            <p class="text-gray-600 dark:text-gray-400">Provide your bank details for payment processing (optional)</p>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 dark:text-blue-400 h-5 w-5 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-blue-800 dark:text-blue-300">
                                        Providing bank information enables you to receive payments. You can skip this step and add bank details later in your school settings.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label for="bank_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Select Bank
                                </label>
                                <select id="bank_code" wire:model.defer="bank_code"
                                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200">
                                    <option value="">-- Select Bank --</option>
                                    @php
                                        $banks = [
                                            '030100' => 'Absa Bank Ghana Limited',
                                            '280100' => 'Access Bank (Ghana) Plc',
                                            '080100' => 'Agricultural Development Bank Plc',
                                            '140100' => 'CalBank PLC',
                                            '130100' => 'Ecobank Ghana Plc',
                                            '040100' => 'GCB Bank Limited',
                                            '230100' => 'Guaranty Trust Bank (Ghana) Limited',
                                            '050100' => 'National Investment Bank Limited',
                                            '180100' => 'Prudential Bank Limited',
                                            '190100' => 'Stanbic Bank Ghana Limited',
                                            '020100' => 'Standard Chartered Bank Ghana Plc',
                                            '060100' => 'United Bank for Africa Ghana Limited',
                                        ];
                                    @endphp
                                    @foreach($banks as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_code')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <input type="hidden" id="settlement_bank" wire:model.defer="settlement_bank">
                            </div>

                            <div>
                                <label for="account_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Account Number
                                </label>
                                <input type="text" id="account_number" wire:model.defer="account_number"
                                       class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-200 @error('account_number') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                       placeholder="Enter your account number">
                                @error('account_number')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
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
                            <p class="text-gray-600 dark:text-gray-400">Please verify all details before confirming</p>
                        </div>

                        <div class="space-y-6">
                            {{-- School Information Review --}}
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">School Information</h3>
                                    <button type="button" @click="currentStep = 1" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">Edit</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div><span class="text-gray-600 dark:text-gray-400">School Name:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.name"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">Email:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.email"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">Phone:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.phone || 'Not provided'"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">Type:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.type"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">Ownership:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.ownership"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">Student Capacity:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.student_capacity"></span></div>
                                    <div class="md:col-span-2"><span class="text-gray-600 dark:text-gray-400">Address:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.address"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">City:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.city"></span></div>
                                    <div><span class="text-gray-600 dark:text-gray-400">Region:</span> <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.state"></span></div>
                                </div>
                            </div>

                            {{-- Academic Structure Review --}}
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Structure</h3>
                                    <button type="button" @click="currentStep = 2" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">Edit</button>
                                </div>
                                <div class="text-sm">
                                    <p class="text-gray-600 dark:text-gray-400 mb-2">Academic Levels Selected: <span class="font-medium text-gray-900 dark:text-white" x-text="$wire.selectedAcademicLevels.length"></span></p>
                                </div>
                            </div>

                            {{-- Bank Information Review --}}
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bank Information</h3>
                                    <button type="button" @click="currentStep = 3" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">Edit</button>
                                </div>
                                <div class="text-sm">
                                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium text-gray-900 dark:text-white" x-text="$wire.bank_code ? 'Provided' : 'Not provided'"></span></p>
                                </div>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 rounded-xl p-6">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-green-600 dark:text-green-400 h-5 w-5 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-green-800 dark:text-green-300">
                                        Click "Complete Setup" below to finalize your school registration and start using the platform!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

                {{-- Navigation Buttons for Steps 1-3 --}}
                <div x-show="currentStep < {{ $totalSteps }}" class="px-6 sm:px-8 py-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <button type="button" x-show="currentStep > 1" @click="previousStep()"
                                class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back
                        </button>
                    </div>
                    <div>
                        <button type="button" @click="nextStep()"
                                class="px-8 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-700 text-white rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 dark:hover:from-blue-700 dark:hover:to-indigo-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900 transition-all duration-200 transform hover:scale-105 flex items-center shadow-lg">
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Navigation Buttons for Step 4 (Review) --}}
                <div x-show="currentStep === {{ $totalSteps }}" class="px-6 sm:px-8 py-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <button type="button" @click="previousStep()"
                            class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </button>
                    <div class="flex gap-4">
                        <button type="button" @click="window.location.href = '/dashboard'"
                                class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200">
                            Skip
                        </button>
                        <button type="submit" wire:click="createSchool" wire:loading.attr="disabled"
                                class="px-8 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 dark:from-green-600 dark:to-emerald-700 text-white rounded-lg font-medium hover:from-green-600 hover:to-emerald-700 dark:hover:from-green-700 dark:hover:to-emerald-800 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900 transition-all duration-200 transform hover:scale-105 flex items-center shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="createSchool">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Complete Setup
                            </span>
                            <span wire:loading wire:target="createSchool" class="flex items-center">
                                <svg class="animate-spin mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
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

                init() {
                    // Auto-focus first input
                    this.$nextTick(() => {
                        if (this.$refs.firstInput) {
                            this.$refs.firstInput.focus();
                        }
                    });

                    // Watch for logo updates
                    this.$watch('$wire.logoPreview', (value) => {
                        this.logoPreview = value;
                    });
                },

                nextStep() {
                    this.$wire.nextStep().then(() => {
                        window.scrollTo({top: 0, behavior: 'smooth'});
                    }).catch(() => {
                        this.scrollToFirstError();
                    });
                },

                previousStep() {
                    this.$wire.previousStep();
                    window.scrollTo({top: 0, behavior: 'smooth'});
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

                scrollToFirstError() {
                    this.$nextTick(() => {
                        const firstError = document.querySelector('.border-red-500');
                        if (firstError) {
                            firstError.scrollIntoView({block: 'center'});
                            firstError.focus();
                        }
                    });
                },
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Smooth transitions */
        * {
            @apply transition-colors duration-200;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        dark ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
</section>
