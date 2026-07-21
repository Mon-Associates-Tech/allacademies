<x-bookshop::layouts.staff :title="'Branches - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Branches</h1>
        <a href="{{ route('bookshop.staff.branches.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
           style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
            + New Branch
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Name</th>
                    <th class="text-left px-5 py-3">Code</th>
                    <th class="text-left px-5 py-3">Location</th>
                    <th class="text-left px-5 py-3">Staff</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $branch->name }}</td>
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $branch->code }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $branch->city }}, {{ $branch->region }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $branch->staff_count }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">
                                {{ $branch->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <a href="{{ route('bookshop.staff.branches.edit', $branch) }}" class="text-purple-600 dark:text-purple-400 font-medium">Edit</a>
                            <a href="{{ route('bookshop.staff.branches.payment.edit', $branch) }}" class="text-purple-600 dark:text-purple-400 font-medium">Payment</a>
                            <form method="POST" action="{{ route('bookshop.staff.branches.toggle-active', $branch) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $branch->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No branches yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($branches->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $branches->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
