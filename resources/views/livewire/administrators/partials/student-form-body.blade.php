<div class="p-6 bg-white dark:bg-gray-800">
    <form id="student-add-form"
          wire:submit.prevent="{{ $formMode === 'edit' ? 'update' : 'create' }}">
        <!-- Basic Information Section -->
        <div class="mb-10">
            <div class="flex items-center mb-6">
                <div
                    class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl mr-4 shadow-lg">
                    <span class="text-lg font-bold text-white">1</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Basic Information</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Essential student details and contact
                        information</p>
                </div>
                <div
                    class="h-px flex-1 bg-gradient-to-r from-blue-200 to-transparent dark:from-blue-800"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- First Name Field -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-blue-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        First Name
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" wire:model="firstName"
                               class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                               placeholder="Enter student's first name">
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('firstName')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Last Name Field -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-blue-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Last Name
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" wire:model="lastName"
                               class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                               placeholder="Enter student's last name">
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('lastName')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-blue-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        Email Address
                        <span class="text-gray-500 text-xs ml-2 font-normal">(Optional)</span>
                    </label>
                    <div class="relative group">
                        <input type="email" wire:model="email"
                               class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                               placeholder="student@example.com">
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Parent Email Field -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-blue-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        Parent Email
                        <span class="text-gray-500 text-xs ml-2 font-normal">(Optional)</span>
                    </label>
                    <div class="relative group">
                        <input type="email" wire:model="parentEmail"
                               class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                               placeholder="parent@example.com">
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>
                    @error('parentEmail')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>

            <!-- Second Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">
                <!-- Username (generated) Field -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-blue-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 12h14M5 12a7 7 0 0114 0M5 12a7 7 0 0114 0"></path>
                        </svg>
                        Username
                        <span class="text-gray-500 text-xs ml-2 font-normal">(Auto-generated)</span>
                    </label>
                    <div class="relative group">
                        <input type="text" wire:model="username" readonly
                               class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 transition-all duration-200 group-hover:shadow-md"
                               placeholder="Will be generated after saving">
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Students will log in with this username.</p>
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-blue-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Password
                        @if($isEditing)
                            <span
                                class="text-gray-500 text-xs ml-2 font-normal">(leave blank to keep current)</span>
                        @else
                            <span class="text-gray-500 text-xs ml-2 font-normal">(defaults to pass1234)</span>
                        @endif
                    </label>
                    <div class="relative group">
                        <input type="password" wire:model="password"
                               class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                               placeholder="Enter secure password">
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('password')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

            </div>

            <!-- Extended Profile -->
            <div class="mt-10">
                <div class="flex items-center mb-4">
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl mr-4 shadow-lg">
                        <span class="text-lg font-bold text-white">1b</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Profile & Contact</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Manage full user and student profile details.</p>
                    </div>
                    <div
                        class="h-px flex-1 bg-gradient-to-r from-indigo-200 to-transparent dark:from-indigo-800"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Other
                            Names</label>
                        <input type="text" wire:model="otherNames"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="Middle names (optional)">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" wire:model="phone"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="+1 555 123 4567">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gender</label>
                        <select wire:model="gender"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                            <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-country-select idPrefix="student-form"
                                          name="country"
                                          codeName="country_code"
                                          wireModel="country"
                                          wireCode="countryCode"
                                          :value="$country"
                                          :codeValue="$countryCode"/>

                        <x-region-select idPrefix="student-form"
                                         name="region"
                                         wireModel="region"
                                         :value="$region"/>

                        <x-city-select idPrefix="student-form"
                                       name="city"
                                       wireModel="city"
                                       :value="$city"/>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Profile
                            Image URL</label>
                        <input type="url" wire:model="profileImageUrl"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="https://...">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Cover
                            Image URL</label>
                        <input type="url" wire:model="coverImage"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="https://...">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">User
                            Status</label>
                        <input type="text" wire:model="userStatus"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="active / suspended">
                    </div>

                    <div class="flex items-center space-x-2 pt-6">
                        <input id="isActive" type="checkbox" wire:model="isActive"
                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="isActive"
                               class="text-sm font-semibold text-gray-700 dark:text-gray-300">Account
                            Active</label>
                    </div>
                </div>
            </div>
        </div>
        <!-- Academic Information Section -->
        <div
            class="border-t border-gray-200 dark:border-gray-600 pt-10">
            <div class="flex items-center mb-8">
                <div
                    class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl mr-4 shadow-lg">
                    <span class="text-lg font-bold text-white">2</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Academic Assignment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Academic groups, levels, and subject
                        assignments</p>
                </div>
                <div
                    class="h-px flex-1 bg-gradient-to-r from-purple-200 to-transparent dark:from-purple-800"></div>
            </div>

            <!-- Academic Group & Level Selection -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Academic Group -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-purple-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Academic Group
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative group">
                        <select wire:model.live="academicGroupId"
                                wire:key="academic-group-select"
                                class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none group-hover:shadow-md">
                            <option value="">-- Select Academic Group
                                --
                            </option>
                            @foreach($academicGroups as $group)
                                <option
                                    value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('academicGroupId')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Academic Level -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-purple-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Academic Level
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative group">
                        <select wire:model.live="academicLevelId"
                                wire:key="academic-level-select-{{ $academicGroupId }}"
                                class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none group-hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(!$academicGroupId || $academicLevels->isEmpty()) disabled @endif>
                            <option value="">
                                @if(!$academicGroupId)
                                    -- Select Academic Group First --
                                @else
                                    -- Select Academic Level --
                                @endif
                            </option>
                            @foreach($academicLevels as $level)
                                <option
                                    value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('academicLevelId')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>

            <!-- Student Group Placement -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-purple-500"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Student Group
                        <span
                            class="text-gray-500 text-xs ml-2 font-normal">(Optional)</span>
                    </label>
                    <div class="relative group">
                        <select wire:model="studentGroupId"
                                class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none group-hover:shadow-md">
                            <option value="">-- Select Student Group --</option>
                            @foreach($studentGroups as $group)
                                <option
                                    value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg
                                class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors duration-200"
                                fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('studentGroupId')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>

            <!-- Teacher Assignment -->
            @if(!empty($availableTeachers) && count($availableTeachers) > 0)
                <div class="flex items-center mb-4 mt-10">
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl mr-4 shadow-lg">
                        <span class="text-lg font-bold text-white">3</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Teacher Assignment</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Choose supporting teachers and mark a primary contact.</p>
                    </div>
                    <div
                        class="h-px flex-1 bg-gradient-to-r from-blue-200 to-transparent dark:from-blue-800"></div>
                </div>

                <div
                    class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 rounded-2xl border border-blue-200 dark:border-blue-700 shadow-lg">

                    <!-- Teachers Selection -->
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Available Teachers
                        </label>
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-4 border border-blue-200 dark:border-blue-700 rounded-xl bg-white dark:bg-gray-800 shadow-inner">
                            @foreach($availableTeachers as $teacher)
                                <label
                                    class="flex items-center space-x-3 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 p-3 rounded-lg transition-all duration-200 group">
                                    <input type="checkbox"
                                           wire:model="selectedTeachers"
                                           value="{{ $teacher->id }}"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 transition-all duration-200">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-700 dark:group-hover:text-blue-300 font-medium">{{ $teacher->user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Primary Teacher Selection -->
                    <div
                        class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-blue-200 dark:border-blue-700">
                        <label
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Primary Teacher
                        </label>
                        <div class="relative group">
                            <select wire:model="primaryTeacherId"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none"
                                    @if(count($availableTeachers) === 0) disabled @endif>
                                <option value="">-- Select Primary
                                    Teacher --
                                </option>
                                @foreach($availableTeachers as $teacher)
                                    <option
                                        value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg
                                    class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                            The primary teacher will be the main
                            contact for this student.</p>
                    </div>

                    @error('selectedTeachers')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                    @error('primaryTeacherId')
                    <div
                        class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                        <svg class="w-4 h-4 mr-2 text-red-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            @endif

            <!-- Subject Assignment -->
            <div class="flex items-center mb-6 mt-10">
                <div
                    class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-emerald-500 to-green-500 rounded-xl mr-4 shadow-lg">
                    <span class="text-lg font-bold text-white">4</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Subject Assignment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Leave selections empty to keep the default: all subjects from the chosen level.</p>
                </div>
                <div
                    class="h-px flex-1 bg-gradient-to-r from-green-200 to-transparent dark:from-green-800"></div>
            </div>

            @if(!empty($levelSubjects) && count($levelSubjects) > 0)
                <div
                    class="mb-8 p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900 rounded-2xl border border-green-200 dark:border-green-700 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-5 h-5 text-white"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Subject Access</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Students automatically have access to every subject in the selected academic level. Adjust below only if you need to add or remove specific subjects.
                                </p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="$toggle('showIndividualSubjects')"
                                class="inline-flex items-center px-4 py-2 border border-green-300 dark:border-green-600 rounded-xl shadow-sm text-sm font-medium text-green-700 dark:text-green-300 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                @if($showIndividualSubjects)
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878L4.2 4.2m9.646 7.096l3.536 3.536M21 12c0 2.5-1 4.5-2.5 6.5"></path>
                                @else
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                @endif
                            </svg>
                            {{ $showIndividualSubjects ? 'Hide' : 'Show' }}
                            Individual Assignments
                        </button>
                    </div>

                    <!-- Academic Level Subjects (Always included) -->
                    <div class="mb-4">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <span
                        class="inline-flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 rounded-full text-xs font-bold mr-2">
                        {{ count($levelSubjects) }}
                    </span>
                            Academic Level Subjects
                        </h5>
                        <div
                            class="p-4 bg-green-100 dark:bg-green-800 border border-green-200 dark:border-green-700 rounded-xl">
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($levelSubjects as $subject)
                                    <div
                                        class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-200">
                                    {{ $subject->name }}
                                </span>
                                        @if(in_array($subject->id, $removedSubjects))
                                            <span
                                                class="text-xs text-red-600 dark:text-red-400 font-medium">(Removed)</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($showIndividualSubjects)
                        <div class="border-t border-green-200 dark:border-green-700 pt-4 mt-4">
                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Adjust Subject Access
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Additional subjects -->
                                <div class="p-4 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-xl">
                                    <h6 class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-2">Add Individual Subjects</h6>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        @foreach($availableAdditionalSubjects as $subject)
                                            <label class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center space-x-2">
                                                    <input type="checkbox" wire:model="additionalSubjects"
                                                           value="{{ $subject->id }}"
                                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600 rounded">
                                                    <span>{{ $subject->name }}</span>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $subject->academicLevel?->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Remove subjects -->
                                <div class="p-4 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-xl">
                                    <h6 class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-2">Remove Level Subjects</h6>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        @foreach($levelSubjects as $subject)
                                            <label class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center space-x-2">
                                                    <input type="checkbox" wire:model="removedSubjects"
                                                           value="{{ $subject->id }}"
                                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 rounded">
                                                    <span>{{ $subject->name }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $formMode === 'edit' ? 'Update existing student record.' : 'Create a new student profile.' }}
            </div>
            <div class="flex space-x-3">
                <button type="button"
                        wire:click="hideForm"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:text-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Close
                </button>
                <button type="button"
                        wire:click.prevent="{{ $formMode === 'edit' ? 'update' : 'create' }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $formMode === 'edit' ? 'update' : 'create' }}"
                        class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-70 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span wire:loading.remove wire:target="{{ $formMode === 'edit' ? 'update' : 'create' }}">
                        {{ $formMode === 'edit' ? 'Update Student' : 'Create Student' }}
                    </span>
                    <span wire:loading wire:target="{{ $formMode === 'edit' ? 'update' : 'create' }}" class="flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a10 10 0 100 20v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
