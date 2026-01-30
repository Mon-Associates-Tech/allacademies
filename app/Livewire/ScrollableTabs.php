<?php

namespace App\Livewire;

use Livewire\Component;

class ScrollableTabs extends Component
{
    public array $tabs = [];

    public string $activeTab = '';

    public string $orientation = 'horizontal'; // 'horizontal' or 'vertical'

    public string $tabContainerClass = '';

    public string $tabClass = '';

    public string $activeTabClass = '';

    public string $inactiveTabClass = '';

    public function mount(
        array $tabs = [],
        string $activeTab = '',
        string $orientation = 'horizontal',
        string $tabContainerClass = '',
        string $tabClass = '',
        string $activeTabClass = '',
        string $inactiveTabClass = ''
    ) {
        $this->tabs = $tabs;
        $this->activeTab = $activeTab;
        $this->orientation = $orientation;

        // Set default classes based on orientation
        if ($this->orientation === 'vertical') {
            $this->tabContainerClass = $tabContainerClass ?: 'space-y-1 border-r border-gray-200 dark:border-gray-700';
            $this->tabClass = $tabClass ?: 'px-4 py-2 border-r-2 font-medium text-sm transition-colors whitespace-nowrap w-full text-left';
            $this->activeTabClass = $activeTabClass ?: 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20';
            $this->inactiveTabClass = $inactiveTabClass ?: 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800';
        } else {
            $this->tabContainerClass = $tabContainerClass ?: 'space-x-8 border-b border-gray-200 dark:border-gray-700';
            $this->tabClass = $tabClass ?: 'py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap';
            $this->activeTabClass = $activeTabClass ?: 'border-indigo-500 text-indigo-600 dark:text-indigo-400';
            $this->inactiveTabClass = $inactiveTabClass ?: 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600';
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', tab: $tab);
    }

    public function render()
    {
        $viewName = $this->orientation === 'vertical'
            ? 'livewire.scrollable-tabs-vertical'
            : 'livewire.scrollable-tabs';

        return view($viewName);
    }
}
