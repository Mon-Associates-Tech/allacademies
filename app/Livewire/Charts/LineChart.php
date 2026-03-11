<?php

namespace App\Livewire\Charts;

use App\Traits\FormatsChartData;
use Livewire\Component;

class LineChart extends Component
{
    use FormatsChartData;

    public array $labels = [];

    public array $datasets = [];

    public array $options = [];

    public ?string $chartId = null;

    public ?string $heightClass = 'h-64';

    public ?string $widthClass = 'w-full';

    public function mount(array $labels = [], array $datasets = [], array $options = [], ?string $chartId = null, ?string $heightClass = 'h-64', ?string $widthClass = 'w-full')
    {
        $this->labels = $labels;
        $this->datasets = $datasets;
        $this->options = $options;
        $this->chartId = $chartId ?? 'line-chart-'.spl_object_id($this);
        $this->heightClass = $heightClass;
        $this->widthClass = $widthClass;
    }

    public function getPayloadProperty(): array
    {
        return $this->formatLineData($this->labels, $this->datasets, $this->options);
    }

    public function render()
    {
        return view('livewire.charts.line-chart');
    }
}
