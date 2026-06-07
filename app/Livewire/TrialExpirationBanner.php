<?php

namespace App\Livewire;

use Livewire\Component;

class TrialExpirationBanner extends Component
{
    public bool $showBanner = false;

    public ?int $daysRemaining = null;

    public ?string $expiresAt = null;

    public bool $isExpired = false;

    public bool $isExpiringSoon = false;

    public ?int $tokensRemaining = null;

    public ?int $tokensUsed = null;

    public array $features = [];

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if (session()->get('trial_banner_dismissed_at') &&
            now()->diffInMinutes(session()->get('trial_banner_dismissed_at')) < 60) {
            $this->showBanner = false;

            return;
        }
        $user->load('subscriptionCycles');
        $activeCycle = $user->getCurrentActiveCycle();

        if (! $activeCycle || ! $activeCycle->isTrial()) {
            $this->showBanner = false;

            return;
        }

        $expiresAt = $activeCycle->cycle_end_date;
        $now = now();

        $this->daysRemaining = $activeCycle->getRemainingDays();
        $this->expiresAt = $expiresAt->format('M j, Y');
        $this->tokensRemaining = $activeCycle->getTokensRemainingAttribute();
        $this->tokensUsed = $activeCycle->tokens_used;

        $this->isExpired = $activeCycle->isExpired();
        $this->isExpiringSoon = $activeCycle->isEndingSoon();

        if ($this->isExpiringSoon || ($this->isExpired && $now->diffInHours($expiresAt) <= 24)) {
            $this->showBanner = true;
            $this->loadFeatures();
        }
    }

    private function loadFeatures(): void
    {
        $this->features = [
            [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
                'title' => 'AI Chat',
                'description' => 'Get instant homework help',
                'link' => route('research-assistant.index'),
                'gradient' => 'from-blue-500 to-cyan-500',
            ],
            [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                'title' => 'Quiz Generator',
                'description' => 'Auto-create practice tests',
                'link' => route('learning.quiz'),
                'gradient' => 'from-purple-500 to-pink-500',
            ],
            [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
                'title' => 'Notes and Uploads',
                'description' => 'Access your uploaded documents',
                'link' => route('notes.index'),
                'gradient' => 'from-green-500 to-emerald-500',
            ],
            [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                'title' => 'Smart Study',
                'description' => 'Personalized learning',
                'link' => route('dashboard'),
                'gradient' => 'from-amber-500 to-orange-500',
            ],
        ];
    }

    public function dismissBanner(): void
    {
        $this->showBanner = false;
        session()->put('trial_banner_dismissed_at', now());
    }

    public function render()
    {
        return view('livewire.trial-expiration-banner');
    }
}
