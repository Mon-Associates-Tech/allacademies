<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Image;
use Illuminate\Support\Facades\DB;

class ShowImages extends Component
{
    public $searchTerm;

    public function render()
    { 
        $images = [];
        if($this->searchTerm)
        {
            $images = Image::search($this->searchTerm)->latest('id')->take(3)->get();
        }else{
            $images = Image::latest('id')->limit(3)->get();
        }

        return view('livewire.show-images', [
            'images' => $images,
        ]);
    } 
    
}