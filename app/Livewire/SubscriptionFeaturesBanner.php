<?php

namespace App\Livewire;

use Livewire\Component;

class SubscriptionFeaturesBanner extends Component
{
    public bool $showBanner = true;
    public string $placement = 'dashboard'; // 'dashboard' or 'subscription'

    public function mount(string $placement = 'dashboard')
    {
        $this->placement = $placement;
    }

    public function dismissBanner()
    {
        $this->showBanner = false;
        session()->put('subscription_banner_dismissed_' . $this->placement, true);
    }

    public function render()
    {
        // Check if banner was previously dismissed
        if (session()->get('subscription_banner_dismissed_' . $this->placement, false)) {
            $this->showBanner = false;
        }

        $features = $this->getFeatures();

        return view('livewire.subscription-features-banner', [
            'features' => $features,
        ]);
    }

    private function getFeatures(): array
    {
        return [
            [
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
                'title' => 'AI Academic Chat',
                'description' => 'Get instant help with homework, explanations, and study guidance',
                'gradient' => 'from-blue-500 to-cyan-500',
            ],
            [
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                'title' => 'Smart Quiz Generator',
                'description' => 'Auto-generate quizzes and assignments from any book chapter',
                'gradient' => 'from-purple-500 to-pink-500',
            ],
            [
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
                'title' => 'Digital Library Access',
                'description' => 'Unlimited access to thousands of academic books and resources',
                'gradient' => 'from-green-500 to-emerald-500',
            ],
            [
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
                'title' => 'Progress Tracking',
                'description' => 'Monitor your learning journey with detailed analytics',
                'gradient' => 'from-orange-500 to-red-500',
            ],
            [
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                'title' => 'Instant Feedback',
                'description' => 'Get immediate responses and personalized learning support',
                'gradient' => 'from-yellow-500 to-amber-500',
            ],
            [
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
                'title' => 'Collaborative Learning',
                'description' => 'Share resources and learn together with group features',
                'gradient' => 'from-indigo-500 to-blue-500',
            ],
        ];
    }
}
