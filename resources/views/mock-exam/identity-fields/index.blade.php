<x-layouts.app>
    <x-mock-header
        title="Identity Fields"
        subtitle="Manage the identity fields for the mock exam."
        :back-url="route('mock-exams.index')">

    </x-mock-header>
    @livewire('mock-exam.identity-field-manager')
    <div>
        @livewire('mock-exam.my-identity-form')
    </div>
</x-layouts.app>
