<?php

namespace App\Livewire;

use App\Models\Image;
use Livewire\Component;

class ShowImages extends Component
{
    public $search;

    public function render()
    {
        $images = Image::query()
            ->when(trim($this->search), function ($query, $search) {
                return $query->search($search);
            })
            ->latest('id')
            ->limit(5)
            ->get();

        return view('livewire.show-images', [
            'images' => $images,
        ]);
    }
}
