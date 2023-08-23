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
        return view('livewire.show-images', [
            'images' => Image::when($this->searchTerm, function($query, $searchTerm){
                return $query->where(DB::raw('lower(tags)'), 'LIKE', "%".strtolower($searchTerm)."%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            })->latest()->limit(3)->get(),
        ]);  
    } 
}