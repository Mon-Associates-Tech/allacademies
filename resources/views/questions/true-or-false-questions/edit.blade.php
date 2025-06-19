<x-layouts.app title="Edit True Or False Question" :has-action="false">
        <x-slot name="breadcrumb">
            <x-breadcrumb :paths="[
                'Academic Groups' => route('academic-groups.index'),
                $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Academic Levels' => route('academic-levels.index', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                $trueOrFalseQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                'Academic Topics' => route('academic-topics.index', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]),
                $trueOrFalseQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                'True Or False Questions' => route('true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject'=>getRouteParameter('academic_subject'), 'academic_level'=>getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]),
                'Edit' => null,
            ]" />
        </x-slot>

        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit True Or False Question</h1>
                        <p class="text-gray-600">Update the question details and configuration</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <div class="col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Question Information</h2>
                            <p class="text-sm text-gray-600 mt-1">Modify the question content and settings.</p>
                        </div>

                        <form method="POST" action="{{ route('true-or-false-questions.update', ['true_or_false_question' => $trueOrFalseQuestion, 'academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}" class="p-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Configuration Fields -->
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700">
                                        Difficulty Level
                                    </label>
                                    <x-form.select name="difficulty_level" :options="[
                                        'unspecified' => 'Unspecified',
                                        'easy' => 'Easy',
                                        'medium' => 'Medium',
                                        'difficult' => 'Difficult',
                                    ]" :value="$trueOrFalseQuestion->difficulty_level" />
                                </div>

                                <div class="space-y-2">
                                    <label for="score" class="block text-sm font-medium text-gray-700">
                                        Score Points <span class="text-red-500">*</span>
                                    </label>
                                    <x-form.input name="score" type="number" :value="$trueOrFalseQuestion->score" required />
                                </div>
                            </div>

                            <!-- Subtopic Field -->
                            <div class="space-y-2">
                                <label for="subtopic" class="block text-sm font-medium text-gray-700">
                                    Subtopic (Optional)
                                </label>
                                <x-form.input
                                    type="text"
                                    name="subtopic"
                                    :value="$trueOrFalseQuestion?->subtopic?->name"
                                    placeholder="Enter subtopic or leave blank"
                                />
                            </div>

                            <!-- Question Content -->
                            <div class="space-y-2">
                                <label for="question" class="block text-sm font-medium text-gray-700">
                                    Question Content <span class="text-red-500">*</span>
                                </label>
                                <x-form.rich-editor name="question" :value="$trueOrFalseQuestion->question" />
                            </div>

                            <!-- Answer Field -->
                            <div class="space-y-2">
                                <x-form.checkbox
                                    name="answer"
                                    :value="$trueOrFalseQuestion->answer"
                                    description="Check if the statement is true, leave unchecked if false"
                                />
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <x-link.secondary :to="route('true-or-false-questions.index', ['true_or_false_question' => $trueOrFalseQuestion,  'academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                        </svg>
                                        Cancel
                                    </x-link.secondary>
                                </div>

                                <x-button.primary type="submit">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Question
                                </x-button.primary>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-span-1 space-y-6">
                    <!-- Context Card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Question Details</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Topic: <strong>{{ $trueOrFalseQuestion->academicTopic->name }}</strong></p>
                                    <p>Subject: <strong>{{ $trueOrFalseQuestion->academicTopic->academicSubject->name }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plugins Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-medium text-gray-900">Question Plugins</h3>
                        </div>
                        <div class="p-4">
                            <x-plugins link="{{ url()->current() . '/new' }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-layouts.app>
