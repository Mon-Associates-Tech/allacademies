<x-layouts.app title="Edit Profile">
    <x-slot name="breadcrumb">
        <x-breadcrumb/>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your personal information and account settings</p>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('profile.show') }}"
                           class="inline-flex items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Cover Image Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Cover Image
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a cover image to personalize your profile header</p>
                </div>
                <div class="p-6">
                    <!-- Current Cover Preview -->
                    <div class="mb-6">
                        <div class="relative rounded-xl overflow-hidden h-40 sm:h-48 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 dark:from-indigo-700 dark:via-purple-700 dark:to-pink-700">
                            @if($user->cover_image)
                                <img src="{{ Storage::url($user->cover_image) }}" alt="Cover" class="w-full h-full object-cover" id="cover-preview">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center" id="cover-placeholder">
                                    <div class="text-center text-white/70">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm">No cover image</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cover Upload -->
                    <div class="space-y-4">
                        <label for="cover_image" class="relative cursor-pointer block">
                            <div id="cover-upload-zone" class="flex justify-center px-6 py-8 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors duration-200 bg-gray-50 dark:bg-gray-700/50">
                                <div class="text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="mt-3 flex justify-center text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Upload cover image</span>
                                        <span class="pl-1">or drag and drop</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 5MB (Recommended: 1200x400px)</p>
                                </div>
                            </div>
                            <input type="file" name="cover_image" id="cover_image" class="sr-only" accept="image/*">
                        </label>

                        <!-- Cover Image Upload Status -->
                        <div id="cover-upload-status" class="hidden">
                            <div class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-green-800 dark:text-green-300">Cover image selected</p>
                                    <p id="cover-file-name" class="text-xs text-green-600 dark:text-green-400 truncate"></p>
                                </div>
                                <button type="button" id="cover-clear-btn" class="ml-3 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @error('cover_image')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center">
                            <input type="checkbox" name="force_update_cover_image" id="force_update_cover_image" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <label for="force_update_cover_image" class="ml-3 text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Remove cover image</span>
                                <span class="text-gray-500 dark:text-gray-400 block">Remove current cover even if no new image is selected</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Picture Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Profile Picture
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload a photo to personalize your account</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-start gap-6">
                        <!-- Current Avatar Display -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg ring-2 ring-gray-200 dark:ring-gray-600" id="avatar-preview">
                                @else
                                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center border-4 border-white dark:border-gray-700 shadow-lg ring-2 ring-gray-200 dark:ring-gray-600" id="avatar-placeholder">
                                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-green-500 rounded-full border-2 border-white dark:border-gray-700 flex items-center justify-center shadow">
                                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar Upload Controls -->
                        <div class="flex-1 w-full space-y-4">
                            <label for="avatar" class="relative cursor-pointer block">
                                <div id="avatar-upload-zone" class="flex justify-center px-6 py-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors duration-200 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="mt-3 flex justify-center text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Upload a photo</span>
                                            <span class="pl-1">or drag and drop</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </div>
                                <x-form.file name="avatar" id="avatar" class="sr-only"/>
                            </label>

                            <!-- Avatar Upload Status -->
                            <div id="avatar-upload-status" class="hidden">
                                <div class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-300">Profile photo selected</p>
                                        <p id="avatar-file-name" class="text-xs text-green-600 dark:text-green-400 truncate"></p>
                                    </div>
                                    <button type="button" id="avatar-clear-btn" class="ml-3 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @error('avatar')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <div class="flex items-center">
                                <x-form.checkbox name="force_update_avatar" id="force_update_avatar" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700"/>
                                <label for="force_update_avatar" class="ml-3 text-sm">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Remove avatar</span>
                                    <span class="text-gray-500 dark:text-gray-400 block">Remove current avatar even if no new image is selected</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your basic profile information</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- First Name -->
                        <div>
                            <x-form.input
                                name="first_name"
                                type="text"
                                label="First Name"
                                :value="$user->first_name"
                                required
                                placeholder="Enter your first name"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('first_name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <x-form.input
                                name="last_name"
                                type="text"
                                :value="$user->last_name"
                                label="Last Name"
                                required
                                placeholder="Enter your last name"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('last_name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Other Names -->
                        <div class="sm:col-span-2">
                            <x-form.input
                                name="other_names"
                                type="text"
                                label="Other Names"
                                :value="$user->other_names"
                                placeholder="Middle name(s) - optional"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('other_names')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <x-form.input
                                name="email"
                                type="email"
                                :value="$user->email"
                                label="Email Address"
                                readonly
                                class="block w-full rounded-lg bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400 cursor-not-allowed"/>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Email address cannot be changed from this form</p>
                            @if($user->email && !$user->hasVerifiedEmail())
                                <div class="mt-3 flex items-center p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                    <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-amber-700 dark:text-amber-300">
                                        Email not verified.
                                        <a href="{{ route('verification.notice') }}" class="font-medium underline hover:no-underline ml-1">Verify now</a>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-form.input
                                name="phone"
                                type="tel"
                                label="Phone Number"
                                :value="$user->phone"
                                placeholder="Enter your phone number"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                            <select
                                name="gender"
                                id="gender"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                <option value="prefer_not_to_say" {{ old('gender', $user->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('gender')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <x-form.input
                                name="country"
                                type="text"
                                label="Country"
                                :value="$user->country"
                                placeholder="Enter your country"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('country')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Region/State -->
                        <div>
                            <x-form.input
                                name="region"
                                type="text"
                                label="Region/State"
                                :value="$user->region"
                                placeholder="Enter your region or state"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('region')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="sm:col-span-2">
                            <x-form.input
                                name="city"
                                type="text"
                                label="City"
                                :value="$user->city"
                                placeholder="Enter your city"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('city')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Level Preference -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Academic Level Preference
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select your preferred academic level for personalized content</p>
                </div>
                <div class="p-6">
                    <livewire:profile.academic-level-preference />
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Account Information
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View your account details and status</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y') }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->updated_at->format('F j, Y g:i A') }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Status</dt>
                            <dd class="mt-2">
                                @if($user->hasVerifiedEmail())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending Verification
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account ID</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white font-mono">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </button>
                            <a href="{{ route('profile.show') }}"
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('security') }}"
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Security
                            </a>
                            <a href="{{ route('password.change') }}"
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Quick Tips -->
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100 mb-3">Profile Tips</h3>
                    <div class="text-sm text-indigo-800 dark:text-indigo-200 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-indigo-500 dark:bg-indigo-400 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong class="text-indigo-900 dark:text-indigo-100">Profile Picture:</strong> Use a clear, professional photo that represents you well. Square images work best.
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-indigo-500 dark:bg-indigo-400 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong class="text-indigo-900 dark:text-indigo-100">Cover Image:</strong> Add a cover image to make your profile stand out. Recommended size is 1200x400 pixels.
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-indigo-500 dark:bg-indigo-400 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <strong class="text-indigo-900 dark:text-indigo-100">Academic Level:</strong> Set your preferred academic level to get personalized content recommendations.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Helper function to format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Preview uploaded avatar image
            const avatarInput = document.getElementById('avatar');
            const avatarStatus = document.getElementById('avatar-upload-status');
            const avatarFileName = document.getElementById('avatar-file-name');
            const avatarClearBtn = document.getElementById('avatar-clear-btn');
            const avatarUploadZone = document.getElementById('avatar-upload-zone');

            if (avatarInput) {
                avatarInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Show status indicator
                        if (avatarStatus) {
                            avatarStatus.classList.remove('hidden');
                            avatarFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                        }
                        // Update upload zone border
                        if (avatarUploadZone) {
                            avatarUploadZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                            avatarUploadZone.classList.add('border-green-400', 'dark:border-green-500');
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const preview = document.getElementById('avatar-preview');
                            const placeholder = document.getElementById('avatar-placeholder');
                            if (preview) {
                                preview.src = e.target.result;
                            } else if (placeholder) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg ring-2 ring-gray-200 dark:ring-gray-600';
                                img.id = 'avatar-preview';
                                placeholder.parentNode.replaceChild(img, placeholder);
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Clear avatar selection
            if (avatarClearBtn) {
                avatarClearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (avatarInput) {
                        avatarInput.value = '';
                    }
                    if (avatarStatus) {
                        avatarStatus.classList.add('hidden');
                    }
                    if (avatarUploadZone) {
                        avatarUploadZone.classList.add('border-gray-300', 'dark:border-gray-600');
                        avatarUploadZone.classList.remove('border-green-400', 'dark:border-green-500');
                    }
                });
            }

            // Preview uploaded cover image
            const coverInput = document.getElementById('cover_image');
            const coverStatus = document.getElementById('cover-upload-status');
            const coverFileName = document.getElementById('cover-file-name');
            const coverClearBtn = document.getElementById('cover-clear-btn');
            const coverUploadZone = document.getElementById('cover-upload-zone');

            if (coverInput) {
                coverInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Show status indicator
                        if (coverStatus) {
                            coverStatus.classList.remove('hidden');
                            coverFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                        }
                        // Update upload zone border
                        if (coverUploadZone) {
                            coverUploadZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                            coverUploadZone.classList.add('border-green-400', 'dark:border-green-500');
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const container = document.querySelector('.relative.rounded-xl.overflow-hidden.h-40');
                            if (container) {
                                // Remove placeholder if exists
                                const placeholder = container.querySelector('#cover-placeholder');
                                if (placeholder) {
                                    placeholder.remove();
                                }
                                // Update or create preview image
                                let preview = container.querySelector('#cover-preview');
                                if (preview) {
                                    preview.src = e.target.result;
                                } else {
                                    preview = document.createElement('img');
                                    preview.src = e.target.result;
                                    preview.alt = 'Cover';
                                    preview.className = 'w-full h-full object-cover';
                                    preview.id = 'cover-preview';
                                    container.appendChild(preview);
                                }
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Clear cover selection
            if (coverClearBtn) {
                coverClearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (coverInput) {
                        coverInput.value = '';
                    }
                    if (coverStatus) {
                        coverStatus.classList.add('hidden');
                    }
                    if (coverUploadZone) {
                        coverUploadZone.classList.add('border-gray-300', 'dark:border-gray-600');
                        coverUploadZone.classList.remove('border-green-400', 'dark:border-green-500');
                    }
                });
            }
        </script>
    @endpush
</x-layouts.app>
