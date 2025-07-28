<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Carbon;

class StoreUserLoginHistory
{
    public function handle(Login $event): void
    {

//        LoginActivity::where('user_id', $event->user->id)
//            ->whereNull('logout_at')
//            ->update(['logout_at' => Carbon::now()]);

        $agent = new Agent();
        $ip = request()->ip();
        $location = Location::get($ip) ?? null;

        LoginActivity::create([
            'user_id' => $event->user->id,
            'session_id' => session()->getId(),
            'user_agent' => request()->userAgent(),
            'ip_address' => $ip,
            'device_type' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'action' => 'login',
            'country' => $location->countryName ?? 'Unknown',
        ]);
    }
}
