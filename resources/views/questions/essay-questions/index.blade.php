<x-layouts.app title="Essay Questions" :show-title-area="false" page-name="Essay Questions">
    <x-exam.page variant="compact">

        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicTopic->academicSubject->academicLevel->academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup])],
            ['label' => $academicTopic->academicSubject->academicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => 'Academic Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => $academicTopic->academicSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => 'Academic Topics', 'href' => route('academic-topics.index', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => $academicTopic->name, 'href' => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])],
        ]" />

        <x-exam.header
            variant="compact"
            :title="$academicTopic->name . ' Essay Questions'"
            eyebrow="essay questions"
            color="muted"
        >
            @can('moderate')
                <x-slot:actions>
                    <a href="{{ route('essay-questions.create', ['academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 bg-primary text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] hover:bg-primary-hi">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Question
                    </a>
                </x-slot:actions>
            @endcan
        </x-exam.header>

        <x-exam.card :padded="false">
            @if ($essayQuestions->count())
                <div class="divide-y divide-line">
                    @foreach ($essayQuestions as $essayQuestion)
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 md:gap-0 px-4 py-3 hover:bg-surface-2/60 transition-colors">
                            <div class="min-w-0 pr-4">
                                <div class="text-ink line-clamp-3" title="{{ strip_tags($essayQuestion->question->up) }}">
{{--                                    {{ strip_tags($essayQuestion->question->up) }}--}}
                                    <x-ui.latex :content="$essayQuestion->question->down" />
                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <x-exam.badge tone="ok">
                                        {{ $essayQuestion->score }} points
                                    </x-exam.badge>
                                    <x-exam.badge :tone="match($essayQuestion->difficulty_level) {
                                        'Easy' => 'ok',
                                        'Medium' => 'warn',
                                        'Hard' => 'danger',
                                        default => 'muted'
                                    }">
                                        {{ $essayQuestion->difficulty_level }}
                                    </x-exam.badge>
                                </div>
                            </div>
                            <div class="flex items-center justify-center md:justify-end gap-3 whitespace-nowrap md:shrink-0 md:my-auto">
                                <a href="{{ route('essay-questions.show', ['essay_question' => $essayQuestion, 'academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:text-primary-hi transition hover:border-current focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                    View
                                </a>

                                @can('moderate')
                                    <a href="{{ route('essay-questions.edit', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                       class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:text-primary-hi transition hover:border-current focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                        Edit
                                    </a>

                                    <x-exam.link-button tone="danger"
                                                        x-data="{}"
                                                        x-on:click="$store.deleteForm.show('Delete Essay Question', 'Are you sure you want to delete this essay question?', '{{ route('essay-questions.destroy', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')">
                                        Delete
                                    </x-exam.link-button>
                                @endcan
                            </div>
                        </div>
                    @endforeach

                    @if($essayQuestions->hasPages())
                        <div class="flex justify-center border-t border-line bg-surface-2 px-4 py-3">
                            {{ $essayQuestions->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="p-6">
                    <x-exam.empty title="No essay questions yet" :description="'Get started by creating your first essay question for ' . $academicTopic->name . '.'">
                        @can('moderate')
                            <a href="{{ route('essay-questions.create', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                               class="mt-5 inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 bg-primary text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] hover:bg-primary-hi">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create First Question
                            </a>
                        @endcan
                    </x-exam.empty>
                </div>
            @endif
        </x-exam.card>

    </x-exam.page>
</x-layouts.app>
