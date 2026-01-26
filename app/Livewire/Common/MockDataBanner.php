<?php

namespace App\Livewire\Common;

use Livewire\Component;

class MockDataBanner extends Component
{
    public string $heading = 'Sample Data';

    public string $message = 'The information displayed on this page contains sample data and may not reflect actual values.';

    public string $variant = 'info'; // info, warning, notice

    public bool $dismissible = true;

    public bool $showIcon = true;

    public string $actionText = '';

    public string $actionUrl = '';

    public bool $isVisible = true;

    public function mount(
        ?string $heading = null,
        ?string $message = null,
        ?string $variant = 'info',
        ?bool $dismissible = true,
        ?bool $showIcon = true,
        ?string $actionText = '',
        ?string $actionUrl = ''
    ) {
        if ($heading) {
            $this->heading = $heading;
        }

        if ($message) {
            $this->message = $message;
        }

        $this->variant = $variant;
        $this->dismissible = $dismissible;
        $this->showIcon = $showIcon;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;

        // Check if user has dismissed this banner in session
        $bannerId = $this->getBannerId();
        if (session()->has("banner_dismissed_{$bannerId}")) {
            $this->isVisible = false;
        }
    }

    public function dismiss()
    {
        $this->isVisible = false;

        // Store dismissal in session for this page
        $bannerId = $this->getBannerId();
        session()->put("banner_dismissed_{$bannerId}", true);
    }

    private function getBannerId(): string
    {
        // Create a unique ID based on heading and message
        return md5($this->heading.$this->message);
    }

    public function render()
    {
        return view('livewire.common.mock-data-banner');
    }
}
