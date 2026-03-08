<?php

namespace App\Livewire;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUpload extends Component
{
    use WithFileUploads;

    public $image = null;

    public $description = '';

    public $tags = [];

    public $tag = '';

    protected $rules = [
        'description' => 'required|string|min:5|max:255',
        'tags' => 'required|array',
        'tags.*' => 'required|string|min:2|max:255',
        'image' => 'required|image',
    ];

    public function mount()
    {
        $this->image = null;
        $this->description = '';
        $this->tags = [];
        $this->tag = '';
    }

    public function uploads()
    {
        $this->validate();

        $path = $this->image->storePublicly('images', 'public');

        if ($path === false) {
            throw ValidationException::withMessages(['image' => 'Image upload failed.']);
        }

        Image::create([
            'tags' => $this->tags,
            'description' => Str::headline($this->description),
            'path' => 'storage/'.$path,
        ]);

        $this->reset();
    }

    public function addTag($newTag)
    {
        $newTag = Str::studly($newTag);

        if ($newTag !== '' && ! in_array($newTag, $this->tags, true)) {
            array_push($this->tags, $newTag);
        }

        $this->reset('tag');
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function render()
    {
        $suggestedTags = [];

        if ('' !== $search = Str::studly($this->tag)) {
            $suggestedTags = Image::query()
                ->select('tags')
                ->whereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ["{$search}%"])
                ->limit(15)
                ->get()
                ->pluck('tags')
                ->flatten()
                ->unique()
                ->filter(function ($tag) use ($search) {
                    return str_starts_with($tag, $search) && ! in_array($tag, $this->tags, true);
                })
                ->values()
                ->all();
        }

        return view('livewire.image-upload', [
            'suggestedTags' => $suggestedTags,
        ]);
    }
}
