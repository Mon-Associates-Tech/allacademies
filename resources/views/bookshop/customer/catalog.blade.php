<x-bookshop::layouts.customer :title="'Catalog - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Catalog</h1>

    @if(! $branch)
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
            No branch currently serves your region ({{ $customer->region ?: 'not set' }}). You can still browse, but you won't
            be able to place an order until a branch is added near you.
        </div>
    @else
        <p class="text-sm text-slate-500 dark:text-slate-400">Orders from this page are served by <strong>{{ $branch->name }}</strong> ({{ $branch->city }}).</p>
    @endif

    <form method="POST" action="{{ route('bookshop.shop.orders.store') }}">
        @csrf

        <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="text-left px-5 py-3">Title</th>
                        <th class="text-left px-5 py-3">Author</th>
                        <th class="text-left px-5 py-3">Price</th>
                        <th class="text-left px-5 py-3">Available</th>
                        <th class="text-left px-5 py-3">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        @php($available = $book->branch_stock ?? 0)
                        <tr class="border-t border-slate-100 dark:border-slate-800">
                            <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $book->title }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->author ?? '—' }}</td>
                            <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($book->price, 2) }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $branch ? $available : '—' }}</td>
                            <td class="px-5 py-3">
                                <input type="number" name="quantities[{{ $book->id }}]" min="0" max="{{ $available }}" value="0"
                                       {{ (! $branch || $available < 1) ? 'disabled' : '' }}
                                       class="w-20 px-2 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white disabled:opacity-40"
                                       style="border-radius: 2px;">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No books in the catalog yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
            <div class="bg-white dark:bg-slate-900 px-5 py-4 mt-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                {{ $books->links() }}
            </div>
        @endif

        @if($branch)
            <div class="mt-6 bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Order Notes (optional)</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;"></textarea>

                <button type="submit" class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    Place Order
                </button>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Set a quantity above for each book you'd like, then place the order — everything with a quantity greater than 0 is included in a single order.</p>
            </div>
        @endif
    </form>
</x-bookshop::layouts.customer>
