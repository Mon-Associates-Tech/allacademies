<x-layouts.app title="Academic Subjects" page-name="Academic Subjects">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => route('academic-levels.show', ['academic_level' => $academicLevel]),
        ]" />
    </x-slot>
    @can('administrate')
    <x-slot name="action">
        <x-link.primary :to="route('academic-levels.academic-subjects.create', ['academic_level' => $academicLevel])">New Academic Subject</x-link.primary>
    </x-slot>
    @endcan

    @if ($academicSubjects->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Code</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($academicSubjects as $academicSubject)
            <tr>
                <x-table.td bold>{{ $academicSubject->name }}</x-table.td>
                <x-table.td>{{ $academicSubject->code }}</x-table.td>
                <x-table.td action>
                    <x-action name="view" :to="route('academic-subjects.show', ['academic_subject' => $academicSubject])" />
                    @can('administrate')
                    <x-action name="edit" :to="route('academic-subjects.edit', ['academic_subject' => $academicSubject])" />
                    <x-action name="delete" :to="route('academic-subjects.destroy', ['academic_subject' => $academicSubject])">
                        Are you sure you want to delete {{ $academicSubject->name }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $academicSubjects->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-layouts.app>
