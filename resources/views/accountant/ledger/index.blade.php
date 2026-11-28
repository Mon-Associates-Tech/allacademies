<x-layouts.app pageName="General Ledger">
    <x-slot name="title">General Ledger</x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-600 dark:text-gray-300">Cashbook of successful fee payments, paid expenses, and paid payroll runs for the selected school.</p>
            <a href="{{ route('accountant.expenses.index') }}" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">Manage expenses</a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="GET" class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow dark:bg-gray-800 md:grid-cols-3">
            <div>
                <label for="start_date" class="mb-1 block text-sm font-medium">From date</label>
                <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="end_date" class="mb-1 block text-sm font-medium">To date</label>
                <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div class="flex items-end gap-2">
                <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white dark:bg-gray-600">Filter</button>
                <a href="{{ route('accountant.ledger.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm dark:bg-gray-700">Reset</a>
            </div>
        </form>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Reference</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3">Reconciliation</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($entries as $entry)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4">{{ \Illuminate\Support\Carbon::parse($entry->transaction_date)->format('M d, Y') }}</td>
                            <td class="px-5 py-4 font-mono text-xs">{{ $entry->reference ?: strtoupper($entry->source_type).'-'.$entry->source_id }}</td>
                            <td class="px-5 py-4">{{ $entry->description }}</td>
                            <td class="px-5 py-4 capitalize">{{ $entry->source_type }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right {{ $entry->direction === 'income' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                                {{ $entry->direction === 'income' ? '+' : '−' }}{{ number_format((float) $entry->amount, 2) }} {{ $entry->currency }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @if($entry->reconciliation_id)
                                    <form method="POST" action="{{ route('accountant.ledger.unreconcile', [$entry->source_type, $entry->source_id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <span class="text-xs text-green-700 dark:text-green-300" title="{{ $entry->reconciliation_note }}">
                                            Matched {{ $entry->statement_date ? \Illuminate\Support\Carbon::parse($entry->statement_date)->format('M d, Y') : '' }}
                                            @if($entry->bank_reference) · {{ $entry->bank_reference }}@endif
                                        </span>
                                        <button class="ml-2 text-xs text-gray-500 hover:underline">Undo</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('accountant.ledger.reconcile') }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="source_type" value="{{ $entry->source_type }}">
                                        <input type="hidden" name="source_id" value="{{ $entry->source_id }}">
                                        <input type="date" name="statement_date" required value="{{ now()->toDateString() }}" aria-label="Statement date" class="rounded border-gray-300 text-xs dark:border-gray-600 dark:bg-gray-700">
                                        <input type="number" name="statement_amount" required min="0.01" step="0.01" value="{{ number_format((float) $entry->amount, 2, '.', '') }}" aria-label="Statement amount" class="w-24 rounded border-gray-300 text-xs dark:border-gray-600 dark:bg-gray-700">
                                        <input type="hidden" name="statement_currency" value="{{ $entry->currency }}">
                                        <span class="text-xs text-gray-500">{{ $entry->currency }}</span>
                                        <input type="text" name="bank_reference" maxlength="100" placeholder="Bank reference" aria-label="Bank reference" class="w-28 rounded border-gray-300 text-xs dark:border-gray-600 dark:bg-gray-700">
                                        <button class="text-violet-700 hover:underline dark:text-violet-300">Reconcile</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No posted finance transactions were found for this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">{{ $entries->links() }}</div>
        </div>
    </div>
</x-layouts.app>