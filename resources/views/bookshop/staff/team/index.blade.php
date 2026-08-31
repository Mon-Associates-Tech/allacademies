<x-bookshop::layouts.staff :title="'Team - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Team</h1>
        <a href="{{ route('bookshop.staff.team.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
           style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
            + Add Staff Member
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Name</th>
                    <th class="text-left px-5 py-3">Email</th>
                    <th class="text-left px-5 py-3">Role</th>
                    <th class="text-left px-5 py-3">Branch</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffMembers as $staffMember)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $staffMember->name }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $staffMember->email }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $staffMember->role->label() }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">
                            {{ $staffMember->branch?->name ?? ($staffMember->isSuperAdmin() ? '—' : 'Unassigned') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">
                                {{ $staffMember->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <a href="{{ route('bookshop.staff.team.edit', $staffMember) }}" class="text-purple-600 dark:text-purple-400 font-medium">Edit</a>
                            <form method="POST" action="{{ route('bookshop.staff.team.toggle-active', $staffMember) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $staffMember->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No staff members yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($staffMembers->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $staffMembers->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
