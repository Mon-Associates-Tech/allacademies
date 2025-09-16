<x-layouts.app title="New Academic Level" :has-action="false">
                    <x-slot name="breadcrumb">
                        <x-breadcrumb :paths="[
                            'Academic Groups' => route('academic-groups.index'),
                            $academicGroup->name => route('academic-groups.show', ['academic_group' => $academicGroup]),
                            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicGroup]),
                            'New Level' => null,
                        ]" />
                    </x-slot>

                    <div class="max-w-2xl mx-auto space-y-6">
                        <!-- Header Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">New Academic Level</h1>
                                    <p class="text-gray-600">Create a new level in {{ $academicGroup->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Context Information -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        This level will be created in <strong>{{ $academicGroup->name }}</strong>.
                                        You can add subjects to it after creation.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Create Form -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Level Information</h2>
                                <p class="text-sm text-gray-600 mt-1">Enter the basic information for the new academic level.</p>
                            </div>

                            <form method="POST" action="{{ route('academic-levels.store', ['academic_group' => $academicGroup]) }}" class="p-6 space-y-6">
                                @csrf

                                <!-- Name Field -->
                                <div class="space-y-2">
                                    <x-form.input
                                        name="name"
                                        type="text"
                                        placeholder="e.g., Form 1, Grade 10, Year 7"
                                        class="w-full"
                                        required
                                    />
                                    <p class="text-xs text-gray-500">The internal name used to identify this academic level.</p>
                                    @error('name')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Label Field -->
                                <div class="space-y-2">
                                    <x-form.input
                                        name="label"
                                        type="text"
                                        placeholder="e.g., Form One, Tenth Grade, Year Seven"
                                        class="w-full"
                                        required
                                    />
                                    <p class="text-xs text-gray-500">The user-friendly name displayed to students and teachers.</p>
                                    @error('label')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Academic Group (Read-only display) -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Academic Group</label>
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                                        </svg>
                                        <span class="text-gray-900 font-medium">{{ $academicGroup->name }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                    <div class="flex items-center space-x-4">
                                        <x-link.secondary :to="route('academic-levels.index', ['academic_group' => $academicGroup])">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                            </svg>
                                            Cancel
                                        </x-link.secondary>
                                    </div>

                                    <x-button.primary type="submit" class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create Academic Level
                                    </x-button.primary>
                                </div>
                            </form>
                        </div>
                    </div>
                </x-layouts.app>
