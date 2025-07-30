<?php

namespace App\Livewire\Common;

trait HasGlobalMessages
{
    public function showSuccess($message, $autoHide = null)
    {
        if (!$message || !is_string($message)) {
            return;
        }
        $this->dispatch('success', message: $message, autoHide: $autoHide);
    }

    public function showError($message, $autoHide = null)
    {
        if (!$message || !is_string($message)) {
            return;
        }
        $this->dispatch('error', message: $message, autoHide: $autoHide);
    }

    public function showWarning($message, $autoHide = null)
    {
        if (!$message || !is_string($message)) {
            return;
        }
        $this->dispatch('warning', message: $message, autoHide: $autoHide);
    }

    public function showInfo($message, $autoHide = null)
    {
        if (!$message || !is_string($message)) {
            return;
        }
        $this->dispatch('info', message: $message, autoHide: $autoHide);
    }

    public function showMessage($message, $type = 'info', $autoHide = null)
    {
        if (!$message || !is_string($message)) {
            return;
        }
        $type = in_array($type, ['success', 'error', 'warning', 'info']) ? $type : 'info';
        $this->dispatch('showMessage', message: $message, type: $type, autoHide: $autoHide);
    }

    public function clearMessages()
    {
        $this->dispatch('clearMessages');
    }
}
