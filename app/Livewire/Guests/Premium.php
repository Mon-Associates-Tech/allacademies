<?php

namespace App\Livewire\Guests;

use App\Models\BookSubscription;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Premium extends Component
{
    public $currentPlan = null;

    public $availablePlans = [];

    public $premiumFeatures = [];

    public $premiumStats = [];

    public function mount()
    {
        $this->loadPremiumData();
    }

    public function loadPremiumData()
    {
        $user = Auth::user();

        // Check current subscription status
        $this->currentPlan = Subscription::where('team_id', $user->current_team_id)
            ->where('status', 'paid')
            ->where('expires_at', '>', now())
            ->first();

        // Available premium plans
        $this->availablePlans = [
            [
                'name' => 'All Academies Basic',
                'price' => 29.99,
                'billing' => 'monthly',
                'features' => [
                    'Access to 100+ premium books',
                    'Unlimited self-assessments',
                    'Basic progress tracking',
                    'Community forum access',
                    'Mobile app access',
                ],
            ],
            [
                'name' => 'All Academies Pro',
                'price' => 79.99,
                'billing' => 'quarterly',
                'savings' => '11%',
                'features' => [
                    'Everything in Basic',
                    'Access to ALL premium books',
                    'Advanced analytics',
                    'Priority customer support',
                    'Offline reading',
                    'Early access to new content',
                ],
            ],
            [
                'name' => 'All Academies Elite',
                'price' => 299.99,
                'billing' => 'yearly',
                'savings' => '17%',
                'popular' => true,
                'features' => [
                    'Everything in Pro',
                    'Personal study advisor',
                    'Custom learning paths',
                    'Live webinars and workshops',
                    'Certification programs',
                    'Group study sessions',
                ],
            ],
        ];

        // Premium features comparison
        $this->premiumFeatures = [
            [
                'feature' => 'Free Books Access',
                'free' => true,
                'premium' => true,
            ],
            [
                'feature' => 'Premium Books',
                'free' => false,
                'premium' => true,
            ],
            [
                'feature' => 'Self Assessments',
                'free' => '5 per month',
                'premium' => 'Unlimited',
            ],
            [
                'feature' => 'Progress Analytics',
                'free' => 'Basic',
                'premium' => 'Advanced',
            ],
            [
                'feature' => 'Forum Access',
                'free' => true,
                'premium' => true,
            ],
            [
                'feature' => 'Study Groups',
                'free' => 'Public only',
                'premium' => 'Private + Public',
            ],
            [
                'feature' => 'Customer Support',
                'free' => 'Email only',
                'premium' => 'Priority support',
            ],
            [
                'feature' => 'Offline Reading',
                'free' => false,
                'premium' => true,
            ],
        ];

        // Premium statistics
        if ($this->currentPlan) {
            $this->premiumStats = [
                'books_accessed' => BookSubscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->count(),
                'money_saved' => $this->calculateMoneySaved(),
                'days_remaining' => $this->currentPlan->expires_at->diffInDays(now()),
                'reading_time' => $this->calculateReadingTime(),
            ];
        }
    }

    private function calculateMoneySaved()
    {
        $user = Auth::user();
        $subscriptions = BookSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('book')
            ->get();

        $individualCost = $subscriptions->sum(function ($subscription) {
            return $subscription->book->annual_subscription_fee ?? 0;
        });

        $paidAmount = $this->currentPlan ? $this->currentPlan->amount : 0;

        return max(0, $individualCost - $paidAmount);
    }

    private function calculateReadingTime()
    {
        // Simple calculation based on assessment activity
        $user = Auth::user();
        if (! $user->student) {
            return 0;
        }

        return $user->student->assessments()
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count() * 30; // Rough estimate: 30 minutes per assessment session
    }

    public function subscribeToPlan($planIndex)
    {
        $plan = $this->availablePlans[$planIndex];

        // Redirect to payment processing
        return redirect()->route('subscriptions.create', [
            'plan' => $plan['name'],
            'price' => $plan['price'],
            'billing' => $plan['billing'],
        ]);
    }

    public function cancelSubscription()
    {
        if ($this->currentPlan) {
            $this->currentPlan->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            session()->flash('success', 'Subscription cancelled successfully. You will retain access until the end of your billing period.');
            $this->loadPremiumData();
        }
    }

    public function render()
    {
        return view('livewire.guests.premium');
    }
}
