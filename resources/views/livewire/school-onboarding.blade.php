<section>
    <div x-data="schoolOnboardingForm()" x-init="init()"
         class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        {{-- Progress Bar --}}
        <div class="bg-white shadow-sm border-b dark:bg-gray-800 dark:border-gray-700">
            <div class="max-w-4xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">School Registration</h1>
                    <span class="text-sm text-gray-500 dark:text-gray-300">Step <span
                            x-text="currentStep"></span> of {{ $totalSteps }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                    <div
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500 ease-out dark:from-blue-600 dark:to-indigo-700"
                        :style="`width: ${(currentStep / {{ $totalSteps }}) * 100}%`"></div>
                </div>
            </div>
        </div>


        <div class="max-w-4xl mx-auto py-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden dark:bg-gray-800 dark:shadow-gray-900/20">

                <section>
                    <form enctype="multipart/form-data" id="schoolForm">
                        {{-- Step 1: School Details --}}
                        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-x-4"
                             x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="px-8 py-6">
                                <div class="text-center mb-8">
                                    <div
                                        class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600"
                                             fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 3L2 9l10 6 10-6-10-6z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2 9v7a2 2 0 002 2h16a2 2 0 002-2V9M12 21v-6"/>
                                        </svg>
                                    </div>

                                    <h2 class="text-2xl font-bold text-gray-900 mb-2">School Information</h2>
                                    <p class="text-gray-600">Let's start with the basic details about your school</p>
                                </div>

                                {{-- Basic Information --}}
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div class="lg:col-span-2">
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                School Name <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="name" wire:model.defer="name"
                                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('name') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="Enter your school name" x-ref="firstInput">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="absolute left-3 top-3.5 text-gray-400 h-5 w-5 dark:text-gray-300" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 14v7m0 0l3-3m-3 3l-3-3"/>
                                                </svg>
                                            </div>
                                            @error('name')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                School Email <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="email" id="email" wire:model.defer="email"
                                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('email') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="school@example.com">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="absolute left-3 top-3.5 text-gray-400 h-5 w-5 dark:text-gray-300" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            @error('email')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                Phone Number
                                            </label>
                                            <div class="relative">
                                                <input type="tel" id="phone" wire:model.defer="phone"
                                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('phone') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="+233 XX XXX XXXX"
                                                       x-on:input="formatPhoneNumber($event)">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="absolute left-3 top-3.5 text-gray-400 h-5 w-5 dark:text-gray-300" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            </div>
                                            @error('phone')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                School Type <span class="text-red-500">*</span>
                                            </label>
                                            <select id="type" wire:model.defer="type"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('type') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                                <option value="" class="dark:bg-gray-700 dark:text-white">Select school type</option>
                                                @foreach($this->schoolTypes as $value => $label)
                                                    <option value="{{ $value }}" class="dark:bg-gray-700 dark:text-white">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('type')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="ownership" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                Ownership Type <span class="text-red-500">*</span>
                                            </label>
                                            <select id="ownership" wire:model.defer="ownership"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('ownership') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                                <option value="" class="dark:bg-gray-700 dark:text-white">Select ownership type</option>
                                                @foreach($this->ownershipTypes as $value => $label)
                                                    <option value="{{ $value }}" class="dark:bg-gray-700 dark:text-white">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('ownership')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                Website
                                            </label>
                                            <div class="relative">
                                                <input type="url" id="website" wire:model.defer="website"
                                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('website') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="https://www.yourschool.edu.gh">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="absolute left-3 top-3.5 text-gray-400 h-5 w-5 dark:text-gray-300" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            @error('website')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="established_date"
                                                   class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                Established Date
                                            </label>
                                            <div class="relative">
                                                <input type="date" id="established_date"
                                                       wire:model.defer="established_date"
                                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('established_date') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="absolute left-3 top-3.5 text-gray-400 h-5 w-5 dark:text-gray-300" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            @error('established_date')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="student_capacity"
                                                   class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                Student Capacity <span class="text-red-500">*</span>
                                            </label>

                                            <div class="relative">
                                                <input type="number" id="student_capacity"
                                                       wire:model.defer="student_capacity" min="1"
                                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('student_capacity') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="e.g., 500">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="absolute left-3 top-3.5 text-gray-400 h-5 w-5 dark:text-gray-300" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                            </div>
                                            @error('student_capacity')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label for="description"
                                                   class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                School Description
                                            </label>
                                            <textarea id="description" wire:model.defer="description" rows="4"
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('description') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                      placeholder="Brief description of your school, its mission, and values..."></textarea>
                                            @error('description')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                            @enderror
                                        </div>

                                        {{-- Logo Upload Section --}}
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                School Logo
                                            </label>
                                            <div class="flex items-center space-x-6">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden dark:border-gray-600">
                                                        <template x-if="logoPreview">
                                                            <img :src="logoPreview"
                                                                 class="w-full h-full object-cover rounded-lg">
                                                        </template>
                                                        <template x-if="!logoPreview">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-gray-400 text-xl h-6 w-6 dark:text-gray-300" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="relative">
                                                        <input type="file" id="logo" wire:model="logo" accept="image/*"
                                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                               x-on:change="handleFileUpload($event)">
                                                        <div
                                                            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors duration-200 dark:border-gray-600 dark:hover:border-blue-500">

                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-gray-400 text-xl mb-2 h-6 w-6 dark:text-gray-300" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                            </svg>
                                                            <p class="text-sm text-gray-600 dark:text-gray-300">Click to upload or drag and
                                                                drop</p>
                                                            <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">PNG, JPG, GIF up to
                                                                2MB
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @error('logo')
                                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $message }}
                                                    </p>
                                                    @enderror
                                                    <div wire:loading wire:target="logo"
                                                         class="mt-2 text-sm text-blue-600 flex items-center">
                                                        <svg class="animate-spin mr-1 h-4 w-4 text-blue-600"
                                                             xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        Uploading logo...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Address Section --}}
                                    <div class="border-t pt-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center dark:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 mr-2 h-5 w-5 dark:text-blue-400"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Address Information
                                        </h3>

                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <div class="lg:col-span-2">
                                                <label for="address"
                                                       class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                    Street Address <span class="text-red-500">*</span>
                                                </label>
                                                <textarea id="address" wire:model.defer="address" rows="3"
                                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('address') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                          placeholder="Enter complete street address"></textarea>
                                                @error('address')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message}}
                                                </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                    City <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="city" wire:model.defer="city"
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('city') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="e.g., Accra">
                                                @error('city')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                    Region/State <span class="text-red-500">*</span>
                                                </label>
                                                <select id="state" wire:model.defer="state"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('state') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror">
                                                    <option value="" class="dark:bg-gray-700 dark:text-white">Select region</option>
                                                    @foreach($this->ghanaRegions as $region)
                                                        <option value="{{ $region }}" class="dark:bg-gray-700 dark:text-white">{{ $region }}</option>
                                                    @endforeach
                                                </select>
                                                @error('state')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message}}
                                                </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="country"
                                                       class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                    Country <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="country" wire:model.defer="country"
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('country') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       readonly>
                                                @error('country')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="postal_code"
                                                       class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                                                    Postal Code
                                                </label>
                                                <input type="text" id="postal_code" wire:model.defer="postal_code"
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('postal_code') border-red-500 ring-2 ring-red-200 dark:ring-red-900/50 @enderror"
                                                       placeholder="e.g., GA-123-4567">
                                                @error('postal_code')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message}}
                                                </p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Academic Structure --}}
                        <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0transform translate-x-4"
                             x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="px-8 py-6">
                                <div class="text-center mb-8">
                                    <div
                                        class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600"
                                             fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 14v7m0 0l3-3m-3 3l-3-3"/>
                                        </svg>
                                    </div>

                                    <h2 class="text-2xl font-bold text-gray-900mb-2">Academic Structure</h2>
                                    <p class="text-gray-600">Configure your school's academic organization
                                        (optional)</p>
                                </div>

                                <div class="space-y-8">
                                    {{-- Academic Groups Selection --}}
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Groups</h3>
                                        <p class="text-sm text-gray-600 mb-4">Select the academic divisions your school
                                            offers</p>

                                        <div class="grid grid-cols-1 md:grid-cols-2lg:grid-cols-3 gap-4">
                                            @foreach($this->availableAcademicGroups as $group)
                                                <label
                                                    class="group relative flex items-center p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-300 cursor-pointer transition-all duration-200 hover:shadow-md">
                                                    <input type="checkbox" wire:model.live="selectedAcademicGroups"
                                                           value="{{ $group->id }}" class="sr-only">
                                                    <div class="flex items-center w-full">
                                                        <div
                                                            class="flex-shrink-0 w-5 h-5 mr-3 border-2 border-gray-300 rounded group-hover:border-blue-500 transition-colors duration-200 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3
                                                        opacity-0 group-hover:opacity-100 transition-opacity
                                                        duration-200" fill="none" viewBox="0 0 24 24"
                                                                 stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                    <span
                                                        class="text-sm font-mediumtext-gray-900 group-hover:text-blue-600 transition-colors duration-200">{{
                                                        $group->name }}</span>
                                                            @if($group->description)
                                                                <p class="text-xs text-gray-500 mt-1">{{ $group->description }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="absolute inset-0 bg-blue-50 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-200">
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Academic Levels Selection --}}
                                    <div x-show="$wire.selectedAcademicGroups.length > 0" x-transition>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Levels</h3>
                                        <p class="text-sm text-gray-600 mb-4">Select specific levels within your chosen
                                            academic groups</p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($this->availableAcademicLevels as $level)
                                                <label
                                                    class="group relative flex items-center p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-green-300 cursor-pointer transition-all duration-200 hover:shadow-md">
                                                    <input type="checkbox" wire:model.live="selectedAcademicLevels"
                                                           value="{{ $level->id }}" class="sr-only">
                                                    <div class="flex items-center w-full">
                                                        <div
                                                            class="flex-shrink-0 w-5 h-5 mr-3 border-2 border-gray-300 rounded group-hover:border-green-500 transition-colors duration-200flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3
                                                        opacity-0 group-hover:opacity-100 transition-opacity
                                                        duration-200" fill="none" viewBox="0 0 24 24"
                                                                 stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                    <span
                                                        class="text-sm font-mediumtext-gray-900 group-hover:text-green-600 transition-colors duration-200">{{
                                                        $level->name }}</span>
                                                            <p class="text-xs text-gray-500 mt-1">{{ $level->academicGroup->name
                                                        }}</p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="absolute inset-0 bg-green-50 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-200">
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Settings Section --}}
                                    <div class="border-t pt-8">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 mr-2 h-5 w-5"
                                                 fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.7240 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            School Settings
                                        </h3>

                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <div>
                                                <label for="timezone"
                                                       class="block text-sm font-medium text-gray-700 mb-2">
                                                    Timezone
                                                </label>
                                                <select id="timezone" wire:model.defer="timezone"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                                    @foreach($this->timezones as $value => $label)
                                                        <option value="{{ $value }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label for="currency"
                                                       class="block text-sm font-medium text-gray-700 mb-2">
                                                    Currency
                                                </label>
                                                <select id="currency" wire:model.defer="currency"
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparenttransition-all duration-200">
                                                    @foreach($this->currencies as $value => $label)
                                                        <option value="{{ $value }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label for="academic_year_start"
                                                       class="block text-sm font-medium text-gray-700 mb-2">
                                                    AcademicYear Start
                                                </label>
                                                <input type="date" id="academic_year_start"
                                                       wire:model.defer="academic_year_start"
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('academic_year_start') border-red-500 ring-2 ring-red-200 @enderror">
                                                @error('academic_year_start')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="academic_year_end"
                                                       class="block text-sm font-medium text-gray-700 mb-2">
                                                    Academic Year End
                                                </label>
                                                <input type="date" id="academic_year_end"
                                                       wire:model.defer="academic_year_end"
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('academic_year_end') border-red-500 ring-2 ring-red-200 @enderror">
                                                @error('academic_year_end')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: BankInformation --}}
                        <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-x-4"
                             x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="px-8 py-6">
                                <div class="text-center mb-8">
                                    <div
                                        class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-600"
                                             fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M310h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>

                                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Bank Information</h2>
                                    <p class="text-gray-600">Provide your bank details for payment processing
                                        (optional)</p>
                                </div>

                                <div class="space-y-8">
                                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                        <div class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-500 h-5 w-5 mt-0.5 mr-3
                                    flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M1316h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-sm text-blue-800">
                                                Providing bank information enables you to receive payments for
                                                subscriptions
                                                and
                                                other services.
                                                You can skip this step and add bank details later in your school
                                                settings.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border border-gray-200rounded-xl p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Bank Account Details</h3>

                                        <div class="space-y-6">
                                            {{-- Bank Selection --}}
                                            <div>
                                                <label for="bank_code"
                                                       class="blocktext-sm font-medium text-gray-700 mb-2">
                                                    Select Bank
                                                </label>
                                                <select id="bank_code" name="bank_code" wire:model.defer="bank_code"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                    <option value="">-- Select Bank --</option>
                                                    @foreach(App\Constants\GhanaBanks::all() as $key => $bank)
                                                        <option value="{{ $key }}">{{ $bank }}</option>
                                                    @endforeach
                                                </select>
                                                @error('bank_code')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror

                                                {{-- Hidden field for bank name --}}
                                                <input type="hidden" name="settlement_bank" id="settlement_bank"
                                                       wire:model.defer="settlement_bank">
                                            </div>

                                            <div>
                                                <label for="account_number"
                                                       class="block text-sm font-medium text-gray-700mb-2">
                                                    Account Number
                                                </label>
                                                <input type="text" name="account_number" id="account_number"
                                                       wire:model.defer="account_number"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('account_number') border-red-500 ring-2 ring-red-200 @enderror"
                                                       placeholder="Enter your account number">
                                                @error('account_number')
                                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4"
                                                         fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4: Review & Confirmation --}}
                        <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-x-4"
                             x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="px-8 py-6">
                                <div class="text-center mb-8">
                                    <div
                                        class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600"
                                             fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M1512a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>

                                    <h2 class="text-2xl font-boldtext-gray-900 mb-2">ReviewYour Information</h2>
                                    <p class="text-gray-600">Please review all details before completing setup</p>
                                </div>

                                {{-- Enhanced School Summary Card --}}
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 border border-blue-200">
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0 mr-4">
                                            <template x-if="logoPreview">
                                                <img :src="logoPreview" alt="School Logo"
                                                     class="w-16 h-16 rounded-xl object-cover shadow-md">
                                            </template>
                                            <template x-if="!logoPreview">
                                                <div
                                                    class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="text-white text-2xl h-6 w-6" fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900" x-text="$wire.name"></h3>
                                            <p class="text-blue-600 font-medium" x-text="$wire.email"></p>
                                            <p class="text-gray-600 text-sm" x-text="$wire.phone"
                                               x-show="$wire.phone"></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-3">
                                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-blue-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <div>
                                                <span
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Type</span>
                                                    <p class="text-sm font-semibold text-gray-900 capitalize"
                                                       x-text="$wire.type"></p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-blue-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                <div>
                                                <span
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ownership</span>
                                                    <p class="text-sm font-semibold text-gray-900 capitalize"
                                                       x-text="$wire.ownership"></p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm"
                                                 x-show="$wire.student_capacity">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-blue-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                <div>
                                                    <span
                                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Capacity</span>
                                                    <p class="text-sm font-semibold text-gray-900"
                                                       x-text="$wire.student_capacity ? $wire.student_capacity.toLocaleString() + ' students' : ''">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-blue-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <div>
                                                <span
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</span>
                                                    <p class="text-sm font-semibold text-gray-900"
                                                       x-text="`${$wire.city}, ${$wire.state}`"></p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm"
                                                 x-show="$wire.website">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-blue-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                <span
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Website</span>
                                                    <p class="text-sm font-semibold text-blue-600 truncate"
                                                       x-text="$wire.website"></p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm"
                                                 x-show="$wire.established_date">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-blue-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <div>
                                                <span
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Established</span>
                                                    <p class="text-sm font-semibold text-gray-900"
                                                       x-text="$wire.established_date ? new Date($wire.established_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long'}) : ''">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm" x-show="$wire.description">
                                    <span
                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Description</span>
                                        <p class="mt-1 text-sm text-gray-700" x-text="$wire.description"></p>
                                    </div>

                                    {{-- Academic Structure Summary --}}
                                    <div class="mt-6 space-y-4" x-show="$wire.selectedAcademicGroups.length > 0">
                                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3 block">AcademicGroups++</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($this->availableAcademicGroups->whereIn('id',
                                                $this->selectedAcademicGroups) as $group)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M12 14v7m0 0l3-3m-3 3l-3-3"/>
                                                </svg>
                                                {{ $group->name }}
                                            </span>
                                                @endforeach

                                            </div>
                                        </div>

                                        <div class="p-4 bg-white rounded-lg shadow-sm"
                                             x-show="$wire.selectedAcademicLevels.length > 0">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3 block">Academic
                                            Levels</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($this->availableAcademicLevels->whereIn('id',
                                                $this->selectedAcademicLevels) as $level)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                {{ $level->name }}
                                                <span class="ml-1 text-green-600 opacity-75">({{
                                                    $level->academicGroup->name }})</span>
                                            </span>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>

                                    {{-- Settings Summary --}}
                                    <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                    <span
                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3 block">Settings</span>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-gray-400 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-gray-700" x-text="$wire.timezone"></span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-gray-400 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-gray-700" x-text="$wire.currency"></span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-gray-400 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-gray-700" x-text="formatAcademicYear()"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Enhanced Confirmation --}}
                                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                                    <label class="flex items-start cursor-pointer group">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" x-model="confirmed"
                                                   class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition-all duration-200">
                                        </div>
                                        <div class="ml-4">
                                            <p
                                                class="text-sm font-semibold text-blue-900 group-hover:text-blue-700 transition-colors duration-200">
                                                I confirm that all information provided is accurate and complete
                                            </p>
                                            <p class="mt-1 text-sm text-blue-700">
                                                By proceeding, I agree to create this school profile and become its
                                                administrator.
                                                I understand that some information can be modified later in the school
                                                settings.
                                            </p>
                                        </div>
                                    </label>
                                </div>


                                {{-- Step 5: Success --}}
                                <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-500"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100">

                                    {{-- Bank details here --}}
                                    <div class="px-8 py-12 text-center">
                                        <div class="mb-8">
                                            <div
                                                class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-green-600 text-4xl w-16 h-16" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h2 class="text-3xl font-bold text-gray-900 mb-4">School Setup
                                                Complete!</h2>
                                            <p class="text-lg text-gray-600 mb-2"
                                               x-text="`${$wire.name} has been successfully registered`"></p>
                                            @if($createdSchool)
                                                <p class="text-sm text-gray-500">School Code: <span
                                                        class="font-mono font-semibold">{{
                                        $createdSchool->code }}</span></p>
                                            @endif
                                        </div>

                                        @if($createdSchool)
                                            {{-- Success Summary --}}
                                            <div
                                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-8 text-left max-w-2xl mx-auto shadow-lg">
                                                <div class="flex items-center justify-center mb-6">
                                                    @if($createdSchool->logo_url)
                                                        <img src="{{ Storage::url($createdSchool->logo_url) }}"
                                                             alt="{{ $createdSchool->name }} Logo"
                                                             class="w-16 h-16 rounded-xl object-cover shadow-mdmr-4">
                                                    @else
                                                        <div
                                                            class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center shadow-md mr-4">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-white text-2xl w-6 h-6" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="text-center">
                                                        <h3 class="text-xl font-bold text-gray-900">{{ $createdSchool->name }}</h3>
                                                        <p class="text-sm text-gray-500">Successfully Registered</p>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 text-center">
                                                    <div class="bg-blue-50 rounded-lg p-4">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             class="text-blue-600 text-xl mb-2 w-6 h-6" fill="none"
                                                             viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2" d="M12 14v7m0 0l3-3m-3 3l-3-3"/>
                                                        </svg>
                                                        <p class="text-sm font-medium text-gray-700">Academic Groups</p>
                                                        <p class="text-lg font-bold text-blue-600">{{
                                            $createdSchool->academicGroups()->count() }}</p>
                                                    </div>
                                                    <div class="bg-green-50 rounded-lg p-4">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             class="text-green-600 text-xl mb-2 w-6 h-6" fill="none"
                                                             viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                        </svg>
                                                        <p class="text-sm font-medium text-gray-700">Academic Levels</p>
                                                        <p class="text-lg font-bold text-green-600">{{
                                            $createdSchool->academicLevels()->count() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif


                                        {{-- Next Steps Guide --}}
                                        <div
                                            class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 mb-8 text-left max-w-3xl mx-auto border border-indigo-200">
                                            <h3
                                                class="text-xl font-bold text-gray-900 mb-6 text-center flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="text-indigo-600 mr-2 h-5 w-5" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                What's Next?
                                            </h3>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div
                                                    class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                                    <div class="flex items-start">
                                                        <div
                                                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-blue-600 w-5 h-5" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-semibold text-gray-900 mb-1">Add Staff &
                                                                Students</h4>
                                                            <p class="text-sm text-gray-600">Start adding teachers,
                                                                librarians, and
                                                                students to your school</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                                    <div class="flex items-start">
                                                        <div
                                                            class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-green-600 w-5 h-5" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-semibold text-gray-900 mb-1">Library
                                                                Management</h4>
                                                            <p class="text-sm text-gray-600">Add books and configure
                                                                lending
                                                                policies</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                                    <div class="flex items-start">
                                                        <div
                                                            class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-purple-600 w-5 h-5" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2" d="M12 14v7m0 0l3-3m-3 3l-3-3"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-semibold text-gray-900 mb-1">Academic
                                                                Structure</h4>
                                                            <p class="text-sm text-gray-600">Fine-tune classes and
                                                                academic
                                                                organization</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                                    <div class="flex items-start">
                                                        <div
                                                            class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="text-orange-600 w-5 h-5" fill="none"
                                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-semibold text-gray-900 mb-1">Configure
                                                                Settings</h4>
                                                            <p class="text-sm text-gray-600">Customize preferences and
                                                                system
                                                                configurations</p>
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--Action Buttons --}}
                                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                            <button type="button" wire:click="completeOnboarding"
                                                    class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold
        hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300
        transformhover:scale-105 transition-all duration-200 shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                Goto Dashboard
                                            </button>
                                            <button type="button"
                                                    onclick="window.location.href = '/admin/school/settings'"
                                                    class="px-8 py-4 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 focus:outline-none
        focus:ring-4 focus:ring-gray-300 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                ConfigureSettings
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </form>


                    <div x-show="currentStep < 4" class="px-8 py-6 bg-gray-50 border-t">
                        <div class="flex justify-between items-center">
                            <div>
                                <template x-if="currentStep > 1">
                                    <button type="button" @click="previousStep()"
                                            class="px-6 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                        </svg>
                                        Back
                                    </button>
                                </template>
                            </div>

                            <div>
                                <template x-if="currentStep < 4">
                                    <button type="button" @click="nextStep()"
                                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center">
                                        Continue
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                </template>

                                <template x-if="currentStep === 4">
                                    <button type="button" @click="nextStep()"
                                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        Review Setup
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Final Step Navigation -->
                    <div x-show="currentStep === 4" class="px-8 py-6 bg-gray-50 border-t">
                        <div class="flex justify-center">
                            <button type="button" wire:click="createSchool"
                                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-4 focus:ring-purple-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200 shadow-lg flex items-center">
            <span wire:loading.remove wire:target="createSchool">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Complete Setup
            </span>
                                <span wire:loading wire:target="createSchool">
                Processing...
            </span>
                            </button>
                        </div>
                    </div>

                </section>
            </div>

            {{-- QuickTips Sidebar --}}
            <div x-show="currentStep === 1" x-transition
                 class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-blue-200 dark:border-blue-800">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-yellow-600 h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    QuickSetup Tips
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 mt-1 mr-3 flex-shrink-0 h-5 w-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm"><strong class="text-blue-900">School Name:</strong> Use your official
                                school name as it appears on documents</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-green-50rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-green-600 mt-1 mr-3 flex-shrink-0 h-5 w-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm"><strong class="text-green-900">Email:</strong> Use an official school
                                email address for communications</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-purple-600 mt-1 mr-3 flex-shrink-0 h-5 w-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm">
                                <strong class="text-purple-900">Flexibility:</strong> Most settings can be
                                modified later from your dashboard
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        document.addEventListener('livewire:load', function () {
            document.getElementById('bank_code').addEventListener('change', function (e) {
                const selectedText = e.target.options[e.target.selectedIndex].text;
                @this.
                set('settlement_bank', selectedText);
            });
        });


        function schoolOnboardingForm() {
            return {
                currentStep: @entangle('currentStep'),
                loading: @entangle('loading'),
                confirmed: false,
                logoPreview: null,

                init() {
                    // Auto-focus first input when component loads
                    this.$nextTick(() => {
                        if (this.$refs.firstInput) {
                            this.$refs.firstInput.focus();
                        }
                        const bankSelect = document.querySelector('#bank_code');
                        if (bankSelect) {
                            bankSelect.addEventListener('change', (e) => {
                                const selectedOption = e.target.options[e.target.selectedIndex];
                                document.querySelector('#settlement_bank').value = selectedOption.text;
                                @this.
                                set('settlement_bank', selectedOption.text);
                            });
                        }
                    });

                    // Listen for Livewire events
                    Livewire.on('validationError', () => {
                        this.scrollToFirstError();
                    });

                    Livewire.on('schoolCreated', () => {
                        this.currentStep = 5;
                    });

                    // Watch for logo preview updates
                    this.$watch('$wire.logoPreview', (value) => {
                        this.logoPreview = value;
                    });
                },

                nextStep() {
                    if (this.currentStep === 1) {
                        this.$wire.validateCurrentStep().then(() => {
                            this.currentStep = 2;
                            this.scrollToTop();
                        }).catch(() => {
                            this.scrollToFirstError();
                        });
                    } else if (this.currentStep < 4) {
                        this.currentStep++;
                        this.scrollToTop();
                    }
                },

                previousStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                        this.scrollToTop();
                    }
                },

                canProceedStep1() {
                    return this.$wire.name &&
                        this.$wire.email &&
                        this.$wire.type &&
                        this.$wire.ownership &&
                        this.$wire.address &&
                        this.$wire.city &&
                        this.$wire.student_capacity &&
                        this.$wire.state;
                },

                canProceedAnReview() {
                    return this.$wire.selectedAcademicLevels.length > 0;
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

                formatAcademicYear() {
                    if (this.$wire.academic_year_start && this.$wire.academic_year_end) {
                        const start = new Date(this.$wire.academic_year_start);
                        const end = new Date(this.$wire.academic_year_end);
                        return `${start.toLocaleDateString('en-US', {month: 'short', year: 'numeric'})} -
        ${end.toLocaleDateString('en-US', {month: 'short', year: 'numeric'})}`;
                    }
                    return 'Not set';
                },

                scrollToTop() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                },

                scrollToFirstError() {
                    this.$nextTick(() => {
                        const firstError = document.querySelector('.border-red-500');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            firstError.focus();
                        }
                    });
                },
            }
        }

        // Global Livewire hooks
        document.addEventListener('livewire:load', function () {
            // Handle step changesLivewire.hook('message.processed', (message, component) => {
            if (message.updateQueue && message.updateQueue.some(update =>
                update.payload.method === 'nextStep' ||
                update.payload.method === 'previousStep' ||
                update.payload.method === 'createSchool'
            )) {
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });

        // Auto-save functionality
        let autoSaveTimer;
        Livewire.hook('message.sent', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                if (window.livewire.find('school-onboarding')) {
                    window.livewire.find('school-onboarding').call('saveDraft');
                }
            }, 30000); // Auto-save every 30 seconds
        });

        // Handle bank selection
        document.getElementById('bank_code').addEventListener('change', function (e) {
            const selectedText = e.target.options[e.target.selectedIndex].text;
            @this.
            set('settlement_bank', selectedText);
        });


        // Prevent form submission on Enter key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.type !== 'textarea' && e.target.type !== 'submit') {
                e.preventDefault();
                return false;
            }
        });
    </script>


    <style>

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        /* Enhanced form styling */
        input:focus,
        select:focus,
        textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        /* Checkbox styling improvements */
        input[type="checkbox"]:checked {
            background-color: #3B82F6;
            border-color: #3B82F6;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns= 'http://www.w3.org/2000/svg' %3e%3cpath d= 'm13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z '/%3e%3c/svg%3e");
        }

        /* Custom checkbox for academic groups/levels */
        .group input[type= "checkbox" ]:checked + div .w-5.h-5 {
            background-color: #3B82F6;
            border-color: #3B82F6;
        }

        .group input[type= "checkbox" ]:checked + div .w-5.h-5 i {
            opacity: 1 !important;
        }

        /* Loading animation improvements */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
            box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Progress bar enhancement */
        .progress-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        /* File upload hover effects */
        .file-upload-area:hover {
            background: linear-gradient(135deg, #EBF4FF 0%, #DBEAFE 100%);
        }

        /* Success animations */
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .bounce-in {
            animation: bounceIn 0.6s ease-out;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .grid.lg\\:grid-cols-2 {
                gap: 1rem;
            }

            .px-8 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Enhanced focus states */
        .focus\\:ring-blue-500:focus {
            --tw-ring-color: rgb(59 130 246 / 0.3);
            --tw-ring-offset-width: 2px;
        }

        /* Smooth step transitions */
        [x-transition] {
            transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Button hover effects */
        button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        button:active:not(:disabled) {
            transform: translateY(0);
        }

        /* Error state animations */
        .border-red-500 {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Custom select styling */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20 '%3e%3cpath stroke=' %236b7280 ' stroke-linecap=' round ' stroke-linejoin=' round ' stroke-width=' 1.5 ' d=' m6 8 4 4 4-4 '/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        /* File input styling */
        input[type= "file" ] {
            font-size: 0;
        }

        input[type= "file" ]::-webkit-file-upload-button {
            visibility: hidden;
        }

        /* Improved form validation styling */
        .border-red-500 {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Loading state improvements */
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Gradient text effects */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Success checkmark animation */
        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .checkmark-animate {
            animation: checkmark 0.6s ease-out;
        }

        /* Form field focus glow */
        .field-glow:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 20px rgba(59, 130, 246, 0.05);
        }

        /* Disabled state improvements */
        button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            transform: none !important;
        }

        /* Mobile responsive enhancements */
        @media (max-width: 768px) {
            .text-2xl {
                font-size: 1.5rem;
            }

            .text-xl {
                font-size: 1.125rem;
            }

            .grid.lg\\:grid-cols-2 > div {
                margin-bottom: 0.5rem;
            }
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>


</section>
