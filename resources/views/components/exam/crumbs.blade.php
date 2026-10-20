@props(['items' => []])

<style>
    .crumbs-nav::-webkit-scrollbar { display: none; }
    .crumbs-nav { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="mb-6 flex items-center gap-2"
     x-data="{
         nav: null,
         scroll: 0,
         maxScroll: 0,
         get canScrollLeft() { return this.scroll > 0 },
         get canScrollRight() { return this.scroll < this.maxScroll },
         scrollLeft() { this.scrollBy(-200) },
         scrollRight() { this.scrollBy(200) },
         scrollBy(amount) {
             this.nav.scrollBy({ left: amount, behavior: 'smooth' });
             this.$nextTick(() => { this.checkScroll() });
         },
         checkScroll() {
             if (!this.nav) return;
             this.scroll = this.nav.scrollLeft;
             this.maxScroll = this.nav.scrollWidth - this.nav.clientWidth;
         }
     }"
     x-init="nav = $refs.nav; $nextTick(() => { checkScroll() })">
    <button x-show="canScrollLeft" @click="scrollLeft" x-cloak
            class="shrink-0 rounded-full p-1 text-muted transition hover:text-ink hover:bg-surface-2">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <div class="flex min-w-0 overflow-hidden">
        <nav x-ref="nav" @scroll="checkScroll"
             {{ $attributes->merge(['class' => 'crumbs-nav flex flex-nowrap items-center gap-2 overflow-x-auto font-mono text-[10.5px] tracking-[0.14em] text-muted']) }}
             aria-label="Breadcrumb">
            @foreach($items as $crumb)
                @if(!$loop->first)
                    <svg class="h-4 w-4 shrink-0 text-ink-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
                @if(!empty($crumb['href']))
                    <a href="{{ $crumb['href'] }}" class="whitespace-nowrap transition hover:text-ink">{{ strtoupper($crumb['label']) }}</a>
                @else
                    <span class="whitespace-nowrap text-ink-soft">{{ strtoupper($crumb['label']) }}</span>
                @endif
            @endforeach
        </nav>
    </div>

    <button x-show="canScrollRight" @click="scrollRight" x-cloak
            class="shrink-0 rounded-full p-1 text-muted transition hover:text-ink hover:bg-surface-2">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>
