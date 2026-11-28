<x-layouts.app pageName="Expenses">
    <x-slot name="title">Expense Register</x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm text-gray-600 dark:text-gray-300">Record school expenses and mark them paid when disbursed. Paid entries appear in the general ledger.</p>
            <a href="{{ route('accountant.ledger.index') }}" class="text-sm font-medium text-violet-700 hover:underline dark:text-violet-300">View general ledger</a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('accountant.expenses.store') }}" class="grid grid-cols-1 gap-4 rounded-lg bg-white p-6 shadow dark:bg-gray-800 md:grid-cols-2 lg:grid-cols-4">
            @csrf
            <div>
                <label for="category" class="mb-1 block text-sm font-medium">Category</label>
                <input id="category" name="category" required maxlength="100" value="{{ old('category') }}" placeholder="Utilities, supplies…" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div class="md:col-span-2">
                <label for="description" class="mb-1 block text-sm font-medium">Description</label>
                <input id="description" name="description" required maxlength="255" value="{{ old('description') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="reference" class="mb-1 block text-sm font-medium">Reference</label>
                <input id="reference" name="reference" maxlength="100" value="{{ old('reference') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="amount" class="mb-1 block text-sm font-medium">Amount</label>
                <input id="amount" name="amount" type="number" min="0.01" step="0.01" required value="{{ old('amount') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="currency" class="mb-1 block text-sm font-medium">Currency</label>
                <input id="currency" name="currency" minlength="3" maxlength="3" required value="{{ old('currency', 'GHS') }}" class="w-full rounded-lg border-gray-300 uppercase dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="expense_date" class="mb-1 block text-sm font-medium">Expense date</label>
                <input id="expense_date" name="expense_date" type="date" required value="{{ old('expense_date', now()->toDateString()) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-medium">Payment status</label>
                <select id="status" name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                    <option value="paid" @selected(old('status') === 'paid')>Paid</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="rounded-lg bg-violet-600 px-4 py-2 font-medium text-white hover:bg-violet-700">Record expense</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                    <tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Description</th><th class="px-5 py-3">Reference</th><th class="px-5 py-3 text-right">Amount</th><th class="px-5 py-3">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4">{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td class="px-5 py-4">{{ $expense->category }}</td>
                            <td class="px-5 py-4">{{ $expense->description }}</td>
                            <td class="px-5 py-4">{{ $expense->reference ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">{{ number_format((float) $expense->amount, 2) }} {{ $expense->currency }}</td>
                            <td class="px-5 py-4">
                                @if($expense->status === 'pending')
                                    <form method="POST" action="{{ route('accountant.expenses.paid', $expense) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-amber-700 hover:underline dark:text-amber-300">Pending · mark paid</button>
                                    </form>
                                @else
                                    <span class="text-green-700 dark:text-green-300">Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No expenses recorded.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">{{ $expenses->links() }}</div>
        </div>
    </div>
</x-layouts.app>