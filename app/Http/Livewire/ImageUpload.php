<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Image;
use Livewire\WithFileUploads;
use Illuminate\Support\Arr;

class ImageUpload extends Component
{

    use WithFileUploads;
 
    public $image;
    public $description;
    public $tags = [];
    public $term;
    public $fruits = [];

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
            'tags' => $this->tags,
            'description' => $this->description,
            'path' => $url,
        ]);

        return to_route('image-upload')
            ->with('success', "Image successfully uploaded.");
    }
    

    public function render()
    {
        $test = Image::whereJsonContains('tags', 'Calculus')->get()->toArray();
        $phones = Image::whereRaw('json_contains(tags, \'["Calculus"]\')')->get();
        // var_dump($test);

        return view('livewire.image-upload', [
            'images' => Image::when($this->term, function($query, $term){
                return $query->whereJsonContains('tags', $term)
                ->orWhere('description', 'LIKE', "%{$term}%");
            })->latest()->limit(3)->get(),
            'tags_suggest' => array_unique(Arr::flatten(Image::all()->pluck('tags')->toArray()))
        ]);
        
    }
    
}