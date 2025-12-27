<x-layouts.app page-name="Create Subscription"  :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]"/>
    </x-slot>

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
