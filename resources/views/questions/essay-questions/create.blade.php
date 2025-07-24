<x-layouts.app title="New Essay Question" :main-only="false">
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
            'Essay Questions' => route('essay-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]"/>
    </x-slot>

    <!-- Add header section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-indigo-100 rounded-full">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Essay Question</h1>
                <p class="text-gray-600">Add a new essay question to {{ $academicTopic->name }}</p>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-5 gap-6">
        <div class="col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Question Details</h2>
                    <p class="text-sm text-gray-600 mt-1">Fill in the details for your essay question.</p>
                </div>

                <form method="POST" action="{{ route('essay-questions.store', [
                                        'academic_topic' => $academicTopic,
                                        'academic_subject' => getRouteParameter('academic_subject'),
                                        'academic_level' => getRouteParameter('academic_level'),
                                        'academic_group' => getRouteParameter('academic_group')
                                    ]) }}" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-form.select
                                name="difficulty_level"
                                label="Difficulty Level"
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
                                value="15"
                                label="Maximum Score"
                            />
                        </div>
                    </div>

                    <div x-data="{ showCustomInput: false, selectedValue: '' }">
                        <label for="subtopic_select" class="block text-sm font-medium text-gray-700 mb-1">
                            Sub Topic
                        </label>

                        @if($academicTopic->subtopics->count() > 0)
                            <select
                                id="subtopic_select"
                                x-model="selectedValue"
                                @change="showCustomInput = (selectedValue === 'new')"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Enter subtopic or leave blank</option>
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
                                />
                            </div>
                        @else
                            {{-- Fallback: Simple text input if no subtopics exist --}}
                            <x-form.input
                                type="text"
                                placeholder="Enter subtopic or leave blank"
                                label="Sub Topic"
                                name="subtopic"
                            />
                            <p class="text-xs text-gray-500 mt-1">No existing subtopics found. Enter a new one above.</p>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question Content</label>
                            <x-form.rich-editor class="rich-editor" full name="question"/>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Model Answer</label>
                            <x-form.rich-editor class="rich-editor" full name="answer"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <x-button.secondary type="button" onclick="history.back()">
                            Cancel
                        </x-button.secondary>
                        <x-button.primary class="ml-4">
                            Create Question
                        </x-button.primary>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Helpful Tools</h2>
                </div>
                <div class="p-6">
                    <x-plugins link="{{ url()->current() . '/new' }}"/>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
