<?php

namespace App\Http\Controllers;

use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        
        // Get users who have set their profile pictures (avatar field is not null)
        $usersWithAvatars = User::whereNotNull('avatar')
            ->where('avatar', '!=', '')
            ->inRandomOrder()
            ->limit(3)
            ->get(['id', 'name', 'avatar']);
        
        return view('branding', compact('usersCount', 'usersWithAvatars'));
    }
}
