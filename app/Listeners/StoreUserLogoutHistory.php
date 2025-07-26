<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Carbon;

class StoreUserLogoutHistory
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            LoginActivity::where('user_id', $event->user->id)
                ->where('session_id', session()->getId())
                ->whereNull('logout_at')
                ->latest()
                ->first()
                ?->update(['logout_at' => Carbon::now()]);
        }
    }
}
