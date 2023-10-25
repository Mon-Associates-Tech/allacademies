<x-auth title="Expiring Subscriptions">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]" />
    </x-slot>
    <x-slot name="action">
        <x-link.secondary :to="route('expiring-subscriptions.renewals')" class="mr-2">Renewed Subscription</x-link.secondary>
        <x-link.primary :to="route('subscriptions.create')">New Subscription</x-link.primary>
    </x-slot>

    @if ($subscriptions->count())
        <x-table>
            <x-slot name="head">
                <tr>
                    <x-table.th>Reference</x-table.th>
                    <x-table.th>Package</x-table.th>
                    <x-table.th>Beneficiaries</x-table.th>
                    <x-table.th>Amount</x-table.th>
                    <x-table.th>Expires</x-table.th>
                    <x-table.th><span class="sr-only">Actions</span></x-table.th>
                </tr>
            </x-slot>

            @foreach ($subscriptions as $subscription)
                <tr>
                    <x-table.td bold>{{ $subscription->reference }}</x-table.td>
                    <x-table.td>{{ $subscription->package }}</x-table.td>
                    <x-table.td>{{ $subscription->beneficiaries }}</x-table.td>
                    <x-table.td>{{ $subscription->currency }} {{ $subscription->amount }}</x-table.td>
                    <x-table.td>{{ $subscription->expires_at->diffForHumans(['parts' => 2]) }}</x-table.td>
                    <x-table.td action>
                        <form class="inline" method="POST"
                            action="{{ route('expiring-subscriptions.store', ['expiring_subscription' => $subscription]) }}">
                            @csrf
                            <button class="text-primary-600 hover:text-primary-900">Renew</button>
                        </form>
                    </x-table.td>
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
