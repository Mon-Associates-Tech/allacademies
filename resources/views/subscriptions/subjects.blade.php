<x-auth title="Subjects on Subscription">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
            $subscription->reference => route('subscriptions.show', [
                'subscription' => $subscription,
            ]),
        ]" />
    </x-slot>
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Code</x-table.th>
            </tr>
        </x-slot>

        @foreach ($subscription->academicSubjects as $academicSubject)
            <tr>
                <x-table.td bold>{{ $academicSubject->name }}</x-table.td>
                <x-table.td>{{ $academicSubject->code }}</x-table.td>
            </tr>
        @endforeach
    </x-table>

    {{-- <div class="mt-3">
        {{ $subscription->academicSubjects->links() }}
    </div> --}}
</x-auth>
