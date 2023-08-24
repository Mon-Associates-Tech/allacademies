<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Image;
use Livewire\WithFileUploads;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImageUpload extends Component
{

    use WithFileUploads;
 
    public $image;
    public $description;
    public $tags = [];
    public $tag;
    public $showTagsSuggestions = false;
   
    protected $rules = [
        'description' => 'required|string|max:180',
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
    

    public function addTag($newTag)
    {
        $this->tags = array_map('strtolower', $this->tags);
        if(!in_array(strtolower($newTag), $this->tags))
        {
            array_push($this->tags, $newTag);
            $this->tag="";
        }else{
            $this->tag="";
        }
        $this->showTagsSuggestions = false;
        
    }

    
    public function removeTag($index)
    {
        unset($this->tags[$index]); 
        $this->tags = array_values($this->tags);
    }
    

    public function render()
    { 
        $unique_tags = array();
    
        if($this->tag){
            $this->showTagsSuggestions = true;
            $tags_suggest = Arr::flatten(Image::pluck('tags')->toArray());
        
            $unique_tags = collect($tags_suggest);
            $unique_tags = $unique_tags->unique();
            $unique_tags->values()->all();

            $unique_tags = $unique_tags->filter(function ($value, $key) {

                return Str::contains(strtolower($value), strtolower($this->tag));
            });
             
            $unique_tags->all();
        }
        
        return view('livewire.image-upload', [
            'tags_suggest' => $unique_tags,
        ]);  

    } 
}