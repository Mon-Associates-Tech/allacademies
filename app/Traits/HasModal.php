<?php

namespace App\Traits;

trait HasModal
{
    public $isOpen = false;
    public $modalTitle = '';
    public $modalContent = '';
    public $modalSize = 'md';
    public $modalTheme = 'auto';
    public $modalCloseOnBackdrop = false;
    public $modalPersistent = false;

    public function openModal($title = '', $content = '', $options = [])
    {
        // Set modal properties first
        $this->modalTitle = $title;
        $this->modalContent = $content;
        $this->modalSize = $options['size'] ?? 'md';
        $this->modalTheme = $options['theme'] ?? 'auto';
        $this->modalCloseOnBackdrop = $options['closeOnBackdrop'] ?? false;
        $this->modalPersistent = $options['persistent'] ?? false;

        // Only open if not already open, or force refresh if already open
        if (!$this->isOpen) {
            $this->isOpen = true;
        } else {
            // If already open, close and reopen to refresh content
            $this->isOpen = false;
            // Use JavaScript setTimeout to ensure DOM updates
            $this->dispatch('force-modal-refresh');
        }

        $this->dispatch('modal-opened');
    }

    public function closeModal()
    {
        if ($this->modalPersistent) {
            return;
        }

        $this->isOpen = false;
        $this->dispatch('modal-closed');

        // Reset modal properties after a brief delay to allow animation
        $this->dispatch('reset-modal-state');
    }

    public function resetModalState()
    {
        $this->modalTitle = '';
        $this->modalContent = '';
        $this->modalSize = 'md';
        $this->modalTheme = 'auto';
        $this->modalCloseOnBackdrop = false;
        $this->modalPersistent = false;
    }

    public function toggleModal()
    {
        $this->isOpen ? $this->closeModal() : $this->openModal();
    }
}
