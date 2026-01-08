<?php

namespace App\Traits;

/**
 * FormatsChartData
 *
 * Helper trait to normalize server-side chart data structures for Chart.js.
 * Intended for use in Controllers or Livewire components before passing to views.
 */
trait FormatsChartData
{
    /**
     * Build a Chart.js-ready payload for Bar (or Line) charts.
     *
     * @param array $labels   Array of x-axis labels
     * @param array $series   Array of datasets, each as ['label' => string, 'data' => array, 'backgroundColor' => string|array, 'borderColor' => string|array]
     * @param array $options  Additional Chart.js options
     * @return array{type:string,data:array,options:array}
     */
    public function formatBarData(array $labels, array $series, array $options = []): array
    {
        $datasets = [];

        foreach ($series as $s) {
            $datasets[] = [
                'label' => $s['label'] ?? 'Series',
                'data' => $this->ensureNumericArray($s['data'] ?? []),
                'backgroundColor' => $s['backgroundColor'] ?? $this->fallbackColors(),
                'borderColor' => $s['borderColor'] ?? 'rgba(0,0,0,0.1)',
                'borderWidth' => $s['borderWidth'] ?? 1,
                'stack' => $s['stack'] ?? null,
            ];
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => array_values($labels),
                'datasets' => $datasets,
            ],
            'options' => $this->mergeDefaultCartesianOptions($options),
        ];
    }

    /**
     * Build a Chart.js-ready payload for Pie (or Doughnut) charts.
     *
     * @param array $labels
     * @param array $values  Numbers aligned by $labels
     * @param array $options
     * @return array{type:string,data:array,options:array}
     */
    public function formatPieData(array $labels, array $values, array $options = []): array
    {
        return [
            'type' => 'pie',
            'data' => [
                'labels' => array_values($labels),
                'datasets' => [[
                    'label' => $options['datasetLabel'] ?? 'Distribution',
                    'data' => $this->ensureNumericArray($values),
                    'backgroundColor' => $options['backgroundColor'] ?? $this->fallbackColors(count($labels)),
                    'borderColor' => $options['borderColor'] ?? 'rgba(255,255,255,1)',
                    'borderWidth' => $options['borderWidth'] ?? 1,
                ]],
            ],
            'options' => $this->mergeDefaultPolarOptions($options),
        ];
    }

    /**
     * Build a Chart.js-ready payload for a Gauge using a Doughnut under the hood.
     *
     * @param float|int $value
     * @param float|int $min
     * @param float|int $max
     * @param array $thresholds Array of bands like [['max' => 50, 'color' => '#ef4444'], ...]
     * @param array $options
     * @return array{type:string,data:array,options:array}
     */
    public function formatGaugeData($value, $min = 0, $max = 100, array $thresholds = [], array $options = []): array
    {
        $min = (float)$min;
        $max = (float)$max;
        if ($max <= $min) { $max = $min + 1; }

        $val = (float)$value;
        if ($val < $min) $val = $min;
        if ($val > $max) $val = $max;

        $range = $max - $min;
        $progress = $range > 0 ? ($val - $min) / $range : 0;

        // If thresholds are provided, derive segments from them; otherwise simple [progress, remainder]
        $labels = [];
        $data = [];
        $colors = [];

        if (!empty($thresholds)) {
            $prev = $min;
            foreach ($thresholds as $t) {
                $bandMax = isset($t['max']) ? (float)$t['max'] : $max;
                if ($bandMax <= $prev) continue;
                $labels[] = ($t['label'] ?? (string)$bandMax);
                $segment = max(0, min($bandMax, $val) - $prev);
                $data[] = $segment;
                $colors[] = $t['color'] ?? '#9ca3af';
                $prev = $bandMax;
            }
            // Remainder (unfilled part) to close the semicircle
            $remainder = max(0, $max - $val);
            if ($remainder > 0) {
                $labels[] = 'remainder';
                $data[] = $remainder;
                $colors[] = $options['remainderColor'] ?? 'rgba(0,0,0,0.08)';
            }
        } else {
            $labels = ['value', 'remainder'];
            $data = [$val - $min, $max - $val];
            $colors = [$options['valueColor'] ?? '#10b981', $options['remainderColor'] ?? 'rgba(0,0,0,0.08)'];
        }

        $chartOptions = array_merge([
            'rotation' => -1 * M_PI, // start at 180deg
            'circumference' => M_PI, // 180deg arc
            'cutout' => '70%',
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => ['enabled' => false],
            ],
        ], $options['chart'] ?? []);

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ]],
            ],
            'options' => $chartOptions,
            'meta' => [
                'value' => $val,
                'min' => $min,
                'max' => $max,
                'progress' => $progress,
                'centerLabel' => $options['centerLabel'] ?? null,
                'showValue' => $options['showValue'] ?? true,
            ],
        ];
    }

    // ----------------- helpers -----------------

    private function ensureNumericArray(array $values): array
    {
        return array_map(static function ($v) {
            return is_numeric($v) ? 0 + $v : null;
        }, $values);
    }

    private function fallbackColors(?int $n = null): array
    {
        $palette = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f43f5e', '#6366f1', '#14b8a6'
        ];
        if ($n === null) return $palette;
        // repeat palette to reach N
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            $out[] = $palette[$i % count($palette)];
        }
        return $out;
    }

    private function mergeDefaultCartesianOptions(array $options): array
    {
        $defaults = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'top'],
                'tooltip' => ['enabled' => true],
            ],
            'scales' => [
                'x' => [
                    'ticks' => ['color' => null],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['color' => null],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
            ],
        ];

        return array_replace_recursive($defaults, $options);
    }

    private function mergeDefaultPolarOptions(array $options): array
    {
        $defaults = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'top'],
                'tooltip' => ['enabled' => true],
            ],
        ];

        return array_replace_recursive($defaults, $options);
    }
}
