<?php

namespace App\Livewire\Media;

use App\Models\Media\MediaFile;
use App\Models\Post;
use Livewire\Component;

class PostForm extends Component
{
    public $postId = null;

    public $title = '';

    public $content = '';

    public $excerpt = '';

    public $featuredImageId = null;

    public $featuredImage = null;

    public $galleryImageIds = [];

    public $galleryImages = [];

    protected $listeners = [
        'featuredImageSelected' => 'setFeaturedImage',
        'galleryImagesSelected' => 'setGalleryImages',
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'excerpt' => 'nullable|string|max:500',
    ];

    public function mount($postId = null)
    {
        if ($postId) {
            $this->postId = $postId;
            $post = Post::with('media')->find($postId);

            $this->title = $post->title;
            $this->content = $post->content;
            $this->excerpt = $post->excerpt;

            // Load featured image
            $featuredImage = $post->getFirstMedia('featured_image');
            if ($featuredImage) {
                $this->featuredImageId = $featuredImage->id;
                $this->featuredImage = $featuredImage;
            }

            // Load gallery images
            $galleryImages = $post->getMedia('gallery');
            $this->galleryImageIds = $galleryImages->pluck('id')->toArray();
            $this->galleryImages = $galleryImages->toArray();
        }
    }

    public function render()
    {
        return view('livewire.post-form');
    }

    public function setFeaturedImage($media)
    {
        $this->featuredImageId = $media['id'];
        $this->featuredImage = MediaFile::find($media['id']);
    }

    public function removeFeaturedImage()
    {
        $this->featuredImageId = null;
        $this->featuredImage = null;
    }

    public function setGalleryImages($mediaArray)
    {
        $this->galleryImageIds = collect($mediaArray)->pluck('id')->toArray();
        $this->galleryImages = $mediaArray;
    }

    public function removeGalleryImage($mediaId)
    {
        $this->galleryImageIds = array_filter($this->galleryImageIds, fn ($id) => $id !== $mediaId);
        $this->galleryImages = array_filter($this->galleryImages, fn ($media) => $media['id'] !== $mediaId);
        $this->galleryImages = array_values($this->galleryImages); // Re-index array
    }

    public function save()
    {
        $this->validate();

        $post = $this->postId ? Post::find($this->postId) : new Post;

        $post->fill([
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'user_id' => auth()->id(),
        ]);

        $post->save();

        // Handle featured image
        if ($this->featuredImageId) {
            // Remove existing featured image
            $post->detachMedia($this->featuredImageId, 'featured_image');
            // Attach new featured image
            $post->attachMedia($this->featuredImageId, 'featured_image');
        }

        // Handle gallery images
        // Remove all existing gallery images
        $existingGallery = $post->getMedia('gallery');
        foreach ($existingGallery as $media) {
            $post->detachMedia($media->id, 'gallery');
        }

        // Attach new gallery images
        foreach ($this->galleryImageIds as $index => $mediaId) {
            $post->attachMedia($mediaId, 'gallery', ['sort_order' => $index]);
        }

        $this->emit('notify', 'Post saved successfully!', 'success');

        if (! $this->postId) {
            return redirect()->route('posts.edit', $post);
        }
    }

    public function openFeaturedImagePicker()
    {
        $this->emit('openMediaPicker', 'featured');
    }

    public function openGalleryPicker()
    {
        $this->emit('openMediaPicker', 'gallery');
    }
}
