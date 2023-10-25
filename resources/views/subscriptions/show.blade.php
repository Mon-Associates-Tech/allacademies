<x-auth title="Subscription Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Reference">{{ $subscription->reference }}</x-detail.data>
        <x-detail.data label="Expires">{{ $subscription->expires_at->diffForHumans(['parts' => 2]) }}</x-detail.data>
        <x-detail.data
            label="Package">{{ $subscription->package == 'individual:full' ? 'Individual (Full Option)' : 'Institution (Full Option)' }}
        </x-detail.data>
        <x-detail.data label="Team">{{ $subscription->team->name }}</x-detail.data>
        <x-detail.data label="Amount">{{ $subscription->currency . $subscription->amount }}</x-detail.data>
        <x-detail.data label="Status">{{ $subscription->status }}</x-detail.data>
        <x-detail.data label="No. Beneficiaries">{{ $subscription->beneficiaries }}</x-detail.data>
        <x-detail.data label="Academic Subjects">
            <x-anchor to="{{ route('subscriptions.subjects', ['subscription' => $subscription]) }}">
                {{ $subscription->academic_subjects_count }} academic
                {{ Str::plural('subject', $subscription->academic_subjects_count) }}
            </x-anchor>
        </x-detail.data>

        @if ($renew)
            <x-slot name="action">
                <form class="inline" method="POST"
                    action="{{ route('expiring-subscriptions.store', ['expiring_subscription' => $subscription]) }}">
                    @csrf
                    <x-button.primary class="text-primary-600 hover:text-primary-900">Renew
                        Subscription</x-button.primary>
                </form>
            </x-slot>
        @endif
    </x-detail>
</x-auth>
