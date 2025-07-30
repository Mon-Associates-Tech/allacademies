<?php

namespace App\Livewire\Common;

use Livewire\Component;

class GlobalMessage extends Component
{
    public $messages = [];
    public $autoHide = true;
    public $hideDelay = 5000; // 5 seconds

    protected $listeners = [
        'showMessage' => 'addMessage',
        'success' => 'addSuccessMessage',
        'error' => 'addErrorMessage',
        'warning' => 'addWarningMessage',
        'info' => 'addInfoMessage',
        'clearMessages' => 'clearAllMessages',
    ];

    public function mount($message = null, $type = 'info', $autoHide = null)
    {
        // Handle direct message passing via Blade
        if ($message && is_string($message)) {
            $this->addMessage($message, $type, $autoHide);
        }

        // Check for flash messages from session
        $this->checkFlashMessages();
    }

    public function checkFlashMessages(): void
    {
        $flashTypes = ['success', 'error', 'warning', 'info', 'message'];

        foreach ($flashTypes as $type) {
            $flashMessage = session($type);
            if ($flashMessage && is_string($flashMessage)) {
                $this->addMessage($flashMessage, $type === 'message' ? 'info' : $type);
                session()->forget($type);
            }
        }
    }

    public function addMessage($message = '', $type = 'info', $autoHide = null): void
    {
        if (!$message || !is_string($message)) {
            return;
        }

        $id = uniqid();
        $autoHide = $autoHide ?? $this->autoHide;

        $this->messages[] = [
            'id' => $id,
            'message' => $message,
            'type' => in_array($type, ['success', 'error', 'warning', 'info']) ? $type : 'info',
            'autoHide' => $autoHide,
            'timestamp' => now()->toISOString(),
        ];

        if ($autoHide) {
            $this->dispatch('autoHideMessage', id: $id, delay: $this->hideDelay);
        }
    }

    public function addSuccessMessage($message, $autoHide = null)
    {
        $this->addMessage($message, 'success', $autoHide);
    }

    public function addErrorMessage($message, $autoHide = null)
    {
        $this->addMessage($message, 'error', $autoHide);
    }

    public function addWarningMessage($message, $autoHide = null)
    {
        $this->addMessage($message, 'warning', $autoHide);
    }

    public function addInfoMessage($message, $autoHide = null)
    {
        $this->addMessage($message, 'info', $autoHide);
    }

    public function dismissMessage($id)
    {
        $this->messages = array_filter($this->messages, fn($message) => $message['id'] !== $id);
    }

    public function clearAllMessages()
    {
        $this->messages = [];
    }

    public function getMessageIcon($type)
    {
        return match ($type) {
            'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.865-.833-2.635 0L4.178 16.5c-.77.833.192 2.5 1.732 2.5z',
            'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function getMessageColors($type)
    {
        return match ($type) {
            'success' => [
                'bg' => 'bg-green-50 dark:bg-green-900/30',
                'border' => 'border-green-200 dark:border-green-800',
                'text' => 'text-green-800 dark:text-green-100',
                'icon' => 'text-green-500 dark:text-green-400',
                'button' => 'text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300',
            ],
            'error' => [
                'bg' => 'bg-red-50 dark:bg-red-900/30',
                'border' => 'border-red-200 dark:border-red-800',
                'text' => 'text-red-800 dark:text-red-100',
                'icon' => 'text-red-500 dark:text-red-400',
                'button' => 'text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300',
            ],
            'warning' => [
                'bg' => 'bg-yellow-50 dark:bg-yellow-900/30',
                'border' => 'border-yellow-200 dark:border-yellow-800',
                'text' => 'text-yellow-800 dark:text-yellow-100',
                'icon' => 'text-yellow-500 dark:text-yellow-400',
                'button' => 'text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300',
            ],
            'info' => [
                'bg' => 'bg-blue-50 dark:bg-blue-900/30',
                'border' => 'border-blue-200 dark:border-blue-800',
                'text' => 'text-blue-800 dark:text-blue-100',
                'icon' => 'text-blue-500 dark:text-blue-400',
                'button' => 'text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300',
            ],
            default => [
                'bg' => 'bg-gray-50 dark:bg-gray-900/30',
                'border' => 'border-gray-200 dark:border-gray-800',
                'text' => 'text-gray-800 dark:text-gray-100',
                'icon' => 'text-gray-500 dark:text-gray-400',
                'button' => 'text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
            ],
        };
    }

    public function render()
    {
        return view('livewire.common.global-message');
    }
}
