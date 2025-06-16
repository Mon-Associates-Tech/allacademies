<?php

namespace App\Livewire\Students;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class BookSubscriptionModal extends Component
{
    public $showModal = false;
    public $subscriptionData = [];

    protected $listeners = [
        'showSubscriptionModal' => 'show',
        'closeSubscriptionModal' => 'close'
    ];

    public function show($subscriptionData): void
    {
        $this->subscriptionData = $subscriptionData;
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->subscriptionData = [];
    }

    public function closeSubscriptionModal(): void
    {
        $this->close();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.students.book-subscription-modal');
    }
}
