<x-layouts.app title="New Academic Topic" :has-action="false" :show-title-area="false">
                    <x-slot name="breadcrumb">
                        <x-breadcrumb :paths="[
                            'Academic Groups' => route('academic-groups.index'),
                            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup, ]),
                            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                            $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                            'New' => null,
                        ]" />
                    </x-slot>

                    <div class="max-w-2xl mx-auto space-y-6">
                        <!-- Header Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Create New Topic</h1>
                                    <p class="text-gray-600">Add a new topic to {{ $academicSubject->name }}</p>
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
                                        This topic will be created under <strong>{{ $academicSubject->name }}</strong> in
                                        <strong>{{ $academicSubject->academicLevel->name }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Create Form -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Topic Information</h2>
                                <p class="text-sm text-gray-600 mt-1">Enter the basic information for this academic topic.</p>
                            </div>

                            <form method="POST" action="{{ route('academic-topics.store', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}" class="p-6 space-y-6">
                                @csrf

                                <!-- Name Field -->
                                <div class="space-y-2">
                                    <x-form.input
                                        name="name"
                                        type="text"
                                        placeholder="Enter topic name"
                                        class="w-full"
                                        value="{{ old('name') }}"
                                        label="Topic Name"
                                        info="Choose a clear and descriptive name for this topic"
                                        info-position="bottom"
                                        required
                                    />
                                    @error('name')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                    <div class="flex items-center space-x-4">
                                        <x-link.secondary :to="route('academic-topics.index', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                            </svg>
                                            Cancel
                                        </x-link.secondary>
                                    </div>

                                    <x-button.primary type="submit" class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create Topic
                                    </x-button.primary>
                                </div>
                            </form>
                        </div>
                    </div>
                </x-layouts.app>
