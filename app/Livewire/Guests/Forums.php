<?php

namespace App\Livewire\Guests;

use App\Models\BookCategory;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Forums extends Component
{
    use WithPagination;

    public $currentView = 'categories'; // 'categories', 'topics', 'posts', 'create-topic', 'create-post'

    public $selectedCategory = null;

    public $selectedTopic = null;

    public $search = '';

    public $sortBy = 'recent';

    // Create topic/post data
    public $newTopicTitle = '';

    public $newTopicContent = '';

    public $newPostContent = '';

    public function mount()
    {
        // Auto-create forum categories based on book categories if they don't exist
        $this->ensureForumCategories();
    }

    public function ensureForumCategories()
    {
        // Create forum categories from book categories if forum system is new
        $bookCategories = BookCategory::all();
        foreach ($bookCategories as $bookCategory) {
            ForumCategory::firstOrCreate([
                'name' => $bookCategory->name.' Discussion',
                'description' => 'Discussions about '.$bookCategory->name.' books and topics',
                'book_category_id' => $bookCategory->id,
            ]);
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->currentView = 'topics';
        $this->resetPage();
    }

    public function backToCategories()
    {
        $this->currentView = 'categories';
        $this->selectedCategory = null;
        $this->selectedTopic = null;
    }

    public function backToTopics()
    {
        $this->currentView = 'topics';
        $this->selectedTopic = null;
    }

    public function showCreateTopic()
    {
        $this->currentView = 'create-topic';
    }

    public function showCreatePost()
    {
        $this->currentView = 'create-post';
    }

    public function createTopic()
    {
        $this->validate([
            'newTopicTitle' => 'required|string|max:255',
            'newTopicContent' => 'required|string|min:10',
        ]);

        $topic = ForumTopic::create([
            'title' => $this->newTopicTitle,
            'forum_category_id' => $this->selectedCategory,
            'user_id' => Auth::id(),
            'is_pinned' => false,
            'is_locked' => false,
        ]);

        ForumPost::create([
            'content' => $this->newTopicContent,
            'forum_topic_id' => $topic->id,
            'user_id' => Auth::id(),
        ]);

        // Log activity
        $topic->logActivity('create', 'Forum Topic Created', 'forum_topic', [
            'topic_title' => $this->newTopicTitle,
            'forum_category_id' => $this->selectedCategory,
            'has_initial_post' => true,
            'created_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        $this->reset(['newTopicTitle', 'newTopicContent']);
        $this->selectTopic($topic->id);
        session()->flash('success', 'Topic created successfully!');
    }

    public function selectTopic($topicId)
    {
        $this->selectedTopic = $topicId;
        $this->currentView = 'posts';
        $this->resetPage();
    }

    public function createPost()
    {
        $this->validate([
            'newPostContent' => 'required|string|min:10',
        ]);

        $post = ForumPost::create([
            'content' => $this->newPostContent,
            'forum_topic_id' => $this->selectedTopic,
            'user_id' => Auth::id(),
        ]);

        // Log activity
        $post->logActivity('create', 'Forum Post Created', 'forum_post', [
            'forum_topic_id' => $this->selectedTopic,
            'content_length' => strlen($this->newPostContent),
            'created_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        $this->reset(['newPostContent']);
        session()->flash('success', 'Reply posted successfully!');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = [];

        switch ($this->currentView) {
            case 'categories':
                $data['categories'] = ForumCategory::withCount(['topics', 'posts'])
                    ->with('latestPost.user')
                    ->get();
                break;

            case 'topics':
                $query = ForumTopic::where('forum_category_id', $this->selectedCategory)
                    ->with(['user', 'latestPost.user'])
                    ->withCount('posts');

                if ($this->search) {
                    $query->where('title', 'like', '%'.$this->search.'%');
                }

                switch ($this->sortBy) {
                    case 'recent':
                        $query->latest('updated_at');
                        break;
                    case 'popular':
                        $query->orderBy('posts_count', 'desc');
                        break;
                    case 'oldest':
                        $query->oldest('created_at');
                        break;
                }

                $data['topics'] = $query->paginate(20);
                $data['category'] = ForumCategory::find($this->selectedCategory);
                break;

            case 'posts':
            case 'create-post':
                $data['topic'] = ForumTopic::with('user')->find($this->selectedTopic);
                $data['posts'] = ForumPost::where('forum_topic_id', $this->selectedTopic)
                    ->with('user')
                    ->oldest()
                    ->paginate(20);
                break;

            case 'create-topic':
                $data['category'] = ForumCategory::find($this->selectedCategory);
                break;
        }

        return view('livewire.guests.forums', $data);
    }
}
