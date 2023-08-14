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
    


    public function upload()
    {
        $data = $this->validate([
            'description' => ['required','string'],
            'tags' => ['required','array'],
            'image' => ['required', 'image'],
          ]);

        $url = $this->image->store('images', 'public');
    
        Image::create($data + ['path' => $url]);
    
        return to_route('image')
            ->with('success', "Image successfully uploaded.");
  
    }
    

    public function render()
    {
        return view('livewire.image-upload');
        
    }
}