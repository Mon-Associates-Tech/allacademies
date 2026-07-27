<x-bookshop::layouts.staff :title="'Books - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Catalog</h1>
        <div class="flex items-center gap-3">
            @if(! $staff->isSuperAdmin())
                <a href="{{ route('bookshop.staff.restock-requests.create') }}" class="text-sm text-purple-600 dark:text-purple-400 underline">
                    Go to Request Form &rarr;
                </a>
            @endif
            @if($staff->isSuperAdmin())
                <a href="{{ route('bookshop.staff.books.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                   style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    + New Book
                </a>
            @endif
        </div>
    </div>

    <form method="GET" class="space-y-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..."
                   class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
            <select name="category_id" class="px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (int) request('category_id') === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}" {{ $sortKey === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white whitespace-nowrap" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
                Filter
            </button>
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="warehouse_out_only" value="1" onchange="this.form.submit()" {{ request('warehouse_out_only') ? 'checked' : '' }} style="border-radius: 2px;">
            Show only books out of stock in the warehouse
        </label>
    </form>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Title</th>
                    <th class="text-left px-5 py-3">Author</th>
                    <th class="text-left px-5 py-3">Category</th>
                    <th class="text-left px-5 py-3">Price</th>
                    @if($staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Total Stock</th>
                    @endif
                    <th class="text-left px-5 py-3">Warehouse</th>
                    @if(! $staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Your Branch</th>
                    @endif
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">
                            <div class="flex items-center gap-3">
                                @if($book->hasCover())
                                    <img src="{{ $book->coverUrl() }}" alt="" class="w-8 h-11 object-cover flex-shrink-0" style="border-radius: 2px;">
                                @else
                                    <div class="w-8 h-11 flex-shrink-0 flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 text-[9px]" style="border-radius: 2px;">N/A</div>
                                @endif
                                {{ $book->title }}
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->author ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($book->price, 2) }}</td>
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->total_stock ?? 0 }}</td>
                        @endif
                        <td class="px-5 py-3">
                            @if($book->warehouse_quantity > 0)
                                <span class="font-mono text-emerald-600 dark:text-emerald-400">{{ $book->warehouse_quantity }}</span>
                            @else
                                <span class="text-xs font-semibold px-2 py-0.5 border text-amber-800 bg-amber-50 border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">Out of stock</span>
                            @endif
                        </td>
                        @if(! $staff->isSuperAdmin())
                            <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $book->branch_quantity ?? 0 }}</td>
                        @endif
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">
                                {{ $book->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3 whitespace-nowrap">
                            @if($staff->isSuperAdmin())
                                <a href="{{ route('bookshop.staff.books.edit', $book) }}" class="text-purple-600 dark:text-purple-400 font-medium">Edit</a>
                                <form method="POST" action="{{ route('bookshop.staff.books.toggle-active', $book) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-slate-500 dark:text-slate-400 font-medium">
                                        {{ $book->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('bookshop.staff.restock-requests.create', ['book_id' => $book->id, 'book_title' => $book->title]) }}"
                                   class="text-purple-600 dark:text-purple-400 font-medium">
                                    Request Stock
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No books found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($books->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $books->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
