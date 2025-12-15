@php
    $hasServerData = isset($value);
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
                console.log('[GaugeChart] init payload:', payload);
                if (!window.GaugeChart) {
                    console.warn('[GaugeChart] GaugeChart helper not found. Is resources/js/charts/gauge.js imported?');
                }
                this.$nextTick(() => {
                    try {
                        // Destroy any existing Chart.js instance for this canvas (just in case)
                        const existing = window.Chart && typeof window.Chart.getChart === 'function'
                            ? window.Chart.getChart(canvas)
                            : null;
                        if (existing) {
                            existing.destroy();
                            console.log('[GaugeChart] existing Chart.js instance destroyed (registry)');
                        }
                        // Also destroy local instance if present
                        if (this.chart && window.ChartHelpers) {
                            window.ChartHelpers.destroyChart(this.chart);
                            this.chart = null;
                            console.log('[GaugeChart] previous chart destroyed before re-create');
                        }
                        if (window.GaugeChart && payload) {
                            this.chart = window.GaugeChart.create(canvas, payload);
                            console.log('[GaugeChart] chart created');
                        } else {
                            console.warn('[GaugeChart] Skipping chart creation (helper or payload missing).');
                        }
                    } catch (e) {
                        console.error('[GaugeChart] error creating chart:', e);
                    }
                });
                this.$watch('$store.darkMode.on', (v) => {
                    const payload2 = @js($this->payload);
                    console.log('[GaugeChart] dark mode changed:', v);
                    if (this.chart && window.GaugeChart) window.GaugeChart.update(this.chart, payload2);
                });
            },
            destroy() {
                if (this.chart && window.ChartHelpers) {
                    window.ChartHelpers.destroyChart(this.chart);
                    console.log('[GaugeChart] chart destroyed');
                }
                this.chart = null;
            }
        }" x-init="init()" x-on:turbo:before-cache.window="destroy()" class="w-full h-full">
            <canvas x-ref="canvas" id="{{ $chartId }}" class="w-full h-full"></canvas>
        </div>
    </div>
</div>
