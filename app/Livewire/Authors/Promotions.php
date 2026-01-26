<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use Illuminate\Contracts\View\View;

class Promotions extends AppComponent
{
    public $activeTab = 'campaigns';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $selectedPromotion = null;

    // Campaign Form Data
    public $campaignTitle = '';

    public $campaignDescription = '';

    public $campaignType = 'discount';

    public $discountType = 'percentage';

    public $discountValue = '';

    public $startDate = '';

    public $endDate = '';

    public $targetAudience = 'all';

    public $selectedBooks = [];

    public $maxUsage = '';

    public $minPurchaseAmount = '';

    public $promoCode = '';

    public $campaignBudget = '';

    public $campaignStatus = 'active';

    // Filters
    public $statusFilter = 'all';

    public $typeFilter = 'all';

    public $searchTerm = '';

    // Social Media
    public $socialPlatform = 'facebook';

    public $socialContent = '';

    public $socialScheduleDate = '';

    public function mount()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addDays(30)->format('Y-m-d');
        $this->promoCode = 'PROMO'.strtoupper(substr(md5(time()), 0, 6));
    }

    public function render(): View
    {
        $data = [
            'campaigns' => $this->getCampaigns(),
            'promotionStats' => $this->getPromotionStats(),
            'topPerformingCampaigns' => $this->getTopPerformingCampaigns(),
            'recentActivity' => $this->getRecentActivity(),
            'socialMetrics' => $this->getSocialMetrics(),
            'marketingTools' => $this->getMarketingTools(),
            'availableBooks' => $this->getAvailableBooks(),
            'campaignTemplates' => $this->getCampaignTemplates(),
            'upcomingCampaigns' => $this->getUpcomingCampaigns(),
            'expiredCampaigns' => $this->getExpiredCampaigns(),
            'audienceInsights' => $this->getAudienceInsights(),
            'competitorAnalysis' => $this->getCompetitorAnalysis(),
        ];

        return view('livewire.authors.promotions', $data);
    }

    private function getCampaigns()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Summer Reading Sale',
                'description' => '25% off all fiction books for the summer season',
                'type' => 'discount',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'promo_code' => 'SUMMER25',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'status' => 'active',
                'usage_count' => 87,
                'max_usage' => 200,
                'revenue_generated' => 1250.50,
                'books_sold' => 43,
                'target_audience' => 'all',
                'budget' => 500,
                'spent' => 234.50,
                'conversion_rate' => 12.5,
                'books' => ['The Great Adventure', 'Mystery of the Old House'],
            ],
            [
                'id' => 2,
                'title' => 'New Release Promotion',
                'description' => 'Free shipping on latest book releases',
                'type' => 'free_shipping',
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'promo_code' => 'NEWBOOK',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(5),
                'status' => 'active',
                'usage_count' => 156,
                'max_usage' => 300,
                'revenue_generated' => 890.75,
                'books_sold' => 67,
                'target_audience' => 'subscribers',
                'budget' => 300,
                'spent' => 198.25,
                'conversion_rate' => 8.7,
                'books' => ['Journey to Tomorrow'],
            ],
            [
                'id' => 3,
                'title' => 'Back to School Special',
                'description' => 'Educational books at 30% discount',
                'type' => 'discount',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'promo_code' => 'SCHOOL30',
                'start_date' => now()->subDays(20),
                'end_date' => now()->subDays(1),
                'status' => 'expired',
                'usage_count' => 234,
                'max_usage' => 500,
                'revenue_generated' => 2100.25,
                'books_sold' => 128,
                'target_audience' => 'students',
                'budget' => 800,
                'spent' => 756.80,
                'conversion_rate' => 15.3,
                'books' => ['Study Guide Pro', 'Mathematics Made Easy'],
            ],
            [
                'id' => 4,
                'title' => 'Holiday Bundle Deal',
                'description' => 'Buy 2 books get 1 free during holidays',
                'type' => 'bundle',
                'discount_type' => 'buy_x_get_y',
                'discount_value' => 0,
                'promo_code' => 'HOLIDAY3',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(60),
                'status' => 'scheduled',
                'usage_count' => 0,
                'max_usage' => 150,
                'revenue_generated' => 0,
                'books_sold' => 0,
                'target_audience' => 'all',
                'budget' => 1000,
                'spent' => 0,
                'conversion_rate' => 0,
                'books' => ['Holiday Stories', 'Winter Tales', 'Christmas Magic'],
            ],
        ]);
    }

    private function getPromotionStats()
    {
        return [
            'total_campaigns' => 12,
            'active_campaigns' => 3,
            'total_revenue' => 8450.75,
            'total_conversions' => 456,
            'average_conversion_rate' => 11.8,
            'total_budget' => 5000,
            'total_spent' => 3240.50,
            'roi' => 161.2,
            'top_performing_promo' => 'SUMMER25',
            'this_month_revenue' => 2340.25,
            'last_month_revenue' => 1980.50,
            'growth_rate' => 18.2,
        ];
    }

    private function getTopPerformingCampaigns()
    {
        return collect([
            [
                'title' => 'Summer Reading Sale',
                'promo_code' => 'SUMMER25',
                'conversion_rate' => 12.5,
                'revenue' => 1250.50,
                'usage_count' => 87,
                'roi' => 233.5,
            ],
            [
                'title' => 'Back to School Special',
                'promo_code' => 'SCHOOL30',
                'conversion_rate' => 15.3,
                'revenue' => 2100.25,
                'usage_count' => 234,
                'roi' => 177.4,
            ],
            [
                'title' => 'New Release Promotion',
                'promo_code' => 'NEWBOOK',
                'conversion_rate' => 8.7,
                'revenue' => 890.75,
                'usage_count' => 156,
                'roi' => 149.2,
            ],
        ]);
    }

    private function getRecentActivity()
    {
        return collect([
            [
                'type' => 'campaign_created',
                'title' => 'Holiday Bundle Deal campaign created',
                'timestamp' => now()->subHours(2),
                'icon' => 'plus-circle',
                'color' => 'green',
            ],
            [
                'type' => 'promo_used',
                'title' => 'SUMMER25 used by customer John Doe',
                'timestamp' => now()->subHours(4),
                'icon' => 'shopping-cart',
                'color' => 'blue',
            ],
            [
                'type' => 'campaign_milestone',
                'title' => 'New Release Promotion reached 150 uses',
                'timestamp' => now()->subHours(6),
                'icon' => 'trending-up',
                'color' => 'purple',
            ],
            [
                'type' => 'budget_alert',
                'title' => 'Back to School Special budget 90% used',
                'timestamp' => now()->subHours(12),
                'icon' => 'exclamation-triangle',
                'color' => 'orange',
            ],
        ]);
    }

    private function getSocialMetrics()
    {
        return [
            'total_reach' => 15600,
            'total_engagement' => 1240,
            'total_clicks' => 340,
            'total_shares' => 89,
            'engagement_rate' => 7.9,
            'click_through_rate' => 2.2,
            'top_platform' => 'Facebook',
            'recent_posts' => 12,
            'scheduled_posts' => 3,
            'platforms' => [
                'facebook' => ['reach' => 6500, 'engagement' => 520, 'clicks' => 145],
                'twitter' => ['reach' => 4200, 'engagement' => 380, 'clicks' => 98],
                'instagram' => ['reach' => 3500, 'engagement' => 280, 'clicks' => 76],
                'linkedin' => ['reach' => 1400, 'engagement' => 60, 'clicks' => 21],
            ],
        ];
    }

    private function getMarketingTools()
    {
        return collect([
            [
                'name' => 'Email Campaign Builder',
                'description' => 'Create beautiful email campaigns to promote your books',
                'icon' => 'mail',
                'color' => 'blue',
                'status' => 'available',
            ],
            [
                'name' => 'Social Media Scheduler',
                'description' => 'Schedule posts across multiple social platforms',
                'icon' => 'calendar',
                'color' => 'green',
                'status' => 'available',
            ],
            [
                'name' => 'Landing Page Creator',
                'description' => 'Build custom landing pages for your promotions',
                'icon' => 'globe',
                'color' => 'purple',
                'status' => 'available',
            ],
            [
                'name' => 'Analytics Dashboard',
                'description' => 'Track performance across all marketing channels',
                'icon' => 'chart-bar',
                'color' => 'orange',
                'status' => 'available',
            ],
            [
                'name' => 'Review Management',
                'description' => 'Manage and respond to book reviews',
                'icon' => 'star',
                'color' => 'yellow',
                'status' => 'coming_soon',
            ],
            [
                'name' => 'Influencer Network',
                'description' => 'Connect with book influencers and reviewers',
                'icon' => 'users',
                'color' => 'pink',
                'status' => 'coming_soon',
            ],
        ]);
    }

    private function getAvailableBooks()
    {
        return collect([
            ['id' => 1, 'title' => 'The Great Adventure', 'price' => 19.99],
            ['id' => 2, 'title' => 'Mystery of the Old House', 'price' => 24.99],
            ['id' => 3, 'title' => 'Journey to Tomorrow', 'price' => 29.99],
            ['id' => 4, 'title' => 'Study Guide Pro', 'price' => 34.99],
            ['id' => 5, 'title' => 'Mathematics Made Easy', 'price' => 27.99],
        ]);
    }

    private function getCampaignTemplates()
    {
        return collect([
            [
                'name' => 'Seasonal Sale',
                'description' => 'Perfect for holiday and seasonal promotions',
                'discount_type' => 'percentage',
                'suggested_discount' => 20,
                'duration' => 14,
                'category' => 'discount',
            ],
            [
                'name' => 'New Release Launch',
                'description' => 'Promote your latest book release',
                'discount_type' => 'fixed',
                'suggested_discount' => 5,
                'duration' => 7,
                'category' => 'launch',
            ],
            [
                'name' => 'Bundle Deal',
                'description' => 'Sell multiple books together',
                'discount_type' => 'bundle',
                'suggested_discount' => 15,
                'duration' => 21,
                'category' => 'bundle',
            ],
            [
                'name' => 'Flash Sale',
                'description' => 'Limited time high-discount promotion',
                'discount_type' => 'percentage',
                'suggested_discount' => 40,
                'duration' => 3,
                'category' => 'flash',
            ],
        ]);
    }

    private function getUpcomingCampaigns()
    {
        return collect([
            [
                'title' => 'Holiday Bundle Deal',
                'start_date' => now()->addDays(30),
                'type' => 'bundle',
                'budget' => 1000,
            ],
            [
                'title' => 'New Year Sale',
                'start_date' => now()->addDays(45),
                'type' => 'discount',
                'budget' => 750,
            ],
        ]);
    }

    private function getExpiredCampaigns()
    {
        return collect([
            [
                'title' => 'Back to School Special',
                'end_date' => now()->subDays(1),
                'final_usage' => 234,
                'final_revenue' => 2100.25,
                'roi' => 177.4,
            ],
        ]);
    }

    private function getAudienceInsights()
    {
        return [
            'total_audience' => 8400,
            'segments' => [
                'subscribers' => 3200,
                'previous_customers' => 2800,
                'new_visitors' => 1600,
                'students' => 800,
            ],
            'demographics' => [
                'age_18_24' => 15,
                'age_25_34' => 35,
                'age_35_44' => 30,
                'age_45_54' => 15,
                'age_55_plus' => 5,
            ],
            'interests' => [
                'fiction' => 45,
                'non_fiction' => 35,
                'educational' => 25,
                'self_help' => 20,
            ],
        ];
    }

    private function getCompetitorAnalysis()
    {
        return [
            'average_discount' => 18.5,
            'common_promo_types' => ['percentage_discount', 'free_shipping', 'bundle_deals'],
            'peak_promotion_times' => ['Black Friday', 'Back to School', 'Holiday Season'],
            'competitor_insights' => [
                'Most competitors offer 15-25% discounts',
                'Bundle deals are increasingly popular',
                'Free shipping threshold is typically $25-30',
            ],
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($promotionId)
    {
        $this->selectedPromotion = $this->getCampaigns()->firstWhere('id', $promotionId);
        if ($this->selectedPromotion) {
            $this->populateForm($this->selectedPromotion);
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedPromotion = null;
        $this->resetForm();
    }

    public function createCampaign()
    {
        $this->validate([
            'campaignTitle' => 'required|min:3|max:100',
            'campaignDescription' => 'required|min:10|max:500',
            'campaignType' => 'required|in:discount,free_shipping,bundle,buy_x_get_y',
            'discountValue' => 'required|numeric|min:0',
            'startDate' => 'required|date|after_or_equal:today',
            'endDate' => 'required|date|after:startDate',
            'promoCode' => 'required|min:3|max:20|unique:promotions,code',
            'maxUsage' => 'nullable|integer|min:1',
            'campaignBudget' => 'nullable|numeric|min:0',
        ]);

        // Create campaign logic here
        $this->closeCreateModal();
        session()->flash('message', 'Campaign created successfully!');
    }

    public function updateCampaign()
    {
        $this->validate([
            'campaignTitle' => 'required|min:3|max:100',
            'campaignDescription' => 'required|min:10|max:500',
            'campaignType' => 'required|in:discount,free_shipping,bundle,buy_x_get_y',
            'discountValue' => 'required|numeric|min:0',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'maxUsage' => 'nullable|integer|min:1',
            'campaignBudget' => 'nullable|numeric|min:0',
        ]);

        // Update campaign logic here
        $this->closeEditModal();
        session()->flash('message', 'Campaign updated successfully!');
    }

    public function deleteCampaign($campaignId)
    {
        // Delete campaign logic here
        session()->flash('message', 'Campaign deleted successfully!');
    }

    public function duplicateCampaign($campaignId)
    {
        $campaign = $this->getCampaigns()->firstWhere('id', $campaignId);
        if ($campaign) {
            $this->populateForm($campaign);
            $this->campaignTitle = $campaign['title'].' (Copy)';
            $this->promoCode = 'PROMO'.strtoupper(substr(md5(time()), 0, 6));
            $this->showCreateModal = true;
        }
    }

    public function pauseCampaign($campaignId)
    {
        // Pause campaign logic here
        session()->flash('message', 'Campaign paused successfully!');
    }

    public function resumeCampaign($campaignId)
    {
        // Resume campaign logic here
        session()->flash('message', 'Campaign resumed successfully!');
    }

    public function generatePromoCode()
    {
        $this->promoCode = 'PROMO'.strtoupper(substr(md5(time()), 0, 6));
    }

    public function useTemplate($templateName)
    {
        $template = $this->getCampaignTemplates()->firstWhere('name', $templateName);
        if ($template) {
            $this->campaignType = $template['category'];
            $this->discountType = $template['discount_type'];
            $this->discountValue = $template['suggested_discount'];
            $this->endDate = now()->addDays($template['duration'])->format('Y-m-d');
            $this->campaignDescription = $template['description'];
        }
    }

    private function resetForm()
    {
        $this->campaignTitle = '';
        $this->campaignDescription = '';
        $this->campaignType = 'discount';
        $this->discountType = 'percentage';
        $this->discountValue = '';
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addDays(30)->format('Y-m-d');
        $this->targetAudience = 'all';
        $this->selectedBooks = [];
        $this->maxUsage = '';
        $this->minPurchaseAmount = '';
        $this->promoCode = 'PROMO'.strtoupper(substr(md5(time()), 0, 6));
        $this->campaignBudget = '';
        $this->campaignStatus = 'active';
    }

    private function populateForm($campaign)
    {
        $this->campaignTitle = $campaign['title'];
        $this->campaignDescription = $campaign['description'];
        $this->campaignType = $campaign['type'];
        $this->discountType = $campaign['discount_type'];
        $this->discountValue = $campaign['discount_value'];
        $this->startDate = $campaign['start_date']->format('Y-m-d');
        $this->endDate = $campaign['end_date']->format('Y-m-d');
        $this->targetAudience = $campaign['target_audience'];
        $this->maxUsage = $campaign['max_usage'];
        $this->promoCode = $campaign['promo_code'];
        $this->campaignBudget = $campaign['budget'];
        $this->campaignStatus = $campaign['status'];
    }

    public function updatedCampaignType()
    {
        // Reset discount type when campaign type changes
        if ($this->campaignType === 'free_shipping') {
            $this->discountType = 'fixed';
            $this->discountValue = 0;
        } elseif ($this->campaignType === 'bundle') {
            $this->discountType = 'bundle';
        }
    }
}
