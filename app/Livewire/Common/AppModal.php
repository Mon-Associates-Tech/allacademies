<?php

namespace App\Livewire\Common;

use Livewire\Component;

class AppModal extends Component
{
    public $show = false;

    public $title = '';

    public $size = 'default';

    public $closable = true;

    public $persistent = false;

    public $maxWidth = '';

    public $backdrop = 'blur';

    public $position = 'bottom';

    public $animation = 'slide';

    public $body = '';

    public $footer = '';

    public $actions = '';

    public $loading = false;

    public $name = '';

    protected $listeners = [
        'openModal' => 'open',
        'closeModal' => 'close',
        'toggleModal' => 'toggle',
    ];

    public function mount(
        $name = '',
        $title = '',
        $size = 'default',
        $closable = true,
        $persistent = false,
        $backdrop = 'blur',
        $position = 'bottom',
        $animation = 'slide'
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->size = $size;
        $this->closable = $closable;
        $this->persistent = $persistent;
        $this->backdrop = $backdrop;
        $this->position = $position;
        $this->animation = $animation;
        $this->setMaxWidth();
    }

    public function open($data = [])
    {
        if ((isset($data['name']) && $data['name'] !== $this->name)) {
            return;
        }
        if (isset($data['title'])) {
            $this->title = $data['title'];
        }
        if (isset($data['size'])) {
            $this->size = $data['size'];
            $this->setMaxWidth();
        }

        $this->show = true;
        $this->dispatch('modal-opened');
    }

    public function close()
    {

        $this->show = false;
        $this->dispatch('modal-closed');

    }

    public function toggle()
    {
        $this->show ? $this->close() : $this->open();
    }

    public function closeOnBackdrop()
    {
        if ($this->closable && ! $this->persistent) {
            $this->close();
        }
    }

    private function setMaxWidth()
    {
        $this->maxWidth = match ($this->size) {
            'xs' => 'max-w-xs',
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'lg' => 'max-w-lg',
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

    public function render()
    {
        return view('livewire.common.app-modal');
    }
}
