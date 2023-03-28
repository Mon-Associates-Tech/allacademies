<x-auth title="Payments">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    @can('administrate')
    <x-slot name="action">
        <x-link.primary :to="route('payments.create')">New Payment</x-link.primary>
    </x-slot>
    @endcan

    @if ($payments->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Reference</x-table.th>
                <x-table.th>Amount</x-table.th>
                <x-table.th>Status</x-table.th>
            </tr>
        </x-slot>

        @foreach ($payments as $payment)
            <tr>
                <x-table.td bold>{{ $payment->reference }}</x-table.td>
                <x-table.td>{{ $payment->currency }} {{ $payment->amount }}</x-table.td>
                <x-table.td><span class="inline-flex items-center rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 capitalize">{{ $payment->status }}</span></x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
