<x-bookshop::layouts.guest :heading="'Almost there'" :subheading="'Your account needs a branch assignment'">
    <div class="text-center">
        <div class="w-16 h-16 mx-auto flex items-center justify-center mb-4"
             style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <p class="text-slate-900 dark:text-white font-semibold">Hi {{ $staff->name }},</p>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            You're signed in, but a super admin hasn't assigned you to a branch yet.
            You won't be able to access the dashboard until that happens — check
            back shortly, or reach out to your super admin directly.
        </p>

        <form method="POST" action="{{ route('bookshop.staff.logout') }}" class="mt-6">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-all"
                    style="border-radius: 2px;">
                Sign Out
            </button>
        </form>
    </div>
</x-bookshop::layouts.guest>
