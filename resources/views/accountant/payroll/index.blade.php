<x-layouts.app pageName="Payroll">
    <x-slot name="title">Payroll</x-slot>

    <div class="space-y-6">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
            Payroll runs snapshot active teachers' salary amounts as draft base pay. Confirm the pay period and amounts before approval; this page does not calculate tax or send payments.
        </div>

        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Create draft payroll run</h2>
            <form method="POST" action="{{ route('accountant.payroll.store') }}" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                @csrf
                <div>
                    <label for="period_start" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Period start</label>
                    <input id="period_start" type="date" name="period_start" required value="{{ old('period_start', now()->startOfMonth()->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="period_end" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Period end</label>
                    <input id="period_end" type="date" name="period_end" required value="{{ old('period_end', now()->endOfMonth()->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="rounded-lg bg-violet-600 px-4 py-2 font-medium text-white hover:bg-violet-700">Create draft</button>
                </div>
            </form>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Currency is GHS. One run per school may cover a given date range; overlapping runs are blocked.</p>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($runs as $run)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $run->period_start->format('M d, Y') }} – {{ $run->period_end->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $run->items_count }}</td>
                                <td class="px-6 py-4 text-sm capitalize text-gray-700 dark:text-gray-300">{{ $run->status }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $run->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm"><a href="{{ route('accountant.payroll.show', $run) }}" class="font-medium text-violet-600 hover:text-violet-800 dark:text-violet-400">Review</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No payroll runs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $runs->links() }}</div>
        </div>
    </div>
</x-layouts.app>