<x-layouts.app title="Create Subscription"  :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]"/>
    </x-slot>

    <section class="max-w-4xl mx-auto bg-white p-4 rounded-md w-full relative min-h-screen h-full mb-8">
        <div class="rounded-md max-w-4xl bg-blue-50 p-4 mb-6 mx-auto justify-center place-items-center">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1 md:flex  md:justify-between">
                    <p class="text-sm text-blue-700">This subscription will apply to
                        <strong>{{ $currentTeam->name }}</strong>. You can change the team if this not your intended
                        team.</p>
                    <p class="mt-3 text-sm md:ml-6 md:mt-0">
                        <a href="{{ route('teams.index') }}"
                           class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600">
                            Change Team
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid place-items-center mx-auto justify-center w-full">

            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf

                @livewire('subscription-form', ['academicGroups' => $academicGroups, 'currentTeam' => $currentTeam])

                <div x-data="{hasSubjects: $numberOfSubjects >= 1}" class="flex max-w-[35rem]  justify-end mt-5">
                    <x-button.primary class="ml-2">Create Subscription</x-button.primary>
                </div>
            </form>
        </div>
    </section>

</x-layouts.app>
