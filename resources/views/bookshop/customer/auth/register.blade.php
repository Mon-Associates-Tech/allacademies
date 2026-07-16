<x-bookshop::layouts.guest :heading="'Create your account'" :subheading="'Order physical books from your nearest branch'">
    @if($errors->any())
        <div class="mb-5 px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300" style="border-radius: 2px;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('bookshop.shop.register') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        {{-- Reuses the project's existing location-selector component so
             region/city map identically onto how orders get resolved to a
             branch later (Phase 4). --}}
        <x-location-selector
            id-prefix="bookshop-register"
            :country-value="old('country', '')"
            :country-code-value="old('country_code', '')"
            :region-value="old('region', '')"
            :city-value="old('city', '')"
            :required="true"
        />

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Address</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white transition-all"
                   style="border-radius: 2px;">
        </div>

        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
            Create Account
        </button>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <a href="{{ route('bookshop.shop.login') }}" class="text-purple-600 dark:text-purple-400 font-medium">Sign in</a>
        </p>
    </form>
</x-bookshop::layouts.guest>
