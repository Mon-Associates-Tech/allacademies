<x-auth title="New Academic Group">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('academic-groups.store') }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Academic Group</x-button.primary>
        </div>
    </form>
</x-auth>
