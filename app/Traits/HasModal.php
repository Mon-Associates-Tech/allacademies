<?php

namespace App\Traits;

trait HasModal
{
    public function openModal($modalName, $data = [])
    {
        $this->dispatch('modal:setActive', array_merge($data, ['modal' => $modalName]));
    }

    public function closeModal($modalName = null)
    {
        if ($modalName) {
            $this->dispatch('modal:close', ['modal' => $modalName]);
        } else {
            $this->dispatch('modal:setActive', ['modal' => null]);
        }
    }

    public function closeAllModals()
    {
        $this->dispatch('modal:setActive', ['modal' => null]);
    }

}
