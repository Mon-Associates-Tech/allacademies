<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Image;
use Livewire\WithFileUploads;

class ImageUpload extends Component
{

    use WithFileUploads;
 
    public $image;
    public $description;
    public $tags = [];

        protected $rules = [
            'description' => 'required|string',
            'tags' => 'required|array',
            'image' => 'required|image',
        ];
    


    public function upload()
    {
        $this->validate();
 
        $url = $this->image->store('images', 'public');
 
        Image::create([
            'tags' => json_encode($this->tags),
            'description' => $this->description,
            'path' => $url,
        ]);

        return to_route('image-upload')
            ->with('success', "Image successfully uploaded.");
  
    }
    

    public function render()
    {
        return view('livewire.image-upload');
        
    }
    
}