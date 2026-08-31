@props(['branch' => null])

<div class="space-y-5 max-w-2xl">
    <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Branch Name</label>
        <input type="text" name="name" value="{{ old('name', $branch?->name) }}" required
               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
    </div>

    <x-bookshop::location-fields
        id-prefix="branch-form"
        :country-value="old('country', $branch?->country ?? '')"
        :country-code-value="old('country_code', $branch?->country_code ?? '')"
        :region-value="old('region', $branch?->region ?? '')"
        :city-value="old('city', $branch?->city ?? '')"
        :required="true"
    />

    <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Address</label>
        <input type="text" name="address" value="{{ old('address', $branch?->address) }}"
               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $branch?->phone) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Email</label>
            <input type="email" name="email" value="{{ old('email', $branch?->email) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Latitude (optional)</label>
            <input type="text" name="latitude" value="{{ old('latitude', $branch?->latitude) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Longitude (optional)</label>
            <input type="text" name="longitude" value="{{ old('longitude', $branch?->longitude) }}"
                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
                   style="border-radius: 2px;">
        </div>
    </div>

    <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
        {{ $branch ? 'Save Changes' : 'Create Branch' }}
    </button>
</div>
