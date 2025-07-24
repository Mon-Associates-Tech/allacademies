<x-layouts.app title="Edit Essay Question" :has-action="false">
        <x-slot name="breadcrumb">
            <x-breadcrumb :paths="[
                'Academic Groups' => route('academic-groups.index'),
                $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                'Academic Levels' => route('academic-levels.index', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $essayQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                'Academic Topics' => route('academic-topics.index', ['academic_subject' => $essayQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                $essayQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                'Essay Questions' => route('essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                'Edit' => null,
            ]"/>
        </x-slot>

        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Essay Question</h1>
                        <p class="text-gray-600">Topic: {{ $essayQuestion->academicTopic->name }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Question Details</h2>
                    </div>

                    <form method="POST" action="{{ route('essay-questions.update', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}" class="p-6 space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Configuration Fields -->
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
                                    :value="$essayQuestion->difficulty_level"
                                />
                                @error('difficulty_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="score" class="block text-sm font-medium text-gray-700">Maximum Score</label>
                                <x-form.input
                                    name="score"
                                    type="number"
                                    :value="$essayQuestion->score"
                                    placeholder="Enter maximum score"
                                    min="0"
                                />
                                @error('score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Subtopic Field -->
                        <div x-data="{ showCustomInput: false, selectedValue: '{{ $essayQuestion->subtopic?->name ?? '' }}' }">
                            <label for="subtopic_select" class="block text-sm font-medium text-gray-700 mb-1">
                                Sub Topic
                            </label>

                            @if($essayQuestion->academicTopic->subtopics->count() > 0)
                                <select
                                    id="subtopic_select"
                                    x-model="selectedValue"
                                    @change="showCustomInput = (selectedValue === 'new')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Enter subtopic or leave blank</option>
                                    @foreach($essayQuestion->academicTopic->subtopics as $subtopic)
                                        <option value="{{ $subtopic->name }}"
                                            {{ (old('subtopic', $essayQuestion->subtopic?->name) == $subtopic->name) ? 'selected' : '' }}>
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
                                    name="subtopic"
                                    :value="$essayQuestion->subtopic?->name"
                                    placeholder="Enter subtopic or leave blank"
                                />
                                <p class="text-xs text-gray-500 mt-1">No existing subtopics found. Enter a new one above.</p>
                            @endif

                            <p class="mt-1 text-sm text-gray-500">Optional: Specify a subtopic for better organization</p>
                        </div>

                        <!-- Question Content -->
                        <div class="space-y-4">
                            <div>
{{--                                <label for="question" class="block text-sm font-medium text-gray-700">Question</label>--}}
                                <x-form.rich-editor
                                    class="rich-editor mt-1"
                                    name="question"
                                    :value="$essayQuestion->question"
                                />
                                @error('question')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
{{--                                <label for="answer" class="block text-sm font-medium text-gray-700">Model Answer</label>--}}
                                <x-form.rich-editor
                                    class="rich-editor mt-1"
                                    name="answer"
                                    :value="$essayQuestion->answer"
                                />
                                @error('answer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <x-link.secondary :to="route('essay-questions.index', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                                Cancel
                            </x-link.secondary>
                            <x-button.primary type="submit">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Question
                            </x-button.primary>
                        </div>
                    </form>
                </div>

                <!-- Sidebar -->
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Plugins & Tools</h2>
                        </div>
                        <div class="p-6">
                            <x-plugins :link="url()->current() . '/new'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const editor = document.getElementById('question-editor');
                if (editor && window.marked) {
                    const rawMarkdown = editor.value;
                    const html = marked.parse(rawMarkdown);
                    editor.value = html;
                }
            });
        </script>
    </x-layouts.app>
