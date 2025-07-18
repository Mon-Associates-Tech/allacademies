<?php

namespace App\Livewire\Common;

trait HasGlobalMessages
{
    public function showSuccess($message, $autoHide = null)
    {
        $this->dispatch('success', $message, $autoHide);
    }

    public function showError($message, $autoHide = null)
    {
        $this->dispatch('error', $message, $autoHide);
    }

    public function showWarning($message, $autoHide = null)
    {
        $this->dispatch('warning', $message, $autoHide);
    }

    public function showInfo($message, $autoHide = null)
    {
        $this->dispatch('info', $message, $autoHide);
    }

    public function showMessage($message, $type = 'info', $autoHide = null)
    {
        $this->dispatch('showMessage', $message, $type, $autoHide);
    }

    public function clearMessages()
    {
        $this->dispatch('clearMessages');
    }
}
