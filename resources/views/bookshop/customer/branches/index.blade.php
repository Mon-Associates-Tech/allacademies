<x-bookshop::layouts.customer :title="'Choose a Branch - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Choose a Branch</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">
        Stock and pricing are branch-specific. Switching clears your cart if you have one in progress.
        @if($homeBranch)
            Your home region's branch is <strong>{{ $homeBranch->name }}</strong>.
        @endif
    </p>

    <div class="space-y-3">
        @forelse($branches as $branch)
            <div class="bg-white dark:bg-slate-900 p-4 flex items-center justify-between" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ $branch->name }}
                        @if($current && $current->id === $branch->id)
                            <span class="text-[10px] font-semibold px-2 py-0.5 border text-purple-700 bg-purple-50 border-purple-200 dark:text-purple-300 dark:bg-purple-900/30 dark:border-purple-800 ml-1" style="border-radius: 2px;">Current</span>
                        @endif
                        @if($homeBranch && $homeBranch->id === $branch->id)
                            <span class="text-[10px] font-semibold px-2 py-0.5 border text-slate-500 bg-slate-50 border-slate-200 dark:text-slate-400 dark:bg-slate-800 dark:border-slate-700 ml-1" style="border-radius: 2px;">Home</span>
                        @endif
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $branch->city }}, {{ $branch->region }}</p>
                </div>
                @if(! $current || $current->id !== $branch->id)
                    <form method="POST" action="{{ route('bookshop.shop.branches.switch', $branch) }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-4 py-2 text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                            Shop Here
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No active branches yet.</p>
        @endforelse
    </div>
</x-bookshop::layouts.customer>
