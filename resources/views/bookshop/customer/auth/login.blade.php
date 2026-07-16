<x-bookshop::layouts.guest :heading="'Welcome back'" :subheading="'Sign in to your BookShop account'">
    @if($errors->any())
        <div class="mb-5 px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('bookshop.shop.login') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" style="border-radius: 2px;">
            Remember me
        </label>

        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
            Sign In
        </button>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Don't have an account?
            <a href="{{ route('bookshop.shop.register') }}" class="text-purple-600 dark:text-purple-400 font-medium">Create one</a>
        </p>
    </form>
</x-bookshop::layouts.guest>
