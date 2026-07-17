<x-bookshop::layouts.staff :title="'Books - BookShop'">
    @php($staff = auth('bookshop_staff')->user())

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Catalog</h1>
        @if($staff->isSuperAdmin())
            <a href="{{ route('bookshop.staff.books.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
               style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                + New Book
            </a>
        @endif
    </div>

    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..."
               class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
        <select name="category_id" class="px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (int) request('category_id') === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Filter
        </button>
    </form>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Title</th>
                    <th class="text-left px-5 py-3">Author</th>
                    <th class="text-left px-5 py-3">Category</th>
                    <th class="text-left px-5 py-3">Price</th>
                    <th class="text-left px-5 py-3">Total Stock</th>
                    <th class="text-left px-5 py-3">Status</th>
                    @if($staff->isSuperAdmin())
                        <th class="text-right px-5 py-3">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $book->title }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->author ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($book->price, 2) }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $book->total_stock ?? 0 }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">
                                {{ $book->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-right space-x-3">
                                <a href="{{ route('bookshop.staff.books.edit', $book) }}" class="text-purple-600 dark:text-purple-400 font-medium">Edit</a>
                                <form method="POST" action="{{ route('bookshop.staff.books.toggle-active', $book) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-slate-500 dark:text-slate-400 font-medium">
                                        {{ $book->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No books found.</td></tr>
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
