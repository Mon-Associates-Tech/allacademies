<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use App\Models\Author;
use Illuminate\Contracts\View\View;

class Community extends AppComponent
{
    public Author $author;

    public $activeTab = 'network';

    public $searchTerm = '';

    public $selectedGenre = 'all';

    public $selectedLocation = 'all';

    public $sortBy = 'newest';

    public $showCreatePostModal = false;

    public $showCreateEventModal = false;

    public $postContent = '';

    public $postTitle = '';

    public $eventTitle = '';

    public $eventDescription = '';

    public $eventDate = '';

    public $eventTime = '';

    public $eventLocation = '';

    public $eventType = 'virtual';

    public $selectedPost = null;

    public $replyContent = '';

    public $showReplyModal = false;

    public function mount(Author $author)
    {
        $this->author = $author;
    }

    public function render(): View
    {
        $data = [
            'communityStats' => $this->getCommunityStats(),
            'featuredAuthors' => $this->getFeaturedAuthors(),
            'recentPosts' => $this->getRecentPosts(),
            'upcomingEvents' => $this->getUpcomingEvents(),
            'discussions' => $this->getDiscussions(),
            'collaborations' => $this->getCollaborations(),
            'networkSuggestions' => $this->getNetworkSuggestions(),
            'myConnections' => $this->getMyConnections(),
        ];

        return view('livewire.authors.community', $data);
    }

    private function getCommunityStats()
    {
        return [
            'total_authors' => Author::count(),
            'active_discussions' => 156, // Mock data
            'upcoming_events' => 23,
            'new_members_this_month' => 45,
            'collaboration_projects' => 12,
            'knowledge_base_articles' => 89,
        ];
    }

    private function getFeaturedAuthors()
    {
        return Author::with(['user', 'books'])
            ->where('id', '!=', $this->author->id)
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }

    private function getRecentPosts()
    {
        // Mock data structure - replace with actual model
        return collect([
            [
                'id' => 1,
                'title' => 'Tips for Writing Compelling Characters',
                'content' => 'Character development is crucial for engaging storytelling. Here are some techniques I\'ve learned over the years...',
                'author' => ['name' => 'Sarah Johnson', 'avatar' => null],
                'likes' => 24,
                'comments' => 8,
                'created_at' => now()->subHours(2),
                'tags' => ['writing-tips', 'characters', 'fiction'],
            ],
            [
                'id' => 2,
                'title' => 'Self-Publishing Success Stories',
                'content' => 'Just hit 10,000 copies sold! Here\'s what I learned about marketing and distribution...',
                'author' => ['name' => 'Michael Chen', 'avatar' => null],
                'likes' => 56,
                'comments' => 15,
                'created_at' => now()->subHours(4),
                'tags' => ['self-publishing', 'marketing', 'success'],
            ],
            [
                'id' => 3,
                'title' => 'Overcoming Writer\'s Block',
                'content' => 'We all face creative blocks. Here are my go-to strategies for getting unstuck...',
                'author' => ['name' => 'Emma Davis', 'avatar' => null],
                'likes' => 18,
                'comments' => 6,
                'created_at' => now()->subHours(6),
                'tags' => ['writing-tips', 'creativity', 'productivity'],
            ],
        ]);
    }

    private function getUpcomingEvents()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Virtual Writing Workshop: Dialogue Mastery',
                'description' => 'Learn to write natural, engaging dialogue that brings your characters to life.',
                'date' => now()->addDays(3),
                'time' => '14:00',
                'type' => 'virtual',
                'attendees' => 45,
                'max_attendees' => 50,
                'host' => ['name' => 'Dr. Jennifer Smith', 'avatar' => null],
            ],
            [
                'id' => 2,
                'title' => 'Author Networking Meetup',
                'description' => 'Connect with fellow authors in your area for collaboration and support.',
                'date' => now()->addDays(7),
                'time' => '18:30',
                'type' => 'in-person',
                'location' => 'Downtown Library',
                'attendees' => 23,
                'max_attendees' => 30,
                'host' => ['name' => 'Local Authors Guild', 'avatar' => null],
            ],
        ]);
    }

    private function getDiscussions()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Best practices for book marketing in 2024',
                'replies' => 34,
                'last_activity' => now()->subMinutes(15),
                'starter' => ['name' => 'Alex Rodriguez', 'avatar' => null],
                'category' => 'Marketing',
            ],
            [
                'id' => 2,
                'title' => 'How to handle negative reviews professionally',
                'replies' => 28,
                'last_activity' => now()->subHours(1),
                'starter' => ['name' => 'Lisa Wang', 'avatar' => null],
                'category' => 'Publishing',
            ],
        ]);
    }

    private function getCollaborations()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Fantasy Anthology Project',
                'description' => 'Looking for 5-6 fantasy authors to contribute short stories to a themed anthology.',
                'spots_available' => 2,
                'total_spots' => 6,
                'deadline' => now()->addDays(30),
                'coordinator' => ['name' => 'Rachel Green', 'avatar' => null],
                'genres' => ['Fantasy', 'Short Stories'],
            ],
            [
                'id' => 2,
                'title' => 'Writing Critique Group',
                'description' => 'Weekly virtual meetup for constructive feedback on works in progress.',
                'spots_available' => 3,
                'total_spots' => 8,
                'deadline' => now()->addDays(7),
                'coordinator' => ['name' => 'Tom Wilson', 'avatar' => null],
                'genres' => ['All Genres'],
            ],
        ]);
    }

    private function getNetworkSuggestions()
    {
        return Author::with(['user', 'books'])
            ->where('id', '!=', $this->author->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    private function getMyConnections()
    {
        return collect([]); // Mock - implement actual connections logic
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function openCreatePostModal()
    {
        $this->showCreatePostModal = true;
        $this->postTitle = '';
        $this->postContent = '';
    }

    public function closeCreatePostModal()
    {
        $this->showCreatePostModal = false;
        $this->postTitle = '';
        $this->postContent = '';
    }

    public function createPost()
    {
        $this->validate([
            'postTitle' => 'required|min:5|max:200',
            'postContent' => 'required|min:20|max:2000',
        ]);

        // Create post logic here
        $this->closeCreatePostModal();
        $this->dispatch('post-created');
    }

    public function openCreateEventModal()
    {
        $this->showCreateEventModal = true;
        $this->resetEventForm();
    }

    public function closeCreateEventModal()
    {
        $this->showCreateEventModal = false;
        $this->resetEventForm();
    }

    private function resetEventForm()
    {
        $this->eventTitle = '';
        $this->eventDescription = '';
        $this->eventDate = '';
        $this->eventTime = '';
        $this->eventLocation = '';
        $this->eventType = 'virtual';
    }

    public function createEvent()
    {
        $this->validate([
            'eventTitle' => 'required|min:5|max:200',
            'eventDescription' => 'required|min:20|max:1000',
            'eventDate' => 'required|date|after:today',
            'eventTime' => 'required',
            'eventLocation' => 'required_if:eventType,in-person',
        ]);

        // Create event logic here
        $this->closeCreateEventModal();
        $this->dispatch('event-created');
    }

    public function likePost($postId)
    {
        // Implement like functionality
        $this->dispatch('post-liked', ['postId' => $postId]);
    }

    public function joinEvent($eventId)
    {
        // Implement join event functionality
        $this->dispatch('event-joined', ['eventId' => $eventId]);
    }

    public function connectWithAuthor($authorId)
    {
        // Implement connection functionality
        $this->dispatch('author-connected', ['authorId' => $authorId]);
    }

    public function joinCollaboration($collaborationId)
    {
        // Implement collaboration join functionality
        $this->dispatch('collaboration-joined', ['collaborationId' => $collaborationId]);
    }
}
