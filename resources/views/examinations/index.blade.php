<x-auth title="Examinations">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    
    @can('privileged', Auth::user()->currentTeam)
        <x-slot name="action">
            <x-link.primary :to="route('academic-subjects.examinations.create', ['academic_subject' => $academicSubject])">New Examination</x-link.primary>
        </x-slot>
    @endcan

    @if ($examinations->count())
        <x-table>
            <x-slot name="head">
                <tr>
                    <x-table.th>Title</x-table.th>
                    <x-table.th><span class="sr-only">Actions</span></x-table.th>
                </tr>
            </x-slot>

            @foreach ($examinations as $examination)
                <tr>
                    <x-table.td bold>{{ $examination->title }}</x-table.td>
                    <x-table.td action>
                        <a class="text-primary-600 hover:text-primary-900"
                            href="{{ route('examinations.show', ['examination' => $examination]) }}">Question Paper</a>
                        <a class="text-primary-600 hover:text-primary-900"
                            href="{{ route('examinations.answers', ['examination' => $examination]) }}">Answer Scheme</a>
                    </x-table.td>
                </tr>
            @endforeach
        </x-table>

        <div class="mt-3">
            {{ $examinations->links() }}
        </div>
    @else
        <x-blank />
    @endif
</x-auth>
