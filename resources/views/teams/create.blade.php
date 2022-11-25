<x-dashboard title="Subscriptions" summary="Add new subscription">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new subscription
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('payments.store') }}">
        @csrf
        <x-form.input full name="reference" />
        <x-form.input full name="amount" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>