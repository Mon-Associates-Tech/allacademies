<x-layouts.app title="Create Subscription"  :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]"/>
    </x-slot>

    <section class="mx-auto bg-white p-4 rounded-md w-full relative min-h-screen h-full mb-8">
        <div class="rounded-md bg-blue-50 p-4 mb-6 mx-auto justify-center place-items-center">
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
                    <p class="my-auto text-sm md:ml-6">
                        <a href="{{ route('teams.index') }}"
                           class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600">
                            Change Team
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="justify-center w-full">

            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf

                @livewire('subscription-form', ['academicGroups' => $academicGroups, 'currentTeam' => $currentTeam])

                <!-- Enhanced Submit Button Section -->
                <div class="max-w-7xl mx-auto mt-8">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                             x-data="{ hasSubjects: @js($academicGroups) }"
                             x-init="$wire.on('subjectsUpdated', (count) => { hasSubjects = count > 0 })">

                            <!-- Validation Message -->
                            <div class="flex items-center text-sm"
                                 :class="hasSubjects ? 'text-green-700' : 'text-amber-700'">
                                <svg x-show="!hasSubjects" class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <svg x-show="hasSubjects" x-cloak class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-show="!hasSubjects">Please select at least one subject to continue</span>
                                <span x-show="hasSubjects" x-cloak>Ready to create your subscription</span>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center gap-3">
                                <button type="submit"
                                        :disabled="!hasSubjects"
                                        :class="hasSubjects ?
                                            'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl transform hover:scale-105' :
                                            'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">

                                    <!-- Loading State -->
                                    <svg x-show="!hasSubjects" class="animate-spin -ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>

                                    <!-- Success Icon -->
                                    <svg x-show="hasSubjects" x-cloak class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>

                                    <span x-show="!hasSubjects">Select Subjects First</span>
                                    <span x-show="hasSubjects" x-cloak>Create Subscription</span>
                                </button>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-center">
                                By creating this subscription, you agree to our
                                <a href="#" class="text-blue-600 hover:text-blue-800 underline">Terms of Service</a>
                                and
                                <a href="#" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

</x-layouts.app>
