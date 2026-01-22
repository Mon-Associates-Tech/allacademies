<?php

namespace App\Livewire;

use Livewire\Component;

class Modal extends Component
{
    public $isOpen = false;

    public $title = '';

    public $content = '';

    public $size = 'md';

    public $theme = 'auto';

    public $closeOnBackdrop = false;

    public $closeOnEsc = true;

    public $showCloseButton = true;

    public $persistent = false;

    protected $listeners = [
        'openModal' => 'open',
        'closeModal' => 'close',
        'toggleModal' => 'toggle',
    ];

    public function mount(
        $title = '',
        $content = '',
        $size = 'md',
        $theme = 'auto',
        $closeOnBackdrop = false,
        $closeOnEsc = true,
        $showCloseButton = true,
        $persistent = false
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->size = $size;
        $this->theme = $theme;
        $this->closeOnBackdrop = $closeOnBackdrop;
        $this->closeOnEsc = $closeOnEsc;
        $this->showCloseButton = $showCloseButton;
        $this->persistent = $persistent;
    }

    public function open($data = [])
    {
        if (! empty($data)) {
            $this->fill($data);
        }

        $this->isOpen = true;
        $this->dispatch('modal-opened');
        $this->dispatch('disable-body-scroll');

        // Force a re-render to ensure Alpine.js picks up the new state
        $this->dispatch('$refresh');
    }

    public function close()
    {
        if ($this->persistent) {
            return;
        }

        $this->isOpen = false;
        $this->dispatch('modal-closed');
        $this->dispatch('enable-body-scroll');

        // Add a small delay to ensure smooth closing animation
        $this->dispatch('modal-closing');
    }

    public function toggle()
    {
        $this->isOpen ? $this->close() : $this->open();
    }

    public function handleBackdropClick()
    {
        if ($this->closeOnBackdrop && ! $this->persistent) {
            $this->close();
        }
    }

    public function handleEscapeKey()
    {
        if ($this->closeOnEsc && ! $this->persistent) {
            $this->close();
        }
    }

    public function getSizeClasses()
    {
        return match ($this->size) {
            'xs' => 'max-w-xs',
            'sm' => 'max-w-sm',
            'lg' => 'max-w-2xl',
            'xl' => 'max-w-4xl',
            '2xl' => 'max-w-6xl',
            'full' => 'max-w-full mx-4',
            default => 'max-w-lg',
        };
    }

    public function getThemeClasses()
    {
        $isDark = $this->theme === 'dark' ||
                 ($this->theme === 'auto' && request()->cookie('theme') === 'dark');

        return $isDark
            ? 'bg-gray-800 text-white border-gray-700'
            : 'bg-white text-gray-900 border-gray-200';
    }

    public function render()
    {
        return view('livewire.modal');
    }
}
