<?php

namespace App\View\Components\Newsletter;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubscriptionForm extends Component
{
    public function __construct(
        public string $theme = 'light',
        public string $size = 'default',
        public bool $showName = false,
        public string $buttonText = 'Subscribe'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.newsletter.subscription-form');
    }
}
