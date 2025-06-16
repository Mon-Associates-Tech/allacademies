<?php

namespace App\Services;

use App\Models\UserLogin;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;

class UserLoginService
{
    public function log($user, $action)
    {
        $agent = new Agent();

        // Update online status based on action
        if ($action === 'logged_in') {
            $user->update([
                'is_online' => true,
                'last_seen_at' => now()
            ]);
            Cache::put('user-online-' . $user->id, true, now()->addMinutes(5));
        } else {
            $user->update([
                'is_online' => false,
                'last_seen_at' => now()
            ]);
            Cache::forget('user-online-' . $user->id);
        }

        return UserLogin::create([
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $agent->device(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'session_id' => session()?->getId()
        ]);
    }
}
