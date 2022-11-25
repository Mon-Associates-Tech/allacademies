<x-dashboard title="Subscriptions" summary="All subscriptions">
    <div>
        Dial <strong>*772*30#</strong> to pay for any subscription. Merchant Code is <em>1326001</em>. Please use the  reference indicated
    </div>
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List all subscriptions
                </div>
                <div>
                    <x-button :to="route('subscriptions.create')">Add new subscription</x-button>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Reference</x-table.th>
                <x-table.th>Package</x-table.th>
                <x-table.th>Beneficiaries</x-table.th>
                <x-table.th>Amount</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th>Expires</x-table.th>
                {{-- <x-table.th><span class="sr-only">Actions</span></x-table.th> --}}
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($subscriptions as $subscription)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $subscription->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $subscription->reference }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $subscription->package }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $subscription->beneficiaries }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $subscription->currency }} {{ $subscription->amount }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $subscription->status }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $subscription->expires_at->diffForHumans(['parts' => 2]) }}</td>
                {{-- <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('subscriptions.edit', ['subscription' => $subscription]) }}">Edit</a>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>