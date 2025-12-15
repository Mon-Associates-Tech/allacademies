<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Traits\FormatsChartData;

class PieChart extends Component
{
    use FormatsChartData;

    public array $labels = [];
    public array $values = [];
    public array $options = [];
    public ?string $chartId = null;
    public ?string $heightClass = 'h-64';
    public ?string $widthClass = 'w-full';

    public function mount(array $labels = [], array $values = [], array $options = [], ?string $chartId = null, ?string $heightClass = 'h-64', ?string $widthClass = 'w-full')
    {
        $this->labels = $labels;
        $this->values = $values;
        $this->options = $options;
        $this->chartId = $chartId ?? 'pie-chart-' . spl_object_id($this);
        $this->heightClass = $heightClass;
        $this->widthClass = $widthClass;
    }

    public function getPayloadProperty(): array
    {
        return $this->formatPieData($this->labels, $this->values, $this->options);
    }

    public function render()
    {
        return view('livewire.charts.pie-chart');
    }
}
