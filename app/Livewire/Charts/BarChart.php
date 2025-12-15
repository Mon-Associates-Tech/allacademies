<?php

namespace App\Livewire\Charts;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Traits\FormatsChartData;

class BarChart extends Component
{
    use FormatsChartData;

    /** @var array */
    public array $labels = [];
    /** @var array */
    public array $datasets = [];
    /** @var array */
    public array $options = [];
    /** @var string|null */
    public ?string $chartId = null;
    /** @var string|null */
    public ?string $heightClass = 'h-64';
    /** @var string|null */
    public ?string $widthClass = 'w-full';

    public function mount(array $labels = [], array $datasets = [], array $options = [], ?string $chartId = null, ?string $heightClass = 'h-64', ?string $widthClass = 'w-full')
    {
        $this->labels = $labels;
        $this->datasets = $datasets;
        $this->options = $options;
        $this->chartId = $chartId ?? 'bar-chart-' . spl_object_id($this);
        $this->heightClass = $heightClass;
        $this->widthClass = $widthClass;
    }

    public function getPayloadProperty(): array
    {
        return $this->formatBarData($this->labels, $this->datasets, $this->options);
    }

    public function render()
    {
        return view('livewire.charts.bar-chart');
    }
}
