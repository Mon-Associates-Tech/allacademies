<x-dashboard title="Subscriptions" summary="All subscriptions">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List all subscriptions
                </div>
                <div>
                    <x-button :to="route('payments.create')">Add new payment</x-button>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Reference</x-table.th>
                <x-table.th>Amount</x-table.th>
                <x-table.th>Status</x-table.th>
                {{-- <x-table.th><span class="sr-only">Actions</span></x-table.th> --}}
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($payments as $payment)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $payment->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $payment->reference }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $payment->currency }} {{ $payment->amount }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $payment->status }}</td>
                {{-- <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('subscriptions.edit', ['subscription' => $subscription]) }}">Edit</a>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>