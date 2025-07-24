<x-layouts.app title="New True Or False Question" :has-action="false">
            <x-slot name="breadcrumb">
                <x-breadcrumb :paths="[
                    'Academic Groups' => route('academic-groups.index'),
                    $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                    'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                    $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                    'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                    $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                    'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                    $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                    'True or False Questions' => route('true-or-false-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                    'New Question' => null,
                ]"/>
            </x-slot>

            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Header -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Create True/False Question</h1>
                                <p class="text-gray-600">Add a new question for {{ $academicTopic->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Question Form -->
                    <form method="POST" action="{{ route('true-or-false-questions.store', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}" class="bg-white rounded-lg shadow-sm border border-gray-200">
                        @csrf

                        <!-- Question Content -->
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                <label for="question" class="block text-sm font-medium text-gray-700">Question Text <span class="text-red-500">*</span></label>
                                <x-form.rich-editor
                                    class="min-h-[200px]"
                                    name="question"
                                    placeholder="Enter your true/false question here..."
                                />
                                @error('question')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Answer Selection -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Correct Answer</label>
                                    <div class="flex items-center space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="answer" value="1" class="form-radio text-green-600">
                                            <span class="ml-2 text-sm text-gray-700">True</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="answer" value="0" class="form-radio text-red-600">
                                            <span class="ml-2 text-sm text-gray-700">False</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="border-t border-gray-200 p-6 bg-gray-50 space-y-4">
                            <h3 class="text-sm font-medium text-gray-900">Question Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-form.select
                                        name="difficulty_level"
                                        label="Difficulty"
                                        :options="[
                    'unspecified' => 'Unspecified',
                    'easy' => 'Easy',
                    'medium' => 'Medium',
                    'difficult' => 'Difficult',
                ]"
                                    />
                                </div>
                                <div>
                                    <x-form.input
                                        name="score"
                                        type="number"
                                        label="Score Points"
                                        value="1"
                                        min="1"
                                        max="100"
                                    />
                                </div>
                                <div x-data="{ showCustomInput: false, selectedValue: '' }">
                                    <label for="subtopic_select" class="block text-sm font-medium text-gray-700 mb-1">
                                        Subtopic
                                    </label>
                                    <select
                                        id="subtopic_select"
                                        x-model="selectedValue"
                                        @change="showCustomInput = (selectedValue === 'new')"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">Select a subtopic (optional)</option>
                                        @foreach($academicTopic->subtopics as $subtopic)
                                            <option value="{{ $subtopic->name }}" {{ old('subtopic') == $subtopic->name ? 'selected' : '' }}>
                                                {{ $subtopic->name }}
                                            </option>
                                        @endforeach
                                        <option value="new">+ Create New Subtopic</option>
                                    </select>

                                    <!-- Hidden input for existing subtopic selection -->
                                    <input
                                        type="hidden"
                                        name="subtopic"
                                        :value="selectedValue !== 'new' ? selectedValue : ''"
                                        x-show="!showCustomInput"
                                    />

                                    <!-- Custom input for new subtopic -->
                                    <div x-show="showCustomInput" x-transition class="mt-2">
                                        <x-form.input
                                            type="text"
                                            name="subtopic"
                                            label="New Subtopic Name"
                                            placeholder="Enter new subtopic name"
                                            x-model="selectedValue"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
                            <x-link.secondary :to="route('true-or-false-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])" class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Cancel
                            </x-link.secondary>

                            <x-button.primary type="submit" class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Question
                            </x-button.primary>
                        </div>
                    </form>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Help Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tips for Good Questions</h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Keep questions clear and unambiguous
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Focus on a single concept
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Avoid using absolute terms
                            </li>
                        </ul>
                    </div>

                    <!-- Plugins Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Available Tools</h3>
                            <x-plugins link="{{ url()->current() . '/new' }}" />
                        </div>
                    </div>
                </div>
            </div>
        </x-layouts.app>
