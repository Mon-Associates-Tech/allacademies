@php
    $hasServerData = !empty($labels ?? []) && !empty($datasets ?? []);
@endphp
<div class="{{ $widthClass }}">
    <div class="relative {{ $heightClass }}">
        <!-- Placeholder when there is no server-side data -->
        @unless($hasServerData)
            <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50/60 dark:bg-gray-900/30 rounded-md z-0">
                No data to display for the selected range
            </div>
        @endunless

        <div wire:ignore x-data="{
            chart: null,
            created: false,
            init() {
                const canvas = this.$refs.canvas;
                const payload = @js($this->payload);
                console.log('[BarChart] init payload:', payload);
                // Retry helper: wait briefly for ChartHelpers to be attached to window (race protection)
                const tryCreate = (attempt = 0) => {
                    if (!window.ChartHelpers) {
                        if (attempt === 0) console.warn('[BarChart] ChartHelpers not found. Waiting for bundle to attach...');
                        if (attempt < 10) {
                            return setTimeout(() => tryCreate(attempt + 1), 100);
                        } else {
                            console.warn('[BarChart] ChartHelpers still missing after retries. Is resources/js/charts/helpers.js imported and built?');
                            return;
                        }
                    }
                    this.$nextTick(() => {
                        try {
                            // Also destroy any stray Chart.js instance bound to this canvas (registry-based)
                            const existing = window.Chart && typeof window.Chart.getChart === 'function'
                                ? window.Chart.getChart(canvas)
                                : null;
                            if (existing) {
                                existing.destroy();
                                console.log('[BarChart] existing Chart.js instance destroyed (registry)');
                            }
                            // If a chart instance already exists on this canvas, destroy it first to avoid reuse errors
                            if (this.chart && window.ChartHelpers) {
                                window.ChartHelpers.destroyChart(this.chart);
                                this.chart = null;
                                console.log('[BarChart] previous chart destroyed before re-create');
                            }
                            if (window.ChartHelpers && payload?.data) {
                                this.chart = window.ChartHelpers.createChart(canvas, payload.type, payload.data, payload.options);
                                this.created = true;
                                console.log('[BarChart] chart created:', this.chart?.config?.type);
                            } else {
                                console.warn('[BarChart] Skipping chart creation (helpers or data missing).');
                            }
                        } catch (e) {
                            console.error('[BarChart] error creating chart:', e);
                        }
                    });
                };
                tryCreate();
                this.$watch('$store.darkMode.on', (v) => {
                    const payload2 = @js($this->payload);
                    console.log('[BarChart] dark mode changed:', v);
                    if (this.chart && window.ChartHelpers) window.ChartHelpers.updateChart(this.chart, payload2.data, payload2.options);
                });
            },
            destroy() {
                if (this.chart && window.ChartHelpers) {
                    window.ChartHelpers.destroyChart(this.chart);
                    console.log('[BarChart] chart destroyed');
                }
                this.chart = null;
            }
        }" x-init="init()" x-on:turbo:before-cache.window="destroy()" class="w-full h-full">
            <canvas x-ref="canvas" id="{{ $chartId }}" class="w-full h-full"></canvas>
        </div>
    </div>
</div>
