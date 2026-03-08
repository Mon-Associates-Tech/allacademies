<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class UserLoginActivityController extends Controller
{
    public function show(User $user)
    {
        // Authorization check - users can only view their own or admins can view all
        if (auth()->id() !== $user->id && !auth()->user()->hasAnyRole(['admin', 'super_admin', 'owner'])) {
            abort(403, 'Unauthorized to view this user\'s login activities.');
        }

        return view('login-activities.show', compact('user'));
    }
}
