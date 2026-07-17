<x-bookshop::layouts.staff :title="'Categories - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Categories</h1>

    <div class="bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.categories.store') }}" class="flex gap-3 mb-6">
            @csrf
            <input type="text" name="name" placeholder="New category name" required
                   class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white transition-all"
                    style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                Add
            </button>
        </form>

        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Books</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-right py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="py-3">
                            <form method="POST" action="{{ route('bookshop.staff.categories.update', $category) }}" class="flex items-center gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}"
                                       class="px-2 py-1 text-sm border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-transparent dark:text-white"
                                       style="border-radius: 2px;">
                                <button type="submit" class="text-xs text-purple-600 dark:text-purple-400">Save</button>
                            </form>
                        </td>
                        <td class="py-3 text-slate-600 dark:text-slate-400">{{ $category->books_count }}</td>
                        <td class="py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-3 text-right space-x-3">
                            <form method="POST" action="{{ route('bookshop.staff.categories.toggle-active', $category) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('bookshop.staff.categories.destroy', $category) }}" class="inline"
                                  onsubmit="return confirm('Delete this category?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-500 dark:text-slate-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
            <div class="mt-4">{{ $categories->links() }}</div>
        @endif
    </div>
</x-bookshop::layouts.staff>
