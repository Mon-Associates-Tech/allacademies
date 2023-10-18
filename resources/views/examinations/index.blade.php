<x-auth title="Examinations">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    @if ($canCreate)
        <x-slot name="action">
            <x-link.primary :to="route('academic-subjects.examinations.create', ['academic_subject' => $academicSubject])">New Examination</x-link.primary>
        </x-slot>
    @else
        <x-alert.info name="Note"
            message="Institution details must be provided under edit teams and approved before you can create an examination."
            :svg="[
                'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
            ]" />
    @endif

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
