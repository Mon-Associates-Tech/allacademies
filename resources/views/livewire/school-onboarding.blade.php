{{-- Enhanced School Onboarding Form - Complete Implementation --}}
<div
    x-data="schoolOnboardingForm()"
    x-init="init()"
    class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100"
>
    {{-- Progress Bar --}}
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-lg font-semibold text-gray-900">School Registration</h1>
                <span class="text-sm text-gray-500">Step <span x-text="currentStep"></span> of {{ $totalSteps }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500 ease-out"
                    :style="`width: ${(currentStep / {{ $totalSteps }}) * 100}%`"
                ></div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            {{-- Step 1: School Details --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="px-8 py-6">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-school text-blue-600 text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">School Information</h2>
                        <p class="text-gray-600">Let's start with the basic details about your school</p>
                    </div>

                    {{-- Basic Information --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="lg:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    School Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="name"
                                        wire:model.defer="name"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="Enter your school name"
                                        x-ref="firstInput"
                                    >
                                    <i class="fas fa-school absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    School Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        wire:model.defer="email"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="school@example.com"
                                    >
                                    <i class="fas fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <div class="relative">
                                    <input
                                        type="tel"
                                        id="phone"
                                        wire:model.defer="phone"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('phone') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="+233 XX XXX XXXX"
                                        x-on:input="formatPhoneNumber($event)"
                                    >
                                    <i class="fas fa-phone absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                @error('phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    School Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="type"
                                    wire:model.defer="type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('type') border-red-500 ring-2 ring-red-200 @enderror"
                                >
                                    <option value="">Select school type</option>
                                    @foreach($this->schoolTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="ownership" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ownership Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="ownership"
                                    wire:model.defer="ownership"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('ownership') border-red-500 ring-2 ring-red-200 @enderror"
                                >
                                    <option value="">Select ownership type</option>
                                    @foreach($this->ownershipTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('ownership')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                    Website
                                </label>
                                <div class="relative">
                                    <input
                                        type="url"
                                        id="website"
                                        wire:model.defer="website"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('website') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="https://www.yourschool.edu.gh"
                                    >
                                    <i class="fas fa-globe absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                @error('website')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="established_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Established Date
                                </label>
                                <div class="relative">
                                    <input
                                        type="date"
                                        id="established_date"
                                        wire:model.defer="established_date"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('established_date') border-red-500 ring-2 ring-red-200 @enderror"
                                    >
                                    <i class="fas fa-calendar absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                @error('established_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="student_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Student Capacity
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        id="student_capacity"
                                        wire:model.defer="student_capacity"
                                        min="1"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('student_capacity') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="e.g., 500"
                                    >
                                    <i class="fas fa-users absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                @error('student_capacity')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="lg:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    School Description
                                </label>
                                <textarea
                                    id="description"
                                    wire:model.defer="description"
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none @error('description') border-red-500 ring-2 ring-red-200 @enderror"
                                    placeholder="Brief description of your school, its mission, and values..."
                                ></textarea>
                                @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Logo Upload Section --}}
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    School Logo
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                            <template x-if="logoPreview">
                                                <img :src="logoPreview" class="w-full h-full object-cover rounded-lg">
                                            </template>
                                            <template x-if="!logoPreview">
                                                <i class="fas fa-camera text-gray-400 text-xl"></i>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                            <input
                                                type="file"
                                                id="logo"
                                                wire:model="logo"
                                                accept="image/*"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                x-on:change="handleFileUpload($event)"
                                            >
                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors duration-200">
                                                <i class="fas fa-cloud-upload-alt text-gray-400 text-xl mb-2"></i>
                                                <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                            </div>
                                        </div>
                                        @error('logo')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                        <div wire:loading wire:target="logo" class="mt-2 text-sm text-blue-600 flex items-center">
                                            <i class="fas fa-spinner fa-spin mr-1"></i>
                                            Uploading logo...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Address Section --}}
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                Address Information
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Street Address <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="address"
                                        wire:model.defer="address"
                                        rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none @error('address') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="Enter complete street address"
                                    ></textarea>
                                    @error('address')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="city"
                                        wire:model.defer="city"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('city') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="e.g., Accra"
                                    >
                                    @error('city')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                        Region/State <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="state"
                                        wire:model.defer="state"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('state') border-red-500 ring-2 ring-red-200 @enderror"
                                    >
                                        <option value="">Select region</option>
                                        @foreach($this->ghanaRegions as $region)
                                            <option value="{{ $region }}">{{ $region }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="country"
                                        wire:model.defer="country"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 @error('country') border-red-500 ring-2 ring-red-200 @enderror"
                                        readonly
                                    >
                                    @error('country')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Postal Code
                                    </label>
                                    <input
                                        type="text"
                                        id="postal_code"
                                        wire:model.defer="postal_code"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('postal_code') border-red-500 ring-2 ring-red-200 @enderror"
                                        placeholder="e.g., GA-123-4567"
                                    >
                                    @error('postal_code')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Academic Structure --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="px-8 py-6">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-green-600 text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Academic Structure</h2>
                        <p class="text-gray-600">Configure your school's academic organization (optional)</p>
                    </div>

                    <div class="space-y-8">
                        {{-- Academic Groups Selection --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Groups</h3>
                            <p class="text-sm text-gray-600 mb-4">Select the academic divisions your school offers</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($this->availableAcademicGroups as $group)
                                    <label class="group relative flex items-center p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-300 cursor-pointer transition-all duration-200 hover:shadow-md">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedAcademicGroups"
                                            value="{{ $group->id }}"
                                            class="sr-only"
                                        >
                                        <div class="flex items-center w-full">
                                            <div class="flex-shrink-0 w-5 h-5 mr-3 border-2 border-gray-300 rounded group-hover:border-blue-500 transition-colors duration-200 flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors duration-200">{{ $group->name }}</span>
                                                @if($group->description)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $group->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="absolute inset-0 bg-blue-50 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-200"></div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Academic Levels Selection --}}
                        <div x-show="$wire.selectedAcademicGroups.length > 0" x-transition>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Levels</h3>
                            <p class="text-sm text-gray-600 mb-4">Select specific levels within your chosen academic groups</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($this->availableAcademicLevels as $level)
                                    <label class="group relative flex items-center p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-green-300 cursor-pointer transition-all duration-200 hover:shadow-md">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedAcademicLevels"
                                            value="{{ $level->id }}"
                                            class="sr-only"
                                        >
                                        <div class="flex items-center w-full">
                                            <div class="flex-shrink-0 w-5 h-5 mr-3 border-2 border-gray-300 rounded group-hover:border-green-500 transition-colors duration-200 flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors duration-200">{{ $level->name }}</span>
                                                <p class="text-xs text-gray-500 mt-1">{{ $level->academicGroup->name }}</p>
                                            </div>
                                        </div>
                                        <div class="absolute inset-0 bg-green-50 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-200"></div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Settings Section --}}
                        <div class="border-t pt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-cog text-blue-600 mr-2"></i>
                                School Settings
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Timezone
                                    </label>
                                    <select
                                        id="timezone"
                                        wire:model.defer="timezone"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    >
                                        @foreach($this->timezones as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                        Currency
                                    </label>
                                    <select
                                        id="currency"
                                        wire:model.defer="currency"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    >
                                        @foreach($this->currencies as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="academic_year_start" class="block text-sm font-medium text-gray-700 mb-2">
                                        Academic Year Start
                                    </label>
                                    <input
                                        type="date"
                                        id="academic_year_start"
                                        wire:model.defer="academic_year_start"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('academic_year_start') border-red-500 ring-2 ring-red-200 @enderror"
                                    >
                                    @error('academic_year_start')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="academic_year_end" class="block text-sm font-medium text-gray-700 mb-2">
                                        Academic Year End
                                    </label>
                                    <input
                                        type="date"
                                        id="academic_year_end"
                                        wire:model.defer="academic_year_end"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('academic_year_end') border-red-500 ring-2 ring-red-200 @enderror"
                                    >
                                    @error('academic_year_end')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Review & Confirmation --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="px-8 py-6">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-eye text-purple-600 text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Review Your Information</h2>
                        <p class="text-gray-600">Please review all details before completing setup</p>
                    </div>

                    {{-- Enhanced School Summary Card --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 border border-blue-200">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 mr-4">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" alt="School Logo" class="w-16 h-16 rounded-xl object-cover shadow-md">
                                </template>
                                <template x-if="!logoPreview">
                                    <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-school text-white text-2xl"></i>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" x-text="$wire.name"></h3>
                                <p class="text-blue-600 font-medium" x-text="$wire.email"></p>
                                <p class="text-gray-600 text-sm" x-text="$wire.phone" x-show="$wire.phone"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-building text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Type</span>
                                        <p class="text-sm font-semibold text-gray-900 capitalize" x-text="$wire.type"></p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-handshake text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ownership</span>
                                        <p class="text-sm font-semibold text-gray-900 capitalize" x-text="$wire.ownership"></p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm" x-show="$wire.student_capacity">
                                    <i class="fas fa-users text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Capacity</span>
                                        <p class="text-sm font-semibold text-gray-900" x-text="$wire.student_capacity ? $wire.student_capacity.toLocaleString() + ' students' : ''"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-map-marker-alt text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</span>
                                        <p class="text-sm font-semibold text-gray-900" x-text="`${$wire.city}, ${$wire.state}`"></p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm" x-show="$wire.website">
                                    <i class="fas fa-globe text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Website</span>
                                        <p class="text-sm font-semibold text-blue-600 truncate" x-text="$wire.website"></p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm" x-show="$wire.established_date">
                                    <i class="fas fa-calendar text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Established</span>
                                        <p class="text-sm font-semibold text-gray-900" x-text="$wire.established_date ? new Date($wire.established_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long'}) : ''"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-white rounded-lg shadow-sm" x-show="$wire.description">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Description</span>
                            <p class="mt-1 text-sm text-gray-700" x-text="$wire.description"></p>
                        </div>

                        {{-- Academic Structure Summary --}}
                        <div class="mt-6 space-y-4" x-show="$wire.selectedAcademicGroups.length > 0">
                            <div class="p-4 bg-white rounded-lg shadow-sm">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3 block">Academic Groups</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($this->availableAcademicGroups as $group)
                                        <span
                                            x-show="$wire.selectedAcademicGroups.includes({{ $group->id }})"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200"
                                        >
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            {{ $group->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-lg shadow-sm" x-show="$wire.selectedAcademicLevels.length > 0">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3 block">Academic Levels</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($this->availableAcademicLevels as $level)
                                        <span
                                            x-show="$wire.selectedAcademicLevels.includes({{ $level->id }})"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200"
                                        >
                                            {{ $level->name }}
                                            <span class="ml-1 text-green-600 opacity-75">({{ $level->academicGroup->name }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Settings Summary --}}
                        <div class="mt-6 p-4 bg-white rounded-lg shadow-sm">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3 block">Settings</span>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                    <span class="text-gray-700" x-text="$wire.timezone"></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-money-bill text-gray-400 mr-2"></i>
                                    <span class="text-gray-700" x-text="$wire.currency"></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                    <span class="text-gray-700" x-text="formatAcademicYear()"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Enhanced Confirmation --}}
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                        <label class="flex items-start cursor-pointer group">
                            <div class="flex items-center h-5">
                                <input
                                    type="checkbox"
                                    x-model="confirmed"
                                    class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition-all duration-200"
                                >
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-blue-900 group-hover:text-blue-700 transition-colors duration-200">
                                    I confirm that all information provided is accurate and complete
                                </p>
                                <p class="mt-1 text-sm text-blue-700">
                                    By proceeding, I agree to create this school profile and become its administrator.
                                    I understand that some information can be modified later in the school settings.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Step 4: Success --}}
            <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="px-8 py-12 text-center">
                    <div class="mb-8">
                        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">School Setup Complete!</h2>
                        <p class="text-lg text-gray-600 mb-2" x-text="`${$wire.name} has been successfully registered`"></p>
                        @if($createdSchool)
                            <p class="text-sm text-gray-500">School Code: <span class="font-mono font-semibold">{{ $createdSchool->code }}</span></p>
                        @endif
                    </div>

                    @if($createdSchool)
                        {{-- Success Summary --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-8 text-left max-w-2xl mx-auto shadow-lg">
                            <div class="flex items-center justify-center mb-6">
                                @if($createdSchool->logo_url)
                                    <img src="{{ Storage::url($createdSchool->logo_url) }}" alt="{{ $createdSchool->name }} Logo"
                                         class="w-16 h-16 rounded-xl object-cover shadow-md mr-4">
                                @else
                                    <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center shadow-md mr-4">
                                        <i class="fas fa-school text-white text-2xl"></i>
                                    </div>
                                @endif
                                <div class="text-center">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $createdSchool->name }}</h3>
                                    <p class="text-sm text-gray-500">Successfully Registered</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <i class="fas fa-graduation-cap text-blue-600 text-xl mb-2"></i>
                                    <p class="text-sm font-medium text-gray-700">Academic Groups</p>
                                    <p class="text-lg font-bold text-blue-600">{{ $createdSchool->academicGroups()->count() }}</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4">
                                    <i class="fas fa-layer-group text-green-600 text-xl mb-2"></i>
                                    <p class="text-sm font-medium text-gray-700">Academic Levels</p>
                                    <p class="text-lg font-bold text-green-600">{{ $createdSchool->academicLevels()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Next Steps Guide --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 mb-8 text-left max-w-3xl mx-auto border border-indigo-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 text-center flex items-center justify-center">
                            <i class="fas fa-rocket text-indigo-600 mr-2"></i>
                            What's Next?
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-users text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Add Staff & Students</h4>
                                        <p class="text-sm text-gray-600">Start adding teachers, librarians, and students to your school</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-book text-green-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Library Management</h4>
                                        <p class="text-sm text-gray-600">Add books and configure lending policies</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Academic Structure</h4>
                                        <p class="text-sm text-gray-600">Fine-tune classes and academic organization</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-cog text-orange-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Configure Settings</h4>
                                        <p class="text-sm text-gray-600">Customize preferences and system configurations</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button
                            type="button"
                            wire:click="completeOnboarding"
                            class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform hover:scale-105 transition-all duration-200 shadow-lg"
                        >
                            <i class="fas fa-rocket mr-2"></i>
                            Go to Dashboard
                        </button>
                        <button
                            type="button"
                            onclick="window.location.href='/admin/school/settings'"
                            class="px-8 py-4 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 transform hover:scale-105 transition-all duration-200 shadow-lg"
                        >
                            <i class="fas fa-cog mr-2"></i>
                            Configure Settings
                        </button>
                    </div>
                </div>
            </div>

            {{-- Navigation Footer --}}
            <div x-show="currentStep < 4" class="px-8 py-6 bg-gray-50 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <template x-if="currentStep === 1">
                            <span>All fields marked with <span class="text-red-500 font-medium">*</span> are required</span>
                        </template>
                        <template x-if="currentStep === 2">
                            <span>Academic structure can be configured later</span>
                        </template>
                        <template x-if="currentStep === 3">
                            <span>Please review and confirm your information</span>
                        </template>
                    </div>

                    <div class="flex space-x-3">
                        {{-- Back Button --}}
                        <template x-if="currentStep > 1 && currentStep < 4">
                            <button
                                type="button"
                                @click="previousStep()"
                                class="px-6 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back
                            </button>
                        </template>

                        {{-- Cancel Button --}}
                        <template x-if="currentStep === 1">
                            <a
                                href="{{ route('dashboard') }}"
                                class="px-6 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200"
                            >
                                Cancel
                            </a>
                        </template>

                        {{-- Next/Continue Buttons --}}
                        <template x-if="currentStep === 1">
                            <button
                                type="button"
                                @click="nextStep()"
                                :disabled="!canProceedStep1()"
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                            >
                                Continue Setup
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </template>

                        <template x-if="currentStep === 2">
                            <button
                                type="button"
                                @click="nextStep()"
                                class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-medium hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
                            >
                                Review & Confirm
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </template>

                        {{-- Complete Setup Button --}}
                        <template x-if="currentStep === 3">
                            <button
                                type="button"
                                wire:click="createSchool"
                                :disabled="loading || !confirmed"
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-4 focus:ring-purple-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200 shadow-lg"
                            >
                                <template x-if="!loading">
                                    <span class="flex items-center">
                                        <i class="fas fa-check mr-2"></i>
                                        Complete Setup
                                    </span>
                                </template>
                                <template x-if="loading">
                                    <span class="flex items-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Creating School...
                                    </span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Tips Sidebar --}}
        <div x-show="currentStep === 1" x-transition class="mt-8 bg-white rounded-xl shadow-lg p-6 border border-blue-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-lightbulb text-yellow-600"></i>
                </div>
                Quick Setup Tips
            </h3>
            <div class="space-y-4">
                <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                    <i class="fas fa-check-circle text-blue-600 mt-1 mr-3 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm"><strong class="text-blue-900">School Name:</strong> Use your official school name as it appears on documents</p>
                    </div>
                </div>
                <div class="flex items-start p-3 bg-green-50 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 mt-1 mr-3 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm"><strong class="text-green-900">Email:</strong> Use an official school email address for communications</p>
                    </div>
                </div>
                <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                    <i class="fas fa-check-circle text-purple-600 mt-1 mr-3 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm"><strong class="text-purple-900">Flexibility:</strong> Most settings can be modified later from your dashboard</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div
        x-show="loading"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        style="display: none;"
    >
        <div class="bg-white rounded-xl p-8 text-center max-w-sm mx-4 shadow-2xl">
            <div class="animate-spin w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-gray-700 font-semibold text-lg">Creating your school...</p>
            <p class="text-gray-500 text-sm mt-2">Please wait while we set everything up</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button @click="show = false" class="ml-4 hover:text-green-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 8000000)"
            class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-4 hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Alpine.js Data and Methods --}}
    <script>
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
                    });

                    // Listen for Livewire events
                    Livewire.on('validationError', () => {
                        this.scrollToFirstError();
                    });

                    Livewire.on('schoolCreated', () => {
                        this.currentStep = 4;
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
                    } else if (this.currentStep < 3) {
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
                        this.$wire.state;
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
                        return `${start.toLocaleDateString('en-US', {month: 'short', year: 'numeric'})} - ${end.toLocaleDateString('en-US', {month: 'short', year: 'numeric'})}`;
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
                }
            }
        }

        // Global Livewire hooks
        document.addEventListener('livewire:load', function () {
            // Handle step changes
            Livewire.hook('message.processed', (message, component) => {
                if (message.updateQueue && message.updateQueue.some(update =>
                    update.payload.method === 'nextStep' ||
                    update.payload.method === 'previousStep' ||
                    update.payload.method === 'createSchool'
                )) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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
        });

        // Prevent form submission on Enter key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'textarea' && e.target.type !== 'submit') {
                e.preventDefault();
                return false;
            }
        });
    </script>

    {{-- Enhanced Styles --}}
    <style>
        /* Custom animations */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        /* Enhanced form styling */
        input:focus, select:focus, textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        /* Checkbox styling improvements */
        input[type="checkbox"]:checked {
            background-color: #3B82F6;
            border-color: #3B82F6;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
        }

        /* Custom checkbox for academic groups/levels */
        .group input[type="checkbox"]:checked + div .w-5.h-5 {
            background-color: #3B82F6;
            border-color: #3B82F6;
        }

        .group input[type="checkbox"]:checked + div .w-5.h-5 i {
            opacity: 1 !important;
        }

        /* Loading animation improvements */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
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
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        /* File input styling */
        input[type="file"] {
            font-size: 0;
        }

        input[type="file"]::-webkit-file-upload-button {
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
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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
</div>
