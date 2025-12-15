<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Traits\FormatsChartData;

class GaugeChart extends Component
{
    use FormatsChartData;

    public float $value = 0;
    public float $min = 0;
    public float $max = 100;
    /** @var array<int,array{max:float|int,color:string,label?:string}> */
    public array $thresholds = [];
    public array $options = [];
    public ?string $centerLabel = null;
    public bool $showValue = true;
    public ?string $chartId = null;
    public ?string $heightClass = 'h-48';
    public ?string $widthClass = 'w-full';

    public function mount(
        float $value = 0,
        float $min = 0,
        float $max = 100,
        array $thresholds = [],
        array $options = [],
        ?string $centerLabel = null,
        bool $showValue = true,
        ?string $chartId = null,
        ?string $heightClass = 'h-48',
        ?string $widthClass = 'w-full',
    ) {
        $this->value = $value;
        $this->min = $min;
        $this->max = $max;
        $this->thresholds = $thresholds;
        $this->options = $options;
        $this->centerLabel = $centerLabel;
        $this->showValue = $showValue;
        $this->chartId = $chartId ?? 'gauge-chart-' . spl_object_id($this);
        $this->heightClass = $heightClass;
        $this->widthClass = $widthClass;
    }

    public function getPayloadProperty(): array
    {
        $opts = $this->options;
        $opts['centerLabel'] = $this->centerLabel;
        $opts['showValue'] = $this->showValue;
        return $this->formatGaugeData($this->value, $this->min, $this->max, $this->thresholds, $opts);
    }

    public function render()
    {
        return view('livewire.charts.gauge-chart');
    }
}
