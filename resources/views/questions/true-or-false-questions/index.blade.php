<x-layouts.app title="True Or False Questions" :has-action="false">
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
            'True or False Questions' => null,
        ]"/>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">True/False Questions</h1>
                        <p class="text-gray-600">{{ $academicTopic->name }}</p>
                    </div>
                </div>

                @can('moderate')
                    <x-link.primary
                        :to="route('true-or-false-questions.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                        class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Question
                    </x-link.primary>
                @endcan
            </div>
        </div>

        <!-- Context Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Questions for topic <strong>{{ $academicTopic->name }}</strong> in
                        <strong>{{ $academicTopic->academicSubject->name }}</strong>
                    </p>
                </div>
            </div>
        </div>

        @if ($trueOrFalseQuestions->count())
            <!-- Questions Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Questions List</h2>
                </div>

                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-table.th>Question</x-table.th>
                            <x-table.th>Score</x-table.th>
                            <x-table.th>Difficulty Level</x-table.th>
                            <x-table.th><span class="sr-only">Actions</span></x-table.th>
                        </tr>
                    </x-slot>

                    @foreach ($trueOrFalseQuestions as $trueOrFalseQuestion)
                        <tr>
                            <x-table.td bold> <span x-html="marked.parse(@js($trueOrFalseQuestion->question->summary))"></span></x-table.td>
                            <x-table.td>{{ $trueOrFalseQuestion->score }}</x-table.td>
                            <x-table.td>{{ $trueOrFalseQuestion->difficulty_level }}</x-table.td>
                            <x-table.td action>
                                <x-action name="view"
                                          :to="route('true-or-false-questions.show', ['true_or_false_question' => $trueOrFalseQuestion, 'academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"/>
                                @can('moderate')
                                    <x-action name="edit"
                                              :to="route('true-or-false-questions.edit', ['true_or_false_question' => $trueOrFalseQuestion, 'academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"/>
                                    <x-action name="delete"
                                              :to="route('true-or-false-questions.destroy', ['true_or_false_question' => $trueOrFalseQuestion, 'academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                                        Are you sure you want to delete {{ $trueOrFalseQuestion->question->summary }}
                                    </x-action>
                                @endcan
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $trueOrFalseQuestions->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="text-center py-16 px-6">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-6" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>

                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">No Questions Added Yet</h3>
                    <p class="text-gray-600 mb-8">Get started by creating your first true/false question.</p>

                    @can('moderate')
                        <x-link.primary
                            :to="route('true-or-false-questions.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                            class="inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add First Question
                        </x-link.primary>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Quick Navigation -->
        <div class="text-center">
            <x-link.secondary
                :to="route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                class="inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Topic
            </x-link.secondary>
        </div>
    </div>
</x-layouts.app>
