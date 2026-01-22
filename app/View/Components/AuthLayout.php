<?php

namespace App\View\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthLayout extends Component
{
    public array $colWidths;

    public int $maxCols = 7;

    public bool $mainOnly = true;

    /**
     * @var false
     */
    public bool $mini;

    public function __construct(string $cols = '1,3,2', bool $mainOnly = true, $mini = false)
    {
        $this->colWidths = array_map('trim', explode(',', $cols));
        $this->mainOnly = $mainOnly;
        $this->mini = $mini;
    }

    public function render(): View|Factory|Htmlable|string|\Closure|\Illuminate\View\View|Application
    {
        return view('components.auth');
    }
}
