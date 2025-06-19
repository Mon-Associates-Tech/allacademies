<x-layouts.app title="New Academic Group" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-white mb-1">Create Academic Group</h2>
                        <p class="text-blue-100">Set up a new academic group to organize your educational content</p>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-8">
                <form method="POST" action="{{ route('academic-groups.store') }}"
                      x-data="{
                          name: '',
                          isValid: false,
                          updateValidation() {
                              this.isValid = this.name.trim().length >= 3;
                          }
                      }"
                      x-init="updateValidation()">
                    @csrf

                    <div class="space-y-8">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="flex items-center mb-6">
                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-form.input
                                        name="name"
                                        type="text"
                                        label="Academic Group Name"
                                        placeholder="e.g., Primary School, Secondary School, University Level"
                                        help-text="Choose a descriptive name that clearly identifies this academic group"
                                        x-model="name"
                                        x-on:input="updateValidation()"
                                        required />
                                </div>
                            </div>
                        </div>

                        <!-- Information Panel -->
                        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-semibold text-blue-900 mb-2">What is an Academic Group?</h4>
                                    <div class="text-sm text-blue-800 space-y-2">
                                        <p>Academic Groups help you organize your educational content by academic level or institution type.</p>
                                        <ul class="list-disc list-inside space-y-1 ml-4">
                                            <li>Group academic levels together (e.g., Primary, Secondary)</li>
                                            <li>Organize subjects and courses by educational stage</li>
                                            <li>Manage teachers and content across different academic levels</li>
                                            <li>Set up subscription packages for different academic groups</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6" x-show="name.trim().length > 0">
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Preview</h3>
                            </div>

                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4 border-l-4 border-blue-500">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-base font-semibold text-gray-900" x-text="name || 'Academic Group Name'"></h4>
                                        <p class="text-sm text-gray-600">0 Academic Levels • 0 Teachers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-8 pt-6 border-t border-gray-200">
                        <!-- Validation Message -->
                        <div class="flex items-center text-sm"
                             :class="isValid ? 'text-green-700' : 'text-amber-700'">
                            <svg x-show="!isValid" class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <svg x-show="isValid" x-cloak class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-show="!isValid">Name must be at least 3 characters long</span>
                            <span x-show="isValid" x-cloak>Ready to create academic group</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('academic-groups.index') }}"
                               class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>

                            <button type="submit"
                                    :disabled="!isValid"
                                    :class="isValid ?
                                        'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl transform hover:scale-105' :
                                        'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">

                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>

                                <span x-show="!isValid">Complete Form First</span>
                                <span x-show="isValid" x-cloak>Create Academic Group</span>
                            </button>
                        </div>
                    </div>

                    <!-- Next Steps Info -->
                    <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200" x-show="isValid">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-green-900 mb-1">Next Steps</h4>
                                <p class="text-sm text-green-800">
                                    After creating this academic group, you'll be able to add academic levels, assign teachers, and organize your educational content.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
