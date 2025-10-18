<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">School Configuration</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage your school's information, branding, academic structure, and grading system</p>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        {{-- Sidebar Navigation --}}
        <div class="lg:col-span-1">
            <nav class="space-y-1 rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                <button wire:click="setActiveSection('basic-info')"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium transition {{ $activeSection === 'basic-info' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Basic Information
                </button>

                <button wire:click="setActiveSection('branding')"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium transition {{ $activeSection === 'branding' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Branding
                </button>

                <button wire:click="setActiveSection('academic-years')"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium transition {{ $activeSection === 'academic-years' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Academic Years
                </button>

                <button wire:click="setActiveSection('academic-periods')"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium transition {{ $activeSection === 'academic-periods' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Academic Periods
                </button>

                <button wire:click="setActiveSection('grade-scales')"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium transition {{ $activeSection === 'grade-scales' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Grade Scales
                </button>
            </nav>
        </div>

        {{-- Main Content Area --}}
        <div class="lg:col-span-3">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                {{-- Basic Information Section --}}
                @if($activeSection === 'basic-info')
                    <div>
                        <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Basic Information</h2>

                        <form wire:submit.prevent="saveBasicInfo">
                            {{-- Logo Upload --}}
                            <div class="mb-6">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">School Logo</label>
                                <div class="flex items-center space-x-4">
                                    @if($logo)
                                        <img src="{{ Storage::url($logo) }}" alt="School Logo" class="h-20 w-20 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-20 w-20 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input type="file" wire:model="newLogo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/20 dark:file:text-indigo-400">
                                        @error('newLogo') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- School Name & Code --}}
                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">School Name *</label>
                                    <input type="text" id="name" wire:model="name" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('name') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="code" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">School Code</label>
                                    <input type="text" id="code" wire:model="code" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            {{-- Contact Information --}}
                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div>
                                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" id="email" wire:model="email" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('email') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="phone" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input type="text" id="phone" wire:model="phone" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('phone') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="website" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                                    <input type="url" id="website" wire:model="website" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('website') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Address Information --}}
                            <div class="mb-6">
                                <label for="address" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <input type="text" id="address" wire:model="address" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-4">
                                <div>
                                    <label for="city" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                                    <input type="text" id="city" wire:model="city" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="state" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">State/Province</label>
                                    <input type="text" id="state" wire:model="state" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="country" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                                    <input type="text" id="country" wire:model="country" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="postal_code" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Postal Code</label>
                                    <input type="text" id="postal_code" wire:model="postal_code" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            {{-- School Details --}}
                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="type" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">School Type</label>
                                    <select id="type" wire:model="type" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Type</option>
                                        @foreach($schoolTypes as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="student_capacity" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Student Capacity</label>
                                    <input type="number" id="student_capacity" wire:model="student_capacity" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="timezone" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
                                    <select id="timezone" wire:model="timezone" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @foreach($timezones as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="currency" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                                    <select id="currency" wire:model="currency" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @foreach($currencies as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="description" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea id="description" wire:model="description" rows="4" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Branding Section --}}
                @if($activeSection === 'branding')
                    <div>
                        <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Branding & Reports</h2>

                        <form wire:submit.prevent="saveBrandingSettings">
                            {{-- Letterhead Upload --}}
                            <div class="mb-6">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Official Letterhead</label>
                                <div class="flex items-start space-x-4">
                                    @if($letterhead)
                                        <img src="{{ Storage::url($letterhead) }}" alt="Letterhead" class="h-32 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
                                    @else
                                        <div class="flex h-32 w-48 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">No letterhead</span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input type="file" wire:model="newLetterhead" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/20 dark:file:text-indigo-400">
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Upload your school's official letterhead for use in documents and reports</p>
                                        @error('newLetterhead') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- School Motto --}}
                            <div class="mb-6">
                                <label for="school_motto" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">School Motto</label>
                                <input type="text" id="school_motto" wire:model="school_motto" placeholder="e.g., Excellence in Education" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            {{-- School Colors --}}
                            <div class="mb-6">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">School Colors</label>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="primary_color" class="mb-1 block text-xs text-gray-600 dark:text-gray-400">Primary Color</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" id="primary_color" wire:model="school_colors.primary" class="h-10 w-20 cursor-pointer rounded border-gray-300 dark:border-gray-600">
                                            <input type="text" wire:model="school_colors.primary" class="block flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="secondary_color" class="mb-1 block text-xs text-gray-600 dark:text-gray-400">Secondary Color</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" id="secondary_color" wire:model="school_colors.secondary" class="h-10 w-20 cursor-pointer rounded border-gray-300 dark:border-gray-600">
                                            <input type="text" wire:model="school_colors.secondary" class="block flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Report Header --}}
                            <div class="mb-6">
                                <label for="report_header" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Report Header Text</label>
                                <textarea id="report_header" wire:model="report_header" rows="3" placeholder="Text to appear at the top of reports" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>

                            {{-- Report Footer --}}
                            <div class="mb-6">
                                <label for="report_footer" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Report Footer Text</label>
                                <textarea id="report_footer" wire:model="report_footer" rows="3" placeholder="Text to appear at the bottom of reports" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                    Save Branding Settings
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Academic Years Section --}}
                @if($activeSection === 'academic-years')
                    <div>
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Academic Years</h2>
                            <button wire:click="createAcademicYear" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                <span class="flex items-center">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Academic Year
                                </span>
                            </button>
                        </div>

                        @if($currentAcademicYear)
                            <div class="mb-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="ml-3 text-sm font-medium text-blue-800 dark:text-blue-300">
                                        Current Academic Year: <strong>{{ $currentAcademicYear->name }}</strong>
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            @forelse($academicYears as $year)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700 {{ $year->is_current ? 'border-indigo-300 bg-indigo-50/50 dark:border-indigo-700 dark:bg-indigo-900/10' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $year->name }}</h3>
                                                @if($year->is_current)
                                                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">Current</span>
                                                @endif
                                                <span class="rounded-full px-3 py-1 text-xs font-medium
                                                    {{ $year->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                                    {{ $year->status === 'upcoming' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                                    {{ $year->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                                    {{ ucfirst($year->status ?? 'active') }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($year->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($year->end_date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if(!$year->is_current)
                                                <button wire:click="setCurrentAcademicYear({{ $year->id }})"
                                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                                    Set as Current
                                                </button>
                                            @endif
                                            <button wire:click="editAcademicYear({{ $year->id }})"
                                                    class="rounded-lg bg-gray-100 p-2 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteAcademicYear({{ $year->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this academic year?')"
                                                    class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No academic years</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new academic year.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                {{-- Academic Periods Section --}}
                @if($activeSection === 'academic-periods')
                    <div>
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Academic Periods</h2>
                            <button wire:click="createPeriod" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                <span class="flex items-center">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Period
                                </span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            @forelse($academicPeriods as $period)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $period->title }}</h3>
                                                <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                    {{ ucfirst($period->type) }} {{ $period->sequence }}
                                                </span>
                                                <span class="rounded-full px-3 py-1 text-xs font-medium
                                                    {{ $period->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                                    {{ $period->status === 'upcoming' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                                    {{ $period->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                                    {{ ucfirst($period->status) }}
                                                </span>
                                            </div>
                                            @if($period->academicYear)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $period->academicYear->name }}</p>
                                            @endif
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($period->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('M d, Y') }}
                                            </p>
                                            @if($period->description)
                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $period->description }}</p>
                                            @endif
                                            @if($period->registration_start || $period->exam_start)
                                                <div class="mt-3 grid grid-cols-2 gap-4 text-xs">
                                                    @if($period->registration_start)
                                                        <div>
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">Registration:</span>
                                                            <span class="text-gray-600 dark:text-gray-400">
                                                                {{ \Carbon\Carbon::parse($period->registration_start)->format('M d') }} - {{ \Carbon\Carbon::parse($period->registration_end)->format('M d, Y') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if($period->exam_start)
                                                        <div>
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">Exams:</span>
                                                            <span class="text-gray-600 dark:text-gray-400">
                                                                {{ \Carbon\Carbon::parse($period->exam_start)->format('M d') }} - {{ \Carbon\Carbon::parse($period->exam_end)->format('M d, Y') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="editPeriod({{ $period->id }})"
                                                    class="rounded-lg bg-gray-100 p-2 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deletePeriod({{ $period->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this academic period?')"
                                                    class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No academic periods</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new academic period.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                {{-- Grade Scales Section --}}
                @if($activeSection === 'grade-scales')
                    <div>
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Grade Scales</h2>
                            <button wire:click="createGradeScale" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                <span class="flex items-center">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Grade
                                </span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($gradeScales as $grade)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg text-lg font-bold text-white" style="background-color: {{ $grade->color }}">
                                                {{ $grade->grade }}
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $grade->grade }}</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $grade->min_score }}% - {{ $grade->max_score }}%
                                                    @if($grade->grade_point)
                                                        <span class="ml-2 text-indigo-600 dark:text-indigo-400">({{ $grade->grade_point }} GPA)</span>
                                                    @endif
                                                </p>
                                                @if($grade->description)
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $grade->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="editGradeScale({{ $grade->id }})"
                                                    class="rounded-lg bg-gray-100 p-2 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteGradeScale({{ $grade->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this grade scale?')"
                                                    class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No grade scales</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new grade scale.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Academic Year Modal --}}
    @if($showAcademicYearModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="showAcademicYearModal = false"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <form wire:submit.prevent="saveAcademicYear">
                        <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                            <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                {{ $editingYearId ? 'Edit Academic Year' : 'Add Academic Year' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="yearName" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Year Name *</label>
                                    <input type="text" id="yearName" wire:model="yearName" placeholder="e.g., 2024-2025" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('yearName') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="yearStartDate" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date *</label>
                                        <input type="date" id="yearStartDate" wire:model="yearStartDate" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @error('yearStartDate') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="yearEndDate" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">End Date *</label>
                                        <input type="date" id="yearEndDate" wire:model="yearEndDate" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @error('yearEndDate') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="yearStatus" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select id="yearStatus" wire:model="yearStatus" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="upcoming">Upcoming</option>
                                        <option value="active">Active</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingYearId ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" wire:click="showAcademicYearModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Academic Period Modal --}}
    @if($showPeriodModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="showPeriodModal = false"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
                    <form wire:submit.prevent="savePeriod">
                        <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                            <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                {{ $editingPeriodId ? 'Edit Academic Period' : 'Add Academic Period' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="periodTitle" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Period Title *</label>
                                    <input type="text" id="periodTitle" wire:model="periodTitle" placeholder="e.g., Fall Semester 2024" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('periodTitle') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="periodType" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type *</label>
                                        <select id="periodType" wire:model="periodType" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <option value="semester">Semester</option>
                                            <option value="term">Term</option>
                                            <option value="quarter">Quarter</option>
                                            <option value="trimester">Trimester</option>
                                            <option value="session">Session</option>
                                        </select>
                                        @error('periodType') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="periodSequence" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sequence</label>
                                        <input type="number" id="periodSequence" wire:model="periodSequence" min="1" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>

                                    <div>
                                        <label for="periodStatus" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select id="periodStatus" wire:model="periodStatus" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <option value="upcoming">Upcoming</option>
                                            <option value="active">Active</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="periodAcademicYearId" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Academic Year</label>
                                    <select id="periodAcademicYearId" wire:model="periodAcademicYearId" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="periodStartDate" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date *</label>
                                        <input type="date" id="periodStartDate" wire:model="periodStartDate" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @error('periodStartDate') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="periodEndDate" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">End Date *</label>
                                        <input type="date" id="periodEndDate" wire:model="periodEndDate" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @error('periodEndDate') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="registrationStart" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Start</label>
                                        <input type="date" id="registrationStart" wire:model="registrationStart" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>

                                    <div>
                                        <label for="registrationEnd" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Registration End</label>
                                        <input type="date" id="registrationEnd" wire:model="registrationEnd" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="examStart" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Exam Start</label>
                                        <input type="date" id="examStart" wire:model="examStart" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>

                                    <div>
                                        <label for="examEnd" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Exam End</label>
                                        <input type="date" id="examEnd" wire:model="examEnd" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>

                                <div>
                                    <label for="periodDescription" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea id="periodDescription" wire:model="periodDescription" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingPeriodId ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" wire:click="showPeriodModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Grade Scale Modal --}}
    @if($showGradeScaleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="showGradeScaleModal = false"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <form wire:submit.prevent="saveGradeScale">
                        <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                            <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                {{ $editingGradeScaleId ? 'Edit Grade Scale' : 'Add Grade Scale' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="gradeName" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Grade *</label>
                                    <input type="text" id="gradeName" wire:model="gradeName" placeholder="e.g., A, B, C" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('gradeName') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="gradeMinScore" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Score (%) *</label>
                                        <input type="number" id="gradeMinScore" wire:model="gradeMinScore" min="0" max="100" step="0.01" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @error('gradeMinScore') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="gradeMaxScore" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum Score (%) *</label>
                                        <input type="number" id="gradeMaxScore" wire:model="gradeMaxScore" min="0" max="100" step="0.01" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        @error('gradeMaxScore') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="gradePoint" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Grade Point (GPA)</label>
                                    <input type="number" id="gradePoint" wire:model="gradePoint" min="0" max="5" step="0.01" placeholder="e.g., 4.0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @error('gradePoint') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="gradeColor" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Display Color</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" id="gradeColor" wire:model="gradeColor" class="h-10 w-20 cursor-pointer rounded border-gray-300 dark:border-gray-600">
                                        <input type="text" wire:model="gradeColor" class="block flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>

                                <div>
                                    <label for="gradeDescription" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea id="gradeDescription" wire:model="gradeDescription" rows="3" placeholder="e.g., Excellent, Outstanding" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingGradeScaleId ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" wire:click="showGradeScaleModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
