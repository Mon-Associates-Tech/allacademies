<x-layouts.app page-name="Create Subscription"  :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]"/>
    </x-slot>
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
    <section class="mx-auto w-full relative min-h-screen h-full mb-8">


        <div class="justify-center w-full">
            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf

                @livewire('subscription-form', ['academicGroups' => $academicGroups, 'currentTeam' => $currentTeam])
            </form>
        </div>
    </section>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById("subscriptionButton");
    const iconDisabled = document.getElementById("iconDisabled");
    const iconEnabled = document.getElementById("iconEnabled");
    const textDisabled = document.getElementById("textDisabled");
    const textEnabled = document.getElementById("textEnabled");

    // Replace this with your actual subjects count logic
    let subjectsCount = {{ count($academicGroups) }};

    function updateButton() {
        if (subjectsCount === 0) {
            button.disabled = true;
            button.classList.remove("bg-gradient-to-r", "from-blue-600", "to-indigo-600", "hover:from-blue-700", "hover:to-indigo-700", "text-white", "shadow-lg", "hover:shadow-xl", "transform", "hover:scale-105");
            button.classList.add("bg-gray-300", "text-gray-500", "cursor-not-allowed");

            iconDisabled.classList.remove("hidden");
            iconEnabled.classList.add("hidden");
            textDisabled.classList.remove("hidden");
            textEnabled.classList.add("hidden");
        } else {
            button.disabled = false;
            button.classList.remove("bg-gray-300", "text-gray-500", "cursor-not-allowed");
            button.classList.add("bg-gradient-to-r", "from-blue-600", "to-indigo-600", "hover:from-blue-700", "hover:to-indigo-700", "text-white", "shadow-lg", "hover:shadow-xl", "transform", "hover:scale-105");

            iconDisabled.classList.add("hidden");
            iconEnabled.classList.remove("hidden");
            textDisabled.classList.add("hidden");
            textEnabled.classList.remove("hidden");
        }
    }

    // Initial load
    updateButton();

    // Example: when subjects are updated (you can trigger this in Livewire or JS)
    window.updateSubjects = function (count) {
        subjectsCount = count;
        updateButton();
    };
});

</script>
</x-layouts.app>
