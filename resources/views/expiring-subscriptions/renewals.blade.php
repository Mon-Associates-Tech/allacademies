<x-auth title="Renewals - Subscriptions">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
            'Expiring Subscriptions' => route('expiring-subscriptions.index'),
        ]" />
    </x-slot>
    <x-slot name="action">
        <x-link.secondary :to="route('expiring-subscriptions.index')" class="mr-2">Expiring Subscription</x-link.secondary>
        <x-link.primary :to="route('subscriptions.create')">New Subscription</x-link.primary>
    </x-slot>

    <div class="rounded-md bg-blue-50 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-1 md:flex md:justify-between">
                <p class="text-sm text-blue-700">Dial <strong>*772*30#</strong> to pay for any subscription renewal.
                    Merchant
                    Code is <em>1326001</em>. Please use the reference indicated.</p>
            </div>
        </div>
    </div>


    @if ($subscriptions->count())
        <x-table>
            <x-slot name="head">
                <tr>
                    <x-table.th>Reference</x-table.th>
                    <x-table.th>Package</x-table.th>
                    <x-table.th>Beneficiaries</x-table.th>
                    <x-table.th>Amount</x-table.th>
                    <x-table.th>Status</x-table.th>
                </tr>
            </x-slot>

            @foreach ($subscriptions as $subscription)
                {{-- {{ dd($subscription) }} --}}
                <tr>
                    <x-table.td bold>{{ $subscription->reference }}</x-table.td>
                    <x-table.td>{{ $subscription->subscription->package }}</x-table.td>
                    <x-table.td>{{ $subscription->subscription->beneficiaries }}</x-table.td>
                    <x-table.td>{{ $subscription->subscription->currency }}
                        {{ $subscription->subscription->amount }}</x-table.td>
                    <x-table.td>{{ $subscription->status }}</x-table.td>
                </tr>
            @endforeach
        </x-table>

        <div class="mt-3">
            {{ $subscriptions->links() }}
        </div>
    @else
        <x-blank />
    @endif
</x-auth>
