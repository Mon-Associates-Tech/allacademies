@props(['staffMember' => null, 'branches'])

<div class="space-y-5 max-w-2xl">
    <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Full Name</label>
        <input type="text" name="name" value="{{ old('name', $staffMember?->name) }}" required
               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Email</label>
            <input type="email" name="email" value="{{ old('email', $staffMember?->email) }}" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $staffMember?->phone) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Role</label>
            <select name="role" id="team-form-role" required
                    class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="admin" {{ old('role', $staffMember?->role?->value) === 'admin' ? 'selected' : '' }}>Branch Admin</option>
                <option value="superadmin" {{ old('role', $staffMember?->role?->value) === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
            </select>
        </div>
        <div id="team-form-branch-wrap">
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Branch</label>
            <select name="branch_id" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">Unassigned (lands on the branch-pending screen)</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (int) old('branch_id', $staffMember?->branch_id) === $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
            {{ $staffMember ? 'New Password (leave blank to keep current)' : 'Password' }}
        </label>
        <input type="password" name="password" {{ $staffMember ? '' : 'required' }}
               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            @if($staffMember)
                Leave blank to keep their current password.
            @else
                Share this with them directly — there's no invite email yet, so they'll need it from you to log in the first time.
            @endif
        </p>
    </div>

    <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
        {{ $staffMember ? 'Save Changes' : 'Create Account' }}
    </button>
</div>

<script>
    (function () {
        const roleSelect = document.getElementById('team-form-role');
        const branchWrap = document.getElementById('team-form-branch-wrap');
        if (!roleSelect || !branchWrap) return;

        const sync = () => {
            const isSuperAdmin = roleSelect.value === 'superadmin';
            branchWrap.style.opacity = isSuperAdmin ? '0.4' : '1';
            branchWrap.querySelector('select').disabled = isSuperAdmin;
        };

        roleSelect.addEventListener('change', sync);
        sync();
    })();
</script>
