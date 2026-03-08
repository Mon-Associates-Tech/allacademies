@php
    $hasServerData = !empty($labels ?? []) && !empty($values ?? []);
@endphp
<div class="{{ $widthClass }}">
    <div class="relative {{ $heightClass }}">
        @unless($hasServerData)
            <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50/60 dark:bg-gray-900/30 rounded-md z-0">
                No data to display for the selected range
            </div>
        @endunless

        <div wire:ignore x-data="{
            chart: null,
            init() {
                const canvas = this.$refs.canvas;
                const payload = @js($this->payload);
                console.log('[PieChart] init payload:', payload);
                // Retry helper: wait briefly for ChartHelpers to be attached to window (race protection)
                const tryCreate = (attempt = 0) => {
                    if (!window.ChartHelpers) {
                        if (attempt === 0) console.warn('[PieChart] ChartHelpers not found. Waiting for bundle to attach...');
                        if (attempt < 10) {
                            return setTimeout(() => tryCreate(attempt + 1), 100);
                        } else {
                            console.warn('[PieChart] ChartHelpers still missing after retries. Is resources/js/charts/helpers.js imported and built?');
                            return;
                        }
                    }
                    this.$nextTick(() => {
                        try {
                            // Destroy any existing Chart.js instance on this canvas via registry to prevent reuse errors
                            const existing = window.Chart && typeof window.Chart.getChart === 'function'
                                ? window.Chart.getChart(canvas)
                                : null;
                            if (existing) {
                                existing.destroy();
                                console.log('[PieChart] existing Chart.js instance destroyed (registry)');
                            }
                            // Also destroy local instance if present
                            if (this.chart && window.ChartHelpers) {
                                window.ChartHelpers.destroyChart(this.chart);
                                this.chart = null;
                                console.log('[PieChart] previous chart destroyed before re-create');
                            }
                            if (window.ChartHelpers && payload?.data) {
                                this.chart = window.ChartHelpers.createChart(canvas, payload.type, payload.data, payload.options);
                                console.log('[PieChart] chart created:', this.chart?.config?.type);
                            } else {
                                console.warn('[PieChart] Skipping chart creation (helpers or data missing).');
                            }
                        } catch (e) {
                            console.error('[PieChart] error creating chart:', e);
                        }
                    });
                };
                tryCreate();
                this.$watch('$store.darkMode.on', (v) => {
                    const payload2 = @js($this->payload);
                    console.log('[PieChart] dark mode changed:', v);
                    if (this.chart && window.ChartHelpers) window.ChartHelpers.updateChart(this.chart, payload2.data, payload2.options);
                });
            },
            destroy() {
                if (this.chart && window.ChartHelpers) {
                    window.ChartHelpers.destroyChart(this.chart);
                    console.log('[PieChart] chart destroyed');
                }
                this.chart = null;
            }
        }" x-init="init()" x-on:turbo:before-cache.window="destroy()" class="w-full h-full">
            <canvas x-ref="canvas" id="{{ $chartId }}" class="w-full h-full"></canvas>
        </div>
    </div>
</div>
