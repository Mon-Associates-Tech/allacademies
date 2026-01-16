<?php

namespace App\Livewire\Forums;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Book;
use App\Models\Forum\ForumAttachment;
use App\Models\Forum\ForumCategory;
use App\Models\Forum\ForumMention;
use App\Models\Forum\ForumPost;
use App\Models\Forum\ForumReaction;
use App\Models\Forum\ForumTopic;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

// Add this import

class ForumManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Navigation
    public $currentView = 'categories';

    public $selectedCategory = null;

    public $selectedTopic = null;

    public $selectedPost = null;

    // Filters and Search
    public $search = '';

    public $sortBy = 'recent';

    public $filterBy = 'all';

    public $academicLevelFilter = null;

    public $academicSubjectFilter = null;

    // Create/Edit Forms
    public $newTopicTitle = '';

    public $newTopicContent = '';

    public $newTopicTags = '';

    public $newTopicAcademicLevel = null;

    public $newTopicAcademicSubject = null;

    public $newTopicAcademicTopic = null;

    public $newTopicStudyGroup = null;

    public $newTopicReferencedBook = null;

    public $newTopicAttachments = [];

    public $newPostContent = '';

    public $newPostAttachments = [];

    public $replyToPostId = null;

    // Moderation
    public $showModerationPanel = false;

    public $moderationAction = '';

    public $moderationReason = '';

    // Data Collections
    public $academicLevels = [];

    public $academicSubjects = [];

    public $academicTopics = [];

    public $studyGroups = [];

    public $books = [];

    public $users = [];

    protected $queryString = [
        'currentView' => ['except' => 'categories'],
        'selectedCategory' => ['except' => null],
        'selectedTopic' => ['except' => null],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'recent'],
        'filterBy' => ['except' => 'all'],
        'academicLevelFilter' => ['except' => null],
        'academicSubjectFilter' => ['except' => null],
    ];

    protected $rules = [
        'newTopicTitle' => 'required|string|max:255',
        'newTopicContent' => 'required|string|min:10',
        'newPostContent' => 'required|string|min:5',
        'newTopicAttachments.*' => 'file|max:10240',
        'newPostAttachments.*' => 'file|max:10240',
    ];

    public function mount()
    {
        $this->loadInitialData();
        $this->ensureForumCategories();

        // Restore navigation state from URL parameters
        $this->restoreNavigationState();
    }

    public function loadInitialData()
    {
        $this->academicLevels = AcademicLevel::all();
        $this->academicSubjects = AcademicSubject::all();
        $this->academicTopics = AcademicTopic::all();
        $this->studyGroups = StudentGroup::where('is_active', true)->get();
        $this->books = Book::where('status', 'published')->get();
        $this->users = User::where('is_active', true)->get();
    }

    public function ensureForumCategories()
    {
        // Create forum categories based on academic hierarchy
        $academicLevels = AcademicLevel::all();
        foreach ($academicLevels as $level) {
            ForumCategory::firstOrCreate([
                'academic_level_id' => $level->id,
                'academic_subject_id' => null,
            ], [
                'name' => $level->name.' Discussion',
                'slug' => $this->generateUniqueSlug($level->name.' Discussion'),
                'description' => 'General discussions for '.$level->name.' level students',
                'color' => 'violet',
                'is_active' => true,
                'sort_order' => $level->id + 100,
            ]);

            // Create subject-specific categories
            $subjects = AcademicSubject::where('academic_level_id', $level->id)->get();
            foreach ($subjects as $subject) {
                ForumCategory::firstOrCreate([
                    'academic_level_id' => $level->id,
                    'academic_subject_id' => $subject->id,
                ], [
                    'name' => $subject->name.' - '.$level->name,
                    'slug' => $this->generateUniqueSlug($subject->name.' '.$level->name),
                    'description' => 'Subject-specific discussions for '.$subject->name.' at '.$level->name.' level',
                    'color' => 'blue',
                    'is_active' => true,
                    'sort_order' => ($level->id * 100) + $subject->id,
                ]);
            }
        }

        // Create general categories
        $generalCategories = [
            ['name' => 'General Discussion', 'description' => 'General academic discussions and questions', 'color' => 'green'],
            ['name' => 'Study Groups', 'description' => 'Find and organize study groups', 'color' => 'purple'],
            ['name' => 'Book Reviews', 'description' => 'Share your thoughts on books and resources', 'color' => 'yellow'],
            ['name' => 'Help & Support', 'description' => 'Get help with platform features and technical issues', 'color' => 'red'],
            ['name' => 'Announcements', 'description' => 'Important announcements and updates', 'color' => 'indigo', 'is_private' => true],
        ];

        foreach ($generalCategories as $index => $categoryData) {
            ForumCategory::firstOrCreate([
                'name' => $categoryData['name'],
            ], array_merge($categoryData, [
                'slug' => $this->generateUniqueSlug($categoryData['name']),
                'is_active' => true,
                'sort_order' => 1000 + $index,
            ]));
        }
    }

    private function generateUniqueSlug($name, $attempt = 0)
    {
        $baseSlug = Str::slug($name);
        $slug = $attempt > 0 ? $baseSlug.'-'.$attempt : $baseSlug;

        if (ForumCategory::where('slug', $slug)->exists()) {
            return $this->generateUniqueSlug($name, $attempt + 1);
        }

        return $slug;
    }

    public function restoreNavigationState()
    {
        // If we have URL parameters, restore the state
        if ($this->selectedTopic) {
            $topic = ForumTopic::find($this->selectedTopic);
            if ($topic) {
                $this->selectedCategory = $topic->forum_category_id;
                $this->currentView = 'posts';
            }
        } elseif ($this->selectedCategory) {
            $this->currentView = 'topics';
        }
    }

    // Navigation Methods

    public function selectCategory($categoryId)
    {
        $category = ForumCategory::find($categoryId);
        if ($category && $this->canAccessCategory($category)) {
            $this->selectedCategory = $categoryId;
            $this->selectedTopic = null;
            $this->currentView = 'topics';
            $this->resetPage();
        }
    }

    private function canAccessCategory($category)
    {
        // Simple check - if category has canAccess method, use it, otherwise allow access
        if (method_exists($category, 'canAccess')) {
            return $category->canAccess(Auth::user());
        }

        return true;
    }

    public function backToCategories()
    {
        $this->reset(['selectedCategory', 'selectedTopic', 'search']);
        $this->currentView = 'categories';
    }

    public function backToTopics()
    {
        $this->reset(['selectedTopic', 'search']);
        $this->currentView = 'topics';
    }

    public function showCreateTopic()
    {
        $this->currentView = 'create-topic';
    }

    public function showCreatePost()
    {
        $this->currentView = 'create-post';
        $this->replyToPostId = null;
    }

    public function startReply($postId)
    {
        $this->replyToPostId = $postId;
        $this->currentView = 'create-post';
    }

    public function createTopic()
    {
        // Validate the input
        $this->validate([
            'newTopicTitle' => 'required|string|max:255',
            'newTopicContent' => 'required|string|min:10',
        ]);

        try {
            // Create the topic
            $topic = ForumTopic::create([
                'title' => $this->newTopicTitle,
                'slug' => Str::slug($this->newTopicTitle),
                'forum_category_id' => $this->selectedCategory,
                'user_id' => Auth::id(),
                'tags' => $this->newTopicTags ? explode(',', trim($this->newTopicTags)) : [],
                'academic_level_id' => $this->newTopicAcademicLevel ?: null,
                'academic_subject_id' => $this->newTopicAcademicSubject ?: null,
                'academic_topic_id' => $this->newTopicAcademicTopic ?: null,
                'study_group_id' => $this->newTopicStudyGroup ?: null,
                'referenced_book_id' => $this->newTopicReferencedBook ?: null,
                'last_activity_at' => now(),
                'is_pinned' => false,
                'is_locked' => false,
                'is_announcement' => false,
                'views_count' => 0,
            ]);

            // Create first post
            $post = ForumPost::create([
                'content' => $this->newTopicContent,
                'forum_topic_id' => $topic->id,
                'user_id' => Auth::id(),
                'is_approved' => true,
                'likes_count' => 0,
                'dislikes_count' => 0,
            ]);

            // Handle attachments if any
            if (! empty($this->newTopicAttachments)) {
                $this->handleAttachments($this->newTopicAttachments, $post);
            }

            // Handle mentions if any
            if ($this->newTopicContent) {
                $this->handleMentions($this->newTopicContent, $post);
            }

            $this->resetTopicForm();
            $this->selectTopic($topic->id);
            session()->flash('success', 'Topic created successfully!');

        } catch (\Exception $e) {
            \Log::error('Error creating topic: '.$e->getMessage());
            session()->flash('error', 'Failed to create topic. Please try again.');
        }
    }

    // CRUD Operations

    private function handleAttachments($attachments, $attachable)
    {
        if (empty($attachments)) {
            return;
        }

        foreach ($attachments as $attachment) {
            try {
                $path = $attachment->store('forum-attachments', 'public');

                ForumAttachment::create([
                    'attachable_type' => get_class($attachable),
                    'attachable_id' => $attachable->id,
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $attachment->getSize(),
                    'mime_type' => $attachment->getMimeType(),
                    'user_id' => Auth::id(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Error handling attachment: '.$e->getMessage());
            }
        }
    }

    private function handleMentions($content, $mentionable)
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $matches);

        if (! empty($matches[1])) {
            $usernames = array_unique($matches[1]);
            $users = User::whereIn('name', $usernames)->get();

            foreach ($users as $user) {
                try {
                    ForumMention::create([
                        'mentionable_type' => get_class($mentionable),
                        'mentionable_id' => $mentionable->id,
                        'mentioned_user_id' => $user->id,
                        'mentioning_user_id' => Auth::id(),
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error creating mention: '.$e->getMessage());
                }
            }
        }
    }

    // Reaction Methods

    private function resetTopicForm()
    {
        $this->reset([
            'newTopicTitle', 'newTopicContent', 'newTopicTags',
            'newTopicAcademicLevel', 'newTopicAcademicSubject',
            'newTopicAcademicTopic', 'newTopicStudyGroup',
            'newTopicReferencedBook', 'newTopicAttachments',
        ]);
    }

    public function selectTopic($topicId)
    {
        $topic = ForumTopic::find($topicId);
        if ($topic) {
            $topic->incrementViews();
            $this->selectedTopic = $topicId;
            $this->selectedCategory = $topic->forum_category_id;
            $this->currentView = 'posts';
            $this->resetPage();
        }
    }

    // Helper Methods

    public function createPost()
    {
        $this->validate(['newPostContent' => 'required|string|min:5']);

        try {
            $post = ForumPost::create([
                'content' => $this->newPostContent,
                'forum_topic_id' => $this->selectedTopic,
                'user_id' => Auth::id(),
                'parent_id' => $this->replyToPostId,
                'is_approved' => true,
                'likes_count' => 0,
                'dislikes_count' => 0,
            ]);

            // Handle attachments
            if (! empty($this->newPostAttachments)) {
                $this->handleAttachments($this->newPostAttachments, $post);
            }

            // Handle mentions
            if ($this->newPostContent) {
                $this->handleMentions($this->newPostContent, $post);
            }

            // Update topic activity
            $topic = ForumTopic::find($this->selectedTopic);
            if ($topic) {
                $topic->updateLastActivity();
            }

            $this->resetPostForm();
            $this->currentView = 'posts';
            session()->flash('success', 'Reply posted successfully!');

            // Refresh the page to show the new post
            $this->dispatch('postCreated');

        } catch (\Exception $e) {
            \Log::error('Error creating post: '.$e->getMessage());
            session()->flash('error', 'Failed to create post. Please try again.');
        }
    }

    private function resetPostForm()
    {
        $this->reset(['newPostContent', 'newPostAttachments', 'replyToPostId']);
    }

    public function toggleLike($postId)
    {
        $post = ForumPost::find($postId);
        if (! $post) {
            return;
        }

        $user = Auth::user();

        $existingReaction = ForumReaction::where([
            'reactable_type' => ForumPost::class,
            'reactable_id' => $postId,
            'user_id' => $user->id,
            'type' => 'like',
        ])->first();

        if ($existingReaction) {
            $existingReaction->delete();
            $post->decrement('likes_count');
        } else {
            // Remove dislike if exists
            ForumReaction::where([
                'reactable_type' => ForumPost::class,
                'reactable_id' => $postId,
                'user_id' => $user->id,
                'type' => 'dislike',
            ])->delete();

            ForumReaction::create([
                'reactable_type' => ForumPost::class,
                'reactable_id' => $postId,
                'user_id' => $user->id,
                'type' => 'like',
            ]);

            $post->increment('likes_count');
            if ($post->dislikes_count > 0) {
                $post->decrement('dislikes_count');
            }
        }

        // Dispatch event to refresh the like count in the UI
        $this->dispatch('postLiked', ['postId' => $postId]);
    }

    public function sharePost($postId)
    {
        $post = ForumPost::with('topic')->find($postId);
        if (! $post) {
            return;
        }

        $url = route('guests.forums').'?currentView=posts&selectedTopic='.$post->forum_topic_id;

        // For now, we'll copy to clipboard via JavaScript
        $this->dispatch('sharePost', [
            'url' => $url,
            'title' => $post->topic->title,
            'postId' => $postId,
        ]);
    }

    // Search and Filter Methods

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedFilterBy()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = [];

        switch ($this->currentView) {
            case 'categories':
                $data['categories'] = $this->getCategoriesData();
                break;

            case 'topics':
                $data['topics'] = $this->getTopicsData();
                $data['category'] = ForumCategory::find($this->selectedCategory);
                break;

            case 'posts':
            case 'create-post':
                $data['topic'] = ForumTopic::with(['user', 'category', 'attachments', 'referencedBook'])->find($this->selectedTopic);
                $data['posts'] = $this->getPostsData();
                break;

            case 'create-topic':
                $data['category'] = ForumCategory::find($this->selectedCategory);
                break;
        }

        return view('livewire.forums.DiscussionForumComponent', $data);
    }

    private function getCategoriesData()
    {
        return ForumCategory::where('is_active', true)
            ->where('is_private', false) // Simplified access check
            ->withCount(['topics', 'posts'])
            ->with(['latestPost.user', 'academicLevel', 'academicSubject'])
            ->orderBy('sort_order')
            ->get();
    }

    private function getTopicsData()
    {
        $query = ForumTopic::where('forum_category_id', $this->selectedCategory)
            ->with(['user', 'latestPost.user', 'academicLevel', 'academicSubject', 'referencedBook'])
            ->withCount('posts');

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        if ($this->academicLevelFilter) {
            $query->where('academic_level_id', $this->academicLevelFilter);
        }

        if ($this->academicSubjectFilter) {
            $query->where('academic_subject_id', $this->academicSubjectFilter);
        }

        switch ($this->sortBy) {
            case 'recent':
                $query->latest('last_activity_at');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->oldest('created_at');
                break;
        }

        return $query->paginate(20);
    }

    private function getPostsData()
    {
        return ForumPost::where('forum_topic_id', $this->selectedTopic)
            ->with(['user', 'attachments', 'mentions.mentionedUser', 'reactions'])
            ->withCount(['likes', 'dislikes'])
            ->oldest()
            ->paginate(20);
    }
}
