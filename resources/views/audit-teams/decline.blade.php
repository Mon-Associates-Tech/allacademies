<x-auth title="Decline Team Changes">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams (Auditing)' => route('audit-teams.index'),
            'Institutional Information Changes' => route('audit-teams.show', ['audit_team' => $auditTeam]),
        ]" />
    </x-slot>


    <form method="POST" action="{{ route('audit-teams.decline', ['audit_team' => $auditTeam]) }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" :value="$auditTeam->name" disabled />
            </div>
            <div class="col-span-2">
                <x-form.textarea name="reason" type="text" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Decline</x-button.primary>
        </div>
    </form>
</x-auth>
