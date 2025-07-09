<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;

class Help extends AppComponent
{
    public $activeTab = 'knowledge-base';
    public $searchTerm = '';
    public $selectedCategory = 'all';
    public $selectedArticle = null;
    public $showContactModal = false;
    public $showFeedbackModal = false;
    public $showTutorialModal = false;
    public $selectedTutorial = null;

    // Contact Form
    public $contactName = '';
    public $contactEmail = '';
    public $contactSubject = '';
    public $contactMessage = '';
    public $contactPriority = 'medium';
    public $contactCategory = 'general';

    // Feedback Form
    public $feedbackType = 'bug';
    public $feedbackTitle = '';
    public $feedbackDescription = '';
    public $feedbackRating = 5;

    // Search and Filter
    public $searchResults = [];
    public $isSearching = false;

    public function mount()
    {
        $this->contactName = auth()->user()->name ?? '';
        $this->contactEmail = auth()->user()->email ?? '';
    }

    public function render(): View
    {
        $data = [
            'supportStats' => $this->getSupportStats(),
            'knowledgeBaseArticles' => $this->getKnowledgeBaseArticles(),
            'faqItems' => $this->getFaqItems(),
            'tutorials' => $this->getTutorials(),
            'quickHelp' => $this->getQuickHelp(),
            'recentUpdates' => $this->getRecentUpdates(),
            'categories' => $this->getCategories(),
            'popularArticles' => $this->getPopularArticles(),
            'systemStatus' => $this->getSystemStatus(),
        ];

        return view('livewire.authors.help', $data);
    }

    private function getSupportStats()
    {
        return [
            'total_articles' => 156,
            'tutorials_available' => 45,
            'avg_response_time' => '2 hours',
            'satisfaction_rate' => 97,
            'tickets_resolved' => 1250,
            'active_users' => 3400,
        ];
    }

    private function getKnowledgeBaseArticles()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Getting Started with Author Dashboard',
                'excerpt' => 'Learn how to navigate and use your author dashboard effectively.',
                'content' => 'Complete guide on using the author dashboard...',
                'category' => 'Getting Started',
                'reading_time' => '5 min',
                'helpful_count' => 234,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
                'tags' => ['dashboard', 'basics', 'navigation']
            ],
            [
                'id' => 2,
                'title' => 'Publishing Your First Book',
                'excerpt' => 'Step-by-step guide to publishing your book on our platform.',
                'content' => 'Complete publishing workflow...',
                'category' => 'Publishing',
                'reading_time' => '12 min',
                'helpful_count' => 567,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(1),
                'tags' => ['publishing', 'books', 'workflow']
            ],
            [
                'id' => 3,
                'title' => 'Managing Book Sales and Analytics',
                'excerpt' => 'Understand your sales data and analytics dashboard.',
                'content' => 'Analytics and sales management guide...',
                'category' => 'Analytics',
                'reading_time' => '8 min',
                'helpful_count' => 189,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(3),
                'tags' => ['analytics', 'sales', 'reporting']
            ],
            [
                'id' => 4,
                'title' => 'Setting Up Author Profile',
                'excerpt' => 'Create an engaging author profile that attracts readers.',
                'content' => 'Profile setup and optimization guide...',
                'category' => 'Profile',
                'reading_time' => '6 min',
                'helpful_count' => 312,
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(4),
                'tags' => ['profile', 'setup', 'optimization']
            ]
        ]);
    }

    private function getFaqItems()
    {
        return collect([
            [
                'id' => 1,
                'question' => 'How do I publish my book?',
                'answer' => 'To publish your book, navigate to the "My Books" section, click "Add New Book", fill in the required information, upload your manuscript, and submit for review.',
                'category' => 'Publishing',
                'helpful_count' => 45
            ],
            [
                'id' => 2,
                'question' => 'What file formats are supported?',
                'answer' => 'We support PDF, EPUB, MOBI, and DOC/DOCX formats for manuscripts. For covers, we accept JPG, PNG, and PDF files.',
                'category' => 'Technical',
                'helpful_count' => 38
            ],
            [
                'id' => 3,
                'question' => 'How do I track my book sales?',
                'answer' => 'You can track your sales through the Analytics section in your dashboard. It provides real-time data on sales, revenue, and reader engagement.',
                'category' => 'Analytics',
                'helpful_count' => 52
            ],
            [
                'id' => 4,
                'question' => 'Can I update my book after publishing?',
                'answer' => 'Yes, you can update your book content, cover, or details at any time. Updates will be reflected within 24 hours.',
                'category' => 'Publishing',
                'helpful_count' => 67
            ],
            [
                'id' => 5,
                'question' => 'How do I set up my author profile?',
                'answer' => 'Go to Profile Settings, add your biography, photo, social media links, and other relevant information to create an engaging author profile.',
                'category' => 'Profile',
                'helpful_count' => 29
            ]
        ]);
    }

    private function getTutorials()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Author Dashboard Overview',
                'description' => 'Complete walkthrough of your author dashboard and its features.',
                'duration' => '15 min',
                'difficulty' => 'Beginner',
                'thumbnail' => '/images/tutorial-dashboard.jpg',
                'video_url' => 'https://example.com/tutorial1',
                'steps' => 8,
                'category' => 'Getting Started'
            ],
            [
                'id' => 2,
                'title' => 'Book Publishing Workflow',
                'description' => 'Learn the complete process of publishing a book from start to finish.',
                'duration' => '25 min',
                'difficulty' => 'Intermediate',
                'thumbnail' => '/images/tutorial-publishing.jpg',
                'video_url' => 'https://example.com/tutorial2',
                'steps' => 12,
                'category' => 'Publishing'
            ],
            [
                'id' => 3,
                'title' => 'Marketing Your Books',
                'description' => 'Effective strategies for promoting and marketing your published books.',
                'duration' => '20 min',
                'difficulty' => 'Intermediate',
                'thumbnail' => '/images/tutorial-marketing.jpg',
                'video_url' => 'https://example.com/tutorial3',
                'steps' => 10,
                'category' => 'Marketing'
            ],
            [
                'id' => 4,
                'title' => 'Analytics and Reporting',
                'description' => 'Understanding your book performance through analytics and reports.',
                'duration' => '18 min',
                'difficulty' => 'Intermediate',
                'thumbnail' => '/images/tutorial-analytics.jpg',
                'video_url' => 'https://example.com/tutorial4',
                'steps' => 9,
                'category' => 'Analytics'
            ]
        ]);
    }

    private function getQuickHelp()
    {
        return collect([
            [
                'icon' => 'book-open',
                'title' => 'Quick Start Guide',
                'description' => 'Get started with your author account',
                'action' => 'View Guide',
                'link' => '#'
            ],
            [
                'icon' => 'upload',
                'title' => 'Upload Your Book',
                'description' => 'Step-by-step book upload process',
                'action' => 'Start Upload',
                'link' => '#'
            ],
            [
                'icon' => 'chart-bar',
                'title' => 'View Analytics',
                'description' => 'Track your book performance',
                'action' => 'View Analytics',
                'link' => '#'
            ],
            [
                'icon' => 'user-circle',
                'title' => 'Setup Profile',
                'description' => 'Complete your author profile',
                'action' => 'Edit Profile',
                'link' => '#'
            ]
        ]);
    }

    private function getRecentUpdates()
    {
        return collect([
            [
                'title' => 'New Analytics Dashboard Released',
                'description' => 'Enhanced reporting with real-time sales data and reader insights.',
                'date' => now()->subDays(2),
                'type' => 'feature'
            ],
            [
                'title' => 'Mobile App Now Available',
                'description' => 'Download our mobile app for iOS and Android to manage your books on the go.',
                'date' => now()->subDays(5),
                'type' => 'announcement'
            ],
            [
                'title' => 'Improved File Upload Speed',
                'description' => 'Book uploads are now 50% faster with our new infrastructure.',
                'date' => now()->subDays(8),
                'type' => 'improvement'
            ]
        ]);
    }

    private function getCategories()
    {
        return collect([
            'all' => 'All Categories',
            'getting-started' => 'Getting Started',
            'publishing' => 'Publishing',
            'analytics' => 'Analytics',
            'profile' => 'Profile',
            'marketing' => 'Marketing',
            'technical' => 'Technical',
            'billing' => 'Billing',
            'account' => 'Account'
        ]);
    }

    private function getPopularArticles()
    {
        return collect([
            [
                'title' => 'Publishing Your First Book',
                'views' => 2340,
                'helpful_count' => 567
            ],
            [
                'title' => 'Setting Up Author Profile',
                'views' => 1890,
                'helpful_count' => 312
            ],
            [
                'title' => 'Managing Book Sales',
                'views' => 1650,
                'helpful_count' => 189
            ]
        ]);
    }

    private function getSystemStatus()
    {
        return [
            'overall_status' => 'operational',
            'services' => [
                'website' => 'operational',
                'api' => 'operational',
                'file_uploads' => 'operational',
                'payments' => 'operational',
                'analytics' => 'operational'
            ],
            'last_updated' => now()->subMinutes(5)
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function selectArticle($articleId)
    {
        $this->selectedArticle = $this->getKnowledgeBaseArticles()->firstWhere('id', $articleId);
    }

    public function selectTutorial($tutorialId)
    {
        $this->selectedTutorial = $this->getTutorials()->firstWhere('id', $tutorialId);
        $this->showTutorialModal = true;
    }

    public function openContactModal()
    {
        $this->showContactModal = true;
    }

    public function closeContactModal()
    {
        $this->showContactModal = false;
        $this->resetContactForm();
    }

    public function openFeedbackModal()
    {
        $this->showFeedbackModal = true;
    }

    public function closeFeedbackModal()
    {
        $this->showFeedbackModal = false;
        $this->resetFeedbackForm();
    }

    public function closeTutorialModal()
    {
        $this->showTutorialModal = false;
        $this->selectedTutorial = null;
    }

    public function submitContact()
    {
        $this->validate([
            'contactName' => 'required|min:2|max:100',
            'contactEmail' => 'required|email',
            'contactSubject' => 'required|min:5|max:200',
            'contactMessage' => 'required|min:20|max:2000',
            'contactPriority' => 'required|in:low,medium,high,urgent',
            'contactCategory' => 'required',
        ]);

        // Send support ticket
        $this->closeContactModal();
        session()->flash('message', 'Your support request has been submitted. We\'ll get back to you soon!');
    }

    public function submitFeedback()
    {
        $this->validate([
            'feedbackTitle' => 'required|min:5|max:200',
            'feedbackDescription' => 'required|min:20|max:1000',
            'feedbackType' => 'required|in:bug,feature,improvement,other',
            'feedbackRating' => 'required|integer|min:1|max:5',
        ]);

        // Process feedback
        $this->closeFeedbackModal();
        session()->flash('message', 'Thank you for your feedback! It helps us improve our platform.');
    }

    public function markArticleHelpful($articleId)
    {
        // Mark article as helpful
        $this->dispatch('article-marked-helpful', ['articleId' => $articleId]);
    }

    public function markFaqHelpful($faqId)
    {
        // Mark FAQ as helpful
        $this->dispatch('faq-marked-helpful', ['faqId' => $faqId]);
    }

    public function performSearch()
    {
        if (empty($this->searchTerm)) {
            $this->searchResults = [];
            return;
        }

        $this->isSearching = true;

        // Simulate search delay
        sleep(1);

        // Mock search results
        $this->searchResults = collect([
            [
                'type' => 'article',
                'title' => 'Getting Started with Author Dashboard',
                'excerpt' => 'Learn how to navigate and use your author dashboard effectively.',
                'url' => '#'
            ],
            [
                'type' => 'faq',
                'title' => 'How do I publish my book?',
                'excerpt' => 'To publish your book, navigate to the "My Books" section...',
                'url' => '#'
            ]
        ]);

        $this->isSearching = false;
    }

    private function resetContactForm()
    {
        $this->contactSubject = '';
        $this->contactMessage = '';
        $this->contactPriority = 'medium';
        $this->contactCategory = 'general';
    }

    private function resetFeedbackForm()
    {
        $this->feedbackType = 'bug';
        $this->feedbackTitle = '';
        $this->feedbackDescription = '';
        $this->feedbackRating = 5;
    }

    public function updatedSearchTerm()
    {
        $this->performSearch();
    }
}
