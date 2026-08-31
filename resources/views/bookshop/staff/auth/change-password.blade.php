<x-bookshop::layouts.guest :heading="'Set a New Password'" :subheading="'Required before you can continue'">
    @if($errors->any())
        <div class="mb-5 px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            {{ $errors->first() }}
        </div>
    @endif

    <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">
        You're using a temporary password. Set your own before continuing.
    </p>

    <form method="POST" action="{{ route('bookshop.staff.password.update') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">New Password</label>
            <input type="password" name="password" required autofocus
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Confirm New Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
            Set Password
        </button>
    </form>

    <form method="POST" action="{{ route('bookshop.staff.logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="w-full text-center text-xs text-slate-500 dark:text-slate-400">Sign out instead</button>
    </form>
</x-bookshop::layouts.guest>
