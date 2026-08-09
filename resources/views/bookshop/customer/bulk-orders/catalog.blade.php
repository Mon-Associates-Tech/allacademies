<x-bookshop::layouts.customer :title="'Bulk Order - BookShop'">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Bulk Order</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Ordering a class set or large quantity for a school, church, or organization? Build your request here.</p>
        </div>
        @if(! empty($builderItems))
            <a href="{{ route('bookshop.shop.bulk-orders.review') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
               style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                Review Request ({{ array_sum($builderItems) }} {{ array_sum($builderItems) === 1 ? 'copy' : 'copies' }})
            </a>
        @endif
    </div>

    @if(! $branch)
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
            <a href="{{ route('bookshop.shop.branches.index') }}" class="underline font-medium">Choose a branch</a> before adding items to your bulk request.
        </div>
    @endif

    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or author..."
               class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
        <select name="category_id" class="px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (int) request('category_id') === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white whitespace-nowrap" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Filter
        </button>
    </form>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Title</th>
                    <th class="text-left px-5 py-3">Category</th>
                    <th class="text-left px-5 py-3">Catalog Price</th>
                    <th class="text-right px-5 py-3">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $book->title }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $book->author }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($book->price, 2) }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($branch)
                                <form method="POST" action="{{ route('bookshop.shop.bulk-orders.add') }}" class="inline-flex items-center gap-2 justify-end">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <input type="number" name="quantity" min="1" value="10" class="w-20 px-2 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800" style="border-radius: 2px;">
                                        Add
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No books match your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($books->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $books->links() }}
        </div>
    @endif
</x-bookshop::layouts.customer>
