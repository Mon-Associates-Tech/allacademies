<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalComponent extends Component
{
    public string $name;

    public string $title;

    public string $size;

    public bool $closable;

    public bool $persistent;

    public string $backdrop;

    public string $position;

    public string $animation;

    public string $maxWidth;

    public bool $show;

    public string $headerBackground;

    public bool $fullheight;

    public string $height;

    public bool $fixedFooter;

    public array $modalData;

    public string $zIndex;

    public function __construct(
        string $name = '',
        string $title = '',
        string $size = 'default',
        bool $closable = true,
        bool $persistent = false,
        string $backdrop = 'blur',
        string $position = 'bottom',
        string $animation = 'slide',
        bool $show = false,
        bool $fullheight = false,
        string $headerBackground = 'bg-white dark:bg-gray-800',
        string $height = '',
        bool $fixedFooter = false,
        array $modalData = [],
        string $zIndex = 'z-50'
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->size = $size;
        $this->closable = $closable;
        $this->persistent = $persistent;
        $this->backdrop = $backdrop;
        $this->position = $position;
        $this->animation = $animation;
        $this->show = $show;
        $this->maxWidth = $this->getMaxWidth($size);
        $this->headerBackground = $headerBackground;
        $this->fullheight = $fullheight;
        $this->height = $height;
        $this->fixedFooter = $fixedFooter;
        $this->modalData = $modalData;
        $this->zIndex = $zIndex;
    }

    private function getMaxWidth(string $size): string
    {
        return match ($size) {
            'xs' => 'max-w-xs',
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'xl' => 'max-w-xl',
            '2xl' => 'max-w-2xl',
            '3xl' => 'max-w-3xl',
            '4xl' => 'max-w-4xl',
            '5xl' => 'max-w-5xl',
            '6xl' => 'max-w-6xl',
            '7xl' => 'max-w-7xl',
            'full' => 'max-w-full',
            default => 'max-w-lg',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.modal-component');
    }
}
