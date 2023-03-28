<x-auth title="New Payment">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payments' => route('payments.index'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('payments.store') }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="reference" type="text" />
            </div>
            <div>
                <x-form.input name="amount" type="text" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Payment</x-button.primary>
        </div>
    </form>
</x-auth>
