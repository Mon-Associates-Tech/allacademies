
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">My Profile</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your personal information</p>
        </div>
        <div>
            <button wire:click="toggleEdit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white 
                           @if($isEditing) bg-red-600 hover:bg-red-700 @else bg-indigo-600 hover:bg-indigo-700 @endif 
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                @if($isEditing)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                @endif
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Avatar Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Profile Picture</h3>
                    
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            @if($currentAvatar)
                                <img src="{{ Storage::url($currentAvatar) }}" alt="Profile Picture" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center border-4 border-gray-200 dark:border-gray-600">
                                    <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        @if($isEditing)
                            <div class="mt-4 w-full">
                                <input wire:model="avatar" type="file" accept="image/*" 
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100">
                                @error('avatar') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                
                                @if($currentAvatar)
                                    <button wire:click="removeAvatar" type="button" 
                                            class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                        Remove Photo
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Student Group Info -->
            @if($studentGroup)
                <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Student Group</h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Group:</span> {{ $studentGroup->name }}
                            </p>
                            @if($studentGroup->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Description:</span> {{ $studentGroup->description }}
                                </p>
                            @endif
                            @if($studentGroup->teacher)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Teacher:</span> {{ $studentGroup->teacher->user->name }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    
                    <form wire:submit.prevent="updateProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                <input wire:model.defer="name" type="text" id="name" 
                                       @if(!$isEditing) disabled @endif
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                                @error('name') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <input wire:model.defer="email" type="email" id="email" 
                                       @if(!$isEditing) disabled @endif
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                                @error('email') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                <input wire:model.defer="phone" type="tel" id="phone" 
                                       @if(!$isEditing) disabled @endif
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                                @error('phone') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                                <input wire:model.defer="date_of_birth" type="date" id="date_of_birth" 
                                       @if(!$isEditing) disabled @endif
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                                @error('date_of_birth') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <textarea wire:model.defer="address" id="address" rows="3" 
                                          @if(!$isEditing) disabled @endif
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif"></textarea>
                                @error('address') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Emergency Contact Name -->
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emergency Contact Name</label>
                                <input wire:model.defer="emergency_contact_name" type="text" id="emergency_contact_name" 
                                       @if(!$isEditing) disabled @endif
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                                @error('emergency_contact_name') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Emergency Contact Phone -->
                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emergency Contact Phone</label>
                                <input wire:model.defer="emergency_contact_phone" type="tel" id="emergency_contact_phone" 
                                       @if(!$isEditing) disabled @endif
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                                @error('emergency_contact_phone') 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if($isEditing)
                            <div class="mt-6 flex justify-end space-x-3">
                                <button wire:click="toggleEdit" type="button" 
                                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save Changes
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>