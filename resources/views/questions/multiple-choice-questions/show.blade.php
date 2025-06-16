<x-layouts.app title="Multiple Choice Question Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            $multipleChoiceQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject,  'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            $multipleChoiceQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]" class="overflow-x-auto" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Question Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                    Multiple Choice Question
                </h1>
                @can('moderate')
                    <div class="flex space-x-2">
                        <x-button.secondary
                            type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show(
                                'Delete Question',
                                'Are you sure you want to delete this question?',
                                '{{ route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'
                            )"
                            class="hidden sm:inline-flex"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </x-button.secondary>
                        <x-link.primary
                            :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                            class="hidden sm:inline-flex"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </x-link.primary>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Question Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Question Text -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Question</h2>
                <div class="prose dark:prose-invert max-w-none">
                    <span x-html="marked.parse(@js($multipleChoiceQuestion->question->summary))"></span>
                </div>
            </div>

          <!-- Options -->
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
              @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                  @if($multipleChoiceQuestion->{"option_$option"}->html)
                      <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150
                          {{ strtoupper($multipleChoiceQuestion->answer) === strtoupper($option) ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                          <div class="flex items-start space-x-4">
                              <div class="flex-shrink-0">
                                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                      {{ strtoupper($multipleChoiceQuestion->answer) === strtoupper($option)
                                          ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                          : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                                      }}">
                                      {{ strtoupper($option) }}
                                  </span>
                              </div>
                              <div class="flex-1 min-w-0">
                                  @php
                                      $optText = $multipleChoiceQuestion->{"option_$option"}->html;
                                  @endphp
                                  <div x-data="{option: @js(parsedMarkdown($optText))}" class="prose dark:prose-invert max-w-none">
                                      {!! parsedMarkdown($optText) !!}
                                  </div>
                                  @if(strtoupper($multipleChoiceQuestion->answer) === strtoupper($option))
                                      <div class="mt-2 flex items-center text-sm text-green-600 dark:text-green-400">
                                          <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                          </svg>
                                          Correct Answer
                                      </div>
                                  @endif
                              </div>
                          </div>
                      </div>
                  @endif
              @endforeach
          </div>

            <!-- Topic Information -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Topic</h3>
                        <x-anchor
                            to="{{ route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                            class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                        >
                            {{ $multipleChoiceQuestion->academicTopic->name }}
                        </x-anchor>
                    </div>

                    @if(isset($multipleChoiceQuestion->subtopic))
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Subtopic</h3>
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $multipleChoiceQuestion->subtopic->name }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile Action Buttons -->
        @can('moderate')
            <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 sm:hidden">
                <div class="flex justify-between gap-4">
                    <x-button.secondary
                        type="button"
                        x-data="{}"
                        x-on:click="$store.deleteForm.show('Delete Question', 'Are you sure?', '{{ route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')"
                        class="flex-1"
                    >
                        Delete
                    </x-button.secondary>
                    <x-link.primary
                        :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                        class="flex-1"
                    >
                        Edit
                    </x-link.primary>
                </div>
            </div>
        @endcan
    </div>
</x-layouts.app>
