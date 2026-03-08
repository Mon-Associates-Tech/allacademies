<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class Settings extends AppComponent
{
    public $activeTab = 'profile';

    // Profile Settings
    public $name = '';

    public $email = '';

    public $pen_name = '';

    public $biography = '';

    public $website = '';

    public $social_links = [];

    public $writing_experience = '';

    public $education = '';

    public $awards = '';

    public $author_statement = '';

    public $avatar = '';

    public $phone = '';

    public $location = '';

    public $birth_date = '';

    public $genres = [];

    public $languages = [];

    // Security Settings
    public $current_password = '';

    public $new_password = '';

    public $new_password_confirmation = '';

    public $two_factor_enabled = false;

    public $backup_codes = [];

    public $active_sessions = [];

    // Notification Settings
    public $email_notifications = [];

    public $push_notifications = [];

    public $sms_notifications = [];

    public $notification_schedule = [];

    // Privacy Settings
    public $profile_visibility = 'public';

    public $show_email = false;

    public $show_phone = false;

    public $show_location = false;

    public $allow_contact = true;

    public $data_sharing = [];

    // Publishing Settings
    public $default_book_visibility = 'public';

    public $auto_publish = false;

    public $review_required = true;

    public $royalty_rate = 70;

    public $payment_method = 'bank_transfer';

    public $payment_details = [];

    public $tax_information = [];

    // Marketing Settings
    public $marketing_consent = true;

    public $author_newsletter = true;

    public $promotional_emails = true;

    public $social_media_sharing = true;

    public $analytics_tracking = true;

    // Account Settings
    public $account_status = 'active';

    public $subscription_tier = 'basic';

    public $subscription_expires = null;

    public $storage_used = 0;

    public $storage_limit = 1000; // MB

    public $api_access = false;

    public $api_key = '';

    // Temporary file upload
    public $new_avatar;

    public function mount()
    {
        $this->loadCurrentSettings();
    }

    public function render(): View
    {
        $data = [
            'author' => Auth::user()->author,
            'user' => Auth::user(),
            'availableGenres' => $this->getAvailableGenres(),
            'availableLanguages' => $this->getAvailableLanguages(),
            'accountStats' => $this->getAccountStats(),
            'securityLogs' => $this->getSecurityLogs(),
            'storageBreakdown' => $this->getStorageBreakdown(),
            'paymentHistory' => $this->getPaymentHistory(),
            'subscriptionPlans' => $this->getSubscriptionPlans(),
            'apiUsage' => $this->getApiUsage(),
            'exportOptions' => $this->getExportOptions(),
            'integrations' => $this->getIntegrations(),
            'themes' => $this->getThemes(),
            'supportTickets' => $this->getSupportTickets(),
            'revenueStats' => $this->getRevenueStats(), // Add this
        ];

        return view('livewire.authors.settings', $data);
    }

    /**
     * Get revenue statistics for billing tab
     */
    private function getRevenueStats()
    {
        $author = Auth::user()->author;

        if (! $author) {
            return [
                'this_month_revenue' => 0,
                'this_month_payments' => 0,
                'total_revenue' => 0,
                'total_payments' => 0,
                'average_per_sale' => 0,
                'has_subaccount' => false,
            ];
        }

        // Get all payments for this author
        $allPayments = Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) use ($author) {
                $query->where('author_id', $author->id);
            })
            ->get();

        // Calculate total revenue (98% author share)
        $totalRevenue = $allPayments->sum(function ($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

        // This month's payments
        $thisMonthPayments = Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) use ($author) {
                $query->where('author_id', $author->id);
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();

        $thisMonthRevenue = $thisMonthPayments->sum(function ($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

        // Average per sale
        $totalPaymentCount = $allPayments->count();
        $averagePerSale = $totalPaymentCount > 0 ? $totalRevenue / $totalPaymentCount : 0;

        return [
            'this_month_revenue' => $thisMonthRevenue,
            'this_month_payments' => $thisMonthPayments->count(),
            'total_revenue' => $totalRevenue,
            'total_payments' => $totalPaymentCount,
            'average_per_sale' => $averagePerSale,
            'has_subaccount' => $author->subaccount !== null,
        ];
    }

    private function loadCurrentSettings(): void
    {
        $user = Auth::user();
        $author = $user->author;

        // Load user data
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatar = $user->avatar;

        // Load author data
        if ($author) {
            $this->pen_name = $author->pen_name ?? '';
            $this->biography = $author->biography ?? '';
            $this->website = $author->website ?? '';
            $this->social_links = $author->social_links ? json_decode($author->social_links, true) : [];
            $this->writing_experience = $author->writing_experience ?? '';
            $this->education = $author->education ?? '';
            $this->awards = $author->awards ?? '';
            $this->author_statement = $author->author_statement ?? '';
        }

        // Initialize social links if empty
        if (empty($this->social_links)) {
            $this->social_links = [
                'facebook' => '',
                'twitter' => '',
                'instagram' => '',
                'linkedin' => '',
                'youtube' => '',
                'tiktok' => '',
                'goodreads' => '',
                'amazon' => '',
            ];
        }

        // Load notification settings
        $this->email_notifications = [
            'new_review' => true,
            'new_order' => true,
            'payment_received' => true,
            'monthly_report' => true,
            'system_updates' => false,
            'promotional' => false,
        ];

        $this->push_notifications = [
            'new_review' => true,
            'new_order' => true,
            'payment_received' => false,
            'system_updates' => false,
        ];

        // Load privacy settings
        $this->profile_visibility = 'public';
        $this->show_email = false;
        $this->show_phone = false;
        $this->show_location = false;
        $this->allow_contact = true;

        // Load publishing settings
        $this->default_book_visibility = 'public';
        $this->auto_publish = false;
        $this->review_required = true;
        $this->royalty_rate = 70;
        $this->payment_method = 'bank_transfer';

        // Load account settings
        $this->account_status = 'active';
        $this->subscription_tier = 'basic';
        $this->storage_used = 250; // MB
        $this->storage_limit = 1000; // MB

        // Initialize other arrays
        $this->genres = ['Fiction', 'Mystery'];
        $this->languages = ['English', 'Spanish'];
        $this->payment_details = ['account_number' => '', 'routing_number' => '', 'account_name' => ''];
        $this->tax_information = ['tax_id' => '', 'tax_country' => '', 'tax_state' => ''];
        $this->data_sharing = ['analytics' => true, 'marketing' => false, 'research' => false];
        $this->notification_schedule = ['start_time' => '09:00', 'end_time' => '17:00', 'timezone' => 'UTC'];
    }

    private function getAvailableGenres()
    {
        return [
            'Fiction', 'Non-Fiction', 'Mystery', 'Thriller', 'Romance', 'Fantasy',
            'Science Fiction', 'Biography', 'History', 'Self-Help', 'Business',
            'Children\'s Books', 'Young Adult', 'Poetry', 'Drama', 'Comedy',
            'Horror', 'Adventure', 'Crime', 'Educational', 'Health', 'Travel',
        ];
    }

    private function getAvailableLanguages()
    {
        return [
            'English', 'Spanish', 'French', 'German', 'Italian', 'Portuguese',
            'Chinese', 'Japanese', 'Korean', 'Arabic', 'Russian', 'Hindi',
            'Dutch', 'Swedish', 'Norwegian', 'Danish', 'Finnish', 'Polish',
        ];
    }

    private function getAccountStats()
    {
        return [
            'total_books' => 12,
            'total_sales' => 1248,
            'total_revenue' => 15420.50,
            'total_reviews' => 342,
            'average_rating' => 4.2,
            'followers' => 856,
            'profile_views' => 12340,
            'downloads' => 3456,
            'member_since' => '2023-01-15',
            'last_login' => now()->subHours(2),
            'books_published_this_month' => 2,
            'revenue_this_month' => 2340.75,
        ];
    }

    private function getSecurityLogs()
    {
        return collect([
            [
                'action' => 'Password Changed',
                'timestamp' => now()->subDays(5),
                'ip_address' => '192.168.1.100',
                'device' => 'Chrome on Windows',
                'location' => 'New York, NY',
                'status' => 'success',
            ],
            [
                'action' => 'Login',
                'timestamp' => now()->subHours(2),
                'ip_address' => '192.168.1.100',
                'device' => 'Chrome on Windows',
                'location' => 'New York, NY',
                'status' => 'success',
            ],
            [
                'action' => 'Failed Login Attempt',
                'timestamp' => now()->subDays(10),
                'ip_address' => '203.0.113.42',
                'device' => 'Unknown',
                'location' => 'Unknown',
                'status' => 'failed',
            ],
            [
                'action' => 'Profile Updated',
                'timestamp' => now()->subDays(15),
                'ip_address' => '192.168.1.100',
                'device' => 'Safari on iPhone',
                'location' => 'New York, NY',
                'status' => 'success',
            ],
        ]);
    }

    private function getStorageBreakdown()
    {
        return [
            'total_used' => 250,
            'total_limit' => 1000,
            'breakdown' => [
                'books' => ['size' => 150, 'count' => 12],
                'images' => ['size' => 75, 'count' => 45],
                'documents' => ['size' => 20, 'count' => 8],
                'other' => ['size' => 5, 'count' => 3],
            ],
        ];
    }

    private function getPaymentHistory()
    {
        return collect([
            [
                'date' => now()->subDays(30),
                'amount' => 1250.00,
                'type' => 'Royalty Payment',
                'status' => 'completed',
                'transaction_id' => 'TXN123456',
            ],
            [
                'date' => now()->subDays(60),
                'amount' => 890.50,
                'type' => 'Royalty Payment',
                'status' => 'completed',
                'transaction_id' => 'TXN123455',
            ],
            [
                'date' => now()->subDays(90),
                'amount' => 1560.75,
                'type' => 'Royalty Payment',
                'status' => 'completed',
                'transaction_id' => 'TXN123454',
            ],
        ]);
    }

    private function getSubscriptionPlans()
    {
        return collect([
            [
                'name' => 'Basic',
                'price' => 0,
                'features' => ['Up to 5 books', '1GB storage', 'Basic analytics'],
                'current' => true,
            ],
            [
                'name' => 'Professional',
                'price' => 29.99,
                'features' => ['Unlimited books', '10GB storage', 'Advanced analytics', 'Priority support'],
                'current' => false,
            ],
            [
                'name' => 'Enterprise',
                'price' => 99.99,
                'features' => ['All Professional features', '100GB storage', 'Custom branding', 'API access'],
                'current' => false,
            ],
        ]);
    }

    private function getApiUsage()
    {
        return [
            'total_requests' => 1250,
            'requests_this_month' => 340,
            'rate_limit' => 1000,
            'remaining' => 660,
            'endpoints_used' => ['books', 'analytics', 'reviews'],
        ];
    }

    private function getExportOptions()
    {
        return [
            'profile_data' => 'Export all profile information',
            'books_data' => 'Export all books and manuscripts',
            'analytics_data' => 'Export sales and analytics data',
            'reviews_data' => 'Export reviews and ratings',
            'financial_data' => 'Export payment and financial records',
        ];
    }

    private function getIntegrations()
    {
        return collect([
            [
                'name' => 'Google Analytics',
                'description' => 'Track website and book page analytics',
                'connected' => true,
                'icon' => 'google',
                'color' => 'blue',
            ],
            [
                'name' => 'Mailchimp',
                'description' => 'Email marketing and newsletters',
                'connected' => false,
                'icon' => 'mail',
                'color' => 'yellow',
            ],
            [
                'name' => 'Social Media',
                'description' => 'Auto-share to social platforms',
                'connected' => true,
                'icon' => 'share',
                'color' => 'purple',
            ],
            [
                'name' => 'PayPal',
                'description' => 'Payment processing',
                'connected' => false,
                'icon' => 'credit-card',
                'color' => 'green',
            ],
        ]);
    }

    private function getThemes()
    {
        return collect([
            ['name' => 'Light', 'active' => true],
            ['name' => 'Dark', 'active' => false],
            ['name' => 'Auto', 'active' => false],
        ]);
    }

    private function getSupportTickets()
    {
        return collect([
            [
                'id' => 'TIC-001',
                'subject' => 'Payment Issue',
                'status' => 'open',
                'created_at' => now()->subDays(2),
                'priority' => 'high',
            ],
            [
                'id' => 'TIC-002',
                'subject' => 'Book Upload Problem',
                'status' => 'resolved',
                'created_at' => now()->subDays(7),
                'priority' => 'medium',
            ],
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'pen_name' => 'nullable|string|max:255',
            'biography' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'writing_experience' => 'nullable|string|max:500',
            'education' => 'nullable|string|max:500',
            'awards' => 'nullable|string|max:500',
            'author_statement' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $author = $user->author;

        // Capture original data
        $originalUserData = $user->only(['name', 'email']);
        $originalAuthorData = $author ? $author->only(['pen_name', 'biography', 'website', 'writing_experience', 'education', 'awards', 'author_statement']) : [];

        // Update user data
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Update author data
        if ($author) {
            $author->update([
                'pen_name' => $this->pen_name,
                'biography' => $this->biography,
                'website' => $this->website,
                'social_links' => json_encode($this->social_links),
                'writing_experience' => $this->writing_experience,
                'education' => $this->education,
                'awards' => $this->awards,
                'author_statement' => $this->author_statement,
            ]);

            // Log activity
            $author->logActivity('update', 'Author Profile Updated', 'author', [
                'author_name' => $this->name,
                'author_pen_name' => $this->pen_name,
                'changes' => [
                    'user' => [
                        'name' => ['old' => $originalUserData['name'] ?? null, 'new' => $this->name],
                        'email' => ['old' => $originalUserData['email'] ?? null, 'new' => $this->email],
                    ],
                    'author' => [
                        'pen_name' => ['old' => $originalAuthorData['pen_name'] ?? null, 'new' => $this->pen_name],
                        'biography' => ['old' => $originalAuthorData['biography'] ?? null, 'new' => $this->biography],
                        'website' => ['old' => $originalAuthorData['website'] ?? null, 'new' => $this->website],
                        'writing_experience' => ['old' => $originalAuthorData['writing_experience'] ?? null, 'new' => $this->writing_experience],
                    ],
                ],
                'updated_by' => auth()->user()?->name ?? 'Unknown',
            ]);
        }

        session()->flash('profile-updated', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        // Log activity
        Auth::user()->logActivity('update', 'Password Changed', 'user', [
            'user_name' => auth()->user()?->name ?? 'Unknown',
            'password_changed' => true,
            'changed_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password-updated', 'Password updated successfully!');
    }

    public function updateNotifications()
    {
        // Update notification preferences
        session()->flash('notifications-updated', 'Notification preferences updated successfully!');
    }

    public function updatePrivacy()
    {
        // Update privacy settings
        session()->flash('privacy-updated', 'Privacy settings updated successfully!');
    }

    public function updatePublishing()
    {
        $this->validate([
            'royalty_rate' => 'required|numeric|min:0|max:100',
            'payment_method' => 'required|in:bank_transfer,paypal,stripe',
        ]);

        // Update publishing settings
        session()->flash('publishing-updated', 'Publishing settings updated successfully!');
    }

    public function updateMarketing()
    {
        // Update marketing preferences
        session()->flash('marketing-updated', 'Marketing preferences updated successfully!');
    }

    public function uploadAvatar()
    {
        $this->validate([
            'new_avatar' => 'required|image|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $this->new_avatar->store('avatars', 'public');

        $user->update(['avatar' => $path]);
        $this->avatar = $path;

        $this->reset('new_avatar');
        session()->flash('avatar-updated', 'Avatar updated successfully!');
    }

    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
            $this->avatar = null;
        }

        session()->flash('avatar-removed', 'Avatar removed successfully!');
    }

    public function enable2FA()
    {
        // Enable two-factor authentication
        $this->two_factor_enabled = true;
        session()->flash('2fa-enabled', 'Two-factor authentication enabled!');
    }

    public function disable2FA()
    {
        // Disable two-factor authentication
        $this->two_factor_enabled = false;
        session()->flash('2fa-disabled', 'Two-factor authentication disabled!');
    }

    public function revokeSession($sessionId)
    {
        // Revoke specific session
        session()->flash('session-revoked', 'Session revoked successfully!');
    }

    public function revokeAllSessions()
    {
        // Revoke all other sessions
        session()->flash('all-sessions-revoked', 'All other sessions revoked successfully!');
    }

    public function generateApiKey()
    {
        $this->api_key = 'ak_'.bin2hex(random_bytes(32));
        session()->flash('api-key-generated', 'API key generated successfully!');
    }

    public function revokeApiKey()
    {
        $this->api_key = '';
        session()->flash('api-key-revoked', 'API key revoked successfully!');
    }

    public function exportData($type)
    {
        // Export specific data type
        session()->flash('data-export-started', 'Data export started. You will receive an email when ready.');
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        $author = $user->author;

        // Log activity before deletion
        if ($author) {
            $author->logActivity('delete', 'Author Account Deletion Initiated', 'author', [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'author_pen_name' => $author->pen_name,
                'initiated_by' => $user->name,
            ]);
        }

        // Handle account deletion
        session()->flash('account-deletion-initiated', 'Account deletion initiated. You will receive a confirmation email.');
    }

    public function connectIntegration($integration)
    {
        // Connect to external integration
        session()->flash('integration-connected', ucfirst($integration).' connected successfully!');
    }

    public function disconnectIntegration($integration)
    {
        // Disconnect from external integration
        session()->flash('integration-disconnected', ucfirst($integration).' disconnected successfully!');
    }

    public function addGenre($genre)
    {
        if (! in_array($genre, $this->genres)) {
            $this->genres[] = $genre;
        }
    }

    public function removeGenre($genre)
    {
        $this->genres = array_filter($this->genres, fn ($g) => $g !== $genre);
    }

    public function addLanguage($language)
    {
        if (! in_array($language, $this->languages)) {
            $this->languages[] = $language;
        }
    }

    public function removeLanguage($language)
    {
        $this->languages = array_filter($this->languages, fn ($l) => $l !== $language);
    }

    public function upgradePlan($plan)
    {
        // Handle plan upgrade
        session()->flash('plan-upgrade-initiated', 'Plan upgrade initiated. Redirecting to payment...');
    }

    public function cancelSubscription()
    {
        // Handle subscription cancellation
        session()->flash('subscription-cancelled', 'Subscription cancelled successfully!');
    }
}
