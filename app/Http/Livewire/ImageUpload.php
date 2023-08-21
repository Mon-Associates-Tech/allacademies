<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Image;
use Livewire\WithFileUploads;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ImageUpload extends Component
{

    use WithFileUploads;
 
    public $image;
    public $description;
    public $tags = [];
    public $term;

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
        $tags_suggest  = array_unique(Arr::flatten(Image::all()->pluck('tags')->toArray()));
        
        return view('livewire.image-upload', [
            'images' => Image::when($this->term, function($query, $term){
                return $query->where(DB::raw('lower(tags)'), "LIKE", "%".strtolower($term)."%")
                ->orWhere('description', 'LIKE', "%{$term}%");
            })->latest()->limit(3)->get(),
            'tags_suggest' => $tags_suggest
        ]);
        
    }
    
}