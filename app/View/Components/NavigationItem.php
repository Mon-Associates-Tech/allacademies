<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavigationItem extends Component
{
    public string $finalIconPath;

    public function __construct(
        public string $route,
        public string $label,
        public ?string $iconPath = null,
        public ?string $icon = null,
        public ?string $activePattern = null,
        public bool $hasArrow = false
    ) {
        $this->activePattern = $activePattern ?? $route;

        // Determine which icon path to use
        if ($iconPath !== null) {
            // Use the provided icon path directly
            $this->finalIconPath = $iconPath;
        } else if ($icon !== null) {
            // Use predefined icon
            $this->finalIconPath = $this->getPredefinedIconPath($icon);
        } else {
            // Default to dashboard icon
            $this->finalIconPath = $this->getPredefinedIconPath('dashboard');
        }
    }

    public function render()
    {
        return view('components.navigation-item');
    }

    private function getPredefinedIconPath(string $iconName): string
    {
        $icons = [
            'message' => "M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z",
            'dashboard' => "M4 2a2 2 0 0 0-2 2v1h12V4a2 2 0 0 0-2-2H4ZM2 7v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H2Zm3 2a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z",
            'user' => "M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002A.274.274 0 0 1 15 13H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z",
            'settings' => "M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"
        ];

        return $icons[$iconName] ?? $this->getDefaultIconPath();
    }

    public function getIconClass(): string
    {
        $baseClass = "shrink-0 fill-current";
        $activeClass = request()->is($this->activePattern) ? 'text-white' : 'text-gray-400 dark:text-gray-500';
        return $baseClass . ' ' . $activeClass;
    }
    private function getDefaultIconPath(): string
    {
        // Default placeholder icon (a simple circle)
        return "M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16zm0-2A6 6 0 1 0 8 2a6 6 0 0 0 0 12zm0-8a2 2 0 1 1 0 4 2 2 0 0 1 0-4z";
    }
}
